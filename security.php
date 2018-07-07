<?php

  include('common.php');
  if (strlen($AccessToken) < 10){

  echo '
  <form action="auth.php" method="post" name="lr2">
    <center>
    <table>
      <tr>
        <td colspan=2>
          <p>If you are authorized to use this service,
            <br>enter your credentials here.<br>
          </p>
        </td>
      </tr>
      <tr>
        <td> 
          Email </td>
        <td> 
          <input type=text size=50 name=email_p>
        </td>
      </tr>
      <tr>
        <td>
          Password 
        </td>
        <td> 
          <input type=password size=50 name=passwd_p>
        </td>
      </tr>
      <tr>
        <td>
          &nbsp;
        </td>
        <td> 
          <input type=submit value=CONTINUE name=login>
          ... or ...     <input type="button" name="forgot" value="I Forgot My Password" onclick="javascript:window.open(\'passreminder.php\',\'_blank\',\'scrollbars=1,width=900,height=600\');">

<!--
<a href="forgotpass.php"> I forgot my password</a>
-->

        </td>
      </tr>
      <tr>
        <td colspan=2>

            <br>
            <br>
            <br>

<!--
<a href="getaccess.php">HERE</a>
-->

        </td>
      </tr>

    </table>
    </center>
  </form>
  ';

    exit;
  }

?>

