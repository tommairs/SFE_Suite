<?php
require_once '../vendor/autoload.php';
require_once "../app_common.php";

//--------------------------------------------------------------------------------------------------------------------
// API access functions
//--------------------------------------------------------------------------------------------------------------------

// Get current inbound relay webhooks, returning as object. Log activity to app_log
function get_ir_hooks($sparkpost_host, $sparkpost_api_key)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/relay-webhooks";
    $method = "GET";
    $req_hdrs = [
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $res = $client->request($method, $req_uri, ["headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("Unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase());
    } else {
        $app_log->info("GET relay-webhooks - OK");
    }
    return [$res->getStatusCode(), json_decode($res->getBody())];
}

// Create an inbound relay webhook, from object in $body. Log activity to app_log. Return a tuple.
function create_ir_hook($sparkpost_host, $sparkpost_api_key, $body)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/relay-webhooks";
    $method = "POST";
    $req_hdrs = [
        "Content-Type" => "application/json",
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $res = $client->request($method, $req_uri, ["json" => $body, "headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("Unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase() . " " . $res->getBody());
    } else {
        $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
    }
    return [$res->getStatusCode(), json_decode($res->getBody())];
}

// Update an inbound relay webhook, from object in $body. Log activity to app_log. Return a tuple.
function update_ir_hook($sparkpost_host, $sparkpost_api_key, $body)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/relay-webhooks";
    $method = "PUT";
    $req_hdrs = [
        "Content-Type" => "application/json",
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $res = $client->request($method, $req_uri, ["json" => $body, "headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("Unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase());
    } else {
        $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
    }
    return json_decode($res->getBody());
}

// Delete an inbound relay webhook $id. Log activity to app_log
function delete_ir_hook($sparkpost_host, $sparkpost_api_key, $id)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/relay-webhooks";
    $method = "DELETE";
    $req_hdrs = [
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    $req_uri .= "/" . urlencode($id);
    $res = $client->request($method, $req_uri, ["headers" => $req_hdrs, "timeout" => 30]);
    if($res->getStatusCode() != 200) {
        $app_log->warning("Unexpected status code " . $res->getStatusCode() .
            " from " . $req_uri . " : " . $res->getReasonPhrase());
        return null;
    } else {
        $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        return json_decode($res->getBody());
    }
}
