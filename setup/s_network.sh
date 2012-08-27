#!/bin/bash

WIZARD=$1

OLDINTERFACES=/etc/network/interfaces
NEWINTERFACES=/etc/network/interfaces

INTERFACE=`/sbin/ifconfig  | grep ^eth | sed "s/ .*//" | head -n 1`

function network {

echo ""
	CURRENT_HOST=$(hostname)
	read -e -p "HOST NAME: " -i "$CURRENT_HOST" CHANGE_HOST

	if [ "$CURRENT_HOST" != "$CHANGE_HOST" ]; then
		echo $CHANGE_HOST >/etc/hostname
		cat /etc/hosts | sed "s/^127\.0\.1\.1[	].*$/127.0.1.1	$CHANGE_HOST/" >/etc/hosts.new; sync; mv /etc/hosts.new /etc/hosts

		echo "host saved"
	fi

	echo ""
	echo "------"
	echo ""

	readNetwork
}

function readNetwork {
	while :
	do
		choice=""

		while [ -z "$choice" ]
		do
			echo "Please choose a network type [1 or 2]?"
			echo "1) DHCP"
			echo "2) Static"
			read -e -p "Netork Type: " -i "" choice
		done

		if [ $choice = 1 ] ; then
		        readNetworkDHCP
		else
		        if [ $choice = 2 ] ; then
		                readNetworkIP
		        else
		            echo "Please make a choice between 1-2!"
		            readNetwork
		        fi
		fi
	done
}

function readNetworkDHCP {
	echo ""
	echo "using DHCP"

	cat <<EOF >$NEWINTERFACES
# This file describes the network interfaces available on your system
# and how to activate them. For more information, see interfaces(5).

# The loopback network interface
auto lo
iface lo inet loopback

# The primary network interface
auto $INTERFACE
iface $INTERFACE inet dhcp
EOF

	proxyfn
}
function readNetworkIP {



	CUR_IFCONFIG=`/sbin/ifconfig $INTERFACE`

	if grep -q "^iface $INTERFACE"".*static" $OLDINTERFACES
	then
		CURRENT_IP=`grep "^	address " $OLDINTERFACES | sed "s/^	address //"`
		CURRENT_SUB=`grep "^	netmask " $OLDINTERFACES | sed "s/^	netmask //"`
		CURRENT_GATEWAY=`grep "^	gateway " $OLDINTERFACES | sed "s/^	gateway //"`
		CURRENT_DNS1=`grep "^	dns-nameservers " $OLDINTERFACES | sed "s/^	dns-nameservers \([^ ]*\).*$/\1/"`
		CURRENT_DNS2=`grep "^	dns-nameservers [^ ]* " $OLDINTERFACES | sed "s/^	dns-nameservers [^ ]* \([^ ]*\).*$/\1/"`
		MUSTSAVE="f"
	else
		CURRENT_IP=""
        CURRENT_SUB=""
		CURRENT_GATEWAY=""
		CURRENT_DNS1=""
		CURRENT_DNS2=""
		MUSTSAVE="t"
	fi



	if [ -z "$CURRENT_IP" ]; then
		CURRENT_IP=`echo $CUR_IFCONFIG | sed "s/.*inet addr:\([0-9\.]*\).*/\1/"`
	fi
	if [ -z "$CURRENT_SUB" ]; then
		CURRENT_SUB=`echo $CUR_IFCONFIG | sed "s/.*Mask:\([0-9\.]*\).*/\1/"`
	fi

	NUMNAMESERVERS=`grep nameserver /etc/resolv.conf | wc -l`
    NS1=""
    NS2=""

    if [ $NUMNAMESERVERS -ge 1 ]
    then
    	NS1=`grep nameserver /etc/resolv.conf | head -n 1 | sed "s/.*nameserver //"`
    fi

    if [ $NUMNAMESERVERS -ge 2 ]
    then
    	NS2=`grep nameserver /etc/resolv.conf | head -n 2 | tail -n 1 | sed "s/.*nameserver //"`
    fi



    if [ -z "$CURRENT_GATEWAY" ]; then
        CURRENT_GATEWAY=`/sbin/route -n | grep "^0\.0\.0\.0" | grep $INTERFACE | sed "s/[^ ]*[ ]*\([^ ]*\).*/\1/"`
    fi

    if [ -z "$CURRENT_DNS1" ]; then
        CURRENT_DNS1=$NS1
    fi
    if [ -z "$CURRENT_DNS2" ]; then
        CURRENT_DNS2=$NS2
    fi





	echo "current"
	echo " IP: $CURRENT_IP"
	echo " SUBNET: $CURRENT_SUB"
	echo " GATEWAY: $CURRENT_GATEWAY"
	echo " DNS1: $CURRENT_DNS1"
	echo " DNS2: $CURRENT_DNS2"

	echo ""

	if [ "$MUSTSAVE" = "t" ]; then
		read -e -p "You need to save these settings. Save now? (y to save | n to edit): " -i "y" saveNetowrk

		if [ "$saveNetowrk" = "y" ]; then
			CHANGE_IP=$CURRENT_IP
		    CHANGE_SUB=$CURRENT_SUB
		    CHANGE_GATEWAY=$CURRENT_GATEWAY
		    CHANGE_DNS1=$CURRENT_DNS1
		    CHANGE_DNS2=$CURRENT_DNS2
			saveNetworkIP

		else
			changeNetworkIP
		fi

	else
		read -e -p "Do you want to change these?: " -i "n" changeNetowrk

		if [ "$changeNetowrk" = "y" ]; then
        	changeNetworkIP
        else
        	proxyfn
        fi

	fi
}
function changeNetworkIP {
	echo ""
	echo "Change: "
	read -e -p "IP: " -i "$CURRENT_IP" CHANGE_IP
	read -e -p "SUBNET: " -i "$CURRENT_SUB" CHANGE_SUB
	read -e -p "GATEWAY: " -i "$CURRENT_GATEWAY" CHANGE_GATEWAY

	read -e -p "DNS1: " -i "$CURRENT_DNS1" CHANGE_DNS1
	read -e -p "DNS2: " -i "$CURRENT_DNS2" CHANGE_DNS2

	echo ""

	read -e -p "Save?: " -i "y" NETWORK_SAVE
   	if [ $NETWORK_SAVE = "y" ]; then
   	    saveNetworkIP
   	    else
        readNetwork
   	fi

}
function saveNetworkIP {

	    cat <<EOF >$NEWINTERFACES
# This file describes the network interfaces available on your system
# and how to activate them. For more information, see interfaces(5).

# The loopback network interface
auto lo
iface lo inet loopback

# The primary network interface
auto $INTERFACE
iface $INTERFACE inet static
    address $CHANGE_IP
    netmask $CHANGE_SUB
    #network $CHANGE_NETWORK
    #broadcast $CHANGE_BCAST
    gateway $CHANGE_GATEWAY
    # dns-* options are implemented by the resolvconf package, if installed
    dns-nameservers $CHANGE_DNS1 $CHANGE_DNS2
EOF



        echo "saved"

        echo " IP: $CHANGE_IP,  SUBNET: $CHANGE_SUB, GATEWAY: $CHANGE_GATEWAY, DNS1: $CHANGE_DNS1, DNS2: $CHANGE_DNS2"
        proxyfn


}
function proxyfn {

	CURRENT_PROXY=$http_proxy
	echo ""
	echo "--- Proxy Server ---"
	echo "Current: $CURRENT_PROXY"
	read -e -p "Do you want to change the proxy?: " -i "n" changeProxy

	if [ "$changeProxy" = "y" ]; then
		echo ""
		echo "must be in format http://DOMAIN\USERNAME:PASSWORD@SERVER:PORT/ leave blank to disable the proxy"
		read -e -p "Proxy: " -i "$CURRENT_PROXY" CHANGE_PROXY

		if [ "$CHANGE_PROXY" != "$CURRENT_PROXY" ]; then
			export $CHANGE_PROXY
		fi
	fi
	finish


}
function finish {
	echo ""
	echo "Activating the configuration"
	sleep 1
	ifdown $INTERFACE
	ifup $INTERFACE
	hostname --file /etc/hostname
	sudo service hostname restart

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
       bash ./s_folder.sh "$WIZARD"
    fi
}
clear
echo "Networking"
echo "-----------------------------------------------"
echo ""
read -e -p "Continue?: " -i "y" goon
if [ $goon = "y" ]; then
	network
else
	endfn
fi