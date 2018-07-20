<?php
//--------------------------------------------
// Workflow Selct Switch
//--------------------------------------------
  session_start();

  require('../common.php');
  // add this to enable the user credentials system
  if (!$_SESSION['AccessToken']){
       $src= base64_encode('/workflow/index.php');
       header('Location: /usermgr/security.php?src='.$src.'');
  }

  // Set page level role access
  $MyRole = $_SESSION['Role'];
  if ($MyRole != "Admin"){
    echo "You are not authorized to view this page.";
    exit;
  }

  $spParams = $p["SparkPost"];
  $mailhost = $spParams["sparkpost_host"];
  $authkey = $spParams["sparkpost_api_key"];


$htmlheader = '
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

';

  $action = $_POST['action'];
  $tid = $_POST['id'];

// edit,
if ($action == "edit"){
  $query = "UPDATE Templates set Status = 'In Progress' WHERE id = ".$tid."";
   $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

       header('Location: /mosaico/editor.html?0.17.3#btjz6ea');

}

// review,
if ($action == "review"){
  $query = "UPDATE Templates set Status = 'Review Request' WHERE id = ".$tid."";
   $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

//  while ($row = $stmt->fetch()){

       header('Location: /workflow');
}

// getapproval,
if ($action == "getapproval"){
  $query = "UPDATE Templates set Status = 'Waiting for Approval' WHERE id = ".$tid."";
   $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

//  while ($row = $stmt->fetch()){
       header('Location: /workflow');
}

// versions,
if ($action == "versions"){


//FIXME
// Need to get $tid here
if (!$tid){
  $tid = =1234;
}

echo $htmlheader;

echo '
 <p>
<h2>Template History</h2> </br>
<table class="table_menu">
    <tr class="stripy">
        <th class="value">Last Edit</th>
        <th class="value">Editied byBy</th>
        <th class="value">Action</th>
        <th class="value">Version #</th>
        <th class="value">Use this version?</th>
    </tr>
';

 $query = "SELECT id, Last_Edit, Last_Editor, Last_Action, Version FROM TemplateHistory WHERE tid=".$tid."";
   $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

  while ($row = $stmt->fetch()){
  
    //FIXME
    echo '
      <tr>
        <td class="value">'. $row[Last_Edit] .'</td>
        <td class="value">'. $row[Last_Editor] .'</td>
        <td class="value">'. $row[Last_Action] .'</td>
        <td class="value">'. $row[Version] .'</td>
        <td class="value">[REVERT]</td>
      </tr>
    ';
  }

  echo "</table></body></html>";
}
// publish,
if ($action == "publish"){

echo $htmlheader;

echo '
<p>
<!--
<form action='. $mailhost .'/api/v1/templates" method=POST>
-->
<form action="ToSparkpost.php" method=POST>
Unique template name <input type=text name="name" size=50><br>
Is this shared with subaccounts? <input type=checkbox name="shared_with_subaccounts" value=false><br>
Valid From address: <input type=text name="from" size=50> <br>
Subject line to use:  <input type=text name="subject" size=50> <br>
<input type=hidden name="html" value="'.$html.'">
<input type=hidden name="action" value="'.$action.'">
<input type=hidden name="tid" value="'.$tid.'">
<input type=hidden name="Version" value="'.$Version.'">
<input type=hidden name="TemplateName" value="'.$TemplateName.'">
<input type=submit name="submit" value="Send To SparkPost">
<input type=reset name="reset" value="CANCEL" OnClick="javascript:hitsory.back(1);">

</form>
';

exit;
}

// revoke, 
if ($action == "revoke"){
   $query = "UPDATE Templates set Status = 'Revoked' WHERE id = '". $tid ."'";
   $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

       header('Location: /workflow');

}


// delete
if ($action == "delete"){

  $query = "UPDATE Templates set Status = 'Deleted' WHERE id = ".$tid."";
   $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

       header('Location: /workflow');

}

?>
