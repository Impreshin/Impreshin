

UPDATE `_global_publications` SET `publication` = 'Limpopo Mirror' WHERE `_global_publications`.`ID` = 2;

SET @companyID := "1";


CREATE TABLE IF NOT EXISTS `nf_articles_body` (`ID` INT(6) NOT NULL AUTO_INCREMENT, `aID` INT(6) DEFAULT NULL, `uID` INT(6) DEFAULT NULL, `body` TEXT, `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`ID`), KEY `aID` (`aID`, `uID`));

INSERT INTO nf_articles_body (aID,uID,body) SELECT ID, authorID, article FROM nf_articles WHERE articleType = 'article' AND article is not null;


ALTER TABLE `nf_articles` DROP `article`, DROP `article_orig`;
ALTER TABLE `nf_articles_body` ADD `stageID` INT(6) NULL DEFAULT NULL;




ALTER TABLE `nf_articles` CHANGE `cID` `catID` INT( 5 ) NULL DEFAULT NULL;


ALTER TABLE `nf_articles` ADD `cID` INT(6) NULL DEFAULT NULL AFTER `ID`, ADD INDEX (`cID`);
UPDATE nf_articles SET cID =  @companyID;

ALTER TABLE `nf_categories` ADD `cID` INT(6) NULL DEFAULT NULL AFTER `ID`, ADD INDEX (`cID`);
UPDATE nf_categories SET cID =  @companyID;




DROP TABLE nf_revisions;
DROP TABLE nf_sources;
DROP TABLE nf_permissions;
DROP TABLE nf_errors;



# Run /old_to_new/nf.php

# Run /fix_db_charset.php




SET @companyID := "1";



ALTER TABLE `nf_article_newsbook` CHANGE `ID` `ID` INT( 6 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nf_article_newsbook` CHANGE `placedBy` `placedBy` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `plannedPage` `plannedPage` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` CHANGE `placedPage` `placedPage` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE nf_article_newsbook DROP INDEX aID_2;
ALTER TABLE nf_article_newsbook DROP INDEX aID;
ALTER TABLE `nf_article_newsbook` CHANGE `articleID` `aID` INT(6) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook` ADD INDEX (`aID`);
ALTER TABLE `nf_article_newsbook` ADD INDEX (`pID`);
ALTER TABLE `nf_article_newsbook` ADD INDEX (`dID`);
ALTER TABLE `nf_article_newsbook` ADD INDEX (`uID`);


# Run /old_to_new/nf_pages.php




ALTER TABLE `nf_article_newsbook_photos` CHANGE `nID` `nID` INT(6) NULL DEFAULT NULL;
ALTER TABLE `nf_article_newsbook_photos` CHANGE `photoID` `fileID` INT(6) NULL DEFAULT NULL;

ALTER TABLE `nf_categories` CHANGE `ID` `ID` INT(6) NOT NULL AUTO_INCREMENT;




ALTER TABLE `nf_comments` CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `nf_comments` CHANGE `aID` `aID` INT( 6 ) NULL;
ALTER TABLE `nf_comments` CHANGE `uID` `uID` INT( 6 ) NULL;
ALTER TABLE `nf_comments` ADD INDEX (`uID`);


ALTER TABLE `nf_files` CHANGE `ID` `ID` INT(6) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nf_files` CHANGE `aID` `aID` INT(6) NULL DEFAULT NULL;
ALTER TABLE `nf_files` ADD `filename_orig` VARCHAR(250) NULL DEFAULT NULL AFTER `filename`;
UPDATE nf_files SET filename_orig = filename;
ALTER TABLE `nf_files` ADD INDEX (`aID`);










UPDATE `nf_articles` SET heading = replace(heading,'´','\''), synopsis = replace(synopsis,'´','\'');
UPDATE `nf_articles_body` SET body = replace(body,'´','\'');
UPDATE `nf_articles` SET heading = replace(heading,'&acute;','\''), synopsis = replace(synopsis,'&acute;','\'');
UPDATE `nf_articles_body` SET body = replace(body,'&acute;','\'');
UPDATE `nf_articles_body` SET body = replace(body,'&nbsp;',' ');
UPDATE `nf_articles_body` SET body = replace(body,'&quot;','"');
UPDATE `nf_articles_body` SET body = replace(body,'&#39;','\'');


ALTER TABLE `nf_articles` ADD `percent_orig` DECIMAL( 5, 2 ) NULL DEFAULT NULL AFTER `words`;
ALTER TABLE `nf_articles` ADD `percent_last` DECIMAL( 5, 2 ) NULL DEFAULT NULL AFTER `words`;





ALTER TABLE `nf_files` ADD `uID` INT( 6 ) NOT NULL ,
ADD INDEX ( `uID` );



UPDATE nf_files SET uID = (SELECT authorID FROM nf_articles WHERE nf_articles.ID = nf_files.aID);




ALTER TABLE `nf_read_comment` ADD `ID` INT( 6 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;




ALTER TABLE `nf_articles` ADD `deleted` TINYINT( 1 ) NULL DEFAULT NULL , ADD `deleted_userID` INT( 6 ) NULL DEFAULT NULL , ADD `deleted_date` DATETIME NULL DEFAULT NULL , ADD `deleted_reason` TEXT NULL DEFAULT NULL , ADD INDEX ( `deleted` , `deleted_userID` );

ALTER TABLE `nf_articles` CHANGE `rejecteduID` `rejected_uID` INT( 6 ) NULL DEFAULT NULL;




RENAME TABLE `nf_read_comment` TO `nf_comments_read` ;

ALTER TABLE `nf_comments_read` CHANGE `commentID` `commentID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_comments_read` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;


ALTER TABLE nf_comments_read DROP INDEX commentID;

ALTER TABLE `nf_comments_read` ADD INDEX ( `commentID` );
ALTER TABLE `nf_comments_read` ADD INDEX ( `uID` );



ALTER TABLE `nf_articles` CHANGE `heading` `title` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


CREATE TABLE IF NOT EXISTS `nf_article_types` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`type` varchar(50) DEFAULT NULL,
	`orderby` tinyint(2) DEFAULT NULL,
	PRIMARY KEY (`ID`)
);

--
-- Dumping data for table `nf_article_types`
--
ALTER TABLE `nf_articles` ADD `typeID` INT( 6 ) NULL DEFAULT NULL AFTER `categoryID` ,ADD INDEX ( `typeID` );
INSERT INTO	`nf_article_types` (`ID`, `type`, `orderby`) VALUES (1, 'Article', 1), (2, 'Photo', 2);
UPDATE nf_articles SET typeID = '1' where articleType = 'article';
UPDATE nf_articles SET typeID = '2' where articleType = 'photo';



ALTER TABLE `nf_articles` CHANGE `status` `stageID` INT( 6 ) NULL DEFAULT NULL;

ALTER TABLE `nf_files` ADD `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `nf_files` CHANGE `uID` `uID` INT( 6 ) NULL DEFAULT NULL;
UPDATE `nf_files` SET `datein`=(SELECT datein FROM nf_articles WHERE nf_articles.ID = nf_files.aID) WHERE 1;

CREATE TABLE IF NOT EXISTS `nf_stages` (`ID` INT(6) NOT NULL AUTO_INCREMENT, `cID` INT(6) DEFAULT NULL, `stage` VARCHAR(100) DEFAULT NULL, `orderby` INT(6) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `cID` (`cID`));


ALTER TABLE `nf_articles` ADD `stageID_new` INT(6) NULL DEFAULT NULL
AFTER `stageID`;
INSERT INTO `nf_stages` (`ID`, `cID`, `stage`, `orderby`) VALUES (1, 0, 'Draft', 0), (2, @companyID, 'Ready',9999), (3, @companyID, 'Posted', 1), (4, @companyID, 'Proof', 2);


UPDATE nf_articles SET stageID_new = '1' WHERE stageID = '0';
UPDATE nf_articles SET stageID_new = '3' WHERE stageID = '1' OR stageID = '2';
UPDATE nf_articles SET stageID_new = '4' WHERE stageID = '3' OR stageID = '4';
UPDATE nf_articles SET stageID_new = '2' WHERE stageID = '5';

ALTER TABLE `nf_articles` DROP `stageID`;
ALTER TABLE `nf_articles` CHANGE `stageID_new` `stageID` INT(6) NULL DEFAULT NULL;
ALTER TABLE `nf_articles` ADD INDEX (`stageID`);


ALTER TABLE `nf_articles_body` ADD `words` INT(6) NULL DEFAULT NULL AFTER `datein`;
ALTER TABLE `nf_articles_body` ADD `percent_orig` DECIMAL(5, 2) NULL DEFAULT NULL AFTER `words`;
ALTER TABLE `nf_articles_body` ADD `percent_last` DECIMAL(5, 2) NULL DEFAULT NULL AFTER `words`;

ALTER TABLE `nf_stages` ADD `labelClass` VARCHAR(50) NULL DEFAULT NULL AFTER `orderby`;

ALTER TABLE `nf_article_newsbook` ADD `pageID` INT(6) NULL DEFAULT NULL
AFTER `placedPage`, ADD INDEX (`pageID`);

ALTER TABLE `nf_articles` ADD INDEX (`authorID`) ;
ALTER TABLE `nf_articles` DROP `synopsis`, DROP `reference`, DROP `declare`;
ALTER TABLE `nf_articles` CHANGE `ID` `ID` INT(6) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nf_categories` CHANGE `category` `category` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `nf_article_types` ADD `labelClass` VARCHAR(30) NULL DEFAULT NULL ;
ALTER TABLE `nf_stages` CHANGE `labelClass` `labelClass` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `nf_articles` CHANGE `stars` `priority` TINYINT(1) NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `nf_checklists` (`ID` INT(6) NOT NULL AUTO_INCREMENT, `cID` INT(6) DEFAULT NULL, `label` VARCHAR(100) DEFAULT NULL, `orderby` INT(3) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `cID` (`cID`));

ALTER TABLE `nf_checklists` ADD `categoryID` INT( 6 ) NULL DEFAULT NULL AFTER `cID`, ADD INDEX ( `categoryID` ) ;

ALTER TABLE `nf_checklists` ADD `description` TEXT NULL DEFAULT NULL AFTER `label`;

ALTER TABLE `nf_articles` ADD `checklist` VARCHAR(250) NULL DEFAULT NULL AFTER `locked_uID` ;

ALTER TABLE `global_companies` ADD `nf_cm_css` TEXT NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `nf_articles_logs` (`ID` INT(6) NOT NULL AUTO_INCREMENT, `aID` INT(6) DEFAULT NULL, `log` TEXT, `label` VARCHAR(100) DEFAULT NULL, `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, `userID` INT(6) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `userID` (`userID`), KEY `datein` (`datein`), KEY `aID` (`aID`));

ALTER TABLE `nf_files` ADD INDEX (`type`);

ALTER TABLE `nf_article_types` CHANGE `labelClass` `icon` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `nf_comments` ADD `parentID` INT(6) NULL DEFAULT NULL AFTER `uID`, ADD INDEX (`parentID`);

CREATE TABLE IF NOT EXISTS `nf_priorities` ( `ID` int(6) NOT NULL AUTO_INCREMENT, `cID` int(6) DEFAULT NULL,	`priority` varchar(30) DEFAULT NULL, `orderby` int(3) DEFAULT NULL,	PRIMARY KEY (`ID`), KEY `cID` (`cID`));


INSERT INTO `nf_priorities` (`ID`, `cID`, `priority`, `orderby`) VALUES (1, 1, 'Low', 1), (2, 1, '1', 2),(3, 1, '2', 3),(4, 1, '3', 4),(5, 1, '4', 5),(6, 1, 'high', 6);

UPDATE nf_articles SET priority = '6' WHERE priority ='5';
UPDATE nf_articles SET priority = '5' WHERE priority ='4';
UPDATE nf_articles SET priority = '4' WHERE priority ='3';
UPDATE nf_articles SET priority = '3' WHERE priority ='2';
UPDATE nf_articles SET priority = '2' WHERE priority ='1';
UPDATE nf_articles SET priority = '1' WHERE priority ='0';

ALTER TABLE `nf_articles` CHANGE `priority` `priorityID` INT( 6 ) NULL DEFAULT NULL;
ALTER TABLE `nf_articles` ADD INDEX ( `priorityID` );

ALTER TABLE `nf_articles` ADD `dateChanged` TIMESTAMP NULL DEFAULT NULL AFTER `datein`;

ALTER TABLE `nf_article_newsbook` ADD `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `uID`;
ALTER TABLE `nf_articles` ADD `meta` TINYTEXT NULL DEFAULT NULL AFTER `checklist`;
ALTER TABLE `nf_article_newsbook_photos` DROP `ID`;
ALTER TABLE `nf_article_newsbook_photos` ADD INDEX ( `fileID` );
ALTER TABLE `nf_article_newsbook_photos` DROP `planned`;

#------

ALTER TABLE `nf_files` ADD `folder` VARCHAR( 40 ) NULL DEFAULT NULL AFTER `aID`;
UPDATE nf_files SET folder = CONCAT(@companyID,'/old');
#------

ALTER TABLE `nf_articles` ADD `rejected_reason` TEXT NULL DEFAULT NULL AFTER `rejected_uID`;

#-----

ALTER TABLE `nf_articles` CHANGE `archivedDate` `archived_date` DATETIME NULL DEFAULT NULL;

#-----

CREATE TABLE IF NOT EXISTS `nf_resources` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`cID` int(6) DEFAULT NULL,
	`type` tinyint(1) DEFAULT NULL,
	`label` varchar(100) DEFAULT NULL,
	`path` varchar(200) DEFAULT NULL,
	`filename` varchar(30) DEFAULT NULL,
	`orderby` int(4) DEFAULT NULL,
	PRIMARY KEY (`ID`),
	KEY `cID` (`cID`),
	KEY `type`(`type`)
);
	
	#---

#DROP TABLE nf_revisions;

CREATE TABLE IF NOT EXISTS `nf_files_body` (
	`ID` int(6) NOT NULL AUTO_INCREMENT,
	`fileID` int(6) DEFAULT NULL,
	`uID` int(6) DEFAULT NULL,
	`body` text,
	`datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`),
	KEY `fileID` (`fileID`),
	KEY `uID` (`uID`)
);
INSERT INTO nf_files_body (fileID,uID,body) SELECT ID, uID, caption FROM nf_files WHERE caption is not null;
ALTER TABLE `nf_files` DROP `caption`;

ALTER TABLE `nf_categories` ADD `nf_cm_css` TEXT NULL DEFAULT NULL AFTER `category` ;
 