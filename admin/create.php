<html>
<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
<p>
<h1>Settings</h1>

<table>
    <tr>
        <td class="title"><h2>Create Inbound Relay Webhook</h2></td>
        <td class="title2"> </td>
    </tr>
</table>

<form action = "do_create.php" method="post">
    <fieldset>
        <legend>Inbound Relay Webhook Settings:</legend>
        <p class="entry_descr"> Name:</p>
        <input type="text" name="name" onfocus="this.value=''" value="e.g. My Best Ever Inbound Mail Service" size="80"><br>
        <p class = "entry_hint"> A friendly label for your webhook, only used for display</p>
        <br>
        <p class="entry_descr"> Target:</p>
        <input type="text" name="target" onfocus="this.value=''" value="e.g. https://example.com/relay-webhook-target" size="80"><br>
        <p class = "entry_hint"> This is the URL we'll send data to. We recommend the use of https</p>
        <br>
        <p class="entry_descr"> Authentication Token (optional):</p>
        <input type="text" name="auth_token" onfocus="this.value=''" value="abcdef" size="80">
        <p class = "entry_hint"> Authentication token will be present in the X-MessageSystems-Webhook-Token header of POST requests to target.<br>
        Your receiver can use this value to confirm the POST is genuine.</p>
        <br>
        <p class="entry_descr"> Match Domain:</p>
        <input type="text" name="match_domain" onfocus="this.value=''" value="bobsburgers.com" size="80"><br>
        <p class = "entry_hint"> Inbound domain associated with this webhook. You will need to set up DNS MX records for this so that any mail for the above domain will be routed to SparkPost.</p>
        <br>
        <input type="submit" value="Create webhook">
    </fieldset>
</form>
<br>
<a href="/">Go Back</a>
</p>
</body>
</html>
