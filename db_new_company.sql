SET @companyName:="Test Company";

SET @user_name:="Test User";
SET @user_email:="test@zoutnet.co.za";
SET @user_password:="test";

SET @publication:="test publication";
SET @publication_columnsav:="8";
SET @publication_cmav:="39";
SET @publication_pagewidth:="263";

INSERT INTO `global_companies` (`company`) VALUES (@companyName);
SET @cID = LAST_INSERT_ID();

INSERT INTO	`global_users` (fullName,email,password) VALUES (@user_name, @user_email, md5(concat('aws_',@user_password,'_',md5('zoutnet'))));
SET @uID = LAST_INSERT_ID();

INSERT INTO	`global_publications` (cID, publication, columnsav, cmav, pagewidth) VALUES (@cID, @publication, @publication_columnsav, @publication_cmav, @publication_pagewidth);
SET @pID = LAST_INSERT_ID();

INSERT INTO	global_users_company (cID,uID, ab) VALUES (@cID,@uID,'1');
INSERT INTO ab_users_pub (pID,uID) VALUES (@pID,@uID);

INSERT INTO	`global_dates` (`pID` ,	`publish_date`)	VALUES (@pID, current_date());


UPDATE global_users_company SET ab_permissions = 'a:9:{s:7:"details";a:2:{s:7:"actions";a:4:{s:5:"check";s:1:"1";s:8:"material";s:1:"1";s:6:"repeat";s:1:"1";s:7:"invoice";s:1:"1";}s:6:"fields";a:3:{s:4:"rate";s:1:"1";s:9:"totalCost";s:1:"1";s:13:"totalShouldbe";s:1:"1";}}s:5:"lists";a:2:{s:6:"fields";a:3:{s:4:"rate";s:1:"1";s:9:"totalCost";s:1:"1";s:13:"totalShouldbe";s:1:"1";}s:6:"totals";a:1:{s:9:"totalCost";s:1:"1";}}s:4:"form";a:4:{s:3:"new";s:1:"1";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:11:"edit_master";s:1:"1";}s:10:"production";a:1:{s:4:"page";s:1:"1";}s:6:"layout";a:3:{s:4:"page";s:1:"1";s:9:"pagecount";s:1:"1";s:8:"editpage";s:1:"1";}s:8:"overview";a:1:{s:4:"page";s:1:"1";}s:7:"records";a:2:{s:7:"deleted";a:1:{s:4:"page";s:1:"1";}s:6:"search";a:1:{s:4:"page";s:1:"1";}}s:7:"reports";a:5:{s:7:"account";a:2:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}}s:8:"marketer";a:3:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}s:7:"targets";a:1:{s:4:"page";s:1:"1";}}s:10:"production";a:1:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}}s:8:"category";a:2:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}}s:11:"publication";a:4:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}s:7:"placing";a:1:{s:4:"page";s:1:"1";}s:7:"section";a:1:{s:4:"page";s:1:"1";}}}s:14:"administration";a:2:{s:11:"application";a:8:{s:8:"accounts";a:2:{s:4:"page";s:1:"1";s:6:"status";a:1:{s:4:"page";s:1:"1";}}s:10:"categories";a:1:{s:4:"page";s:1:"1";}s:9:"marketers";a:2:{s:4:"page";s:1:"1";s:7:"targets";a:1:{s:4:"page";s:1:"1";}}s:10:"production";a:1:{s:4:"page";s:1:"1";}s:8:"sections";a:1:{s:4:"page";s:1:"1";}s:7:"placing";a:2:{s:4:"page";s:1:"1";s:7:"colours";a:1:{s:4:"page";s:1:"1";}}s:7:"loading";a:1:{s:4:"page";s:1:"1";}s:13:"inserts_types";a:1:{s:4:"page";s:1:"1";}}s:6:"system";a:3:{s:5:"dates";a:1:{s:4:"page";s:1:"1";}s:5:"users";a:1:{s:4:"page";s:1:"1";}s:12:"publications";a:1:{s:4:"page";s:1:"1";}}}}' WHERE uID = @uID;



# clear the db #

TRUNCATE TABLE ab_accounts;
TRUNCATE TABLE ab_accounts_pub;
TRUNCATE TABLE ab_accounts_status;
TRUNCATE TABLE ab_advert_sizes;
TRUNCATE TABLE ab_bookings;
TRUNCATE TABLE ab_bookings_logs;
TRUNCATE TABLE ab_categories;
TRUNCATE TABLE ab_category_pub;
TRUNCATE TABLE ab_placing_sub;
TRUNCATE TABLE ab_inserts_types;
TRUNCATE TABLE ab_marketers;
TRUNCATE TABLE ab_marketers_pub;
TRUNCATE TABLE ab_marketers_targets;
TRUNCATE TABLE ab_marketers_targets_pub;
TRUNCATE TABLE ab_page_load;
TRUNCATE TABLE ab_placing;
TRUNCATE TABLE ab_production;
TRUNCATE TABLE ab_production_pub;
TRUNCATE TABLE ab_users_pub;
TRUNCATE TABLE ab_users_settings;
TRUNCATE TABLE global_companies;
TRUNCATE TABLE global_dates;
TRUNCATE TABLE global_pages;
TRUNCATE TABLE global_pages_sections;
TRUNCATE TABLE global_publications;
TRUNCATE TABLE global_users;
TRUNCATE TABLE global_users_company;

