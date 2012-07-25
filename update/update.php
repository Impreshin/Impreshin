<?php
/**
 * User: William
 * Date: 2012/07/24 - 10:43 AM
 */

class update {
	function __construct($cfg){


	}
	public static function code(){



		//exec("git pull https://WilliamStam:awssmudge1@github.com/WilliamStam/Press-Apps.git master");


		$output = shell_exec('git checkout master -f');
		$output = shell_exec('git pull https://WilliamStam:awssmudge1@github.com/WilliamStam/Press-Apps.git master 2>&1');
		$output = str_replace("From https://github.com/WilliamStam/Press-Apps","", $output);
		$output = str_replace("* branch            master     -> FETCH_HEAD","", $output);
		$output = trim($output);
		return $output;
	}

	public static function db($cfg){



		$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
		mysql_select_db($cfg['DB']['database'], $link);
		$sql = 'SELECT `value` FROM system WHERE `system`="db_version" LIMIT 1';
		$result = mysql_query($sql, $link) or die(mysql_error());
		$row = mysql_fetch_assoc($result);

		$v = $row['value'];









		include_once("db_update.php");


		$uv = key(array_slice($sql, -1, 1, TRUE));
		$updates = 0;
		$result = "";
		$return = "";
		if ($uv != $v) {
			$result = self::db_backup($cfg,"update_cv".$v);
			$nsql = array();

			foreach ($sql as $version=> $exec) {

				if ($version > $v) {
					foreach ($exec as $t) {
						$nsql[] = $t;
					}

				}
			}
			$sql = array_values($nsql);


			foreach ($sql as $e) {
				//echo $e . "<br>";
				if ($e) {
					$updates = $updates + 1;
					mysql_query($e, $link) or die(mysql_error());
				}
			}


			if ($v){
				mysql_query("UPDATE system SET `version`='$uv' WHERE `system`='db_version'", $link) or die(mysql_error());
			} else {
				mysql_query("INSERT INTO system(`system`, `value`) VALUES('db_version','$uv')", $link) or die(mysql_error());
			}


		}

		if ($result){
			$return .= "Backup name: " . $result."<br>";
		}
		if ($updates){
			$return .= "Updates: " . $updates."<br>";
		} else {
			$return .= "Already up-to-date.<br>";
		}

		return $return;
	}
	static function db_backup($cfg,$append_file_name){

		$filename = $cfg['backup'] . date("Y_m_d_H_i_s") . "_". $append_file_name. ".sql";

		$dbhost = $cfg['DB']['host'];
		$dbuser = $cfg['DB']['username'];
		$dbpwd = $cfg['DB']['password'];
		$dbname = $cfg['DB']['database'];

		if (!file_exists($cfg['backup'])) @mkdir($cfg['backup'], 0777, true);

		passthru("mysqldump --opt --host=$dbhost --user=$dbuser --password=$dbpwd $dbname > $filename");


		return "$filename";// passthru("tail -1 $filename");


	}
}
