# Mosaico PHP Backend

This is a (now working) PHP backend for Mosaico

This has been forked from https://github.com/ainterpreting/mosaico-php-backend which seems to be completely dead and no-longer maintained.  I am **not** a php programmer but needed to get this working.  If anyone wants to take this over or offer any cleanup/help with the code I would more than welcome it.

It has been tested with mosaico installed in /var/www/mosaico and with document root as /var/www/mosaico.  If your apache setup is different you will probably have to change the paths in config.php and maybe in index.php as well.

Mosaico can be found at https://github.com/voidlabs/mosaico

First, install and set up Mosaico.  Then install these files on top of the Mosaico installation.

This install includes the directories upload, dl, and img (which should be copied into your main mosaico directory) and add an .htaccess file in each of those directories with the following code:
```
RewriteEngine On
RewriteRule ^(.*)$ /backend-php/index.php [QSA,L]
```
I hope to remove these empty directories soon as having to have these be actual directories is pretty stupid.

## Dependencies

It is expected that you are running Apache with mod_rewrite support enabled.

You also do need to have Imagemagick support enabled in your PHP configuration.

## New folders and files
```
backend-php/config.php
```
In this file are a few variables that you can adjust if necessary.  Please check this file and make sure all the paths are correct for your Mosaico installation, and that PHP can write files to those paths.  If they are wrong or PHP cannot write files to those paths, your image uploads *will not* work.

```
/backend-php/index.php
```
This is the PHP backend engine that handles the required functions:
* image uploads
* retrieving of a list of uploaded images
* downloading of the HTML email
* sending of the test email
* generating the placeholder images
* the resizing of images

The PHP backend also generates static resized images when downloading the HTML email or sending the test email.

## Modified files

Editor.html no longer needs to be modified from the original mosaico one. 
