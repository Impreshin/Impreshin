<?php
/*
 * Date: 2012/09/21
 * Time: 12:35 PM
 */

//namespace docs;
class messages {
	function __construct() {
		$this->f3 = \base::instance();
		$this->user = $this->f3->get("user");
		

	}






	


	function page(){
		$applications = $this->f3->get("applications");
		$key = $this->f3->get("app");
		$params = $this->f3->get("params");

		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];
	
		
		
	//test_array($settings); 
	
	
		$tmpl = new \template("template.tmpl","./messages/");
		$tmpl->render = true;
		$tmpl->enable_spellcheck = function_exists('enchant_broker_init')? '1' : '0';
		$tmpl->users = \models\user::getAll("cID='$cID'", "fullName ASC");
		
	
		$tmpl->output();
	
	
	
	
	
	
	}
	
	function _list(){
		$key = $this->f3->get("app");
		$params = $this->f3->get("params");
		$this->f3->set("json",true);
		$user = $this->f3->get("user");
		$uID = $user['ID'];
		$applications = $this->f3->get("applications");
		
		//test_array($applications); 
		
		$section = isset($_GET['section'])?$_GET['section']:"home";
		$ID = isset($_GET['ID'])?$_GET['ID']:"";
		
		
		$return = array(
			"section"=>$section
		);
		$return['side'] = array(
			"unread"=>0,
			"total"=>0,
			"users"=>array(),
			"system"=>array()

		);
		$section_parts = explode("|",$section);
		$section_main= isset($section_parts[0])?$section_parts[0]:"";
		$section_filter= isset($section_parts[1])?$section_parts[1]:"";

		$group_filter = "";
		$new_messages = "";
		if ($ID){
			
			$detailsO = new \models\messages;
			$details = $detailsO->get($ID);
			
			if ($details['to_uID']==$uID){
				if ($details['read']!='1'){
					\models\messages::_save($details['ID'],array("read"=>'1'),array("dry"=>"false"));
				}
				
			}

			$details['direction'] = "out";
			if ($details['to_uID']==$uID){
				$details['direction'] = "in";
			}

			if ($details['app']){
				$details['from_fullName'] =  $applications[$details['app']]['name'];
			}

			$group_filter = $details['subject'];
			if ($details['direction']=='out'){
				$group_filter = "[".$details['to_uID'] . "]" . $group_filter;
			} else {
				$group_filter = "[".$details['from_uID'] . "]" . $group_filter;
			}

			
			
			$return['details'] = $details;
		}
		
		
		$data = \models\messages::getAll("from_uID='$uID' || to_uID = '$uID'");
		//test_array($data); 
		$messages = array();
		foreach ($data as $message){
			unset($message['message']);
			if ($message['app']) $message['from_fullName'] = $applications[$message['app']]['name'];
			
			$message['direction'] = "out";
			$message['date'] = disp_date($message['datein']);


			$from_ID = $message['from_uID'];
			$to_ID = $message['to_uID'];
			$sec = "users";
			if ($message['app']){
				$from_ID = $message['app'];
				$sec = "system";
			}
			
			
			
			if ($message['to_uID']==$uID){
				$message['direction'] = "in";

				if ($message['read']=='0'){
					//test_array(array($message['read'],$unread,$return['side']['users'][$from_uID]['unread']));
				}
			}
			
			if (!isset($return['side'][$sec][$from_ID])){
				$return['side'][$sec][$from_ID] = array(
					"ID"=>$from_ID,
					"name"=>$message['from_fullName'],
					"unread"=>0,
					"total"=>0
				);
			}
			

			if ($message['to_uID']==$uID){
				$unread = ($message['read']=='1')?0:1;
			} else {
				$unread = 0;
			}

			$return['side']['unread'] = $return['side']['unread'] + $unread;
			$return['side']['total'] = $return['side']['total'] + 1;
			$return['side'][$sec][$from_ID]['unread'] = $return['side'][$sec][$from_ID]['unread'] + $unread;
			$return['side'][$sec][$from_ID]['total'] = $return['side'][$sec][$from_ID]['total'] + 1;

			
			if (!isset($return['side'][$sec][$to_ID])){
				$return['side'][$sec][$to_ID] = array(
					"ID"=>$to_ID,
					"name"=>$message['to_fullName'],
					"unread"=>0,
					"total"=>0
				);
			}
			$return['side'][$sec][$to_ID]['unread'] = $return['side'][$sec][$to_ID]['unread'] + $unread;
			$return['side'][$sec][$to_ID]['total'] = $return['side'][$sec][$to_ID]['total'] + 1;
			
			
			$includemsg = false;
			
			SWITCH ($section_main){
				CASE 'user':
						if ($message['to_uID']==$section_filter || $message['from_uID']==$section_filter) {
							$includemsg = true;
						}
					break;
				CASE 'system':
						if ($message['app']==$section_filter) {
							$includemsg = true;
						}
					break;
				DEFAULT:
					if ($message['read']!='1' && $message['to_uID']==$uID){
						$includemsg = true;
						
					}
					
					
					break;
			}


		
			if ($includemsg){
				
				$group = $message['subject'];
				if ($message['direction']=='out'){
					$group = "[".$message['to_uID'] . "]" . $group;
				} else {
					$group = "[".$message['from_uID'] . "]" . $group;
				}
				//echo $group . "<br>";
				
				
				if(!isset($messages[$group])){
					$messages[$group] = array();
				}

				
				
				$messages[$group][] = $message;
				if ($group == $group_filter && isset($details['ID'])){
					if (!is_array($new_messages)) $new_messages = array();
					$new_messages[] = $message ;
					
				}
			}
			
		}
		
		
		//test_array($return['side']['users']); 
		
		
		//exit();
		
		
		$n = array();
		foreach ($return['side']['users'] as $k=>$record){
			if ($k!=$uID){
				$n[] = $record;
			}
		}
		$return['side']['users'] = $n;

		$n = array();
		foreach ($return['side']['system'] as $record){
			$n[] = $record;
		}
		$return['side']['system'] = $n;


		//test_array($messages); 
		$n = array();
		if ($new_messages || isset($details['ID'])){
			if (!is_array($new_messages)) $new_messages = array();
			$n = $new_messages;
		} else {
			foreach ($messages as $message_group){

				$record = $message_group[0];
				$unread_count_group = 0;
				foreach ($message_group as $m){
					if ($m['to_uID']==$uID && $m['read']!='1'){
						$unread_count_group = $unread_count_group + 1;
					}
					
				}
				

				$n[] = array(
					"count"=>count($message_group),
					"unread"=>$unread_count_group,
					"record"=>$record,
					"messages"=>($message_group)
				);

			}
		}

		
		$messages = $n;
		
		
		
		$return['messages'] = $messages;
		//test_array($return); 
		
		$notifications = "\\apps\\$key\\models\\notifications";
		$GLOBALS["output"]['notifications'] = $notifications::show();
		return $GLOBALS["output"]['data'] = $return;
	}
	function do_state(){
		$read = isset($_REQUEST['read'])?$_REQUEST['read']:"";
		$ids = isset($_REQUEST['ids'])?$_REQUEST['ids']:"";
		if (!is_array($ids)) {
			$ids = explode(",",$ids);
		}
		
		if ($read=='0' || $read=='1'){
			foreach ($ids as $id){
				\models\messages::_save($id,array("read"=>$read),array("dry"=>"false"));
			}

		}

		$this->f3->set("json",true);
		return $GLOBALS["output"]['data'] = $ids;
		

	}
	function do_message(){
		$user = $this->f3->get("user");
		$cfg = $this->f3->get("CFG");
		$to_uID = isset($_REQUEST['to_uID'])?$_REQUEST['to_uID']:"";
		$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:"";
		$message = isset($_REQUEST['message'])?$_REQUEST['message']:"";


		$message = $this->f3->scrub($message, $cfg['nf']['whitelist_tags']);
		$message = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $message);
		
		$values = array(
			"from_uID"=>$user['ID'],
			"to_uID"=>$to_uID,
			"subject"=>$subject,
			"message"=>$message
		);
		
		
		$id = \models\messages::_save("",$values);
		

		$this->f3->set("json",true);
		return $GLOBALS["output"]['data'] = $id;
		

	}
}

function disp_date($date){
	$date = strtotime($date);
	
	$cur = strtotime(date("Y-m-d"));
	$return = "";
	
	
	
	if (date("Y-m-d",$cur) == date("Y-m-d",$date)){
		$return = date("h:i a",$date);;
	} else {
		if (date("y",$cur)!=date("y",$date)){
			$return = date("d M 'y",$date);
		} else {
			$return = date("d M",$date);
		}
		
	}
	
	return $return;
	return $date ;
}