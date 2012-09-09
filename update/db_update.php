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
	)

);

