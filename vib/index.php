<?php
$location = $_GET['l'];
if ($location) {
  include('data/'.$location);
}
else {
  echo "That object is not available";
}
?>
