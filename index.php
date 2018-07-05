<?php

/*
// make sure we start with a secure connection.
if ($_SERVER['SERVER_PORT'] != "443"){
   header("Location: https://suite.trymsys.net");
   die();
}

*/

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
<b>Select a function:</b> </br>
<a href="/mosaico/">Template Editor</a><br>
<a href="/workflow/mgr.html">Template Library Managager</a><br>
<a href="/config">Manage Configuration  Data</a><br>
<a href="/admin/">Relay Webhook Administration</a><br>
</p>
 </body>
</html>
