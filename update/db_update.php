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
	"12"=>array(
		"RENAME TABLE `ab_placing_sub` TO `ab_placing_sub`;",
		"ALTER TABLE `ab_bookings` ADD `sub_placingID` INT( 6 ) NULL DEFAULT NULL AFTER `placing` , ADD `sub_placing` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `sub_placingID` , ADD INDEX ( `sub_placingID` );",
		"UPDATE ab_bookings SET colourID = (SELECT ID from system_publishing_colours WHERE system_publishing_colours.colour = ab_bookings.colour) WHERE ab_bookings.colour <> ''",
		"ALTER TABLE `ab_bookings` DROP `colour`, DROP `colourSpot`, DROP `colourLabel`;",
		"ALTER TABLE `global_publications` ADD `colours` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `publication`;",
		"UPDATE `ab_placing_sub` SET `colour`= (SELECT ID from system_publishing_colours where system_publishing_colours.colour = ab_placing_sub.colour);",
		"ALTER TABLE `ab_placing_sub` CHANGE `colour` `colourID` INT( 6 ) NULL DEFAULT NULL;",
		"ALTER TABLE `ab_placing` ADD `colourID` INT( 6 ) NULL DEFAULT NULL AFTER `placing`;",
		"ALTER TABLE `global_pages` CHANGE `colour` `colourID` INT( 6 ) NULL DEFAULT NULL;",
		"ALTER TABLE `ab_marketers_targets` CHANGE `target` `target` DECIMAL( 10, 2 ) NULL DEFAULT NULL;",
		"file:../db_triggers.sql"



	)


);

