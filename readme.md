captivout
=========

based of http://sourceforge.net/projects/vouchergen/


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
 
