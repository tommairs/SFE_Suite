# /usr/bash 
###########################################################
# Bash Installer and configurtion tool for SFE Suite
###########################################################

# Set these vars first, then execute this script

YOURDOMAINURL="suite.trymsys.net"
YOUREMAIL="tmairs@aasland.com"


## Modify Apache to serve the suite

echo "
<VirtualHost *:80>
    ServerAdmin $YOUREMAIL
    DocumentRoot /var/www/html/sfesuite/
    ServerName $YOURDOMAINURL
    ErrorLog logs/$YOURDOMAINURL-error_log
    CustomLog logs/$YOURDOMAINURL-access_log common

Alias /mosaico/ /var/www/mosaico-master/dist/
Alias /dl/ /var/www/mosaico-master/backend-php/
Alias /img/ /var/www/mosaico-master/backend-php/
Alias /upload/ /var/www/mosaico-master/backend-php/
Alias /templates/ /var/www/mosaico-master/templates/

<Directory /var/www/mosaico-master>
    Options MultiViews FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>


</VirtualHost>
" > /etc/httpd/conf.d/sfesuite.conf

/bin/systemctl reload httpd.service

mv mosaico-master /var/www/

cd /var/www/mosaico-master/
yum -y install ImageMagick ImageMagick-devel
yum -y install gcc php70w-devel php70w-pear
pecl install imagick
echo "extension=imagick.so" > /etc/php.d/imagick.ini
service httpd reload

npm install
npm i -g npm
npm install -g grunt-cli
npm install mysql -save
npm audit fix
grunt

mv templates/ dist/ -f
ln -s dist/templates templates

## END ##

