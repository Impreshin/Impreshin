<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_categories extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$category = isset($_POST['category']) ? $_POST['category'] : "";
		$publications = isset($_POST['publications']) ? $_POST['publications'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($category==""){
			$submit = false;
			$return['error'][] = "Need to specify a Category Name";
		}









		$values = array(
			"category"         => $category,
			"publications"     => $publications,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\categories::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\categories::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$cID = $user['publication']['cID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE ab_categories SET orderby = '$i' WHERE ID = '$id' AND cID = '$cID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}

	function _pub() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $user['publication']['ID'];


		$p = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_category_pub");
		$p->load("catID='$ID' and pID='$pID'");
		if (!$p->ID) {
			$p->catID = $ID;
			$p->pID = $pID;
			$p->save();
			$pub = "Added: " . $user['publication']['publication'];
		} else {
			$p->erase();
			$pub = "Removed: " . $user['publication']['publication'];
		}


		$changes = array(
			array(
				"k"=> "publication",
				"v"=> $pub,
				"w"=> '-'
			)
		);

		$a = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_categories");
		$a->load("ID='$ID'");
		$label = "";
		if ($a->ID) {
			$label = "Record Edited ($a->category)";
		}

		\models\logging::save("categories", $changes, $label);
		return $changes;
	}
}
