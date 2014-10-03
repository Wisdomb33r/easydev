<?php

// this include will include all the files that are needed for the console
// it will also start or reactivate the session and make some standards verifications on the variables
// it also make a verification that the user is logged
require_once('includes.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
// this is done through a constant for easy reconfiguration.
$adminMainMenu = CONFIG_MENU_ID;

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
	  $query = 'UPDATE '.CONFIGURATION.' SET value="'.$_POST['default_language'].'" WHERE id="default_language"';
	  mysql_query($query) or die('Error while updating default language.');
	  
	  // redirect on the same page but with a message to say that config has been properly changed
	  header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmConfigModif');
	  exit;
	}
  }
  else{
	// include the header of the page
	include 'adminheader.php';

	// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the config was properly changed
	if(isset($_GET['action']) && $_GET['action']=='confirmConfigModif'){
	  echo '<p><strong>'.htmlentities(Translator::translate('console_config_modif_confirmation'), ENT_COMPAT, 'UTF-8').'</strong></p>'."\n";
	}
	/*
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
	  }*/
	
	// print the HTML form to configure the console
	echo '<p class="largemargintop">'.htmlentities(Translator::translate('console_config_modify_info'), ENT_COMPAT, 'UTF-8').'</p>'."\n"
	  .'<form action="config.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n"
	  .'<table class="form">'."\n"
	  .'  <tr>'."\n"
	  .'    <td>'.htmlentities(Translator::translate('default_language'), ENT_COMPAT, 'UTF-8').' : </td>'."\n"
	  .'    <td><select class="selectinput" name="default_language">'."\n";
	$query = 'SELECT value FROM '.CONFIGURATION.' WHERE id="default_language"';
	$result = mysql_query($query) or die('Error while selecting default language configuration.');
	$row = mysql_fetch_array($result);

	$query = 'SELECT language, tag FROM '.TRANSLATION_LANGUAGES;
	$result = mysql_query($query) or die('Error while selecting language list.');
	while($line = mysql_fetch_array($result)){
	  echo '      <option value="'.$line['tag'].'"'.($line['tag'] == $row['value'] ? ' selected="selected"' : '').'>'.$line['language'].'</option>'."\n";
	}
	  echo '        </td>'."\n"
	  .'  </tr>'."\n"
	  .'  <tr>'."\n"
	  .'    <td>&nbsp;</td>'."\n"
	  .'    <td><input class="bouton" type="submit" name="add" value="'.htmlentities(Translator::translate('submit'), ENT_COMPAT, 'UTF-8').'" />'."\n"
	  .'        <input class="bouton" type="submit" name="cancel" value="'.htmlentities(Translator::translate('cancel'), ENT_COMPAT, 'UTF-8').'" /></td>'."\n"
	  .'  </tr>'."\n"
	  .'</table>'."\n"
	  .'</form>'."\n";

	include 'adminfooter.php';
  }
}
?>