<?php
// Read email to a var
// Capture inbound HTTP REQUEST
$verb = $_SERVER['REQUEST_METHOD'];
$authtoken = $_SERVER['HTTP_X-MessageSystems-Webhook-Token'];
$filepath = "/var/www/html/seapps/vib/data/";
$urlpath = "http://app.trymsys.net/seapps/vib/data/";
if ($verb == "POST") {
  if ($authtoken="<redacted>"){
    $body = file_get_contents("php://input");
    $result = json_decode($body,TRUE);
    
    // Extract target location and save it
    $rawmessage.= $result[0]['msys']['relay_message']['content']['html'];
    preg_match('/http(.*)\/\/(.*)l=([a-zA-Z\d$_-]+)/',$rawmessage,$viblink);
    $location = $viblink[3];
    // Add link to TEXT version
    $htmlpart = "<html><body><p><center>";
    $htmlpart .= "<a href=\"".$urlpath.$location."-txt.html\">Click here for any available TEXT version</a></center><br>";
    $htmlpart .= $result[0]['msys']['relay_message']['content']['html'];
    $htmlpart .= "</body></html>";
    // Add link to HTML version
    $textpart = "<html><body><p><center>";
    $textpart .= "<a href=\"".$urlpath.$location.".html\">Click here for any available HTML version</a></center><br><pre>";
    $textpart .= $result[0]['msys']['relay_message']['content']['text'];
    $textpart .= "</pre></body></html>";
    file_put_contents ($filepath.$location.".html",$htmlpart);
    file_put_contents ($filepath.$location."-txt.html",$textpart);
  }
}
?>
