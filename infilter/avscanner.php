<?php
/*
 * Inbound mail file scanning:
 *  This behaviour can be customised in suite.ini
 *
 *      - Collects JSON-format inbound relay mail files from configured spool directory
 *      - puts each mail in RFC822 .eml format into spool/eml directory ready for scanning
 *      - Unpacks MIME parts and checks attachment size against configurable maximum
 *      - Invokes clamd service to scan mail. clamd will check the whole mail incl. attachments
 *      - If mail is OK, forwards to endpoint using http(s) POST and sends a "thank you .." reply back via SparkPost
 *      - If mail is not OK, sends a "we could not accept" reply back via SparkPost
 *      - Processed .eml and JSON files are moved to the "done" folder
 *
 * Steve Tuck, SparkPost - June 2018
 *
 * External dependencies:
 * php-mailparse
 * See (OSX):      https://gist.github.com/thelbouffi/118107b77f52f5a07eb840c3f2993509
 *     (CentOS):   https://ma.ttias.be/installing-the-pecl-pear-mailparse-module-for-php-on-centos/
 *
 * https://github.com/php-mime-mail-parser/php-mime-mail-parser
 *                 use Composer command shown on ths page
 */
require_once '../vendor/autoload.php';
require_once "../app_common.php";
require_once "clamd_service.php";

// helper to send replies back via SparkPost using specified template
function sparkpost_template_send($sparkpost_host, $sparkpost_api_key, $template, $recip, $global_sub_data)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/transmissions";
    //DEBUG: override this, e.g. $req_uri = "https://my-runscope.herokuapp.com/sgr3p0sg";
    $req_hdrs = [
        "Content-Type"  => "application/json",
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $tx_body = ["campaign_id" => "avscanner autoreply",
        "recipients" => [ [ "address" => $recip ] ],            // This has to be a list of addresses
        "content" => [ "template_id" => $template ],
        "substitution_data" => $global_sub_data,
    ];
    $res = $client->request("POST", $req_uri, ["json" => $tx_body, "headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("Unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase());
    } else {
        $app_log->info("Email " . $template . " sent to: " . $recip);
    }
}

// Deliver an object (internally converted to JSON) using the specified delivery method
function deliver_json($delivery_method, $delivery_url, $obj)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(['http_errors' => false]);
    $res = $client->request($delivery_method, $delivery_url, [ "json" => $obj, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("unexpected status code " . $res->getStatusCode() .
            " from " . $delivery_url . " : " . $res->getReasonPhrase());
    } else {
        $app_log->info("message http " . $delivery_method . " to " . $delivery_url);
    }
}


//--------------------------------------------------------------------------------------------------------------------
// Main code
//--------------------------------------------------------------------------------------------------------------------

//--- Get configuration
$p = getParams("../suite.ini");
$avParams = $p["infilter"];
// Get logging set up early on, for error reporting etc
$app_log = new App_log($avParams["logdir"], basename(__FILE__));

$workdir_path = chk_config($avParams, "workdir", "r");
$mail_path = chk_config($avParams, "maildir", "w");
$done_path = chk_config($avParams, "donedir", "w");

$max_attachment_size = get_config($avParams, "max_attachment_size");
if (!$max_attachment_size) {
    $max_attachment_size = 0;                        // Default = no check
} elseif ($max_attachment_size != strval((int)$max_attachment_size)) {
    $app_log->error("max_attachment_size misconfigured - must be integer");
    exit(1);
} else {
    $max_attachment_size = (int)$max_attachment_size;
}

$delivery_url = get_config_mandatory($avParams, "delivery_url");
$delivery_method = get_config_mandatory($avParams, "delivery_method");

$replies_enabled = get_config($avParams, "replies_enabled");
if($replies_enabled) {
    $sp_accept_template = get_config_mandatory($avParams, "sp_accept_template");
    $sp_reject_template = get_config_mandatory($avParams, "sp_reject_template");
    $sparkpost_api_key = get_config_mandatory($p["SparkPost"], "sparkpost_api_key");
    $sparkpost_host = get_config_mandatory($p["SparkPost"], "sparkpost_host");
}

//--- Open connection towards Clamd for scanning
$my_clam = new Clamd_service(get_config_mandatory($avParams, "LocalSocket"));
$app_log->info("AV scanner version/database reports " . trim($my_clam->version()));

//--- Find all message files matching our expected file extension
$file_list = glob($workdir_path .  DIRECTORY_SEPARATOR . "*.json");
foreach($file_list as $jfile) {
    $rawBody = file_get_contents($jfile);
    $ir = json_decode($rawBody);
    if (!$ir) {
        $app_log->warning("content body must be valid JSON format - file " . basename($jfile));
    }
    $msg_count = 0;
    foreach ($ir as $msg_idx => $e) {
        $msg = $e->msys->relay_message;
        if (!$msg) {
            $app_log->warning("content body must be valid JSON []msys.relay_message format - file " . basename($jfile));
            continue;
        }
        $base = basename($jfile, ".json");               //  get base name of this input file. There could be >1 mail per JSON blob
        $msg_filename = $mail_path . DIRECTORY_SEPARATOR . $base . "_" . strval($msg_idx) . ".eml";
        // Get the mail payload
        $email_content = $msg->content->email_rfc822;
        if (!$email_content) {
            $app_log->warning("content body must contain []msys.relay_message.content.email_rfc822 - file ". basename($jfile));
            continue;
        }
        if ($msg->content->email_rfc822_is_base64) {
            $email_content = base64_decode($email_content);               //TODO: create test case for this
        }
        // Now have a valid RFC822 message body.  Use Parser class
        $Parser = new PhpMimeMailParser\Parser();
        $Parser->setText($email_content);

        // Once we've indicated where to find the mail, we can parse out the data.  We'll need the From address for replies
        $addressFrom = $Parser->getAddresses('from'); //Return an array : [["display"=>"test", "address"=>"test@example.com", false],["display"=>"test2", "address"=>"test2@example.com", false]]
        if(sizeof($addressFrom) != 1) {
            $app_log->info("file " . $jfile . " contains more than one from address - file ". basename($jfile));
            continue;
        }
        $basicFrom = $addressFrom[0]["address"];
        // Write .eml file to our spool folder (even if it's too big etc. to aid debugging)
        $ok = file_put_contents($msg_filename, $email_content);
        if (!$ok) {
            $app_log->error("file " . $msg_filename . " could not be written");
            exit(1);                                        // indicates OS-level problem, so stop
        }
        $msg_count++;                                       // Successfully wrote one message to a file.
        $attachTooBig = false;
        if($max_attachment_size > 0) {
            // Get an array of Attachment items from $Parser.  Check inline images/content also
            $attachments = $Parser->getAttachments(true);
            foreach($attachments as $a) {
                $contentLength = strlen($a->getContent());
                // PHP strings are binary-safe so even files with embedded NULs still give valid length - see https://stackoverflow.com/a/12698815/8545455
                $app_log->info("file " . basename($msg_filename) . ", attachment " . $a->getFilename() . ", type ".
                    $a->getContentType() . ", length " . $contentLength . " bytes");
                if($contentLength > $max_attachment_size) {
                    $app_log->info("Exceeds configured max_attachment_size of " . $max_attachment_size . " bytes. Dropped.");
                    $attachTooBig = true;
                }
            }
        }
        $fileVerdictOK = false;                                         // always assume bad before checking, for safety
        if(!$attachTooBig) {
            // clamd scanner will unpack the .eml file itself and scan the attachments etc. See clamdoc.pdf, "MULTISCAN".
            $results = trim($my_clam->multiscan($msg_filename));        // trim off whitespace incl. \n
            $app_log->info($results);
            $splitRes = explode(":", $results);                // filename will be in [0] and verdict in [1]
            if (sizeof($splitRes) != 2) {
                $app_log->error("unexpected return value from clamd " . $results);
                exit(1);
            }
            $verdictStr = trim($splitRes[1]);
            // remove whitespace to permit string comparison
            $fileVerdictOK = ($verdictStr === "OK");
        }
        if(!$attachTooBig && $fileVerdictOK) {
            // This email was OK
            if($replies_enabled) {
                sparkpost_template_send($sparkpost_host, $sparkpost_api_key, $sp_accept_template, $basicFrom, null);
            }
            deliver_json($delivery_method, $delivery_url, $msg);
        } else {
            // This email was bad
            if($replies_enabled) {
                // give size limit in terms of megabytes, as easier for people to understand
                $sub_data = ["max_attachment_size" => strval($max_attachment_size/1024/1024) . " megabytes"];
                sparkpost_template_send($sparkpost_host, $sparkpost_api_key, $sp_reject_template, $basicFrom, $sub_data);
            }
            // Build and send a "negative" ack of inbound messages onwards, carrying just plaintext from the message
            $nack = new stdClass();
            $nack->rcpt_to = $msg->rcpt_to;
            $nack->msg_from = $msg->msg_from;
            $nack->friendly_from = $msg->friendly_from;
            $nack->content->subject = $msg->content->subject;
            $nack->rejection_reason = array();
            // Append reasons that apply
            if($attachTooBig) {
                $nack->rejection_reason[] = "One or more attachments exceeded max size of " . $max_attachment_size . " bytes";
            }
            if(isset($verdictStr)) {
                $nack->rejection_reason[] = "Virus scan reported " . $verdictStr;
            }
            deliver_json($delivery_method, $delivery_url, $nack);
        }
        // Finished with this .eml file - move it to done dir
        // Production code could delete the file, rather than move it
        rename($msg_filename, $done_path . DIRECTORY_SEPARATOR . basename($msg_filename));
    }
    // Finished with this JSON input file - move it to the done (dung?) dir
    // Production code could delete the file, rather than move it
    rename($jfile, $done_path . DIRECTORY_SEPARATOR . basename($jfile) );
}