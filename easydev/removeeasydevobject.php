<?php

// this include will include all the files that are needed for the console
// it will also start or reactivate the session and make some standards verifications on the variables
// it also make a verification that the user is logged
require_once('includes.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
// this is done through a constant for easy reconfiguration.
$adminMainMenu = COMPILER_MENU_ID;

// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: main.php');
  exit;
}
else{ // if the user has the permissions

  if($_SERVER['REQUEST_METHOD'] == 'POST'){ // the user posted his form
	
	// verify if the user has clicked on the "cancel" button
	if(isset($_POST['cancel'])){
	  header('Location: main.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU]); // redirect on the main page
	  exit;
	}

	// NOTE : as all relations are InnoDB links, there is no need to touch ADMINSUB neither EASYDEV_OBJECTS neither EASYDEV_OBJECTS_FOREIGN_KEY
	//        the delete will cascade to them by deleting the ADMINMAIN
	foreach($_POST['deleteids'] as $delete){
	  // verify if a foreign key is pointing on this table
	  $query = 'SELECT id_object FROM '.EASYDEV_OBJECTS_FOREIGN_KEY.' WHERE id_foreign_object="'.$delete.'"';
	  $result = mysql_query($query) or die('Error while selecting foreign key.<br />'.$query);
	  if(mysql_num_rows($result) > 0){
		// this can happens only if user manipulates the $_POST values, because the script should not allow to check such object
		header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
		exit;
	  }

	  // verify if one or more linking tables is pointing on this table
	  $query = 'SELECT id_objet1, id_objet2, table_name, relationname FROM '.EASYDEV_OBJECTS_LINKING_TABLES.' WHERE id_objet1="'.$delete.'" OR id_objet2="'.$delete.'"';
	  $resultlt = mysql_query($query) or die('Error while selecting linking tables.<br />'.$query);

	  while($line = mysql_fetch_array($resultlt)){
		// if there is one, retrieve the name of both objects
		$query = 'SELECT name FROM '.EASYDEV_OBJECTS.' WHERE id_mainmenu="'.$line['id_objet1'].'"';
		$query2 = 'SELECT name FROM '.EASYDEV_OBJECTS.' WHERE id_mainmenu="'.$line['id_objet2'].'"';

		$resultobjet1 = mysql_query($query) or die('Error while selecting object name.<br />'.$query);
		$resultobjet2 = mysql_query($query2) or die('Error while selecting object name.<br />'.$query);

		$line1 = mysql_fetch_array($resultobjet1);
		$line2 = mysql_fetch_array($resultobjet2);
		
		$obj1name = $line1['name'];
		$obj2name = $line2['name'];

		// remove the scripts for the N:M relation
		unlink('objectnmrel_'.$obj1name.'_'.$obj2name.'_'.$line['relationname'].'.php');
		unlink('objectnmrel_'.$obj2name.'_'.$obj1name.'_'.$line['relationname'].'.php');

		// remove the table of the relation
		$query = 'DROP TABLE '.$line['table_name'];
		mysql_query($query) or die('Error while dropping object relation table.<br />'.$query);
	  }

	  // start a transaction to protect the different queries
	  $query = 'START TRANSACTION';
	  mysql_query($query) or die('Error while starting transaction.<br />'.$query);

	  // retrieve the name of the object
	  $query = 'SELECT name FROM '.EASYDEV_OBJECTS.' WHERE id_mainmenu="'.$delete.'"';
	  $result = mysql_query($query) or die('Error while selecting object name.<br />'.$query);
	  $line = mysql_fetch_array($result);
	  $objectname = $line['name'];

	  // verify if there is any image script associated to this object
	  $query = 'SELECT field_name FROM '.EASYDEV_OBJECTS_IMAGE_SCRIPTS.' WHERE id_object="'.$delete.'"';
	  $resultis = mysql_query($query) or die('Error while selecting image scripts.<br />'.$query);

	  while($line = mysql_fetch_array($resultis)){
		unlink('object_image_'.$objectname.'_'.$line['field_name'].'.php');
	  }

	  // delete the mainmenu for the objects
	  $query = 'DELETE FROM '.ADMINMAIN.' WHERE id="'.$delete.'"';
	  mysql_query($query) or die('Error while deleting main menu section.<br />'.$query);

	  // remove the object table
	  // WARNING : MySQL makes an AUTOCOMMIT when executing most of the statement that create, delete or alter tables.
	  // Here the commit is done through the DROP TABLE statement.
	  $query = 'DROP TABLE object_'.$objectname;
	  mysql_query($query) or die('Error while dropping object table.<br />'.$query);

	  // remove all the generated scripts
	  unlink('objectadd_'.$objectname.'.php');
	  unlink('objectdelete_'.$objectname.'.php');
	  unlink('object_'.$objectname.'.class.php');

	  // insert the log
	  $today = date('Y-m-d H:i');
	  $log = $today.' : Suppression of class \"'.$objectname.'\" by '.$_SESSION[SESSION_NAME].'.';
	  $query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.$log.'")';
	  mysql_query($query) or die('Error while inserting administrator log.<br />'.$query);
	}

	// redirect on the same page with a confirmation message of the delete
	header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmObjectDelete');
	exit;
  }
  else{
	// include the header of the page
	include 'adminheader.php';

	// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the admin was successfully deleted.
	if(isset($_GET['action']) && $_GET['action']=='confirmObjectDelete'){
	  echo '<p><strong>'.htmlentities($translator->translate('console_remove_easydev_object_confirmation')).'</strong></p>'."\n";
	}

	// select all easydev objects currently in the database
	$query = 'SELECT id_mainmenu, name FROM '.EASYDEV_OBJECTS.' ORDER BY id_mainmenu ASC';
	$result = mysql_query($query) or die('Error while selecting objects list.<br />'.$query);
	
	// print the HTML form to delete objects in the database
	echo '<p class="largemargintop">'.htmlentities($translator->translate('console_remove_objects_header')).'</p>'."\n"
	  .'<form action="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n"
	  .'<table class="form">'."\n";

	while($line = mysql_fetch_array($result)){
	  // verify if a foreign key is pointing on this table
	  $query = 'SELECT id_object FROM '.EASYDEV_OBJECTS_FOREIGN_KEY.' WHERE id_foreign_object="'.$line['id_mainmenu'].'"';
	  $resultfk = mysql_query($query) or die('Error while selecting foreign key constraint.<br />'.$query);

	  // verify if a relation table is pointing on this table
	  $query = 'SELECT table_name FROM '.EASYDEV_OBJECTS_LINKING_TABLES.' WHERE id_objet1="'.$line['id_mainmenu'].'" OR id_objet2="'.$line['id_mainmenu'].'"';
	  $resultlt = mysql_query($query) or die('Error while selecting linking tables constraint.<br />'.$query);

	  echo '  <tr>'."\n";
	  if(mysql_num_rows($resultfk) > 0){
		echo '    <td></td>'."\n";
		echo '    <td>'.$line['name'].' *</td>'."\n";
	  }
	  else if(mysql_num_rows($resultlt) > 0){
		echo '    <td><input class="checkboxinput" type="checkbox" name="deleteids[]" value="'.$line['id_mainmenu'].'" /></td>'."\n";
		echo '    <td>'.$line['name'].' **</td>'."\n";
	  }
	  else{
		echo '    <td><input class="checkboxinput" type="checkbox" name="deleteids[]" value="'.$line['id_mainmenu'].'" /></td>'."\n";
		echo '    <td>'.$line['name'].'</td>'."\n";
	  }
	  echo '  </tr>'."\n";
	}
	echo '  <tr>'."\n"
	  .'    <td>&nbsp;</td>'."\n"
	  .'    <td><input class="bouton" type="submit" name="delete" value="'.htmlentities($translator->translate('delete')).'" />'."\n"
	  .'        <input class="bouton" type="submit" name="cancel" value="'.htmlentities($translator->translate('cancel')).'" /></td>'."\n"
	  .'  </tr>'."\n"
	  .'</table>'."\n"
	  .'</form>'."\n"
	  .'<p> * '.$translator->translate('console_foreign_key_constraint_delete_explanations').'</p>'."\n"
	  .'<p> ** '.$translator->translate('console_linking_table_constraint_delete_explanations').'</p>'."\n";

	// include the footer of the page
	include 'adminfooter.php';
  }
}
?>