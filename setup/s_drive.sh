#!/bin/bash
export LANG=""
WIZARD=$1
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




function formatDrive {

	DISK=$1
	PART=$DISK""1

	echo "Formatting and parttitioning disk $DISK | $PART"
	echo ""


	sudo dd if=/dev/zero of=$DISK bs=512 count=1024 >/dev/null 2>&1
	echo -e -n "n\np\n1\n\n\nw\n" | sudo fdisk $DISK >/dev/null 2>&1



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
	# sudo mkdir /media/data
	sudo mount $1 /media/data


	LINE='LABEL=DATA    /media/data   ext4    defaults  0   2'
	grep -q "^LABEL=DATA" /etc/fstab || sudo sh -c 'echo "$LINE" >>/etc/fstab'

	finish
}
function finish {

	echo "--- Done ---"
	sleep 1
	endfn
}
function endfn {
echo ""
echo ""
	 read -p "Press [Enter] key to continue..."
	if [ -z "$WIZARD" ]; then
       bash ./setup.sh
    else
       bash ./s_network.sh "$WIZARD"
    fi
}


clear
echo "Drive & Partitions"
echo "-----------------------------------------------"
echo ""

while [ -z "$DISK" ]
 do
	getDrive
 done
 read -e -p "Use drive $DISK. Warning all data will be lost on the drive. Continue?: " -i "y" goon
	if [ $goon = "y" ]; then
		formatDrive $DISK
	else
		getDrive
	fi

