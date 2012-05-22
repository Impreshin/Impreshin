<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab\publications as publications;

class loading {
	private $classname;


	public static function getLoading($pID="", $cm = "") {
		$timer = new timer();

		$return = array(
			"pages"=>0,
			"loading"=>0,
			"other"=>array()
		);
		$loadingData = F3::get("DB")->exec("
			SELECT * FROM 	ab_page_load WHERE pID = '$pID' ORDER BY pages ASC
		");


		$publication = new publications();
		$publication = $publication->get($pID);


		$pageSize = $publication['cmav'] * ($publication['columnsav']);

		$loading = array();
		$use = "";
		$i = 0;
		foreach ($loadingData as $item){
			$pages = $item['pages'];
			$percent = $item['percent'];
			$avspace = $pages * $pageSize;

			$keepin = $avspace * ($percent/100);

			$loading[$item['ID']] = array(
				"pages"=> $pages,
				"loading"=>number_format(($cm/ $avspace)*100,2),
				"nr"=>$i
			);

			if ($cm <= $keepin && $use == ""){
				$use = $item['ID'];

			}
			$i++;

		}
		//$loading[$use]['current'] = $i;


		$return['pages'] = $loading[$use]['pages'];
		$return['loading'] = $loading[$use]['loading'];









		$cur = $loading[$use]['nr'];
		$a = array();
		foreach ($loading as $item) {
			$item['current'] = ($item['nr']==$cur)?"*":'';
			unset($item['nr']);
			$a[] = $item;
		}
		$loading = $a;

		if(isset($loading[$cur + 2])) $return['other'][] = $loading[$cur + 2];
		if(isset($loading[$cur + 1])) $return['other'][] = $loading[$cur + 1];
		if(isset($loading[$cur ])) $return['other'][] = $loading[$cur];
		if(isset($loading[$cur - 1])) $return['other'][] = $loading[$cur - 1];
		if(isset($loading[$cur - 2])) $return['other'][] = $loading[$cur - 2];







		$timer->stop("Models - loading - getLoading", func_get_args());
		return $return;
	}

}