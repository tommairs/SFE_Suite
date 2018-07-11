# "infilter" tool installation


Please copy/paste this file one line at a time, to check output results, rather than trying to script it all.

Depending on the way your Linux server is set up, some parts of this installer may need to be run as root.


get PHP mailparse extension
 requires pear - see
   https://jason.pureconcepts.net/2012/10/install-pear-pecl-mac-os-x/
   https://ma.ttias.be/installing-the-pecl-pear-mailparse-module-for-php-on-centos/

```
curl -O http://pear.php.net/go-pear.phar
php -d detect_unicode=0 go-pear.phar
/home/centos/pear/bin/pear version
yum install re2c
/home/centos/pear/bin/pear install pecl/mailparse
```

# note workaround to get mailparse to load late - (by naming the ini file z- ..)
# see https://stackoverflow.com/questions/21127052/php-mailparse-so-error-undefined-symbol-mbfl-convert-filter-flush

echo "; Added SMT 2018--06-27 to get PHP mailparse extension\
extension=mailparse.so" > /etc/z-mailparse.ini

# should show installed OK .. if not, consult the above links
php -info | grep mailparse

# Following is copied from https://getcomposer.org/download/
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# This pulls in all the library dependencies declared in composer.json.  If only everything was done this way ;-)
php composer.phar install

# The following work dirs need to be owned by apache, so that PHP scripts run by httpd can safely write to them
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

# do following as root
yum-config-manager --enable epel
yum install -y clamav-server clamav-data clamav-update clamav-filesystem clamav clamav-scanner-systemd clamav-devel clamav-lib clamav-server-systemd

vim /etc/freshclam.conf
# ensure that #Example line is commented out.  Optionally we can log the update messages
## UpdateLogFile /var/log/freshclam.log

freshclam

vim /etc/clamd.d/scan.conf
vim /etc/cron.d/clamav-update
vim /etc/sysconfig/freshclam

# start the scanner daemon, if necessary specifying the config file
clamd --config-file /etc/clamd.d/scan.conf

# create clamd logfile appropriate owner / permissions (clam runs as special non-root user clamscan)
cd /var/log
echo .>/var/log/clamd.scan
chown clamscan:clamscan clamd.scan

# check the daemon can run in interactive mode, without giving errors
/usr/sbin/clamd -c /etc/clamd.d/scan.conf –nofork=yes
systemctl  start clamd@scan
systemctl  status clamd@scan

# here are the clamav logfiles to monitor
sudo tail -f /var/log/clamd.scan /var/log/freshclam.log

# test that crontab job is working.  Should cause logfile output
/usr/share/clamav/freshclam-sleep xnow

crontab cronfile
# check that this is properly set up with crontab -e

echo "*** Now set up your specific config items in infilter/suite.ini, such as SparkPost API key etc"

# app logs will appear in dir set in suite.ini

# using swaks to send emails to the scanner with attachments - example
#  swaks --from steve.tuck@sparkpost.com --to test@relay.thetucks.com --attach ~/quarantine/eicar.txt

## Inbound domain(s) configuration

Your inbound domain(s) should be set up according to [this article](https://www.sparkpost.com/docs/tech-resources/inbound-email-relay-webhook/).

The suite provides an easy web UI tool to set up both inbound_domains and relay_webhooks (see suite Admin page).

The tool displays whether the MX records check out properly and whether your endpoints are receptive to http(s) POST attempts.
