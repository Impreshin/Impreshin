<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class layout extends data {
	function __construct() {
		parent::__construct();

	}
	function _list() {
		$user = $this->f3->get("user");

		$userID = $user['ID'];
		$pID = $user['pID'];
		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$settings = models\settings::_read("layout");

		$categoryID = (isset($_REQUEST['categoryID']) && $_REQUEST['categoryID'] != "") ? $_REQUEST['categoryID'] : $settings['categoryID'];

		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="") ? $_REQUEST['filter'] : $settings['filter'];

		
	
		$stats = $this->_stats();
		$maxPage = $stats['loading']['pages'];



		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];

		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")) {
			if ($settings['order']['c'] == $_REQUEST['order']) {
				if ($ordering_d == "ASC") {
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}

			}

		}


		
		
		
		
		
		//test_array($maxPage); 
		$values = array();
		$values["layout"] = array(
			"filter"=>$filter,
			"categoryID" => array(
				$pID=>$categoryID,
			),
			"order"      => array(
				"c"=> $ordering_c,
				"o"=> $ordering_d
			),
		);


		
		models\settings::save($values);
		
		$stage_stuff = "";
		if ($filter=='1'){
			$stage_stuff = "AND nf_stages.ID='2'";
		}
		//
		


	//	test_array(array($pID,$dID)); 
		$records = models\articles::getAll("(nf_article_newsbook.pID = '$pID' AND nf_article_newsbook.dID='$dID') $stage_stuff  AND nf_articles.deleted is null", "", array("c" => $ordering_c, "o" => $ordering_d) , array("pID"=>$pID,"dID"=>$dID));
		$rawBookings= $records;
		$records = models\articles::display($records);


		
		
		

	//	test_array($records); 
		if (count($records)) $records = $records[0]['records'];
		$return = array();
		

		$cols = array(
			"ID",
			"title",
			"datein",
			"cm",
			"words",
			"typeID",
			"type",
			"type_icon",
			"category",
			"author",
			"stage",
			"photosCount",
			"page",
			"stageID",
			
		);


		$r = array();
		$cats = array();
		foreach ( models\categories::getAll("cID='". $user['company']['ID']."'","orderby ASC") as $item){
			$item['recordCount']=0;
			$cats[$item['ID']]=$item;
		}
		
		
		$remainingRecords = 0;
		
		foreach ($records as $record){
			$b = array();
			foreach ($cols as $col){
				$b[$col] = $record[$col];
			}
			if (!$b['page'] || $b['page']>$maxPage){
				if ($record['categoryID']==$categoryID){
					$r[] = $b;
				}
				$remainingRecords++;
				if (!isset($cats[$record['categoryID']]['recordCount']))$cats[$record['categoryID']]['recordCount'] = 0;
				$cats[$record['categoryID']]['recordCount']=$cats[$record['categoryID']]['recordCount'] + 1;
			}
			
		}
		

		$return['category'] =$cats;
		
		
		//test_array($return); 
		$records = $r;

		$return['records'] = $records;
		$return['categoryID'] = $categoryID;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];

		

		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC");
		$lockedPages = 0;
		foreach ($pagesReal as $page) {

			if ($page['locked'] == '1') $lockedPages++;
		}
		
		$loading = $stats['loading'];
		//$stats = models\record_stats::stats($rawBookings,array("planned"));
		
		$totals = array(
			"records"=>count($rawBookings),
			"planned"=>count($rawBookings) - $remainingRecords
		);
		
		$stats=array();
		$stats['loading'] = $loading;
		$stats['records'] = array(
			"total"=>$totals["records"],
			"planned"=>array(
				"r"=>$totals["planned"],
				"p"=>($totals['planned']) ? number_format((($totals["planned"] / $totals["records"]) * 100), 2) : 0
			)
		);
		
		$stats['records']['locked'] = array(
			"r" => $lockedPages,
			"p" => number_format($lockedPages ? ($lockedPages / count($pagesReal)) * 100 : 0, 2)
		);

		$return['stats'] = $stats;



		

		return $GLOBALS["output"]['data'] = $return;
	}

	function _pages($page="") {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$pageSelected = ($page)?$page: isset($_REQUEST['page'])?$_REQUEST['page']:"";
		$pageID = "";

		$recordsRaw = models\articles::getAll("(nf_article_newsbook.pID = '$pID' AND nf_article_newsbook.dID='$dID') AND nf_stages.ID='2' AND nf_articles.deleted is null", "title ASC", "",array("pID"=>$pID,"dID"=>$dID));


		//	test_array($records); 



		$ab_records = \apps\ab\models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND pageID is not null");
		


		
		$ab = array();
		foreach ($ab_records as $record){
			if ($record['pageID']) {
				

				if (!isset($ab[$record['pageID']])) {
					$ab[$record['pageID']] = array(
						"cm"=>0,
						"records"=>array()
					);
				}
				if ($record['totalspace']) {
					$ab[$record['pageID']]['cm'] = $ab[$record['pageID']]['cm'] + $record['totalspace'];
				}
				$ab[$record['pageID']]['records'][] = array(
					"ID"=>$record['ID'],
					"client"=>$record['client'],
					"totalspace"=>$record['totalspace']+0,
					
				);
			}

		}
		
		
		//test_array($ab); 

		
		
		$stats = $this->_stats();
		$maxPage = $stats['loading']['pages'];

		$editionPages = $stats['loading']['pages'];


		$blank = array(
			"page"   => 0,
			"locked"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => "",
			"colour_l" => "",
		    "pdf"=>""
		);




		$r = array();
		$placedCount = 0;
		foreach ($recordsRaw as $record){
			if ($record['pageID']) {
				$a = array();
				$a['ID'] = $record['ID'];
				$a['title'] = $record['title'];
				$a['cm'] = $record['cm']+0;
				$a['pageID'] = $record['pageID'];
				$a['page'] = $record['page'];
				$a['type'] = $record['type'];
				$a['type_icon'] = $record['type_icon'];
				$a['placed'] = $record['placed'];

				
				$r[$record['pageID']][] = $a;
			}
			
		}
		$records = $r;
		
		
		
		
		
		
		$colourGroups = array();
		foreach ($user['publication']['colours_group'] as $g){
			$colourGroups[$g['ID']] = $g;
		}



		//test_array($colourGroups);


		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'","page ASC");

		$r = array();
		$lockedPages = 0;
		$recordCount = 0;


		$pageSize = $user['publication']['columnsav'] * $user['publication']['cmav'];
		
		$ab_stats = array();
		
		foreach ($pagesReal as $page){
			
			if ($pageSelected==$page['page']) $pageID = $page['ID'];

			$colour = array(
				"heading"=>"",
				"limit"=>"",
				"icons"=>"",
			);
			if ($page['colourID']){
				if (isset($colourGroups[$page['colourID']])){
					$colour = array(
						"heading"=> $colourGroups[$page['colourID']]['label'],
						"icons"=> strtolower(str_replace(array(" ","&","_"),"",$colourGroups[$page['colourID']]['label'])),
						"limit"=> $colourGroups[$page['colourID']]['colour_string'],
					);
				}


			}
			if ($page['locked']=='1') $lockedPages++;



			
			
			$cm = isset($ab[$page['ID']]['cm'])?$ab[$page['ID']]['cm']:0;
			$percent = 0;
			if ($cm){
				$percent = ($cm / $pageSize) * 100;
			}
			
			$ab_array = array(
				"cm"=>$cm+0,
				"percent"=>number_format($percent, 2),
				"records"=>isset($ab[$page['ID']]['records'])?$ab[$page['ID']]['records']:array()
			);
			
			
			$ab_stats[$page['ID']] = $ab_array;			

			$r[$page['page']] = array(
				"page"   => $page['page'],
				"pdf"   => $page['pdf'],
				"locked"   => $page['locked'],
				"section"=> array(
					"i"=> ($page['sectionID']) ? $page['sectionID'] : "",
					"n"=> ($page['section']) ? $page['section'] : "",
					"c"=> ($page['section_colour']) ? $page['section_colour'] : ""
				),
				"colour" => $colour,
				"records"=>isset($records[$page['ID']])?$records[$page['ID']]:array(),
				"ab"=>$ab_array
			);
			
		}

		
		$pages = array();
		for ($i = 1; $i <= $editionPages; $i++) {
			$p = $blank;
			$p['page'] = $i;
			if (isset($r[$i])){
				$pages[] = $r[$i];
				$recordCount = $recordCount + count($r[$i]['records']);
			} else {
				$pages[] = $p;
			}

		}





		


		$pagesCount = count($pages);



		$spreads = ($pagesCount / 2) + 1;


		$t = array(
			"count"  => $pagesCount,
			"spreads"=> $spreads
		);

		$spread = array();

		$h = 0;


		for ($i = 0; $i < $spreads; $i++) {
			$lh = $h++;
			if (isset($pages[$lh])){

				$page = $pages[$lh];
				$page['side'] = "left";
				//if (isset($spread[$i])) {
					$spread[$i]['pages'][] = $page;
					if ($i > 0) {
						if (isset($pages[$h])) {
							$page = $pages[$h++];
							$page['side'] = "right";

							$spread[$i]['pages'][] = $page;
						}
					}
					$spread[$i]['side'] = (strpos($i / 2, ".")) ? "right" : "left";
					$spread[$i]['index'] = $i;
				//}
			}




		}

		if (count(($spread))) $spread[count($spread) - 1]['pages'][0]['side'] = "right";
		$a = array();
		foreach ($spread as $b) $a[] = $b;
		$spread = $a;

		$pages = array();
		$pages["spreads"] = $spread;
		$pages["count"] = $pagesCount;

		$totals = array(
			"records"=>count($recordsRaw),
			"planned"=>$recordCount
		);
		$loading = $stats['loading'];
		$stats=array();
		
		$stats['loading'] = $loading;
		$stats['records'] = array(
			"total"=>$totals["records"],
			"planned"=>array(
				"r"=>$totals["planned"],
				"p"=>($totals['planned']) ? number_format((($totals["planned"] / $totals["records"]) * 100), 2) : 0
			)
		);

		$stats['records']['locked'] = array(
			"r" => $lockedPages,
			"p" => number_format($lockedPages ? ($lockedPages / count($pagesReal)) * 100 : 0, 2)
		);



		//test_array($ab_stats); 
		

		if ($pageID){
			$return['records'] = isset($records[$pageID])?$records[$pageID]:array();
			$return['ab'] = isset($ab_stats[$pageID])?$ab_stats[$pageID]:array();
		} else {
			$return = $pages;
		}
		
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];
		$return['stats'] = $stats;

		return $GLOBALS["output"]['data'] = $return;
	}


	function _page($page=""){
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$page = ($page)?$page: isset($_REQUEST['page'])?$_REQUEST['page']:"";
		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];
		
		
		$colourGroups = array();
		foreach ($user['publication']['colours_group'] as $g) {
			$colourGroups[$g['ID']] = $g;
		}


		$pagesReal = models\pages::getAll("page='$page' AND global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC, ID DESC");

		if (count($pagesReal)){
			$page = $pagesReal[0];
		
			$pageID = isset($page['ID'])?$page['ID']:"";

			$colour = array(
				"heading"=>"",
				"limit"=>"",
				"icons"=>"",
			);
			if ($page['colourID']){
				if (isset($colourGroups[$page['colourID']])){
					$colour = array(
						"heading"=> $colourGroups[$page['colourID']]['label'],
						"icons"=> strtolower(str_replace(array(" ","&","_"),"",$colourGroups[$page['colourID']]['label'])),
						"limit"=> $colourGroups[$page['colourID']]['colour_string'],
					);
				}
	
	
			}
			$return = array(
				"page"   => $page['page'],
				"locked"   => $page['locked'],
				"section"=> array(
					"i"=> $page['sectionID'],
					"n"=> $page['section'],
					"c"=> $page['section_colour']
				),
				"colour" => $colour,
			);
			
			
			$return = $return +  $this->_pages($pageID);
		} else {
			$return = array(
				"page"   => 0,
				"locked"   => 0,
				"section"=> array(
					"n"=> "",
					"c"=> "",

				),
				"colour" => "",
				"colour_l" => "",

			);
			$r =  $this->_pages();
			
			$return = $return + array(
					"records"=>array(),
					"stats"=>$r['stats']
				);
			
		}
		

		
		

		


		return $GLOBALS["output"]['data'] = $return;
	}
	function _pageOld($page=""){

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$page = ($page)?$page: isset($_REQUEST['page'])?$_REQUEST['page']:"";

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		$blank = array(
			"page"   => 0,
			"locked"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => "",
			"colour_l" => "",

		);


		$colourGroups = array();
		foreach ($user['publication']['colours_group'] as $g) {
			$colourGroups[$g['ID']] = $g;
		}


		$pagesReal = models\pages::getAll("page='$page' AND global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC, ID DESC");

		$page = $pagesReal[0];

		$colour = array(
				"heading"=>"",
				"limit"=>"",
				"icons"=>"",
			);
			if ($page['colourID']){
				if (isset($colourGroups[$page['colourID']])){
					$colour = array(
						"heading"=> $colourGroups[$page['colourID']]['label'],
						"icons"=> strtolower(str_replace(array(" ","&","_"),"",$colourGroups[$page['colourID']]['label'])),
						"limit"=> $colourGroups[$page['colourID']]['colour_string'],
					);
				}


			}



		$r = array(
				"page"   => $page['page'],
				"locked"   => $page['locked'],
				"section"=> array(
					"i"=> $page['sectionID'],
					"n"=> $page['section'],
					"c"=> $page['section_colour']
				),
				"colour" => $colour,
			);


		$pageID = $page['ID'];
		//$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND typeID='1'", "client ASC");
		
		$recordsRaw = models\articles::getAll("(nf_article_newsbook.pID = '$pID' AND nf_article_newsbook.dID='$dID') AND nf_stages.ID='2' AND nf_articles.deleted is null AND pageID = '$pageID'", "title ASC", "",array("pID"=>$pID,"dID"=>$dID));
		
		
		$records = array();
		foreach ($recordsRaw as $record) {
			if ($record['pageID']) {
				$a = array();
				$a['ID'] = $record['ID'];
				$a['title'] = $record['title'];
				$a['cm'] = $record['cm']+0;
				$a['pageID'] = $record['pageID'];
				$a['page'] = $record['page'];
				$a['type'] = $record['type'];
				$a['type_icon'] = $record['type_icon'];
				$a['placed'] = $record['placed'];

				$records[$record['pageID']][] = $a;
			}
		}

		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC");
		$lockedPages = 0;
		foreach ($pagesReal as $page) {

			if ($page['locked'] == '1') $lockedPages++;
		}

		$r['records'] = $records;
		$stats = $this->_stats();
		$stats['records']['locked'] = array(
			"r" => $lockedPages,
			"p" => number_format($lockedPages ? ($lockedPages / count($pagesReal)) * 100 : 0, 2)
		);

		$r['stats'] = $stats;


		$return = $r;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];



		return $GLOBALS["output"]['data'] = $return;
	}
	function _stats($data="") {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		if (!is_array($data)) {
			$data = \apps\ab\models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.deleted is null AND ab_bookings.typeID='1' ");
			$statsData = array();
			$layoutcm = 0;
			foreach ($data as $item){
				$layoutcm = $layoutcm + $item['totalspace'];
				if ($item['checked']=='1') $statsData[] = $item;
			}
			$loading = \apps\ab\models\loading::getLoading($pID, $layoutcm, $currentDate['pages']);
			$stats = \apps\ab\models\record_stats::stats($statsData,array("cm","placed","placed_cm"), $loading['pages']);
			$stats['loading'] = $loading;

		} else {
			$stats = \apps\ab\models\record_stats::stats($data,array("cm"));
			$loading = \apps\ab\models\loading::getLoading($pID, $stats['cm'], $currentDate['pages']);
			$stats = \apps\ab\models\record_stats::stats($data,array("cm","placed","placed_cm"), $loading['pages']);
			$stats['loading'] = $loading;
		}




		return $GLOBALS["output"]['data'] = $stats;

	}

	function _details_page(){
		$page_nr = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];

		$dID = $user['publication']['current_date']['ID'];

		$page = models\pages::getAll("page='$page_nr' AND global_pages.dID = '$dID' AND global_pages.pID='$pID'");

		if (count($page)) {
			$page = $page[0];

		} else {
			$page = models\pages::dbStructure();
			$page['page'] = $page_nr;

			$page['dID'] = $dID;
			$page['cID'] = $user['company']['ID'];
			$page['pID'] = $user['publication']['ID'];
			$page['page'] = $page_nr;
			$page['pdf'] = "";
		}
		//test_array($page); 
		$pageID = $page['ID'];
		$page['a']['edit'] = ($user['permissions']['layout']['editpage'])?1:0;

		$recordsRaw = models\articles::getAll("(nf_article_newsbook.pID = '$pID' AND nf_article_newsbook.dID='$dID') AND nf_stages.ID='2' AND nf_articles.deleted is null AND pageID = '$pageID'", "title ASC", "",array("pID"=>$pID,"dID"=>$dID));
		
		
		//test_array($recordsRaw);
		$records = array();
		$recordCount = 0;
		foreach ($recordsRaw as $record) {
				$a = array();
				$a['ID'] = $record['ID'];
				$a['title'] = $record['title'];
				$a['cm'] = $record['cm'];
				$a['pageID'] = $record['pageID'];
				$a['page'] = $record['page'];
				$a['type'] = $record['type'];
				$a['type_icon'] = $record['type_icon'];
				$a['photosCount'] = $record['photosCount'];
				$a['placed'] = $record['placed'];

			$records[] = $a;
			$recordCount++;
		}

		$page['records']= $records;


		



		$page['stats'] = array(
			"records" => $recordCount,
		);
		$GLOBALS["output"]['data'] = $page;
	}
	function _details_section(){
		$ID = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];

		$dID = $user['publication']['current_date']['ID'];







		$section = new models\sections();
		$section = $section->get($ID);
		$pages = models\pages::getAll("sectionID='".$section['ID']."' AND global_pages.dID = '$dID' AND global_pages.pID='$pID'");

		$cm = 0;
		$records = 0;
		$n = array();
		$pageSize = $user['publication']['cmav'] * $user['publication']['columnsav'];
		foreach ($pages as $page){

			$n[] = $page;
		}
		$pages = $n;


		$totalAVspace = $pageSize * count($pages);
		$loading = ($cm)? ($cm / $totalAVspace)*100:0;
		$loading = number_format($loading,2);
		
		$stats = array(
			"pages"=>count($pages),
			"cm"=> $cm,
			"records"=> $records,
			"loading"=> $loading
		);
		$return = $section;
		$return['pages'] = $pages;
		$return['stats'] = $stats;

		$GLOBALS["output"]['data'] = $return;
	}


}
