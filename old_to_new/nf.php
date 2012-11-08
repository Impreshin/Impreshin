<?php
/**
 * User: William
 * Date: 2012/10/29 - 12:12 PM
 */

class nf_import {
	function __construct(){
		$this->old_dbname = "apps";
		$this->old_DB = new DB('mysql:host=localhost;dbname=' . $this->old_dbname, 'william', 'stars');

		$this->new_DB = F3::get("DB");

		echo '<link rel="stylesheet" type="text/css" href="/ui/_css/bootstrap.css"/>';
		echo '<style>';
		echo 'form {margin:0;padding:0}';
		echo 'button {margin-top:-5px}';
		echo 'h1 {margin-top:20px; margin-bottom: 10px; background-color: #e1e1e1;}';
		//echo 'body { font-size: 13px; }';
		echo 'del { background-color:  rgba(255, 0, 0, 0.2);}';
		echo 'ins { background-color: rgba(0, 128, 0, 0.3);}';
		echo 'table, article {margin-bottom:0;}';
		echo 'article {padding:0;}';
		echo '</style>';

	}
	public  function records(){

		if (isLocal()) {


			/*
		 of: 0
		 show
		 */

			$records_show = 100;
			$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
			$newoffset = $offset + 1;

			$start_offset = $offset * $records_show;



			$DB = $this->old_DB;
			$dbname = $this->old_dbname;
			//$DB->sql("TRUNCATE TABLE apps.nf_articles_revisions");


			if ($offset==0){
				/*
				$files = $DB->sql("SELECT * FROM " . $dbname . ".nf_files ORDER BY ID ASC");

				foreach ($files as $file) {
					$aID = $file['aID'];
					$fID = $file['ID'];
					$DB->sql("INSERT INTO " . $dbname . ".nf_articles_files_link (articleID, fileID) values ('$aID','$fID')");
				}*/
			}





			$records = $DB->sql("SELECT * FROM " . $dbname . ".nf_articles ORDER BY ID ASC LIMIT $start_offset,$records_show");
			$percent = 0;

			$count = $DB->sql("SELECT count(ID) as nr FROM " . $dbname . ".nf_articles");
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
			echo '<div class="progress progress-striped active">
  <div class="bar" style="width: '.$percent.'%;"></div>
</div>';

			echo '<table class="table">';
			echo '<tr>';
			echo '<th>ID</th>';
			echo '<th>heading</th>';
			echo '<th>Added</th>';
			echo '<th>Removed</th>';
			echo '<th>Old</th>';
			echo '<th>New</th>';
			echo '<th>Percent</th>';
			echo '<th>Time</th>';
			echo '</tr>';

			foreach ($records as $record) {


				$ID = $record['ID'];
				$o = $record['article_orig'];
				$a = $record['article'];
				$s = $record['synopsis'];
				$uID = $record['authorID'];
				$lb = $record['lockedBy'];
				$d = $record['datein'];

				echo '<td>'.$record['ID'].'</td>';
				echo '<td>'.$record['heading'].'</td>';






				$percent = 0.00;
				$patch = "";


				if ($o && $a) {
					$timer = new timer();
					$diff = percentDiff($o, $a, true);
					$t = $timer->stop($record['ID']);

					echo '<td>' . $diff['stats']['added'] . '</td>';
					echo '<td>' . $diff['stats']['removed'] . '</td>';
					echo '<td>' . $diff['stats']['old'] . '</td>';
					echo '<td>' . $diff['stats']['new'] . '</td>';
					echo '<td>' . $diff['stats']['percent'] . '</td>';
					echo '<td>' . $t . '</td>';


					$percent = $diff['stats']['percent'];

					$patch = $diff['patch'];


					$DB->exec("UPDATE ".$dbname.".nf_articles SET percent = '" . $percent . "' WHERE ID = '" . $ID . "'");
				} else {
					echo '<td colspan="6">-</td>';
				}
				echo "</tr>";



			}
			echo '</table>';

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
	public  function users(){
		if (isLocal()) {
			$DB = $this->old_DB;
			$dbname = $this->old_dbname;
			$newDB = $this->new_DB;


			$cfg_email_domain = "@zoutnet.co.za";
			$cfg_cID = "1";




			if (isset($_POST['newID']) && isset($_GET['ID']) && isset($_GET['section'])){

				$oldID = $_GET['ID'];
				$newID = $_POST['newID'];

				$section = $_GET['section'];


				if ($section=='users'){
					$new_user_axon = new \Axon("global_users", $newDB);
					$old_user_axon = new \Axon("_global_access", $DB);
					$old_user_axon->load("ID='" . $oldID . "'");
					if ($newID == "new") {
						$old_username = $old_user_axon->Username;

						$new_user_axon->fullName = $old_user_axon->FullName;
						$new_user_axon->email = $old_username . $cfg_email_domain;
						$new_user_axon->password = $old_username;

						$new_user_axon->save();


						$ID = $new_user_axon->_id;
					} else {
						$new_user_axon->load("ID='" . $newID . "'");
						$ID = $new_user_axon->ID;
					}

					$axon_global_users_company = new \Axon("global_users_company", $newDB);
					$axon_global_users_company->load("cID='$cfg_cID' and uID = '$ID'");
					$axon_global_users_company->cID = $cfg_cID;
					$axon_global_users_company->uID = $ID;
					$axon_global_users_company->nf = '1';
					$axon_global_users_company->save();


					$DB->exec("UPDATE nf_articles SET new_authorID='$ID' WHERE authorID = '$oldID'");
					$DB->exec("UPDATE nf_articles SET new_lockedBy='$ID' WHERE lockedBy = '$oldID'");
					$DB->exec("UPDATE nf_articles SET new_rejecteduID='$ID' WHERE rejecteduID = '$oldID'");

					$DB->exec("UPDATE nf_article_newsbook SET new_uID='$ID' WHERE uID = '$oldID'");
					$DB->exec("UPDATE nf_article_newsbook SET new_placedBy='$ID' WHERE placedBy = '$oldID'");

					$DB->exec("UPDATE nf_comments SET new_uID='$ID' WHERE uID = '$oldID'");


					$old_user_axon->newID = $ID;
					$old_user_axon->save();


				} elseif  ($section == "publications"){

					$new_axon = new \Axon("global_publications", $newDB);
					$old_axon = new \Axon("_global_publications", $DB);
					$old_axon->load("ID='" . $oldID . "'");
					if ($newID == "new") {
						$new_axon->cID = $cfg_cID;
						$new_axon->publication = $old_axon->publication;
						$new_axon->save();

						$ID = $new_axon->_id;
					} else {
						$new_axon->load("ID='" . $newID . "'");
						$ID = $new_axon->ID;
					}

					$DB->exec("UPDATE nf_article_newsbook SET new_pID='$ID' WHERE pID = '$oldID'");
					$DB->exec("UPDATE _global_datelist SET new_pID='$ID' WHERE pID = '$oldID'");


					$old_axon->newID = $ID;
					$old_axon->save();

					$new_axon->reset();
					$old_axon->reset();
				}





				F3::reroute("?t=".date('YmdHs'));
			}



			$access = $DB->exec("SELECT *, (SELECT count(ID) FROM $dbname.nf_articles WHERE $dbname._global_access.ID = $dbname.nf_articles.authorID ) as `count` FROM $dbname._global_access WHERE newID is null or newID = '0' ORDER BY FullName ASC;");

			$new_access = $this->new_DB->exec("SELECT * FROM global_users ORDER BY fullName ASC");

			$publications_old = $DB->exec("SELECT * FROM $dbname._global_publications WHERE newID is null or newID = '0';");
			$publications_new = $this->new_DB->exec("SELECT * FROM global_publications");

			if (!count($access) && !count($publications_old)) {
				$this->records();
			}



			if (count($access)){


				echo '<h1>Users</h1>';
				echo '<table class="table">';
				echo '<tr>';
				echo '<th>ID</th>';
				echo '<th>Name</th>';
				echo '<th>Username</th>';
				echo '<th>count</th>';
				echo '<th>New</th>';
				echo '</tr>';
				foreach ($access as $user){
					echo '<tr>';
					echo '<td>'.$user['ID'].'</td>';
					echo '<td>'.$user['FullName'].'</td>';
					echo '<td>'.$user['Username'].'</td>';
					echo '<td>'.$user['count'].'</td>';
					echo '<td>';
					echo '<form method="POST" action="?ID='.$user['ID'].'&section=users">';
					echo '<select name="newID" id="newID">';
					echo '<option value="new">Create a new User</option>';
					echo '<optgroup label="Users">';
					foreach ($new_access as $new_user){
						$selected = '';
						if ($user['FullName'] == $new_user['fullName']){
							$selected = 'selected="selected"';
						}
						echo '<option value="'.$new_user['ID'].'" '.$selected.'>'.$new_user['fullName'].' - ' . $new_user['email'].'</option>';
					}
					echo '</optgroup>';

					echo '</select> ';
					echo '<button type="submit" class="btn">Save</button>';
					echo '</form>';
					echo '</td>';

					echo '</tr>';
				}
				echo '</table>';

				echo '<hr>';
			}

			if (count($publications_old)){
				echo '<h1>Publications</h1>';
				echo '<table class="table">';
				echo '<tr>';
				echo '<th>ID</th>';
				echo '<th>publication</th>';
				echo '<th>New</th>';
				echo '</tr>';

				foreach ($publications_old as $row) {
					echo '<tr>';
					echo '<td>' . $row['ID'] . '</td>';
					echo '<td>' . $row['publication'] . '</td>';
					echo '<td>';
					echo '<form method="POST" action="?ID=' . $row['ID'] . '&section=publications">';
					echo '<select name="newID" id="newID">';
					echo '<option value="new">Create a new Publication</option>';
					echo '<optgroup label="Users">';
					foreach ($publications_new as $row_sub) {
						$selected = '';
						if ($row['publication'] == $row_sub['publication']) {
							$selected = 'selected="selected"';
						}
						echo '<option value="' . $row_sub['ID'] . '" ' . $selected . '>' . $row_sub['publication'] . '</option>';
					}
					echo '</optgroup>';

					echo '</select> ';
					echo '<button type="submit" class="btn">Save</button>';
					echo '</form>';
					echo '</td>';

					echo '</tr>';
				}

				echo '</table>';
			}

			exit();


		}
	}
}
