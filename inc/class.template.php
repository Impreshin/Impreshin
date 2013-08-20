<?php
/*
 * Date: 2011/06/27
 * Time: 4:33 PM
 */

class template {
	private $config = array(), $vars = array();

	function __construct($template, $folder = "") {
		$this->f3 = Base::instance();
		$this->config['cache_dir'] = $this->f3->get('TEMP');
		$this->vars['folder'] = $folder;
		$this->template = $template;
		$this->timer = new \timer();




	}
	function __destruct(){
		$page = $this->template;
		//test_array($page);
		if (isset($this->vars['page']['template'])){
			$page = $page . " -> " . $this->vars['folder'] . $this->vars['page']['template'];
		}
		$this->timer->stop("Template",  $page);
	}

	public function __get($name) {
		return $this->vars[$name];
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}


	public function load() {

		$curPageFull = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$cfg = $this->f3->get('CFG');
		unset($cfg['DB']);
		unset($cfg['package']);

		$v = $this->f3->get("VERSION");


		$this->vars['_nav_top'] = $this->vars['folder']."_nav_top.tmpl";
		$this->vars['_v'] = $v;


		$app = $this->f3->get("app");
		$app_list = $this->f3->get("applications");

		$app = isset($app_list[$app])? $app_list[$app]:"";



		$user = $this->f3->get('user');
		

		$cfg = $this->f3->get('CFG');
		unset($cfg['DB']);
		unset($cfg['package']);


		$this->vars['_nav_top'] = $this->vars['folder'] . "_nav_top.tmpl";


		//test_array($user);

		$this->vars['_uri'] = $_SERVER['REQUEST_URI'];
		$this->vars['_folder'] = $this->vars['folder'];
		$this->vars['_version'] = $this->f3->get('version');
		$this->vars['_cfg'] = $cfg;
		$this->vars['_docs'] = $this->f3->get('docs');
		$this->vars['isLocal'] = isLocal();
		$this->vars['_application'] = $app;



		$this->vars['_httpdomain'] = siteURL();
		$this->vars['_user'] = $user;

		//test_array($this->vars);
		$userID = $this->vars['_user'];


		if (isset($this->vars['page'])) {
			$page = $this->vars['page'];
			$tfile = $page['template'];

			$page['template'] = $tfile . '.tmpl';

			$folder = $this->vars['folder'];

			$page['js'] = array();
			$page['css'] = array();
			if (isset($this->vars['page']['js'])) {
				if (is_array($this->vars['page']['js'])){
					foreach ($this->vars['page']['js'] as $item){
						$page['js'][] = $item;
					}
				} else {
					$page['js'][] = $this->vars['page']['js'];
				}
			}

			if (file_exists('' . $folder . '_js/' . $tfile . '.js')) {
				$page['js'][] = '/' . $folder . '_js/' . $tfile .".". $v.'.js';
			}


			if (isset($this->vars['page']['css'])) {
				if (is_array($this->vars['page']['css'])) {
					foreach ($this->vars['page']['css'] as $item) {
						$page['css'][] = $item;
					}
				} else {
					$page['css'][] = $this->vars['page']['css'];
				}
			}


			if (file_exists('' . $folder . '_css/' . $tfile . '.css')) {
				$page['css'][] = '/' . $folder . '_css/' . $tfile .".". $v.'.css';
			}
			if (file_exists('' . $folder . '_css/style.css')) {
				$page['css'][] = '/' . $folder . '_css/style.'.$v.'.css';
			}



			if (file_exists('' . $folder . 'templates/' . $tfile . '_templates.jtmpl')) {
				$page['template_tmpl'] = 'templates/' . $tfile . '_templates.jtmpl';
			} else {
				if (!isset($page['template_tmpl'])) $page['template_tmpl'] = "";
			}
			if (file_exists('' . $folder . 'templates/' . $tfile . '.jtmpl')) {
				$page['template_tmpl'] = 'templates/' . $tfile . '.jtmpl';
			} else {
				if (!isset($page['template_tmpl'])) $page['template_tmpl'] = "";
			}




			//test_array($page);

			/*

			foreach ($folders as $folder){

				if (file_exists('' . $folder . '' . $tfile . '.tmpl')) {
					$usethisfolder = true;
					$this->templatefolder = $folder;
					$page['folder'] = $folder;
				}


				if (file_exists('' . $folder . '_js/' . $tfile . '.js') && !$force_js) {
					$page['template_js'] = '/min/js_' . $_v . '?file=/' . $folder . '_js/' . $tfile . '.js';
				} else {
					if (!isset($page['template_js'])) $page['template_js'] = "";
				}
				if (file_exists('' . $folder . '_css/' . $tfile . '.css') && !$force_css) {
					$page['template_css'] = '/min/css_' . $_v . '?file=/' . $folder . '_css/' . $tfile . '.css';
				} else {
					if (!isset($page['template_css'])) $page['template_css'] = "";
				}
				if (file_exists('' . $folder . 'templates/' . $tfile . '_templates.jtmpl') && !$force_tmpl) {
					//exit('/tmpl?file=' . $tfile . '_templates.jtmpl');

					$file = '/' . $folder . 'templates/' . $tfile . '_templates.jtmpl';

					$page['template_tmpl'] = 'templates/' . $tfile . '_templates.jtmpl';
				} else {
					if (!isset($page['template_tmpl'])) $page['template_tmpl'] = "";
				}

				if ($usethisfolder){
					break;
				}
				

			}
			//test_array($page);
			if (!isset($page['help']) || !$page['help']){
				$app = $this->f3->get("app");
				$sub_section = $page['sub_section'];
				if (strpos($sub_section,"_")){
					$sub_section = explode("_", $page['sub_section']);
					$sub_section = implode("/", $sub_section);
					$page['help'] = "/$app/help/" . $page['section'] . "/" . $sub_section;
				} else {
					$page['help'] = "/$app/help/" . $page['section'] . "/" . $page['sub_section'];
				}



			}

			$this->vars['page'] = $page;

			//test_array($page);
			*/
			$this->vars['page'] = $page;
			return $this->render_template();
		} else {
			return $this->render_string();
		}




	}

	public function render_template() {

		if (is_array($this->vars['folder'])){
			$folder = $this->vars['folder'];
		} else {
			$folder = array(
				"ui/",
				$this->vars['folder'],
				"apps/"
			);
		}



		$loader = new Twig_Loader_Filesystem($folder);
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
		$this->f3->set("__runTemplate", true);
		$this->f3->set("json", false);
		echo $this->load();

	}

}
