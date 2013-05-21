<?php
/*
 * Date: 2013/05/16
 * Time: 10:50 AM
 */

$cfg = array();
require_once('config.default.inc.php');
require_once('config.inc.php');

require_once('/inc/functions.php');
require_once('/update/update.php');


echo "DB backup created</b>\n";
echo "</b>\n";
echo update::db_backup($cfg, "charset");
echo "</b>\n";
echo "</b>\n";
echo '<hr>\n';

function MysqlError() {
	if (mysql_errno()) {
		echo "<b>Mysql Error: " . mysql_error() . "</b>\n";
	}
}




	$username = $cfg['DB']['username'];
	$password = $cfg['DB']['password'];
	$db = $cfg['DB']['database'];
	$host = $cfg['DB']['host'];

	$target_charset = "utf8";
	$target_collate = "utf8_general_ci";

	echo "<pre>";

	$conn = mysql_connect($host, $username, $password);
	mysql_select_db($db, $conn);

	$tabs = array();
	$res = mysql_query("SHOW TABLES");
	MysqlError();
	while (($row = mysql_fetch_row($res)) != null) {
		$tabs[] = $row[0];
	}

// now, fix tables
	foreach ($tabs as $tab) {
		$res = mysql_query("show index from {$tab}");
		MysqlError();
		$indicies = array();

		while (($row = mysql_fetch_array($res)) != null) {
			if ($row[2] != "PRIMARY") {
				$indicies[] = array(
					"name"   => $row[2],
					"unique" => !($row[1] == "1"),
					"col"    => $row[4]
				);
				mysql_query("ALTER TABLE {$tab} DROP INDEX {$row[2]}");
				MysqlError();
				echo "Dropped index {$row[2]}. Unique: {$row[1]}\n";
			}
		}

		$res = mysql_query("DESCRIBE {$tab}");
		MysqlError();
		while (($row = mysql_fetch_array($res)) != null) {
			$name = $row[0];
			$type = $row[1];
			$set = false;
			if (preg_match("/^varchar\((\d+)\)$/i", $type, $mat)) {
				$size = $mat[1];
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} VARBINARY({$size})");
				MysqlError();
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} VARCHAR({$size}) CHARACTER SET {$target_charset}");
				MysqlError();
				$set = true;

				echo "Altered field {$name} on {$tab} from type {$type}\n";
			} else if (!strcasecmp($type, "CHAR")) {
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} BINARY(1)");
				MysqlError();
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} VARCHAR(1) CHARACTER SET {$target_charset}");
				MysqlError();
				$set = true;

				echo "Altered field {$name} on {$tab} from type {$type}\n";
			} else if (!strcasecmp($type, "TINYTEXT")) {
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} TINYBLOB");
				MysqlError();
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} TINYTEXT CHARACTER SET {$target_charset}");
				MysqlError();
				$set = true;

				echo "Altered field {$name} on {$tab} from type {$type}\n";
			} else if (!strcasecmp($type, "MEDIUMTEXT")) {
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} MEDIUMBLOB");
				MysqlError();
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} MEDIUMTEXT CHARACTER SET {$target_charset}");
				MysqlError();
				$set = true;

				echo "Altered field {$name} on {$tab} from type {$type}\n";
			} else if (!strcasecmp($type, "LONGTEXT")) {
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} LONGBLOB");
				MysqlError();
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} LONGTEXT CHARACTER SET {$target_charset}");
				MysqlError();
				$set = true;

				echo "Altered field {$name} on {$tab} from type {$type}\n";
			} else if (!strcasecmp($type, "TEXT")) {
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} BLOB");
				MysqlError();
				mysql_query("ALTER TABLE {$tab} MODIFY {$name} TEXT CHARACTER SET {$target_charset}");
				MysqlError();
				$set = true;

				echo "Altered field {$name} on {$tab} from type {$type}\n";
			}

			if ($set) mysql_query("ALTER TABLE {$tab} MODIFY {$name} COLLATE {$target_collate}");
		}

		// re-build indicies..
		foreach ($indicies as $index) {
			if ($index["unique"]) {
				mysql_query("CREATE UNIQUE INDEX {$index["name"]} ON {$tab} ({$index["col"]})");
				MysqlError();
			} else {
				mysql_query("CREATE INDEX {$index["name"]} ON {$tab} ({$index["col"]})");
				MysqlError();
			}

			echo "Created index {$index["name"]} on {$tab}. Unique: {$index["unique"]}\n";
		}

		// set default collate
		mysql_query("ALTER TABLE {$tab}  DEFAULT CHARACTER SET {$target_charset} COLLATE {$target_collate}");
	}

// set database charset
	mysql_query("ALTER DATABASE {$db} DEFAULT CHARACTER SET {$target_charset} COLLATE {$target_collate}");

	mysql_close($conn);
	echo "</pre>";

 ?>
