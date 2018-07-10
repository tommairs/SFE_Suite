<?php
//--------------------------------------------
// User Account Manager
// bolt-on credentials manager
//--------------------------------------------
  session_start();

// add this to enable the user credentials system
  if (!$_SESSION['AccessToken']){
       $src= base64_encode('/index.php');
       header('Location: usermgr/security.php?src='.$src.'');
  }

  // Set page level role access
  $MyRole = $_SESSION['Role'];
  if ($MyRole != "Admin"){
    echo "You are not authorized to view this page.";
    exit;
  }

require ('../common.php');

?>

<html>

 <head>
  <title>
    User Access Manager
  </title>
  <link rel="stylesheet" type="text/css" href="/config/style.css">
 </head>
 <body>
  <h1>
    Simple Front End Suite
  </h1>
 &nbsp; <a href="/">Go Back</a>
<p>
<h2>Select a function:</h2> </br>
<table class="table_menu">
    <tr class="stripy">
        <td class="selector"><a class="button" href="edituser.php">USERS</a></td>
        <td class="value"><b>Create or Add User Access Credentials</b></td>
    </tr>
    <tr class="stripy">
        <td class="selector"><a class="button" href="editroles.php">ROLES</a></td>
        <td class="value"><b>Manage User Roles</b></td>
    </tr>


</table>

</p>
 </body>
</html>


