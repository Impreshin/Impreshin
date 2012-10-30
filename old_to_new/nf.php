<?php
/**
 * User: William
 * Date: 2012/10/29 - 12:12 PM
 */

class nf_import {
	public static function db(){

		if (isLocal()) {


			/*
		 of: 0
		 show
		 */

			$records_show = 30;
			$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
			$newoffset = $offset + 1;

			$start_offset = $offset * $records_show;


			$DB = new DB('mysql:host=localhost;dbname=apps', 'william', 'stars');
			//$DB->sql("TRUNCATE TABLE apps.nf_articles_revisions");

			echo '<style>';
			echo 'body { font-size: 13px; }';
			echo 'del { background-color:  rgba(255, 0, 0, 0.2);}';
			echo 'ins { background-color: rgba(0, 128, 0, 0.3);}';
			echo '</style>';

			$records = $DB->sql("SELECT * FROM apps.nf_articles ORDER BY ID ASC LIMIT $start_offset,$records_show");
			$percent = 0;

			$count = $DB->sql("SELECT count(ID) as nr FROM apps.nf_articles");
			if (count($count)){
				$count = $count[0]['nr'];
			} else {
				$count = 0;
			}
			if ($count>0){
				$percent = number_format((($start_offset+ $records_show) / $count) * 100,2);
			}


			echo "showing: (" . count($records) . ") | $start_offset,$records_show | $percent%<hr> ";
			if (!count($records)) {
				echo "DONE";
				exit();
			}
			;

			foreach ($records as $record) {


				$ID = $record['ID'];
				$o = $record['article_orig'];
				$a = $record['article'];
				$s = $record['synopsis'];
				$uID = $record['authorID'];
				$lb = $record['lockedBy'];
				$d = $record['datein'];
				echo "<article>" . $record['ID'] . " | " . $record['heading'] . "<p>";


				$filesID = "";
				$files = $DB->sql("SELECT * FROM apps.nf_files WHERE aID = '$ID' ORDER BY ID ASC");
				if (count($files)) {
					$pf = array();
					foreach ($files as $file) $pf[] = $file['ID'];

					$filesID = implode(",", $pf);
				}


				$percent = 0.00;
				$patch = "";


				if ($o && $a) {
					$timer = new timer();
					$diff = percentDiff($o, $a, true);
					$t = $timer->stop($record['ID']);

					echo "added: " . ($diff['stats']['added']) . " | removed: " . ($diff['stats']['removed']) . " | old: " . $diff['stats']['old'] . " | new: " . $diff['stats']['new'] . " | percent: " . $diff['stats']['percent'] . " | time: " . $t . "<br>";


					$percent = $diff['stats']['percent'];

					$patch = $diff['patch'];


					$DB->exec("UPDATE apps.nf_articles SET percent = '" . $percent . "' WHERE ID = '" . $ID . "'");
				}
				echo $filesID;
				echo "</article><hr>";

				if ($o) {

					$DB->exec("INSERT INTO nf_articles_revisions (aID,uID,remark,synopsis,article, patch, filesID, datein) VALUES (:aID,:uID,'New Article - import',:synopsis,:article, :patch, :filesID, :datein);", array(":aID"     => $ID,
					                                                                                                                                                                                                        ":uID"     => $uID,
					                                                                                                                                                                                                        ":synopsis"=> $s,
					                                                                                                                                                                                                        ":article" => $o,
					                                                                                                                                                                                                        ":patch"   => "",
					                                                                                                                                                                                                        ":filesID" => $filesID,
					                                                                                                                                                                                                        ":datein"  => $d
					                                                                                                                                                                                                )
					);
				}
				if ($a) {
					$DB->exec("INSERT INTO nf_articles_revisions (aID,uID,remark,synopsis,article, patch, filesID, percent,  datein) VALUES (:aID,:uID,'Edit Article - import',:synopsis,:article, :patch, :filesID, :percent, :datein);", array(":aID"     => $ID,
					                                                                                                                                                                                                                             ":uID"     => $lb,
					                                                                                                                                                                                                                             ":synopsis"=> $s,
					                                                                                                                                                                                                                             ":article" => $a,
					                                                                                                                                                                                                                             ":patch"   => $patch,
					                                                                                                                                                                                                                             ":filesID" => $filesID,
					                                                                                                                                                                                                                             ":percent" => $percent,
					                                                                                                                                                                                                                             ":datein"  => $d
					                                                                                                                                                                                                                     )
					);
				}


			}

			if (count($records)) {
				//echo "redirect to " . $newoffset;

				echo "<meta http-equiv='refresh' content='1;URL=?offset=" . $newoffset . "&r=" . date("YmdHis") . "'>";
				/*exit();
			 sleep(5);
			 $app->reroute("?offset=".$newoffset);*/
			}

			exit();
		}

	}
}
