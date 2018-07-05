<?php

// make sure we start with a secure connection.
if ($_SERVER['SERVER_PORT'] != "443"){
   header("Location: https://suite.trymsys.net/workflow/");
   die();
}


?>

<html>

 <head>
  <title>
    Simple Front End Suite
  </title>
  <link rel="stylesheet" type="text/css" href="styles.css">
 </head>
 <body>
  <h1>Template Library Manager</h1>
<a href=/>Top Menu</a>
<h2>Select a template to manage:</h2>
<table cellpadding=5 cellspacing=1 border=1>
<tr bgcolor=#919396>
  <th>Template Name/Link</th>
  <th>Thumb</th>
  <th>Status</th>
  <th>SPID</th>
</tr>
<tr>
  <td><a href="">Template Name</a></td>
  <td>Thumbnail</td>
  <td>In Progress</td>
  <td>0192830981203</td>
</tr>
</table>
 </body>
</html>
