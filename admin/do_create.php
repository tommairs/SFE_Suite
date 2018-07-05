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
if(!empty($_POST)) {

    $body = [
        "name" => get_elem_mandatory($_POST, "name"),
        "target" => get_elem_mandatory($_POST, "target"),
        "auth_token" => get_elem($_POST, "auth_token"),
        "match" => [
            "domain" => get_elem_mandatory($_POST, "match_domain"),
            "protocol" => "SMTP"
        ]
    ];
    list($res, $d) = create_ir_hook($sparkpost_host, $sparkpost_api_key, $app_log, $body);
    if($res == 200) {
        echo "Webhook created";
    } else {
        echo "<h1> Problem creating webhook: " . $d->errors[0]->description . "</h1>";
    }
} else {
    $app_log->error("Empty POST received .. weird");
}
?>

<br>
<p> Please press BACK button in your browser and try again.
</p>
</body>
</html>