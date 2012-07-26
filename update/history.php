<?php
include_once ("../inc/functions.php");
require_once 'lib/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

/**
 * Configure the autoloader
 *
 * The Symfony ClassLoader Component is used, but could easy be substituted for
 * another autoloader.
 *
 * @link https://github.com/symfony/ClassLoader
 * @link http://symfony.com/doc/current/cookbook/tools/autoloader.html
 */
$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
// Register the location of the GitHub namespace
$loader->registerNamespaces(array(
	                            'Buzz'              => 'lib/vendor',
	                            'GitHub'            => 'lib'
                            )
);
$loader->register();


use GitHub\API\Authentication;
use GitHub\API\User\User;
use GitHub\API\AuthenticationException;

// Lets access the User API
$user = new User();



// Set user credentials and login
//$user->setCredentials(new Authentication\Basic('username', 'password'));
$user->setCredentials(new Authentication\Basic('awstam@gmail.com', 'awssmudge1'));
$user->login();
$response = "";

try {
	//test_array($user->repos());
	// Check if your following user
	var_dump($user->isFollowing("octocat"));

	// Update some user details
	//var_dump($user->update(array('location' => 'Wales, United Kingdom')));

	// Get all emails for user
	var_dump($user->emails()->all());

	$response = $user->keys()->all();



	// Add key for user
	//var_dump($user->keys()->create("New Key", "ssh-rsa CCC"));
} catch (AuthenticationException $exception) {
	echo $exception->getMessage();
}

test_array($response);

// Finally lets logout
$user->logout();