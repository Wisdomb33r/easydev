<?php

// this include will include all the files that are needed for the console
// it will also start or reactivate the session and make some standards verifications on the variables
// it also make a verification that the user is logged
require_once('includes.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
// this is done through a constant for easy reconfiguration.
$adminMainMenu = ADMIN_MENU_ID;

// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: '.CONSOLE_PATH.'index.php');
  exit;
}
else{ // if the user has the permissions

  if($_SERVER['REQUEST_METHOD'] == 'POST'){ // the user posted his form
	
	// verify if the user has clicked on the "cancel" button
	if(isset($_POST['cancel'])){
	  header('Location: '.CONSOLE_PATH.'index.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]); // redirect on the main page
	  exit;
	}

	// remove the selected administrators
	// NOTE : as all permissions, logs and personal info are sql links with InnoDB with "ON DELETE = cascade", no need to remove in other tables
	foreach($_POST['deleteids'] as $delete){
	  $query = 'SELECT name FROM '.AUTHORIZED_ADMINS.' WHERE ID="'.$delete.'"';
	  $result = mysql_query($query) or die('Error while selecting name of admin.');
	  $line = mysql_fetch_array($result);
	  $adminname = $line['name'];

	  $query = 'DELETE FROM '.AUTHORIZED_ADMINS.' WHERE id="'.$delete.'"';
	  mysql_query($query) or die('Error while deleting admin.');

	  // insert the log
	  $today = date('Y-m-d H:i');
	  $log = $today.' : Suppression of admin \"'.$adminname.'\" by '.$_SESSION[SESSION_NAME].'.';
	  $query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.$log.'")';
	  mysql_query($query) or die('Error while inserting administrator log.');
	}

	// redirect on the same page with a confirmation message of the delete
	header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmAdminDelete');
	exit;
  }
  else{
	// include the header of the page
	include 'adminheader.php';

	// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the admin was successfully deleted.
	if(isset($_GET['action']) && $_GET['action']=='confirmAdminDelete'){
	  echo '<p><strong>'.htmlentities(Translator::translate('console_remove_admin_confirmation'), ENT_COMPAT, 'UTF-8').'</strong></p>'."\n";
	}

	// select all administrators
	$query = 'SELECT id, name FROM '.AUTHORIZED_ADMINS.' ORDER BY id ASC';
	$result = mysql_query($query) or die('Error while selecting admin list.');
	
	// print the HTML form to delete administrators in the database
	echo '<p class="largemargintop">'.htmlentities(Translator::translate('console_remove_admin_header'), ENT_COMPAT, 'UTF-8').'</p>'."\n"
	  .'<form action="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n"
	  .'<table class="form">'."\n";

	while($line = mysql_fetch_array($result)){
	  echo '  <tr>'."\n"
		.'    <td>'
		.($_SESSION[SESSION_LOGIN] != $line['id'] ? 
		  '<input class="checkboxinput" type="checkbox" name="deleteids[]" value="'.$line['id'].'" />' : 
		  '')
		.'</td>'."\n"
		.'    <td>'.$line['name'].'</td>'."\n"
		.'  </tr>'."\n";
	}
	echo '  <tr>'."\n"
	  .'    <td>&nbsp;</td>'."\n"
	  .'    <td><input class="bouton" type="submit" name="delete" value="'.htmlentities(Translator::translate('delete'), ENT_COMPAT, 'UTF-8').'" />'."\n"
	  .'        <input class="bouton" type="submit" name="cancel" value="'.htmlentities(Translator::translate('cancel'), ENT_COMPAT, 'UTF-8').'" /></td>'."\n"
	  .'  </tr>'."\n"
	  .'</table>'."\n"
	  .'</form>'."\n";

	include 'adminfooter.php';
  }
}
?>