<?php
//--------------------------------------------
// Home page - aka:Top Menu
//--------------------------------------------
  session_start();
//include('common.php');
require('security.php');

?>

<html>

 <head>
  <title>
    Simple Front End Suite
  </title>
  <link rel="stylesheet" type="text/css" href="/config/style.css">
 </head>
 <body>
  <h1>
    Simple Front End Suite
  </h1>
<p>
<h2>Select a function:</h2> </br>
<table class="table_menu">
    <tr class="stripy">
        <td class="selector"><a class="button" href="http://<?php echo $TopHome; ?>/mosaico/">GO!</a></td>
        <td class="value"><b>Template Editor</b></td>
    </tr>
    <tr class="stripy">
        <td class="selector"><a class="button" href="/workflow/">GO!</a></td>
        <td class="value"><b>Template Library Manager</b></td>
    </tr>
    <tr class="stripy">
        <td class="selector"><a class="button" href="/config/">GO!</a></td>
        <td class="value"><b>Manage Configuration Data</b></td>
    </tr>
    <tr class="stripy">
        <td class="selector"><a class="button" href="/admin/">GO!</a></td>
        <td class="value"><b>Relay Webhook Administration</b></td>
    </tr>
    <tr class="stripy">
        <td class="selector"><a class="button" href="users.php">GO!</a></td>
        <td class="value"><b>User Management</b></td>
    </tr>
    <tr class="stripy">
        <td class="selector"><a class="button" href="logout.php">GO!</a></td>
        <td class="value"><b>Logout</b></td>
    </tr>

</table>

</p>
 </body>
</html>


