<?php


class update {
	function __construct($cfg){


	}
	public static function code($cfg){
		$root_folder = dirname(dirname(__FILE__));
		$docs_folder = $root_folder . '\\docs';


		chdir($root_folder);
		$return = "";
		$return .= "<h5>Impreshin</h5>";
		if (!file_exists($root_folder."\\.git")) {
			shell_exec('git init');
		} else {
			
			shell_exec('git stash');
			shell_exec('git reset --hard HEAD');
		}


		$proxy = "";
		if (file_exists("/media/data/use_proxy")) {
			$proxy = trim(file_get_contents("/media/data/use_proxy"));
			if ($proxy) {
				shell_exec('git config http.proxy ' . $proxy . ' 2>&1');
			}

		}
		$output = shell_exec('git pull https://'.$cfg['git']['username'] .':'.$cfg['git']['password'] .'@'.$cfg['git']['path'] .' ' . $cfg['git']['branch'] . ' 2>&1');

		$str = str_replace(".git","",$cfg['git']['path']);
		$output = str_replace("From $str","", $output);
		$output = str_replace("* branch            ". $cfg['git']['branch'] ."     -> FETCH_HEAD","", $output);
		$return .= trim($output);


		if (isset($cfg['git']['docs'])){
			$return .= "<p></p><h5>Documentation</h5>";




			//echo $docs_folder;


			if (!file_exists($docs_folder)){
				mkdir($docs_folder, 2777, true);
				chdir("docs");
				shell_exec('git init');

			} else {
				chdir("docs");
				shell_exec('git reset --hard HEAD');
				shell_exec('git stash');
			}

			if ($proxy) {
				shell_exec('git config http.proxy ' . $proxy . ' 2>&1');
			}

			$output = shell_exec('git pull https://' . $cfg['git']['docs']['username'] . ':' . $cfg['git']['docs']['password'] . '@' . $cfg['git']['docs']['path'] . ' ' . $cfg['git']['docs']['branch'] . ' 2>&1');
			$str = str_replace(".git", "", $cfg['git']['docs']['path']);
			$output = str_replace("From $str", "", $output);
			$output = str_replace("* branch            " . $cfg['git']['docs']['branch'] . "     -> FETCH_HEAD", "", $output);
			$return .= trim($output);

			chdir($root_folder);

		}


		return $return;
	}

	public static function db($cfg){
		
		$link = mysqli_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password'], $cfg['DB']['database']);
		
		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		
		
		
		$sql = 'SELECT `value` FROM system WHERE `system`="db_version" LIMIT 1';
		$result = mysqli_query($link,$sql);
		
		
		if(empty($result)) {
			$query = mysqli_query($link,"CREATE TABLE IF NOT EXISTS `system` (  `ID` int(6) NOT NULL AUTO_INCREMENT,  `system` varchar(100) DEFAULT NULL,  `value` varchar(100) DEFAULT NULL,  PRIMARY KEY (`ID`))");
			
			$query = mysqli_query($link,"INSERT INTO `system` (`system`,`value`) values ('db_version','0')");
			
			$sql = 'SELECT * FROM system WHERE `system`="db_version" LIMIT 1';
			$result = mysqli_query($link,$sql);
			
		}
		$version = $result->fetch_array();
		
		if (isset($version['value'])){
			$version = $version['value'];
		}
		
		
		
		
		
		
		
		$v = $version*1;

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
					//echo substr($e, 0, 5);
					$updates = $updates + 1;
					if (substr($e, 0, 5) == "file:") {
						$file = str_replace("file:","",$e);
						$e= @file_get_contents($file);


					}
					self::db_execute($cfg,$e);
				//	mysql_query($e, $link) or die(mysql_error());


				}
			}


			if ($v){
				mysqli_query($link,"UPDATE system SET `value`='{$uv}' WHERE `system`='db_version'") or die(mysqli_error($link));
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

		passthru("mysqldump --opt --single-transaction --host=$dbhost --user=$dbuser --password=$dbpwd $dbname $gzip > $filename");


		return "$filename";// passthru("tail -1 $filename");


	}
	
	
	static function db_execute($cfg,$sql){
		$link = mysqli_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password'], $cfg['DB']['database']);
		
		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		
		$query = $sql;
		
		/* execute multi query */
		if (mysqli_multi_query($link, $query)) {
			do {
				/* store first result set */
				if ($result = mysqli_store_result($link)) {
					while ($row = mysqli_fetch_row($result)) {
						//printf("%s\n", $row[0]);
					}
					mysqli_free_result($result);
				}
				/* print divider */
				if (mysqli_more_results($link)) {
					//printf("-----------------\n");
				}
			} while (@mysqli_next_result($link));
		}
		
		/* close connection */
		mysqli_close($link);
		
	}
	
}
