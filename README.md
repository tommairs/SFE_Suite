# SFE_Suite
Simple Front End Suite


## Goals
 - provide a simple responsive content editor (not a campaign engine)
 - provide responsive preview capability
 - provide inbound AV/AS scanning of reply mail
 - provide archive store and retreival
 - provide View In Browser capability
 - provide basic workflow management 
 - make it all available to master or subaccount levels
 - integrate to SparkPost APIs for full automation

## Disclainer
This is created as an **OPEN SOURCE** community project.  There is **NO support** and **NO guarantee for performance**.  If you like what you see, you are welcome to fork to your own project, observing the Apache license and providing credit where necessary. PRs are welcome as are any comments or suggestions. 

## Installation
The intention was to build this entirely in AWS so all the instructions are geared that way. If you build elsewhere, make the appropriate corrections.

**First** deploy a suitable server location.  I used an m3.medium instance with 200Gb storage and CentOS 6.5 OS (ami-b6bdde86) for this. It will likley work exactly the same on CentoOS 7.x but I have not tried it yet.

**Next**, build a LAMP-GNP stack out on it.  You may not need all the tools, but it is good to have them if you are developing additional features. I happen to have a handy script right here if you need one: https://github.com/tommairs/LAMP-GNP-Stack-Builder

**Finally** navigate to /var/www/html/ and init/pull this repo.

