<?php
  session_start(); 

  $SuiteRoot = "/var/www/html/SFE_Suite/";

  require( $SuiteRoot.'m_func.php');
  $p = getParams( $SuiteRoot.'suite.ini');

  $dbParams = $p['DataStore'];
  $dbhost = $dbParams['dbhost'];
  $dbuser = $dbParams['dbuser'];
  $dbpass = $dbParams['dbpass'];
  $dbname = $dbParams['dbname'];
  $TZ = $adminParams['TZ'];

  $adminParams = $p["admin"];
  $TopHome = $adminParams["TopHome"];//Top Level URL

  date_default_timezone_set($TZ);
  $today = time();

  if (!is_writable(session_save_path())) {
    echo 'Session path "'.session_save_path().'" is not writable for PHP!'; 
  }

  $AccessToken = $_SESSION['AccessToken'];
  // make sure we start with a secure connection.
  if ($_SERVER['SERVER_PORT'] != "443"){
     header("Location: https://".$TopHome."");
     die();
  }
  $AccParts = explode("|",(base64_decode($AccessToken)));
  $Email =   $_SESSION['email'];
  $aAdmin = substr($AccParts[1],0,1);
  $aEditor = substr($AccParts[1],1,1);
  $aApprover = substr($AccParts[1],2,1);
  $aReviewer = substr($AccParts[1],3,1);


    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
    try 
    { 
        $db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, $options); 
    } 
    catch(PDOException $ex) 
    { 
        // Note: On a production website, you should not output $ex->getMessage(). 
        // It may provide an attacker with helpful information about your code 
        die("Failed to connect to the database: " . $ex->getMessage()); 
    } 
     
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    // This statement configures PDO to return database rows from your database using an associative 
    // array.  This means the array will have string indexes, where the string value 
    // represents the name of the column in your database. 
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    // http://php.net/manual/en/security.magicquotes.php 
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
     
    header('Content-Type: text/html; charset=utf-8'); 
   


