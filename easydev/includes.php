<?php
// include the different files needed
require_once('includes/connection.php');
require_once('includes/translator.class.php');
require_once('includes/constants.php');
require_once('includes/dbconstants.php');
require_once('includes/field.class.php');
require_once('includes/dbobject.class.php');

session_start();
// change the language if the user wants to
if(isset($_GET[SESSION_LANGUAGE])){
  $_SESSION[SESSION_LANGUAGE] = $_GET[SESSION_LANGUAGE];
}
$translator = new translator();
$session_permissions = array();
if(isset($_SESSION[SESSION_LOGIN])){ // if there is a user login in the session
  $session_userid = $_SESSION[SESSION_LOGIN]; // retrieve user id
  
  // get the id's of all "main" sections
  $query = 'SELECT id FROM '.ADMINMAIN;
  $result = mysql_query($query) or die('Error while selecting main section list <br />'.$query);
  
  // default initialization of all permissions to "false"
  while($line = mysql_fetch_array($result)){
	$session_permissions[$line['id']] = 0;
  }
  
  // get all permissions for all the main sections
  $query = 'SELECT id_mainsection FROM '.PERMISSION_ADMINS.' WHERE id_admin="'.$_SESSION[SESSION_LOGIN].'"';
  $result = mysql_query($query) or die('Error while selecting admin permissions. <br />'.$query);
  
  // update the $session_permissions array
  while($line = mysql_fetch_array($result)){
	$session_permissions[$line['id_mainsection']] = 1;
  }
}
else{ // user should not see the page, let's redirect on index.php where user can log
  header('Location: index.php');
  exit;
}

// verify that user has permission to view the opened menu he wants to see
if(isset($_GET[CURRENTMENU])){ // if the user wants to see the content of a menu
  if(! $session_permissions[$_GET[CURRENTMENU]]){
	// we redirect on main.php which is the default console page (every one can see this one). This should happens only if user manipulates the GET URL
	header('Location: main.php');
	exit;
  }
}
?>