<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_history {
	function __construct(){

	}
	function page(){





		$tmpl = new \template("template.tmpl", "ui/front/");
		$tmpl->page = array(
			"section"    => "history",
			"sub_section"=> "form",
			"template"   => "page_history",
			"meta"       => array(
				"title"=> "History",
			)
		);
		$tmpl->output();

	}
	function getCommits(){


		require_once './lib/GitHub/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

		$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
// Register the location of the GitHub namespace
		$loader->registerNamespaces(array(
			                            'Buzz'              => __DIR__ . '/../lib/vendor/Buzz/lib',
			                            'GitHub'            => __DIR__ . '/../lib'
		                            )
		);
		$loader->register();


		use GitHub\API\Authentication;
		use GitHub\API\User\User;
		use GitHub\API\AuthenticationException;

// Lets access the User API
		$user = new User();

	}


}
