<?php
/**
 * User: William
 * Date: 2012/07/04 - 3:17 PM
 */
namespace controllers\nf;

use \timer as timer;
use models\nf as models;


class controller_general_thumb {
	public static function article(){
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");

		$dataO = new models\articles();


		$file = $dataO->getFile($f3->get("PARAMS.ID"));
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
			\Graphics::fakeImage(1, 1);
		}

		exit();



	}




}
