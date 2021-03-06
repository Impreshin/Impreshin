<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class articles extends save {
	function __construct() {
		parent::__construct();


	}
	function stage() {
		$user = $this->f3->get("user");
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";
		$value = isset($_POST['stageID']) ? $_POST['stageID'] : "";
		$values = array(
			"stageID" => $value,
			"locked_uID" => NULL,
		);
		$r = array();





		$submit = true;


		if ($submit) {
			models\articles::save($aID, $values, array("dry" => false,"section"=>"stage"));
			$r['ID'] = $aID;
			$r['values'] = $values;
		}

		test_array($r);

	}
	function unlock() {
		$user = $this->f3->get("user");
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";
		$values = array(
			"locked_uID" => NULL, 
		);
		$r = array();





		$submit = true;


		if ($submit) {
			models\articles::save($aID, $values, array("dry" => false,"section"=>"unlocked"));
			$r['ID'] = $aID;
			$r['values'] = $values;
		}

		test_array($r);

	}
	function archive() {
		$user = $this->f3->get("user");
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";
		$archived = isset($_POST['archived']) ? $_POST['archived'] : "0";
		$values = array(
			"archived" => $archived, 
			"archived_date" => $archived=='1'?date("Y-m-d H:i:s"):NULL
		);
		$r = array();





		$submit = true;


		if ($submit) {
			models\articles::save($aID, $values, array("dry" => false,"section"=>"archive"));
			$r['ID'] = $aID;
			$r['values'] = $values;
		}

		test_array($r);

	}

	function archive_mass() {
		$user = $this->f3->get("user");
		
		$ids = (isset($_POST['aIDs']) && $_POST['aIDs'] && $_POST['aIDs'] != "undefined") ? $_POST['aIDs'] : array();

		$values = array(
			"archived" => "1",
			"archived_date" => date("Y-m-d H:i:s")
		);
		
		
		foreach ($ids as $id){
			$ID = models\articles::save($id, $values, array("dry" => false,"section"=>"archive"));
		}
		

		
		test_array($ids);

	}

	function reject() {
		$user = $this->f3->get("user");
		$reason = isset($_POST['reject_reason']) ? $_POST['reject_reason'] : "";
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";

		$values = array(
			"rejected" => "1", 
			"rejected_uID" => $user['ID'], 
			"rejected_reason" => $reason, 
			"stageID" => '1', 
			"locked_uID" => NULL
		);
		$r = array();

		if (isset($values['title']) && $values['title'] == '') {
			$submit = false;
			$r['error'][] = array(
				"field" => "reject_reason", 
				"msg" => "Need to specify a reason"
			);
		}






		$submit = true;
		if (!$reason) $submit = false;
		if ($submit) {


			models\articles::save($aID, $values, array("dry" => false,"section"=>"rejected"));
			$r['ID'] = $aID;
			$r['values'] = $values;

			$values = array(
				"aID" => $aID,
				"uID" => $user['ID'],
				"comment" => '<span class="label label-important">Rejected</span> ' . $values['rejected_reason'],
				"parentID" => ""
			);


			models\comments::save("", $values);
			
			
		}

		test_array($r);

	}

	function placed() {
		$user = $this->f3->get("user");
		$pID = isset($_POST['pID']) ? $_POST['pID'] : "";
		$dID = isset($_POST['dID']) ? $_POST['dID'] : "";
		$placed = isset($_POST['placed']) ? $_POST['placed'] : "0";
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";

		$uID = $placed=='1'? $user['ID']:"";

		//test_array(array( "aID"=>$aID,					   "pID"=>$pID,					   "dID"=>$dID,					   "placed"=>$placed,				   )); 

		//$this->f3->get("DB")->exec("UPDATE nf_article_newsbook SET placed = '$placed', placed_uID = '$uID' WHERE pID = '$pID' AND dID = '$dID' AND aID = '$aID'");

		$b = new \DB\SQL\Mapper( $this->f3->get("DB"), "nf_article_newsbook");
		$b->load("pID = '$pID' AND dID = '$dID' AND aID = '$aID'");
		
		$label = $placed=='1'? "Placed":"Un-Placed";
		$log = array();
		$log[] = array(
			"k"=>"placed",
			"v"=>$placed,
			"w"=>$b->placed=='1'?'1':'0'
		);
		
		if (!$b->dry()){
			$b->placed=$placed;
			$b->placed_uID=$uID;
			$b->save();

		}

		//$log = array($log);
		models\articles::logging($aID,$log,$label);
		test_array("done");

	}

	function comment() {
		$user = $this->f3->get("user");
		$ID = isset($_POST['comment-ID']) ? $_POST['comment-ID'] : "";
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";


		$values = array(
			"aID" => $aID, 
			"uID" => $user['ID'], 
			"comment" => isset($_POST['comment']) ? $_POST['comment'] : "", 
			"parentID" => isset($_POST['comment-parentID']) && $_POST['comment-parentID'] && $_POST['comment-parentID'] != "null" && $_POST['comment-parentID'] != "undefined" ? $_POST['comment-parentID'] : "",);
		

		models\comments::save($ID, $values);


		$cfg = $this->f3->get("CFG");

		if ($cfg['system_messages']==true){
			
					
			
			$art = new models\articles();
			$art = $art->get($aID);
			
		
			
			$subject = "New comment: <span class='g s'>".$art['title']."</span>";
			$msg = "Comment posted by: ".$user['fullName']."<hr>". $values['comment'];
			$url = "/app/nf#ID=".$art['ID']."&details-tab=details-pane-comments";
			
			$to_uID_array = array($art['authorID'],$user['ID']);
			
			foreach ($art['comments'] as $userID){
				if (!in_array($userID['uID'],$to_uID_array)) $to_uID_array[] = $userID['uID'];
			}
			
			$t = array();
			foreach ($to_uID_array as $ttttt){
				if (!in_array($ttttt,$t)) $t[] = $ttttt;
			}

			$to_uID_array = $t;

			foreach ($to_uID_array as $to_uID){
				//if ($to_uID != $user['ID']){
					$message_values = array(
						"cID"=>$user['company']['ID'],
						"app"=>"nf",
						"to_uID"=>$to_uID,
						"subject"=>$subject,
						"message"=>$msg,
						"url"=>$url,
						"read"=>($to_uID == $user['ID'])?"1":"0"
					);

					\models\messages::_save("",$message_values);
			//	}
			};

			
		};
		
		
		
		
		
		test_array(array("ID" => $ID, "values" => $values));

	}
	
	

	function newsbook() {
		$user = $this->f3->get("user");
		$ID = isset($_POST['newsbook-ID']) ? $_POST['newsbook-ID'] : "";
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";


		$values = array(
			"aID" => $aID, 
			"pID" => isset($_POST['newsbook-pID']) && $_POST['newsbook-pID'] && $_POST['newsbook-pID'] != "null" && $_POST['newsbook-pID'] != "undefined" ? $_POST['newsbook-pID'] : "", 
			"dID" => isset($_POST['newsbook-dID']) && $_POST['newsbook-dID'] && $_POST['newsbook-dID'] != "null" && $_POST['newsbook-dID'] != "undefined" ? $_POST['newsbook-dID'] : "", 
			"uID" => $user['ID'], "placed" => 0);

		// do the insert thing.. get the ID
		
		
		$exists = models\newsbooks::exists($values['aID'],$values['dID']);
		$error = "";
	
		//test_array(array("c"=>count($exists),"in"=>in_array($ID,$exists),"ex"=>$exists));
		
		if (count($exists)==0 || $ID!=""){

			$ID = models\newsbooks::save($ID, $values);


			$files = array();

			$file_ids = array();
			$sql = array();

			foreach (explode(",", $_POST['files']) as $fileID) {
				$sql[] = "('" . $ID . "', '" . $fileID . "')";
				$files[] = array("nID" => $ID, "fileID" => $fileID);
				$file_ids[] = $fileID;
			}

			if (count($sql)) {

				$sql = "INSERT INTO nf_article_newsbook_photos (`nID`,`fileID`) VALUES " . implode(",", $sql);

				$this->f3->get("DB")->exec("DELETE FROM nf_article_newsbook_photos WHERE nID = '$ID'");
				$this->f3->get("DB")->exec($sql);
			}

		} else {
			$values['exists'] = $exists[0];

			$error = "The article already exists in this newsbook\n\rClick 'Ok' to edit the newsbook or 'Cancel' to cancel";
		}
		
		




		$return = array("ID" => $ID, "values" => $values);

		if ($error) $return['error'] = $error;
		test_array($return);


	}

	function remove_newsbook() {
		$ID = (isset($_GET['ID']) && $_GET['ID'] && $_GET['ID'] != "undefined") ? $_GET['ID'] : "";
		models\newsbooks::_delete($ID);
		test_array("done");
	}

	function form() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];



		$ID = (isset($_GET['ID']) && $_GET['ID'] && $_GET['ID'] != "undefined") ? $_GET['ID'] : "";
		$type = (isset($_GET['type'])) ? $_GET['type'] : "";


		// ------------------------
		$submit = true;
		$r = array();

		if ($ID){
			$record = new models\articles();
			$return = $record->get($ID);


			$allow = array(
				"newsbook"=>"0",
				"locked"=>"0",
				"reject"=>"0",
				"delete"=>"0",
				"archive"=>"0",
				"edit"=>"0",
				"print" => "1",
				"stageNext"=>"0",
				"stagePrev"=>"0",
				"stage_jump_list"=>"0",
				"placed"=>"0"
			);


			$permissions = $user['permissions'];
			$stage_permissions = isset($permissions['stages'][$return['stageID']])?$permissions['stages'][$return['stageID']]:array("label"=>"-none-",
				"edit"=>"0",
				"to"=>"0",
				"reject"=>"0",
				"delete"=>"0",
				"newsbook"=>"0");

			if ($stage_permissions['edit']=='1'){
				if ($return['locked_uID']){
					if ($return['locked_uID']==$user['ID']){
						$allow['edit'] = '1';
					}
				} else {
					$allow['edit'] = '1';
				}
			}
			if ($stage_permissions['delete']=='1'){
				$allow['delete']='1';
			}
			if ($stage_permissions['newsbook']=='1'){
				$allow['newsbook']='1';
			}
			if ($stage_permissions['reject']=='1'){
				$allow['reject']='1';
			}
			if ($permissions['details']['overwrite_locked']=="1" || ($return['locked_uID']==$user['ID'] && $return['stageID']!='1')){
				$allow['locked']='1';
			}
			if ($permissions['details']['archive']=="1"){
				$allow['archive']='1';
			}
			if ($permissions['details']['stage_jump_list']=="1"){
				$allow['stage_jump_list']='1';
			}
			if ($permissions['form']['edit_master']=="1"){
				$allow['edit']='1';
			}



			if ($allow['edit']!='1'){
				$submit = false;
				$r['error'][] = array("field" => "title", "msg" => "The permission for this article has changed. You no longer have the permission to edit it. No changes saved");
			}
			
			
			

		}
		
		
		
		$values = array();

		if ($type) {
			$values['typeID'] = $type;
		}






		if (isset($_POST['title'])) $values['title'] = $_POST['title'];
		if (isset($_POST['body'])) $values['body'] = $_POST['body'];
		if (isset($_POST['authorID'])) $values['authorID'] = $_POST['authorID'];
		if (isset($_POST['categoryID'])) $values['categoryID'] = $_POST['categoryID'];
		if (isset($_POST['meta'])) $values['meta'] = $_POST['meta'];
		if (isset($_POST['priorityID'])) $values['priorityID'] = $_POST['priorityID'];
		if (isset($_POST['cm'])) $values['cm'] = $_POST['cm'];
		if (isset($_POST['checklist'])) $values['checklist'] = $_POST['checklist'];
		if (isset($_POST['stageID'])) $values['stageID'] = $_POST['stageID'];
		if (isset($_POST['language'])) $values['lang'] = $_POST['language'];
		
		if ($_GET['locked']=='0'){
			$values['locked_uID'] = NULL;
		} else {
			$values['locked_uID'] = $user['ID'];
		}

		
		
		


		$values['deleted'] = NULL;
		$values['deleted_userID'] = NULL;
		$values['deleted_user'] = NULL;
		$values['deleted_date'] = NULL;
		$values['deleted_reason'] = NULL;

		$ss = array();
		$ss["form"] = array(
			"type" => $type, 
			"last_author" => isset($values['authorID']) && $values['authorID'] ? $values['authorID'] : "", 
			"last_category" => isset($values['categoryID']) && $values['categoryID'] ? $values['categoryID'] : "",
			"last_language" => isset($values['lang']) && $values['lang'] ? $values['lang'] : ""
		);

		models\settings::save($ss);





		$settings = \apps\nf\settings::_available();




		

		if (isset($values['title']) && $values['title'] == '') {
			$submit = false;
			$r['error'][] = array("field" => "title", "msg" => "Need to specify a title");
		}

		if ((isset($_POST['current_stage_ID']) && ($_POST['current_stage_ID'] != '1' || (isset($_POST['stageID']) && $_POST['stageID'] != '1')))) {
			if (isset($values['meta']) && $values['meta'] == '') {
				$submit = false;
				$r['error'][] = array("field" => "meta", "msg" => "Need to specify meta");
			}
		}





		if ($submit) {

			$values['rejected'] = '0';
			$values['rejected_uID'] = NULL;
			$values['rejected_reason'] = NULL;


			if (isset($values['stageID']) && $values['stageID']) {
				$values['locked_uID'] = NULL;
			}
			
			if ($type!='1'){
				if (isset($values['cm']))$values['cm']=NULL;
				if (isset($values['words']))$values['words']=NULL;
			}

			array_walk_recursive($values, "form_write");
			$ID = models\articles::save($ID, $values);




			$filetypes = $settings['allowedFileTypes'];

			$files = array();


			foreach ($_POST as $k => $v) {
				if (strpos($k, "file-filename-") === 0) {
					$file_ID = str_replace("file-filename-", "", $k);
					$n = $file_ID;


					$filename = isset($_POST['file-filename-' . $n]) ? $_POST['file-filename-' . $n] : "";





					$icon = "file";
					$filetype = "0";
					$file_ext = strtolower($filename);
					$file_ext = explode(".", $file_ext);
					$file_ext = $file_ext[count($file_ext) - 1];


					foreach ($filetypes as $file_k => $file_v) {
						foreach ($file_v as $file_key => $file_item) {
							if (in_array($file_ext, $file_item)) {
								$icon = $file_key;
								$filetype = $file_k;
							}
						}
					}




					$fileID = isset($_POST['file-ID-' . $n]) ? $_POST['file-ID-' . $n] : "";

					$file = array(
						"aID" => $ID, 
						"filename" => $filename, 
						"filename_orig" => sanitize_filename(isset($_POST['file-filename_orig-' . $n]) ? $_POST['file-filename_orig-' . $n] : ""), 
						"caption" => isset($_POST['file-caption-' . $n]) ? $_POST['file-caption-' . $n] : "", 
						"folder" => isset($_POST['file-folder-' . $n]) ? $_POST['file-folder-' . $n] : "", 
						"uID" => isset($_POST['file-uID-' . $n]) ? $_POST['file-uID-' . $n] : $user['ID'], 
						"type" => $filetype,

					);
					array_walk_recursive($file, "form_write");
					models\files::save($fileID, $file);

					//$files[] = $file;

				}
			}


			foreach ($files as $file) {

			}






			$r['ID'] = $ID;
			$r['values'] = $values;
			//$r['post'] = $_POST;

		}
		test_array($r);
		exit();
	}


	function _delete() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		$delete_reason = (isset($_POST['delete_reason'])) ? $_POST['delete_reason'] : "";

		$result = models\articles::_delete($ID, $delete_reason);

		echo json_encode(array("result" => $result));
		exit();
	}
	function file_delete() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		

		$result = models\files::_delete($ID);

		echo json_encode(array("result" => $result));
		exit();
	}
}
