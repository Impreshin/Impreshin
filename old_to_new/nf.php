<?php
/*
 * Date: 2013/08/21
 * Time: 12:04 PM
 */

$cfg = array();
require_once('../config.default.inc.php');
require_once('../config.inc.php');

require_once('../inc/functions.php');
require_once('../update/update.php');



$output = array();
//$output["db_backup"] = update::db_backup($cfg, "NF_stuff");


$table_changes = array(
	"nf_articles"=>array(
		"authorID"=>array(
			"from"=>array(
				"table"=>"_global_access",
				"column"=>"FullName",
				"lookup"=>"ID"
			),
			"to"=>array(
				"table"=>"global_users",
				"column" => "fullName",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
		),
		"catID"=>array(
			"rename"=>"categoryID",
			"type"=>"INT(6)"
		),
		"lockedBy"=>array(
			"from"=>array(
				"table"=>"_global_access",
				"column"=>"FullName",
				"lookup"=>"ID"
			),
			"to"=>array(
				"table"=>"global_users",
				"column" => "fullName",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
			"rename"=>"locked_uID",
			"type"=>"INT(6)"
		),
		"rejecteduID"=>array(
			"from"=>array(
				"table"=>"_global_access",
				"column"=>"FullName",
				"lookup"=>"ID"
			),
			"to"=>array(
				"table"=>"global_users",
				"column" => "fullName",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
		)
	),
	"nf_article_newsbook"=>array(
		"aID"=>array(

			"rename"=>"articleID",
			"type" => "INT(6)"
		),
		"uID"=>array(
			"from"=>array(
				"table"=>"_global_access",
				"column"=>"FullName",
				"lookup"=>"ID"
			),
			"to"=>array(
				"table"=>"global_users",
				"column" => "fullName",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
		),
		"nID"=>array(
			"from"=>array(
				"table"=>"_global_publications",
				"column"=>"publication",
				"lookup" => "ID"
			),
			"to"=>array(
				"table"=>"global_publications",
				"column" => "publication",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
			"rename"=>"pID",
			"type" => "INT(6)"
		),
		"ndID"=>array( // need to still do the lookups
			"rename"=>"dID",
			"type"=>"INT(6)"
		),
	),
	"nf_comments"=>array(
		"uID"=>array(
			"from"=>array(
				"table"=>"_global_access",
				"column"=>"FullName",
				"lookup"=>"ID"
			),
			"to"=>array(
				"table"=>"global_users",
				"column" => "fullName",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
		),
	),
	"nf_read_comment"=>array(
		"uID"=>array(
			"from"=>array(
				"table"=>"_global_access",
				"column"=>"FullName",
				"lookup"=>"ID"
			),
			"to"=>array(
				"table"=>"global_users",
				"column" => "fullName",
				"lookup"=>"ID",
				"create_if_doesnt_exist"=>true
			),
		),
	)

);

$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
mysql_select_db($cfg['DB']['database'], $link);

$actions = array();
$cleanup = array();
$renames = array();
FOREACH ($table_changes AS $table=>$columns){
	//echo $table . "<br>";

	foreach ($columns as $col => $args) {

		if (isset($args['type']) && $args['type']) {
			if (isset($args['rename']) && $args['rename']) {
				$renames[] = "ALTER TABLE `$table` CHANGE `$col` `" . $args['rename'] . "` " . $args['type'] . ";";
			} else {
				$renames[] = "ALTER TABLE `$table` CHANGE `$col` `$col` " . $args['type'] . ";";
			}
		}
		if ((isset($args['from']) && isset($args['to'])) && ($args['from'] && $args['to'])){
			$col_o = $col . "_old";
			$actions[] = "ALTER TABLE `$table` CHANGE `$col` `$col_o` INT( 6 );";

			//$actions[] = "UPDATE `$table` SET `$col_o` = `$col`;";
			$actions[] = "ALTER TABLE `$table` ADD `$col` INT( 6 ) NULL DEFAULT NULL AFTER `$col_o`;";


			$cleanup[] = "ALTER TABLE `$table` DROP `$col_o`;";


		}
	}

}



foreach ($actions as $action){
	$result = mysql_query($action, $link) or die(mysql_error());

}


$data_actions = array();
$p = array();
FOREACH ($table_changes AS $table => $columns){
	foreach ($columns as $col => $args) {
		$col_o = $col . "_old";
		$depend_on = isset($args['to']['depend_on']) ? $args['to']['depend_on'] : "";
		if (isset($args['from'])){

			
			$sql = "SELECT DISTINCT `$col_o` FROM `$table`;";
			$result = mysql_query($sql, $link) or die(mysql_error());

			

			$r = array();
			$s = array();
			if ($result) {
				while ($row = mysql_fetch_assoc($result)) {
					// do something with the $row
					$value = $row[$col_o];
					if ($value){
						$r[] = $row[$col_o];


						$from_t  = $args['from']['table'];
						$from_c  = $args['from']['column'];
						$from_l  = $args['from']['lookup'];

						// create_if_doesnt_exist

						$to_t = $args['to']['table'];
						$to_c = $args['to']['column'];
						$to_l = $args['to']['lookup'];


						$sql = "SELECT `$from_c` FROM `$from_t` WHERE `$from_l` = '$value' LIMIT 0,1;";
						$res = mysql_query($sql, $link) or die(mysql_error());

						$from_row = mysql_fetch_assoc($res);


						$from_value = trim($from_row[$from_c]);


						$sql = "SELECT `$to_l` FROM `$to_t` WHERE `$to_c` = '$from_value' LIMIT 0,1;";
						$res = mysql_query($sql, $link) or die(mysql_error());
						$to_row = mysql_fetch_assoc($res);
						$new = $to_row[$to_l];

						if ($args['to']['create_if_doesnt_exist'] && !$to_row){
							mysql_query("INSERT INTO $to_t ($to_c) VALUES ('$from_value');", $link) or die(mysql_error());
							$new_res = mysql_query("SELECT $to_l FROM $to_t ORDER BY ID DESC LIMIT 0,1", $link) or die(mysql_error());
							$new_row = mysql_fetch_assoc($new_res);
							$new = $new_row[$to_l];

						}

						mysql_query("UPDATE `$table` SET `$col` = '$new' WHERE `$col_o` = '$value';", $link) or die(mysql_error());

						//$s[] = "UPDATE `$table` SET `$col` = '$new' WHERE `$col_o` = '$value';";
					}

				}

			} else {
				echo mysql_error();
			}

			//foreach ($values as $val){

			//}
			$data_actions[$table][$col]['values'] = $r;
			$data_actions[$table][$col]['sql'] = $s;
		}


	}
}


foreach ($renames as $action) {
	$result = mysql_query($action, $link) or die(mysql_error());

}

$actions = array();
$table = "nf_article_newsbook";
$actions[] = "ALTER TABLE `$table` CHANGE `dID` `dID_old` INT( 6 );";
$actions[] = "ALTER TABLE `$table` ADD `dID` INT( 6 ) NULL DEFAULT NULL AFTER `dID_old`;";
$cleanup[] = "ALTER TABLE `$table` DROP `dID_old`;";

foreach ($actions as $action) {
	$result = mysql_query($action, $link) or die(mysql_error());

}


$t = array();

$sql = "SELECT DISTINCT `dID_old`, `pID` FROM `nf_article_newsbook`;";
$result = mysql_query($sql, $link) or die(mysql_error());

$vals = array();



if ($result) {
	while ($row = mysql_fetch_assoc($result)) {
		$val = $row['dID_old'];
		$pID = $row['pID'];


		$sql = "SELECT `ID`, `DateIN` FROM `_global_datelist` WHERE `ID` = '$val' LIMIT 0,1;";
		$res = mysql_query($sql, $link) or die(mysql_error());

		$from_row = mysql_fetch_assoc($res);
		$from_value = trim($from_row['DateIN']);


		$sql = "SELECT `ID`, `publish_date`, pID FROM `global_dates` WHERE `publish_date` = '$from_value' AND pID = '$pID' LIMIT 0,1;";
		$res = mysql_query($sql, $link) or die(mysql_error());
		$to_row = mysql_fetch_assoc($res);
		$new = $to_row['ID'];


		if (!$new){
			mysql_query("INSERT INTO global_dates (pID, publish_date) VALUES ('$pID','$from_value');", $link) or die(mysql_error());
			$new_res = mysql_query("SELECT ID FROM global_dates ORDER BY ID DESC LIMIT 0,1", $link) or die(mysql_error());
			$new_row = mysql_fetch_assoc($new_res);
			$new = $new_row['ID'];
		}


		$value = $from_row['ID'];
		mysql_query("UPDATE `nf_article_newsbook` SET `dID` = '$new' WHERE `dID_old` = '$value';", $link) or die(mysql_error());

		$t[] = $val;

		$vals[$val] = array("oldID" => $from_row['ID'], "pID" => $pID, "newID" => $new, "value" => $from_value,);
	}
}


foreach ($cleanup as $action) {
	$result = mysql_query($action, $link) or die(mysql_error());

}
$output["structure"]=$actions;
$output["data"]= $data_actions;

test_array($output);

?>
