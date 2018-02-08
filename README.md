Mattermost-IMAP Module
======================

This module provides an external IMAP authentication in Mattermost for the Team Edition (free).
Please check the "Thanks" part down below. There is a huge amount of work done by Denis Clavier.

## Overview

Currently, External authentication in Mattermost is not featured in the Team Edition (only in the Enterprise Edition). Thus, the only way to get external authentication in Mattermost is to install Gitlab and use its Single Sign On (SSO) feature. 

However, although Gitlab is a nice software, it is resources-consuming and a bit complicated to manage if you just want the SSO feature. That's the reason why, this module provides an oauth server to only reproduce the Gitlab SSO feature and allows a simple and secure IMAP authentication to Mattermost.

The Mattermost-LDAP project uses the Gitlab authentication feature from Mattermost and substitute Gitlab to IMAP interaction. The main advantage of this module is to provide a light and easy to use IMAP connector for Mattermost not to need Gitlab.

## Module Description

This module provides an Oauth2 server designed for php, a IMAP connector for PHP and some files for automatic configuration. Once installed and configured with Mattermost, the module allows IMAP authentication by replacing Gitlab SSO. This module allows many configuration settings to try to comply with your settings and configuration. Mattermost-IMAP can be used with MySQL or PostgreSQL database on many operating systems. See Limitation section for more information. 

## Setup 
### Requirements
This module requires the following : 

* PHP (minimum 5.3.9)
* php-imap
* php-pdo 
* php-pgsql or php-mysql
* httpd
* postgresql or mariadb (mysql)
* postgresql-server or mariadb-server
* git

Obviously, you must have a Mattermost Server installed and be administrator on it, and a IMAP server configured.

### Pre-install

Install required packages :

* For Centos 7, RHEL 7 and Fedora :
```
#For PostgreSQL
sudo yum -y --nogpgcheck install httpd php postgresql-server postgresql php-ldap php-pdo php-pgsql git 

#For MySQL
sudo yum -y --nogpgcheck install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git
```
* For Debian, ubuntu, Mint :
```
#For PostgreSQL
sudo apt-get -y install httpd php postgresql-server postgresql php-ldap php-pdo php-pgsql git 

#For MySQL
sudo apt-get -y install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git
```

Start and enable service for Apache and Database (for all distribution using systemd):
```
#For PostgreSQL
sudo systemctl start httpd
sudo systemctl start postgresql
sudo systemctl enable httpd
sudo systemctl enable postgresql


#For MySQL
sudo systemctl start httpd
sudo systemctl start mariadb
sudo systemctl enable httpd
sudo systemctl enable mariadb
```

Your system is ready to install and run Mattermost-LDAP module.


## Install
Clone (or download and extract) this repository in your /var/www/html (or your httpd root directory) :
```
cd ~
git clone https://github.com/royvandongen/Mattermost-IMAP.git
cd Mattermost-IMAP
cp -r oauth/ /var/www/html/
```

You need to create a database for the oauth server. For this purpose, you can use the script "init_postgres.sh" or "init_mysql.sh". These scripts try to configure your database automatically, by creating a new user and a new database associated for the oauth server. Scripts also create all tables necessary for the module. If script failed, please report here, and try to configure manually your database by adapting command in scripts. Before running the script you can change the default settings by editing the config_init.sh file and modifying configuration variables. For postgresql, you can copy and paste following lines :
```
nano config_init.sh
./init_postgres.sh
```

This script will automatically create and add a new client in the oauth server, returning a client id and a client secret. You need to keep these two token to configure Mattermost. Please be sure the client secret remained secret. The redirect url in the script must comply with the hostname of your Mattermost server, else Mattermost could not get data from the Oauth server.


## Configuration

* Init script configuration :
#### oauth_user
Oauth user in the database. This user must have right on the oauth database to store oauth tokens. By default : oauth	
#### oauth_pass
Oauth user password in the database. By default, oauth_secure-pass
#### ip
Hostname or IP address of the database. By default : 127.0.0.1 		
#### port
The port to connect to the database. By default : 5432 (postgres) 		
#### oauth_db_name
Database name for oauth server. By default	: oauth_db	
#### client_id	
The application ID shared with mattermost. This ID should be a random token. You can use openssl to generate this token (openssl rand -hex 32). By default, this variable contain the openssl command, which use the openssl package. The token will be printed at the end of the script. 
#### client_secret
The application secret shared with mattermost. This secret should be a random token. You can use openssl to generate this token (openssl rand -hex 32). By default, this variable contain the openssl command, which use the openssl package. The token will be printed at the end of the script. Secret must be different of the client ID.
#### redirect_uri
The callback address where oauth will send tokens to Mattermost. Normally it should be http://mattermost.company.com/signup/gitlab/complete
#### grant_types
The type of authentification use by Mattermost. It should be "authorization_code".
#### scope
The scope of authentification use by Mattermost. It should be "api".
#### user_id
The username of the user who create the Mattermost client in Oauth. This field has no impact, and could be used as a commentary field. By default this field is empty.

* Mattermost :
Active Gitlab authentication in system console > Gitlab (or config.json on server) and fill application id and secret with the two token got during install section. For the next fields use this :
```
User API Endpoint : http://HOSTNAME/oauth/resource.php
Auth Endpoint: http://HOSTNAME/oauth/authorize.php
Token Endpoint: http://HOSTNAME/oauth/token.php
```
Change HOSTNAME by hostname or ip of the server where you have installed Mattermost-LDAP module. 

* Database credentials
Edit oauth/config_db.php and adapt, with your settings, to set up database in PHP.

#### $host
Hostname or IP address of the database. (ex : localhost)
#### $port
The port of your database to connect. (ex : 5432 for postgres)
#### $name
Database name for oauth server. If you use init script make sure to use the same database name. (ex : oauth_db) 
#### $type
Database type to adapt PDO to your database server. Should be mysql or pgsql.
#### $username
Oauth user in the database. This user must have right on the oauth database to store oauth tokens. If you use init script make sure to use the same database user. (ex : oauth)
#### $password
Oauth user password in the database. If you use init script make sure to use the same database user. (ex : oauth_secure-pass)

* Imap configuration
Edit oauth/config_email.php and adapt, with your settings to set up the IMAP server.

#### $email_host
Hostname or IP address of the IMAP server. (ex : localhost)
#### $email_port
The port of your IMAP server to connect. (ex : 993 for imaps)
#### $email_proto
The used protocol for the emailserver. Defaults to IMAP, but can be changed to support POP3.

## Usage
If you have succeeded previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your IMAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with your emailaddress. The process will fail if an existing user already use your IMAP email. To bind a user to the IMAP authentication, sign in mattermost with this user account, go in account settings > security > sign-in method and "switch to using Gitlab SSO".


## Limitation
This module has been tested on Ubuntu with MySQL.

Others operating systems has not been tested yet but should work fine. 

Postgres has not really been tested so it is possible there is some bugs with.

## To do list
 * Change Gitlab button
 * Security audit

## Thanks

The original project ( Mattermost-LDAP ) is made by Denis Clavier. Please visit his personal github page [https://github.com/Crivaledaz](https://github.com/Crivaledaz)! or his Mattermost-LDAP project [https://github.com/Crivaledaz/Mattermost-LDAP](https://github.com/Crivaledaz/Mattermost-LDAP)

## Known issues
 * When used incorrectly you CAN do a plaintext authentication to an external IMAP server.
 Never, ever configure the IMAP/POP3 connector without the SSL option. There is reason NOT to have this configured in the config_email.php file.

 * PHP date timezone error
 Edit php.ini to set up date.timezone option and restart httpd service, or use the date_default_timezone_set() function in config_db.php

 * Token request failed
 Try to add a new rule in your firewall (or use iptables -F on both Mattermost server and Oauth server)

 * .htaccess does not work
 Add following lines to your httpd.conf and restart httpd service.
 ```
 <Directory "/var/www/html/oauth">
    AllowOverride All
</Directory>
 ```



