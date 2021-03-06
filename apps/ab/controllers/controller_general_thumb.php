<?php
/**
 * User: William
 * Date: 2012/07/04 - 3:17 PM
 */
namespace apps\ab\controllers;

use apps\ab\models as models;


class controller_general_thumb extends \apps\ab\controllers\_ {



	public function material() {
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$f3->set("json", False);

		$data = new models\bookings();
		$data = $data->get($f3->get("PARAMS.ID"));





		header("Content-Type: image/png");
		header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));
		header('Last-Modified: ' . date(DATE_RFC1123, strtotime($data['material_date'])));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}

		$folder = "ab/" . $data['cID'] . "/" . $data['pID'] . "/" . $data['dID'] . "/material/";
		$filename = $data['material_file_store'];

		if (isset($_GET['instantrender'])) {
			$filename = $_GET['s'];
		}


		$upload_folder = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $cfg['upload']['folder']);
		$folder = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $folder);

		$w = (isset($_GET['w'])) ? $_GET['w'] : "500";
		$h = (isset($_GET['h'])) ? $_GET['h'] : "500";
		$crop = (isset($_GET['c']) && $_GET['c'] == "true") ? true : false;
		$stretch = (isset($_GET['stretch']) && $_GET['stretch'] == "true") ? true : false;

		$w = round($w);
		$h = round($h);
		//test_array(array("w"=>$w,"h"=>$h,"filename"=>$filename));
		if (file_exists($upload_folder . $folder . $filename)) {


			$file_extension = strtolower(substr(strrchr($filename, "."), 1));





			if ($file_extension == "pdf") {
				$thumb = "thumb" . DIRECTORY_SEPARATOR . str_replace(".pdf", ".png", $filename);

				if (!file_exists($upload_folder . $folder . "thumb" . DIRECTORY_SEPARATOR)) mkdir($upload_folder . $folder . "thumb" . DIRECTORY_SEPARATOR, 01777, true);

				if (!file_exists($upload_folder . $folder . $thumb) && file_exists($upload_folder . $folder . $filename)) {
					$exportPath = $upload_folder . $folder . $thumb;
					$res = "96";
					$pdf = $upload_folder . $folder . $filename;

					$str = "gs -dCOLORSCREEN -dNOPAUSE -box -sDEVICE=png16m -dUseCIEColor -dTextAlphaBits=4 -dFirstPage=1 -dLastPage=1 -dGraphicsAlphaBits=4 -o$exportPath -r$res  $pdf";

					exec($str);

					self::remove_white($upload_folder . $folder . $thumb);
				}






				if (file_exists($upload_folder . $folder . $thumb)) {
					//test_array(array($folder . $thumb));



					$image = new \Image($folder . $thumb);
					$image->resize($w, $h, $crop);
					$image->render();
					unset($image);
				}
			}
		}

		$f3->set("exit", true);

	}

	public function general_thumb() {
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$f3->set("json", False);






		header("Content-Type: image/png");
		header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));


		$folder = isset($_GET['file']) ? $_GET['file'] : "";
		$upload_folder = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $cfg['upload']['folder']);


		$file = str_replace(array("/", "\\", "//", "\\\\"), DIRECTORY_SEPARATOR, $upload_folder . $folder);










		$w = ($f3->get("PARAMS['width']")) ? $f3->get("PARAMS['width']") : "500";
		$h = ($f3->get("PARAMS['height']")) ? $f3->get("PARAMS['height']") : "500";

		$crop = (isset($_GET['c']) && $_GET['c'] == "true") ? true : false;
		$stretch = (isset($_GET['stretch']) && $_GET['stretch'] == "true") ? true : false;

		$w = round($w);
		$h = round($h);
		//test_array(array("w"=>$w,"h"=>$h,"filename"=>$filename));
		if (file_exists($file)) {
			$file_extension = strtolower(substr(strrchr($file, "."), 1));
			if ($file_extension == "pdf") {
				
			}
			
			

//test_array($file); 


			$image = new \Image($folder);
			$image->resize($w, $h, $crop);
			$image->render();
			unset($image);
		}



		$f3->set("exit", true);

	}

	public function page() {
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$user = $f3->get("user");
		$f3->set("json", False);

		$dID = $f3->get("PARAMS.dID");
		$page = $f3->get("PARAMS.page");



		$data = $f3->get("DB")->exec("SELECT *, (SELECT cID FROM global_publications WHERE global_publications.ID = global_pages.pID) as cID FROM global_pages WHERE dID = '$dID' AND page = '$page'");

		if (count($data)) {
			$data = $data[0];
		} else {
			$data['dID'] = $dID;
			$data['cID'] = $user['company']['ID'];
			$data['pID'] = $user['publication']['ID'];
			$data['page'] = $page;
			$data['pdf'] = "";
		}


		//test_array($data); 




		header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}

		$folder = "pages/" . $data['cID'] . "/" . $data['pID'] . "/" . $data['dID'] . "/";
		$filename = $data['pdf'];

		if (isset($_GET['instantrender'])) {
			$filename = $_GET['s'];
		}


		$upload_folder = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $cfg['upload']['folder']);
		$folder = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $folder);

		$w = (isset($_GET['w'])) ? $_GET['w'] : "500";
		$h = (isset($_GET['h'])) ? $_GET['h'] : "500";
		$crop = (isset($_GET['c']) && $_GET['c'] == "true") ? true : false;

		$w = round($w);
		$h = round($h);
		//test_array(array("w"=>$w,"h"=>$h,"filename"=>$filename));
		if (file_exists($upload_folder . $folder . $filename)) {


			$file_extension = strtolower(substr(strrchr($filename, "."), 1));





			if ($file_extension == "pdf") {
				$thumb = "thumb" . DIRECTORY_SEPARATOR . str_replace(".pdf", ".png", $filename);

				if (!file_exists($upload_folder . $folder . "thumb" . DIRECTORY_SEPARATOR)) mkdir($upload_folder . $folder . "thumb" . DIRECTORY_SEPARATOR, 01777, true);

				if (!file_exists($upload_folder . $folder . $thumb) && file_exists($upload_folder . $folder . $filename)) {
					$exportPath = $upload_folder . $folder . $thumb;
					$res = "96";
					$pdf = $upload_folder . $folder . $filename;

					$str = "gs -dCOLORSCREEN -dNOPAUSE -box -sDEVICE=png16m -dUseCIEColor -dTextAlphaBits=4 -dFirstPage=1 -dLastPage=1 -dGraphicsAlphaBits=4 -o$exportPath -r$res  $pdf";

					//echo $str;
					//exit();
					exec($str);

					self::remove_white($upload_folder . $folder . $thumb);
				}






				if (file_exists($upload_folder . $folder . $thumb)) {
					//test_array(array($folder . $thumb));



					$image = new \Image($folder . $thumb);
					$image->resize($w, $h, $crop);
					$image->render();
					unset($image);
				}
			}
		}

		$f3->set("exit", true);

	}

	public static function remove_white($thumb) {
		$img = imagecreatefrompng($thumb);
		//find the size of the borders
		$b_top = 0;
		$b_btm = 0;
		$b_lft = 0;
		$b_rt = 0;

//top
		for (; $b_top < imagesy($img); ++$b_top) {
			for ($x = 0; $x < imagesx($img); ++$x) {
				if (imagecolorat($img, $x, $b_top) != 0xFFFFFF) {
					break 2; //out of the 'top' loop
				}
			}
		}

//bottom
		for (; $b_btm < imagesy($img); ++$b_btm) {
			for ($x = 0; $x < imagesx($img); ++$x) {
				if (imagecolorat($img, $x, imagesy($img) - $b_btm - 1) != 0xFFFFFF) {
					break 2; //out of the 'bottom' loop
				}
			}
		}

//left
		for (; $b_lft < imagesx($img); ++$b_lft) {
			for ($y = 0; $y < imagesy($img); ++$y) {
				if (imagecolorat($img, $b_lft, $y) != 0xFFFFFF) {
					break 2; //out of the 'left' loop
				}
			}
		}

//right
		for (; $b_rt < imagesx($img); ++$b_rt) {
			for ($y = 0; $y < imagesy($img); ++$y) {
				if (imagecolorat($img, imagesx($img) - $b_rt - 1, $y) != 0xFFFFFF) {
					break 2; //out of the 'right' loop
				}
			}
		}

//copy the contents, excluding the border
		$newimg = imagecreatetruecolor(imagesx($img) - ($b_lft + $b_rt), imagesy($img) - ($b_top + $b_btm));

		imagecopy($newimg, $img, 0, 0, $b_lft, $b_top, imagesx($newimg), imagesy($newimg));

//finally, output the image


		imagepng($newimg, $thumb);
	}


}
