SET @companyID:="1";

ALTER DATABASE apps DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


DROP TABLE nf_revisions;
DROP TABLE nf_sources;
DROP TABLE nf_permissions;
DROP TABLE nf_errors;


ALTER TABLE `nf_categories` CHANGE `category` `category` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

RENAME TABLE `nf_logs` TO `nf_article_logs`;
ALTER TABLE `nf_article_logs` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `nf_article_logs` CHANGE `reason` `reason` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `nf_article_logs` CHANGE `aID` `aID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_logs` CHANGE `uID` `uID` INT( 5 ) NULL DEFAULT NULL;

ALTER TABLE `nf_article_newsbook` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nf_article_newsbook` CHANGE `aID` `aID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `nID` `nID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `ndID` `ndID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `placedBy` `placedBy` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE nf_article_newsbook DROP INDEX aID_2;
ALTER TABLE nf_article_newsbook DROP INDEX aID;

ALTER TABLE `nf_article_newsbook` CHANGE `nID` `pID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `ndID` `dID` INT( 6 ) NULL DEFAULT NULL;


ALTER TABLE `nf_comments` CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `nf_comments` CHANGE `aID` `aID` INT( 6 ) NULL;
ALTER TABLE `nf_comments` CHANGE `uID` `uID` INT( 6 ) NULL;


UPDATE `nf_articles` SET heading = replace(heading,'´','\''), synopsis = replace(synopsis,'´','\''), article = replace(article,'´','\''), article_orig = replace(article_orig,'´','\'');
UPDATE `nf_articles` SET heading = replace(heading,'&acute;','\''), synopsis = replace(synopsis,'&acute;','\''), article =
	replace(article,'&acute;','\''), article_orig = replace(article_orig,'&acute;','\'');

ALTER TABLE `nf_articles` CHANGE `heading` `heading` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `nf_articles` CHANGE `authorID` `authorID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_articles` CHANGE `cID` `cID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_articles` ADD INDEX ( `authorID` );
ALTER TABLE `nf_articles` ADD `percent` DECIMAL( 5, 2 ) NULL DEFAULT NULL AFTER `words`;

/*
CREATE TABLE IF NOT EXISTS `nf_articles_revisions` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`aID` int(6) DEFAULT NULL,
	`uID` int(6) DEFAULT NULL,
	`remark` varchar(100) DEFAULT NULL,
	`synopsis` text,
	`article` text,
	`patch` text,
	`percent` decimal(5,2) DEFAULT NULL,
	`filesID` varchar(250) DEFAULT NULL,
	`datein` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`),
	KEY `aID` (`aID`),
	KEY `uID` (`uID`)
);
*/
CREATE TABLE IF NOT EXISTS `nf_articles_revisions` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`aID` int(6) DEFAULT NULL,
	`uID` int(6) DEFAULT NULL,
	`patch` text,
	`datein` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`),
	KEY `aID` (`aID`,`uID`)
);
ALTER TABLE `nf_files` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `nf_articles` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `nf_article_logs` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE nf_article_logs DROP INDEX aID;
ALTER TABLE `nf_article_logs` ADD INDEX ( `aID` );

ALTER TABLE `nf_article_logs` CHANGE `uID` `userID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_logs` ADD INDEX ( `userID` );

ALTER TABLE `nf_article_logs` CHANGE `reason` `label` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `nf_article_logs` MODIFY `userID` INT( 6 ) NULL DEFAULT NULL AFTER `label`;

ALTER TABLE `nf_article_logs` MODIFY `datein` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `label`;
ALTER TABLE `nf_article_logs` ADD `log` TEXT NULL AFTER `aID`;
ALTER TABLE `nf_article_logs` CHANGE `datein` `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;

RENAME TABLE nf_article_logs TO nf_articles_logs;
RENAME TABLE nf_articles_revisions TO nf_articles_edits;


ALTER TABLE `nf_articles_edits` ADD `stage` TINYINT( 1 ) NULL DEFAULT NULL;

ALTER TABLE `nf_articles` CHANGE `status` `stage` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `nf_articles` DROP INDEX `status` , ADD INDEX `stage` ( `stage` );

ALTER TABLE `nf_files` ADD `uID` INT( 6 ) NOT NULL ,
ADD INDEX ( `uID` );

ALTER TABLE `nf_files` CHANGE `filename` `filename` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `nf_files` CHANGE `caption` `caption` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE nf_files SET uID = (SELECT authorID FROM nf_articles WHERE nf_articles.ID = nf_files.aID);
CREATE TABLE IF NOT EXISTS `nf_articles_files_link` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`articleID` int(6) DEFAULT NULL,
	`fileID` int(6) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `articleID` (`articleID`,`fileID`)
);


/* ----------------------------------------------------------------------------------------------------------------*/
/* User ID updating */
ALTER TABLE `_global_access` ADD `newID` INT( 6 ) NULL DEFAULT NULL AFTER `ID`;
ALTER TABLE `nf_articles` ADD `new_authorID` INT( 6 ) NULL DEFAULT NULL AFTER `authorID`;
ALTER TABLE `nf_articles` ADD `new_rejecteduID` INT( 6 ) NULL DEFAULT NULL AFTER `rejecteduID`;
ALTER TABLE `nf_articles` ADD `new_lockedBy` INT( 6 ) NULL DEFAULT NULL AFTER `lockedBy`;

ALTER TABLE `nf_article_newsbook` ADD `new_uID` INT( 6 ) NULL DEFAULT NULL AFTER `uID`;
ALTER TABLE `nf_article_newsbook` ADD `new_placedBy` INT( 6 ) NULL DEFAULT NULL AFTER `placedBy`;

ALTER TABLE `nf_comments` ADD `new_uID` INT( 6 ) NULL DEFAULT NULL AFTER `uID`;

ALTER TABLE `nf_files` ADD `new_uID` INT( 6 ) NULL DEFAULT NULL AFTER `uID`;
ALTER TABLE `nf_read_comment` ADD `new_uID` INT( 6 ) NULL DEFAULT NULL AFTER `uID`;

/* publication ID */
ALTER TABLE `_global_publications` ADD `newID` INT( 6 ) NULL AFTER `ID`;
ALTER TABLE `nf_article_newsbook` ADD `new_pID` INT( 6 ) NULL DEFAULT NULL AFTER `pID`;
ALTER TABLE `_global_datelist` ADD `new_pID` INT( 6 ) NULL AFTER `pID`;


/* ----------------------------------------------------------------------------------------------------------------*/
/* RENAME TABLE nf_article_newsbook TO nf_articles_newsbooks;

ALTER TABLE `nf_newsbooks` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
*/

/* ------------------------------------------------------------ */
/*          Run the revision script /nf/import12345 now         */
/* ------------------------------------------------------------ */
/* ------------------------------------------------------------ */
/*          Run the revision script /nf/data12345 now         */
/* ------------------------------------------------------------ */

UPDATE nf_articles SET authorID = new_authorID, lockedBy = new_lockedBy, rejecteduID = new_rejecteduID;
UPDATE nf_article_newsbook SET uID = new_uID, placedBy = new_placedBy, pID = new_pID;
UPDATE _global_datelist SET pID = new_pID;




ALTER TABLE `nf_articles` ADD `deleted` TINYINT( 1 ) NULL DEFAULT NULL ,
ADD `deleted_userID` INT( 6 ) NULL DEFAULT NULL ,
ADD `deleted_date` DATETIME NULL DEFAULT NULL ,
ADD `deleted_reason` TEXT NULL DEFAULT NULL ,
ADD INDEX ( `deleted` , `deleted_userID` );

ALTER TABLE `nf_articles` CHANGE `cID` `categoryID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE nf_articles DROP INDEX cID;
ALTER TABLE `nf_articles` ADD INDEX ( `categoryID` );
ALTER TABLE `nf_articles` ADD `cID` INT( 6 ) NULL DEFAULT NULL AFTER `ID` ,
ADD INDEX ( `cID` );

UPDATE `nf_articles` SET `cID` =@companyID;
ALTER TABLE `nf_articles` CHANGE `rejecteduID` `rejected_userID` INT( 6 ) NULL DEFAULT NULL;




SELECT apps._global_datelist.*, (SELECT publish_date FROM adbooker_v5.global_dates WHERE adbooker_v5.global_dates.pID = apps._global_datelist.new_pID ORDER BY  ABS( apps._global_datelist.DateIN - adbooker_v5.global_dates.publish_date ) LIMIT 0,1 ) as impreshin_date FROM apps._global_datelist



RENAME TABLE `apps`.`nf_articles` TO `adbooker_v5`.`nf_articles` ;
RENAME TABLE `apps`.`nf_articles_edits` TO `adbooker_v5`.`nf_articles_edits` ;
RENAME TABLE `apps`.`nf_articles_logs` TO `adbooker_v5`.`nf_articles_logs` ;
RENAME TABLE `apps`.`nf_articles_files_link` TO `adbooker_v5`.`nf_articles_files_link` ;
RENAME TABLE `apps`.`nf_files` TO `adbooker_v5`.`nf_files` ;

