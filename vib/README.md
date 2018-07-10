# VIB

##View In Browser functionality for Sparkpost

This project was built to satisfy the need of having a View In Browser capability usable with SparkPost.

The code takes in a BCC of a message and presents it for Web Browser viewing.
It uses a random number and the current timestamp to provide a UUID to identify a specific message.  

The code comes in two parts.
1. A component that creates a link with the UUID that can be added to the message content
2. A component to store a copy in a Web service for viewing after some time.
  
Arguably a 3rd component is the administration tool of the web service that allows you to control how long messages are stored.

**genkey.php** - an API that accepts an email address, and campaign_id, creates a hash and returns it for use as the ViewInBrowser link.

**econsumer.php** - This will take in a relay-webhook from Sparkpost and save the HTML and TEXT parts as files for later viewing. Be sure to create a valid [inbound domain](https://developer.sparkpost.com/api/inbound-domains.html) first

### Installation:
As with any git project, you can just clone this repo, or copy the files to your chosen location.  You will need a working web server - I did this using [Apache HTTPD](https://httpd.apache.org/) - and a [SparkPost account](https://app.sparkpost.com/sign-up?src=Dev-Website&sfdcid=701600000011daf&_ga=2.4894199.579704299.1516653596-1497769791.1510582304).

Once you have set up your web server, go to your SparkPost account and set up a couple of things:

First, you must ensure that you have registered an *[inbound domain](https://developer.sparkpost.com/api/inbound-domains.html)* in your SparkPost account

Next, you need to setup the Inbound [Relay-Webhook](https://developer.sparkpost.com/api/relay-webhooks.html) to point to your VIB project.  When you configure the relay_webhook, I highly recommend using an AuthToken so you can validate the inbound traffic.

### Usage:
When you are creating a message, first execute the ***genkey.php*** script.  You can do this remotely from any web browser and just copy and paste the provided link, or you can use it programmatically. eg: wget <yourserver>/genkey.php.  This will produce something like ***https://<your_server>/<your_app>/vib?l=1516749514104287***

Now use that link as the View In Browser link.  For example, at the top of the message you can add something like this:

Click <a href="https://<your_server>/<your_app>/vib?l=1516749514104287"> HERE </a> to view this message in your web browser.

Again, this can be done programmatically very easily, or you can do it manually.

Now when you send the message, you need to BCC your VIB app so that it can create viewable images.  You do this by sending a copy to the inbound domain you defined earlier.  The local part is irrelevant, so if your inbound domain is "mynewserver.com", then you can BCC the message to "vib@mynewserver.com" and let the code to all the rest.

Feel free to clone, copy, branch and leave any comments or suggestions in the repo.






