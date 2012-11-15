<?php
/**
 * User: William
 * Date: 2012/07/04 - 3:17 PM
 */
namespace controllers\nf;
use \F3 as F3;
use \timer as timer;
use models\nf as models;


class controller_general_thumb {
	public static function article(){
		$cfg = F3::get("cfg");

		$dataO = new models\articles();


		$file = $dataO->getFile(F3::get("PARAMS.ID"));
		$data = $dataO->get($file['aID']);

	//	test_array($file);

		header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));
		header('Last-Modified: ' . date(DATE_RFC1123, strtotime($file['datein'])));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}

		$year = date("Y",strtotime($data['datein']));
		$month = date("m", strtotime($data['datein']));

		$folder = $cfg['upload']['folder'] . "nf/" . $data['cID'] . "/" . $data['authorID'] . "/$year/$month/" ;
		$filename = $file['filename'];

		if (isset($_GET['instantrender'])){
			$filename = $_GET['s'];
		}

		if (file_exists($folder. $filename)){

			$w = (isset($_GET['w'])) ? $_GET['w'] : "500";
			$h = (isset($_GET['h'])) ? $_GET['h'] : "500";

			$file_extension = strtolower(substr(strrchr($filename, "."), 1));

			\Graphics::thumb($folder . $filename, $w, $h);

		} else {
			\Graphics::fakeImage(0, 0);
		}

		exit();



	}

	public static function remove_white($thumb){
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


		imagejpeg($newimg, $thumb);
	}



}
