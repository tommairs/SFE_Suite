<?php
//--------------------------------------------
// Workflow Manager
// Allows previews to be selected for review and approval
// Approved templates can be sent to SparkPost for use
//--------------------------------------------

include('../m_func.php');

?>

<html>

 <head>
  <title>
   Template Library Manager
  </title>
  <link rel="stylesheet" type="text/css" href="/config/style.css">
 </head>
 <body>
  <h1>
  Template Library Manager
  </h1>
 &nbsp; <a href="/">Go Back</a>

<p>
<h2>Select a function:</h2> </br>
<table class="table_menu">
    <tr class="stripy">
        <th class="value">Template Name/Link</th>
        <th class="name">Thumbnail</th>
        <th class="name">Status</th>
        <th class="name">SPID</th>
    </tr>
    <tr class="stripy">
        <td class="value"><a href=https://suite.trymsys.net/previews/153083071914283.html target=_blank>Template: 153083071914283</a></td>
        <td class="name"><a href=/previews/153083071914283.png target=_blank><img src=/previews/153083071914283.png width=50 height=50></a></td>
        <td class="Status_InProgress">In Progress</td>
        <td class="name">Not Assigned</td>
    </tr>
</table>

</p>
 </body>
</html>


