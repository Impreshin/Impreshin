

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

/* ------------------------------------------------------------ */
/*          Run the revision script /nf/import12345 now         */
/* ------------------------------------------------------------ */


ALTER TABLE `nf_articles` DROP `synopsis` ,
DROP `article` ,
DROP `article_orig` ;

ALTER TABLE `nf_articles` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

