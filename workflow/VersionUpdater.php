<?php

require('common.php');

$Last_Edit = $today;
$Last_Editor = $FullName;
$TemplateName = $_POST['name'];
$HTML = $_POST['html'];
$Notes = $_POST['notes'];
 
$tmpVersion = 0;

$query = "SELECT id, Version FROM TemplateHistory WHERE TemplateName=".$TemplateName." ORDER BY Version ASC";
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
    $tmpVersion = $row[Version];
  }


if ($tmpVersion == 0){
  $Last_Action = "Initial Load"; 
  $Version = 1;
}
else{
  $Last_Action = "Content Edits";
  $Notes = $Notes;
  $Version = $Version + 1;;
}


$query = "INSERT INTO TemplateHistory ( tid, Last_Edit, Last_Editor, Last_Action, Versioni, Notes) VALUES( $tid, $Last_Edit, $Last_Editor, $Last_Action, $Version, $Notes)";
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

 // while ($row = $stmt->fetch()){


       header('Location: /workflow');




?>
