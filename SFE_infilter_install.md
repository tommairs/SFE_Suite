# "infilter" tool installation

Copy/paste this file one line at a time, to check output results, rather than automating.

Depending on the way your Linux server is set up, some parts of this installer may need to be run as root.

Steps described below:
- PHP mailparse
- Composer lib dependencies
- ClamAV
- Application, incl cron task, work directories and app_log directory
- Inbound domain(s) configuration
- Testing


## PHP mailparse
Mailparse is a PHP extension which needs to be installed using `pear` rather than `composer`.
See https://jason.pureconcepts.net/2012/10/install-pear-pecl-mac-os-x/
and
https://ma.ttias.be/installing-the-pecl-pear-mailparse-module-for-php-on-centos/

```
curl -O http://pear.php.net/go-pear.phar
php -d detect_unicode=0 go-pear.phar
/home/centos/pear/bin/pear version
yum install re2c
/home/centos/pear/bin/pear install pecl/mailparse
```

Workaround to get mailparse to load late - (by naming the ini file z- ..)
see https://stackoverflow.com/questions/21127052/php-mailparse-so-error-undefined-symbol-mbfl-convert-filter-flush

```
echo "; Added SMT 2018--06-27 to get PHP mailparse extension\
extension=mailparse.so" > /etc/z-mailparse.ini
```

Test mailparse installed OK .. if not, consult the above links
```
php -info | grep mailparse
```

## Composer lib dependencies

Composer is generally easy to work with, as dependencies are declared in the `composer.json` file for you. According to
https://getcomposer.org/download/
it is good practice to install the composer tool itself afresh each time. Check above links for latest
practice, which at time of writing is:

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
This creates the composer tool as a `.phar` file.

You can then pull in all the library dependencies declared in `composer.json` as follows
```
php composer.phar install
```

## ClamAV
For more info, refer to
https://www.clamav.net/documents/installing-clamav
and
https://www.centosblog.com/how-to-install-clamav-and-configure-daily-scanning-on-centos/

Do the following as root:
```
yum-config-manager --enable epel
yum install -y clamav-server clamav-data clamav-update clamav-filesystem clamav clamav-scanner-systemd clamav-devel clamav-lib clamav-server-systemd
```

Then edit:
```
vim /etc/freshclam.conf
```
Ensure that `#Example` line is commented out.  Log the updates that `freshclam` makes by changing the following line in the .conf file
```
UpdateLogFile /var/log/clamav/freshclam.log
```

Check it updates OK manually:
```
freshclam
```

Next we configure and start the clamd "daemon". Edit the clamd config file:
```
vim /etc/clamd.conf
```
Check the logfile specified has a valid directory, and the logfile size is set to `2M`.
Clam runs as special non-root user specified in the `User` directive in this file.
Specify the local socket file to be used with the line
```
LocalSocket /tmp/clamd.socket

```

Start the scanner daemon interactively to check it works without giving errors.  Command prompt may take 10s+ to return.
```
/usr/sbin/clamd –nofork=yes
```

Check the logfiles are being written to.
```
tail /var/log/clamav/clamd.log 
```

Find and kill your clamd task, because it will otherwise prevent the next step from working.
```
ps aux | grep clam
kill _your task number here_
```

Set the daemon to start as a service.

CentOS 7:
```
systemctl  start clamd@scan
systemctl  status clamd@scan
```

### ClamAV automatic updating

The `freshclam` task needs to be run every x hours to keep the database up to date.

Create a new cron job
```
vim /etc/cron.d/clamav-update
```
Give it the following content:
```
## Adjust this line...
MAILTO=root

## It is ok to execute it as root; freshclam drops privileges and becomes
## user 'clamupdate' as soon as possible
0  */3 * * * root /usr/share/clamav/freshclam-sleep
```

Test that crontab job is working.  Should cause logfile output.
```
/usr/share/clamav/freshclam-sleep xnow
```

## Application

Set up your specific config items in file `suite.ini`, such as SparkPost API key etc.  This file also specifies the app logfile
locations, work directories etc.
These directories should be owned by user `apache`, so that PHP scripts run by `httpd` can write to them:

```
cd infilter
mkdir spool
chown apache:apache spool
chmod 755 spool

mkdir spool/done
chown apache:apache spool/done
chmod 755 spool/done

mkdir spool/eml
chown apache:apache spool/eml
chmod 755 spool/eml

mkdir app_logs
chown apache:apache app_logs
chmod 755 app_logs
```

Check that the batch process runs OK interactively as user `apache`:
```
sudo -H -u apache bash -c 'php /var/www/html/SFE_Suite/infilter/avscanner.php'
```

Each time this is run, you should see a line appended to the date-stamped application log file in directory specified by `suite.ini`.
```
2018-07-11 15:43:27.650116|avscanner.php|INFO|AV scanner version/database reports ClamAV 0.99.4/24743/Wed Jul 11 06:56:17 2018
```

Schedule this task to run every minute using
```
crontab -e cronfile
```

## Inbound domain(s) configuration
Your inbound domain(s) should be set up according to [this article](https://www.sparkpost.com/docs/tech-resources/inbound-email-relay-webhook/).

The suite provides an easy web UI tool to set up both inbound_domains and relay_webhooks.

The tool displays whether the MX records check out properly and whether your endpoints are receptive to http(s) POST attempts.


## Testing
Files can be sent to your inbound domain(s) with attachments from any mail client.

Monitor the app logfile, and if necessary the ClamAV logfile(s) and Apache httpd logfiles to troubleshoot.
Processed files are moved to the `done` directory rather than being deleted, to permit further investigation of mai lcontents.

Mail with attachments that trigger antivirus warnings is somewhat tricky to generate, as most mail clients will forbid the attachments
even before they are sent.  Your own OS AV scanner may find and quarantine the files even before you attach them.

Useful test files include:
- EICAR test file: http://www.eicar.org/86-0-Intended-use.html
- PHP logo test file: http://php.webtutor.pl/en/2011/05/13/php-code-injection-a-simple-virus-written-in-php-and-carried-in-a-jpeg-image/

Once you have these files, you can send them easily using [swaks](http://www.jetmore.org/john/code/swaks/) as follows
(assuming, for example, you have them in a directory called `quarantine` under your home dir):
```
swaks --from steve.tuck@sparkpost.com --to test@relay.thetucks.com --attach ~/quarantine/eicar.txt
```
