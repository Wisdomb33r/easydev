<?php

// this include will include all the files that are needed for the console
// it will also start or reactivate the session and make some standards verifications on the variables
// it also make a verification that the user is logged
require_once('includes.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
// this is done through a constant for easy reconfiguration.
$adminMainMenu = LOG_MENU_ID;

global $LINK;

// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: '.CONSOLE_PATH.'index.php');
  exit;
}
else{ // if the user has the permissions

  // include the header of the page
  include 'adminheader.php';

  $query = 'SELECT log FROM '.LOGS.' ORDER BY id DESC LIMIT 0, 200';
  $result = mysqli_query($LINK, $query) or die('Error while selecting administrator logs.');

  while ($line = mysqli_fetch_array($result)){
	echo '<p>'.$line['log'].'</p>';
  }
  
  include 'adminfooter.php';
}
?>