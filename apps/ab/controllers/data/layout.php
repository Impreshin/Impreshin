<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
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

		$placingID = (isset($_REQUEST['placingID']) && $_REQUEST['placingID'] != "") ? $_REQUEST['placingID'] : $settings['placingID'];

		$stats = $this->_stats();

		$maxPage = $stats['loading']['pages'];

		$values = array();
		$values["layout"] = array(
			"placingID" => array(
				$pID=>$placingID,
			)
		);


		models\settings::save($values);


		$records = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND ab_bookings.placingID='$placingID' AND (page is null OR page > '$maxPage')");
		$rawBookings= $records;
		$records = models\bookings::display($records);


		if (count($records)) $records = $records[0]['records'];
		$return = array();
		$return['placing'] = $this->f3->get("DB")->exec("SELECT ID, placing, (SELECT count(ab_bookings.ID) FROM ab_bookings LEFT JOIN global_pages ON ab_bookings.pageID = global_pages.ID WHERE placingID =ab_placing.ID AND ab_bookings.pID = '$pID' AND ab_bookings.dID = '$dID' AND deleted is null AND checked = '1' AND (page is null OR page > '$maxPage')) AS recordCount FROM ab_placing WHERE pID = '$pID' ORDER BY orderby");

		$cols = array(
			"ID",
			"client",
			"colour",
			"colourLabel",
			"colourID",
			"col",
			"cm",
			"totalspace",
			"category",
			"remark",
			"remarkTypeID",
			"userName",
			"checked_user",
			"material_status",
			"material_approved",
			"remarkType",
			"remarkTypeLabelClass",
		);


		$r = array();
		foreach ($records as $record){
			$b = array();
			foreach ($cols as $col){
				$b[$col] = $record[$col];
			}
			$r[] = $b;
		}
		$records = $r;

		$return['records'] = $records;
		$return['placingID'] = $placingID;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];


		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC");
		$lockedPages = 0;
		foreach ($pagesReal as $page) {

			if ($page['locked'] == '1') $lockedPages++;
		}

		$stats['records']['locked'] = array(
			"r" => $lockedPages,
			"p" => number_format($lockedPages ? ($lockedPages / count($pagesReal)) * 100 : 0, 2)
		);

		$return['stats'] = $stats;




		return $GLOBALS["output"]['data'] = $return;
	}

	function _pages() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND checked = '1' AND ab_bookings.typeID='1' ", "client ASC");
		$stats = $this->_stats();


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
			"pdf" => "",
		    "records"=>array()
		);




		$bookings = array();
		foreach ($bookingsRaw as $booking){
			
			$placed="0";
			if ($booking['x_offset']!==null&&$booking['y_offset']!==null){
				$placed = '1';
			}
			
			if ($booking['pageID']) {
				$a = array();
				$a['ID'] = $booking['ID'];
				$a['client'] = $booking['client'];
				$a['colourID'] = $booking['colourID'];
				$a['col'] = $booking['col'] + 0;
				$a['cm'] = $booking['cm'] + 0;
				$a['totalspace'] = $booking['totalspace'] + 0;
				$a['pageID'] = $booking['pageID'];
				$a['page'] = $booking['page'];
				$a['material'] = $booking['material'];
				$a['material_approved'] = $booking['material_approved'];
				$a['planned'] = $placed;

				$bookings[$booking['pageID']][] = $a;
			}
		}
		$colourGroups = array();
		foreach ($user['publication']['colours_group'] as $g){
			$colourGroups[$g['ID']] = $g;
		}



		//test_array($colourGroups);


		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'","page ASC");

		$r = array();
		$lockedPages = 0;
		foreach ($pagesReal as $page){

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





			$r[$page['page']] = array(
				"page"   => $page['page'],
				"locked"   => $page['locked'],
				"section"=> array(
					"i"=> ($page['sectionID']) ? $page['sectionID'] : "",
					"n"=> ($page['section']) ? $page['section'] : "",
					"c"=> ($page['section_colour']) ? $page['section_colour'] : ""
				),
				"colour" => $colour,
				"percent"=> $page['percent'],
				"cm"     => $page['cm']+0,
				"pdf"     => $page['pdf'],
				"records"=>isset($bookings[$page['ID']])?$bookings[$page['ID']]:array()
			);
		}

		$pages = array();
		for ($i = 1; $i <= $editionPages; $i++) {
			$p = $blank;
			$p['page'] = $i;
			if (isset($r[$i])){
				$pages[] = $r[$i];
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

		$stats['records']['locked'] = array(
			"r"=>$lockedPages,
			"p"=> number_format($lockedPages? ($lockedPages / $pagesCount)*100:0,2)
		);



		$return = $pages;
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


		$blank = array(
			"page"   => 0,
			"locked"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => "",
			"colour_l" => "",
			"percent"=>0,
			"cm"=>0

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
				"percent"=> $page['percent'],
				"cm"     => $page['cm']+0
			);


		$pageID = $page['ID'];
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND ab_bookings.typeID='1'", "client ASC");
		$bookings = array();
		foreach ($bookingsRaw as $booking) {
			$placed="0";
			if ($booking['x_offset']!==null&&$booking['y_offset']!==null){
				$placed = '1';
			}
			
			if ($booking['pageID'] == $pageID) {
				$a = array();
				$a['ID'] = $booking['ID'];
				$a['client'] = $booking['client'];
				$a['colourID'] = $booking['colourID'];
				$a['col'] = $booking['col'] + 0;
				$a['cm'] = $booking['cm'] + 0;
				$a['totalspace'] = $booking['totalspace'] + 0;
				$a['pageID'] = $booking['pageID'];
				$a['page'] = $booking['page'];
				$a['material'] = $booking['material'];
				$a['material_approved'] = $booking['material_approved'];
				$a['material_status'] = $booking['material_status'];
				$a['planned'] = $placed;

				$bookings[] = $a;
			}
		}

		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC");
		$lockedPages = 0;
		foreach ($pagesReal as $page) {

			if ($page['locked'] == '1') $lockedPages++;
		}

		$r['records'] = $bookings;
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
			$data = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.deleted is null AND ab_bookings.typeID='1' ");
			$statsData = array();
			$layoutcm = 0;
			foreach ($data as $item){
				$layoutcm = $layoutcm + $item['totalspace'];
				if ($item['checked']=='1') $statsData[] = $item;
			}
			$loading = models\loading::getLoading($pID, $layoutcm, $currentDate['pages']);
			$stats = models\record_stats::stats($statsData,array("cm","placed","placed_cm"), $loading['pages']);
			$stats['loading'] = $loading;

		} else {
			$stats = models\record_stats::stats($data,array("cm"));
			$loading = models\loading::getLoading($pID, $stats['cm'], $currentDate['pages']);
			$stats = models\record_stats::stats($data,array("cm","placed","placed_cm"), $loading['pages']);
			$stats['loading'] = $loading;
		}




		return $GLOBALS["output"]['data'] = $stats;

	}

	function _details_page($val=""){
		if ($val){
			$page_nr = $val;
		} else {
			$page_nr = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
		}
		
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
		$pageID = $page['ID'];
		$page['a']['edit'] = ($user['permissions']['layout']['editpage'])?1:0;


		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND ab_bookings.typeID='1' AND pageID='$pageID'", "client ASC");
		$bookings = array();
		$cm = 0;
		$records = 0;
		foreach ($bookingsRaw as $booking) {
			//test_array($booking); 

			$placed="0";
			if ($booking['x_offset']!==null&&$booking['y_offset']!==null){
				$placed = '1';
			}
			
			
				$a = array();
				$a['ID'] = $booking['ID'];
				$a['client'] = $booking['client'];
				$a['colour'] = $booking['colour'];
				$a['colourLabel'] = $booking['colourLabel'];
				$a['col'] = $booking['col'] + 0;
				$a['cm'] = $booking['cm'] + 0;
				$a['totalspace'] = $booking['totalspace'] + 0;
				$a['pageID'] = $booking['pageID'];
				$a['page'] = $booking['page'];
				$a['material'] = $booking['material'];
				$a['material_approved'] = $booking['material_approved'];
				$a['material_status'] = $booking['material_status'];
				//$a['material_file_filename'] = $booking['material_file_filename'];
				$a['material_file_store'] = $booking['material_file_store'];
				$a['x_offset'] = $booking['x_offset']?$booking['x_offset']+0:"";
				$a['y_offset'] = $booking['y_offset']?$booking['y_offset']+0:"";
				$a['planned'] = $placed;

				$bookings[] = $a;
			if ($a['cm']) $cm = $cm + $a['totalspace'] + 0;
			$records++;
		}

		$page['records']= $bookings;


		$pageSize = $user['publication']['cmav'] * $user['publication']['columnsav'];
		$totalAVspace = $pageSize;
		$loading = ($cm) ? ($cm / $totalAVspace) * 100 : 0;
		$loading = number_format($loading, 2);



		$page['stats'] = array(
			"cm"=>$cm,
			"records" => $records,
			"loading" => $loading
		);
		return $GLOBALS["output"]['data'] = $page;
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
			if ($page['cm']) $cm = $cm+$page['cm'];
			if ($page['records']) $records = $records+$page['records'];
			$page['loading'] = number_format(($page['cm']) ? ($page['cm'] / $pageSize) * 100 : 0, 2);

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
