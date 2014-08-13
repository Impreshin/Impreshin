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

$str = '1.000';

$str = is_numeric($str);



test_array($str); 
