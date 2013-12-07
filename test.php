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


date_default_timezone_set('Africa/Johannesburg');
$cID = "1";

$broker = enchant_broker_init();
$tag = 'en_GB';
$cust='my';

enchant_broker_set_dict_path($broker, ENCHANT_MYSPELL, 'C:\PHP\enchant\MySpell');


$file = realpath("./uploads/dictionaries/$cID/$cust.txt");



//test_array($file); 
//print_r($dicts);

if (enchant_broker_dict_exists($broker, $tag)) {

	
	$dict = enchant_broker_request_dict($broker, $tag);
	
	//$dict2 = enchant_broker_request_pwl_dict( $broker , 'http://impreshin.local/dic.php' );

	//test_array($dict); 
//	enchant_dict_add_to_session ( $dict , "woofstamer" );
	$orig_word = $word = 'failingg';
	

	$suggestions = array();
	if (file_exists($file)){
		$dict2 = enchant_broker_request_pwl_dict( $broker , $file );
		if (enchant_dict_check($dict2, $word) !== true) {
			$r = enchant_dict_suggest($dict2, $word);
			if ($r){
				foreach ($r as $word){
					if (!in_array($word,$suggestions)) $suggestions[] = $word;
				}
			}
			
			
		}
	}
	
	
	if (enchant_dict_check($dict, $word) !== true) {
		$r = enchant_dict_suggest($dict, $word);
		if ($r){
			foreach ($r as $word){
				if (!in_array($word,$suggestions)) $suggestions[] = $word;
			}
		}
	}
	
	
	
	test_array(array("word"=>$orig_word,"suggestions"=>$suggestions)); 
	
	
	echo nl2br(print_r($suggestions, true));
}

enchant_broker_free($broker);