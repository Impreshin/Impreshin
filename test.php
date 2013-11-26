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




function grab_xml_definition ($word, $ref, $key)
{	$uri = "http://www.dictionaryapi.com/api/v1/references/" . urlencode($ref) . "/xml/" .
	urlencode($word) . "?key=" . urlencode($key);
	
	
	
	return file_get_contents($uri);
};

$resp = '<?xml version="1.0" encoding="utf-8" ?><entry_list version="1.0"><entry id="test[1]"><ew>test</ew><subj>ED-2a(2)#MA-1b(1)#MT-1a#CH-2a(1)</subj><hw hindex="1">test</hw><sound><wav>test0001.wav</wav><wpr>!test</wpr></sound><pr>Ëˆtest</pr><fl>noun</fl><et>Middle English, vessel in which metals were assayed, potsherd, from Anglo-French <it>test, tees</it> pot, Latin <it>testum</it> earthen vessel; akin to Latin <it>testa</it> earthen pot, shell</et><def><date>14th century</date> <sn>1 a</sn> <ssl>chiefly British</ssl> <dt>:<sx>cupel</sx></dt>  <sn>b <snp>(1)</snp></sn> <dt>:a critical examination, observation, or evaluation :<sx>trial</sx></dt> <sd>specifically</sd> <dt>:the procedure of submitting a statement to such conditions or operations as will lead to its proof or disproof or to its acceptance or rejection <vi>a <it>test</it> of a statistical hypothesis</vi></dt>  <sn><snp>(2)</snp></sn> <dt>:a basis for evaluation :<sx>criterion</sx></dt>  <sn>c</sn> <dt>:an ordeal or oath required as proof of conformity with a set of beliefs</dt> <sn>2 a</sn> <dt> :a means of <fw>testing</fw>: as</dt>  <sn><snp>(1)</snp></sn> <dt>:a procedure, reaction, or reagent used to identify or characterize a substance or constituent</dt>  <sn><snp>(2)</snp></sn> <dt>:something (as a series of questions or exercises) for measuring the skill, knowledge, intelligence, capacities, or aptitudes of an individual or group </dt>  <sn>b</sn> <dt>:a positive result in such a test</dt> <sn>3</sn> <dt>:a result or value determined by testing</dt> <sn>4</sn> <dt>:<sx>test match</sx></dt></def></entry><entry id="test[2]"><ew>test</ew><subj>ED-1</subj><hw hindex="2">test</hw><fl>adjective</fl><def><date>1687</date> <sn>1</sn> <dt>:of, relating to, or constituting a test</dt> <sn>2</sn> <dt>:subjected to, used for, or revealed by <fw>testing</fw> <vi>a <it>test</it> group</vi> <vi><it>test</it> data</vi></dt></def></entry><entry id="test[3]"><ew>test</ew><hw hindex="3">test</hw><fl>verb</fl><def><vt>transitive verb</vt><date>1748</date> <sn>1</sn> <dt>:to put to test or proof :<sx>try</sx> <un>often used with <it>out</it></un></dt> <sn>2</sn> <dt>:to require a doctrinal oath of</dt><vt>intransitive verb</vt> <sn>1 a</sn> <dt>:to undergo a test</dt>  <sn>b</sn> <dt>:to be assigned a standing or evaluation on the basis of <fw>tests</fw> <vi><it>test</it><it>ed</it> positive for cocaine</vi> <vi>the cake <it>test</it><it>ed</it> done</vi></dt> <sn>2</sn> <dt>:to apply a test as a means of analysis or diagnosis <un>used with <it>for</it> <vi><it>test</it> for mechanical aptitude</vi></un></dt></def><uro><ure>test*abil*i*ty</ure><sound><wav>test0002.wav</wav><wpr>+tes-tu-!bi-lu-tE</wpr></sound> <pr>ËŒtes-tÉ™-Ëˆbi-lÉ™-tÄ“</pr> <fl>noun</fl></uro><uro><ure>test*able</ure><sound><wav>test0003.wav</wav><wpr>!tes-tu-bul</wpr></sound> <pr>Ëˆtes-tÉ™-bÉ™l</pr> <fl>adjective</fl></uro><dro><drp>test the waters</drp> <vr><vl>also</vl> <va>test the water</va> </vr> <def><dt>:to make a preliminary test or survey (as of reaction or interest) before embarking on a course of action</dt></def></dro></entry><entry id="test[4]"><ew>test</ew><subj>ZI</subj><hw hindex="4">test</hw><fl>noun</fl><et>Latin <it>testa</it> shell</et><def><date>circa 1842</date><dt>:an external hard or firm covering (as a shell) of many invertebrates (as a foraminifer or a mollusk)</dt></def></entry><entry id="Test"><ew>Test</ew><subj>BB</subj><hw>Test</hw><fl>abbreviation</fl><def><dt>Testament</dt></def></entry><entry id="test ban"><ew>test ban</ew><subj>GV</subj><hw>test ban</hw><fl>noun</fl><def><date>1958</date><dt>:a self-imposed partial or complete ban on the testing of nuclear weapons that is mutually agreed to by countries possessing such weapons</dt></def></entry><entry id="test bed"><ew>test bed</ew><subj>AE#ML</subj><hw>test bed</hw><fl>noun</fl><def><date>1914</date><dt>:a vehicle (as an airplane) used for testing new equipment (as engines or weapons systems)</dt> <sd>broadly</sd> <dt>:any device, facility, or means for testing something in development</dt></def></entry><entry id="test case"><ew>test case</ew><subj>LW</subj><hw>test case</hw><fl>noun</fl><def><date>1850</date> <sn>1</sn> <dt>:a representative case whose outcome is likely to serve as a precedent</dt> <sn>2</sn> <dt>:a proceeding brought by agreement or on an understanding of the parties to obtain a decision as to the constitutionality of a statute</dt></def></entry><entry id="test-drive"><ew>test-drive</ew><subj>AV-1#BZ#CP-2</subj><hw>testâ€“drive</hw><sound><wav>testdr01.wav</wav><wpr>!tes(t)-+drIv</wpr></sound><pr>Ëˆtes(t)-ËŒdrÄ«v</pr><fl>verb</fl><in><if>testâ€“drove</if> <pr>-ËŒdrÅv</pr></in><in><if>testâ€“driv*en</if> <pr>-ËŒdri-vÉ™n</pr></in><in><if>testâ€“driv*ing</if> <pr>-ËŒdrÄ«-viÅ‹</pr></in><def><vt>transitive verb</vt><date>1950</date> <sn>1</sn> <dt>:to drive (a motor vehicle) in order to evaluate performance</dt> <sn>2</sn> <dt>:to use or examine (as a computer program) in order to evaluate performance <vi><it>testâ€“drive</it> the new game</vi></dt></def><uro><ure>testâ€“drive</ure> <fl>noun</fl></uro></entry><entry id="test-fly"><ew>test-fly</ew><subj>AE</subj><hw>testâ€“fly</hw><sound><wav>testfl01.wav</wav><wpr>!test-+flI</wpr></sound><pr>Ëˆtest-ËŒflÄ«</pr><fl>verb</fl><in><if>testâ€“flew</if> <pr>-ËŒflÃ¼</pr></in><in><if>testâ€“flown</if> <pr>-ËŒflÅn</pr></in><in><if>testâ€“fly*ing</if></in><def><vt>transitive verb</vt><date>1936</date><dt>:to subject to a flight test <vi><it>testâ€“fly</it> an experimental plane</vi></dt></def></entry></entry_list>';




$word = isset($_GET['word'])?$_GET['word']:"";
if ($word){
	$resp = grab_xml_definition($word, "collegiate", "da88aa48-f888-42b6-8bc3-9afa52fe6446");


	

	$xml   = simplexml_load_string($resp);
	$array = XML2Array($xml);
	$array = array($xml->getName() => $array);
	
	$defs = $array['entry_list']['entry'];
	//test_array($defs);
	
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