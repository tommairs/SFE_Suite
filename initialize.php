<?php


if ($_SERVER['REFERRER'] != ""){
  echo "This must ve run from CLI";
  exit;
}

   include ("env.ini");
   $firstpass = $_POST['first'];
   $a_user =  $_POST['user'];
   $a_pass =  $_POST['pass'];
   
   if ($_SERVER['SERVER_PORT'] != "443"){
     echo "Please use HTTPS";
     exit;
   }

   if ($firstpass != "false"){
   echo "
    <html><body>
    <p>
      This is the initialization script.  Running this will wipe and replace the database configured in <i>env.ini</i>.<br />
      Please set the desired database name, username, and password in the <i>env.ini</i> file BEFORE running this script.<br />
      \$LNHome = \"https://10.79.0.15/licenseninja-dev/\";  // This is the installed location of the web service  <br />
      \$TZ = 'America/Los_Angeles'; //This is the Linux standard region format<br />
      \$dbhost = 'localhost'; // should be left as \"localhost\"<br />
      \$dbuser = 'licenseman-dev';  // The desired DB username<br />
      \$dbpass = 'mydevpassword';  // The desired DB Password<br />
      \$dbname = 'LicenseNinja-dev';  // The desired DB Name<br />
      \$p_email = \"LicenseNinjaDEV@messagesystems.com\";  // The email address used for panic mode license generation<br />
      \$p_passwd = \"MyPrivatePassword\";  // The password for panic mode license generation<br />
      <br />
      If the above is set, continue below:<br />
      <br />
      <form name=\"dbinit\" method=\"POST\" action=\"#\">
      <table>
       <tr>
        <td>Root or authorized username for MySQL administration<br /> (must have GRANT priv):</td>
        <td><input type=\"text\" name=\"user\"></td>
       </tr>
       <tr>
        <td>Root or authorized user password for MySQL administration<br /> (will not be saved):</td>
        <td><input type=\"password\" name=\"pass\"><input type=\"hidden\" name=\"first\" value=\"false\"></td>
       </tr>
       <tr>
        <td><input type=\"submit\" value=\"Create DB\"></td>
        <td>&nbsp;</td>
       </tr>
     </table>
     </form>

   ";


   }
   else{
 
   echo "
    <html><body>
    <p>
     Creating Database ".$dbname." as user ".$a_user." <br />";

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

echo "<font color=red>". $query ."</font><br>\r\n";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>\r\n";

        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create DB: " . $ex->getMessage());
        }


     echo "Creating ".$dbname.".Issuers Table <br />";
$query = "CREATE TABLE ".$dbname.".Issuers ( 
id INT NOT NULL AUTO_INCREMENT,
FullName VARCHAR(50) NOT NULL ,
Email VARCHAR(255) NOT NULL  ,
Authorized INT NOT NULL DEFAULT 1  ,
PRIMARY KEY (id)
) ENGINE=INNODB";

echo "<font color=red>". $query ."</font><br>\r\n";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>\r\n";
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






     echo "Creating Customer Details Table <br />";
 
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

echo "<font color=red>". $query ."</font><br>\r\n";


        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>\r\n";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create Customer Detail table: " . $ex->getMessage());
        }


    echo " Creating History Table <br />";

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


echo "<font color=red>". $query ."</font><br>\r\n";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>\r\n";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to History table: " . $ex->getMessage());
        }

 
     echo "Creating User <br />";
     $query = "CREATE USER :US@'localhost' IDENTIFIED BY :PA";
     $query_params = array(':US' => $dbuser, ':PA' => $dbpass);
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>\r\n";
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
           die("Failed to create User: " . $ex->getMessage());
        }

     echo "Creating Grant <br />";
     $query = "GRANT ALL PRIVILEGES ON *.* TO '".$dbuser."'@'localhost'";
     $query_params = array(':DB => $dbname, :US' => $dbuser, ':HO' => $dbhost);
echo "<font color=red>". $query ."</font><br>\r\n";

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
          if ($result == 1){$result_en = "success";}
    echo "<font color=green>". $result_en ."</font><br>\r\n";
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


     echo "Initialization complete.  If there are no errors above, click <a href=\"".$LNHome."\">HERE</a> to continue: <br />";

}



?>

