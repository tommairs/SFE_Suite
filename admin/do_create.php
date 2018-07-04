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
$d = get_ir_hooks($sparkpost_host, $sparkpost_api_key, $app_log);

echo "Placeholder"
?>

