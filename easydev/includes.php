<?php
// include the different files needed
require_once('includes/connection.php');
require_once('includes/translator.class.php');
require_once('includes/constants.php');
require_once('includes/dbconstants.php');

session_start();

global $LINK;

// global usage functions
function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}
function recursive_stripslashes($value){
	$value = is_array($value) ? array_map('recursive_stripslashes', $value) : stripslashes($value);
	return $value;
}

// handle the case when magic quotes are activated by removing all the backslashes in GPC
if(get_magic_quotes_gpc()){
	$_POST = recursive_stripslashes($_POST);
	$_GET = recursive_stripslashes($_GET);
	$_COOKIE = recursive_stripslashes($_COOKIE);
}

// find the list of available languages
$languageList = Translator::languageList();

// change the language if the user wants to and if the language in the list of supported languages
if(isset($_GET[SESSION_LANGUAGE]) && in_array($_GET[SESSION_LANGUAGE], $languageList)){
	$_SESSION[SESSION_LANGUAGE] = $_GET[SESSION_LANGUAGE];
}

$session_permissions = array();
if(isset($_SESSION[SESSION_LOGIN])){ // if there is a user login in the session
	$session_userid = $_SESSION[SESSION_LOGIN]; // retrieve user id

	// get the id's of all "main" sections
	$query = 'SELECT id FROM '.ADMINMAIN;
	$result = mysqli_query($LINK, $query) or die('Error while selecting main section list ');

	// default initialization of all permissions to "false"
	while($row = mysqli_fetch_array($result)){
		$session_permissions[$row['id']] = 0;
	}

	// get all permissions for all the main sections
	$query = 'SELECT id_mainsection FROM '.PERMISSION_ADMINS.' WHERE id_admin="'.$_SESSION[SESSION_LOGIN].'"';
	$result = mysqli_query($LINK, $query) or die('Error while selecting admin permissions.');

	// update the $session_permissions array
	while($row = mysqli_fetch_array($result)){
		$session_permissions[$row['id_mainsection']] = 1;
	}
}
else{ // user should not see the page, let's print the login form
	if($_SERVER['REQUEST_URI'] == CONSOLE_PATH.'index.php'){
		include 'includes/loginbox.php';
	}
	else{
		header('Location: '.CONSOLE_PATH.'index.php');
	}
	exit();
}

// verify that user has permission to view the opened menu he wants to see
if(isset($_GET[CURRENTMENU])){ // if the user wants to see the content of a menu
	if(! isset($session_permissions[$_GET[CURRENTMENU]])){
		// we redirect on index.php which is the default console page (every one can see this one). This should happens only if user manipulates the GET URL
		header('Location: '.CONSOLE_PATH.'index.php');
		exit;
	}
}
?>