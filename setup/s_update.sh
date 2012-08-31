#!/bin/bash

WIZARD=$1
function startfn {
php_output=`php /media/data/web/setup/cfg.php`
	IFS=":"
	while read -r key val; do
	    eval ${key}="${val}"
	done <<< "$php_output"



	echo ""
	echo "Fetching updates. This could take a while..."

	echo ""


	cd /media/data/web

	git reset --hard HEAD
    git pull https://$git_username:$git_password@$git_path $git_branch

	cd ~/


	finish
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
       bash ./setup.sh
    fi
}

clear

echo "Impreshin Update"
echo "-----------------------------------------------"
echo ""

read -e -p "Do you want to update Impreshin?: " -i "y" goonfn
if [ $goonfn = "y" ]; then
	startfn
else
	endfn
fi
