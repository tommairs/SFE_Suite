<html>
<head>
<link rel="stylesheet" type="text/css" href="/admin/style.css">
</head>
<body>
<p>
<h1>Settings</h1>

<table>
    <tr>
        <td class="title"><h2>Inbound Relay Webhook Management</h2></td>
        <td class="title2"><a class="button" href="create.php">Create</a></td>
    </tr>
</table>


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
list($res, $d) = get_ir_hooks($sparkpost_host, $sparkpost_api_key);

echo '<table>';
echo '<tr class="stripy"><th class="name">Name</th> <th class="target">Target</th> <th class="auth_token">auth_token</th> <th class="match_domain">match.domain</th></tr>';

foreach($d ->results as $i => $k) {
    echo '<tr class="stripy"><td class="name"><a href="details.php?id=' . $k->id . '">' . $k->name . '</a></td>
    <td class="target">' . $k->target . '</td>
    <td class="auth_token">' . $k->auth_token . '</td>
    <td class="match_domain">' . $k->match->domain . '</td></tr>';
}
echo '</table>';

?>

<br>
<a href="/">Go Back</a>
</p>
</body>
</html>
