<?php
require_once '../vendor/autoload.php';
require_once "../app_common.php";

//--------------------------------------------------------------------------------------------------------------------
// API access functions - because SparkPost lib is only a thin wrapper anyway for these endpoints
//--------------------------------------------------------------------------------------------------------------------

// Get resource, returning (status, body) list. Log activity to app_log
function get_resource_list($sparkpost_host, $sparkpost_api_key, $resource)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/" . $resource;
    $method = "GET";
    $req_hdrs = [
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    try {
        $res = $client->request($method, $req_uri, ["headers" => $req_hdrs, "timeout" => 30]);
        if($res->getStatusCode() != 200) {
            $app_log->warning("Unexpected status code " . $res->getStatusCode() .
                " from " . $req_uri . " : " . $res->getReasonPhrase());
        } else {
            $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        }
        return array($res->getStatusCode(), json_decode($res->getBody()));
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $app_log->error($e);
        return null;
    }
}

// Get a single resource, returning (status, body) list. Log activity to app_log
function get_resource($sparkpost_host, $sparkpost_api_key, $resource, $id)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/" . $resource . "/" . urlencode($id);
    $method = "GET";
    $req_hdrs = [
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    try {
        $res = $client->request($method, $req_uri, ["headers" => $req_hdrs, "timeout" => 30]);
        if($res->getStatusCode() != 200) {
            $app_log->warning("Unexpected status code " . $res->getStatusCode() .
                " from " . $req_uri . " : " . $res->getReasonPhrase());
        } else {
            $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        }
        return array($res->getStatusCode(), json_decode($res->getBody()));
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $app_log->error($e);
        return null;
    }
}

// Create resource, returning (status, body) list. Log activity to app_log
function create_resource($sparkpost_host, $sparkpost_api_key, $resource, $body)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/" . $resource;
    $method = "POST";
    $req_hdrs = [
        "Content-Type" => "application/json",
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    try {
        $res = $client->request($method, $req_uri, ["json" => $body, "headers" => $req_hdrs, "timeout" => 30]);
        if($res->getStatusCode() != 200) {
            $app_log->warning("Unexpected status code " . $res->getStatusCode() .
                " from " . $req_uri . " : " . $res->getReasonPhrase() . " " . $res->getBody());
        } else {
            $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        }
        return array($res->getStatusCode(), json_decode($res->getBody()));
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $app_log->error($e);
        return null;
    }
}

// Update resource, returning (status, body) list. Log activity to app_log
function update_resource($sparkpost_host, $sparkpost_api_key, $resource, $id, $body)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/" . $resource . "/" . urlencode($id);
    $method = "PUT";
    $req_hdrs = [
        "Content-Type" => "application/json",
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    try {
        $res = $client->request($method, $req_uri, ["json" => $body, "headers" => $req_hdrs, "timeout" => 30]);
        if($res->getStatusCode() != 200) {
            $app_log->warning("Unexpected status code " . $res->getStatusCode() .
                " from " . $req_uri . " : " . $res->getReasonPhrase());
        } else {
            $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        }
        return array($res->getStatusCode(), json_decode($res->getBody()));
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $app_log->error($e);
        return null;
    }
}

// Delete resource, returning (status, body) list. Log activity to app_log
function delete_resource($sparkpost_host, $sparkpost_api_key, $resource, $id)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $req_uri = $sparkpost_host. "/api/v1/" . $resource . "/" . urlencode($id);
    $method = "DELETE";
    $req_hdrs = [
        "Accept" => "application/json",
        "Authorization" => $sparkpost_api_key
    ];
    try {
        $res = $client->request($method, $req_uri, ["headers" => $req_hdrs, "timeout" => 30]);
        if($res->getStatusCode() != 200) {
            $app_log->warning("Unexpected status code " . $res->getStatusCode() .
                " from " . $req_uri . " : " . $res->getReasonPhrase());
        } else {
            $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        }
        return array($res->getStatusCode(), json_decode($res->getBody()));
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $app_log->error($e);
        return null;
    }
}

//--------------------------------------------------------------------------------------------------------------------
// Target and MX record checking
//--------------------------------------------------------------------------------------------------------------------

function check_target($req_uri)
{
    global $app_log;
    $client = new \GuzzleHttp\Client(["http_errors" => false]);
    $method = "POST";
    $req_hdrs = [
        "Accept" => "application/json",
    ];
    try {
        $res = $client->request($method, $req_uri, ["json" => [], "headers" => $req_hdrs, "timeout" => 5]);
        $app_log->info($method . " " . $req_uri . " " . $res->getStatusCode());
        return $res->getStatusCode();
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $app_log->error($e);
        return null;
    }
}

function check_mx($req_uri)
{
    global $app_log;
    $records = [];
    $ok = getmxrr($req_uri, $records);
    $app_log->info("DNS lookup to " . $req_uri . " result " . json_encode($records));
    // Check for good return code and all three standard MX record values to be present and correct
    return ($ok && in_array("rx1.sparkpostmail.com", $records) && in_array("rx2.sparkpostmail.com", $records) && in_array("rx3.sparkpostmail.com", $records));
}