vouchergen
==========

based of http://sourceforge.net/projects/vouchergen/


roadmap
-------

  * use file for configuration (done)
  * extract db code into own module (done)
  * add a bunch of tests (progressing...)
  * add templates for views (easier readable code) (progressing...)
  * use PDO for db access (current mysql api deprecated, no transactions)
  * add transactions (currently there is a small chance that same key is sendt to multiple persons)
  * use prepared statement (to prevent sql injection)
  * make country code configurable
  * make config py env vars possible (config can be done in apache config file)
  * make current config which is in db configurable by config file or env vars or remain in db (deploy can be easier made automatically)
  * search for new name
  * integrate i18n into templates
 
