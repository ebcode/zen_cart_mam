#
# * This SQL script upgrades the core Zen Cart database structure from v1.3.9 to v1.3.9_Multiple_Addresses_Mod
# *
# * @package Installer
# * @access private
# * @copyright Copyright 2003-2010 Zen Cart Development Team
# * @copyright Portions Copyright 2003 osCommerce
# * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
# * @version $Id: mysql_upgrade_zencart_139_to_139_Multiple_Addresses_Mod.sql 17919 2011-12-20 04:45:45Z $
#

############ IMPORTANT INSTRUCTIONS ###############
#
# * Zen Cart uses the zc_install/index.php program to do database upgrades
# * This SQL script is intended to be used by running zc_install
# * It is *not* recommended to simply run these statements manually via any other means
# * ie: not via phpMyAdmin or via the Install SQL Patch tool in Zen Cart admin
# * The zc_install program catches possible problems and also handles table-prefixes automatically
# *
# * To use the zc_install program to do your database upgrade:
# * a. Upload the NEWEST zc_install folder to your server
# * b. Surf to zc_install/index.php via your browser
# * c. On the System Inspection page, scroll to the bottom and click on Database Upgrade
# *    NOTE: do NOT click on the "Install" button, because that will erase your database.
# * d. On the Database Upgrade screen, you'll be presented with a list of checkboxes for
# *    various Zen Cart versions, with the recommended upgrades already pre-selected.
# * e. Verify the checkboxes, then scroll down and enter your Zen Cart Admin username
# *    and password, and then click on the Upgrade button.
# * f. If any errors occur, you will be notified.  Some warnings can be ignored.
# * g. When done, you'll be taken to the Finished page.
#
#####################################################

# Set store to Down-For-Maintenance mode.  Must reset manually via admin after upgrade is done.
UPDATE configuration set configuration_value = 'true' where configuration_key = 'DOWN_FOR_MAINTENANCE';

# add switch for new split-tax functionality
#INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, #date_added) VALUES ('Show Split Tax Lines', 'SHOW_SPLIT_TAX_CHECKOUT', 'false', 'If multiple tax rates apply, show each rate as a separate line at checkout', '1', '22', #'zen_cfg_select_option(array(\'true\', \'false\'), ', now());

# Clear out active customer sessions
#TRUNCATE TABLE whos_online;
#TRUNCATE TABLE db_cache;
#TRUNCATE TABLE sessions;

# garbage collection for old paypal sessions:
DELETE FROM paypal_session WHERE expiry < unix_timestamp();

############ Multiple Addresses Mod ##################

# add multiple_addresses boolean and foreign key to orders_products_
#ALTER TABLE orders drop COLUMN multiple_addresses;
#ALTER TABLE orders ADD COLUMN multiple_addresses tinyint(1) default '0';

# add multiple_addresses_customers_baskets_products_orders table to connect orders and baskets products to addresses
#DROP TABLE multiple_addresses_customers_baskets_products_orders;
#CREATE TABLE multiple_addresses_customers_baskets_products_orders (multiple_addresses_customers_baskets_products_orders_id int(11) PRIMARY KEY AUTO_INCREMENT, #customers_basket_id int(11), customers_id int(11), orders_products_id int(11), orders_id int(11), products_id int(11), products_quantity_for_this_address smallint #unsigned, address_book_id int(11), shipping_method varchar(128), shipping_module_code varchar(32), cost decimal(15,4) default '0.0000', shipping_method_title varchar##(128), entry_company varchar(64), entry_firstname varchar(32), entry_lastname varchar(32), entry_street_address varchar(64), entry_suburb varchar(32), entry_postcode #varchar(10), entry_city varchar(32), entry_state varchar(32), entry_country_id int(11), entry_zone_id int(11)) ENGINE=MyISAM; 

# add entry_name to address_book table
#ALTER TABLE address_book drop column entry_title;
#ALTER TABLE address_book add column entry_title varchar(128) default 'default';

# add address book entry title maximum and minimum values to configuration
#DELETE FROM configuration where configuration_title = 'Address Book Entry Title';
#INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES #('Address Book Entry Title', 'ADDRESS_BOOK_ENTRY_TITLE_MIN_LENGTH', '1', 'Minimum length of address book entry titles ', '2', '4', now());

#INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES #('Address Book Entry Title', 'ADDRESS_BOOK_ENTRY_TITLE_MAX_LENGTH', '20', 'Maximum length of address book entry titles ', '3', '4', now());

# create table to store customers_id for toggling a multiple address checkout  
#DROP TABLE IF EXISTS multiple_addresses_mod;
#CREATE TABLE multiple_addresses_mod(customers_id int(11) PRIMARY KEY NOT NULL default '0'
#) ENGINE=MyISAM;

#####################################################

#### VERSION UPDATE STATEMENTS
## THE FOLLOWING 2 SECTIONS SHOULD BE THE "LAST" ITEMS IN THE FILE, so that if the upgrade fails prematurely, the version info is not updated.
##The following updates the version HISTORY to store the prior version's info (Essentially "moves" the prior version info from the "project_version" to "project_version_history" table
#NEXT_X_ROWS_AS_ONE_COMMAND:3
INSERT INTO project_version_history (project_version_key, project_version_major, project_version_minor, project_version_patch, project_version_date_applied, project_version_comment)
SELECT project_version_key, project_version_major, project_version_minor, project_version_patch1 as project_version_patch, project_version_date_applied, project_version_comment
FROM project_version;

## Now set to new version
UPDATE project_version SET project_version_major='1', project_version_minor='3.9_Multiple_Addresses_Mod', project_version_patch1='', project_version_patch1_source='', project_version_patch2='', project_version_patch2_source='', project_version_comment='Version Update 1.3.9->1.3.9_Multiple_Addresses_Mod', project_version_date_applied=now() WHERE project_version_key = 'Zen-Cart Main';
UPDATE project_version SET project_version_major='1', project_version_minor='3.9_Multiple_Addresses_Mod', project_version_patch1='', project_version_patch1_source='', project_version_patch2='', project_version_patch2_source='', project_version_comment='Version Update 1.3.9->1.3.9_Multiple_Addresses_Mod', project_version_date_applied=now() WHERE project_version_key = 'Zen-Cart Database';

#####  END OF UPGRADE SCRIPT
