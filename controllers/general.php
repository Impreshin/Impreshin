<?php
/*
 * Date: 2012/02/23
 * Time: 12:55 PM
 */

class general {
	function css_min(){
		ob_start("ob_gzhandler");
		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		header("Content-Type: text/css");
		if ($file) {

			$fileDetails = pathinfo(($file));
			$base = "." . $fileDetails['dirname'] . "/";
			$file = $fileDetails['basename'];

			//echo $base."\n".$file;
			$t = file_get_contents($base . $file);

		} else {

			$files = array(
				"/ui/_css/ui-lightness/jquery-ui.css",
				"/ui/_css/bootstrap.css",
				"/ui/_css/jquery.jscrollpane.css",
				"/ui/_css/ui.daterangepicker.css",
				"/ui/_css/style.css"
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

		exit($t);



	}

	function js_min() {
		ob_start("ob_gzhandler");

		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		//$file = F3::get('PARAMS.filename');
		header("Content-Type: application/x-javascript");
		$t = "";
		if ($file) {
			$fileDetails = pathinfo(($file));
			$base = "." . $fileDetails['dirname'] . "/";
			$file = $fileDetails['basename'];
			$t = file_get_contents($base . $file);
		} else {
			$files = array(
				"_js/libs/jquery-ui.js",
				"_js/libs/bootstrap.min.js",
				"_js/plugins/bootstrap-datepicker.js",
				// ------ //
				"_js/plugins/date.js",
				"_js/plugins/jquery.daterangepicker.js",
				"_js/plugins/jquery.mousewheel.js",
				"_js/plugins/mwheelIntent.js",
				"_js/plugins/jquery.jscrollpane.js",
				"_js/plugins/jquery.jqote2.js",
				"_js/plugins/jquery.ba-bbq.js",
				"_js/plugins/jquery.cookie.js",
				"_js/plugins/jquery.autologout.js",
				"_js/plugins/jquery.scrollto.min.js",



			);


			$t = "";
			foreach ($files as $file) {
				$base = F3::get("UI");
				$t .= file_get_contents($base . $file);

			}


		}
		exit($t);



	}
	function upload() {
		$folder = (isset($_GET['folder'])) ? $_GET['folder'] : "";

		if ($folder) {

			$folder = F3::get("MEDIA_folder") . $folder;
			if (!file_exists($folder)) mkdir($folder, 01777, true);
		}

		//exit();

		// HTTP headers for no cache etc
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Settings
		//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		$targetDir = $folder;

		//$cleanupTargetDir = false; // Remove old files
		//$maxFileAge = 60 * 60; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);

		// Uncomment this one to fake upload time
		// usleep(5000);

		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) $count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		// Create target dir
		if (!file_exists($targetDir)) @mkdir($targetDir);

		// Remove old temp files
		/* this doesn't really work by now

		 if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
			 while (($file = readdir($dir)) !== false) {
				 $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				 // Remove temp files if they are older than the max age
				 if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
					 @unlink($filePath);
			 }

			 closedir($dir);
		 } else
			 die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		 */

		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"])) $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"])) $contentType = $_SERVER["CONTENT_TYPE"];

		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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

		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	}



}
