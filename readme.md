vouchergen
==========

based of http://sourceforge.net/projects/vouchergen/


roadmap
-------

  * use file for configuration (done)
  * extract db code into own module (done)
  * add templates for views (easier readable code) (done)
  * use pdo for db access (current mysql api deprecated, no transactions) (done)
  * add transactions (currently there is a small chance that same key is sendt to multiple persons) (done)
  * use prepared statement (to prevent sql injection) (done)
  * make config py env vars possible (config can be done in apache config file) (done)
  * make current config which is in db configurable by config file or env vars or remain in db (deploy can be easier made automatically) (done)
  * integrate i18n into templates (done)
  * make country phone prefix code configurable
  * add a bunch of tests (progressing...)
  * make userinterface beautiful (done) and consolidated (progressing...)
  * search for new name
 
