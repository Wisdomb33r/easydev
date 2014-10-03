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
	  //	  die('cancel');
	  header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]); // redirect on the main page
	  exit;
	}

	$adminname = '';
	// verify that the userid isset and that it exists
	if(isset($_POST['userid'])){
	  // verify that the admin exist
	  $query = 'SELECT name FROM '.AUTHORIZED_ADMINS.' WHERE id="'.$_POST['userid'].'"';
	  $result = mysql_query($query) or die('Error while selecting name of administrator.');

	  // if there is no admin with this userid
	  if(mysql_num_rows($result) == 0){
		//	die('user do not exist');
		header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
		exit;
	  }
	  else{
		$line = mysql_fetch_array($result);
		$adminname = $line['name'];
	  }
	}
	else{ // no $_POST['userid'] is set, it's an error (should not happens)
	  header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}

	$errors = array();
	// ---------- beginning of the verifications on the form -----------------------
	  
	// no verification for this page

	// ------------------ end of the verifications on the form ----------------------
	
	// if there is an error here, we need to redirect on the same page
	if(count($errors) != 0){
	  $_SESSION[SESSION_ERRORS] = $errors;
	  $_SESSION[SESSION_POSTED] = $_POST;
	  
	  // PHP redirection on this script to print errors
	  header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}
	else{ // if there is no errors
	  // update the permissions of the admin

	  // First retrieve all the different ID of main sections
	  $query = 'SELECT id FROM '.ADMINMAIN.' ORDER BY id ASC';
	  $result = mysql_query($query) or die('Error while selecting main sections.');
	  while($line = mysql_fetch_array($result)){
		// Create a variable which is equal to newpermission1, newpermission2... for each section
		$variablepermission = 'newpermission'.$line['id'];
		
		// Now retrieve the current permission of the admin for the current menu id
		$querypermission = 'SELECT id_mainsection FROM '.PERMISSION_ADMINS.' WHERE id_admin="'.$_POST['userid'].'" AND id_mainsection="'.$line['id'].'"';
		$resultpermission = mysql_query($querypermission) or die('Error while selecting permission.');
		
		if(mysql_num_rows($resultpermission) > 0){ // The admin has right for this section
		  if(isset($_POST[$variablepermission])){ // The admin should have right for this section
			// nothing to do
		  }
		  else{ // The admin should not have rights for this section
			// Need to delete the row which make the admin having the right for this section
			$querydeletepermission = 'DELETE FROM '.PERMISSION_ADMINS.' WHERE id_admin='.$_POST['userid'].' AND id_mainsection="'.$line['id'].'"';
			mysql_query($querydeletepermission) or die('Error while deleting permission.');
		  }
		}
		else{ // The admin has no right already for this section
		  if(isset($_POST[$variablepermission])){ // The admin should have right for this section
			// Need to add a row which make the admin having the right for this section
			$queryaddpermission = 'INSERT INTO '.PERMISSION_ADMINS.' (id_admin, id_mainsection) VALUES ("'.$_POST['userid'].'", "'.$line['id'].'")';
			$resultaddpermission = mysql_query($queryaddpermission) or die('Error while inserting new permission.');
		  }
		  else{ // The admin should not have rights for this section
			// nothing to do
		  }
		}
	  }

	  // insert the log
	  $today = date('Y-m-d H:i');
	  $log = $today.' : New permissions for admin \"'.$adminname.'\" created by '.$_SESSION[SESSION_NAME].'.';
	  $query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.$log.'")';
	  mysql_query($query) or die('Error while inserting administrator log.');

	  // redirect on the same page but with a message to say that admin permissions has been updated sucessfully
	  header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmPermissionChanges');
	  exit;
	}
  }
  else if(isset($_GET['action']) && $_GET['action'] == 'changepermission'){ // the admin wants to edit the permissions of $_GET['userid']
	if(isset($_GET['userid'])){ // if $_GET['userid'] is set
	  // verify that the admin exist
	  $query = 'SELECT name FROM '.AUTHORIZED_ADMINS.' WHERE id="'.$_GET['userid'].'"';
	  $result = mysql_query($query) or die('Error while selecting name of administrator.');

	  // if there is no admin with this userid
	  if(mysql_num_rows($result) == 0){
		header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
		exit;
	  }
	  else{ // if the admin exists
		// retrieve his name
		$line = mysql_fetch_array($result);
		$adminname = $line['name'];

		// select the current permissions of the administrator designated by $_GET['userid']
		$query = 'SELECT id_mainsection FROM '.PERMISSION_ADMINS.' WHERE id_admin="'.$_GET['userid'].'"';
		$resultpermissionadmin = mysql_query($query) or die('Error while selecting permissions.');

		// create an array with these permissions
		$permissions = array();
		while($line = mysql_fetch_array($resultpermissionadmin)){
		  array_push($permissions, $line['id_mainsection']);
		}

		// select all the mainsections for which there is a permission to set
		$query = 'SELECT id, text FROM '.ADMINMAIN.' ORDER BY id ASC';
		$resultmainsections = mysql_query($query) or die('Error while selecting main sections.');

		// include the header of the page
		include 'adminheader.php';

		// print the form header
		echo '<p class="largemargintop">'.Translator::translate('console_permission_admin_change').' "<strong>'.$adminname.'</strong>"</p>'
		  .'<form action="permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n"
		  .'<table class="form">'."\n";

		// print the permissions
		while($line = mysql_fetch_array($resultmainsections)){
		  echo '  <tr>'."\n"
			.'    <td class="objectmodifyname">'.Translator::translate($line['text']).'</td>'."\n"
			.'    <td><input type="checkbox" name="newpermission'.$line['id'].'"'.(in_array($line['id'], $permissions) ? ' checked="checked"' : '').' /></td>'."\n"
			.'  </tr>'."\n";
		}

		// print the form footer
		echo '  <tr>'."\n"
		  .'    <td></td>'."\n"
		  .'    <td><input type="hidden" name="userid" value="'.$_GET['userid'].'" />'."\n"
		  .'        <input class="bouton" type="submit" name="change" value="'.Translator::translate('update').'" />'."\n"
		  .'        <input class="bouton" type="submit" name="cancel" value="'.Translator::translate('cancel').'" /></td>'."\n"
		  .'  </tr>'."\n"
		  .'</table>'."\n"
		  .'</form>'."\n";

		include 'adminfooter.php';
	  }
	}
	else{ // user id is not set, we cannot modify the permissions of unknown admin
	  header('Location: permissionadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	}
  }
  else{
	// include the header of the page
	include 'adminheader.php';

	// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the admin permissions were successfully entered in database.
	if(isset($_GET['action']) && $_GET['action']=='confirmPermissionChanges'){
	  echo '<p><strong>'.htmlentities(Translator::translate('console_permission_admin_confirmation'), ENT_COMPAT, 'UTF-8').'</strong></p>'."\n";
	}
	
	// print the HTML table header to display all administrators
	echo '<p class="largemargintop">'.htmlentities(Translator::translate('console_change_admin_permission_header'), ENT_COMPAT, 'UTF-8').'</p>'."\n"
	  .'<table class="form">'."\n";
	
	// select all administrators from database
	$query = 'SELECT id, name FROM '.AUTHORIZED_ADMINS;
	$result = mysql_query($query) or die('Error while selecting administratos.');

	// print all the administratos with a link to modify their permissions
	while($line = mysql_fetch_array($result)){
	  echo '  <tr>'."\n"
		.'    <td class="objectmodifyname">'.$line['name'].'</td>'."\n"
		.'    <td><a class="default" href="permissionadmin.php?action=changepermission&amp;userid='.$line['id'].'&amp;'.CURRENTMENU.'='.$_GET[CURRENTMENU].'">'.Translator::translate('change_permission').'</a></td>'."\n"
		.'  </tr>'."\n";
	}

	// print table footer
	echo '</table>'."\n";
	
	// include html page footer
	include 'adminfooter.php';
  }
}
?>