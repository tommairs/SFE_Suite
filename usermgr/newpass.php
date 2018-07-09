<?php
require ("common.php");


/*
$AccessToken = $_SESSION['AccessToken'];
//echo $htmlheader;

if (strlen($AccessToken) < 10){
       header('Location: index.php');
  exit;
}
*/

include('myfunctions.php');

$validuser = "true";

$cUser = $_GET['user'];
if (!$cUser){
  $cUser = $_POST['user'];
}
$cSkip = $_GET['s'];
if (!$cSkip){
  $cSkip = $_POST['skip'];
}
$cValidation = $_GET['v'];
if (!$cValidation){
  $cValidation = $_POST['validation'];
}
$cEmail = base64_decode($_GET['b']);
if (!$cEmail){
  $cEmail = $_POST['email'];
}

$OldPass = $_POST['O_Passkey'];
$NewPass = $_POST['N_Passkey'];
$Status = trim($_POST['Status']);


/*
echo "Collecting data... <br>";
echo "cUser = $cUser<br>";
echo "cSkip = $cSkip<br>";
echo "cValidation = $cValidation<br>";
echo "cEmail = $cEmail<br>";
echo "Status = $Status <br>";
*/

  if (!$Status){
    echo "Checking on current user <br>";

    if ($cValidation){
      // Verify the user credibility
      $query = "SELECT * FROM Users WHERE Email = :Em ";
      $query_params = array(
              ':Em' => $cEmail
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (Select All): " . $ex->getMessage());
        }
        if (sizeof($row) <1){
          echo " No matching email address found <br>";

        }

      while ($row = $stmt->fetch()){
//        echo "Looping through matching users <br>";

        if ($cValidation == $row[Passkey]){ 
          echo "Validation Matches <br>";
          if ($cUser == $row[id]) {
            echo "User Matches <br>";
            if ($cEmail == $row[Email]){
              echo "Email matches <br>";
              $validuser = "true";
              echo "User Validated <br>";
            }
          }
        }
        else {
          $validuser = "false";
          echo "User s NOT Valid <br>";
          echo "User action failed. Exiting. <br>";
          exit;

        }
      }
    }
    else{
      // if $cSkip != "true"
      // Verify the user credibility
      $query = "SELECT * FROM Users WHERE id = :Id ";
      $query_params = array(
              ':Id' => $cUser
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (Select All): " . $ex->getMessage());
        }
      while ($row = $stmt->fetch()){
        if ((password_verify($OldPass,$row[Passkey])) AND ($cUser == $row[id])){
          $validuser = "true";
          echo "User action confirmed <br>";
        }
        else {
          echo "User action failed. Exiting. <br>";
          $validuser = "false";
          exit;
        }
      }

    }

    echo '<html><body>
      <p><b>PASSWORD RESET</b><br>
      <form method=POST>
        <table>
    ';
    if (!$cSkip){ 
      echo '
            <tr><td>CURRENT Password: </td><td><input type="password" size=50 name="O_Passkey" value="">
      ';
    }
    else{
      echo '
          <tr><td colspan=2>
            For '. $cEmail .' <br>
            <input type="hidden" name="skip" value="true">
            <input type="hidden" name="validation" value="'. $cValidation .'">
          </td><td>
      ';

    }
    echo '
          <tr><td>NEW Password: </td><td><input type="password" size=50 name="N_Passkey" value="">
          <input type="hidden" name="user" value="'. $cUser.'">
          <input type="hidden" name="Status" value="current"></td></tr>
          <tr><td colspan=2>(Must be at least 12 chars, 1 upper, 1 lower, 1 number. 50 max chars)</td></tr>
          <tr><td>&nbsp;</td><td><input type="submit" name="submit" value="UPDATE"></td></tr>
    ';

    if (!$cSkip){ 
      echo '
          <tr><td>&nbsp;</td><td><a href="passreminder.php" target="_blank">Forgot my password</a> :( </td></tr>
      ';
    }
    echo '
         </table>
       </form>
      
     </body></html>
     ';
  }


  if (($validuser == "true") AND ($Status == "current")){
       echo "Updatng user account credentials <br>";

        $H_Pass = password_hash($NewPass, PASSWORD_DEFAULT);
        $query = "UPDATE Users SET Passkey=:Pk WHERE ID=:ID";
        $query_params = array(
              ':ID' => $cUser,
              ':Pk' => $H_Pass,
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (Update): " . $ex->getMessage());
        }

      echo "<br>Password updated<br>";
      echo '
        <p><br>
        <input type=button name=close value="CLOSE THIS WINDOW" OnCLick="javascript:window.close();">
        </p>
      ';
      $Status = "fin";
      $cUser == "";
    }
//  }




?>
