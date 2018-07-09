<?php

/****************************************************************/
// SentMailTo(Email,FullName,Body)                              
//  - Use SparkPost to send an email to the email address using 
//    the  HTML body supplied
/****************************************************************/

function SendMailTo($myEmail,$myFullName,$myBody,$Subject){

  $authkey = $_SESSION['AuthKey'];
  $mailhost = $_SESSION['MailHost'];
  $alertsender = $_SESSION['AlertSender'];
  
   $message_html = $myBody;

  $message_html = preg_replace('/"/',"'",$message_html);
  $message = preg_replace('/<br>/',"\n",$message_html);
  $message= preg_replace('/<p>/',"\n\n",$message);
  $message= preg_replace('/<\/p>/',"",$message);

  $to = $myEmail;

  $rcpt_list = "
    {
      \"address\": {\"email\": \"$to\",\"name\": \"".$FullName."\"},
      \"tags\": [],\"metadata\": {},\"substitution_data\": {}
    }
  ";

  $json = "{
    \"options\": {
      \"open_tracking\": true,\"click_tracking\": true
    },
    \"campaign_id\": \"Password Recovery\",
    \"return_path\": \"postmaster@sedemo.trymsys.net\",
    \"metadata\": {},\"substitution_data\": {},
    \"recipients\": [
        $rcpt_list
    ],
   \"content\": {
      \"from\": {\"name\": \"User Access Password Manager\",\"email\": \"$alertsender\"},
      \"subject\": \"$Subject\",
      \"reply_to\": \"".$Last_Editor." <".$email.">\",
      \"headers\": {},
      \"text\": \"$message\",
      \"html\": \"$message_html\"
    }
  }";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://$mailhost/api/v1/transmissions");
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

//FIXME for debugging
var_dump($response);


  if($response->total_rejected_recipients > 0) {
      echo 'Message could not be sent.';
  } 
  else {
    echo "<p>   Message sent!  </p> ";
  }

}


?>
