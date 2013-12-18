<?php
function test_array($array,$splitter=''){

	if (!is_array($array) && $splitter){
		$array = explode($splitter,$array);
	}

	if (!is_array($array)){
		echo ($array);
		exit();
	}

	header("Content-type: application/json; charset=UTF-8");
	echo json_encode($array);
	exit();
}

$str = 'a:11:{s:11:"provisional";a:8:{s:5:"group";a:2:{s:1:"g";s:6:"author";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:5:"title";s:1:"o";s:4:"DESC";}s:9:"highlight";s:6:"locked";s:5:"stage";s:1:"2";s:6:"filter";s:1:"*";s:6:"search";s:0:"";s:3:"col";a:8:{i:0;s:5:"title";i:1;s:2:"cm";i:2;s:6:"placed";i:3;s:11:"photosCount";i:4;s:12:"commentCount";i:5;s:4:"page";i:6;s:9:"newsbooks";i:7;s:8:"priority";}s:5:"count";i:8;}s:8:"newsbook";a:7:{s:5:"group";a:2:{s:1:"g";s:6:"author";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:5:"title";s:1:"o";s:3:"ASC";}s:6:"filter";s:1:"*";s:6:"search";s:0:"";s:9:"highlight";s:6:"placed";s:3:"col";a:7:{i:0;s:5:"title";i:1;s:6:"author";i:2;s:5:"stage";i:3;s:2:"cm";i:4;s:11:"photosCount";i:5;s:4:"page";i:6;s:8:"priority";}s:5:"count";i:7;}s:11:"admin_dates";a:1:{s:5:"order";a:2:{s:1:"c";s:12:"publish_date";s:1:"o";s:4:"DESC";}}s:6:"search";a:5:{s:5:"group";a:2:{s:1:"g";s:6:"author";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:6:"datein";s:1:"o";s:3:"ASC";}s:6:"search";a:2:{s:6:"search";s:11:"kersseisoen";s:5:"dates";s:24:"2013-11-22 to 2013-12-31";}s:3:"col";a:7:{i:0;s:5:"title";i:1;s:6:"author";i:2;s:5:"stage";i:3;s:2:"cm";i:4;s:11:"photosCount";i:5;s:6:"datein";i:6;s:8:"priority";}s:5:"count";i:7;}s:6:"layout";a:3:{s:10:"categoryID";a:3:{i:1;s:1:"1";i:6;s:1:"1";i:5;s:1:"1";}s:5:"order";a:2:{s:1:"c";s:2:"cm";s:1:"o";s:3:"ASC";}s:6:"filter";s:1:"*";}s:4:"form";a:4:{s:4:"type";s:1:"1";s:11:"last_author";s:2:"30";s:13:"last_category";s:1:"1";s:13:"last_language";s:5:"af_ZA";}s:11:"admin_users";a:1:{s:5:"order";a:2:{s:1:"c";s:8:"fullName";s:1:"o";s:3:"ASC";}}s:10:"production";a:5:{s:5:"group";a:2:{s:1:"g";s:4:"page";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:5:"title";s:1:"o";s:3:"ASC";}s:6:"filter";s:1:"*";s:9:"highlight";s:5:"ready";s:6:"search";s:0:"";}s:23:"reports_author_newsbook";a:10:{s:5:"years";s:9:"2013,2012";s:9:"timeframe";s:3:"12m";s:8:"combined";s:1:"0";s:5:"group";a:2:{s:1:"g";s:4:"type";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:5:"title";s:1:"o";s:3:"ASC";}s:9:"tolerance";s:2:"15";s:2:"ID";a:1:{s:5:"cID_1";s:2:"30";}s:3:"col";a:9:{i:0;s:5:"title";i:1;s:5:"stage";i:2;s:2:"cm";i:3;s:5:"words";i:4;s:8:"priority";i:5;s:11:"photosCount";i:6;s:11:"photosCount";i:7;s:11:"dateChanged";i:8;s:11:"photosCount";}s:5:"count";i:6;s:6:"filter";s:1:"1";}s:24:"reports_author_submitted";a:9:{s:5:"years";s:14:"2013,2012,2011";s:9:"timeframe";s:3:"12m";s:8:"combined";s:1:"0";s:5:"group";a:2:{s:1:"g";s:4:"type";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:11:"dateChanged";s:1:"o";s:3:"ASC";}s:9:"tolerance";s:2:"25";s:2:"ID";a:1:{s:5:"cID_1";s:2:"41";}s:3:"col";a:8:{i:0;s:5:"title";i:1;s:5:"stage";i:2;s:2:"cm";i:3;s:5:"words";i:4;s:8:"priority";i:5;s:9:"newsbooks";i:6;s:11:"dateChanged";i:7;s:11:"photosCount";}s:5:"count";i:8;}s:16:"records_newsbook";a:3:{s:5:"group";a:2:{s:1:"g";s:6:"author";s:1:"o";s:3:"ASC";}s:5:"order";a:2:{s:1:"c";s:5:"title";s:1:"o";s:3:"ASC";}s:3:"dID";a:1:{s:3:"p_1";s:4:"1288";}}}';

$str = unserialize($str);



test_array($str); 
