<?php
/**
 * User: William
 * Date: 2012/07/24 - 10:43 AM
 */

class update {
	function __construct($cfg){


	}
	public static function code($cfg){
		$return = "";
		$return .= "<h5>Impreshin</h5>";
		shell_exec('git stash');
		$output = shell_exec('git pull https://'.$cfg['git']['username'] .':'.$cfg['git']['password'] .'@'.$cfg['git']['path'] .' ' . $cfg['git']['branch'] . ' 2>&1');

		$str = str_replace(".git","",$cfg['git']['path']);
		$output = str_replace("From $str","", $output);
		$output = str_replace("* branch            ". $cfg['git']['branch'] ."     -> FETCH_HEAD","", $output);
		$return .= trim($output);


		if (isset($cfg['git']['docs'])){
			$return .= "<p></p><h5>Documentation</h5>";


			$docs_folder = $_SERVER['DOCUMENT_ROOT'] . '\\docs';


			if (!file_exists($docs_folder)){
				mkdir($docs_folder, 0777, true);
				shell_exec('cd docs; git init');

			}
			shell_exec('cd docs; git stash');
			$output = shell_exec('cd docs; git pull https://' . $cfg['git']['docs']['username'] . ':' . $cfg['git']['docs']['password'] . '@' . $cfg['git']['docs']['path'] . ' ' . $cfg['git']['docs']['branch'] . ' 2>&1');
			$str = str_replace(".git", "", $cfg['git']['docs']['path']);
			$output = str_replace("From $str", "", $output);
			$output = str_replace("* branch            " . $cfg['git']['docs']['branch'] . "     -> FETCH_HEAD", "", $output);
			$return .= trim($output);
		}


		return $return;
	}

	public static function db($cfg){

		$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
		mysql_select_db($cfg['DB']['database'], $link);
		$sql = 'SELECT `value` FROM system WHERE `system`="db_version" LIMIT 1';
		$result = mysql_query($sql, $link) or die(mysql_error());
		$row = mysql_fetch_assoc($result);

		$v = $row['value']*1;

		include_once("db_update.php");

		$uv = key(array_slice($sql, -1, 1, TRUE));
		$updates = 0;
		$result = "";
		$return = "";
		$filename = "backup_cv" . $v;


		if ($uv != $v) {

			$nsql = array();

			foreach ($sql as $version=> $exec) {
				$version = $version * 1;
				if ($version > $v) {
					foreach ($exec as $t) {
						$nsql[] = $t;
					}

				}
			}
			$sql = array_values($nsql);

			if (count($nsql) > 0) $filename = "update_cv" . $v;
			$result = self::db_backup($cfg, $filename);


			foreach ($sql as $e) {
				//echo $e . "<br>";
				if ($e) {
					$updates = $updates + 1;
					mysql_query($e, $link) or die(mysql_error());
				}
			}


			if ($v){
				mysql_query("UPDATE system SET `value`='$uv' WHERE `system`='db_version'", $link) or die(mysql_error());
			} else {
				mysql_query("INSERT INTO system(`system`, `value`) VALUES('db_version','$uv')", $link) or die(mysql_error());
			}


		} else {

			$result = self::db_backup($cfg, $filename);
		}

		if ($result){
			$return .= "Backup name: " . $result."<br>";
		}
		if ($updates!=0){
			$return .= "Updates: " . $updates."<br>";
		} else {
			$return .= "Already up-to-date.<br>";
		}

		return $return;
	}
	static function db_backup($cfg,$append_file_name){

		$filename = $cfg['backup'] . date("Y_m_d_H_i_s") . "_". $append_file_name. "_" . $cfg['git']['branch'] . ".sql";

		$dbhost = $cfg['DB']['host'];
		$dbuser = $cfg['DB']['username'];
		$dbpwd = $cfg['DB']['password'];
		$dbname = $cfg['DB']['database'];
		$gzip = $cfg['gzip'];

		if (!file_exists($cfg['backup'])) @mkdir($cfg['backup'], 0777, true);

		if ($gzip){
			$filename = $filename . ".gz";
			$gzip = "| gzip ";
		} else {
			$gzip = "";
		}

		passthru("mysqldump --opt --host=$dbhost --user=$dbuser --password=$dbpwd $dbname $gzip > $filename");


		return "$filename";// passthru("tail -1 $filename");


	}
}
