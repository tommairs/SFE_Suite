<?php

// make sure we start with a secure connection.
if ($_SERVER['SERVER_PORT'] != "443"){
   header("Location: https://suite.trymsys.net");
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
  <h1>
    Simple Front End Suite
  </h1>
<p>
<h2>Select a function:</h2> </br>
<table>
<tr><td bgcolor=#f49242><a href="/mosaico/">Template Editor</a><br></td></tr>
<tr><td bgcolor=#f4ca41><a href="/workflow/">Template Library Manager</a><br></td></tr>
<tr><td bgcolor=#85f441><a href="/config/">Manage Configuration Data</a><br></td></tr>
<tr><td bgcolor=#41f4d3><a href="/admin/">Relay Webhook Administration</a><br></td></tr>
</table>
</p>
 </body>
</html>
