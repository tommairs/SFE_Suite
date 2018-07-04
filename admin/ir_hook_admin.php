<?php
require_once '../vendor/autoload.php';
require_once "../app_common.php";

// API access functions. Note that have to pass in params as globals not shared between files (besides globals being mostly ugly)
function get_ir_hooks($sparkpost_host, $sparkpost_api_key, $app_log)
{
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/relay-webhooks";
    $req_hdrs = [ "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $res = $client->request("GET", $req_uri, ["headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("Unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase());
        return null;
    } else {
        $app_log->info("GET relay-webhooks - OK");
        return json_decode($res->getBody());
    }
}