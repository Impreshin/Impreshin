<?php
/**
 * User: William
 * Date: 2012/07/04 - 3:17 PM
 */
namespace apps\nf\controllers;
use \timer as timer;
use \apps\ab\models as models;


class _file extends \apps\nf\controllers\_ {



	

	public function thumbnail() {
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$f3->set("json",False);

		$cfg = $this->f3->get("CFG");


		$user = $this->f3->get("user");
		$app = $this->f3->get('PARAMS.app');
		$folder = ($cfg['upload']['folder'] . $app . "/");
	

		//header("Content-Type: image/png");
		//header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		//header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		//	header('HTTP/1.1 304 Not Modified');
			die();
		}

		$filename = isset($_REQUEST['file'])?$_REQUEST['file']:"";
		$crop = isset($_REQUEST['crop'])?$_REQUEST['crop']:true;
		
		if ($crop==="false"){
			$crop=false;
		}

		$file = $folder . "nf/".$filename;
		$file = $f3->fixslashes($file);
		$file = str_replace("//","/",$file);


		//test_array($this->f3->get('PARAMS.w'));

		$width = $this->f3->get('PARAMS.w')? $this->f3->get('PARAMS.w'): "";
		$height = $this->f3->get('PARAMS.h')? $this->f3->get('PARAMS.h'): "";


		//test_array($file);

		if (file_exists($file)) {
			$thumb = new \mods_Image($file);

			$thumb->resize($width, $height, $crop);
			$thumb->render();

		}


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

		$cfg = $this->f3->get("CFG");


		$user = $this->f3->get("user");
		$app = $this->f3->get('PARAMS.app');
		$folder = ($cfg['upload']['folder'] . $app . "/");
		
		
		
		$file = isset($_GET['file'])?$_GET['file']:"";
		$filename = isset($_GET['filename'])?$_GET['filename']:basename($file);

		$path = $folder . "nf/".$file;
		$path = $f3->fixslashes($path);
		$path = str_replace("//","/",$path);
		
		$o = new \Web();
		

		header('Content-Type: '.$o->mime($file));
		header('Content-Disposition: attachment; '.
				   'filename='.basename($filename));
		header('Accept-Ranges: bytes');
		header('Content-Length: '.$size=filesize($path));

		echo readfile($path);
		
		
		exit();
		
		
	}


}
