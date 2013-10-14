<?php
$t = date("Y-m-d",strtotime(date("Y")."-".date('m')."-".'1')) . " to " . date("Y-m-d",strtotime(date("Y")."-".date('m')."-".date('t'))) ;
echo $t;