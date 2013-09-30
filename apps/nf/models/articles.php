<?php

namespace apps\nf\models;
use \timer as timer;

class articles {
	private $classname;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();
	}

	private static function _from($options=array()) {

		$newsbook_sql = "";
		if (isset($options['pID'])&&$options['pID'] && isset($options['dID'])&&$options['dID']) {
			$newsbook_sql = "AND (nf_article_newsbook.pID = '".$options['pID']."' AND nf_article_newsbook.dID = '".$options['dID']."')";

		}
		
		$return = "(((((((((( nf_articles  INNER JOIN global_users ON nf_articles.authorID = global_users.ID) INNER JOIN nf_categories ON nf_articles.categoryID = nf_categories.ID) INNER JOIN nf_article_types ON nf_articles.typeID = nf_article_types.ID) INNER JOIN nf_stages ON nf_articles.stageID = nf_stages.ID) INNER JOIN nf_priorities ON nf_articles.priorityID = nf_priorities.ID) LEFT JOIN global_users AS global_users_1 ON nf_articles.locked_uID = global_users_1.ID) LEFT JOIN global_users AS global_users_3 ON nf_articles.rejected_uID = global_users_3.ID) LEFT JOIN global_users AS global_users_2 ON nf_articles.deleted_userID = global_users_2.ID) LEFT JOIN (((nf_article_newsbook LEFT JOIN global_publications ON nf_article_newsbook.pID = global_publications.ID $newsbook_sql) LEFT JOIN global_dates ON nf_article_newsbook.dID = global_dates.ID $newsbook_sql) LEFT JOIN global_pages ON nf_article_newsbook.pageID = global_pages.ID $newsbook_sql) ON nf_articles.ID = nf_article_newsbook.aID ) ) ";

		return $return;
	}

	function get($ID,$options=array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		//$currentDate = $user['publication']['current_date'];
		//$currentDate = $currentDate['publish_date'];
		//test_array($currentDate);
		$from = self::_from($options);
		$newsbook_sql = $newsbook_select = "";
		if (isset($options['pID'])&&$options['pID'] && isset($options['dID'])&&$options['dID']) {
			$newsbook_sql = "AND (p_nb.pID = '".$options['pID']."' AND p_nb.dID = '".$options['dID']."')";
			$newsbook_select = "(SELECT FLOOR(global_pages.page) FROM nf_article_newsbook p_nb INNER JOIN global_pages ON p_nb.pageID = global_pages.ID WHERE p_nb.aID = nf_articles.ID $newsbook_sql) as page, ";

		}
		
		
		$result = $f3->get("DB")->exec("
			SELECT
				nf_articles.*,
				nf_article_types.type AS type,
				nf_article_types.icon AS type_icon,
				nf_categories.category AS category,
				global_users.fullName AS author,
				global_users.ID AS authorID,
				nf_stages.ID AS stageID,
				nf_stages.stage AS stage,
				nf_stages.labelClass AS stageLabelClass,
				global_users_1.fullName AS locked_fullName,
				global_users_3.fullName AS rejected_fullName,
				nf_priorities.priority AS priority,
				if(global_users_1.ID,1,0) AS locked,
				if(nf_stages.ID='2',1,0) AS ready,
				if(global_dates.ID, 1, 0) AS in_newsbook,
				if((SELECT count(p_nb.ID) FROM nf_article_newsbook p_nb INNER JOIN global_pages ON p_nb.pageID = global_pages.ID WHERE p_nb.aID = nf_articles.ID AND p_nb.placed='1' $newsbook_sql),1,0) as placed,
				$newsbook_select
				(SELECT body FROM nf_articles_body WHERE nf_articles.ID = aID AND stageID ='1' ORDER BY ID DESC  LIMIT 0,1) as draft,
	(SELECT body FROM nf_articles_body WHERE nf_articles.ID = aID AND nf_articles_body.ID  ORDER BY ID DESC LIMIT 0,1)  as body



			FROM $from


			WHERE nf_articles.ID = '$ID';

		"
		);
		if (count($result)) {
			$return = $result[0];
			$files = files::getAll("aID='" . $return['ID'] . "'", "ID DESC");
			$f = array();
			foreach ($files as $file) {
				$f[] = $file;
			}
			$return['media'] = files::display($f);
			
			$comments = comments::getAll("aID='" . $return['ID'] . "'", "ID DESC");
			$return['commentCount'] = count($comments);
			$return['comments'] = comments::display($comments);
			
			//$return['logs'] = articles::getLogs($return['ID']);
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}
	public static function getNewsbooks($aID,$orderby){
		$timer = new timer();
		$f3 = \Base::instance();
		if ($orderby) {
			$orderby = "ORDER BY " . $orderby . "";
		} else {
			$orderby = " ";
		}

		$return = $f3->get("DB")->exec("
			SELECT nf_article_newsbook.*, global_publications.ID AS pID, global_publications.publication, global_dates.ID AS dID, global_dates.publish_date, global_pages.ID AS pageID, FLOOR(global_pages.page) AS page, global_users.fullName
			FROM (((nf_article_newsbook LEFT JOIN global_pages ON nf_article_newsbook.pageID = global_pages.ID) INNER JOIN global_publications ON nf_article_newsbook.pID = global_publications.ID) INNER JOIN global_dates ON nf_article_newsbook.dID = global_dates.ID) INNER JOIN global_users ON nf_article_newsbook.uID = global_users.ID
			WHERE nf_article_newsbook.aID = '$aID'
			$orderby
		"
		);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
		
	}

	public static function getAll_count($where = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$from = self::_from();
		$return = $f3->get("DB")->exec("
			SELECT count(nf_articles.ID) AS records
			FROM $from
			$where
		"
		);
		if (count($return)) {
			$return = $return[0]['records'];
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	public static function getAll_select($select, $where = "", $orderby, $groupby = "") {
		/*
						test_array(array(
							"select"=>$select,
							"where"=>$where,
							"orderby"=>$orderby,
							"group"=>$groupby
						));
		*/
		$timer = new timer();
		$f3 = \Base::instance();
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
		$from = self::_from();
		$return = $f3->get("DB")->exec("
			SELECT $select
			FROM $from
			$where
			$groupby
			$orderby
		"
		);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	public static function getAll($where = "", $grouping = array("g" => "none", "o" => "ASC"), $ordering = array("c" => "datein", "o" => "DESC"), $options = array("limit" => "","pID"=>"","dID"=>"" )) {
		$f3 = \Base::instance();
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		if (!$grouping){
			$grouping = array("g" => "none", "o" => "ASC");
		}
		if (!$ordering){
			$ordering = array("c" => "datein", "o" => "DESC");
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
		if (isset($options['limit'])&&$options['limit']) {
			if (strpos($options['limit'], "LIMIT") === false) {
				$limit = " LIMIT " . $options['limit'];
			} else {
				$limit = $options['limit'];
			}
		} else {
			$limit = " ";
		}

		$newsbook_sql = "";
		if (isset($options['pID'])&&$options['pID'] && isset($options['dID'])&&$options['dID']) {
			$newsbook_sql = "AND (p_nb.pID = '".$options['pID']."' AND p_nb.dID = '".$options['dID']."')";
			$newsbook_select = "(SELECT FLOOR(global_pages.page) FROM nf_article_newsbook p_nb INNER JOIN global_pages ON p_nb.pageID = global_pages.ID WHERE p_nb.aID = nf_articles.ID $newsbook_sql) as page, ";
			
		} else {
			$newsbook_select = "(SELECT TRIM(',' FROM TRIM(GROUP_CONCAT(if (g_pages.ID,CONCAT(' ', g_publications.publication, ' (', g_dates.publish_date, ' | ', FLOOR(g_pages.page),')'),'')))) FROM ((nf_article_newsbook nb INNER JOIN global_publications g_publications ON nb.pID = g_publications.ID) INNER JOIN global_dates g_dates ON nb.dID = g_dates.ID) LEFT JOIN global_pages g_pages ON nb.pageID = g_pages.ID WHERE nb.aID = nf_articles.ID)  as page, ";
		}

		//test_array($usepub_placed); 
		$from = self::_from($options);

		$sql = "
			SELECT DISTINCT
			 	nf_articles.*,
				nf_article_types.type AS type,
				nf_article_types.icon AS type_icon,
				nf_categories.category AS category,
				global_users.fullName AS author,
				global_users.ID AS authorID,
				nf_stages.ID AS stageID,
				nf_stages.stage AS stage,
				nf_stages.labelClass AS stageLabelClass,
				global_users_1.fullName AS locked_fullName,
				global_users_3.fullName AS rejected_fullName,
				nf_priorities.priority AS priority,
				if(global_users_1.ID,1,0) AS locked,
				if(nf_stages.ID='2',1,0) AS ready,
				if(global_dates.ID, 1, 0) AS in_newsbook,
				if((SELECT count(p_nb.ID) FROM nf_article_newsbook p_nb INNER JOIN global_pages ON p_nb.pageID = global_pages.ID WHERE p_nb.aID = nf_articles.ID AND p_nb.placed='1' $newsbook_sql),1,0) as placed,
				(SELECT TRIM(GROUP_CONCAT(CONCAT(' ', g_publications.publication, ' (', g_dates.publish_date, if(g_pages.ID,CONCAT(' | ',FLOOR(g_pages.page)),''),')'))) FROM ((nf_article_newsbook nb INNER JOIN global_publications g_publications ON nb.pID = g_publications.ID) INNER JOIN global_dates g_dates ON nb.dID = g_dates.ID) LEFT JOIN global_pages g_pages ON nb.pageID = g_pages.ID WHERE nb.aID = nf_articles.ID)  AS newsbooks,
				$newsbook_select
				(SELECT count(ID) FROM nf_comments WHERE nf_comments.aID =  nf_articles.ID) AS commentCount,
				(SELECT count(ID) FROM nf_files WHERE nf_files.aID =  nf_articles.ID AND nf_files.type='1') AS photosCount,
				(SELECT count(ID) FROM nf_files WHERE nf_files.aID =  nf_articles.ID AND nf_files.type='2') AS filesCount
			$select           ,
			nf_articles.ID as ID
			FROM ($from )

			
			$where
			$orderby
			$limit
		";
		if (isset($_GET['sql'])){
			echo $sql;exit();
		}
		//
		//
		$result = $f3->get("DB")->exec($sql);
		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}

	public static function getEdits($aID, $orderby) {
		$f3 = \Base::instance();
		$timer = new timer();

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}

		$where = "WHERE aID='$aID'";


		//test_array($limit);
		$sql = "
			SELECT nf_articles_body.*, global_users.fullName, nf_stages.stage, nf_stages.labelClass

			FROM (nf_articles_body INNER JOIN global_users ON nf_articles_body.uID = global_users.ID) LEFT JOIN nf_stages ON nf_articles_body.stageID = nf_stages.ID
			$where
			$orderby
		";
		//echo $sql;
		//exit();
		$result = $f3->get("DB")->exec($sql);
		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}


	public static function display($data, $options = array("highlight" => "", "filter" => "*")) {
		$f3 = \Base::instance();
		if (!isset($options['highlight'])) $options['highlight'] = array("","");
		if (!isset($options['filter'])) $options['filter'] = array("","");
		
		if (!is_array($options['highlight'])){
			$options['highlight'] = array($options['highlight'],'1');
		}
		if (!is_array($options['filter'])){
			$options['filter'] = array($options['highlight'][0],$options['filter']);
		}
		
	//	test_array($options);

		$timer = new timer();
		$user = $f3->get("user");
		$permissions = $user['permissions'];
		if (is_array($data)) {
			$a = array();
			foreach ($data as $item) {
				$showrecord = true;





				if ($showrecord) $a[] = $item;
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
			
			if ((isset($options["highlight"][0]))&&(isset($options["highlight"][1]))) {
				$record['highlight'] = 0;
				if (isset($record[$options["highlight"][0]]) && $record[$options["highlight"][0]]==$options["highlight"][1] && $options["highlight"][1]!="*"){
					$record['highlight'] = 1;
				}
			}
			if ((isset($options["filter"][0]))&&(isset($options["filter"][1]))&&($options["filter"][0]!="" && $options["filter"][1]!="")) {
				if ($options["filter"][1]=="*"){
					$showrecord = true;
				} else {
					if ($record[$options["filter"][0]]==$options["filter"][1]){

					} else {
						$showrecord = false;
					}
				}
				
			}

			//$record['show']=$showrecord;
			//test_array($record); 
			/*
			if (isset($options["filter"])) {
				if ($options["filter"] == "*") {
					$showrecord = true;
				} else {
					if (isset($record[$options["highlight"]]) && $record[$options["highlight"]] == $options['filter']) {
						$showrecord = true;
					} else {
						$showrecord = false;
					}
				}
			}*/
			//	test_array($permissions);
//echo $record[$options["highlight"]] . " | " . $showrecord . " | " . $options["filter"]. "<br>";
			if ($showrecord) {
				if (!isset($a[$record['heading']])) {
					$groups[] = $record['heading'];
					$arr = array("heading" => $record['heading'], "count" => "");
					$arr['groups'] = "";
					$arr['records'] = "";
					$a[$record['heading']] = $arr;
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
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	private static function order($grouping, $ordering) {
		$f3 = \Base::instance();
		$o = explode(".", $ordering['c']);
	//	test_array($ordering['c']);
		$a = array();
		foreach ($o as $b) {
			$a[] = "" . $b . "";
		}
		$a = implode(".", $a);
		$a = explode(",",$a);
		$orderby = array();
		foreach ($a as $col){
			$orderby[] = " " . $col . " " . $ordering['o'];
		}
		$orderby = implode(",",$orderby);


		$arrange = "";
		$ordering = $grouping['o'];
		switch ($grouping['g']) {
			case "author":
				$orderby = "COALESCE(global_users.fullName,99999) $ordering, " . $orderby;
				$arrange = "global_users.fullName as heading";
				break;
			case "stage":
				$orderby = "COALESCE(nf_stages.orderby,99999) $ordering, " . $orderby;
				$arrange = "nf_stages.stage as heading";
				break;
			case "category":
				$orderby = "COALESCE(nf_categories.category,'zzzzzzzz') $ordering, " . $orderby;
				$arrange = "nf_categories.category as heading";
				break;
			case "priority":
				$orderby = "COALESCE(nf_priorities.orderby,99999) $ordering, " . $orderby;
				$arrange = "COALESCE(nf_priorities.priority,'') as heading";
				break;
			case "page":
				$orderby = "COALESCE(FLOOR(global_pages.page),99999) $ordering, " . $orderby;
				$arrange = "COALESCE(CONCAT('Page ',FLOOR(global_pages.page)),'Not placed') as heading";
				break;

			case "none":
				$orderby = "" . $orderby;
				$arrange = "'None' as heading";
				break;
		}

		//test_array(array($orderby));
		return array("order" => $orderby, "select" => $arrange);
	}

	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$a = new \DB\SQL\Mapper($f3->get("DB"), "nf_articles");
		$a->load("ID='$ID'");
		if (!$a->dry()) {
			$a->deleted = "1";
			$a->deleted_userID = $userID;
			$a->deleted_user = $user['fullName'];
			$a->deleted_date = date("Y-m-d H:i:s");
			$a->deleted_reason = ($reason);
			$a->dateChanged = date("Y-m-d H:i:s");
			$a->save();
			$changes = array(array("k" => "Deleted", "v" => "1", "w" => ""), array("k" => "deleted_user", "v" => $user['fullName'], "w" => ""), array("k" => "deleted_reason", "v" => $reason, "w" => ""));
			self::logging($a->ID, $changes, "Article Deleted");
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return "deleted";
	}

	

	public static function save($ID = "", $values = array(), $opts = array("dry" => true, "section" => "booking")) {
		//test_array($values);
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		
		
		$lookupColumns = array();
		$lookupColumns["authorID"] = array("sql" => "(SELECT fullName FROM global_users WHERE ID = '{val}')", "col" => "author", "val" => "");
		$lookupColumns["categoryID"] = array("sql" => "(SELECT category FROM nf_categories WHERE ID = '{val}')", "col" => "category", "val" => "");
		$lookupColumns["typeID"] = array("sql" => "(SELECT type FROM nf_article_types WHERE ID = '{val}')", "col" => "type", "val" => "");
		$lookupColumns["stageID"] = array("sql" => "(SELECT stage FROM nf_stages WHERE ID = '{val}')", "col" => "stage", "val" => "");
		$lookupColumns["priorityID"] = array("sql" => "(SELECT priority FROM nf_priorities WHERE ID = '{val}')", "col" => "priority", "val" => "");
		$lookupColumns["locked_uID"] = array("sql" => "(SELECT fullName FROM global_users WHERE ID = '{val}')", "col" => "locked_user", "val" => "");
		$lookupColumns["rejected_uID"] = array("sql" => "(SELECT fullName FROM global_users WHERE ID = '{val}')", "col" => "rejected_user", "val" => "");
		$lookupColumns["deleted_userID"] = array("sql" => "(SELECT fullName FROM global_users WHERE ID = '{val}')", "col" => "deleted_user", "val" => "");
		$lookup = array();
		$changes = array();
		
		$cfg = $f3->get("CFG");
		$writeBody = false;
		$currentStage = '1';
		if ($ID){
			$details = new articles();
			$details = $details->get($ID);

			$currentStage = $details['stageID'];
			

			if (isset($values['body']) && $values['body']) {
				$body = $values['body'];


				$body = $f3->scrub($body, $cfg['nf']['whitelist_tags']);

				if ($body != $details['body']) {
					$writeBody = true;
					$values['body'] = $body;
					$words = htmlspecialchars_decode($body);
					$words = $f3->scrub($words);

					$_body = $words;
					$_draft = $f3->scrub($details['draft']);
					$_dbody = $f3->scrub($details['body']);

					$values['words'] = str_word_count($words);

					similar_text($_draft, $_body, $sim);
					$percent = number_format((float)100 - $sim, 2, '.', '');

					$values['percent_orig'] = $percent;

					similar_text($_dbody, $_body, $sim);
					$percent = number_format((float)100 - $sim, 2, '.', '');

					$values['percent_last'] = $percent;


					$changes[] = array("k" => "body", "v" =>"Change", "w" => "<div class='r'>". $values['percent_last']."%</div>");
					
				}


			}
			
			
		} else {
			$values['cID']=$user['company']['ID'];
			$values['stageID']= $currentStage = "1";
			$writeBody = true;

			$body = $values['body'];
			$words = htmlspecialchars_decode($body);
			$words = $f3->scrub($words);


			$values['words'] = str_word_count($words);
			$changes[] = array("k" => "body", "v" => "Added", "w" => "<div class='r'>new</div>");

			$values['percent_last'] = "";
			$values['percent_orig'] = "";
			
		}
		

		


		
		
		
		
		
		$a = new \DB\SQL\Mapper($f3->get("DB"), "nf_articles");
		$a->load("ID='$ID'");
		$cfg = $f3->get("CFG");
		//test_array($cfg);
		
		
		
		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$cur = $a->$key;
				if ($cur != $value) {
					if (isset($lookupColumns[$key])) {
						$lookupColumns[$key]['val'] = $value;
						$lookupColumns[$key]['was'] = $cur;
						$lookup[] = $lookupColumns[$key];
					} else {
						$w = $cur;
						$v = $value;
						
						$changes[] = array("k" => $key, "v" => $v, "w" => str_replace("0000-00-00 00:00:00", "", $w));
					}
				}
				$a->$key = $value;
			}
		}
		
		if (count($changes)){
			$a->dateChanged = date("Y-m-d H:i:s");
		}
		if ($opts['dry'] || !$a->dry()) {
			$a->save();
		}
		if (!$ID) {
			$label = "Article Added";
			$ID = $a->ID;
		} else {
			$label = "Article Edited";
		}
		$sql = "SELECT 1 ";
		
		
		foreach ($lookup as $col) {
			$sql .= ", " . str_replace("{val}", $col['val'], $col['sql']) . " AS " . $col['col'];
			$sql .= ", " . str_replace("{val}", $col['was'], $col['sql']) . " AS " . $col['col'] . "_was";
		}
		$v = $f3->get("DB")->exec($sql);
		$v = $v[0];
		foreach ($lookup as $col) {
			$changes[] = array("k" => $col['col'], "v" => $v[$col['col']], "w" => $v[$col['col'] . "_was"]);
		}
		
	//	test_array(array("v"=>$values,"c"=>$changes)); 
		
		
		
		
	
		
		if ($writeBody){

			$b = new \DB\SQL\Mapper($f3->get("DB"), "nf_articles_body");
			$b->aID = $ID;
			$b->uID = $user['ID'];
			$b->body = $values['body'];
			$b->words = $values['words'];
			if ($values['percent_last']) $b->percent_last = $values['percent_last'];
			if ($values['percent_orig']) $b->percent_orig = $values['percent_orig'];
			$b->stageID = $currentStage;
			
			$b->save();

			
			
			
		}
		if (count($changes)) self::logging($ID, $changes, $label);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $ID;
	}

	public static function getLogs($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = $f3->get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =nf_articles_logs.userID ) AS fullName FROM nf_articles_logs WHERE aID = '$ID' ORDER BY datein DESC");
		$a = array();
		foreach ($return as $record) {
			$record['log'] = json_decode($record['log']);
			$a[] = $record;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $a;
	}

	private static function logging($ID, $log = array(), $label = "Log") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$log = mysql_escape_string(json_encode($log));
		//	$log = str_replace("'", "\\'", $log);
		$f3->get("DB")->exec("INSERT INTO nf_articles_logs (`aID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);
	}

	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN nf_articles;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["media"] = array();

		return $result;
	}
}