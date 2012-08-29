#!/bin/bash

WIZARD=$1
function startfn {

	echo "Zoutnet doesnt need the database stuff as we will be loading the db from the online version"



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
echo "ImpreshiN Setup for first run"
echo "-----------------------------------------------"
echo ""

read -e -p "Continue?: " -i "y" goonfn
if [ $goonfn = "y" ]; then
	startfn
else
	endfn
fi
