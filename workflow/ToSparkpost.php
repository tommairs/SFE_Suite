<?php

include ('../common.php');

$spParams = $p["SparkPost"];
$mailhost = $spParams["sparkpost_host"];
$authkey = $spParams["sparkpost_api_key"];
$alertsenderemail = $moParams["alertsenderemail"];
$alertsendername = $moParams["alertsendername"];
$securitycode = $moParams["securitycode"];

$tid = $_POST['tid'];
$action = $_POST['action'];
$name = $_POST['name'];
$sws = $_POST['shared_with_subaccounts'];
$from = $_POST['from'];
$subject = json_encode($_POST['subject']);
$html = json_encode($_POST['html']);
$o_track = $_POST['o_track'];
$c_track = $_POST['c_track'];
$description = $_POST['description'];
$p_from = $_POST['p_from'];
$reply_to = $_POST['reply_to'];
$username =  $_SESSION['FullName'];
$Version = $_POST['Version'];


if (!$o_track){$o_track = "true";}
if (!$c_track){$c_track = "true";}
if (!$sws){$sws="false";}

// FIXME debugging
if (strlen($html) < 3){
  $html = "<html><body><p>Modified Placeholder</p></body></html>";
}

$html=json_encode($html);

if ($action == "publish"){
  $newSPID = strtolower(str_replace(' ', '_', $name));

  $json = '{
    "id": "'.$newSPID.'",
    "name": "'.$name.'",
    "published": true,
  ';
  if($description != ""){
    $json .='    "description": "'.$description.'",';
  }
  $json .= '  "shared_with_subaccounts": '.$sws.',
    "options": {
      "open_tracking": '.$o_track.',
      "click_tracking": '.$c_track.'
    },
    "content": {
      "from": {
        "email": "'.$from.'",
        "name": "'.$p_from.'"
      },
';

  if($reply_to){
    $json .='  "reply_to": "'.$reply_to.'",';
  }

$json .='      "text": "This message is HTML Only",
      "subject": '.$subject.',
      "html": '.$html.'
    }
  }';


//echo $json;


  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "$mailhost/api/v1/templates");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: $authkey"
  ));

  $response = curl_exec($ch);
  $res = json_decode($response,1);
  curl_close($ch);

//var_dump($response);

//print_r($res["errors"][0]["message"]);

if ($res["errors"][0]["message"] == "template already exists"){
  echo "This Template already exists... updating<br>";
  
$url = "$mailhost/api/v1/templates/$newSPID?update_published=true";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: $authkey"
  ));

  $response = curl_exec($ch);
  $res = json_decode($response,1);
  curl_close($ch);
  if (!$response){
    echo " There was an error updating this template <br>";
    var_dump($response);
    exit;
  }

}


if ($res["results"]["id"] != ""){
  $templatename = $res["results"]["id"];
};


$today_f = date("d-m-Y",$today);

  $query = "UPDATE Templates set Editor = '".$username."', Last_Edit = ".$today.", Status = 'Published', SPID='$newSPID' WHERE id = ".$tid."";
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

/*
  $query = "INSERT INTO TemplateHistory (Editor,Action,Version,HTML,TemplateName,Last_Edit,Status,SPID,tid) VALUES ($Editor,$action,$Version,$html,$TemplateName,$today,$Status,$newSPID,$tid)";
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

*/
       header('Location: /workflow');
  
  exit;
}




// revoke,
if ($action == "revoke"){
  $query = "UPDATE Templates set Status = 'Revoked' WHERE id = ".$tid."";
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



?>
