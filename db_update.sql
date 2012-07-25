SET @companyName:="Kathorus";
SET @usernamePrefix:="@zoutnet.co.za";



ALTER DATABASE adbooker_v5 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `adbooker_ab_access` DROP `addbooking` ,DROP `priv_1` ,DROP `priv_2` ,DROP `priv_3` ,DROP `priv_4` ,DROP `priv_5` ,DROP `priv_6` ,DROP `priv_7` ,DROP `priv_8` ,DROP `priv_9` ,DROP `priv_10` ,DROP `priv_11` ,DROP `priv_12` ,DROP `priv_13` ,DROP `priv_14` ,DROP `priv_15` ,DROP `priv_16` ,DROP `priv_17` ,DROP `priv_18` ,DROP `priv_19` ,DROP `priv_20` ,DROP `priv_21` ,DROP `priv_22` ,DROP `priv_23` ,DROP `priv_24` ,DROP `priv_25` ,DROP `priv_26` ,DROP `priv_27` ,DROP `priv_28` ,DROP `priv_29` ,DROP `priv_30` ,DROP `priv_31` ,DROP `priv_32` ,DROP `priv_33` ,DROP `priv_34` ,DROP `priv_35` ,DROP `priv_36` ,DROP `priv_37` ,DROP `priv_38` ,DROP `priv_39` ,DROP `priv_40` ,DROP `priv_41` ,DROP `priv_42` ,DROP `priv_43` ,DROP `priv_44` ,DROP `priv_45` ,DROP `priv_46` ,DROP `priv_47` ,DROP `priv_48` ,DROP `priv_49` ;


RENAME TABLE `adbooker_ab_access` TO `global_users`; ALTER TABLE `global_users` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_datelist` TO `global_dates`; ALTER TABLE `global_dates` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_publications` TO `global_publications`; ALTER TABLE `global_publications` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

RENAME TABLE `adbooker_ab_accesspublink` TO `ab_users_pub`; ALTER TABLE `ab_users_pub` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_accountnumbers` TO `ab_accounts`; ALTER TABLE `ab_accounts` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_accountpub` TO `ab_accounts_pub`; ALTER TABLE `ab_accounts_pub` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_advertplacing` TO `ab_placing`; ALTER TABLE `ab_placing` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_categories` TO `ab_categories`; ALTER TABLE `ab_categories` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_cat_pub` TO `ab_category_pub`; ALTER TABLE `ab_category_pub` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_marketers` TO `ab_marketers`; ALTER TABLE `ab_marketers` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_marketerspublink` TO `ab_marketers_pub`; ALTER TABLE `ab_marketers_pub` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_marketer_targets` TO `ab_marketers_targets`; ALTER TABLE `ab_marketers_targets` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_materialpeople` TO `ab_material_source`; ALTER TABLE `ab_material_source` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_materialpeoplepublink` TO `ab_material_source_pub`; ALTER TABLE `ab_material_source_pub` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_pageload` TO `ab_page_load`; ALTER TABLE `ab_page_load` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
RENAME TABLE `adbooker_ab_simpleadd` TO `ab_advert_sizes`; ALTER TABLE `ab_advert_sizes` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `adbooker_ab_modules`, `adbooker_ab_module_users`, `adbooker_ab_mod_costanalysis`, `adbooker_ab_mod_quotations`, `adbooker_backup_info`;

CREATE TABLE IF NOT EXISTS `global_companies` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`company` varchar(100) DEFAULT NULL,
	PRIMARY KEY (`ID`)
);

ALTER TABLE `global_publications` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` , ADD INDEX ( `cID` );
ALTER TABLE `global_publications` CHANGE `papers` `publication` VARCHAR( 100 ) NULL DEFAULT NULL;

ALTER TABLE `ab_accounts` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` ,ADD INDEX ( `cID` );
ALTER TABLE `ab_accounts` CHANGE `Client` `account` VARCHAR( 50 ) NULL DEFAULT NULL;
ALTER TABLE `ab_accounts` CHANGE `Comment` `remark` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `ab_accounts` CHANGE `AccountNum` `accNum` VARCHAR( 50 ) NULL DEFAULT NULL;
ALTER TABLE `ab_accounts` CHANGE `BlockAccount` `blocked` TINYINT( 1 ) NULL DEFAULT '0';

ALTER TABLE ab_accounts DROP INDEX ID;
ALTER TABLE ab_accounts DROP INDEX AccountNum;

ALTER TABLE `ab_accounts_pub` CHANGE `accNum` `aID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_accounts_pub` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL;

ALTER TABLE ab_accounts_pub DROP PRIMARY KEY;
ALTER TABLE `ab_accounts_pub` ADD PRIMARY KEY(`ID`);
ALTER TABLE `ab_accounts_pub` DROP INDEX ID;
ALTER TABLE `ab_accounts_pub` ADD INDEX ( `pID` );
ALTER TABLE `ab_accounts_pub` ADD INDEX ( `aID` );
ALTER TABLE `ab_accounts_pub` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_accounts_pub` CHANGE `aID` `aID` INT( 6 ) NULL DEFAULT NULL;


ALTER TABLE `ab_advert_sizes` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE `ab_advert_sizes` ADD PRIMARY KEY(`ID`);
ALTER TABLE `ab_advert_sizes` DROP INDEX ID;
ALTER TABLE `ab_advert_sizes` ADD INDEX ( `pID` );

ALTER TABLE `ab_categories` CHANGE `catname` `category` VARCHAR( 50 ) NULL DEFAULT NULL;
ALTER TABLE `ab_categories` CHANGE `orderby` `orderby` INT( 3 ) NULL DEFAULT NULL;
ALTER TABLE `ab_categories` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` ,ADD INDEX ( `cID` );
ALTER TABLE `ab_category_pub` CHANGE `cID` `catID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_category_pub` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL;

ALTER TABLE `ab_marketers` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` ,ADD INDEX ( `cID` );
ALTER TABLE `ab_marketers` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers` DROP INDEX ID;
ALTER TABLE `ab_marketers` CHANGE `Marketer` `marketer` VARCHAR( 30 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers` CHANGE `MarketerTel` `number` VARCHAR( 30 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers` CHANGE `MarketerEmail` `email` VARCHAR( 50 ) NULL DEFAULT NULL;

ALTER TABLE ab_marketers_pub DROP PRIMARY KEY;
ALTER TABLE `ab_marketers_pub` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers_pub` CHANGE `mID` `mID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers_pub` ADD PRIMARY KEY(`ID`);
ALTER TABLE ab_marketers_pub DROP INDEX ID;
ALTER TABLE `ab_marketers_pub` ADD INDEX ( `pID` );
ALTER TABLE `ab_marketers_pub` ADD INDEX ( `mID` );
ALTER TABLE `ab_marketers_pub` DROP `targetamount`;

ALTER TABLE `ab_marketers_targets` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ab_marketers_targets` CHANGE `mID` `mID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers_targets` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE `ab_marketers_targets` CHANGE `target` `target` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_marketers_targets` ADD PRIMARY KEY(`ID`);
ALTER TABLE ab_marketers_targets DROP INDEX ID;
ALTER TABLE `ab_marketers_targets` ADD INDEX ( `pID` );
ALTER TABLE `ab_marketers_targets` ADD INDEX ( `mID` );

ALTER TABLE `ab_material_source` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ab_material_source` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_material_source` ADD PRIMARY KEY(`ID`);
ALTER TABLE ab_material_source DROP INDEX ID;
ALTER TABLE `ab_material_source` ADD INDEX ( `uID` );
ALTER TABLE `ab_material_source` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` ,ADD INDEX ( `cID` );

ALTER TABLE `ab_material_source_pub` ADD PRIMARY KEY(`ID`);
ALTER TABLE ab_material_source_pub DROP INDEX ID;
ALTER TABLE `ab_material_source_pub` ADD INDEX ( `pID` );
ALTER TABLE `ab_material_source_pub` ADD INDEX ( `mID` );

ALTER TABLE `ab_page_load` CHANGE `paperID` `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE ab_page_load DROP INDEX ID;
ALTER TABLE `ab_page_load` ADD INDEX ( `pID` );
ALTER TABLE `ab_page_load` CHANGE `pages` `pages` INT( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `ab_page_load` CHANGE `percenttouse` `percent` INT( 3 ) NULL DEFAULT NULL;

ALTER TABLE `ab_placing` CHANGE `addType` `placing` VARCHAR( 50 ) NULL DEFAULT NULL;
ALTER TABLE `ab_placing` CHANGE `tariffs` `rate` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_placing` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE ab_placing DROP INDEX ID;
ALTER TABLE `ab_placing` ADD INDEX ( `pID` );


ALTER TABLE `ab_users_pub` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ab_users_pub` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_users_pub` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE ab_users_pub DROP PRIMARY KEY;
ALTER TABLE `ab_users_pub` ADD PRIMARY KEY(`ID`);
ALTER TABLE ab_users_pub DROP INDEX ID;
ALTER TABLE `ab_users_pub` ADD INDEX ( `pID` );
ALTER TABLE `ab_users_pub` ADD INDEX ( `uID` );

ALTER TABLE `global_dates` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE global_dates DROP INDEX ID;
ALTER TABLE `global_dates` DROP `archived`;
ALTER TABLE `global_dates` ADD INDEX ( `pID` ) ;
ALTER TABLE `global_dates` CHANGE `DateIN` `publish_date` DATE NULL DEFAULT NULL;
ALTER TABLE `global_dates` ADD `ab_current` TINYINT( 1 ) NULL DEFAULT '0', ADD INDEX ( `ab_current` );
UPDATE global_dates SET ab_current = currentShow;
ALTER TABLE `global_dates` DROP `currentShow`;

ALTER TABLE `global_publications` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `global_publications`
DROP `numRepeet`,
DROP `DateCycle`,
DROP `MonthToshow`,
DROP `calctype`;
ALTER TABLE global_publications DROP PRIMARY KEY;
ALTER TABLE `global_publications` ADD PRIMARY KEY(`ID`);
ALTER TABLE global_publications DROP INDEX ID;
UPDATE global_publications SET cashsalnumber = (SELECT ID FROM ab_accounts WHERE accNum = global_publications.cashsalnumber);
ALTER TABLE `global_publications` CHANGE `cashsalnumber` `cashsaleID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` CHANGE `InsertRate` `InsertRate` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` CHANGE `pub_tel` `pub_tel` VARCHAR( 20 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` CHANGE `pub_fax` `pub_fax` VARCHAR( 20 ) NULL DEFAULT NULL;

ALTER TABLE `global_publications` CHANGE `fcolper` `ab_colour_full_percent` TINYINT ( 3 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` CHANGE `fcolmin` `ab_colour_full_min` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` CHANGE `scolper` `ab_colour_spot_percent` TINYINT( 3 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` CHANGE `scolmin` `ab_colour_spot_min` DECIMAL( 8, 2 ) NULL DEFAULT NULL;


ALTER TABLE global_users DROP INDEX ID;
ALTER TABLE `global_users` CHANGE `FullName` `fullName` VARCHAR( 100 ) NULL DEFAULT NULL;
ALTER TABLE `global_users` CHANGE `Username` `email` VARCHAR( 100 ) NULL DEFAULT NULL;
ALTER TABLE `global_users` CHANGE `Password` `password` VARCHAR( 100 ) NULL DEFAULT NULL;
ALTER TABLE `global_users` ADD `last_app` VARCHAR( 10 ) NULL DEFAULT NULL;





INSERT INTO adbooker_ab_archives
		SELECT * FROM adbooker_ab_bookings;
DROP TABLE adbooker_ab_bookings;

RENAME TABLE `adbooker_ab_archives` TO `ab_bookings`; ALTER TABLE `ab_bookings` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `ab_bookings` DROP INDEX ID;
ALTER TABLE `ab_bookings` ADD INDEX ( `pID` );
ALTER TABLE `ab_bookings` ADD INDEX ( `datein` );
ALTER TABLE `ab_bookings` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE `ab_bookings` CHANGE `client` `client` VARCHAR( 50 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `Colour` `colour` VARCHAR( 50 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `ColourCost` `colourCost` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `colourspot` `colourSpot` VARCHAR( 40 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `totalshouldbe` `totalShouldbe` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `totalcost` `totalCost` DECIMAL( 8, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `Discount` `discount` DECIMAL( 5, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `AgDiscount` `agencyDiscount` DECIMAL( 5, 2 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `AdvertPlacing` `placingID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `AdvertPlacingName` `placing` VARCHAR( 100 ) NULL DEFAULT NULL;
UPDATE `ab_bookings` SET `categories` = NULL WHERE `categories` = '';
ALTER TABLE `ab_bookings` CHANGE `categories` `categoryID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` ADD `category` VARCHAR( 100 ) NULL DEFAULT NULL AFTER `categoryID`;

UPDATE ab_bookings SET category = (SELECT category FROM `ab_categories` WHERE ab_categories.ID = ab_bookings.categoryID) WHERE categoryID is not null;
UPDATE ab_bookings SET bookingtype =
	CASE bookingtype
		WHEN 'advert' THEN 1
		WHEN 'insert' THEN 2
		END;

ALTER TABLE `ab_bookings` CHANGE `bookingtype` `typeID` TINYINT( 3 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `AccountNum` `accNum` VARCHAR( 20 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `ordernum` `orderNum` VARCHAR( 40 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `keynum` `keyNum` VARCHAR( 40 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `comments` `remark` TEXT NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` ADD `remarkType` TINYINT( 3 ) NULL DEFAULT NULL AFTER `remark`;
UPDATE ab_bookings SET remarkType = '1';
ALTER TABLE `ab_bookings` CHANGE `marketer` `marketer` VARCHAR( 30 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` CHANGE `marketerID` `marketerID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` ADD `userID` INT( 6 ) NULL DEFAULT NULL AFTER `marketerID`;
ALTER TABLE `ab_bookings` CHANGE `BookedBy` `userName` VARCHAR( 30 ) NULL DEFAULT NULL;

UPDATE ab_bookings SET userID = (SELECT ID FROM `global_users` WHERE global_users.email = ab_bookings.userName LIMIT 0,1 );

ALTER TABLE `ab_bookings`
DROP `material_date`,
DROP `layout_date`,
DROP `MaterialReady`,
DROP `matpersonID`,
DROP `matperson`,
DROP `layoutchecked`;
ALTER TABLE `ab_bookings` CHANGE `Checked` `checked` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `ab_bookings` CHANGE `repeated` `repeat` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `ab_bookings` CHANGE `checked_date` `checked_date` DATETIME NULL DEFAULT NULL AFTER `checked`;


ALTER TABLE `ab_bookings` ADD `dID` INT( 6 ) NULL DEFAULT NULL AFTER `pID`;
UPDATE ab_bookings SET dID = (SELECT ID FROM `global_dates` WHERE global_dates.pID = ab_bookings.pID AND global_dates.`publish_date` = ab_bookings.datein LIMIT 0,1 );

ALTER TABLE `ab_bookings` CHANGE `datein` `publishDate` DATE NULL DEFAULT NULL AFTER dID;
ALTER TABLE `ab_bookings` CHANGE `BookedDate` `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `ab_bookings` DROP INDEX `datein` , ADD INDEX `publishDate` ( `publishDate` );
ALTER TABLE `ab_bookings` ADD INDEX ( `dID` );
ALTER TABLE `ab_bookings` ADD INDEX ( `marketerID` );
ALTER TABLE `ab_bookings` ADD INDEX ( `placingID` );
ALTER TABLE `ab_bookings` ADD INDEX ( `categoryID` );
ALTER TABLE `ab_bookings` ADD INDEX ( `userID` );
ALTER TABLE `ab_bookings` ADD INDEX ( `checked` );

CREATE TABLE IF NOT EXISTS `ab_bookings_types` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`type` varchar(100) DEFAULT NULL,
	`orderby` int(6) DEFAULT '0',
	PRIMARY KEY (`ID`)
);



DROP TABLE `adbooker_ab_colours`, `adbooker_ab_deleted`, `adbooker_ab_bookings_delete`, `adbooker_ab_bookings_edit`, `adbooker_ab_placing_link`, `adbooker_ab_placing_loads`, `adbooker_ab_placing_sec`, `adbooker_ab_printlist`, `adbooker_ab_printlistusers`, `adbooker_ab_printlist_sql`;

# ------------------------------------------------------- #

INSERT INTO `global_companies` (`company`) VALUES (@companyName);

SET @cID = LAST_INSERT_ID();

UPDATE ab_accounts SET cID = @cID;
UPDATE ab_categories SET cID = @cID;
UPDATE ab_marketers SET cID =@cID;
UPDATE ab_material_source SET cID = @cID;
UPDATE global_publications SET cID =@cID;

INSERT INTO `ab_bookings_types` (`ID`,`type`) VALUES ('1', 'Adverts'),('2', 'Inserts');

CREATE TABLE IF NOT EXISTS `ab_users_settings` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`uID` int(6) DEFAULT NULL,
	`list_settings` text,
	PRIMARY KEY (`ID`),
	KEY `uID` (`uID`)
);

CREATE TABLE IF NOT EXISTS `ab_accounts_status` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`cID` int(6) DEFAULT NULL,
	`status` varchar(100) DEFAULT NULL,
	`blocked` tinyint(1) DEFAULT '0',
	`labelClass` varchar(100) DEFAULT NULL,
	`orderby` int(6) DEFAULT '0',
	PRIMARY KEY (`ID`),
	KEY `cID` (`cID`)
);
INSERT INTO
	`ab_accounts_status` (`ID`, `cID`, `status`, `blocked`, `labelClass`, `orderby`)
	VALUES (1, @cID, 'Ok', 0, NULL, 0),
		(2,@cID, 'Blocked', 1, 'label-important', 2),
		(3, @cID, 'Cash only', 0, 'label-warning', 1);



ALTER TABLE `ab_accounts` CHANGE `blocked` `statusID` INT( 6 ) NULL DEFAULT NULL;

UPDATE `ab_accounts` SET `statusID` = '2' WHERE `statusID` = '1';
UPDATE `ab_accounts` SET `statusID` = '1' WHERE `statusID` = '0';




ALTER TABLE `ab_users_settings` CHANGE `list_settings` `settings` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


UPDATE `ab_accounts` SET account = replace(account,'´','\'');
UPDATE `ab_accounts` SET account = replace(account,'&acute;','\'');

UPDATE `ab_bookings` SET client = replace(client,'´','\''), keyNum = replace(keyNum,'´','\''), orderNum = replace(orderNum,'´','\''), remark = replace(remark,'´','\'');
UPDATE `ab_bookings` SET client = replace(client,'&acute;','\''), keyNum = replace(keyNum,'&acute;','\''), orderNum = replace(orderNum,'&acute;','\''), remark = replace(remark,'&acute;','\'');


UPDATE `ab_categories` SET category = replace(category,'´','\'');
UPDATE `ab_categories` SET category = replace(category,'&acute;','\'');
UPDATE `ab_placing` SET placing = replace(placing,'´','\'');
UPDATE `ab_placing` SET placing = replace(placing,'&acute;','\'');

ALTER TABLE `ab_bookings` CHANGE `remarkType` `remarkTypeID` INT( 6 ) NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `ab_remark_types` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`remarkType` varchar(100) DEFAULT NULL,
	`labelClass` varchar(30) DEFAULT NULL,
	PRIMARY KEY (`ID`)
);


INSERT INTO	`ab_remark_types` (`remarkType`, `labelClass`) VALUES ('General', NULL),('Info', 'label-info'),('Instruction', 'label-warning');
ALTER TABLE `ab_users_settings` ADD `pID` INT( 6 ) NULL DEFAULT NULL;

ALTER TABLE `ab_bookings` ADD INDEX ( `accNum` );
ALTER TABLE `ab_accounts` ADD INDEX ( `accNum` );

CREATE TABLE IF NOT EXISTS `ab_colour_rates` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`pID` int(6) DEFAULT NULL,
	`placingID` int(6) DEFAULT NULL,
	`label` varchar(100) DEFAULT NULL,
	`rate` decimal(8,2) DEFAULT '0.00',
	`colour` varchar(20) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `placingID` (`placingID`,`pID`)
);
ALTER TABLE `ab_colour_rates` ADD `orderby` TINYINT( 3 ) NULL DEFAULT '0';


UPDATE ab_bookings SET `rate` = InsertRate WHERE typeID = '2';
ALTER TABLE `ab_bookings` DROP `InsertRate`;
ALTER TABLE `ab_bookings` ADD `colourLabel` VARCHAR( 100 ) NULL DEFAULT NULL AFTER `colourSpot`;
ALTER TABLE `ab_bookings` ADD `colourID` int( 6 ) NULL DEFAULT NULL AFTER `client`;
ALTER TABLE `ab_bookings` ADD INDEX ( `colourID` );
ALTER TABLE `ab_bookings` DROP `colourCost`;

CREATE TABLE IF NOT EXISTS `ab_bookings_logs` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`bID` int(6) DEFAULT NULL,
	`log` text,
	`label` varchar(100) DEFAULT NULL,
	`datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`userID` int(6) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `bID` (`bID`,`userID`)
);

INSERT INTO	ab_bookings_logs (bID, log, label, datein, userID) SELECT bID, null as log, action as label, datein, (SELECT ID FROM global_users WHERE global_users.email = adbooker_ab_booking_log.user) AS userID  FROM adbooker_ab_booking_log;
DROP TABLE adbooker_ab_booking_log;


	RENAME TABLE `ab_material_source` TO `ab_production` ;
RENAME TABLE `ab_material_source_pub` TO `ab_production_pub` ;
ALTER TABLE `ab_production` CHANGE `name` `production` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `ab_production_pub` CHANGE `mID` `productionID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE ab_production_pub DROP INDEX mID;
ALTER TABLE `ab_production_pub` ADD INDEX ( `productionID` );

ALTER TABLE `ab_bookings` ADD `material_source` TINYINT( 1 ) NULL DEFAULT NULL AFTER `checked_date` ,
ADD `material_productionID` INT( 6 ) NULL DEFAULT NULL AFTER `material_source` ,
ADD `material_file` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `material_productionID` ,
ADD `material_status` TINYINT( 1 ) NULL DEFAULT '0' AFTER `material_file` ,
ADD `material_date` TIMESTAMP NULL DEFAULT NULL AFTER `material_status` ,
ADD INDEX ( `material_productionID` , `material_status` );

ALTER TABLE `ab_bookings` ADD `material_approved` TINYINT( 1 ) NULL DEFAULT '0' AFTER `material_date`;
ALTER TABLE `ab_bookings` ADD `material_production` VARCHAR( 100 ) NULL DEFAULT NULL AFTER `material_productionID`;
ALTER TABLE `ab_bookings` ADD `checked_userID` INT( 6 ) NULL DEFAULT NULL AFTER `checked_date` ,
ADD INDEX ( `checked_userID` );
ALTER TABLE `ab_bookings` ADD `checked_user` VARCHAR( 100 ) NULL DEFAULT NULL AFTER `checked_userID`;



ALTER TABLE `ab_users_settings` ADD `page` VARCHAR( 100 ) NULL DEFAULT NULL;

ALTER TABLE `ab_bookings` ADD `deleted` TINYINT( 1 ) NULL DEFAULT NULL ,
ADD `deleted_userID` INT( 6 ) NULL DEFAULT NULL ,
ADD `deleted_date` DATETIME NULL DEFAULT NULL ,
ADD `deleted_reason` TEXT NULL DEFAULT NULL ,
ADD INDEX ( `deleted` , `deleted_userID` );

ALTER TABLE `ab_bookings` ADD `deleted_user` VARCHAR( 100 ) NULL DEFAULT NULL AFTER `deleted_userID`;

ALTER TABLE `global_dates` ADD `pages` INT( 3 ) NULL DEFAULT '0',
ADD INDEX ( `pages` );

ALTER TABLE `ab_bookings` ADD `pageID` INT( 6 ) NULL DEFAULT NULL AFTER `material_approved` ,
ADD INDEX ( `pageID` );

CREATE TABLE IF NOT EXISTS `global_pages` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`pID` int(6) DEFAULT NULL,
	`dID` int(6) DEFAULT NULL,
	`sectionID` int(6) DEFAULT NULL,
	`page` decimal(7,1) DEFAULT NULL,
	`colour` varchar(50) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `pID` (`pID`,`dID`,`sectionID`)
) ;

CREATE TABLE IF NOT EXISTS `global_pages_sections` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`section` varchar(100) DEFAULT NULL,
	`section_colour` varchar(100) DEFAULT NULL,
	PRIMARY KEY (`ID`)
) ;

ALTER TABLE `global_pages_sections` ADD `pID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` ,
ADD INDEX ( `pID` );

ALTER TABLE `ab_bookings` CHANGE `repeat` `repeat_from` INT( 6 ) NULL DEFAULT NULL;

ALTER TABLE `global_users` ADD `last_activity` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `global_users` ADD `last_page` VARCHAR( 250 ) NULL DEFAULT NULL;

ALTER TABLE `global_pages` ADD `locked` TINYINT( 1 ) NULL DEFAULT '0';


# ----------------- #
ALTER TABLE `global_publications` ADD `ab_currentDate` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `global_publications` ADD INDEX ( `ab_currentDate` );

UPDATE  global_publications SET ab_currentDate = (SELECT ID FROM global_dates WHERE pID = global_publications.ID AND ab_current = '1');

ALTER TABLE `global_dates` DROP `ab_current`;
# ----------------- #

CREATE TABLE IF NOT EXISTS `global_users_company` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`cID` int(6) DEFAULT NULL,
	`uID` int(6) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `cID` (`cID`,`uID`)
);
ALTER TABLE `global_users_company` ADD `ab` TINYINT( 1 ) NULL DEFAULT NULL;
INSERT INTO	global_users_company (cID, uID, ab) SELECT @cID as cID, ID as uID, '1' as ab FROM global_users;

ALTER TABLE `ab_users_pub` CHANGE `pID` `pID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `ab_users_pub` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;

ALTER TABLE `ab_users_settings` DROP `page`;
ALTER TABLE `ab_users_settings` ADD `last_activity` DATETIME NULL DEFAULT NULL;
# ----------------- #

ALTER TABLE `global_users_company` ADD `ab_permissions` TEXT NULL DEFAULT NULL AFTER `ab`;

# ----------------- #


update global_users SET email = concat(email,@usernamePrefix);

# ----------------- #
UPDATE global_users_company SET ab_permissions = 'a:9:{s:7:"details";a:2:{s:7:"actions";a:5:{s:5:"check";s:1:"1";s:8:"material";s:1:"1";s:6:"repeat";s:1:"1";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";}s:6:"fields";a:3:{s:4:"rate";s:1:"1";s:9:"totalCost";s:1:"1";s:13:"totalShouldbe";s:1:"1";}}s:5:"lists";a:2:{s:6:"fields";a:3:{s:4:"rate";s:1:"1";s:9:"totalCost";s:1:"1";s:13:"totalShouldbe";s:1:"1";}s:6:"totals";a:1:{s:9:"totalCost";s:1:"1";}}s:4:"form";a:1:{s:4:"page";s:1:"1";}s:11:"provisional";a:1:{s:4:"page";s:1:"1";}s:10:"production";a:1:{s:4:"page";s:1:"1";}s:6:"layout";a:3:{s:4:"page";s:1:"1";s:9:"pagecount";s:1:"1";s:8:"editpage";s:1:"1";}s:8:"overview";a:1:{s:4:"page";s:1:"1";}s:7:"records";a:2:{s:7:"deleted";a:1:{s:4:"page";s:1:"1";}s:6:"search";a:1:{s:4:"page";s:1:"1";}}s:14:"administration";a:2:{s:11:"application";a:7:{s:8:"accounts";a:2:{s:4:"page";s:1:"1";s:6:"status";a:1:{s:4:"page";s:1:"1";}}s:10:"categories";a:1:{s:4:"page";s:1:"1";}s:9:"marketers";a:2:{s:4:"page";s:1:"1";s:7:"targets";a:1:{s:4:"page";s:1:"1";}}s:10:"production";a:1:{s:4:"page";s:1:"1";}s:8:"sections";a:1:{s:4:"page";s:1:"1";}s:7:"placing";a:2:{s:4:"page";s:1:"1";s:7:"colours";a:1:{s:4:"page";s:1:"1";}}s:7:"loading";a:1:{s:4:"page";s:1:"1";}}s:6:"system";a:3:{s:5:"dates";a:1:{s:4:"page";s:1:"1";}s:5:"users";a:1:{s:4:"page";s:1:"1";}s:12:"publications";a:1:{s:4:"page";s:1:"1";}}}}' WHERE  uID = (SELECT ID FROM global_users WHERE email = concat('administrator',@usernamePrefix) LIMIT 0,1);

ALTER TABLE `ab_bookings` ADD `accountID` INT( 6 ) NULL DEFAULT NULL AFTER `accNum`;

UPDATE ab_bookings SET accountID = (SELECT ID FROM `ab_accounts` WHERE ab_accounts.accNum = ab_bookings.accNum LIMIT 0,1 );
ALTER TABLE ab_bookings DROP INDEX accNum;
ALTER TABLE `ab_bookings` ADD INDEX ( `accountID` );

# ----------------- #

ALTER TABLE `global_users_company` ADD `ab_marketerID` INT( 6 ) NULL ,
ADD INDEX ( `ab_marketerID` );

ALTER TABLE `global_users_company` ADD `ab_productionID` INT( 6 ) NULL ,
ADD INDEX ( `ab_productionID` );



ALTER TABLE `global_dates` ADD INDEX ( `publish_date` );

# ----------------- #
ALTER TABLE `ab_bookings` CHANGE `material_file` `material_file_filename` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `ab_bookings` ADD `material_file_filesize` INT( 6 ) NULL DEFAULT '0' AFTER `material_file_filename`;
ALTER TABLE `ab_bookings` ADD `material_file_store` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `material_file_filesize`;

# ----------------- #

ALTER TABLE `ab_bookings` ADD `invoiceNum` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `accountID`;

# ----------------- #


ALTER TABLE `ab_marketers_targets` ADD `date_from` DATE NULL DEFAULT NULL AFTER `mID`;
ALTER TABLE `ab_marketers_targets` ADD `date_to` DATE NULL DEFAULT NULL AFTER `date_from`;
ALTER TABLE `ab_marketers_targets` DROP `pID`;
ALTER TABLE `ab_marketers_targets` DROP `monthin`;
ALTER TABLE `ab_marketers_targets` DROP `yearin`;

CREATE TABLE IF NOT EXISTS `ab_marketers_targets_pub` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`mtID` int(6) DEFAULT NULL,
	`pID` int(6) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `mtID` (`mtID`,`pID`)
);
# ----------------- #
CREATE TABLE IF NOT EXISTS `ab_inserts_types` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`pID` int(6) DEFAULT NULL,
	`insertsLabel` varchar(50) DEFAULT NULL,
	`orderby` smallint(6) DEFAULT NULL,
	`rate` decimal(8,2) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `pID` (`pID`)
);

# ----------------- #

ALTER TABLE `ab_bookings` ADD `insertLabel` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `InsertPO` ,
ADD `insertTypeID` INT( 6 ) NULL DEFAULT NULL AFTER `insertLabel` ,
ADD INDEX ( `insertTypeID` );

CREATE TABLE IF NOT EXISTS `system` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`system` varchar(100) DEFAULT NULL,
	`value` varchar(100) DEFAULT NULL,
	PRIMARY KEY (`ID`)
);
# ----------------- #

ALTER TABLE `ab_marketers` ADD `code` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `email`;

# ----------------- #

