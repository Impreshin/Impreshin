<?php
include_once("Mobile_Detect.php");
$detect = new Mobile_Detect();

// web version
$allow_mobile = isset($_COOKIE['mobile'])? true:false;
if (isset($_GET['mobile'])) {
	if ($_GET['mobile']=='false'){
		setcookie("mobile", "");
		$allow_mobile = false;
	} else {
		setcookie("mobile", true, time() + 31536000, "/");
		$allow_mobile = true;
	}

}


if ($allow_mobile && $detect->isMobile()){
	if (!$detect->isTablet()) {
		header("Location:http://www.mobisite.mobi");
	}
}







@include("Mobile_Detect.php");
$detect = new Mobile_Detect();
if ($detect->isMobile() && isset($_COOKIE['mobile'])) { // if mobile is detected and the cookie is there
	$detect = "false"; // if anything this should be = false (no "") - not sure what the class returns tho since if ("false") will = yes should you use it later on
} elseif ($detect->isMobile()) {
	header("Location:http://www.mobisite.mobi");
} elseif ($detect->isTablet()) {

	header("Location:http://www.fullsite.co.za");
}