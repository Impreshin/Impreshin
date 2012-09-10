<?php
/*
 * Date: 2011/06/27
 * Time: 4:33 PM
 */

class template {
	private $config = array(), $vars = array();

	function __construct($template, $folder = "") {
		$this->config['cache_dir'] = F3::get('TEMP');

		$this->vars['folder'] = $folder;

		$this->template = $template;



	}

	public function __get($name) {
		return $this->vars[$name];
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}


	public function load() {

		$curPageFull = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$curPage = explode("?", $curPageFull);
		$_v = isset($_GET['v']) ? $_GET['v'] : F3::get('v');
		$user = F3::get('user');

		$cfg = F3::get('cfg');
		unset($cfg['DB']);


		$this->vars['_nav_top'] = $this->vars['folder']."_nav_top.tmpl";

		$publications = $user['publications'];
		$publication = $user['publication'];

		$this->vars['_folder'] = $this->vars['folder'];
		$this->vars['_version'] = F3::get('version');
		$this->vars['_v'] = $_v;
		$this->vars['_cfg'] = $cfg;
		$this->vars['_last_pages'] = F3::get('last_pages');
		$this->vars['isLocal'] = isLocal();


		$this->vars['_httpdomain'] = siteURL();
		$this->vars['_user'] = $user;
		$this->vars['_settings'] = array(
			"settings"=> F3::get('settings'),
			"publication"=> $publication,
			"publications"=> $publications,
			"json"=>array(
				"publication" => json_encode($publication),
				"publications"=> json_encode($publications),
			)
		);

		$userID = $this->vars['_user'];

		if (isset($this->vars['page'])) {
			$page = $this->vars['page'];
			$tfile = $page['template'];
			if (file_exists('' . $this->vars['folder'] . '' . $tfile . '.tmpl')) {
				$page['template'] = $tfile . '.tmpl';
			} else {
				$page['template'] = "none";
			}
			if (file_exists('' . $this->vars['folder'] . '_js/' . $tfile . '.js')) {
				$page['template_js'] = '/min/js_' . $_v . '?file=/' . $this->vars['folder'] . '_js/' . $tfile . '.js';
			} else {
				$page['template_js'] = "";
			}
			if (file_exists('' . $this->vars['folder'] . '_css/' . $tfile . '.css')) {
				$page['template_css'] = '/min/css_'.$_v.'?file=/' . $this->vars['folder'] . '_css/' . $tfile . '.css';
			} else {
				$page['template_css'] = "";
			}
			if (file_exists('' . $this->vars['folder'] . 'templates/' . $tfile . '_templates.jtmpl')) {
				//exit('/tmpl?file=' . $tfile . '_templates.jtmpl');

				$file = '/' . $this->vars['folder'] . 'templates/' . $tfile . '_templates.jtmpl';

				$page['template_tmpl'] = 'templates/' . $tfile . '_templates.jtmpl';
			} else {
				$page['template_tmpl'] = "";
			}
			$this->vars['page'] = $page;
			return $this->render_template();
		} else {
			return $this->render_string();
		}




	}

	public function render_template() {
		$loader = new Twig_Loader_Filesystem(array("ui/",$this->vars['folder']));
		$twig = new Twig_Environment($loader, array(
			//'cache' => $this->config['cache_dir'],
		));


		//test_array($this->vars);

		return $twig->render($this->template, $this->vars);


	}

	public function render_string() {
		$loader = new Twig_Loader_String();
		$twig = new Twig_Environment($loader);
		return $twig->render($this->vars['template'], $this->vars);
	}


	public function output() {
		F3::set("__runTemplate", true);
		echo $this->load();

	}

}
