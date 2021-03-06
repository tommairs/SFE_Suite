# SFE_Suite
Simple Front End Suite
THIS IS A WORK IN PROGRESS AND IS NOT READY FOR PRODUCTION USE YET... Come back later.


## Goals
 - provide a simple responsive WYSIWYG template content editor (not a campaign engine)
 - provide responsive preview capability (connector to external tool)
 - provide inbound AV/AS scanning of reply mail
 - provide archive store pipe (BCC route EML files to external store)
 - provide View In Browser capability
 - provide basic workflow management with version control
 - make it all available to master or subaccount levels
 - integrate to SparkPost APIs for full automation
 

## Disclaimer
This is created as an **OPEN SOURCE** community project.  There is **NO support** and **NO guarantee for performance**.  If you like what you see, you are welcome to fork to your own project, observing the Apache license and providing credit where necessary. PRs are welcome as are any comments or suggestions. 

## Credits and other redirects
**Mosaico** - This project is based on a **HEAVILY** modified verson of Mosaico.  You can find the original source [here](https://github.com/voidlabs/mosaico). There were far to many mods to simply pull and update the original so a modified version of release 0.17.3 is included in this repo. Many thanks to [@bago](https://github.com/bago) for his work on this project.  His original GNU License is included in the repo.

**markalston** - This project also includes a modified version of [markalston]'s (https://github.com/markalston) [Mosaico-PHP-Backend] (https://github.com/markalston/mosaico-php-backend). His original GNU License is included in the repo.


## Cost Factor Warning
Some of these processes use BCC or redirects that can incur additional messaging costs with your email provider. PLEASE be aware of the per transaction costs of your provider.

## Installation
The intention was to build this entirely in AWS so all the instructions are geared that way. If you build elsewhere, make the appropriate corrections.

1. deploy a suitable server location.  I used an m3.medium instance with 200Gb storage and CentOS 7.4 OS (ami-51076231) for this. 
2. build a LAMP-GNP stack out on it.  You may not need all the tools, but it is good to have them if you are developing additional features. I happen to have a handy script right here if you need one: https://github.com/tommairs/LAMP-GNP-Stack-Builder/blob/master/rh7-installer.sh
3. navigate to /var/www/html/ 
4. create the application folder and cd to it ( mkdir sfesuite;cd sfesuite )
5. init and pull this repo with git init;git pull https://github.com/tommairs/SFE_Suite
6. execute SFEinstaller.sh as root (sudo sh SFEinstaller.sh )
7. rename suite.ini.example to suite.ini and manually edit it with your own specific values.  This software will not work without it. 
8. navigate a browser to your host url and start [using it](https://github.com/tommairs/SFE_Suite/blob/master/USAGE.md)

## Usage
For usage instructions please refer to [USAGE.md](https://github.com/tommairs/SFE_Suite/blob/master/USAGE.md)

## Contributing
For information on adding contributions, quesitons, suggestions, support, pull requests, etc, please see [CONTRIBUTING.mg](https://github.com/tommairs/SFE_Suite/blob/master/CONTRIBUTING.md)


