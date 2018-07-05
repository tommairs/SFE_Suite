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

if(!empty($_GET)) {
    $id = get_elem_mandatory($_GET, "id");
    list($res, $d) = get_resource($sparkpost_host, $sparkpost_api_key, "relay-webhooks",  $id);
    // Prepare default values for forms input
    $name = $d->results->name;
    $target = $d->results->target;
    $auth_token = $d->results->auth_token;
    $match_domain = $d->results->match->domain;

} else {
    $app_log->error("Empty query params received .. weird");
    exit(1);
}
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="/admin/style.css">
</head>
<body>
<p>
<h1>Settings</h1>

<table>
    <tr>
        <td class="title"><h2>Edit Inbound Relay Webhook</h2></td>
        <td class="title2"><a class="button" href="do_delete.php?id=<?= $id ?>">Delete webhook</a></td>
    </tr>
</table>

<form action = "do_create_update.php?id=<?= $id ?>" method="post">
    <fieldset>
        <legend>Inbound Relay Webhook Settings:</legend>
        <p class="entry_descr"> Name:</p>
        <input type="text" name="name" value="<?= $name ?>" size="80"><br>
        <p class = "entry_hint"> A friendly label for your webhook, only used for display</p>
        <br>
        <p class="entry_descr"> Target:</p>
        <input type="text" name="target" value="<?= $target ?>" size="80"><br>
        <p class = "entry_hint"> This is the URL we'll send data to. We recommend the use of https</p>
        <br>
        <p class="entry_descr"> Authentication Token (optional):</p>
        <input type="text" name="auth_token" value="<?= $auth_token ?>" size="80">
        <p class = "entry_hint"> Authentication token will be present in the X-MessageSystems-Webhook-Token header of POST requests to target.<br>
            Your receiver can use this value to confirm the POST is genuine.</p>
        <br>
        <p class="entry_descr"> Match Domain:</p>
        <input type="text" name="match_domain" value="<?= $match_domain ?>" size="80"><br>
        <p class = "entry_hint"> Inbound domain associated with this webhook. You will need to set up DNS MX records for this so that any mail for the above domain will be routed to SparkPost.</p>
        <br>
        <input type="submit" value="Update webhook">
    </fieldset>
</form>
<br>
<a href="/admin">Go Back</a>
</p>
</body>
</html>
