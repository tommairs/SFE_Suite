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

// Set up appropriately for either Create or Update
if(empty($_GET)) {
    echo "Empty query string - stopping.";
    exit(1);
}
$ir_id = get_elem_mandatory($_GET, "id");

// Get the domain from this webhook so that we can also delete it
list($res, $d) = get_resource($sparkpost_host, $sparkpost_api_key, "relay-webhooks", $ir_id);
if ($res != 200) {
    echo "Can't get relay webhook id " . $ir_id;
    exit(1);
}
$domain = $d->results->match->domain;

list($res, $d) = delete_resource($sparkpost_host, $sparkpost_api_key, "relay-webhooks", $ir_id);
if ($res == 200) {
    echo "Webhook deleted <br>";
} else {
    echo "Problem deleting webhook: " . $d->errors[0]->message . "<br>";
}

list($res, $d) = delete_resource($sparkpost_host, $sparkpost_api_key, "inbound-domains", $domain);
if ($res == 200) {
    echo "Inbound domain deleted <br>";
} else {
    echo "Problem deleting inbound domain: " . $d->errors[0]->message . "<br>";
}
?>

<a href="/admin">Back to admin</a>
</body>
</html>