<?php
/**
 * User: William
 * Date: 2012/07/04 - 3:17 PM
 */
namespace apps\pf\controllers;
use \timer as timer;
use \apps\ab\models as models;


class _file extends \apps\pf\controllers\_ {



	

	

	public function thumbnail() {
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$user = $f3->get("user");
		
		$f3->set("json",False);

		$dID = $f3->get("PARAMS.dID");
		$page = $f3->get("PARAMS.page");



		$data = $f3->get("DB")->exec("SELECT *, (SELECT cID FROM global_publications WHERE global_publications.ID = global_pages.pID) as cID FROM global_pages WHERE dID = '$dID' AND page = '$page'");

		if (count($data)){
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


		$upload_folder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $cfg['upload']['folder']);
		$folder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $folder);

		$w = (isset($_GET['w'])) ? $_GET['w'] : "500";
		$h = (isset($_GET['h'])) ? $_GET['h'] : "500";
		$crop = (isset($_GET['c']) && $_GET['c']=="true") ? true:false;

		$w = round($w);
		$h = round($h);
		//test_array(array("w"=>$w,"h"=>$h,"filename"=>$filename));
		if (file_exists($upload_folder . $folder . $filename)) {


			$file_extension = strtolower(substr(strrchr($filename, "."), 1));





			if ($file_extension == "pdf") {
				$thumb = "thumb" . DIRECTORY_SEPARATOR . str_replace(".pdf", ".png", $filename);

				if (!file_exists($upload_folder . $folder . "thumb". DIRECTORY_SEPARATOR)) mkdir($upload_folder . $folder . "thumb". DIRECTORY_SEPARATOR, 01777, true);

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
					$image->resize($w,$h,$crop);
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
	function download(){
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$f3->set("json",False);

		$dID = $f3->get("PARAMS.dID");
		$page = $f3->get("PARAMS.page");



		$data = $f3->get("DB")->exec("SELECT *, (SELECT cID FROM global_publications WHERE global_publications.ID = global_pages.pID) as cID, (SELECT publish_date FROM global_dates WHERE global_dates.ID = global_pages.dID) as publish_date, (SELECT publication FROM global_publications WHERE global_publications.ID = global_pages.pID) as publication FROM global_pages WHERE dID = '$dID' AND page = '$page'");

		if (count($data)){
			$data = $data[0];

		}

		$folder = "pages/" . $data['cID'] . "/" . $data['pID'] . "/" . $data['dID'] . "/";
		$filename = $data['pdf'];


		$upload_folder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $cfg['upload']['folder']);
		$folder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $folder);

		if (file_exists($upload_folder . $folder . $filename)) {
			$o = new \Web();
			
			$path = $upload_folder . $folder . $filename;

			$gen = $data['publication'];
			$gen = $gen . "_" . $data['publish_date'];
			$gen = $gen . "_page-" . ($data['page'] + 0);
			
			
			$gen = $gen . ".pdf";

			//test_array($gen); 
			header('Content-Type: '.$o->mime($filename));
			header('Content-Disposition: attachment; '.
			       'filename='.$gen);
			header('Accept-Ranges: bytes');
			header('Content-Length: '.$size=filesize($path));

			echo readfile($path);


			exit();
			
		} else {
			$f3->error("404");
		}

		
		
		
		
		
	}


}
