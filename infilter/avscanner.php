<?php
/*
 * Simple AV file scanner
 *
 * Based on https://github.com/Elycin/php-clamav/
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
require_once "getParams.php";

class Clamd_service
{
    private $socket_path;
    private $socket;
    private $buffer_length = 1024;

    private $character_prefix = "n";

    public function __construct($socket_path = "/tmp/clamd.ctl")
    {
        $this->socket_path = $socket_path;
        return $this->doesSocketExist();
    }

    public function doesSocketExist()
    {
        return is_file($this->socket_path);
    }

    private function connect()
    {
        $this->socket = fsockopen("unix://" . $this->socket_path);
        return $this->socket;
    }

    // low level socket access function - no need to call this directly
    private function send($query)
    {
        if(!$this->connect()) {
            echo("Can't connect to socket " . $this->socket_path);
            exit(1);
        }
        fwrite($this->socket, $query);
        $response = fread($this->socket, $this->buffer_length);
        fclose($this->socket);
        return $response;
    }

    // multipurpose method supporting all clamd commands. Maps method name into uppercase command.
    public function __call($name, $arguments)
    {
        // prevent PHP warning with empty arguments
        if(empty($arguments)) {
            $arguments[0] = "";
        }
        $pending_command = trim(sprintf("%s%s %s",
                $this->character_prefix, strtoupper($name), $arguments[0])) . "\n";
        return $this->send($pending_command);
    }

    private function exceptionSocketDoesNotExist()
    {
        return new \Exception(sprintf("IPC Socket File %s does not exist.", $this->socket_path));
    }
}


// helper to get config
function get_config($avParams, $k)
{
    if (!array_key_exists($k, $avParams)) {
        echo "Error - " . $k . " not defined - check .ini file";
        return null;
    } else {
        return $avParams[$k];
    }
}
// helper to check if configured directories are set up and readable/writeable)
function chk_config($avParams, $k, $mode)
{
    $d = get_config($avParams, $k);
    if (!$d) {
        // don't allow empty string, as later, realpath would default to current directory: see
        // http://php.net/manual/en/function.realpath.php
        echo "Error - " . $k . " is empty string - check .ini file";
        return null;
    }
    $dpath = realpath($d);
    if (!$dpath) {
        echo "Error - can't open " . $dpath;
        return null;
    }
    if ($mode === "r") {
        if (!is_readable($dpath)) {
            echo "Error - can't open " . $dpath . " for reading";
            return null;
        }
    } elseif ($mode === "w") {
        if (!is_writeable($dpath)) {
            echo "Error - can't open " . $dpath . " for writing";
            return null;
        }
    }
    return $dpath;              // all OK
}

// helper to send replies back via SparkPost using specified template
function sparkpost_template_send($sparkpost_host, $sparkpost_api_key, $template, $recip)
{
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/transmissions";
    //DEBUG: override this, e.g. $req_uri = "https://my-runscope.herokuapp.com/sgr3p0sg";
    $req_hdrs = [ "Content-Type"  => "application/json",
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $tx_body = ["campaign_id" => "avscanner autoreply",
        "recipients" => [ [ "address" => $recip ] ],            // This has to be a list of addresses
        "content" => [ "template_id" => $template ]
    ];
    $res = $client->request("POST", $req_uri, ["json" => $tx_body, "headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        echo "Warning: unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase() . PHP_EOL;
    } else {
        echo "- INFO: Email " . $template . " sent to: " . $recip . PHP_EOL;
    }
}


//--------------------------------------------------------------------------------------------------------------------
// Main code
//--------------------------------------------------------------------------------------------------------------------

//--- Get configuration
$p = getParams("suite.ini");
$avParams = $p["infilter"];

$workdir_path = chk_config($avParams, "workdir", "r");
$mail_path = chk_config($avParams, "maildir", "w");
$done_path = chk_config($avParams, "donedir", "w");
if (!$workdir_path || !$mail_path || !$done_path) {
    // something's badly set up. Exit now
    exit(1);
}

$max_attachment_size = get_config($avParams, "max_attachment_size");
if (!$max_attachment_size) {
    $max_attachment_size = 0;                        // Default = no check
} elseif ($max_attachment_size != strval((int)$max_attachment_size)) {
    echo "Error: max_attachment_size misconfigured - must be integer";
    exit(1);
} else {
    $max_attachment_size = (int)$max_attachment_size;
}

$delivery_url = get_config($avParams, "delivery_url");
if(!$delivery_url) {
    echo "Error: delivery_url must be set";
    exit(1);
}
$delivery_method = get_config($avParams, "delivery_method");
if(!$delivery_method) {
    echo "Error: delivery_method must be set";
    exit(1);
}

$replies_enabled = get_config($avParams, "replies_enabled");
if(!$replies_enabled) {
    echo "Error: replies_enabled must be set true or false";
} else {
    $sp_accept_template = get_config($avParams, "sp_accept_template");
    if(!$sp_accept_template) {
        echo "Error: sp_accept_template must be defined";
        exit (1);
    }
    $sp_reject_template = get_config($avParams, "sp_reject_template");
    if(!$sp_reject_template) {
        echo "Error: sp_reject_template must be defined";
        exit (1);
    }
    $sparkpost_api_key = get_config($p["SparkPost"], "sparkpost_api_key");
    if(!$sparkpost_api_key) {
        echo "Error: sparkpost_api_key must be defined";
        exit (1);
    }
    $sparkpost_host = get_config($p["SparkPost"], "sparkpost_host");
    if(!$sparkpost_host) {
        echo "Error: sparkpost_host must be defined";
        exit (1);
    }

}

//--- Open connection towards Clamd for scanning
$my_clam = new Clamd_service($avParams["LocalSocket"]);
echo "AV scanner version/database reports " . trim($my_clam->version()) . PHP_EOL;

//--- Find all message files matching our expected file extension
$file_list = glob($workdir_path .  DIRECTORY_SEPARATOR . "*.json");
foreach($file_list as $jfile) {
    $rawBody = file_get_contents($jfile);
    $ir = json_decode($rawBody);
    if (!$ir) {
        echo "Warning: content body must be valid JSON format - file " . basename($jfile);
    }
    $msg_count = 0;
    $jsonVerdictOK = true;                                      // this flag is for all messages in this JSON POST
    foreach ($ir as $msg_idx => $e) {
        $msg = $e->msys->relay_message;
        if (!$msg) {
            echo "Warning: content body must be valid JSON []msys.relay_message format - file " . basename($jfile);
            continue;
        }
        $base = basename($jfile, ".json");               //  get base name of this input file. There could be >1 mail per JSON blob
        $msg_filename = $mail_path . DIRECTORY_SEPARATOR . $base . "_" . strval($msg_idx) . ".eml";
        // Get the mail payload
        $email_content = $msg->content->email_rfc822;
        if (!$email_content) {
            echo "Warning - content body must contain []msys.relay_message.content.email_rfc822 - file ". basename($jfile);
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
            echo "Warning: file " . $jfile . " contains more than one from address - file ". basename($jfile);
            continue;
        }
        $basicFrom = $addressFrom[0]["address"];
        // Write .eml file to our spool folder (even if it's too big etc. to aid debugging)
        $ok = file_put_contents($msg_filename, $email_content);
        if (!$ok) {
            echo "Error: file " . $msg_filename . " could not be written";
            exit(1);                                        // indicates OS-level problem, so stop
        }
        $msg_count++;                                       // Successfully wrote one message to a file.
        $attachTooBig = false;
        if($max_attachment_size > 0) {
            // Get an array of Attachment items from $Parser.  Check inline images/content also
            $attachments = $Parser->getAttachments(true);
            foreach($attachments as $a) {
                ;
                $contentLength = strlen($a->getContent());
                // PHP strings are binary-safe so even files with embedded NULs still give valid length - see https://stackoverflow.com/a/12698815/8545455
                if($contentLength > $max_attachment_size) {
                    echo "- INFO: file " . basename($msg_filename) . " contains attachment " . $a->getFilename() . ", type ". $a->getContentType() .
                        " that is " . $contentLength . " bytes, exceeding configured max_attachment_size of " . $max_attachment_size . PHP_EOL;
                    $attachTooBig = true;
                }
            }
        }
        $fileVerdictOK = false;                                             // always assume bad before checking, for safety
        if(!$attachTooBig) {
            // clamd scanner will unpack the .eml file itself and scan the attachments etc. See clamdoc.pdf, "MULTISCAN".
            $results = $my_clam->multiscan($msg_filename);
            echo "- INFO: " . $results;
            $splitRes = explode(":", trim($results));              // filename will be in [0] and verdict in [1]
            if (sizeof($splitRes) != 2) {
                echo "Error: unexpected return value from clamd ", $results;
                exit(1);
            }
            $verdictStr = trim($splitRes[1]);
            // remove whitespace to permit string comparison
            $fileVerdictOK = ($verdictStr === "OK");
        }
        if(!$attachTooBig && $fileVerdictOK) {
            // This email was OK
            if($replies_enabled) {
                sparkpost_template_send($sparkpost_host, $sparkpost_api_key, $sp_accept_template, $basicFrom);
            }
        } else {
            // This email was bad
            if($replies_enabled) {
                sparkpost_template_send($sparkpost_host, $sparkpost_api_key, $sp_reject_template, $basicFrom);
            }
            $jsonVerdictOK = false;                                       // we won't forward the enclosing JSON
        }
    }
    // now checked the emails within this JSON file.  Push it out if OK.  This is assuming really 1 message per JSON file, which is currently the case
    if($jsonVerdictOK) {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $res = $client->request($delivery_method, $delivery_url, [ "body" => $rawBody, "timeout" => 30]);
        if($res->getStatusCode() != 200) {
            echo "Warning: unexpected status code " . $res->getStatusCode() .
                " from " . $delivery_url . " : " . $res->getReasonPhrase() . PHP_EOL;
        } else {
            echo "- INFO: message forwarded to: " . $delivery_method . " " . $delivery_url . PHP_EOL;
        }
    }
}