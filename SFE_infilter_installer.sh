#!/usr/bin/env bash
# Installer for "infilter" tool SMT 2018-06-28

# This installer may need to be run as root, because of the way the overall project folder is installed as root

# first, get PHP mailparse extension
# requires pear - see
#   https://jason.pureconcepts.net/2012/10/install-pear-pecl-mac-os-x/
#   https://ma.ttias.be/installing-the-pecl-pear-mailparse-module-for-php-on-centos/

sudo curl -O http://pear.php.net/go-pear.phar
php -d detect_unicode=0 go-pear.phar
/home/centos/pear/bin/pear version
sudo yum install re2c
sudo /home/centos/pear/bin/pear install pecl/mailparse			# almost works .. missing util re2c

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

echo "*** Now set up your specific config items in infilter/suite.ini, such as SparkPost API key etc"