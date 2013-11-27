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




function grab_xml_definition ($word)
{	$uri = "http://www.stands4.com/services/v2/defs.php?uid=3116&tokenid=DncJPzPES3OLbTH7&word=" . urlencode($word);
	
	
	
	return file_get_contents($uri);
};



$word = isset($_GET['word'])?$_GET['word']:"";
if ($word){
	$resp = grab_xml_definition($word);


	//test_array($resp);
	

	$xml   = simplexml_load_string($resp);
	$array = XML2Array($xml);
	$array = array($xml->getName() => $array);
	
	$defs = $array['results'];
	test_array($defs);
	
	$def = array();
	foreach ($defs as $k=>$item){
		if (is_numeric($k) || $item['dt']){
			
			$d= array();
			$dt = $item['def'];
			foreach ($dt['dt'] as $kk=>$i){
				if (is_numeric($kk)||$i['dt']){
				if (is_string($i)) $d[] = $i;
					}
			}
			
			
			$def[] = array(
				"heading"=>$item['ew'],
				"subj"=>$item['subj'],
				"fl"=>$item['fl'],
				"def"=>$d,
				
			);
		}
		
	}
	
	$return = array(
		"word"=>$defs['ew'],
		"subj"=>$defs['subj'],
		"sound"=>$defs['sound']['wav'],
		"pr"=>$defs['pr'],
		"fl"=>$defs['fl'],
		"date"=>$defs['def']['date'],
	);
	
	$return['def']=$def;
	
	test_array($return);
	
}
function XML2Array(SimpleXMLElement $parent){
	$array = array();

	foreach ($parent as $name => $element) {
		($node = & $array[$name])
		&& (1 === count($node) ? $node = array($node) : 1)
		&& $node = & $node[];

		$node = $element->count() ? XML2Array($element) : trim($element);
	}

	return $array;
}