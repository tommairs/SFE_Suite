<?php

include ('../common.php');

$spParams = $p["SparkPost"];
$mailhost = $spParams["sparkpost_host"];
$authkey = $spParams["sparkpost_api_key"];
$alertsenderemail = $moParams["alertsenderemail"];
$alertsendername = $moParams["alertsendername"];
$securitycode = $moParams["securitycode"];


$action = $_POST['action'];
$name = $_POST['name'];
$sws = $_POST['shared_with_subaccounts'];
$from = $_POST['from'];
$subject = json_encode($_POST['subject']);
$html = $_POST['html'];

if ($html == ""){
  $html = "<html><body><p>Placeholder</p></body></html>";
}

$html=json_encode($html);

if ($action == "publish"){
  $newSPID = strtolower(str_replace(' ', '_', $name));

  $json = '{
    "id": "'.$newSPID.'",
    "name": "'.$name.'",
    "published": true,
    "description": "'.$description.'",
    "shared_with_subaccounts": '.$sws.',
    "options": {
      "open_tracking": '.$o_track.',
      "click_tracking": '.$c_track.'
    },
    "content": {
      "from": {
        "email": "'.$from.'",
        "name": "$p_from"
      },
      "reply_to": "'.$reply_to.'",
      "text": "This message is HTML Only",
      "from": "'.$from.'",
      "subject": '.$subject.',
      "html": '.$html.'
    }
  }';




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
  curl_close($ch);

  echo "Response: ". var_dump($response);


/*



  $query = "UPDATE Templates set Status = 'Published', SPID="$newSPID" WHERE id = ".$tid."";
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
//}

*/
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
