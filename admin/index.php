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
list($res, $d) = get_resource_list($sparkpost_host, $sparkpost_api_key, "relay-webhooks");

echo '<table>';
echo '<tr class="stripy">
    <th class="name">Name</th>
    <th class="target">Target</th>
    <th class="auth_token">auth_token</th>
    <th class="match_domain">match.domain</th>
    <th class="mx_check">MX check</th>
    <th class="endpoint_check">Target check</th>
    </tr>';

foreach($d ->results as $i => $k) {
    $endpoint_str = (check_target($k->target) == 200) ? '<span style="color:lightgreen">&#x2714;</span>' : '<span style="color:red">x</span>';
    $mx_str = (check_mx($k->match->domain) == 200) ? '<span style="color:lightgreen">&#x2714;</span>' : '<span style="color:red">x</span>';

    echo '<tr class="stripy">
    <td class="name"><a href="edit.php?id=' . $k->id . '">' . $k->name . '</a></td>
    <td class="target">' . $k->target . '</td>
    <td class="auth_token">' . $k->auth_token . '</td>
    <td class="match_domain">' . $k->match->domain . '</td>
    <td class="mx_check">' . $mx_str . '</td>
    <td class="endpoint_check">' . $endpoint_str . '</td>
    </tr>';
}
echo '</table>';
?>

<br>
<a href="/">Go Back</a>
</p>
</body>
</html>
