<?php
/**
 * User: William
 * Date: 2012/07/04 - 3:17 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;


class controller_general_download extends \apps\ab\controllers\_ {
	public static function material(){
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");

		$data = new models\bookings();
		$data = $data->get($f3->get("PARAMS.ID"));



		$path = $cfg['upload']['folder'] . "ab/" . $data['cID'] . "/" . $data['pID'] . "/" . $data['dID'] . "/material/" . $data['material_file_store'];

		if (file_exists($path)){
			self::download($path,$data['material_file_filename']);

		} else {
			echo "file doesnt exist";
		}
		exit();



	}
	public static function download($file,$filename){
		$f3 = \base::instance();

		$file = $f3->fixslashes($file);





		$size = filesize($file);
		$name = rawurldecode($file);
		$known_mime_types = array(
			"pdf"  => "application/pdf",
			"txt"  => "text/plain",
			"html" => "text/html",
			"htm"  => "text/html",
			"exe"  => "application/octet-stream",
			"zip"  => "application/zip",
			"doc"  => "application/msword",
			"docx" => "application/msword",
			"xls"  => "application/vnd.ms-excel",
			"xlsx" => "application/vnd.ms-excel",
			"ppt"  => "application/vnd.ms-powerpoint",
			"gif"  => "image/gif",
			"png"  => "image/png",
			"jpeg" => "image/jpg",
			"jpg"  => "image/jpg",
			"php"  => "text/plain"
		);

		$file_extension = strtolower(substr(strrchr($file, "."), 1));
		if (array_key_exists($file_extension, $known_mime_types)) {
			$mime_type = $known_mime_types[$file_extension];
		} else {
			$mime_type = "application/force-download";
		}


		@ob_end_clean(); //turn off output buffering to decrease cpu usage

		// required for IE, otherwise Content-Disposition may be ignored
		if (ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

		header('Content-Type: ' . $mime_type);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header("Content-Transfer-Encoding: binary");
		header('Accept-Ranges: bytes');

		/* The three lines below basically make the
						download non-cacheable */
		header("Cache-control: private");
		header('Pragma: private');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		// multipart-download and download resuming support
		if (isset($_SERVER['HTTP_RANGE'])) {
			list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
			list($range) = explode(",", $range, 2);
			list($range, $range_end) = explode("-", $range);
			$range = intval($range);
			if (!$range_end) {
				$range_end = $size - 1;
			} else {
				$range_end = intval($range_end);
			}

			$new_length = $range_end - $range + 1;
			header("HTTP/1.1 206 Partial Content");
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range-$range_end/$size");
		} else {
			$new_length = $size;
			header("Content-Length: " . $size);
		}

		/* output the file itself */
		$chunksize = 1 * (1024 * 1024); //you may want to change this
		$bytes_send = 0;
		if ($file = fopen($file, 'r')) {
			if (isset($_SERVER['HTTP_RANGE'])) fseek($file, $range);

			while (!feof($file) && (!connection_aborted()) && ($bytes_send < $new_length)) {
				$buffer = fread($file, $chunksize);
				print($buffer); //echo($buffer); // is also possible
				flush();
				$bytes_send += strlen($buffer);
			}
			fclose($file);
		} else die('Error - can not open file.');

		die();
	}



}
