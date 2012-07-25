<?php
/**
 * User: William
 * Date: 2012/07/24 - 10:43 AM
 */

class update {
	function __construct(){

	}
	public static function code(){



		//exec("git pull https://WilliamStam:awssmudge1@github.com/WilliamStam/Press-Apps.git master");

		echo "Updating...<hr>";
		$output = shell_exec('git reset --hard HEAD');
		$output = shell_exec('git pull https://WilliamStam:awssmudge1@github.com/WilliamStam/Press-Apps.git master 2>&1');
		echo "<pre>$output</pre>";
		exit();
	}

	public static function db($cfg){

		$db_update = new \Axon("system");
		$db_update->load("`system`='db_version'");

		include_once("db_update.php");





		$v = $db_update->value;
		$uv = key(array_slice($sql, -1, 1, TRUE));
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
					F3::get("DB")->exec($e);
				}
			}
			$db_update->system = "db_version";
			$db_update->value = $uv;

			$db_update->save();

		}

	}
	static function db_backup($cfg,$append_file_name){

		$filename = $cfg['backup'] . date("Y_m_d_H_i_s") . "_". $append_file_name. ".sql";

		$dbhost = $cfg['DB']['host'];
		$dbuser = $cfg['DB']['username'];
		$dbpwd = $cfg['DB']['password'];
		$dbname = $cfg['DB']['database'];

		if (!file_exists($cfg['backup'])) @mkdir($cfg['backup'], 0777, true);

		passthru("mysqldump --opt --host=$dbhost --user=$dbuser --password=$dbpwd $dbname > $filename");


		return "$filename ";// passthru("tail -1 $filename");


	}
}
