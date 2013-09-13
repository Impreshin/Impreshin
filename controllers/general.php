<?php
/*
 * Date: 2012/02/23
 * Time: 12:55 PM
 */

class general {
	function __construct() {
		$this->f3 = \base::instance();
	}

	function css_min() {
		ob_start("ob_gzhandler");
		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		header("Content-Type: text/css");
		$expires = 60 * 60 * 24 * 14;
		header("Pragma: public");
		header("Cache-Control: maxage=" . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
		if ($file) {

			$fileDetails = pathinfo(($file));
			$base = "." . $fileDetails['dirname'] . "/";
			$file = $fileDetails['basename'];

			//echo $base."\n".$file;
			$t = file_get_contents($base . $file);

		} else {

			$files = array(
				//"/ui/_css/ui-lightness/jquery-ui.css",
				"/ui/_css/bootstrap.css",
				"/ui/_css/jquery.jscrollpane.css",
				"/ui/_css/ui.daterangepicker.css",
				"/ui/_css/select2.css",
				"/ui/_css/jquery.miniColors.css",
				"/ui/_css/style.css",
				"/ui/_css/FeedEk.css",
				"/ui/jqPlot/jquery.jqplot.min.css",
				"/ui/fancybox/jquery.fancybox.css"
			);


			$t = "";
			$base = array();
			foreach ($files as $file) {
				$fileDetails = pathinfo(($file));
				$base = "." . $fileDetails['dirname'] . "/";
				$file = $fileDetails['basename'];

				$t .= file_get_contents($base . $file);

			}


		}

		$t = preg_replace('/\s+/', ' ', $t);
		//exit($this->minify($t,"css"));
		exit($t);


	}

	function js_min() {
		ob_start("ob_gzhandler");

		$expires = 60 * 60 * 24 * 14;
		header("Pragma: public");
		header("Cache-Control: maxage=" . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		//$file = $this->f3->get('PARAMS.filename');
		header("Content-Type: application/x-javascript");
		$t = "";
		if ($file) {
			$fileDetails = pathinfo(($file));
			$base = "." . $fileDetails['dirname'] . "/";
			$file = $fileDetails['basename'];
			$t = file_get_contents($base . $file);
		} else {
			$files = array(
				"/ui/_js/plugins/jquery.jscrollpane.js",
				"/ui/_js/libs/jquery-ui.js",


				"/ui/_js/libs/bootstrap.js",

				// ------ //
				"/ui/_js/plugins/date.js",
				"/ui/_js/plugins/jquery.hotkeys.js",

				"/ui/_js/plugins/jquery.daterangepicker.js",
				"/ui/_js/plugins/jquery.mousewheel.js",
				//"/ui/_js/plugins/mwheelIntent.js",
				"/ui/_js/plugins/jquery.jqote2.js",
				"/ui/_js/plugins/jquery.ba-bbq.js",
				"/ui/_js/plugins/jquery.cookie.js",
				"/ui/_js/plugins/jquery.autologout.js",
				"/ui/_js/plugins/select2.js",
				"/ui/_js/plugins/jquery.getData.js",
				"/ui/fancybox/jquery.fancybox.pack.js",


				//"/ui/plupload/js/browserplus-min.js",
				"/ui/plupload/js/plupload.full.js",



				"/ui/jqPlot/jquery.jqplot.js",
				"/ui/jqPlot/plugins/jqplot.highlighter.js",
				"/ui/jqPlot/plugins/jqplot.cursor.min.js",
				"/ui/jqPlot/plugins/jqplot.canvasTextRenderer.min.js",
				"/ui/jqPlot/plugins/jqplot.canvasAxisLabelRenderer.min.js",
				"/ui/jqPlot/plugins/jqplot.canvasAxisTickRenderer.min.js",
				"/ui/jqPlot/plugins/jqplot.categoryAxisRenderer.min.js",
				"/ui/jqPlot/plugins/jqplot.pointLabels.min.js",
				"/ui/jqPlot/plugins/jqplot.trendline.min.js",


			);


			$t = "";
			foreach ($files as $file) {
				$fileDetails = pathinfo(($file));
				$base = "." . $fileDetails['dirname'] . "/";
				$file = $fileDetails['basename'];

				$t .= file_get_contents($base . $file);

			}


		}

		//$t = preg_replace('/\s+/', ' ', $t);
		exit($t);
		//exit($this->minify($t, "js"));


	}


	function upload() {
		$folder = (isset($_GET['folder'])) ? $_GET['folder'] : "";
		//$folder = substr($folder,0,-1);

		$cfg = $this->f3->get("CFG");


		$user = $this->f3->get("user");


		$app = $this->f3->get('PARAMS.app');




		$folder = ($cfg['upload']['folder'] . $app . "/" . $folder);
		$tmpFolder = $cfg['upload']['folder'] . 'tmp/';

		$folder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $folder);
		$tmpFolder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $tmpFolder);

/*

		test_array(array(
			           "folder"=>$folder,
			           "tmp_folder"=>$tmpFolder,
			         //  "name"=> $_REQUEST["name"],
			         //  "cfg"  => $cfg,
			         //  "user" => $user,
			           "app"  => $app

		           )
		);

*/
		ini_set('upload_tmp_dir', $tmpFolder);
		ini_set('upload_max_filesize', '20M');
		ini_set('post_max_size', '20M');


		if (!file_exists($tmpFolder)) @mkdir($tmpFolder, 0777, true);
		if (!file_exists($folder)) @mkdir($folder, 0777, true);

		//$targetDir = $cfg['upload']['folder'] . $app . "/temp/";
		//if (!file_exists($targetDir)) @mkdir($targetDir, 0777, true);

		$targetDir = $folder;


		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-Type: application/json");

// Settings
//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";


		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds

// 5 minutes execution time
		@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) $count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Create target dir
		if (!file_exists($targetDir)) @mkdir($targetDir);

// Remove old temp files
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}

			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"])) $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"])) $contentType = $_SERVER["CONTENT_TYPE"];

// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096)) fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096)) fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
		}

/*
		$exif = @exif_read_data($targetDir . DIRECTORY_SEPARATOR . $fileName,0,true);
// Return JSON-RPC response

		$result = array(
			"jsonrpc"=>"2.0",
			"result"=>"null",
			"id"=>"id"
		);

		if ($exif){
			$result['exif']['meta'] = $exif['IFD0'];
		}
		test_array($result);*/
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

	}


}
