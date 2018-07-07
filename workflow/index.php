<?php
//--------------------------------------------
// Workflow Manager
// Allows previews to be selected for review and approval
// Approved templates can be sent to SparkPost for use
//--------------------------------------------

include('../m_func.php');

$p = getParams("../suite.ini");
$adminParams = $p["admin"];
$TopHome = $adminParams["TopHome"];//Top Level URL

// make sure we start with a secure connection.
if ($_SERVER['SERVER_PORT'] != "443"){
   header("Location: https://".$TopHome."");
   die();
}


?>

<html>

 <head>
  <title>
   Template Library Manager
  </title>
  <link rel="stylesheet" type="text/css" href="/config/style.css">
 </head>
 <body>
  <h1>
  Template Library Manager
  </h1>
 &nbsp; <a href="/">Go Back</a>

<p>
<h2>Select a function:</h2> </br>
<table class="table_menu">
    <tr class="stripy">
        <th class="value">Template Name/Link</th>
        <th class="name">Thumbnail</th>
        <th class="name">Status</th>
        <th class="name">SPID</th>
    </tr>
    <tr class="stripy">
        <td class="value">Template Name/Link</td>
        <td class="name">Thumbnail</td>
        <td class="name">Status</td>
        <td class="name">SPID</td>
    </tr>
</table>

</p>
 </body>
</html>


