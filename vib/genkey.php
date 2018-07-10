<?php
// Provide API access to a generation tool that provides a UUID for ViewInBrowser
// Take in the CampaignID and email address
// Use the current timestamp and calculate a UUID
// Return the UUID that can be included in the SparkPost template
// Variable declarations:
// Set the root URL for viewInBrowser link. Default is THIS FQDN 
$URL = $_SERVER['SERVER_NAME'];
// $URL = "";
// Set the desired protocol
$proto = "https://";
// Capture inbound HTTP REQUEST
  $verb = $_SERVER['REQUEST_METHOD'];
  if ($verb == "GET") {
    $body = file_get_contents("php://input");
   //Generate large random number and combine with current time to make UUID
    $lownum = 100000;
    $highnum = 999999;
    $arnd = rand($lownum,$highnum);
    $t = time();
    $UUID = "$t"."$arnd";
    echo $proto.$URL."?l=".$UUID;
  }
?>
