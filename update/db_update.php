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
	"7"=>array(
		"ALTER TABLE `global_companies` ADD `ab` TINYINT( 1 ) NULL DEFAULT '0' AFTER `company` ,ADD `nf` TINYINT( 1 ) NULL DEFAULT '0' AFTER `ab`;"
	)

);

