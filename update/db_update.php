<?php
/*
 * Date: 2012/07/24
 * Time: 9:47 AM
 */

$sql = array(
	"1"=>array(
		"ALTER TABLE `ab_marketers_targets` ADD `locked` TINYINT( 1 ) NULL DEFAULT '0';"
	),
	"2"=>array(
		"ALTER TABLE `global_users` ADD `su` TINYINT( 1 ) NULL DEFAULT '0';"
	),
	"3"=>array(
		"CREATE TABLE `nf_users_settings` (`ID` int( 6 ) NOT NULL AUTO_INCREMENT ,`uID` int( 6 ) DEFAULT NULL ,`settings` text,`pID` int( 6 ) DEFAULT NULL ,`last_activity` datetime DEFAULT NULL ,PRIMARY KEY ( `ID` ) ,KEY `uID` ( `uID` ) );",
		"CREATE TABLE `nf_users_pub` (`ID` int( 6 ) NOT NULL AUTO_INCREMENT ,`pID` int( 6 ) DEFAULT NULL ,`uID` int( 6 ) DEFAULT NULL ,PRIMARY KEY ( `ID` ) ,KEY `pID` ( `pID` ) ,KEY `uID` ( `uID` ) ) ;",
		"ALTER TABLE `global_publications` ADD `nf_currentDate` INT( 6 ) NULL DEFAULT NULL ,ADD INDEX ( `nf_currentDate` );",
		"ALTER TABLE `global_users_company` ADD `nf_permissions` TEXT NULL DEFAULT NULL AFTER `ab_permissions`;",
		"ALTER TABLE `global_users_company` ADD `nf` TINYINT( 1 ) NULL DEFAULT NULL AFTER `ab` ;",
	),
	"4"=>array(
		"ALTER TABLE `nf_users_settings` ADD `last_page` VARCHAR( 250 ) NULL DEFAULT NULL;",
		"ALTER TABLE `ab_users_settings` ADD `last_page` VARCHAR( 250 ) NULL DEFAULT NULL;",
	),
	"5"=>array(
		"ALTER TABLE `global_publications`  DROP `ab_colour_full_percent`,  DROP `ab_colour_spot_percent`,  DROP `ab_colour_full_min`,  DROP `ab_colour_spot_min`;"
	),
	"6"=>array(
		"ALTER TABLE `global_companies` ADD `ab_upload_material` TINYINT( 1 ) NULL DEFAULT '0';",
		"ALTER TABLE `global_publications` ADD `ab_upload_material` TINYINT( 1 ) NULL DEFAULT '1';"
	),
	"7" => array(
		"ALTER TABLE `global_companies` ADD `ab` TINYINT( 1 ) NULL DEFAULT '0' AFTER `company` ,ADD `nf` TINYINT( 1 ) NULL DEFAULT '0' AFTER `ab`;"
	),
	"8"=>array(
		"CREATE TABLE IF NOT EXISTS `global_logs` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `cID` int(6) DEFAULT NULL,  `app` varchar(3) DEFAULT NULL,  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `uID` int(6) DEFAULT NULL,  `label` varchar(100) DEFAULT NULL,  `section` varchar(50) DEFAULT NULL,  `log` text,  PRIMARY KEY (`ID`),  KEY `uID` (`uID`),  KEY `section` (`section`),  KEY `cID` (`cID`));"
	),
	"9"=>array(
		"ALTER TABLE `global_users_company` ADD `nf_author` TINYINT( 1 ) NULL DEFAULT NULL;"
	),
	"10"=>array(
		"ALTER TABLE `global_companies` ADD `packageID` INT( 6 ) NULL DEFAULT NULL;"
	),
	"11"=>array(
		"ALTER TABLE `global_users_company` ADD `allow_setup` TINYINT( 1 ) NULL DEFAULT '0' AFTER `uID`;"
	),
	"12" => array(
		"DROP TRIGGER IF EXISTS before_insert_ab_bookings;",
		"DROP TRIGGER IF EXISTS before_update_ab_bookings;",
		"DROP TRIGGER IF EXISTS after_update_global_dates;",
		"DROP TRIGGER IF EXISTS after_update_ab_accounts;",
		"CREATE TABLE IF NOT EXISTS `system_publishing_colours` ( `ID` int(6) NOT NULL AUTO_INCREMENT,  `colour` varchar(30) DEFAULT NULL,  `colourLabel` varchar(30) DEFAULT NULL,  `orderby` int(3) DEFAULT NULL,  PRIMARY KEY (`ID`));",
		"INSERT INTO `system_publishing_colours` (`ID`, `colour`, `colourLabel`, `orderby`) VALUES (1, 'None', 'Black and White', 1), (2, 'Full', 'Full Colour', 2), (3, 'Spot', 'Spot Colour', 3);",
		"CREATE TABLE IF NOT EXISTS `system_publishing_colours_groups` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `label` varchar(50) DEFAULT NULL,  `colours` varchar(50) DEFAULT NULL,  `icon` varchar(100) DEFAULT NULL,  `orderby` int(3) DEFAULT NULL,  PRIMARY KEY (`ID`));",
		"INSERT INTO `system_publishing_colours_groups` (`ID`, `label`, `colours`, `icon`, `orderby`) VALUES(1, 'Full Colour', '1,2,3', NULL, 2),(2, 'Black & White', '1', NULL, 1),(3, 'Spot Colour', '1,3', NULL, 3);",
		"RENAME TABLE `ab_colour_rates` TO `ab_placing_sub`;",
		"ALTER TABLE `ab_bookings` ADD `sub_placingID` INT( 6 ) NULL DEFAULT NULL AFTER `placing` , ADD `sub_placing` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `sub_placingID` , ADD INDEX ( `sub_placingID` );",
		"UPDATE ab_bookings SET colourID = (SELECT ID from system_publishing_colours WHERE system_publishing_colours.colour = ab_bookings.colour) WHERE ab_bookings.colour <> '';",
		"ALTER TABLE `ab_bookings` DROP `colour`, DROP `colourSpot`, DROP `colourLabel`;",
		"ALTER TABLE `global_publications` ADD `colours` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `publication`;",
		"UPDATE `ab_placing_sub` SET `colour`= (SELECT ID from system_publishing_colours where system_publishing_colours.colour = ab_placing_sub.colour);",
		"ALTER TABLE `ab_placing_sub` CHANGE `colour` `colourID` INT( 6 ) NULL DEFAULT NULL;",
		"ALTER TABLE `ab_placing` ADD `colourID` INT( 6 ) NULL DEFAULT NULL AFTER `placing`;",
		"ALTER TABLE `global_pages` CHANGE `colour` `colourID` INT( 6 ) NULL DEFAULT NULL;",
		"ALTER TABLE `ab_marketers_targets` CHANGE `target` `target` DECIMAL( 10, 2 ) NULL DEFAULT NULL;",

		"file:../db_triggers.sql",
	),
	"13"=>array(
		"ALTER TABLE `ab_bookings` ADD `payment_methodID` INT( 6 ) NULL DEFAULT NULL AFTER `keyNum` ,ADD `payment_method_note` VARCHAR( 100 ) NULL DEFAULT NULL AFTER `payment_methodID` ,ADD INDEX ( `payment_methodID` )",
		"CREATE TABLE IF NOT EXISTS `system_payment_methods` ( `ID` int(6) NOT NULL AUTO_INCREMENT, `label` varchar(30) DEFAULT NULL, `orderby` int(3) DEFAULT NULL,  PRIMARY KEY (`ID`));",
		"INSERT INTO `system_payment_methods` (`label`, `orderby`) VALUES (NULL, 1),('Cash', 2),('EFT', 3),('Cheque', 4);"

	),
	"14"=>array(
		"ALTER TABLE ab_bookings_logs DROP INDEX bID;",
		"ALTER TABLE `ab_bookings_logs` ADD INDEX ( `bID` );",
		"ALTER TABLE `ab_bookings_logs` ADD INDEX ( `userID` );",
		"ALTER TABLE `ab_bookings_logs` ADD INDEX ( `datein` );"
	),
	"15"=>array(
		"ALTER TABLE `ab_users_settings` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `pID` , ADD INDEX ( `cID` ) ;",
		"ALTER TABLE `nf_users_settings` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `pID` , ADD INDEX ( `cID` ) ;"
	),
	"16"=>array(
		"ALTER TABLE `global_pages` ADD INDEX ( `dID` );",
		"ALTER TABLE `global_pages` ADD INDEX ( `sectionID` );",
		"ALTER TABLE `global_pages` ADD INDEX ( `colourID` );"
	),
	"17"=>array(
		"ALTER TABLE `ab_bookings` ADD `dateChanged` TIMESTAMP NULL DEFAULT NULL AFTER `datein`;"
	),
	"18"=>array(
		"ALTER TABLE `ab_accounts` ADD `email` VARCHAR( 250 ) NULL DEFAULT NULL , ADD `phone` VARCHAR( 250 ) NULL DEFAULT NULL;"
	),
	"19"=>array(
		"ALTER TABLE `global_pages` ADD `ab_locked` TINYINT( 0 ) NULL DEFAULT NULL , ADD `nf_locked` TINYINT( 0 ) NULL DEFAULT NULL , ADD INDEX ( `ab_locked` , `nf_locked` )"
	),
	"20"=>array(
		"CREATE TABLE IF NOT EXISTS `global_messages` (`ID` int(6) NOT NULL, `from_uID` int(6) DEFAULT NULL, `to_uID` int(6) DEFAULT NULL, `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `heading` varchar(100) DEFAULT NULL, `message` text, `read` tinyint(1) DEFAULT '0', PRIMARY KEY (`ID`),  KEY `from_uID` (`from_uID`,`to_uID`));"
	),
	"21"=>array(
		"ALTER TABLE `global_messages` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;",
		"ALTER TABLE `global_messages` CHANGE `heading` `subject` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
		"ALTER TABLE `global_messages` ADD `app` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `ID`;"
	),
	"22"=>array(
		"ALTER TABLE `global_messages` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` , ADD INDEX ( `cID` );",
		"ALTER TABLE `global_messages` ADD `url` VARCHAR( 200 ) NULL DEFAULT NULL AFTER `message`;",
		"ALTER TABLE `global_messages` ADD INDEX ( `read` );"
	),
	"23"=>array(
		"ALTER TABLE `nf_stages` CHANGE `orderby` `orderby` INT( 6 ) NULL DEFAULT '1';"
	),
	"24"=>array(
		"ALTER TABLE `nf_article_newsbook_photos` ADD `ID` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;"
	),
	"25"=>array(
		"ALTER TABLE  `global_companies` ADD  `timezone` VARCHAR( 100 ) NULL DEFAULT NULL , ADD  `language` VARCHAR( 100 ) NULL DEFAULT NULL , ADD  `currency` VARCHAR( 100 ) NULL DEFAULT NULL"
	),
	"26"=>array(
		"ALTER TABLE  `ab_bookings` CHANGE  `cm`  `cm` DECIMAL( 7,3 ) NULL DEFAULT NULL;",
		"ALTER TABLE  `ab_bookings` CHANGE  `totalspace`  `totalspace` DECIMAL( 7,3 ) NULL DEFAULT NULL;",
		"ALTER TABLE  `global_companies` ADD  `units` VARCHAR( 100 ) NULL DEFAULT  'metric';",
		"ALTER TABLE  `global_publications` CHANGE  `pagewidth`  `pagewidth` DECIMAL( 6, 2 ) NULL DEFAULT  '0';",
		"ALTER TABLE  `global_publications` CHANGE  `cmav`  `cmav` DECIMAL( 6, 2 ) NULL DEFAULT NULL;"
	),
	"27"=>array(
		"ALTER TABLE  `nf_articles` CHANGE  `cm`  `cm` DECIMAL( 7, 3 ) NULL DEFAULT NULL;"
	),
	"28"=>array(
		"ALTER TABLE  `ab_bookings` ADD  `x_offset` DECIMAL( 6, 3 ) NULL DEFAULT NULL AFTER  `pageID` , ADD  `y_offset` DECIMAL( 6, 3 ) NULL DEFAULT NULL AFTER  `x_offset`;"
	),
    "29"=>array(
	    "ALTER TABLE  `global_pages` ADD  `pdf` VARCHAR( 100 ) NULL DEFAULT NULL;"
    ),
    "30"=>array(
	    "ALTER TABLE  `global_users_company` ADD  `pf` TINYINT( 1 ) NULL DEFAULT NULL AFTER  `nf`;",
        "ALTER TABLE  `global_users_company` ADD  `pf_permissions` TEXT NULL DEFAULT NULL AFTER  `nf_permissions`;",
        "ALTER TABLE  `global_companies` ADD  `pf` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `nf`;"
    ),
    "31"=>array(
	   "CREATE TABLE IF NOT EXISTS `pf_users_pub` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `pID` int(6) DEFAULT NULL,  `uID` int(6) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `pID` (`pID`),  KEY `uID` (`uID`));" ,
       "CREATE TABLE IF NOT EXISTS `pf_users_settings` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `uID` int(6) DEFAULT NULL,  `settings` text,  `pID` int(6) DEFAULT NULL,  `cID` int(6) DEFAULT NULL,  `last_activity` datetime DEFAULT NULL,  `last_page` varchar(250) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `uID` (`uID`),  KEY `cID` (`cID`));"
    ),
    "32"=>array(
	    "ALTER TABLE  `global_pages` ADD  `pdf_uID` INT( 6 ) NULL DEFAULT NULL AFTER  `pdf` , ADD  `pdf_datein` DATETIME NULL DEFAULT NULL AFTER  `pdf_uID`;"
    ),
    "33"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_users_settings` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `uID` int(6) DEFAULT NULL,  `settings` text,  `pID` int(6) DEFAULT NULL,  `cID` int(6) DEFAULT NULL,  `last_activity` datetime DEFAULT NULL,  `last_page` varchar(250) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `uID` (`uID`),  KEY `cID` (`cID`));",
        "CREATE TABLE IF NOT EXISTS `cm_users_pub` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `pID` int(6) DEFAULT NULL,  `uID` int(6) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `pID` (`pID`),  KEY `uID` (`uID`));",
        "ALTER TABLE  `global_users_company` ADD  `cm` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `pf`;",
        "ALTER TABLE  `global_users_company` ADD  `cm_permissions` TEXT NULL DEFAULT NULL AFTER  `pf_permissions`;",
        "ALTER TABLE  `global_companies` ADD  `cm` TINYINT NULL DEFAULT  '0' AFTER  `pf`;"
    ),
    "34"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_companies` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `company` varchar(200) DEFAULT NULL,  `short` varchar(200) DEFAULT NULL,  `phone` varchar(200) DEFAULT NULL,  `fax` varchar(200) DEFAULT NULL,  `email` varchar(200) DEFAULT NULL,  `website` varchar(200) DEFAULT NULL,  `address1` varchar(200) DEFAULT NULL,  `address2` varchar(200) DEFAULT NULL,  `city` varchar(200) DEFAULT NULL,  `country` varchar(200) DEFAULT NULL,  `postalCode` varchar(200) DEFAULT NULL,  `taxID` varchar(200) DEFAULT NULL,  PRIMARY KEY (`ID`));",
        "CREATE TABLE IF NOT EXISTS `cm_contacts` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `firstName` varchar(200) DEFAULT NULL,  `lastName` varchar(200) DEFAULT NULL,  `title` varchar(200) DEFAULT NULL,  `phone` varchar(200) DEFAULT NULL,  `mobile` varchar(200) DEFAULT NULL,  `fax` varchar(200) DEFAULT NULL,  `email` varchar(200) DEFAULT NULL,  `website` varchar(200) DEFAULT NULL,  `address1` varchar(200) DEFAULT NULL,  `address2` varchar(200) DEFAULT NULL,  `city` varchar(200) DEFAULT NULL,  `country` varchar(200) DEFAULT NULL,  `postalCode` varchar(200) DEFAULT NULL,  PRIMARY KEY (`ID`));",
        "CREATE TABLE IF NOT EXISTS `cm_companies_contacts` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `companyID` int(6) DEFAULT NULL,  `contactID` int(6) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `companyID` (`companyID`,`contactID`));",
        "ALTER TABLE  `cm_companies` ADD  `dateChanged` TIMESTAMP NULL DEFAULT NULL;",
        "CREATE TABLE IF NOT EXISTS `cm_companies_logs` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `companyID` int(6) DEFAULT NULL,  `log` text,  `label` varchar(100) DEFAULT NULL,  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `userID` int(6) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `userID` (`userID`),  KEY `datein` (`datein`),  KEY `companyID` (`companyID`));",
        "ALTER TABLE  `cm_companies` CHANGE  `dateChanged`  `dateChanged` DATETIME NULL DEFAULT NULL;",
        "ALTER TABLE  `cm_companies` ADD  `cID` INT( 6 ) NULL DEFAULT NULL AFTER  `ID` , ADD INDEX (  `cID` );",
        "ALTER TABLE  `cm_contacts` ADD  `cID` INT( 6 ) NULL DEFAULT NULL AFTER  `ID` ,ADD INDEX (  `cID` );"
    ),
    "35"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_contacts_logs` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `contactID` int(6) DEFAULT NULL,  `log` text,  `label` varchar(100) DEFAULT NULL,  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `userID` int(6) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `userID` (`userID`),  KEY `datein` (`datein`),  KEY `contactID` (`contactID`));"
    ),
    "36"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_details_types` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `group` varchar(100) DEFAULT NULL,  `type` varchar(100) DEFAULT NULL,  `orderby` int(4) DEFAULT NULL,  PRIMARY KEY (`ID`));",
        "INSERT INTO `cm_details_types` (`ID`, `group`, `type`, `orderby`) VALUES (1, 'Phones', 'Mobile', 1), (2, 'Phones', 'Work', 2), (3, 'Phones', 'Home', 3), (4, 'Phones', 'Main', 4), (5, 'Phones', 'Other', 5), (6, 'Fax', 'Work', 6), (7, 'Fax', 'Home', 7), (8, NULL, 'Pager', 100), (9, NULL, 'Website', 101), (10, 'Address', 'Address 1', 10), (11, 'Address', 'Address 2', 11), (12, 'Address', 'City/Town', 12), (13, 'Address', 'Suburb', 13);"
    ),
    "37"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_companies_details` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `parentID` int(6) DEFAULT NULL,  `catID` int(6) DEFAULT NULL,  `value` varchar(100) DEFAULT NULL,  `group` varchar(50) DEFAULT NULL,  `orderby` int(4) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `parentID` (`parentID`,`catID`));",
        "ALTER TABLE `cm_companies`  DROP `phone`,  DROP `fax`,  DROP `email`,  DROP `website`,  DROP `address1`,  DROP `address2`,  DROP `city`,  DROP `country`,  DROP `postalCode`;",
        "INSERT INTO  `cm_details_types` (`ID` ,`group` ,`type` ,`icon` ,`orderby`)VALUES (NULL ,  'Address',  'Postal Code', NULL ,  '14');",
        "DROP TABLE cm_details_types;",
        "CREATE TABLE IF NOT EXISTS `cm_contacts_details` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `parentID` int(6) DEFAULT NULL,  `catID` int(6) DEFAULT NULL,  `value` varchar(100) DEFAULT NULL,  `group` varchar(50) DEFAULT NULL,  `orderby` int(4) DEFAULT NULL,  PRIMARY KEY (`ID`),  KEY `parentID` (`parentID`,`catID`));",
        "ALTER TABLE `cm_contacts`  DROP `phone`,  DROP `mobile`,  DROP `fax`,  DROP `email`,  DROP `website`,  DROP `address1`,  DROP `address2`,  DROP `city`,  DROP `country`,  DROP `postalCode`;",
        "ALTER TABLE  `cm_companies_logs` CHANGE  `companyID`  `parentID` INT( 6 ) NULL DEFAULT NULL;",
        "ALTER TABLE  `cm_contacts_logs` CHANGE  `contactID`  `parentID` INT( 6 ) NULL DEFAULT NULL;",
        "ALTER TABLE  `cm_contacts` ADD  `dateChanged` TIMESTAMP NULL DEFAULT NULL;"
        
    ),
    "38"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_companies_links_company` (  `parentID` int(6) DEFAULT NULL,  `linkedID` int(6) DEFAULT NULL,  KEY `parentID` (`parentID`,`linkedID`));",
	    "CREATE TABLE IF NOT EXISTS `cm_companies_links_contact` (  `parentID` int(6) DEFAULT NULL,  `linkedID` int(6) DEFAULT NULL,  KEY `parentID` (`parentID`,`linkedID`));", 
	   
    ),
    "39"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_companies_notes` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `parentID` int(6) DEFAULT NULL,  `uID` int(6) DEFAULT NULL,  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `heading` varchar(200) DEFAULT NULL,  `note` text,  PRIMARY KEY (`ID`),  KEY `parentID` (`parentID`), KEY `uID` (`uID`));",
    ),
    "40"=>array(
	    "CREATE TABLE IF NOT EXISTS `cm_companies_interactions` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `parentID` int(6) DEFAULT NULL,  `typeID` int(6) DEFAULT NULL,  `uID` int(6) DEFAULT NULL,  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `heading` varchar(200) DEFAULT NULL,  `text` text,  PRIMARY KEY (`ID`),  KEY `parentID` (`parentID`),  KEY `uID` (`uID`),  KEY `typeID` (`typeID`));"
    )
	

);

