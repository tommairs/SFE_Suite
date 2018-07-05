<?php

include('env.ini');
$to = $_POST['email'];
$password = $_POST['password'];
$controlcode = $_POST['controlcode'];
$subject = $_POST['subject'];
$headers = $_POST['headers'];
$html = $_POST['html'];
$subs = $_POST['subs'];

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


// FIXME - this is all for debugging
/*
$myfile = fopen("testfile.txt", "w");

fwrite($myfile, "<html><body><p>");

fwrite($myfile,  "$to<br>");
fwrite($myfile,  "$password <br>");
fwrite($myfile,  "$subject <br>");
fwrite($myfile,  "$headers <br>");
fwrite($myfile,  "$html <br>");


fwrite($myfile,  "</p></body></html>");
fclose($myfile);
*/

if ($password != $securitycode){
  exit;
}

// If this is a valid request, process the transmission
require 'vendor/autoload.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpClient, ['key'=>$authkey]);

$mailhost = "app.sparkpost.com";
$alertsenderemail = "sfetest@bounces.trymsys.net";
$alertsenderename = "SFESuite Tester";
$securitycode = "lkasjdopqjdkqmccnqpqwdlkmqdop";



$promise = $sparky->transmissions->post([
    'content' => [
        'from' => [
            'name' => $alertsenderename,
            'email' => $alertsenderemail,
        ],
        'subject' => $subject,
        'html' => $html,
        'text' => 'This transmission is HTML Only.',
    ],
    'substitution_data' => [$subs],
    'recipients' => [
        [
            'address' => [
                'name' => $to_pretty,
                'email' => $to_email,
            ],
        ],
    ],
]);

$myjson = "[
    'content' => [
        'from' => [
            'name' => $alertsenderename,
            'email' => $alertsenderemail,
        ],
        'subject' => $subject,
        'html' => $html,
        'text' => 'This transmission is HTML Only.',
    ],
    'substitution_data' => [$subs],
    'recipients' => [
        [
            'address' => [
                'name' => $to_pretty,
                'email' => $to_email,
            ],
        ],
    ],
]";
file_put_contents("testfile.txt", $myjson);

/*
$today = time();
$random = rand(10000,99999);
$controlcode = $today.$random;
*/

$pfile="previews/".$controlcode.".html";
//$pfile="previews/123456123456123456.html";

$mypreview = "<html><body onload='window.opener.close();'><p>";
$mypreview .= "FROM: <$alertsenderename> $alertsenderemail <br>";
$mypreview .= "TO: <$to_pretty> $to_email <br>";
$mypreview .= "SUBJECT: $subject  <br><br>";
$mypreview .= "$html";

file_put_contents($pfile, $mypreview);

// open preview in a new window

//header("location: preview.php?content=$controlcode");


?>
