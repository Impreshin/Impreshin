<?php
class pagination {
	public function __construct() {
	}

	public function calculate_pages($total_rows, $rows_per_page, $page_num) {
		$arr = array();
		// calculate last page
		$last_page = ceil($total_rows / $rows_per_page);
		// make sure we are within limits
		$page_num = (int)$page_num;
		if ($page_num < 1) {
			$page_num = 1;
		} elseif ($page_num > $last_page) {
			$page_num = $last_page;
		}
		$upto = ($page_num - 1) * $rows_per_page;
		$arr['limit'] = 'LIMIT ' . $upto . ',' . $rows_per_page;
		$arr['current'] = $page_num;
		if ($page_num == 1) $arr['previous'] = $page_num; else
			$arr['previous'] = $page_num - 1;
		if ($page_num == $last_page) $arr['next'] = $last_page; else
			$arr['next'] = $page_num + 1;
		$arr['last'] = $last_page;
		$arr['info'] = 'Page (' . $page_num . ' of ' . $last_page . ')';
		$arr['pages'] = $this->get_surrounding_pages($page_num, $last_page, $arr['next']);
		return $arr;
	}

	function get_surrounding_pages($page_num, $last_page, $next) {
		$arr = array();
		$show = 19; // how many boxes
		// at first
		if ($page_num == 1) {
			// case of 1 page only
			if ($next == $page_num) return array(1);
			for ($i = 0; $i < $show; $i++) {
				if ($i == $last_page) break;
				$arr[] = array("p"=>$i + 1);
			}
			return $arr;
		}
		// at last
		if ($page_num == $last_page) {
			$start = $last_page - $show;
			if ($start < 1) $start = 0;
			for ($i = $start; $i < $last_page; $i++) {
				$arr[] = array("p"=>$i + 1);
			}
			return $arr;
		}





		// at middle
		$start = $page_num - $show / 2;
		if ($start < 1) $start = 0;
		if ($last_page- $page_num == 1){
			$arr[] = array("p"=> floor($start));
		}
		for ($i = $start; $i < $page_num; $i++) {
			$arr[] = array("p"=>floor($i + 1));
		}

		for ($i = ($page_num + 1); $i < ($page_num + $show / 2 + 1); $i++) {
			if ($i == ($last_page + 1)) break;
			$arr[] = array("p"=>floor($i));
		}

		$a = array();

		$i = 0;
		$t = 0;
		foreach ($arr as $page) {
			$t = $i++;
			if ($t <= $show && $page['p'] != 0) $a[] = $page;
		}
		$i = 0;
		if (count($a)<$show){
			for ($i = count($a)+1; $i <= $show; $i++) {
				$a[] = array("p"=> floor($i));
			}
		}

		$b = array();
		$i = 0;
		foreach ($a as $page) {
			$t = $i++;
			//$page['show'] = $show;
			if ($t < $show && $page['p'] != 0) $b[] = $page;
		}


		return $b;
	}
}
