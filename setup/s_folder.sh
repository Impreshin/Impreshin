#!/bin/bash
export LANG=""
WIZARD=$1
function startfn {
	echo ""
	echo "Stopping MySQL"
	sudo stop mysql
	echo "Stopping Samba"
	sudo service smbd stop

	echo "----------"
	echo ""
	echo "Setting up Drive Permissions"

	sudo chgrp plugdev /media/data
  	sudo chmod g+w /media/data
  	sudo chmod +t /media/data

	echo ""
	echo "Extracting Folders & Files"



	# tar pczf ./data_drive.tar.gz /media/data --exclude=/media/data/lost+found
	sudo tar -pxzf ./data_drive.tar.gz -C /
	echo "--- done ---"
	echo ""
	echo "Applying Folder Permissions"
	echo ""
	sudo chown -R mysql:mysql /media/data/mysql
	echo "MySQL - done"
	echo ""
	sudo chown -R www-data:www-data /media/data/backups
	echo "Backups - done"
	echo ""
	sudo chown -R www-data:www-data /media/data/uploads
	echo "Uploads - done"
	echo ""
	sudo chown -R www-data:www-data /media/data/web
	echo "Impreshin - done"
	echo ""
	echo "----------"

	echo "Starting MySQL"
	sudo start mysql

	echo "Starting Samba"
	sudo service smbd start

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
       bash ./s_database.sh "$WIZARD"
    fi
}

clear
echo "Folders & Files"
echo "-----------------------------------------------"
echo ""

read -e -p "Continue?: " -i "y" goonfn
if [ $goonfn = "y" ]; then
	startfn
else
	endfn
fi