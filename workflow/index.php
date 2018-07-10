<?php
//--------------------------------------------
// Workflow Manager
// Allows previews to be selected for review and approval
// Approved templates can be sent to SparkPost for use
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
<!--
        <th class="name">Thumbnail</th>
-->
        <th class="name">Owner</th>
        <th class="name">Create Date</th>
        <th class="name">Status</th>
        <th class="spid">SPID</th>
        <th class="name">Action</th>
    </tr>

<?php

// Select templates from DB
// list in the table

  $query = "SELECT Owner, Last_Edit, TemplateName, Status, SPID FROM Templates";
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


//-----------------------------------------
// For each record...
//-----------------------------------------
  echo '<form> 
        <tr class="stripy">
        <td class="value"><a href=https://suite.trymsys.net/previews/'.  $row['TemplateName'] .'.html target=_blank>Template: '.  $row['TemplateName'] .'</a></td>
 <!--
       <td class="name"><a href=/previews/153083071914283.png target=_blank><img src=/previews/153083071914283.png width=50 height=50></a></td>
-->
        <td class="name">'. $row['Owner'] .'</td>
        <td class="date">'. date("d-m-Y",$row['Last_Edit']) .'</td>
        <td class="Status_'.  str_replace(' ', '', $row['Status']) .'">'.  $row['Status'] .'</td>
        <td class="spid">'. $row['SPID'] .'</td>
        <td class="name">
          <select>
            <option value="--">Select an action</option>
            <option value="edit">Edit</option>
            <option value="review">Request Review</option>
            <option value="getapproval">Request Approval</option>
            <option value="versions">Show History</option>
  ';

  if (($MyRole == "Admin") OR ($MyRole == "Approver")){
    echo '
            <option value="publish">Approve for Publication</option>
            <option value="revoke">Revoke Publication</option>
            <option value="delete">Delete</option>
    ';
  }
echo '
          </select>
        </td>
    </tr>
</form>';

//-----------------------------------------
  }



echo '
</table>
</p>
 </body>
</html>
';

?>

