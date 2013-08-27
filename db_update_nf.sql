

UPDATE `impreshin`.`_global_publications` SET `publication` = 'Limpopo Mirror' WHERE `_global_publications`.`ID` = 2;

SET @companyID := "1";
#ALTER TABLE `nf_articles` ADD `article_orig2` TEXT NULL DEFAULT NULL AFTER `article`;

#UPDATE nf_articles SET article_orig2 = CONVERT(article_orig USING utf8);
#ALTER TABLE `nf_articles` DROP `article_orig`;
#ALTER TABLE `nf_articles` CHANGE `article_orig2` `article_orig` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `nf_articles_body` (`ID` INT(6) NOT NULL AUTO_INCREMENT, `aID` INT(6) DEFAULT NULL, `uID` INT(6) DEFAULT NULL, `body` TEXT, `datein` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`ID`), KEY `aID` (`aID`, `uID`));

INSERT INTO nf_articles_body (aID,uID,body) SELECT ID, authorID, COALESCE(article_orig,article) FROM nf_articles WHERE articleType = 'article' AND COALESCE(article_orig,article) is not null;
INSERT INTO nf_articles_body (aID,uID,body) SELECT ID, locked_uID,article FROM nf_articles WHERE articleType = 'article' AND article_orig is not null;

ALTER TABLE `nf_articles` DROP `article`, DROP `article_orig`;

ALTER TABLE `nf_articles_body` ADD `stageID` INT(6) NULL DEFAULT NULL;


## SELECT ID, (SELECT DateIN FROM _global_datelist WHERE ID = nf_article_newsbook.dID_old LIMIT 0,1) as orig, (SELECT publish_date FROM global_dates WHERE ID = nf_article_newsbook.dID  LIMIT 0,1) as latest FROM  nf_article_newsbook ORDER BY ID DESC;



ALTER TABLE `nf_articles` ADD `cID` INT(6) NULL DEFAULT NULL AFTER `ID`, ADD INDEX (`cID`);
UPDATE nf_articles SET cID =  @companyID;

ALTER TABLE `nf_categories` ADD `cID` INT(6) NULL DEFAULT NULL AFTER `ID`, ADD INDEX (`cID`);
UPDATE nf_categories SET cID =  @companyID;




DROP TABLE nf_revisions;
DROP TABLE nf_sources;
DROP TABLE nf_permissions;
DROP TABLE nf_errors;



# Run /fix_db_charset.php now








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

INSERT INTO `nf_stages` (`ID`, `cID`, `stage`, `orderby`) VALUES (1, @companyID, 'Posted', 1), (2, @companyID, 'Proof', 2), (3, @companyID, 'Ready', 3);

ALTER TABLE `nf_articles_body` ADD `words` INT(6) NULL DEFAULT NULL
AFTER `datein`;
ALTER TABLE `nf_articles_body` ADD `percent_orig` DECIMAL(5, 2) NULL DEFAULT NULL
AFTER `words`;
ALTER TABLE `nf_articles_body` ADD `percent_last` DECIMAL(5, 2) NULL DEFAULT NULL
AFTER `words`;