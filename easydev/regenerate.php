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
	header('Location: '.CONSOLE_PATH.'index.php');
	exit;
}
else{ // if the user has the permissions

	$typelist = array('string', 'integer', 'date', 'bool', 'double');

	// if we want to regenerate the scripts
	if(isset($_GET['action']) && $_GET['action'] == 'regenerate' && isset($_GET['id'])){

		// retrieve the object defined by user from database
		$query = 'SELECT definition FROM '.EASYDEV_OBJECTS.' WHERE id_mainmenu="'.$_GET['id'].'"';
		$result = mysql_query($query) or die('Error while selecting definition based on id.');
		$row = mysql_fetch_array($result);

		if(!$row){
			header('Location: '.$_SERVER['PHP_SELF']);
			exit();
		}
		$easydevobject = $row['definition'];

		// -------------------- start of syntax checks ------------------------------
		// initialize a tokenizer
		$tokenizer = new tokenizer($easydevobject);

		// tokenize the user string and get a list of tokens
		$tokenlist = $tokenizer->tokenize();

		// if there is some errors in the tokenizer object
		$tokenizererrors = $tokenizer->errorslist;
		if(count($tokenizererrors) > 0){

			// PHP redirection on this script to print errors
			// NOTE : this should not happens as we are compiling an already-compiled object
			header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
			exit;
		}

		// ----------------- end of syntax checks -----------------------------------

		// ------------- start of parsing checks ------------------------------------
		// initialize a parser object
		$parser = new parser($tokenlist, $easydevobject);

		// parse the tokens and get a list of dbobjects
		$dbobjects = $parser->parse();

		if(count($parser->errorslist) > 0){

			// PHP redirection on this script to print errors
			// NOTE : this should not happens as we are compiling an already-compiled object
			header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
			exit;
		}

		// -------------- end of parsing checks -------------------------------------
		$errors = array();

		// execute the different tasks on the objects
		foreach($dbobjects as $dbobject){
			// retrieve the id of the mainmenu for this object
			// NOTE : as one compilation process contains several objects, this id_mainmenu can be different than $_GET['id']
			$query = 'SELECT id_mainmenu FROM '.EASYDEV_OBJECTS.' WHERE name="'.$dbobject->name.'"';
			$result = mysql_query($query) or die('Error while selecting main menu id based on name.');
			$row = mysql_fetch_array($result);
			$mainmenuid = $row['id_mainmenu'];

			// for relationNM, write the scripts on disc
			foreach($dbobject->fieldlist as $field){
				if($field->type == 'relationNM'){
					// get back the two menu id of the related table
					$query = 'SELECT id_mainmenu FROM '.EASYDEV_OBJECTS.' WHERE name="'.$field->label.'"';
					$result = mysql_query($query) or die('Error while selecting easydev object id.');
					$row = mysql_fetch_array($result);
					$foreignid = $row['id_mainmenu'];

					// if we are in the first defined object of the relation, the foreign id is not already set, so do nothing (all job done by the second object)
					if(isset($field->options['secondobject']) && $field->options['secondobject']){

						// find the foreign object
						$foreignobject = null;
						foreach($dbobjects as $obj){
							if($obj->name == $field->label){
								$foreignobject = $obj;
							}
						}

						// write the scripts for relations on disc
						$localRelCode = $dbobject->htmlNMRelationGenerator($mainmenuid, $field, $foreignobject);
						$foreignRelCode = $dbobject->htmlNMRelationGenerator($foreignid, $field, $foreignobject, true);
						$filepointerlocal = fopen('genscripts/objectnmrel_'.$dbobject->name.'_'.$field->label.'_'.$field->options['relationname'].'.php', 'w');
						$filepointerforeign = fopen('genscripts/objectnmrel_'.$field->label.'_'.$dbobject->name.'_'.$field->options['relationname'].'.php', 'w');
						if(!$filepointerlocal || !$filepointerforeign){
							//$query = 'ROLLBACK';
							//mysql_query($query) or die('Error while transaction rollback.');
							array_push($errors, Translator::translate('compile_fopen_pointer_error'));
							//$rolledback = true;
						}
						else{
							fwrite($filepointerlocal, $localRelCode);
							fwrite($filepointerforeign, $foreignRelCode);
							fclose($filepointerlocal);
							fclose($filepointerforeign);
						}
					} // end of if(isset($field->options['secondobject']) ...
				} // end of if($field->type == 'relationNM')
			} // end of foreach($dbobject->fieldlist as $field)

			//if(!$rolledback){ // if an NM relation did not make the process to ROLLBACK
			// generate the add html/php page and write it in a file
			$addPageCode = $dbobject->htmlAdderGenerator($mainmenuid);
			// find all the dbobjects that are linked
			$foreignobjectlist = array();
			foreach($dbobject->fieldlist as $field){
				if($field->type == 'relation1N'){
					foreach($dbobjects as $obj){
						if($obj->name == $field->label){
							$foreignobjectlist[] = $obj;
						}
					}
				}
			}
			$deletePageCode = $dbobject->htmlDeleterGenerator($mainmenuid, $foreignobjectlist);
			$classObjectCode = $dbobject->createObjectClass($foreignobjectlist);

			$filePointer = fopen('genscripts/objectadd_'.$dbobject->name.'.php', 'w');
			$filePointerDelete = fopen('genscripts/objectdelete_'.$dbobject->name.'.php', 'w');
			$filePointerClass = fopen('genscripts/object_'.$dbobject->name.'.class.php', 'w');
			if(!$filePointer || !$filePointerDelete || !$filePointerClass){
				//$query = 'ROLLBACK';
				//mysql_query($query) or die('Error while transaction rollback.');
				array_push($errors, Translator::translate('compile_fopen_pointer_error'));
			}
			else{
				fwrite($filePointer, $addPageCode);
				fwrite($filePointerDelete, $deletePageCode);
				fwrite($filePointerClass, $classObjectCode);
				fclose($filePointer);
				fclose($filePointerDelete);
				fclose($filePointerClass);
			}

			// insert the log
			$today = date('Y-m-d H:i');
			$log = $today.' : Regeneration of scripts for class \"'.$dbobject->name.'\" done by '.$_SESSION[SESSION_NAME].'.';
			$query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.$log.'")';
			mysql_query($query) or die('Error while inserting administrator log.');
		} // end of foreach($dbobjects as $dbobject)

		if(count($errors) > 0){
			$_SESSION[SESSION_ERRORS] = $errors;
			$_SESSION[SESSION_POSTED] = array();

			// PHP redirection on this script to print errors
			header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
			exit;
		}

		// now that the object is successfully generated, redirect on the main page
		header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action=confirmRegenerate');
		exit;

	}
	else{// if there is no regeneration process launched
		// include the header of the page
		include 'adminheader.php';

		// verify if $_GET['action'] is set. If it is the case, need to print a message to indicate that the compilation was successfully done.
		if(isset($_GET['action']) && $_GET['action']=='confirmRegenerate'){
			echo '<p><strong>'.htmlentities(Translator::translate('console_regeneration_confirmation'), ENT_COMPAT, 'UTF-8').'</strong></p>'."\n";
		}

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

			// do not forget to remove these two variables
			unset($_SESSION[SESSION_ERRORS]);
			unset($_SESSION[SESSION_POSTED]);
		}

		echo '<p class="largemargintop">'.htmlentities(Translator::translate('console_regeneration_page_title'), ENT_COMPAT, 'UTF-8').'</p>'."\n";

		// select every compilation done up to now
		$query = 'SELECT id_mainmenu AS id, name, definition FROM '.EASYDEV_OBJECTS.' ORDER BY definition';
		$result = mysql_query($query) or die('Error while selecting EasyDev objects.');

		echo '<table class="form">'."\n";
		$first = true;
		$lastdefinition = ''; // remember the last definition
		$lastid = 0;
		while($row = mysql_fetch_array($result)){
			if($row['definition'] != $lastdefinition){
				if(!$first){ // if this is not the first definition evaluated
					echo '</td>'."\n";
					echo '<td><a class="default" href="'.$_SERVER['PHP_SELF'].'?id='.$lastid.'&amp;action=regenerate&amp;'.CURRENTMENU.'='.$_GET[CURRENTMENU].'">'.htmlentities(Translator::translate('console_regeneration_link_text'), ENT_COMPAT, 'UTF-8').'</a></td>'."\n";
					echo '</tr>'."\n";
				}
				$lastdefinition = $row['definition'];
				$lastid = $row['id'];
				$first = false;
				echo '<tr>'."\n";
				echo '<td>'.$row['name'];
			}
			else{
				echo ', '.$row['name'];
			}
		}
		echo '</td>'."\n";
		echo '<td><a class="default" href="'.$_SERVER['PHP_SELF'].'?id='.$lastid.'&amp;action=regenerate&amp;'.CURRENTMENU.'='.$_GET[CURRENTMENU].'">'.htmlentities(Translator::translate('console_regeneration_link_text'), ENT_COMPAT, 'UTF-8').'</a></td>'."\n";
		echo '</tr>'."\n";
		echo '</table>'."\n";

		// include the footer of the page
		include 'adminfooter.php';
	}
}
?>