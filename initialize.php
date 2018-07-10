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


TemplateName, Status, SPID, Owner FROM Templates

    echo " Creating Templates Table ";

$query = "CREATE TABLE ".$dbname.".Templates (
id INT NOT NULL AUTO_INCREMENT,
TemplateName VARCHAR(50) NOT NULL,
Last_Edit INT ,
Owner VARCHAR(50) ,
Status VARCHAR(50),
HTML MEDIUMTEXT,
SPID VARCHAR(50) ,
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




    echo " Creating TemplateHistory Table ";

$query = "CREATE TABLE ".$dbname.".TemplateHistory ( 
id INT NOT NULL AUTO_INCREMENT,
tid INT NOT NULL,
Last_Edit INT ,
Last_Editor VARCHAR(50) ,
Last_Action Varchar(50),
Last_HTML MEDIUMTEXT,
Version INT ,
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



    echo " Creating Users Table <br />";

$query = "CREATE TABLE ".$dbname.".Users (
id INT NOT NULL AUTO_INCREMENT,
Email VARCHAR(100) NOT NULL,
FullName VARCHAR(100) NOT NULL,
iKey VARCHAR(10) NOT NULL,
PassKey VARCHAR(100) NOT NULL,
Role VARCHAR(50) NOT NULL,
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

