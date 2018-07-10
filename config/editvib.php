<?php
//--------------------------------------------
// VIB Config editor
//--------------------------------------------
  session_start();

  // add this to enable the user credentials system
  if (!$_SESSION['AccessToken']){
       $src= base64_encode('/vib/editvib.php');
       header('Location: usermgr/security.php?src='.$src.'');
  }

  // Set page level role access 
  $MyRole = $_SESSION['Role'];
  if ($MyRole != "Admin"){
    echo "You are not authorized to view this page.";
    exit;
  }

?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="/config/style.css">
</head>
<body>
<p>
<h1>View In Browser Settings</h1>
 &nbsp; <a href="/">Go Back</a>
<table>
    <tr>
        <td class="title"><h2>System Variables Config Management</h2></td>
        <td class="title2"><a class="button" href="update.php">Update</a></td>
    </tr>
</table>


<?php
//--------------------------------------------------------------------------------------------------------------------
// Main code
//--------------------------------------------------------------------------------------------------------------------

//--- Get configuration
require_once '../vendor/autoload.php';
require_once '../app_common.php';
//include('../m_func.php');
$p = getParams("../suite.ini");
// Get logging set up early on, for error reporting etc
//$app_log = new App_log($p["admin"]["logdir"], basename(__FILE__));


echo '<form>';
echo '<table>';
echo '<tr class="stripy">
    <th class="name">Name</th>
    <th class="value">Target</th>
    </tr>';

foreach($p as $i => $k) {
    if ($i == "vib"){
      echo '<tr class="stripy">
      <td class="name"><b>' .$i . '</b></td>
      <td class="value">&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - &nbsp;</td>
      </tr>';
      foreach($k as $l => $m) {
        echo '<tr class="stripy">
        <td class="name">' .$l . '</td>
        <td class="value"><input type=text value="'. $m .'" size=50 name='. $i . '.' . $l .'></td>
        </tr>';
      }
    }
}

echo '</table>';
echo '</form>';

?>

<br>
</p>
</body>
</html>
