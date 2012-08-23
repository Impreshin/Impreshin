#!/bin/bash
export LANG=""
function getDrive {
	NUM=0

	echo ""

	for i in `sudo fdisk -l 2>/dev/null | grep "^Disk /" | sed "s/^Disk \([^:]*\): \([^ ]*\) \([^,]*\).*/\1;\2;\3/"`
	do
		NUM=$(($NUM+1))


		DISK=`echo $i | sed "s/^\([^;]*\).*/\1/"`
		SIZE=`echo $i | sed "s/^[^;]*;\([^;]*\);\(.*\)/\1 \2/"`

		DISKS[$NUM]=$DISK

		echo "$NUM) $DISK - $SIZE"
	done

	read -p "Choose disk: " DISKNUM

	DISK=${DISKS[$DISKNUM]}

	if [ -z "$DISK" ]
	then
		echo ""
		echo "Sorry, $DISKNUM is not a valid answer"
	fi
}

function useDrive {
	echo ""
	read -e -p "Use drive $1. Warning all data will be lost on the drive. Continue?: " -i "y" goon
	if [ $goon = "y" ]; then
		formatDrive $1
	else
		getDrive
	fi
}



function formatDrive {

	DISK=$1
	PART=$DISK""1


	sudo dd if=/dev/zero of=$DISK bs=1M count=1 >/dev/null 2>&1
	echo -e -n "n\np\n1\n\n\nw\n" | fdisk $DISK >/dev/null 2>&1

	RET=$?
	if [ "$RET" != "0" ]
	then
		echo "Failed to partition disk."
		exit 1
	fi

	sync

	echo $DISK partitioned.

	mkfs.ext4 -L DATA $PART >/dev/null 2>&1

	RET=$?
	if [ "$RET" != "0" ]
	then
		echo "Failed to initialise filesystem."
		exit 1
	fi

	sync

	echo $PART initialised.

	mountDrive $PART


}
function mountDrive {
	echo ""
	echo "Mounting the drive to /media/data"
	sudo mkdir /media/data
	sudo mount $1 /media/data


	permissions
}
function permissions {

	echo ""
	echo "Setting up permissions"

	sudo chgrp plugdev /media/data
  	sudo chmod g+w /media/data
  	sudo chmod +t /media/data

	cd /

	sudo stop mysql
	echo ""
	echo ""
	echo "extracting files"
	sudo tar -xvzf /home/administrator/data_drive.tar.gz
	echo ""
	echo ""
	echo "mysql folder permissions"
	sudo chown -R mysql:mysql /media/data/mysql
	echo "done"
	echo ""
	echo "backups folder permissions"
	sudo chown -R www-data:www-data /media/data/backups
	echo "done"
	echo ""
	echo "uploads folder permissions"
	sudo chown -R www-data:www-data /media/data/uploads
	echo "done"
	echo ""
	echo "web folder permissions"
	sudo chown -R www-data:www-data /media/data/web
	echo "done"
	echo ""
	echo ""

	sudo start mysql


	mysql_script
}
function mysql_script {

echo "mysql"


	#finishing
}
function networkStuff {

	CURRENT_IP=/sbin/ifconfig 'eth0' | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'
	CURRENT_SUB=/sbin/ifconfig 'eth0' | grep 'Mask:' | cut -d: -f4 | awk '{ print $1}'

	echo "current"
	echo " IP: $CURRENT_IP"
	echo " Sub: $CURRENT_SUB"


	#finishing
}
function finishing {
	echo "rebooting in 5 seconds"
	sleep 5
	sudo reboot
}
clear

networkStuff

# while [ -z "$DISK" ]
# do
#	getDrive
# done
#
# useDrive $DISK