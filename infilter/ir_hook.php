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
// check authorization, if present
if(array_key_exists("Authorization", $avParams)) {
    // config expects an auth header in inbound relay webhook - ensure it's set up the same in SparkPost
    $headers = apache_request_headers();
    if(!array_key_exists("Authorization", $headers)) {
        echo "Authorization header is missing";
        http_response_code(501);
        exit(1);
    } else {
        if($headers["Authorization"] != $avParams["Authorization"]) {
            echo "Authorization header mismatch";
            http_response_code(501);
            exit(1);
        }
    }
}

$rawBody = file_get_contents('php://input');
$uniq = uniqid();
// write as JSON files. Then we have all metadata and can forward the whole JSON blob easily from the batch process
$dbg_filename = $workdir_path . DIRECTORY_SEPARATOR . "msg_" . $uniq . ".json";
$dbg_fh = fopen($dbg_filename, "w");
if (!$dbg_fh) {
    http_response_code(501);
    echo "Server problem - json write 1";
    exit(1);
}
$ok = fwrite($dbg_fh, $rawBody);
if(!$ok) {
    http_response_code(501);
    echo "Server problem - json write 2";
    exit(1);
}
$ok = fclose($dbg_fh);
if(!$ok) {
    http_response_code(501);
    echo "Server problem - json write 3";
    exit(1);
}
// committed the JSON blob to file, done for now
http_response_code(200);
echo "ir_hook: received a webhook push";
