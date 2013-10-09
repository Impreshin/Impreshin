<?php
/**
 * User: William
 * Date: 2013/05/08 - 11:20 AM
 */
namespace models;
use \timer as timer;


class import {
	function __construct() {
		$this->f3 = \Base::instance();


	}

	public static function _do($source,$target) {
		$return = "";
		$f3 = \Base::instance();

	//	test_array(array($source,$target));

		$cfg = $f3->get("cfg");


		$fromDB = new \DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $source . '', $cfg['DB']['username'], $cfg['DB']['password']);
		$toDB = new \DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $target . '', $cfg['DB']['username'], $cfg['DB']['password']);




		$tables = self::_tables();


		$from = array();
		$to = array();
		$cleanup = array();
		foreach ($tables as $table => $vals) {
			foreach ($vals['change'] as $col) {
				$col_o = $col . "_old";
				$from[] = "ALTER TABLE `$table` ADD `$col_o` INT( 6 ) NULL DEFAULT NULL AFTER `$col`;";
				$from[] = "UPDATE `$table` SET `$col_o` = `$col`;";


				$to[] = "ALTER TABLE `$table` ADD `$col_o` INT( 6 ) NULL DEFAULT NULL AFTER `$col`,  ADD INDEX(`$col_o`);";
				$cleanup[] = "ALTER TABLE `$table` DROP `$col_o`;";
			}
			$to[] = "ALTER TABLE `$table` ADD `importing` TINYINT( 1 ) NULL DEFAULT NULL ;";
			$cleanup[] = "ALTER TABLE `$table` DROP `importing`;";

		}


		foreach ($from as $sql) {
			$fromDB->exec($sql);
		}

		foreach ($to as $sql) {
			$toDB->exec($sql);
		}


		$toImport = array();

		$outputTables = array();
		foreach ($tables as $table => $vals) {
			$colsData = $fromDB->exec("SHOW COLUMNS FROM $table");
			$cols = array();
			foreach ($colsData as $col) {
				if (!in_array($col['Field'], array("ID"))) {
					$cols[] = "`" . $col['Field'] . "`";
				}
			}


			$colsString = implode(",", $cols);
			$toImport_sql = "INSERT INTO `" . $target . "`.`$table` ($colsString,`importing`) SELECT $colsString, '1' as importing FROM `" . $source . "`.`$table`";
			$toDB->exec($toImport_sql);
			$toImport[] = $toImport_sql;


			$lookup_sql = "";

			$outputTables[$table]['i'] = $toImport_sql;


		}

		$outputTables = array();
		foreach ($tables as $table => $vals) {
			$lookup = $vals['lookup'];
			$table_str = "`$table`";


			$sql_col = array();
			foreach ($vals['lookup'] as $lookup_table => $cols) {
				$lookup_table_str = "`$lookup_table`";
				foreach ($cols as $col => $lookup_col) {
					$lookup_col_old = $lookup_col . "_old";
					$sql_col[] = "`$col` = COALESCE((SELECT `$lookup_col` FROM $lookup_table_str WHERE $lookup_table_str.`$lookup_col_old` = $table_str.`$col` LIMIT 0,1),NULL)  ";


				}
				//$sql[] = $cols;
			}

			$sql = "";
			if (count($sql_col)) {
				$sql_col = implode(", ", $sql_col);
				$sql = "UPDATE $table_str SET " . $sql_col . " WHERE importing = '1'";
				$toDB->exec($sql);
			}


			$outputTables[$table]['l'] = $sql;

		}


		$sql = "UPDATE `ab_bookings` AS a JOIN ab_bookings AS b ON a.repeat_from = b.ID_old SET a.repeat_from = b.ID  WHERE a.importing = '1'";
		$toDB->exec($sql);


		foreach ($cleanup as $sql) {
			$toDB->exec($sql);
		}









		return $return;

	}

	public static function _tables(){
		$tables = array(
			"nf_articles"              => array(
				"lookup" => array(
					"global_companies"   => array(
						"cID" => "ID"
					),
					"ab_accounts_status" => array(
						"statusID" => "ID"
					)
				)
			),


		);
		return $tables;
	}



}
