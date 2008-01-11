<?php 

// include the different files needed
require_once('includes.php');
require_once('tokenizer.class.php');
require_once('parser.class.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
// this is done through a constant for easy reconfiguration.
$adminMainMenu = COMPILER_MENU_ID;

// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: main.php');
  exit;
}
else{ // if the user has the permissions

  $typelist = array('string', 'integer', 'date', 'bool', 'double');

  // if there is some post data
  if($_SERVER['REQUEST_METHOD'] == 'POST'){

	// retrieve the object defined by user without the additional slashes
	$easydevobject = stripslashes($_POST['easyDevObject']);
	
	// -------------------- start of syntax checks ------------------------------
	// initialize a tokenizer
	$tokenizer = new tokenizer($easydevobject);

	// tokenize the user string and get a list of tokens
	$tokenlist = $tokenizer->tokenize();

	// if there is some errors in the tokenizer object
	$tokenizererrors = $tokenizer->errorslist;
	if(count($tokenizererrors) > 0){
	  $_SESSION[SESSION_ERRORS] = $tokenizererrors;
	  $_SESSION[SESSION_POSTED] = $_POST;
	  
	  // PHP redirection on this script to print errors
	  header('Location: '.$_SERVER[PHP_SELF].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}
	
	// ----------------- end of syntax checks -----------------------------------
	
	// ------------- start of parsing checks ------------------------------------
	// initialize a parser object
	$parser = new parser($tokenlist, $easydevobject);
	
	// parse the tokens and get a list of dbobjects
	$dbobjects = $parser->parse();

	if(count($parser->errorslist) > 0){
	  $_SESSION[SESSION_ERRORS] = $parser->errorslist;
	  $_SESSION[SESSION_POSTED] = $_POST;
	
	  // PHP redirection on this script to print errors
	  header('Location: '.$_SERVER[PHP_SELF].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}
	
	// -------------- end of parsing checks -------------------------------------
	
	// ------------- start of application-level checks --------------------------
	
	// verify that there is no duplicate objects with what is already in the database
	$query = 'SELECT id_mainmenu FROM '.EASYDEV_OBJECTS.' WHERE (0) ';
	foreach($dbobjects as $object){
	  $query .= 'OR name="'.$object->name.'" ';
	}
	$result = mysql_query($query) or die('Error while verifying duplicate entry in database.<br />'.$query);
	
	// if the number of objects returned is greater than 0, there is a conflict with database
	if(mysql_num_rows($result) > 0){
	  $errors = array();
	  array_push($errors, $translator->translate('compile_database_duplicate_object_error'));
	  $_SESSION[SESSION_ERRORS] = $errors;
	  $_SESSION[SESSION_POSTED] = $_POST;
	  
	  // PHP redirection on this script to print errors
	  header('Location: '.$_SERVER[PHP_SELF].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}
	
	// ------------- end of application-level checks ----------------------------
	$errors = array();
	
	// execute the different tasks on the objects
	foreach($dbobjects as $dbobject){
	  // open a transaction to protect all the different database inserts
	  $query = 'START TRANSACTION';
	  mysql_query($query) or die('Error while starting transaction.<br />'.$query);

	  // create a new menu for the new object
	  $query = 'INSERT INTO '.ADMINMAIN.' (text) VALUES ("'
		.$dbobject->name.'")';
	  mysql_query($query) or die('Error while inserting new main menu entry.<br />'.$query);

	  // retrieve the id of the main menu
	  $query = 'SELECT LAST_INSERT_ID()';
	  $result = mysql_query($query) or die('Error while selecting last insert id.<br />'.$query);
	  $line = mysql_fetch_row($result);
	  $mainmenuid = $line[0];

	  // insert the object in the object list in database
	  $query = 'INSERT INTO '.EASYDEV_OBJECTS.' (id_mainmenu, name, definition) VALUES ("'.$mainmenuid.'", "'
		.$dbobject->name.'", "'.addslashes($dbobject->usertextdef).'")'; // because we removed the slashes added on this variable, now need to add it
	  mysql_query($query) or die('Error while inserting new easydev object.<br />'.$query);

	  // create a new sub menu for the add page of the new object
	  $query = 'INSERT INTO '.ADMINSUB.' (id_mainmenu, text, url) VALUES ("'.$mainmenuid.'",
		"compile_add_object_sub_menu_title", "objectadd_'.$dbobject->name.'")';
	  mysql_query($query) or die('Error while inserting new sub menu entry.<br />'.$query);

	  // create a new sub menu for the delete page of the new object
	  $query = 'INSERT INTO '.ADMINSUB.' (id_mainmenu, text, url) VALUES ("'.$mainmenuid.'",
		"compile_delete_object_sub_menu_title", "objectdelete_'.$dbobject->name.'")';
	  mysql_query($query) or die('Error while inserting new sub menu entry.<br />'.$query);

	  // for relation1N, register the foreign key into easydev_objects_foreign_key
	  foreach($dbobject->fieldlist as $field){
		if($field->type == 'relation1N'){
		  // get back the id of the object identified as foreign object
		  $query = 'SELECT id_mainmenu FROM '.EASYDEV_OBJECTS.' WHERE name="'.$field->label.'"';
		  $result = mysql_query($query) or die('Error while selecting easydev object id.<br />'.$query);

		  // there can be one and only one result
		  $row = mysql_fetch_array($result);
		  
		  // update easydev_objects_foreign_key
		  $query = 'INSERT INTO '.EASYDEV_OBJECTS_FOREIGN_KEY.' (id_object, id_foreign_object, relationname) VALUES ("'.$mainmenuid.'", "'.$row['id_mainmenu'].'", "'.$field->options['relationname'].'")';
		  mysql_query($query) or die('Error while inserting foreign key entry.<br />'.$query);
		}
	  }

	  $rolledback = false;
	  // for relationNM, register the linking table into easydev_objects_linking_tables
	  foreach($dbobject->fieldlist as $field){
		if($field->type == 'relationNM'){
		  // get back the two menu id of the related table
		  $query = 'SELECT id_mainmenu FROM '.EASYDEV_OBJECTS.' WHERE name="'.$field->label.'"';
		  $result = mysql_query($query) or die('Error while selecting easydev object id.<br />'.$query);
		  $row = mysql_fetch_array($result);
		  $foreignid = $row['id_mainmenu'];
		  
		  // if we are in the first defined object of the relation, the foreign id is not already set, so do nothing (all job done by the second object)
		  if(isset($field->options['secondobject']) && $field->options['secondobject']){
			// register the linking table
			$query = 'INSERT INTO '.EASYDEV_OBJECTS_LINKING_TABLES.' (id_objet1, id_objet2, table_name, relationname) '
			  .'VALUES ("'.$foreignid.'", "'.$mainmenuid.'", "object_'.$field->label.'_'.$dbobject->name.'_'.$field->options['relationname'].
			  '_nmrelation", "'.$field->options['relationname'].'")';
			mysql_query($query) or die('Error while inserting relational table entry.<br />'.$query);
			
			// add the two new submenu
			$query = 'INSERT INTO '.ADMINSUB.' (id_mainmenu, text, url) '.
			  'VALUES ("'.$mainmenuid.'", "<-> '.$field->label.' ('.$field->options['relationname'].')", '.
			  '"objectnmrel_'.$dbobject->name.'_'.$field->label.'_'.$field->options['relationname'].'")';
			mysql_query($query) or die('Error while inserting new sub menu entry.<br />'.$query);
			
			$query = 'INSERT INTO '.ADMINSUB.' (id_mainmenu, text, url) '.
			  'VALUES ("'.$foreignid.'", "<-> '.$dbobject->name.' ('.$field->options['relationname'].')", '.
			  '"objectnmrel_'.$field->label.'_'.$dbobject->name.'_'.$field->options['relationname'].'")';
			mysql_query($query) or die('Error while inserting new sub menu entry.<br />'.$query);
			
			// write the scripts for relations on disc
			$localRelCode = $dbobject->htmlNMRelationGenerator($mainmenuid, $field);
			$foreignRelCode = $dbobject->htmlNMRelationGenerator($foreignid, $field, true);
			if(file_exists('objectnmrel_'.$field->label.'_'.$dbobject->name.'_'.$field->options['relationname'].'.php') 
			   || file_exists('objectnmrel_'.$dbobject->name.'_'.$field->label.'_'.$field->options['relationname'].'.php')){
			  $query = 'ROLLBACK';
			  mysql_query($query) or die('Error while transaction rollback.<br />'.$query);
			  array_push($errors, $translator->translate('compile_autogenerated_page_exist_error'));
			  $rolledback = true;
			}
			else{
			  $filepointerlocal = fopen('objectnmrel_'.$dbobject->name.'_'.$field->label.'_'.$field->options['relationname'].'.php', 'w');
			  $filepointerforeign = fopen('objectnmrel_'.$field->label.'_'.$dbobject->name.'_'.$field->options['relationname'].'.php', 'w');
			  if(!$filepointerlocal || !$filepointerforeign){
				$query = 'ROLLBACK';
				mysql_query($query) or die('Error while transaction rollback.<br />'.$query);
				array_push($errors, $translator->translate('compile_fopen_pointer_error'));
				$rolledback = true;
			  }
			  else{
				fwrite($filepointerlocal, $localRelCode);
				fwrite($filepointerforeign, $foreignRelCode);
				fclose($filepointerlocal);
				fclose($filepointerforeign);
			  }
			}
		  } // end of if(isset($field->options['secondobject']) ...
		} // end of if($field->type == 'relationNM')
	  } // end of foreach($dbobject->fieldlist as $field)

	  if(!$rolledback){ // if an NM relation did not make the process to ROLLBACK
		// generate the add html/php page and write it in a file
		$addPageCode = $dbobject->htmlAdderGenerator($mainmenuid);
		$deletePageCode = $dbobject->htmlDeleterGenerator($mainmenuid);
		$classObjectCode = $dbobject->createObjectClass();
		if(file_exists('objectadd_'.$dbobject->name.'.php') 
		   || file_exists('objectdelete_'.$dbobject->name.'.php') 
		   || file_exists('object_'.$dbobject->name.'.class.php')){
		  $query = 'ROLLBACK';
		  mysql_query($query) or die('Error while transaction rollback.<br />'.$query);
		  array_push($errors, $translator->translate('compile_autogenerated_page_exist_error'));
		}
		else{
		  $filePointer = fopen('objectadd_'.$dbobject->name.'.php', 'w');
		  $filePointerDelete = fopen('objectdelete_'.$dbobject->name.'.php', 'w');
		  $filePointerClass = fopen('object_'.$dbobject->name.'.class.php', 'w');
		  if(!$filePointer || !$filePointerDelete || !$filePointerClass){
			$query = 'ROLLBACK';
			mysql_query($query) or die('Error while transaction rollback.<br />'.$query);
			array_push($errors, $translator->translate('compile_fopen_pointer_error'));
		  }
		  else{
			fwrite($filePointer, $addPageCode);
			fwrite($filePointerDelete, $deletePageCode);
			fwrite($filePointerClass, $classObjectCode);
			fclose($filePointer);
			fclose($filePointerDelete);
			fclose($filePointerClass);
		  }
		}
	  } // end of if(! $rolledback)
	  /* WARNING : MySQL make an implicit COMMIT (end of transaction) when using most of the statements that
	   * create, delete or alter a table or a database in any manner. In this case, the application need a CREATE
	   * TABLE statement to create the table for the compiled object. For this reason the script do not contains 
	   * any COMMIT statement but only ROLLBACK when there is an error. Consequently, before executing the CREATE
	   * TABLE statement, we need to verify that everything was ok up to this point. The COMMIT is performed through
	   * the execution of the CREATE TABLE statement.
	   */
	  if(count($errors) == 0){
		// transform the different objects into SQL statements
		$querylist = $dbobject->sqltransform();
		foreach($querylist as $query){
		  mysql_query($query) or die('Error while object SQL execution.<br />'.$query);
		}

		// insert the log
		$today = date('Y-m-d H:i');
		$log = $today.' : New class with name \"'.$dbobject->name.'\" compiled by '.$_SESSION[SESSION_NAME].'.';
		$query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.$log.'")';
		mysql_query($query) or die('Error while inserting administrator log.<br />'.$query);
	  }
	} // end of foreach($dbobjects as $dbobject)

	if(count($errors) > 0){
	  $_SESSION[SESSION_ERRORS] = $errors;
	  $_SESSION[SESSION_POSTED] = $_POST;
	  
	  // PHP redirection on this script to print errors
	  header('Location: '.$_SERVER[PHP_SELF].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
	  exit;
	}
	
	// now that the object is successfully generated, redirect on the main page
	header('Location: '.$_SERVER[PHP_SELF].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmCompilation');
	exit;
	
	// ----------------- end of parsing checks --------------------------------
	
  }
  else{// if there is no post data
	// include the header of the page
	include 'adminheader.php';
	
	// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the compilation was successfully done.
	if(isset($_GET['action']) && $_GET['action']=='confirmCompilation'){
	  echo '<p><strong>'.htmlentities($translator->translate('console_compilation_confirmation')).'</strong></p>'."\n";
	}

	$form = '';
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
	  $form = $posted['easyDevObject'];
	  
	  // do not forget to remove these two variables
	  unset($_SESSION[SESSION_ERRORS]);
	  unset($_SESSION[SESSION_POSTED]);
	}
	
	// Pritn the form and if there was some errors, make the content of the textarea with what was sent
	echo '<form name="objectDefineArea" action="compiler.php?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n"
	  .'<textarea class="textareainput" name="easyDevObject">'."\n";
	if($form != ''){
	  echo htmlentities(stripslashes($form));
	}
	echo '</textarea><br />'."\n"
	  .'<input class="bouton" type="submit" name="create" value="'.$translator->translate('compile').'" />'."\n"
	  .'</form>'."\n";

	// include the footer of the page
	include 'adminfooter.php';
  }
}
?>