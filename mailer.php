<?php
//--------------------------------------------
// SparkPost Mailer
// Accepts a post of data and transforms it to a Sparkpost transmissions call.
//--------------------------------------------

include('m_func.php');

/*
$thisDir = dirname(__FILE__); 
require_once $thisDir.'/vendor/autoload.php';
require_once $thisDir.'/app_common.php';
*/

$p = getParams("suite.ini");
$adminParams = $p["admin"];
$spParams = $p["SparkPost"];
$moParams = $p["Editor"];
$spmaillog = $spParams["logfile"];
//$app_log = new App_log($adminParams["logdir"], basename(__FILE__));

$mailhost = $spParams["sparkpost_host"];
$authkey = $spParams["sparkpost_api_key"];
$alertsenderemail = $moParams["alertsenderemail"];
$alertsendername = $moParams["alertsendername"];
$securitycode = $moParams["securitycode"];

//    $app_log->info("SP Mailer router started");

$to = $_POST['email'];
$password = $_POST['password'];
$controlcode = $_POST['controlcode'];
$subject = $_POST['subject'];
$headers = $_POST['headers'];
$html = json_encode($_POST['html']);
$subs = $_POST['subs'];

$html = preg_replace('/%2F/','/',$html);
$html = preg_replace('/%3A/',':',$html);
$html = preg_replace('/src="\/img\/\?/','',$html);
//$html = preg_replace('/&amp;method=resize&amp;params=[0-9%A-Znul]+"/','"',$html);

file_put_contents($spmaillog, "Started mail build for item ".$controlcode."\r\n");

/* for debugging only...
file_put_contents("./postdata.txt", "Collected data\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $mailhost ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $apikey_ ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $alertsenderemail ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $alertsendername ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $to ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $password ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $securitycode ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $controlcode ."\r\n",FILE_APPEND);
file_put_contents("./postdata.txt", $subject ."\r\n",FILE_APPEND);
*/


$parts = explode(">",$to);
if($parts[0]){
  $to_pretty = ltrim($parts[0],"<");
}
else{
  $to_pretty = " ";
}
if($parts[1]){
  $to_email = trim($parts[1]);
}
else{
  $to_email = $to;
}


if ($password != $securitycode){

//    $app_log->info("SP Mailer aborted with bad security key");
file_put_contents($spmaillog,"Exit with security failure \r\n",FILE_APPEND);
  exit;
}

//    $app_log->info("SP Mailer writing out JSON");


// If this is a valid request, process the transmission
//file_put_contents("./postdata.txt", "Firing up Sparky... \r\n",FILE_APPEND);

$myjson = '{
  "options": {
    "open_tracking": true,
    "click_tracking": true
  },
  "campaign_id": "SFE Test Messages",
  "metadata": {},
  "substitution_data": {},
  "recipients": [
    {
      "address": {
        "email": "'.$to_email.'",
        "name": "'.$to_pretty.'"
      },
      "substitution_data": {}
    }
  ],
  "content": {
      "from": {
        "name": "'.$alertsendername.'",
        "email": "'.$alertsenderemail.'"
      },
      "subject": "'.$subject.'",
      "text": "This messages is HTML only",
      "html": '.$html.'
    }
  }
';


//    $app_log->info("SP Mailer wrote JSON to textfile.txt");

//file_put_contents("./postdata.txt", $myjson ."\r\n",FILE_APPEND);

$pfile="previews/".$controlcode.".html";

$mypreview = "<html><body onload='window.opener.close();'><p>";
$mypreview .= "FROM: <$alertsendername> $alertsenderemail <br>";
$mypreview .= "TO: <$to_pretty> $to_email <br>";
$mypreview .= "SUBJECT: $subject  <br><br>";
$mypreview .= $html;

file_put_contents($pfile, $mypreview);

// open preview in a new window

//header("location: preview.php?content=$controlcode");

/******* Ship it to SparkPost **********/

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "$mailhost/api/v1/transmissions");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $myjson);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: $authkey"
  ));

  $response = curl_exec($ch);
  curl_close($ch);

//var_dump($response);

//  $r_text = implode("\r\n",$response);

//file_put_contents("./postdata.txt", "RESPONSE = ". $r_text ."\r\n",FILE_APPEND);
file_put_contents($spmaillog, "Mail sent to ".$to_email."  \r\n",FILE_APPEND);

?>
