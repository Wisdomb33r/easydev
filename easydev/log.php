<?php
session_start();

require_once "includes/connection.php";
require_once "includes/constants.php";
require_once "includes/dbconstants.php";

if(isset($_POST['Username']) && isset($_POST['Userpass'])) { // if username and password has been sent by POST method
  $Name = $_POST['Username'];
  $Pass = sha1($_POST['Userpass']); // sha1 hash of the password
  $Name = addslashes($Name); // escape special characters

  $query = 'SELECT id, name FROM '.AUTHORIZED_ADMINS.' WHERE name="'.$Name.'" AND password="'.$Pass.'"';
  $result = mysql_query($query) or die('Error while authenticating admin. <br />'.$query);

  if(mysql_num_rows($result) > 0){ // if there is at least one admin with this name and password
	$line = mysql_fetch_array($result);
    $_SESSION[SESSION_LOGIN] = $line['id']; // we register his id in the session
	$_SESSION[SESSION_NAME] = $line['name']; // we register his name in the session
    
    // http redirect on the main page of the console
	header('Location: main.php');
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