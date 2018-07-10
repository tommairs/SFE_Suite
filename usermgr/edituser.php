<?php
require ("common.php");
$AccessToken = $_SESSION['AccessToken'];
echo '
<html>

 <head>
  <title>
    User Access Manager
  </title>
  <link rel="stylesheet" type="text/css" href="/config/style.css">
 </head>
 <body>
  <h1>
    Simple Front End Suite
  </h1>
 &nbsp; <a href="/">Go Back</a>
<p>
';

$CurrentUser = $_POST['CN'];

$Status = $_POST['Status'];
$FullName = $_POST['FullName'];
$Email = $_POST['Email'];
$iKey = $_POST['iKey'];
$Role = $_POST['Role'];
$Passkey = $_POST['Passkey'];
$enabled = $_POST['enabled'];

if ($enabled == ""){
  $enabled = 1;
}

$aAdmin = $_POST['aAdmin'];
$aLTerm = $_POST['aLTerm'];
$aLPerm = $_POST['aLPerm'];
$aLWild = $_POST['aLWild'];
$aUcrud = $_POST['aUcrud'];
$aReport = $_POST['aReport'];
$aCust = $_POST['aCust'];

// First build tempiKey from discrete values
if (!$aAdmin){$aAdmin = "0";}
if (!$aLTerm){$aLTerm = "0";}
if (!$aLPerm){$aLPerm = "0";}
if (!$aLWild){$aLWild = "0";}
if (!$aUcrud){$aUcrud = "0";}
if (!$aReport){$aReport = "0";}
if (!$aCust){$aCust = "0";}

$tempiKey = $aAdmin.$aLTerm.$aLPerm.$aLWild.$aUcrud.$aReport.$aCust;

//compare and select one
if ($tempiKey != $iKey){
  $iKey = $tempiKey;
}
if (!$iKey){
  $iKey = $tempiKey;
}


// Get user roles

   $query = "SELECT RoleName FROM UserRoles";
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
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run query (get table): " . $ex->getMessage());
        }

  while ($row = $stmt->fetch()){
    $roles[] = $row['RoleName'];
  }



 echo '
  <hr><p>

<h2>User Editor:</h2> 
   <form method="post" name="lr1">
  User Name
    <select name="CN" id="CN">
      <option value="NEW">Add New User</option>

  ';

   $query = "SELECT FullName,id FROM Users"; 
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
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  		
            die("Failed to run query (get table): " . $ex->getMessage()); 
        } 
        
  while ($row = $stmt->fetch()){
    echo '<option value="'. $row[id] . '"';
    if ($row[id] == $CurrentUser){
      echo ' selected';
    }
    echo '> '. $row[FullName] . ' </option> ';
  } 

  echo '
    </select>
      <input type="submit" name="Edit" id="Edit" value="Edit" />
    </form>';



  // Validate and record a new user account //

  if ($Status == "new"){
    // Check password validation
    // - at least 12 chars, 1 upper, 1 lower, 1 number. 50 max char
    $passok = "YES";
    preg_match('/([0-9]+)/', $Passkey, $nums);
    preg_match('/([A-Z]+)/', $Passkey, $uppers);
    preg_match('/([a-z]+)/', $Passkey, $lowers);
    $passlen = strlen($Passkey);

    if (count($nums) < 1){
      echo "<font color=red> * Must have at least one NUMBER</font><br>";
      $passok = "NO";
    }
    if (count($uppers) < 1){
      echo "<font color=red> * Must have at least one UPPER CASE character</font><br>";
      $passok = "NO";
    }
    if (count($lowers) < 1){
      echo "<font color=red> * Must have at least one LOWER CASE character</font><br>";
      $passok = "NO";
    }
    if ($passlen < 12){
      echo "<font color=red> * Must have at lease 10 charaters</font><br>";
      $passok = "NO";
    }
    if ($passlen > 50){
      echo "<font color=red> * Must have no more than 50 characters</font><br>";
      $passok = "NO";
    }

    if ($passok == "NO"){
      echo "Record not saved. Try again.<br>";
    }

    if ($passok == "YES"){

      $H_Pass = password_hash($Passkey, PASSWORD_DEFAULT);

      $query = "INSERT INTO Users (FullName,Email,Passkey,Role,iKey) VALUES (:FN,:Em,:Pk,:Ro,:Ik)";
      $query_params = array(
              ':FN' => $FullName,
              ':Em' => $Email,
              ':En' => $enabled,
              ':Pk' => $H_Pass,
              ':Ik' => $iKey,
              ':Ro' => $Role
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
            die("Failed to run query (Insert): " . $ex->getMessage());
        }
      $Status = "";
      $CurrentUser = $row[id];

      echo "<PRE>";

    }
  }

  if ($Status == "current"){
      $query = "UPDATE Users SET FullName=:FN,Email=:Em,Role=:Ro,iKey=:Ik WHERE ID=:ID";
      $query_params = array(
              ':ID' => $CurrentUser,
              ':FN' => $FullName,
              ':Em' => $Email,
              ':Ik' => $iKey,
              ':Ro' => $Role
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

    $Status = "fin";
    $CurrentUser == ""; 
  } 



  if($CurrentUser != ""){
    echo '<form name="lr2" method="post"><table>';

    if ($CurrentUser == "NEW"){
       echo '<tr>
               <td>
                 Record ID: </td><td><input type="text" size=50 name="CN_p" value="NEW" disabled>
                 <input type="hidden" size=50 name="CN_p" value="NEW">
               </td>
             </tr>';
       echo '<tr><td>Full Name: </td><td><input type="text" size=50 name="FullName" value=""></td></tr>';
       echo '<tr><td>Email: </td><td><input type="text" size=50 name="Email" value=""></td></tr>';
       echo '<tr><td>Passkey: </td><td><input type="password" size=50 name="Passkey" value="***************">
               <font color=red>(Must be at least 12 chars, 1 upper, 1 lower, 1 number. 50 max chars)</font></td></tr>';
       echo '<tr><td>Role: </td><td>
             <select name="Role" id="Role">
            ';

       foreach($roles as $r){
            echo'   <option value="'. $r .'">'. $r .'</option>';
       }
       echo '
             </select>
       </td></tr>';



       echo '<tr><td>
            &nbsp;
            <input type="hidden" name="Status" value="new">
            <input type="hidden" name="CN" value="id">
          </td><td>
          </td></tr>';
       echo '<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="UPDATE"</td></tr>';
   
    }
    else{

/***************************************************************/
/* If the user exists, load the data here for editing          */
/***************************************************************/

      $query = "SELECT * FROM Users WHERE id = :CN "; 
      $query_params = array(
              ':CN' => $CurrentUser
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
        echo '<tr><td>Record ID: </td><td><input type="text" size=20 name="CN_p" value="'. $row[id] .'"></td></tr>';
        echo '<tr><td>Full Name: </td><td><input type="text" size=50 name="FullName" value="'. $row[FullName] . '"></td></tr>';
        echo '<tr><td>Email: </td><td><input type="text" size=50 name="Email" value="'. $row[Email] . '"></td></tr>';
        echo '<tr><td>Passkey: </td><td>

             <!-- remove this before production-->
             <!-- <input type="password" size=50 name="Passkey" value="***************">-->
             
            <input type=button value="Reset Password" onclick="javascript:window.open(\'newpass.php?user='. $row[id] .'\',\'_blank\',\'scrollbars=0,width=550,height=275\');"> 
 
 
   <input type=button value="Email Password Reset" onclick="javascript:window.open(\'passreminder.php?E_Reminder='. $row[Email] .'\',\'_blank\',\'scrollbars=0,width=550,height=275\');">

             </td></tr>';
       echo '<tr><td>Role: </td><td>
             <select name="Role" id="Role">
            ';

       foreach($roles as $r){
            echo'   <option value="'. $r .'"';
            if ($row['Role'] == $r){
              echo ' selected';
            }
            echo '>'. $r .'</option>';
       }
       echo '
             </select>
       </td></tr>';


        echo '<tr><td>
            &nbsp;
            <input type="hidden" name="Status" value="current">
            <input type="hidden" name="CN" value="'. $row[id] . '">
          </td><td>
          </td></tr>';
        echo '<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="UPDATE"></td></tr>';
      }
    } 
        echo '</table></form></p>';
        }

  echo '
    <p><hr><br>
    <input type=button name=close value="BACK" OnCLick="javascript:history.back();">
    <input type=button name=close value="CLOSE THIS WINDOW" OnCLick="javascript:Close();">
    </p>
  ';

echo '

</p>
 </body>
</html>

';

?>
