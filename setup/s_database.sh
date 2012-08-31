#!/bin/bash

WIZARD=$1
function startfn {



	php_output=`php /media/data/web/setup/cfg.php`
	IFS=":"
	while read -r key val; do
	    eval ${key}="${val}"
	done <<< "$php_output"

	echo ""
	echo "New Company: "
	echo "------------"
    read -e -p "Company: " -i "" CHANGE_COMPANY

    echo ""
    echo "Admin User"
    echo "------------"
    read -e -p "Full Name: " -i "" CHANGE_NAME
    read -e -p "Email: " -i "" CHANGE_EMAIL
    read -e -p "Password: " -i "" CHANGE_PASSWORD

    echo ""
    echo "Publication"
    echo "------------"
    read -e -p "NAME: " -i "" CHANGE_publication
    read -e -p " Page Columns:       " -i "8" CHANGE_columnsav
    read -e -p " Page Cm:            " -i "39" CHANGE_cmav
    read -e -p " Page Width (in mm): " -i "263" CHANGE_pagewidth


	 echo ""
    echo "Date"
    echo "------------"
    read -e -p "NAME: " -i "$(date +%Y-%m-%d)" CHANGE_date

	echo ""
	echo ""


	read -p "Press [Enter] key to write these values to the db..."
	cleardb
	doNewCompany
}
function doNewCompany {


mysql -h $db_host -u $db_username -p$db_password $db_database<<EOFMYSQL

SET @companyName:="$CHANGE_COMPANY";

SET @user_name:="$CHANGE_NAME";
SET @user_email:="$CHANGE_EMAIL";
SET @user_password:="$CHANGE_PASSWORD";

SET @publication:="$CHANGE_publication";
SET @publication_columnsav:="$CHANGE_columnsav";
SET @publication_cmav:="$CHANGE_cmav";
SET @publication_pagewidth:="$CHANGE_pagewidth";


SET @publishDate:="$CHANGE_date";

INSERT INTO global_companies (company) VALUES (@companyName);
SET @cID = LAST_INSERT_ID();

INSERT INTO	global_users (fullName,email,password) VALUES (@user_name, @user_email, md5(concat('aws_',@user_password,'_',md5('zoutnet'))));
SET @uID = LAST_INSERT_ID();

INSERT INTO	global_publications (cID, publication, columnsav, cmav, pagewidth) VALUES (@cID, @publication, @publication_columnsav, @publication_cmav, @publication_pagewidth);
SET @pID = LAST_INSERT_ID();

INSERT INTO	global_users_company (cID,uID, ab) VALUES (@cID,@uID,'1');
INSERT INTO ab_users_pub (pID,uID) VALUES (@pID,@uID);

INSERT INTO	global_dates (pID ,	publish_date)	VALUES (@pID, @publishDate);


UPDATE global_users_company SET ab_permissions = 'a:9:{s:7:"details";a:2:{s:7:"actions";a:4:{s:5:"check";s:1:"1";s:8:"material";s:1:"1";s:6:"repeat";s:1:"1";s:7:"invoice";s:1:"1";}s:6:"fields";a:3:{s:4:"rate";s:1:"1";s:9:"totalCost";s:1:"1";s:13:"totalShouldbe";s:1:"1";}}s:5:"lists";a:2:{s:6:"fields";a:3:{s:4:"rate";s:1:"1";s:9:"totalCost";s:1:"1";s:13:"totalShouldbe";s:1:"1";}s:6:"totals";a:1:{s:9:"totalCost";s:1:"1";}}s:4:"form";a:4:{s:3:"new";s:1:"1";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:11:"edit_master";s:1:"1";}s:10:"production";a:1:{s:4:"page";s:1:"1";}s:6:"layout";a:3:{s:4:"page";s:1:"1";s:9:"pagecount";s:1:"1";s:8:"editpage";s:1:"1";}s:8:"overview";a:1:{s:4:"page";s:1:"1";}s:7:"records";a:2:{s:7:"deleted";a:1:{s:4:"page";s:1:"1";}s:6:"search";a:1:{s:4:"page";s:1:"1";}}s:7:"reports";a:5:{s:7:"account";a:2:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}}s:8:"marketer";a:3:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}s:7:"targets";a:1:{s:4:"page";s:1:"1";}}s:10:"production";a:1:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}}s:8:"category";a:2:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}}s:11:"publication";a:4:{s:7:"figures";a:1:{s:4:"page";s:1:"1";}s:9:"discounts";a:1:{s:4:"page";s:1:"1";}s:7:"placing";a:1:{s:4:"page";s:1:"1";}s:7:"section";a:1:{s:4:"page";s:1:"1";}}}s:14:"administration";a:2:{s:11:"application";a:8:{s:8:"accounts";a:2:{s:4:"page";s:1:"1";s:6:"status";a:1:{s:4:"page";s:1:"1";}}s:10:"categories";a:1:{s:4:"page";s:1:"1";}s:9:"marketers";a:2:{s:4:"page";s:1:"1";s:7:"targets";a:1:{s:4:"page";s:1:"1";}}s:10:"production";a:1:{s:4:"page";s:1:"1";}s:8:"sections";a:1:{s:4:"page";s:1:"1";}s:7:"placing";a:2:{s:4:"page";s:1:"1";s:7:"colours";a:1:{s:4:"page";s:1:"1";}}s:7:"loading";a:1:{s:4:"page";s:1:"1";}s:13:"inserts_types";a:1:{s:4:"page";s:1:"1";}}s:6:"system";a:3:{s:5:"dates";a:1:{s:4:"page";s:1:"1";}s:5:"users";a:1:{s:4:"page";s:1:"1";}s:12:"publications";a:1:{s:4:"page";s:1:"1";}}}}' WHERE uID = @uID;

EOFMYSQL


    finish


}
function cleardb {
mysql -h $db_host -u $db_username -p$db_password $db_database<<EOFMYSQL

TRUNCATE TABLE ab_accounts;
TRUNCATE TABLE ab_accounts_pub;
TRUNCATE TABLE ab_accounts_status;
TRUNCATE TABLE ab_advert_sizes;
TRUNCATE TABLE ab_bookings;
TRUNCATE TABLE ab_bookings_logs;
TRUNCATE TABLE ab_categories;
TRUNCATE TABLE ab_category_pub;
TRUNCATE TABLE ab_colour_rates;
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

EOFMYSQL
}

function finish {

	echo "--- Done ---"
	endfn
}
function endfn {
echo ""
echo ""
 read -p "Press [Enter] key to continue..."
	if [ -z "$WIZARD" ]; then
       bash ./setup.sh
    else
       bash ./s_update.sh "$WIZARD"
    fi
}

clear

echo "Impreshin New Company"
echo "-----------------------------------------------"
echo ""

read -e -p "Do you want to set up a new company?: " -i "n" goonfn
if [ $goonfn = "y" ]; then
	startfn
else
	endfn
fi
