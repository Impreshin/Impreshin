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
				"/ui/jqPlot/jquery.jqplot.min.css"
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
		//$file = F3::get('PARAMS.filename');
		header("Content-Type: application/javascript");
		$t = "";
		if ($file) {
			$fileDetails = pathinfo(($file));
			$base = "." . $fileDetails['dirname'] . "/";
			$file = $fileDetails['basename'];
			$t = file_get_contents($base . $file);
		} else {
			$files = array(
				"/ui/_js/libs/jquery-ui.js",
				"/ui/_js/libs/bootstrap.min.js",

				// ------ //
				"/ui/_js/plugins/date.js",

				"/ui/_js/plugins/jquery.daterangepicker.js",
				"/ui/_js/plugins/jquery.mousewheel.js",
				"/ui/_js/plugins/mwheelIntent.js",
				"/ui/_js/plugins/jquery.jscrollpane.js",
				"/ui/_js/plugins/jquery.jqote2.js",
				"/ui/_js/plugins/jquery.ba-bbq.js",
				"/ui/_js/plugins/jquery.cookie.js",
				"/ui/_js/plugins/jquery.autologout.js",
				"/ui/_js/plugins/select2.js",
				"/ui/_js/plugins/jquery.miniColors.js",
				"/ui/_js/plugins/FeedEk.js",



				"/ui/plupload/js/browserplus-min.js" ,
				"/ui/plupload/js/plupload.js" ,
				"/ui/plupload/js/plupload.gears.js" ,
				"/ui/plupload/js/plupload.silverlight.js" ,
				"/ui/plupload/js/plupload.flash.js" ,
				"/ui/plupload/js/plupload.browserplus.js" ,
				"/ui/plupload/js/plupload.html4.js" ,
				"/ui/plupload/js/plupload.html5.js" ,



				"/ui/jqPlot/jquery.jqplot.min.js",
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

exit($t);
		//exit($this->minify($t, "js"));



	}
	function minify($code,$ext){
		$src = $code;
		$ptr = 0;
		$dst = '';

		while ($ptr < strlen($src)) {
			if ($src[$ptr] == '/') {
				// Presume it's a regex pattern
				$regex = TRUE;
				if ($ptr > 0) {
					// Backtrack and validate
					$ofs = $ptr;
					while ($ofs > 0) {
						$ofs--;
						// Pattern should be preceded by a punctuation
						if (ctype_punct($src[$ofs])) {
							while ($ptr < strlen($src)) {
								$str = strstr(substr($src, $ptr + 1), '/', TRUE);
								if (!strlen($str) && $src[$ptr - 1] != '/' || strpos($str, "\n") !== FALSE) {
									// Not a regex pattern
									$regex = FALSE;
									break;
								}
								$dst .= '/' . $str;
								$ptr += strlen($str) + 1;
								if ($src[$ptr - 1] != '\\' || $src[$ptr - 2] == '\\') {
									$dst .= '/';
									$ptr++;
									break;
								}
							}
							break;
						} elseif ($src[$ofs] != "\t" && $src[$ofs] != ' ') {
							// Not a regex pattern
							$regex = FALSE;
							break;
						}
					}
					if ($regex && $ofs < 1) $regex = FALSE;
				}
				if (!$regex || $ptr < 1) {
					if (substr($src, $ptr + 1, 2) == '*@') {
						// Conditional block
						$str = strstr(substr($src, $ptr + 3), '@*/', TRUE);
						$dst .= '/*@' . $str . $src[$ptr] . '@*/';
						$ptr += strlen($str) + 6;
					} elseif ($src[$ptr + 1] == '*') {
						// Multiline comment
						$str = strstr(substr($src, $ptr + 2), '*/', TRUE);
						$ptr += strlen($str) + 4;
					} elseif ($src[$ptr + 1] == '/') {
						// Single-line comment
						$str = strstr(substr($src, $ptr + 2), "\n", TRUE);
						$ptr += strlen($str) + 2;
					} else {
						// Division operator
						$dst .= $src[$ptr];
						$ptr++;
					}
				}
				continue;
			}
			if ($src[$ptr] == '\'' || $src[$ptr] == '"') {
				$match = $src[$ptr];
				// String literal
				while ($ptr < strlen($src)) {
					$str = strstr(substr($src, $ptr + 1), $src[$ptr], TRUE);
					$dst .= $match . $str;
					$ptr += strlen($str) + 1;
					if ($src[$ptr - 1] != '\\' || $src[$ptr - 2] == '\\') {
						$dst .= $match;
						$ptr++;
						break;
					}
				}
				continue;
			}
			if (ctype_space($src[$ptr])) {
				$last = substr($dst, -1);
				$ofs = $ptr + 1;
				if ($ofs + 1 < strlen($src)) {
					while (ctype_space($src[$ofs])) $ofs++;
					if (preg_match('/[\w%]' . '[\w' . ($ext == 'css' ? '\)\]\}#\-*\.' : '') . '$]/', $last . $src[$ofs])) $dst .= $src[$ptr];
				}
				$ptr = $ofs;
			} else {
				$dst .= $src[$ptr];
				$ptr++;
			}
		}
		return  $dst;
	}
	function upload() {
		$folder = (isset($_GET['folder'])) ? $_GET['folder'] : "";

		$cfg = F3::get("cfg");



		$user = F3::get("user");


		$app = F3::get('PARAMS.app');



		$folder = $cfg['upload']['folder'] . $app . "/". $folder;

		ini_set('upload_tmp_dir', $cfg['upload']['folder'].'tmp');
		ini_set('upload_max_filesize', '20M');
		ini_set('post_max_size', '20M');



		if (!file_exists($cfg['upload']['folder'] . 'tmp')) @mkdir($cfg['upload']['folder'] . 'tmp', 0777, true);
		if (!file_exists($folder)) @mkdir($folder, 0777, true);

		//$targetDir = $cfg['upload']['folder'] . $app . "/temp/";
		//if (!file_exists($targetDir)) @mkdir($targetDir, 0777, true);

		$targetDir = $folder;

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

// Settings

//$targetDir = 'uploads';

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



			//\controllers\ab\controller_thumb::create_thumb($folder, $fileName);

		}


// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

	}



}
