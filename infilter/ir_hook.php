<?php
/*
 * Inbound relay webhook receiver code
 *
 * Steve Tuck, SparkPost - June 2018
 */

require_once "getParams.php";

//--------------------------------------------------------------------------------------------------------------------
// Main code
//--------------------------------------------------------------------------------------------------------------------

// Webhook handling code should perform minimal work before returning a response code back to SparkPost. See
// https://www.sparkpost.com/blog/webhooks-beyond-the-basics/
//
// Below, we spool each inbound message to an RFC822-compliant .eml file ready for further batch processing.

$p = getParams("suite.ini");
$avParams = $p["infilter"];
// Check working message directory set up and accessible
if(!array_key_exists("workdir", $avParams)) {
    http_response_code(501);
    echo "Server problem workdir 1";
    exit(1);
}
$wd = $avParams["workdir"];
if(!$wd) {
    // don't allow empty string, as later, realpath would default to current directory: see
    // http://php.net/manual/en/function.realpath.php
    http_response_code(501);
    echo "Server problem workdir 2";
    exit(1);
}
$workdir_path = realpath($wd);
if(!$workdir_path) {
    http_response_code(501);
    echo "Server problem workdir 3";
    exit(1);
}
if(!is_writable($workdir_path)) {
    http_response_code(501);
    echo "Server problem workdir 4";
    exit(1);
}
// Check incoming http request
if(!array_key_exists("REQUEST_METHOD", $_SERVER)) {
    http_response_code(501);
    echo "http request method must be present";
    exit(1);
}
$req_method = $_SERVER["REQUEST_METHOD"];
// Read the request body. Important to quickly return 200OK to SparkPost
if($req_method != "POST") {
    http_response_code(501);
    echo "http request method must be POST";
    exit(1);
}
if(!array_key_exists("CONTENT_TYPE", $_SERVER)) {
    http_response_code(501);
    echo "Missing Content-Type header";
    exit(1);
}
$content_type = $_SERVER["CONTENT_TYPE"];
if($content_type != "application/json") {
    http_response_code(501);
    echo "Header Content-Type must be application/json";
    exit(1);
}
$headers = apache_request_headers();
$ir = json_decode(file_get_contents('php://input'));
if(!$ir) {
    http_response_code(501);
    echo "Content body must be valid JSON format";
    exit(1);
}
// $ir is object decoded from valid JSON
$msg_count = 0;
foreach ($ir as $e) {
    $msg = $e->msys->relay_message;
    if (!$msg) {
        http_response_code(501);
        echo "Content body must be valid JSON []msys.relay_message format";
        exit(1);
    }
    $msg_filename = $workdir_path . DIRECTORY_SEPARATOR . "msg_" . uniqid() . $avParams["file_extension"];
    $msg_fh = fopen($msg_filename, "w");
    if (!$msg_fh) {
        http_response_code(501);
        echo "Server problem - msg write 1";
        exit(1);
    }
    // Get the mail payload
    $email_content = $msg->content->email_rfc822;
    if (!$email_content) {
        http_response_code(501);
        echo "Content body must contain []msys.relay_message.content.email_rfc822";
        exit(1);
    }
    if ($msg->content->email_rfc822_is_base64) {
        $email_content = base64_decode($email_content);               //TODO: create test case for this
    }
    // Now have a valid RFC822 message body, which can be written to our spool folder
    // Build a filename from the webhook_id and the message index (which will usually be zero, because one msg per hook)
    $ok = fwrite($msg_fh, $email_content);
    if(!$ok) {
        http_response_code(501);
        echo "Server problem - msg write 2";
        exit(1);
    }
    $ok = fclose($msg_fh);
    if(!$ok) {
        http_response_code(501);
        echo "Server problem - msg write 3";
        exit(1);
    }
    $msg_count++;
    // Successfully wrote one message
}
http_response_code(200);
echo "ir_hook: received messages: " . strval($msg_count);
// Log a received mail event