# backup usb to image
dd if=/dev/sdc conv=sync,noerror bs=1M | gzip -c  > /home/william/Desktop/impreshin.img.gz

# sudo dd if=/dev/sdc of=/home/william/Desktop/impreshin.bin
# sudo fdisk -l /dev/sdc > /home/william/Desktop/impreshin_fdisk.info

# write the backup to the drive
sudo gunzip -c /home/william/Desktop/impreshin.img.gz | sudo dd of=/dev/sdb bs=1M




# sudo gunzip -c /home/william/Desktop/impreshin.img.gz | sudo dd of=/dev/sdb conv=sync,noerror bs=1M
# gzip -cd /home/william/Desktop/impreshin.img.gz | dd of=/dev/sdb