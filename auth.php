i<?php
// Authenticates login and stores session values
include ('common.php');

// get $email and $password for login
$AccessToken = $_SESSION['AccessToken'];
if ($AccessToken){
  echo $htmlheader;
  echo "<center>You are already logged in.  Click <a href=\"logout.php?action=clear\">HERE</a> to logout.</center>";
  echo "</body></html>";
  exit;
}
else {

  $email = $_POST['email_p'];
  $pass_p = $_POST['passwd_p'];

    $_SESSION['email'] = $email;

  if (($email)){ 
    // Verify the user against the auth table before continuing.
    $query = "SELECT Email,FullName,Authorized,iKey,Passkey, Role FROM Users WHERE Email = '".$email."'";
    $query_params = array(
              ':CN' => '1'
        );

        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)
        {
           //  CreateDB();
        }

      $row = $stmt->fetch();

      if ($row['Email'] != ""){

        // validate Passkey.  NOTE: need to salt this later for more security
    
     //   $H_Pass = password_hash($row['Passkey'], PASSWORD_DEFAULT);
        if (password_verify($pass_p, $row['Passkey'])){
     //   if ($pass_p ==$row['Passkey']){

          $_SESSION['Email'] = $email;
          $_SESSION['FullName'] = $row['FullName'];
          $_SESSION['Authorized'] = $row['Authorized'];
          $_SESSION['Role'] = $row['Role'];
          $_SESSION['showall'] = "false";
          $_SESSION['AccessToken'] = base64_encode($row['FullName']."|".$row['iKey']."|".$today);
          $_SESSION['iKey'] = $row['iKey'];   // 1111111 = ALL, 0000000 = None
          $_SESSION['Lattempts']=0;
          $_SESSION['AuthKey'] = $authkey;
          $_SESSION['MailHost'] = $mailhost;
          $_SESSION['AlertSender'] = $alertsender;
        }
        else {
          echo $htmlheader;
          $_SESSION['Lattempts'] = $_SESSION['Lattempts'] + 1;
          echo "<center>Invalid credentials. Attempt was logged (". $_SESSION['Lattempts'] .") Click <a href=\"index.php\">HERE</a> to try again.</center>";
          echo "</body></html>";
          exit;
        }
      }
      else{
          echo $htmlheader;
          $_SESSION['Lattempts'] = $_SESSION['Lattempts'] + 1;
          echo "<center>Email is not in the system. Attempt was logged (". $_SESSION['Lattempts'] .") Click <a href=\"index.php\">HERE</a> to try again.</center>";
          echo "</body></html>";
          exit;

      }
  }

 else {
    echo $htmlheader;
    echo "<center>You need to provide credentials to login.  Click <a href=\"index.php\">HERE</a> to try again.</center>";
    echo "</body></html>";
    exit;
  }

       header('Location: index.php');
}

?>
