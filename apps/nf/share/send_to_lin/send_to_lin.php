<?php
namespace apps\nf\share;


use apps\nf\models as models;
use timer as timer;

class send_to_lin extends share {
	private static $instance;
	
	function __construct($meetingID = '') {
		parent::__construct();
		$this->f3->set("json", true);
		
		//test_array($this->f3->get("user")); 
	}
	
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	function routes() {
		$routes = array();
		$routes[] = array(
				"method" => 'GET|POST',
				"path" => '/app/nf/share/send_to_lin',
				"controller" => 'apps\nf\share\send_to_lin->form',
				"a" => true,
				"l" => false,
		);
		$routes[] = array(
				"method" => 'GET|POST',
				"path" => '/app/nf/share/send_to_lin/post',
				"controller" => 'apps\nf\share\send_to_lin->post',
				"a" => true,
				"l" => false,
		);
		return $routes;
	}
	
	function form() {
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$record = models\articles::getInstance()->get($ID);
		
		$company = \models\company::getInstance()->get($record['cID']);
		$companydata = json_decode($company['data'],true);
		$send_to_lin_url = isset($companydata['share']['send_to_lin']['domain'])? $companydata['share']['send_to_lin']['domain']:"";
		if ($send_to_lin_url==""){
			$this->f3->error(404);
		}
		//print_r($companydata);
		//test_array($send_to_lin_url); 
		
		
		
		$url = $send_to_lin_url.'/api/domain/_share_form';
		$timer = new timer();
		$web = new \Web();
		$data = $web->request($url);
		$data = json_decode($data['body'], true);
		$timer->stop("fetch api form data", $url);
		$recordO = new models\articles();
		$record = $recordO->get($ID);
		
		
		$data = $data['data'];
		$cats = array();
		foreach ($data['categories'] as $item) {
			$selected = '0';
			if (strtolower($item['category']) == strtolower($record['category'])) $selected = '1';
			$item['selected'] = $selected;
			$cats[] = $item;
		}
		$data['categories'] = $cats;
		
		$authors = array();
		foreach ($data['authors'] as $item) {
			$selected = '0';
			if (strtolower($item['name']) == strtolower($record['author'])) $selected = '1';
			$item['selected'] = $selected;
			$authors[] = $item;
		}
		$data['authors'] = $authors;
		
		
		$data = array(
				"record" => $record,
				"remote" => $data
		);
		//	test_array($data); 
		$this->f3->set("__runTemplate", true);
		$this->f3->set("json", false);
		
		$tmpl = new \template("form.twig", "apps/nf/share/send_to_lin");
		$tmpl->data = $data;
		echo $tmpl->render_template();
		
	}
	function post(){
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		
		$record = models\articles::getInstance()->get($ID);
		$company = \models\company::getInstance()->get($record['cID']);
		$companydata = json_decode($company['data'],true);
		$send_to_lin_url = isset($companydata['share']['send_to_lin']['domain'])? $companydata['share']['send_to_lin']['domain']:"";
		if ($send_to_lin_url==""){
			$this->f3->error(404);
		}
		
		
		
		$data = array(
				"heading" => isset($_POST['heading']) ? $_POST['heading'] : "",
				"synopsis" => isset($_POST['synopsis']) ? $_POST['synopsis'] : "",
				"article" => isset($_POST['article']) ? $_POST['article'] : "",
				"publishDate" => isset($_POST['publishDate']) ? $_POST['publishDate'] : date("Y-m-d H:i:s"),
				"catID" => isset($_POST['catID']) ? $_POST['catID'] : "",
				"pubID" => isset($_POST['pubID']) ? implode(",", $_POST['pubID']) : "",
				"authorID" => isset($_POST['authorID']) ? $_POST['authorID'] : "",
				"flags" => isset($_POST['flags']) ? implode(",", $_POST['flags']) : "",
				"tags" => isset($_POST['tags']) ? $_POST['tags'] : "",
				"tags_timeline" => isset($_POST['tags_timeline']) ? $_POST['tags_timeline'] : "",
		
		);
		
		
		$files = array();
		$include_files =  isset($_POST['files']) ? $_POST['files'] : array();
		
		foreach ($record['media'] as $item){
			if (in_array($item['ID'],$include_files)){
				$item['caption'] = isset($_POST['caption-'.$item['ID']]) ? $_POST['caption-'.$item['ID']] : "";
				$files[] = $item;
			}
		}
		
		//test_array($files);
		
		$cfg = $this->f3->get("CFG");
		
		
		
		$folder = ($cfg['upload']['folder']  . "nf/");
		
		
		
		$web = new \Web();
		$i = 0;
		foreach ($files as $item){
			$i = $i + 1;
			$filename = $item['filename'];
			$mime = $web->mime($item['filename']);
			$path = $folder. $item['folder'];
			
			if (file_exists($path.$filename)) {
				$cfile = getCurlValue($path . $filename, $mime, "nf_" . $filename);
				
				
				$data["file$i"] = $cfile;
				$n = str_replace(".", "_", $filename);
				
				$data["file-orig-nf_" . $n] = $item['filename_orig'];
				$data["file-caption-nf_" . $n] = $item['caption'];
			}
			
		}
		
		
		//test_array($data); 
		
		
		$ch = curl_init();
		$options = array(CURLOPT_URL => $send_to_lin_url . "/admin/save/articles_api_save/_save/",
				CURLOPT_RETURNTRANSFER => true,
				CURLINFO_HEADER_OUT => true, //Request header
				CURLOPT_HEADER => true, //Return header
				CURLOPT_SSL_VERIFYPEER => false, //Don't veryify server certificate
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $data
		);
		
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		$header_info = curl_getinfo($ch,CURLINFO_HEADER_OUT);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($result, 0, $header_size);
		$body = substr($result, $header_size);
		curl_close($ch);
		
		
		
		
		
		
		test_array(array(
				'header'=>array(
						"sent"=>$header_info,
						"recieved"=>$header
				),
				"body"=>$body
		));
		
		
		
		
	}
	
	
}
function getCurlValue($filename, $contentType, $postname)
{
	// PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
	// See: https://wiki.php.net/rfc/curl-file-upload
	if (function_exists('curl_file_create')) {
		return curl_file_create($filename, $contentType, $postname);
	}
	
	// Use the old style if using an older version of PHP
	$value = "@{$filename};filename=" . $postname;
	if ($contentType) {
		$value .= ';type=' . $contentType;
	}
	
	return $value;
}
