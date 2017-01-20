<?php
require_once "includes/connection.php";
require_once "includes/constants.php";
require_once "includes/dbconstants.php";
session_start();

function recursive_stripslashes($value){
	$value = is_array($value) ? array_map('recursive_stripslashes', $value) : stripslashes($value);

	return $value;
}

global $LINK;
// handle the case when magic quotes are activated by removing all the backslashes in GPC
if(get_magic_quotes_gpc()){
	$_POST = recursive_stripslashes($_POST);
	$_GET = recursive_stripslashes($_GET);
	$_COOKIE = recursive_stripslashes($_COOKIE);
}

if(isset($_POST['Username']) && isset($_POST['Userpass'])) { // if username and password has been sent by POST method
	$Name = $_POST['Username'];
	$Pass = sha1($_POST['Userpass']); // sha1 hash of the password
	$Name = addslashes($Name); // escape special characters

	$query = 'SELECT id, name FROM '.AUTHORIZED_ADMINS.' WHERE name="'.$Name.'" AND password="'.$Pass.'"';
	$result = mysqli_query($LINK, $query) or die('Error while authenticating admin.');

	if(mysqli_num_rows($result) > 0){ // if there is at least one admin with this name and password
		$line = mysqli_fetch_array($result);
		$_SESSION[SESSION_LOGIN] = $line['id']; // we register his id in the session
		$_SESSION[SESSION_NAME] = $line['name']; // we register his name in the session

		// http redirect on the main page of the console
		header('Location: '.CONSOLE_PATH.'index.php');
		exit;
	}
	else{ // there is no admin with this username and password
		header('Location: index.php');
		exit;
	}
}
else {
	header('Location: index.php');
	exit;
}

?>