<?php

// this include will include all the files that are needed for the console
// it will also start or reactivate the session and make some standards verifications on the variables
// it also make a verification that the user is logged
require_once('includes.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
// this is done through a constant for easy reconfiguration.
$adminMainMenu = ADMIN_MENU_ID;

global $LINK;

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

	$errors = array();
	// ---------- beginning of the verifications on the form -----------------------
	  
	// verify that no special characters have beeen inserted (regexp verifies also that there is at least one char in the name of the admin)
	$regexpresult = preg_match(USERNAME_ACCEPTED_CHARS, $_POST['Name']);
	if($regexpresult == 0 || $regexpresult == false){
	  array_push($errors, Translator::translate('console_username_error'));
	}

	// verify that no other admin has exactly the same name than this one
	$query = 'SELECT id FROM '.AUTHORIZED_ADMINS.' WHERE name="'.$_POST['Name'].'"';
	$result = mysqli_query($LINK, $query) or die('Could not select admins in database');
	if($line = mysqli_fetch_array($result)){
	  array_push($errors, Translator::translate('console_duplicate_admin_username_error'));
	}
	
	// verify that the password was two times the same
	if($_POST['Pass'] != $_POST['PassConfirmation']){
	  array_push($errors, Translator::translate('console_wrong_confirm_password_error'));
	}

	// verify that the password has at least 6 chars
	if(strlen($_POST['Pass']) < 6){
	  array_push($errors, Translator::translate('console_too_short_password_error'));
	}

	// ------------------ end of the verifications on the form ----------------------
	
	// if there is an error here, we need to redirect on the same page
	if(count($errors) != 0){
	  $_SESSION[SESSION_ERRORS] = $errors;
	  $_SESSION[SESSION_POSTED] = $_POST;
	  
	  // PHP redirection on this script to print errors
	  header('Location: addadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}
	else{ // if there is no errors
	  // store the admin in the database
	  $query = 'INSERT INTO '.AUTHORIZED_ADMINS.' (name, password) VALUES ("'.$_POST['Name'].'", "'.sha1($_POST['Pass']).'")';
	  mysqli_query($LINK, $query) or die('Error while inserting new administrator into database.');

	  $today = date('Y-m-d H:i');
	  $log = $today.' : New administrator with name \"'.$_POST['Name'].'\" inserted by '.$_SESSION[SESSION_NAME].'.';
	  $query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.$log.'")';
	  mysqli_query($LINK, $query) or die('Error while inserting administrator log.');
	  
	  // redirect on the same page but with a message to say that admin has been successfully entered in the database
	  header('Location: addadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmAdminAdd');
	  exit;
	}
  }
  else{
	// include the header of the page
	include 'adminheader.php';

	// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the admin was successfully entered in database.
	if(isset($_GET['action']) && $_GET['action']=='confirmAdminAdd'){
	  echo '<p><strong>'.htmlentities(Translator::translate('console_add_admin_confirmation'), ENT_COMPAT, 'UTF-8').'</strong></p>'."\n";
	}
	
	$name = '';
	// Verify if there is some errors and posted values.
	if(isset($_SESSION[SESSION_ERRORS]) && isset($_SESSION[SESSION_POSTED])){
	  
	  $errors = $_SESSION[SESSION_ERRORS];
	  $posted = $_SESSION[SESSION_POSTED];
	  
	  // print the errors
	  echo '<ul class="errors">'."\n";
	  foreach ($errors as $error){
		echo '<li class="errors">'.$error.'</li>'."\n";
	  }
	  echo '</ul>'."\n";
	  
	  // restore the values of the form
	  $name = $posted['Name'];
	  
	  // do not forget to remove these two variables
	  unset($_SESSION[SESSION_ERRORS]);
	  unset($_SESSION[SESSION_POSTED]);
	}
	
	// print the HTML form to add an administrator in the database
	echo '<p class="largemargintop">'.htmlentities(Translator::translate('console_add_admin_header'), ENT_COMPAT, 'UTF-8').'</p>'."\n"
	  .'<form action="addadmin.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n"
	  .'<table class="form">'."\n"
	  .'  <tr>'."\n"
	  .'    <td>'.htmlentities(Translator::translate('name'), ENT_COMPAT, 'UTF-8').' : </td>'."\n"
	  .'    <td><input class="textinput" type="text" name="Name" maxlength="50" '.($name != '' ? 'value="'.htmlentities(stripslashes($name), ENT_COMPAT, 'UTF-8').'" ' : '').'/></td>'."\n"
	  .'  </tr>'."\n"
	  .'  <tr>'."\n"
	  .'    <td>'.htmlentities(Translator::translate('password'), ENT_COMPAT, 'UTF-8').' : </td>'."\n"
	  .'    <td><input class="passwordinput" type="password" name="Pass" maxlength="20" /></td>'."\n"
	  .'  </tr>'."\n"
	  .'  <tr>'."\n"
	  .'    <td>'.htmlentities(Translator::translate('password_confirmation'), ENT_COMPAT, 'UTF-8').' : </td>'."\n"
	  .'    <td><input class="passwordinput" type="password" name="PassConfirmation" maxlength="20" /></td>'."\n"
	  .'  </tr>'."\n"
	  .'  <tr>'."\n"
	  .'    <td>&nbsp;</td>'."\n"
	  .'    <td><input class="bouton" type="submit" name="add" value="'.htmlentities(Translator::translate('add'), ENT_COMPAT, 'UTF-8').'" />'."\n"
	  .'        <input class="bouton" type="submit" name="cancel" value="'.htmlentities(Translator::translate('cancel'), ENT_COMPAT, 'UTF-8').'" /></td>'."\n"
	  .'  </tr>'."\n"
	  .'</table>'."\n"
	  .'</form>'."\n";

	include 'adminfooter.php';
  }
}
?>