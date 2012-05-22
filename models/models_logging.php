<?php
/*
 * Date: 2011/12/08
 * Time: 4:30 PM
 */
namespace models;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class logging {
	private $log = array();
	private $companyID;
	private $meetingID;

	function __construct($companyID="",$meetingID="") {
		$this->companyID = $companyID;
		$this->meetingID = $meetingID;

	}

	public static function getAll($where="",$orderby=""){
		$timer = new timer();

		if ($where) {
			$where = "WHERE " . $where;
		}
		if ($orderby) {
			$orderby = "ORDER BY " . $orderby;
		}

		$result = F3::get("DB")->sql("SELECT *, (SELECT fullname from mp_users WHERE mp_users.ID = uID) AS fullname FROM mp_logging $where $orderby");

		$return = $result;


		$timer->stop("Models - logging - getAll", func_get_args());
		return $return;
	}
	public static function getAllTypes(){
		$timer = new timer();

		$result = F3::get("DB")->sql("SELECT * FROM mp_logging_types ORDER BY ID ASC");

		$return = $result;


		$timer->stop("Models - logging - getAllTypes", func_get_args());
		return $return;
	}
	public function _log($value) {
		$this->log[] = $value;
	}

	public function save($heading,$type="",$companyID="",$meetingID=""){
		if ($companyID) $this->companyID = $companyID;
		if ($meetingID) $this->meetingID = $meetingID;

		if (!$this->companyID && $this->meetingID){
			$m = new Axon("mp_meetings");
			$m->load("ID='". $this->meetingID."'");
			$this->companyID = $m->companyID;
		}

		$str = serialize($this->log);

		$user = F3::get("user");
		$userID = $user['ID'];

		$a = new Axon("mp_logging");
		$a->uID = $userID;
		$a->heading = $heading;
		$a->description = $str;
		$a->companyID = $this->companyID;
		$a->meetingID = $this->meetingID;
		$a->type = $type;

		$a->save();

		return $a->_id;

	}


}
