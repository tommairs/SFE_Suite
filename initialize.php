<?php


if ($_SERVER['REQUEST_METHOD'] != ""){
  echo "This script must be run from the CLI";
  exit;
}

   include ("env.ini");
   system('clear');
   echo"


      This is the DB initialization script.  Running this will wipe and replace the database configured in [env.ini].
      Please set the desired database name, username, and password in the [env.ini] file BEFORE running this script.

      \$LNHome = \"https://10.79.0.15/myserver-dev/\";  // This is the installed location of the web service  
      \$TZ = 'America/Los_Angeles'; //This is the Linux standard region format
      \$dbhost = 'localhost'; // should be left as \"localhost\"
      \$dbuser = 'mydevuser';  // The desired DB username
      \$dbpass = 'mydevpassword';  // The desired DB Password
      \$dbname = 'db-dev';  // The desired DB Name

      If the above are NOT set, you will be prompted for them below.
      prese CTRL-C to quit. 

  ";
      echo "Root or authorized username for MySQL administration (must have GRANT priv):";
      $user = readline();

      echo "Root or authorized user password for MySQL administration (will not be saved):";
      $pass = readline();

        $LNHome = readline("Base URL of web service (Currently = '$LNHome'):");
        $TZ = readline("TimeZone (Currently = '$TZ') :");
        $dbhost = readline("Database hostname (Currently = '$dbhost') :");
        $dbname = readline("Database Name (Currently = '$dbname') :");
        $dbuser = readline("Database User (Currently = '$dbuser') :");
        $dbpass = readline("Database Password (Currently = '$dbpass') :");

echo "I'll use these settings.:
  ROOT user = $user
  ROOT pass = <REDACTED>
  URL HOME = $LNHome
  Time Zone = $TZ
  DB Host = $dbhost
  DB User = $dbuser
  DB Password = <REDACTED>
  DB Name = $dbname
";

echo "If the above settings are ok, press ENTER/RETURN.  If not press CTRL-C";
        $A = readline();




exit;

// FIXME
 
   echo "
     Creating Database ".$dbname." as user ".$user." ";

    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    try
    {
        $db = new PDO("mysql:host={$dbhost};charset=utf8", $a_user, $a_pass, $options);
    }
    catch(PDOException $ex)
    {
        // Note: On a production website, you should not output $ex->getMessage().
        // It may provide an attacker with helpful information about your code
        die("Failed to connect to the database: " . $ex->getMessage());
    }


//     $db = new PDO('mysql:host=$dbhost;dbname=$dbname;charset=utf8', $a_user, $a_pass);
 
     $query = "CREATE database ".$dbname."";
     $query_params = array(':DB' => $dbname);

echo "<font color=red>". $query ."</font><br>";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>";

        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create DB: " . $ex->getMessage());
        }


     echo "Creating ".$dbname.".Issuers Table ";
$query = "CREATE TABLE ".$dbname.".Issuers ( 
id INT NOT NULL AUTO_INCREMENT,
FullName VARCHAR(50) NOT NULL ,
Email VARCHAR(255) NOT NULL  ,
Authorized INT NOT NULL DEFAULT 1  ,
PRIMARY KEY (id)
) ENGINE=INNODB";

echo "<font color=red>". $query ."</font><br>";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create Issuers table: " . $ex->getMessage());
        }

$query = "INSERT INTO " . $dbname . ".Issuers (FullName,Email,Authorized) VALUES ('".$dbuser."','".$dbuser."',1)";

       try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create Issuers table: " . $ex->getMessage());
        }






     echo "Creating Customer Details Table ";
 
$query = "CREATE TABLE ".$dbname.".CustomerTemplate ( 
id INT NOT NULL AUTO_INCREMENT,
Cust_name VARCHAR(200) NOT NULL  ,
Cust_ID INT NOT NULL UNIQUE ,
Cust_contact_name VARCHAR(200)   ,
Cust_contact_email VARCHAR(200)   ,
CC_email VARCHAR(200)   ,
Lic_alloc INT NOT NULL DEFAULT 0 ,
Lic_used INT ,
Product VARCHAR(50) NOT NULL  ,
Version INT  ,
Storage INT ,
Function VARCHAR(10) NOT NULL  ,
Modules_AD VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_ME VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_RT VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_CSAPI VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_BEIK VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_SB VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_CM VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_CT VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_VR VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_SMS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_MMS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_Push VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_XMPP VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_M4Gen VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_MC VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_Scope VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_AT VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_KAV VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_PAV VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_TAV VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_IA VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_Eleven VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_VAS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_FS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Type VARCHAR(4) NOT NULL DEFAULT \"TEMP\" ,
Category VARCHAR(12)   ,
Term_Exp INT   ,
Paid INT DEFAULT 1  ,
Perm_Auth VARCHAR(25)   ,
SE_Name VARCHAR(200)   ,
AE_Name VARCHAR(200)   ,
Notes VARCHAR(1024)   ,
PRIMARY KEY (id)
) ENGINE=INNODB";

echo "<font color=red>". $query ."</font><br>";


        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create Customer Detail table: " . $ex->getMessage());
        }


    echo " Creating History Table ";

$query = "CREATE TABLE ".$dbname.".History ( 
id INT NOT NULL AUTO_INCREMENT,
MAC VARCHAR(20) NOT NULL UNIQUE,
Expiration INT  ,
Modules_AD VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_ME VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_RT VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_CSAPI VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_BEIK VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_SB VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_CM VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_CT VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_VR VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_SMS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_MMS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_Push VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_M4Gen VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_MC VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_Scope VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_AT VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_KAV VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_PAV VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_TAV VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_IA VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_Eleven VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_VAS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Modules_FS VARCHAR(2) NOT NULL DEFAULT \"N\" ,
Last_Edit INT ,
Last_Editor VARCHAR(50) ,
Issue_Date INT NOT NULL ,
Issuer VARCHAR(50) NOT NULL ,
Notes VARCHAR(255)  ,
Version INT ,
Product varchar(50),
Cust_ID int ,
Storage int ,
Modules_XMPP varchar(2),
Cust_name varchar(200) ,
PRIMARY KEY (id)
) ENGINE=INNODB";


echo "<font color=red>". $query ."</font><br>";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to History table: " . $ex->getMessage());
        }

 
     echo "Creating User ";
     $query = "CREATE USER :US@'localhost' IDENTIFIED BY :PA";
     $query_params = array(':US' => $dbuser, ':PA' => $dbpass);
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create User: " . $ex->getMessage());
        }

     echo "Creating Grant ";
     $query = "GRANT ALL PRIVILEGES ON *.* TO '".$dbuser."'@'localhost'";
     $query_params = array(':DB => $dbname, :US' => $dbuser, ':HO' => $dbhost);
echo "<font color=red>". $query ."</font><br>";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create Grant: " . $ex->getMessage());
        }

     $query = "FLUSH PRIVILEGES";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create Grant: " . $ex->getMessage());
        }


     echo "Initialization complete.  If there are no errors above, click <a href=\"".$LNHome."\">HERE</a> to continue: ";




?>

