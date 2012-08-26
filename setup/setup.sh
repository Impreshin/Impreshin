#!/bin/bash
export LANG=""

function runscript {
	clear
	echo "Setup"
	echo "-----------------------------------------------"
	echo ""
	ARRAY=( 'Wizard' 'Partitioning' 'Networking' 'Folders & Files' 'ImpreshiN Setup')
	ELEMENTS=${#ARRAY[@]}

	# echo each element in array
	# for loop
	for (( i=0;i<$ELEMENTS;i++)); do
	    echo "$i) ${ARRAY[${i}]}"
	done
	read -p "Choose Script: " -i "0" SCRIPT

	if [ -z "${ARRAY[SCRIPT]}" ]; then
		echo ""
		echo "Sorry, $SCRIPT is not a valid answer"
		runscript
	else
	echo "Running ${ARRAY[SCRIPT]}"
	sleep 1


	case $SCRIPT in
        0)
	        read -e -p "this will launch the first time wizard. Continue?: " -i "y" goon

	        if [ $goon = "y" ]; then
            		 bash ./s_drive.sh "1"
            	else
            		runscript
            	fi

        ;;
        1) bash ./s_drive.sh ;;
        2) bash ./s_network.sh ;;
        3) bash ./s_folder.sh ;;
        4) bash ./s_database.sh ;;
    esac


	fi


}



runscript

