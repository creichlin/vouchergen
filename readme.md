captivout
=========

based on http://sourceforge.net/projects/vouchergen/

warning
-------

It has only be tested on debian based linux systems. It is possible that problems arise on other systems.

changes
-------

  * use file for configuration
  * make config by env vars possible (config can be done in apache config file)
  * config from db can be set by file or env vars (deploy can be easier made automatically)
  * extract db code into own module
  * add templates for views (easier readable code)
  * use pdo for db access (current mysql api deprecated, no transactions)
  * add transactions (was a small chance that same key is sendt to multiple persons)
  * use prepared statement (to prevent sql injection)
  * integrate i18n into templates
  * make country phone prefix code configurable
  * add a bunch of tests
  * make userinterface beautiful (done) and consolidated
 
installation
------------

caaptivout needs php, mysql and curl. on debian based systems one has to install following packages:

    apache2 libapache2-mod-php5 mysql-server php5-mysql php5-curl

get the captivout code from https://github.com/creichlin/captivout

the content of the folder captivout goes into a folder which is accessible from apache. a possible apache config file could look like:

    <VirtualHost *:80>
        ServerAdmin webmaster@localhost

        DocumentRoot /var/www/captivout
        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory /var/www/captivout/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log

        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>
    
A empty mysql database needs to be created. this can be done by connecting to mysql as root and execute following statemenets:

    CREATE DATABASE captivout;
    CREATE USER 'captivout'@'localhost' IDENTIFIED BY 'db-password';
    GRANT ALL PRIVILEGES ON captivout. * TO 'captivout'@'localhost';
    
A config file goes into /etc/captivout/captivout

    username = admin
    password = admin-interface-password
    db-host = localhost
    db-username = captivout
    db-password = db-password
    db-schema = captivout
    
The values above are needed. Those are username and password for the web interface and connection information for the
databse created above. Other configuration values can be added, see the config section for a list.

This should be enough to login over the web-interface.


using sms with pfsense
----------------------

The captivate zone which should be used for allowing to send tickets by sms needs to have the index.php and error.php files
uploaded as **Portal page contents** and **Authentication error page contents** in the pfsense captivate zone configuration.

In these two files the HOST string needs to be overwriten by the hostname where captivout is installed. The HOST also
needs to be added to the **Allowed Hostnames** config in the used zone.

These files
forward the logged out user to a captivout from where he can request an sms with a ticket and he can also enter
the received ticket code which will post back to pfsense where he will be authenticated.


configuration
-------------

Here the available config options are listed. They can go into the /etc/captivout/captivout config file.

They can also be set by environment variables of the form CPO_USERNAME, CPO_DB_HOST... with **-** replaced by **_** and
**CPO_** prefix. Environment variables will overwrite the file values.

Some of the values can also be set inside the web interface. They will overwrite all other values. In the interface
there is also an overview with all values and where they are set.


##### username

The username used to log into the web interface.

##### password

The password needed to log into the web interface

##### db-host

Hostname of the server where mysql is installed. Usually localhost. If not localhost the webserver needs to be allowed
to access the db server.

##### db-schema

The name of the database/schema

##### db-username

username to log into mysql. needs rights to create, delete tables as well as CRUD operations.

##### db-password

password for mysql login.

##### dbtables

key value pairs in json notation. the separate tables that will be created where the tickets get stored. The key is the
physical name of the databse, the value is the human readable version for the interface.

    {'sms': 'Sms Tickets', 'print': 'Print Tickets'}
    
will create two tables, one for sending tickets per sms and one for printing out pdfs with ticket vouchers.

In the admin interface this value is edited as a multiline textinput where key and value are separated by the | symbol.

##### tbl_header

a list of strings in json format. each value is the heading of the columns in the generated pdf. So far exactly 4 values are needed.
The first volumn is the ticket id (from db) and the second is the actual code.

    {'ID', 'Ticket', 'Date', 'Guest'}
    
In the above configuration string the table contains headers with the ticket id and the ticket code as well two empty
columns where the date and the guest can be noted of the ticket given out.

In the admin interface this is a multiline textinput where each column is one line.

##### vou_hader

The title which goes on each printed ticket.

##### vou_text

Some text on each printed ticket.

##### vou_label

Emphasized ext that goes on each printed ticket, the tickets code will be appended.

##### sms_gateway

This is a list of key, value objects encoded in json. Its used to define the gatway to send sms.

    [
      {
        "label": "Default",
        "table": "sms",
        "language": "de",
        "countryPrefix": "+41",
        "example": "079 123 45 67",
        "text": "Der code fuer das netz lala lautet {TICKET}",
        "validator": "0[0-9]{9}",
        "httpGet": "http://www.sms-revolution.ch/API/httpsms.php?user=user&password=password&text={TEXT}&to={NUMBER}"
      }
    ]

  * label is only for admin interface and basically irrelevant
  * table defines the table where the codes for the sms should be fetched from. one of the keys in the **dbtables** option
  * language is the code of the language the captivate login page should be in. the code given needs to have a file in captivout/include/lang
  * countryPrefix: country code, only one can be configured for now
  * example is a dummy number that will be displayed in the input field so the guest knows how to enter the number. should be withouth country code
  * text is the text that will be sendt by the sms. {TICKET} will be replaced by the actual ticket code.
  * validator validate a user entered number with all non numeric characters removed beforehand. is a regular expression, php style.
  * httpGet is the call to the actual sms gateway. only get request with the information encoded as parameters is supported for now.
    the {TEXT} part will be replaced by the sms content to be sendt and {NUMBER} will be the entered phone number with the country code prefix.
  
  
