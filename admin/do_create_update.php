<html>
<head>
    <link rel="stylesheet" type="text/css" href="/admin/style.css">
</head>
<body>

<?php
//--------------------------------------------------------------------------------------------------------------------
// Main code
//--------------------------------------------------------------------------------------------------------------------
require_once "ir_hook_admin.php";

//--- Get configuration
$p = getParams("../suite.ini");
// Get logging set up early on, for error reporting etc
$app_log = new App_log($p["admin"]["logdir"], basename(__FILE__));

$sparkpost_api_key = get_config_mandatory($p["SparkPost"], "sparkpost_api_key");
$sparkpost_host = get_config_mandatory($p["SparkPost"], "sparkpost_host");

// Validate the entered values as far as we can
if(empty($_POST)) {
    $app_log->error("Empty POST received .. weird");
    exit(1);
}

// Set up appropriately for either Create or Update
if(empty($_GET)) {
    $method = "POST";                   // This will be a Create attempt
} else {
    $method = "PUT";                    // This will be a Create attempt
    $ir_id = get_elem_mandatory($_GET, "id");
}

// Check if the inbound domain exists
$new_domain = get_elem_mandatory($_POST, "match_domain");
list($res, $d) = get_resource($sparkpost_host, $sparkpost_api_key, "inbound-domains",  $new_domain);
$domain_ok = false;

if($res == 200) {
    echo "Inbound domain " . $new_domain . " already exists in your account<br>";
    $domain_ok = true;
} elseif($res == 403) {
    echo "API key doesn't have inbound-domains permissions<br>";
} elseif($res == 404) {
    // Resource could not be found - this is OK - let's try to create it
    list($res, $d) = create_resource($sparkpost_host, $sparkpost_api_key, "inbound-domains", [ "domain" => $new_domain ]);
    if($res == 200) {
        echo "Successfully created inbound domain " . $new_domain . "<br>";
        $domain_ok = true;
    }
    else {
        echo "Could not create inbound domain " . $new_domain . "<br>" . $d->errors[0]->message . " : " . $d->errors[0]->description ."<br>";
    }
}

// Only try to set up the webhook if the domain is already registered
if($domain_ok) {
    $body = [
        "name" => get_elem_mandatory($_POST, "name"),
        "target" => get_elem_mandatory($_POST, "target"),
        "auth_token" => get_elem($_POST, "auth_token"),
        "match" => [
            "domain" => $new_domain,
            "protocol" => "SMTP"
        ]
    ];
    if ($method == "POST") {
        // Create
        list($res, $d) = create_resource($sparkpost_host, $sparkpost_api_key, "relay-webhooks", $body);
        if ($res == 200) {
            echo "Webhook created / updated<br>";
        } else {
            echo "Problem creating / updating webhook: " . $ir_id . "<br>" . $d->errors[0]->message . " : " . $d->errors[0]->description ."<br>";
        }
    } elseif ($method == "PUT") {
        // Check if we should deregister the current inbound_domain after doing the update
        list($res, $d) = get_resource($sparkpost_host, $sparkpost_api_key, "relay-webhooks", $ir_id);
        if($res == 200) {
            $old_domain = $d->results->match->domain;
        } else {
            echo "Problem reading existing webhook: " . $ir_id . "<br>" . $d->errors[0]->message . " : " . $d->errors[0]->description ."<br>";
            $old_domain = null;
        }
        // Update the relay webhook
        list($res, $d) = update_resource($sparkpost_host, $sparkpost_api_key, "relay-webhooks", $ir_id, $body);
        if ($res == 200) {
            echo "Webhook created / updated<br>";
        } else {
            echo "Problem creating / updating webhook: " . $ir_id . "<br>" . $d->errors[0]->message . " : " . $d->errors[0]->description ."<br>";
        }
        if($old_domain && $old_domain != $new_domain) {
            // deregister the old domain
            list($res, $d) = delete_resource($sparkpost_host, $sparkpost_api_key, "inbound-domains", $old_domain);
            if ($res == 200) {
                echo "Old inbound domain ". $old_domain . " successfully removed <br>";
            } else {
                echo "Problem removing old inbound domain: " . $ir_id . "<br>" . $d->errors[0]->message . " : " . $d->errors[0]->description ."<br>";
            }
        }
    } else {
        echo "Invalid Query parameter - stopping<br>";
    }
}
?>

<a href="/admin">Back to admin</a>
</body>
</html>