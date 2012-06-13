<?php
/*
 * Date: 2011/12/05
 * Time: 9:24 AM
 */
namespace models;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class logs {

	public static function getAllContent($contentID,$returnshort=false){
		$timer = new timer();

		$return = F3::get("DB")->sql("SELECT mp_logs.*, mp_users.name  FROM mp_logs LEFT JOIN mp_users ON mp_logs.userID = mp_users.ID WHERE contentID = '$contentID' ORDER BY datein DESC");

		if ($returnshort){
			$a = array();
			foreach($return as $record){
				$a[] = array(
					"d"=> $record['datein'],
					"t"=> $record['text'],
					"n"=> $record['name']
				);
			}
			$return = $a;
		}


		$timer->stop("Models - logs - getAllContent", func_get_args());
		return $return;
	}

	public static function save($id=array("contentID"=>"","commentID"=>"","meetingID"=>"","companyID"=>"","answerID"=>""),$text="",$userID=""){
		$timer = new timer();
		if (!$userID){
			$user = F3::get("user");
			$userID = $user['ID'];
		}



		$c = new Axon("mp_logs");

		if ((isset($id["contentID"]))) $c->contentID = (isset($id["contentID"])) ? $id["contentID"] : "";
		if ((isset($id["commentID"]))) $c->commentID = (isset($id["commentID"])) ? $id["commentID"] : "";
		if ((isset($id["meetingID"]))) $c->meetingID = (isset($id["meetingID"])) ? $id["meetingID"] : "";
		if ((isset($id["companyID"]))) $c->companyID = (isset($id["companyID"])) ? $id["companyID"] : "";
		if ((isset($id["answerID"]))) $c->answerID = (isset($id["answerID"])) ? $id["answerID"] : "";
		if ($text) $c->text = ($text) ? $text : "";
		$c->userID = $userID;

		$c->save();

		$timer->stop("Models - logs - save", func_get_args());

	}
}
