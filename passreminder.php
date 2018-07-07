<?php
require ("common.php");
$AccessToken = $_SESSION['AccessToken'];
echo $htmlheader;
$sentmail = false;

include('myfunctions.php');

$E_Reminder = $_POST['E_Reminder'];
if (!$E_Reminder){
  $E_Reminder = $_GET['E_Reminder'];
}

if($E_Reminder == ""){

 echo '
  <p>
   <form method="post" name="lr1">
       Enter your email address here and if an account is associated with that address, <br>
       a password reset email will be sent to that address. <br><br>
       Email: <input type="text" size=50 name="E_Reminder" value="" autofocus>
       <Input type="submit" name="submit" value="SEND"> 
       
   </form>
  </p>
  ';
}
else{
  // Validate email format
  if(filter_var($E_Reminder, FILTER_VALIDATE_EMAIL)) {

  // Check email against DB

  // If email exists, send the "reset" message to it.
      $query = "SELECT * FROM Users WHERE Email = :Em ";
      $query_params = array(
              ':Em' => $E_Reminder
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
        $ValidationKey = $row[Passkey];
        $resetlink = "".  $LNHome . "newpass.php?user=". $row[id] ."&s=true&v=". $ValidationKey ."&b=". base64_encode($row['Email'])  ."";
        $body = '<p>FROM: Message Systems License Ninja</p><p>'. $row[FullName] .',</p><p>Someone using this email address requested a password reset at '. date("l jS \of F Y h:i:s A T", $today) .'.<br>If this was you, please click the link below or paste it onto a browser to complete the reset.</p><p>If this was NOT you, please ignore this email and no changes will be made.</p><p><a href="'. $resetlink .'">'. $resetlink .'</a></p>';
         $Subject = "Password reset notification from Message Systems License Ninja";




        SendMailTo($row[Email],$row['FullName'],$body,$Subject);        
        $sentmail = true;
      }
      if ($sentmail == true){
        echo "Reset email sent <br>";
      }
      else {
        echo "That E-mail is NOT registered in the system.<br>";
        echo "Click <a href=https://ninja.trymsys.net/Licenseninja2/getaccess.php>here</a> to request access.<br>";
      }
    }
    else {
      // If it does not exist, just close.
      echo "Thank you.";
    }

  echo '
    <p><br>
    <input type=button name=close value="CLOSE THIS WINDOW" OnCLick="javascript:window.close();">
    </p>
  ';

}

  echo '</body></html>';
