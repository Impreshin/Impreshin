<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab\publications as publications;

class loading {
	private $classname;


	public static function getLoading($pID = "", $cm = "", $forcepages = "") {
		$timer = new timer();


		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$pID) $pID = $user['pID'];

		if ($pID == $user['pID']){
			$publication = $user['publication'];
		} else {
			$publication = new publications();
			$publication = $publication->get($pID);
		}

		$return = array(
			"pages"  => 0,
			"loading"=> 0,
			"other"  => array(),
			"forced"  => false,
			"error"  => ""
		);


		if ($forcepages){
			$forcepages = F3::get("DB")->exec("
				SELECT *, ABS( pages - $forcepages ) AS distance FROM ab_page_load
				WHERE pID = '$pID'
				ORDER BY distance
				LIMIT 6
			");
			$forcepages = $forcepages[0]["pages"];
			$return['forced']=true;
		}






			$pageSize = $publication['cmav'] * ($publication['columnsav']);


			$loadingData = F3::get("DB")->exec("
				SELECT * FROM 	ab_page_load WHERE pID = '$pID' ORDER BY pages ASC
			");

			$loading = array();
			$use = "";
			$i = 0;
			foreach ($loadingData as $item) {
				$pages = $item['pages'];
				$percent = $item['percent'];
				$avspace = $pages * $pageSize;

				$keepin = $avspace * ($percent / 100);

				$loading[$item['ID']] = array(
					"pages"  => $pages,
					"loading"=> number_format(($cm / $avspace) * 100, 2),
					"nr"     => $i
				);

				if ($forcepages){
					if ($item['pages'] == $forcepages) {
						$use = $item['ID'];

					}
				} else {
					if ($cm <= $keepin && $use == "") {
						$use = $item['ID'];
					}
				}
				$i++;

			}
			//$loading[$use]['current'] = $i;

		if (!$use){
			$use = $loadingData[count($loadingData)-1]['ID'];
			$return['error']="Please check your loading settings, the current loading trumps your highest page number";
		}

			$return['pages'] = $loading[$use]['pages'];
			$return['loading'] = $loading[$use]['loading'];


			$cur = $loading[$use]['nr'];
			$a = array();
			foreach ($loading as $item) {
				$item['current'] = ($item['nr'] == $cur) ? "*" : '';
				unset($item['nr']);
				$a[] = $item;
			}
			$loading = $a;

			if (isset($loading[$cur - 2])) $return['other'][] = $loading[$cur - 2];
			if (isset($loading[$cur - 1])) $return['other'][] = $loading[$cur - 1];
			if (isset($loading[$cur])) $return['other'][] = $loading[$cur];
			if (isset($loading[$cur + 1])) $return['other'][] = $loading[$cur + 1];
			if (isset($loading[$cur + 2])) $return['other'][] = $loading[$cur + 2];


		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

}