<?php

namespace models\nf;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class articles {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];
		$currentDate = $user['publication']['current_date'];

		$currentDate = $currentDate['publish_date'];

		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		$dID = $user['publication']['current_date']['ID'];

		$from = self::list_from();
		//test_array($currentDate);

		$result = F3::get("DB")->exec("
			SELECT nf_articles.*,
			nf_article_types.type as type,nf_article_types.labelClass as type_labelClass,
				(SELECT fullName FROM global_users WHERE global_users.ID = nf_articles.authorID) as author,
				nf_categories.category as category,
				nf_stages.stage as stage,nf_stages.labelClass as stage_labelClass,
				(SELECT count(ID) FROM nf_files WHERE nf_files.aID = nf_articles.ID AND type = '1') as photos,
				(SELECT count(ID) FROM nf_files WHERE nf_files.aID = nf_articles.ID AND type='2') as files,
				if ((SELECT count(ID) FROM nf_article_newsbook WHERE nf_article_newsbook.aID = nf_articles.ID AND nf_article_newsbook.dID = '$dID' LIMIT 0,1)<>0,1,0) as currentNewsbook,
				if ((SELECT count(ID) FROM nf_article_newsbook WHERE nf_article_newsbook.aID = nf_articles.ID LIMIT 0,1)<>0,1,0) as inNewsBook
			$from
			WHERE nf_articles.ID = '$ID';
		");


		if (count($result)) {
			$return = ($result[0]);
			$return['datein_D'] = date("d F Y H:m:s", strtotime($return['datein']));
			$return['logs'] = articles::getLogs($return['ID']);
			$return['files'] = articles::getFiles($return['ID']);
			$return['newsbooks'] = articles::getNewsbooks($return['ID']);

		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getNewsbooks($ID){
		$timer = new timer();
		$user = F3::get("user");
		$result = F3::get("DB")->exec("
			SELECT global_publications.publication, global_dates.publish_date
			FROM (nf_article_newsbook INNER JOIN global_dates ON nf_article_newsbook.dID = global_dates.ID) INNER JOIN global_publications ON nf_article_newsbook.pID = global_publications.ID
			WHERE nf_article_newsbook.aID = '$ID';

		");
		$return = $result;


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getFile($ID) {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$result = F3::get("DB")->exec("
			SELECT nf_files.*
			FROM nf_files
			WHERE nf_files.ID = '$ID';
		");


		if (count($result)) {
			$return = ($result[0]);
		} else {
			$table = F3::get("DB")->exec("EXPLAIN nf_files;");
			$result = array();
			foreach ($table as $key => $value) {
				$result[$value["Field"]] = "";
			}


			$return = $result;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function getFiles($ID) {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$result = F3::get("DB")->exec("
			SELECT nf_files.*
			FROM nf_files
			WHERE nf_files.aID = '$ID';
		");

		$return = $result;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function getEdits($ID,$returnarticles=false){
		$timer = new timer();

		$select = array(
			"nf_articles_edits.ID","datein","fullName","percent","percent_orig","stage","stageID"
		);

		$select = implode(", ",$select);
		$result = F3::get("DB")->exec("
			SELECT $select
			FROM (nf_articles_edits INNER JOIN nf_stages ON nf_articles_edits.stageID = nf_stages.ID) INNER JOIN global_users ON nf_articles_edits.uID = global_users.ID
			WHERE nf_articles_edits.aID = '$ID';
		");

		$return = $result;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	private static function list_from() {
		return "FROM ((((nf_articles INNER JOIN nf_article_types ON nf_articles.typeID = nf_article_types.ID) INNER JOIN global_users AS global_users_author ON nf_articles.authorID = global_users_author.ID) LEFT JOIN global_users AS global_users_lockedBy ON nf_articles.lockedBy = global_users_lockedBy.ID) INNER JOIN nf_categories ON nf_articles.categoryID = nf_categories.ID) INNER JOIN nf_stages ON nf_articles.stageID = nf_stages.ID";
	}

	public static function getCount($where = "") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		$from = self::list_from();

		$return = F3::get("DB")->exec("
			SELECT count(nf_articles.ID) as records
			$from

			$where
		");
		if (count($return)) {
			$return = $return[0]['records'];
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getSelect($select, $where = "", $orderby, $groupby = "") {
		/*
				return array(
					"select"=>$select,
					"where"=>$where,
					"orderby"=>$orderby,
					"group"=>$groupby
				);
		*/
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($groupby) {
			$groupby = " GROUP BY " . $groupby;
		}
		$from = self::list_from();

		$return = F3::get("DB")->exec("
			SELECT $select
			$from


			$where
			$groupby
			$orderby
		");


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $grouping = array(
		"g" => "none",
		"o" => "ASC"
	), $ordering = array("c" => "heading", "o" => "ASC"), $options = array("limit" => "")) {
		$timer = new timer();
		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		$dID = $user['publication']['current_date']['ID'];

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$order = articles::order($grouping, $ordering);

		$orderby = $order['order'];
		$select = $order['select'];
		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($select) {
			$select = " ," . $select;
		}

		if ($options['limit']) {
			if (strpos($options['limit'], "LIMIT") == -1) {
				$limit = " LIMIT " . $options['limit'];
			} else {
				$limit = $options['limit'];
			}

		} else {
			$limit = " ";
		}


		//test_array($orderby);
		$from = self::list_from();
		//test_array($order);
		$result = F3::get("DB")->exec("
			SELECT nf_articles.*,
				nf_article_types.type as type,nf_article_types.labelClass as type_labelClass,
				(SELECT fullName FROM global_users WHERE global_users.ID = nf_articles.authorID) as author,
				nf_categories.category as category,
				nf_stages.stage as stage,
				(SELECT count(ID) FROM nf_files WHERE nf_files.aID = nf_articles.ID AND type = '1') as photos,
				(SELECT count(ID) FROM nf_files WHERE nf_files.aID = nf_articles.ID AND type='2') as files,
				if ((SELECT count(ID) FROM nf_article_newsbook WHERE nf_article_newsbook.aID = nf_articles.ID AND nf_article_newsbook.dID = '$dID' LIMIT 0,1)<>0,1,0) as currentNewsbook,
				if ((SELECT count(ID) FROM nf_article_newsbook WHERE nf_article_newsbook.aID = nf_articles.ID LIMIT 0,1)<>0,1,0) as inNewsBook

			$select

			$from
			$where
			$orderby
			$limit
		");


		$return = $result;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function display($data, $options = array("highlight" => array(), "filter" => array())) {
		$highlight = $options['highlight'];
		$options['highlight']['field'] = (isset($highlight[0]) && $highlight[0]) ? $highlight[0] : "";
		$options['highlight']['value'] = ($options['highlight']['field'] && isset($highlight[1]) && $highlight[1]) ? $highlight[1] : "";

		$filter = $options['filter'];
		$options['filter']['field'] = (isset($filter[0]) && $filter[0]) ? $filter[0] : "";
		$options['filter']['value'] = ($options['filter']['field'] && isset($filter[1]) && $filter[1]) ? $filter[1] : "";


		$timer = new timer();
		$user = F3::get("user");
		$permissions = $user['permissions'];
		if (is_array($data)) {
			$a = array();


			foreach ($data as $item) {


				$a[] = ($item);
			}
			$data = $a;

		}


		$return = array();
		$a = array();
		$groups = array();


		foreach ($data as $record) {
			if (isset($user['permissions']['fields'])) {
				foreach ($user['permissions']['fields'] as $key => $value) {
					if ($value == 0) {
						if (isset($record[$key])) unset($record[$key]);
						if (isset($record[$key . "_C"])) unset($record[$key . "_C"]);
					}
				}
			}

			$showrecord = true;


			if ($options['highlight']['field']) {
				if ($options['highlight']['value']) {
					$record['highlight'] = ($record[$options['highlight']['field']] == $options['highlight']['value']) ? 1 : 0;
				} else {
					$record['highlight'] = $record[$options['highlight']['field']];
				}
			}


			if ($options['filter']['field']) {
				if ($options['filter']['value']) {
					if ($record[$options['filter']['field']] == $options['filter']['value']) {
						$showrecord = true;
					} else {
						$showrecord = false;
					}
				}
			}


//echo $record[$options["highlight"]] . " | " . $showrecord . " | " . $options["filter"]. "<br>";
			if ($showrecord) {
				if (!isset($a[$record['heading']])) {
					$groups[] = $record['heading'];

					$arr = array(
						"heading" => $record['heading'],
						"count"   => "",
						"cm"      => 0

					);
					$arr['groups'] = "";
					$arr['records'] = "";


					$a[$record['heading']] = $arr;
				}

				if ($record['typeID'] == '1') {
					$a[$record['heading']]["cm"] = $a[$record['heading']]["cm"] + $record['cm'];
				}

				if (isset($permissions['lists']['fields'])) {
					foreach ($permissions['lists']['fields'] as $key => $value) {
						if ($value == 0) {
							if (isset($record[$key])) unset($record[$key]);
							if (isset($record[$key . "_C"])) unset($record[$key . "_C"]);
						}
					}
				}


				$a[$record['heading']]["records"][] = $record;
			}
		}

		$return = array();


//exit();
		foreach ($a as $record) {
			$record['count'] = count($record['records']);


			$record['groups'] = $groups;
			$return[] = $record;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;

	}

	private static function order($grouping, $ordering) {

		$o = explode(".", $ordering['c']);
		$a = array();
		foreach ($o as $b) {
			$a[] = "`" . $b . "`";
		}
		$a = implode(".", $a);
		$orderby = " " . $a . " " . $ordering['o'];
		$arrange = "";
		$ordering = $grouping['o'];
		switch ($grouping['g']) {
			case "type":
				$orderby = "COALESCE(nf_article_types.orderby,99999) $ordering, " . $orderby;
				$arrange = "nf_article_types.type as heading";
				break;


			case "none":
				$orderby = "" . $orderby . ",nf_articles.datein DESC ";
				$arrange = "'None' as heading";
				break;
			case "author":
				$orderby = "COALESCE(global_users_author.fullName,99999) $ordering," . $orderby;
				$arrange = "COALESCE(global_users_author.fullName,'None') as heading";
				break;
			case "newsbook":
				$orderby = "COALESCE(global_users_author.fullName,99999) $ordering," . $orderby;
				$arrange = "COALESCE(global_users_author.fullName,'None') as heading";
				break;

		}

		//test_array($grouping);

		return array(
			"order" => $orderby,
			"select" => $arrange
		);
	}

	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();

		$user = F3::get("user");
		$userID = $user['ID'];


		$a = new Axon("nf_articles");
		$a->load("ID='$ID'");

		if (!$a->dry()) {
			$a->deleted = "1";
			$a->deleted_userID = $userID;
			$a->deleted_user = $user['fullName'];
			$a->deleted_date = date("Y-m-d H:i:s");
			$a->deleted_reason = ($reason);

			$a->save();
			$changes = array(
				array(
					"k" => "Deleted",
					"v" => "1",
					"w" => ""
				),
				array(
					"k" => "deleted_user",
					"v" => $user['fullName'],
					"w" => ""
				),
				array(
					"k" => "deleted_reason",
					"v" => $reason,
					"w" => ""
				)
			);

			articles::logging($a->ID, $changes, "Article Deleted");
		}


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "deleted";
	}

	public static function save($ID = "", $values = array(), $opts = array("dry" => true, "section" => "booking")) {

		test_array($values);
		$raw = $values;
		$values = $values['values'];
		$timer = new timer();
		$lookupColumns = array();
		/*
		$lookupColumns["dID"] = array("sql"=>"(SELECT publish_date FROM global_dates WHERE ID = '{val}')","col"=>"publish_date","val"=>"");
		$lookupColumns["placingID"] = array("sql"=>"(SELECT placing FROM ab_placing WHERE ID = '{val}')","col"=>"placing","val"=>"");
		$lookupColumns["categoryID"] = array("sql"=>"(SELECT `category` FROM ab_categories WHERE ID = '{val}')","col"=>"category","val"=>"");
		$lookupColumns["marketerID"] = array("sql"=>"(SELECT `marketer` FROM ab_marketers WHERE ID = '{val}')","col"=>"marketer","val"=>"");
		$lookupColumns["colourID"] = array("sql"=>"(SELECT `colour` FROM ab_colour_rates WHERE ID = '{val}')","col"=>"colour","val"=>"");
		$lookupColumns["material_productionID"] = array("sql"=>"(SELECT `production` FROM ab_production WHERE ID = '{val}')","col"=>"production","val"=>"");
		$lookupColumns["remarkTypeID"] = array("sql"=>"(SELECT `remarkType` FROM ab_remark_types WHERE ID = '{val}')","col"=>"remarkType","val"=>"");
		$lookupColumns["checked_userID"] = array("sql"=>"(SELECT `fullName` FROM global_users WHERE ID = '{val}')","col"=>"checked_user","val"=>"");
		$lookupColumns["material_source"] = array("sql"=>"(CASE '{val}' WHEN 1 THEN 'Production' WHEN 2 THEN 'Supplied' END)","col"=>"material_source","val"=>"");
		$lookupColumns["material_status"] = array("sql"=>"(CASE '{val}' WHEN 1 THEN 'Ready' WHEN 0 THEN 'Not Ready' END)","col"=>"material_status","val"=>"");
		$lookupColumns["checked"] = array("sql"=>"(CASE '{val}' WHEN 1 THEN 'Checked' WHEN 0 THEN 'Not Checked' END)","col"=>"checked","val"=>"");
		$lookupColumns["pageID"] = array("sql"=>"(SELECT TRUNCATE(`page`,0) FROM global_pages WHERE ID = '{val}')","col"=>"page","val"=>"");
		$lookupColumns["accountID"] = array("sql"=>"(SELECT concat(accNum,' | ',account) FROM ab_accounts WHERE ID = '{val}')","col"=>"Account","val"=>"");
		*/
		$lookup = array();


		$a = new Axon("nf_articles");
		$a->load("ID='$ID'");
		$user = F3::get("user");




		/*
				if (($cfg['material'] && $user['company']['ab_upload_material'] == '1' && $user['publication']['ab_upload_material'] == '1') && !$a->dry()) {
					if ($a->material_file_store){
						$oldFolder = $cfg['folder'] . "ab/" . $cID . "/" . $a->pID . "/" . $a->dID . "/material/";


						if ((isset($values['material_status']) && $values['material_status'] == "0" && $a->material_file_store) || (isset($values['material_file_store']) && $a->material_file_store != $values['material_file_store'])) {

							if (file_exists($oldFolder . $a->material_file_store)) {
								@unlink($oldFolder . $a->material_file_store);
							}
						} else {






						if (isset($values['dID'])) {

							//echo "old: " . $oldFolder . $a->material_file_store . "<br>";
							if (file_exists($oldFolder. $a->material_file_store)){



								$newFolder = $cfg['folder'] . "ab/" . $cID . "/" . $a->pID . "/" . $values['dID'] . "/material/";


								//echo "new: ". $newFolder . $a->material_file_store ."<br>";

								if (!file_exists($newFolder)) @mkdir($newFolder, 0777, true);

								@rename($oldFolder . $a->material_file_store, $newFolder . $a->material_file_store);
							}


						}
						}



					}


				}
		*/


		$changes = array();
		$material = false;
		foreach ($values as $key => $value) {

			$cur = $a->$key;
			if ($cur != $value) {
				if (isset($lookupColumns[$key])) {
					$lookupColumns[$key]['val'] = $value;
					$lookupColumns[$key]['was'] = $cur;
					$lookup[] = $lookupColumns[$key];
				} else {
					$w = $cur;
					$v = $value;
					$changes[] = array(
						"k" => $key,
						"v" => $v,
						"w" => str_replace("0000-00-00 00:00:00", "", $w)
					);
				}

			}
			$a->$key = $value;
		}

		if ($opts['dry'] || !$a->dry()) {
			$a->save();
		}


		if (!$ID) {
			$label = "Article Added";
			$ID = $a->_id;
		} else {
			$label = "Article Edited";
		}

		$sql = "SELECT 1 ";

		foreach ($lookup as $col) {
			$sql .= ", " . str_replace("{val}", $col['val'], $col['sql']) . " AS " . $col['col'];
			$sql .= ", " . str_replace("{val}", $col['was'], $col['sql']) . " AS " . $col['col'] . "_was";
		}


		$v = F3::get("DB")->exec($sql);
		$v = $v[0];
		foreach ($lookup as $col) {
			$changes[] = array(
				"k" => $col['col'],
				"v" => $v[$col['col']],
				"w" => $v[$col['col'] . "_was"]
			);
		}


		//if (count($changes)) articles::logging($ID, $changes, $label);


		$n = $ID;

		$p = new Axon("nf_articles_edits");
		$p->aID = $ID;
		$p->uID = $user['ID'];
		$p->patch = $raw['patch'];
		$p->percent =
		$p->percent_orig =
		$p->stageID =




		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $n;
	}

	private static function getLogs($ID) {
		$timer = new timer();

		$return = F3::get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =nf_articles_logs.userID ) AS fullName FROM nf_articles_logs WHERE aID = '$ID' ORDER BY datein DESC");
		$a = array();
		foreach ($return as $record) {
			$record['log'] = json_decode($record['log']);
			$a[] = $record;
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $a;
	}

	private static function logging($ID, $log = array(), $label = "Log") {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$log = mysql_escape_string(json_encode($log));
		//	$log = str_replace("'", "\\'", $log);


		F3::get("DB")->exec("INSERT INTO nf_articles_logs (`aID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
	}

	public static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN nf_articles;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["files"] = array();
		$result["newsbooks"] = array();
		return $result;
	}
}