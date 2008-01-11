<?php
/* dbobject represent the object the user wants to define.
 *
 */
class dbobject{
  var $name; // the name of the object
  var $fieldlist; // the different fields of the object
  var $usertextdef; // the textual definition that user entered
  var $translator; // the translator

  function __construct($name, $fieldlist, $usertextdef, $translator){
	$this->name = $name;
	$this->fieldlist = $fieldlist;
	$this->usertextdef = $usertextdef;
	$this->translator = $translator;
  }

  /* Getter for the differents attributes.
   * @param string name the name of the attribute to get.
   */
  public function __get($name){
	return $this->$name;
  }

  /* Setter for the differents attributes.
   * @param string name the name of the attribute to set.
   * @param mixed value the new value of the attribute.
   */
  public function __set($name, $value){
	$this->$name = $value;
  }

  /* Print the class name and definition in html.
   * This is a debug function.
   */
  public function htmlPrint(){
	echo '<br />class '.$this->name.' {';
	foreach($this->fieldlist as $fieldobject){
	  $fieldobject->htmlPrint();
	}
	echo '<br />}';
  }
  
  /* Transform the class into a SQL CREATE TABLE query to create the table
   * WARNING : mysql_query do not support multiple request into the same function call.
   * I need to return an array of request to execute consecutively by the caller.
   * @return array An array of strings containing the different queries to execute by the caller.
   */
  public function sqltransform(){
	$return = array();
	$ret = 'CREATE TABLE object_'.$this->name.' ('."\n"
	  .'id int(10) unsigned NOT NULL auto_increment,'."\n";

	foreach($this->fieldlist as $field){
	  switch($field->type){
	  case 'relationNM':
		// nothing to add, it is in a separate table
		break;
	  case 'relation1N':
		$ret .= '1n_rel_'.$field->options['relationname'].' int(10) unsigned NOT NULL,'."\n";
		break;
	  case 'string':
		$ret .= $field->label.' text collate latin1_german1_ci NULL,'."\n";
		break;
	  case 'date':
		$ret .= $field->label.' timestamp NULL,'."\n";
		break;
	  case 'bool':
		$ret .= $field->label.' tinyint(1) NULL,'."\n";
		break;
	  case 'integer':
		$ret .= $field->label.' int(10) NULL,'."\n";
		break;
	  case 'double':
		$ret .= $field->label.' double NULL,'."\n";
		break;
	  case 'finder':
	  case 'updater':
		break; // these two cases have impact only on the class object generation
	  default:
		die('Default statement in sqltransform() encountered.');
		break;
	  }
	}
	$ret .= 'PRIMARY KEY (ID)'."\n"
	  .') ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;';
	array_push($return, $ret);

	// add the constraints on the fields that are relations with other tables
	foreach($this->fieldlist as $field){
	  switch($field->type){
	  case 'relation1N':
		// keys of the table
		$query2 = 'ALTER TABLE object_'.$this->name.' ADD INDEX (1n_rel_'.$field->options['relationname'].');';
		array_push($return, $query2);

		// InnoDB relations
		$query3 = 'ALTER TABLE object_'.$this->name.
		  ' ADD FOREIGN KEY (1n_rel_'.$field->options['relationname'].')'.
		  ' REFERENCES object_'.$field->label.' (id)'.
		  ' ON DELETE CASCADE ON UPDATE CASCADE;';
		array_push($return, $query3);
		break;
	  case 'relationNM':
		// only for the second object defined in the code, make the linking table and InnoDB links
		if(isset($field->options['secondobject']) && $field->options['secondobject']){
		  // create linking table
		  $query = 'CREATE TABLE object_'.$field->label.'_'.$this->name.'_'.$field->options['relationname'].'_nmrelation ('."\n"
			.'id_'.$field->label.' int(10) unsigned NOT NULL,'."\n"
			.'id_'.$this->name.' int(10) unsigned NOT NULL,'."\n"
			.'PRIMARY KEY (id_'.$field->label.',id_'.$this->name.')'."\n"
			.') ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;';
		  array_push($return, $query);

		  // create the InnoDB relations
		  $query = 'ALTER TABLE object_'.$field->label.'_'.$this->name.'_'.$field->options['relationname'].'_nmrelation'.
			' ADD FOREIGN KEY (id_'.$field->label.')'.
			' REFERENCES OBJECT_'.$field->label.' (id)'.
			' ON DELETE CASCADE ON UPDATE CASCADE;';
		  array_push($return, $query);
		  $query = 'ALTER TABLE object_'.$field->label.'_'.$this->name.'_'.$field->options['relationname'].'_nmrelation'.
			' ADD FOREIGN KEY (id_'.$this->name.')'.
			' REFERENCES object_'.$this->name.' (id)'.
			' ON DELETE CASCADE ON UPDATE CASCADE;';
		  array_push($return, $query);
		}
		break;
	  }
	}
	
	return $return;
  }

  /* Read a template from a file and transform it into a PHP script.
   * NOTE : the template file can use several predefined variables in meta-tags : 
   *        - $this->name : the name of the object
   *        - $this->fieldlist : the list of all fields of the object
   *        - $mainmenuid : if the generated script should go in a specific section of the EasyDev console, the mainmenuid identify this menu
   * @param string $templatefile the template file
   * @param integer $mainmenuid the id of the console menu the script should belongs to
   */
  public function useTemplate($templatefile, $predefinedvariables=null, $debug=false){
	// restore the predefined variables (to be valid in the scope of this funciton, so they can be used in the template)
	if($predefinedvariables != null){
	  foreach($predefinedvariables as $key => $value){
		$$key = $value; // I love php for this ;-)
	  }
	}

	// read the template from the .tpl file
	$filepointer = fopen('templates/'.$templatefile, 'r');
	$template = ''; // the variable which will contain the content of the template file
	if(! $filepointer){
	  die('Error while reading template file.');
	}
	else{
	  while(! feof($filepointer)){
		$template .= fgets($filepointer, 999); // read all lines one by one (we expect any line to be shorter than 999 bytes ;-) )
	  }
	}
	
	// now we have in $templatecontent the content of the template file
	$translatedcode = ''; // the variable which will contain the final php code of the script
	$currentposition = 0;
	$nextMetaTagPos = PHP_INT_MAX;
	while($nextMetaTagPos && $nextMetaTagPos > 0){
	  $nextMetaTagPos = strpos($template, '<%', $currentposition); // search for <% in the template starting from position $currentposition
	  if($nextMetaTagPos > 0){
		// everything what is before the <% is added between quotes to the templatecode
		// NOTE : because we want to replace only single quotes and not double quotes, no use of addslashes but str_replace instead
		//        maybe addslashes has an optional argument to indicates we want to replace only single quotes... did not verify
		$translatedcode .= 'echo \''.str_replace('\'', '\\\'', substr($template, $currentposition, $nextMetaTagPos - $currentposition)).'\';';
		$currentposition = $nextMetaTagPos + 2; // we skip the <%= string
		
		// search for the end tag %>
		$endofMetaTagPos = strpos($template, '%>', $currentposition);
		if(!$endofMetaTagPos){
		  die('Parsing error in template file '.$templatefile.'. Expecting : "%>". Found : EOF');
		}
		else{
		  $translatedcode .= substr($template, $currentposition, $endofMetaTagPos - $currentposition);
		  $currentposition = $endofMetaTagPos + 2; // skip the %> meta tag end
		}
	  }
	} // end of while

	// add the rest of the file which do not contain any meta tag anymore
	$translatedcode .= 'echo \''.str_replace('\'', '\\\'', substr($template, $currentposition, strlen($template))).'\';';
	
	// now we have the code to eval in $translatedcode
	// start a bufferization to retrieve the content without it being sent to the user
	ob_start();
	eval($translatedcode);
	$return = ob_get_clean();
	if($debug){
	  die($translatedcode.'<br >-----------------------------------------------------------------'.$return);
	}
	return $return;
  }

  /* Create a PHP class definition for the current object.
   */
  public function createObjectClass(){
	$script = $this->useTemplate('objectclass.tpl');
	return $script;
  }

  /* Return the html/php code for the delete pages
   * @param integer $mainmenuid the id of the console main menu the script is part of
   */ 
  public function htmlDeleterGenerator($mainmenuid){
	$predefinedvariables = array();
	$predefinedvariables['mainmenuid'] = $mainmenuid;
	$script = $this->useTemplate('objectdelete.tpl', $predefinedvariables);
	return $script;
  }

  /* Return the html/php code for the add script
   * @param integer $mainmenuid the id of the console main menu the script is part of
   */ 
  public function htmlAdderGenerator($mainmenuid){
	$predefinedvariables = array();
	$predefinedvariables['mainmenuid'] = $mainmenuid;
	$script = $this->useTemplate('objectadd.tpl', $predefinedvariables);
	return $script;
  }

  /* Return the html/php code for the relationNM
   * @param integer $mainmenuid the id of the console main menu the script is part of
   * @param string $relationfieldname The name of the object with which the relation is. Useful only if the object has several NM relations.
   * @param boolean $foreign Indicates if the script should generate the page for the foreign object
   */ 
  public function htmlNMRelationGenerator($mainmenuid, $relationfield, $foreign=false){
	$predefinedvariables = array();
	$predefinedvariables['mainmenuid'] = $mainmenuid;
	$predefinedvariables['relationfield'] = $relationfield;
	$predefinedvariables['foreign'] = $foreign;
	$script = $this->useTemplate('objectNMrel.tpl', $predefinedvariables);
	
	return $script;
  }

  /* Return the html/php code for the relationNM
   * @param integer $mainmenuid the id of the console main menu the script is part of
   * @param string $relationfieldname The name of the object with which the relation is. Useful only if the object has several NM relations.
   * @param boolean $foreign Indicates if the script should generate the page for the foreign object
   */ 
  public function htmlNMRelationGeneratorBKP($mainmenuid, $relationfieldname, $foreign=false){
	// verify that the relationfieldname exist in the fieldlist
	$relationfield = null;
	foreach($this->fieldlist as $field){
	  if($relationfield == null && $field->type == 'relationNM' && $field->label == $relationfieldname){
		$relationfield = $field;
	  }
	}

	if($relationfield == null) // this is an error that should never occur
	  die('Error while generating relationNM scripts : bad relation field parameter.');

	// ----- retrieve in $localtextfields and $remotetextfields the fields of $relationfield->label and $this->name that can be printed as text ------
	$relationtextfields = array();
	$localtextfields = array();

	// find the labels that can be displayed as text from $this->fieldlist (local text fields)
	foreach($this->fieldlist as $field){
	  switch($field->type){
	  case 'integer':
	  case 'string':
	  case 'double':
	  case 'bool':
		// if the type of the field is int, bool, double, text, it can be printed as text
		array_push($localtextfields, $field->label);
		break;
	  default:
		break;
	  }
	}

	// need to select all the objects from object_$relationfield->label (relation text fields)
	// WARNING : as we are in object $this->name, we do not know the fields of object $relationfield->label.
	//           for this reason, need to analyze the table to select only text-printable fields
	
	// find the labels which can be displayed as text from the table object_$relationfield->label
	$query = 'DESCRIBE object_'.$relationfield->label;
	$result = mysql_query($query) or die('Error while getting table description.<br />'.$query);
	
	// this list will be used later to print a select list, so ids and other relations should not be taken into account.
	while($line = mysql_fetch_array($result)){
	  $triplet = substr($line['Type'], 0, 3);
	  switch($triplet){
	  case 'int':
	  case 'tin':
	  case 'tex':
	  case 'dou':
		// if the type of the field is int, tinyint, text or double, it can be printed as text
		if($line['Field'] != 'id' && substr($line['Field'], 0, 3) != 'id_'){// remove the primary key and relations
		  array_push($relationtextfields, $line['Field']);
		}
		break;
	  default:
		break;
	  }
	}

	// -------------------------------------------- end of search of textfields ---------------------------------------------------------------------

	// now generate the script
	$ret = 
	  '<?php'."\n"
	  .'/*********************************************************************************'."\n"
	  .' * Autogenerated script'."\n"
	  .' * EasyDev v.0.x copyright Patrick Mingard 2007'."\n"
	  .' * Any modification of this code can alter the behaviour of EasyDev v.0.x console'."\n" 
	  .' ********************************************************************************/'."\n"
	  .' '."\n"
	  .'require_once(\'includes.php\');'."\n" // includes of the generated script
	  .' '."\n"
	  .'// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.'."\n"
	  .'$adminMainMenu = '.$mainmenuid.';'."\n"
	  .' '."\n"
	  .'// verify that the logged user has right to see this page'."\n"
	  .'if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights'."\n"
	  .'  header(\'Location: main.php\');'."\n"
	  .'  exit;'."\n"
	  .'}'."\n"
	  .'if($_SERVER[REQUEST_METHOD] == \'POST\'){'."\n" // if server method request == POST in the generated script
	  .'  // first remove all relations to insert new ones'."\n"
	  .'  $query = \'DELETE FROM object_'.$relationfield->label.'_'.$this->name.'_nmrelation WHERE ';
	if($foreign)
	  $ret .= 'id_'.$relationfield->label.'="\'.$_POST[\'selected\'].\'"\';'."\n";
	else
	  $ret .= 'id_'.$this->name.'="\'.$_POST[\'selected\'].\'"\';'."\n";
	$ret .= '  mysql_query($query) or die(\'Error while deleting relations.<br />\'.$query);'."\n"
	  .'  '."\n"
	  .'  // insert all the relations'."\n"
	  .'  foreach($_POST[\'relationids\'] as $newrelation){'."\n"
	  .'    $query = \'INSERT INTO object_'.$relationfield->label.'_'.$this->name.'_nmrelation (id_'.$relationfield->label.', id_'.$this->name.') ';
	if($foreign)
	  $ret .= 'VALUES ("\'.$_POST[\'selected\'].\'", "\'.$newrelation.\'")\';'."\n";
	else
	  $ret .= 'VALUES ("\'.$newrelation.\'", "\'.$_POST[\'selected\'].\'")\';'."\n";
	$ret .= '    mysql_query($query) or die(\'Error while inserting new relations.<br />\'.$query);'."\n"
	  .'  }'."\n"
	  .'  '."\n"
	  .'  // make a redirection on the same page'."\n"
 	  .'  header(\'Location: \'.$_SERVER[\'PHP_SELF\'].\'?\'.CURRENTMENU.\'=\'.$_GET[CURRENTMENU]);'."\n"
	  .'  exit;'."\n"
	  .'}'."\n"
	  .'else if(isset($_GET[\'action\']) && $_GET[\'action\'] == \'displayRelationObjects\'){ // if $_GET[\'action\'] == displayRelationObjects'."\n"
	  .'  // verify that $_GET[\'selected\'] is set (verification that the user chose an object for a relation'."\n"
	  .'  if(isset($_GET[\'selected\'])){'."\n"
	  .'    // verify that the object exist'."\n"
	  .'    $query = \'SELECT id FROM object_'.($foreign ? $relationfield->label : $this->name).' WHERE id="\'.$_GET[\'selected\'].\'"\';'."\n"
	  .'    $result = mysql_query($query) or die(\'Error while selecting objects list.<br />\'.$query);'."\n"
	  .'    if(mysql_num_rows($result) > 0){ // there is at least one object with this id in database (in fact there cannot be more than one)'."\n"
	  .'      // display all the objects that can be related to the one chosen. '."\n"; // First find all the text-printable fields
	$textfields = array();
	if($foreign){
	  // need to select all the objects from object_$this->name (local)
	  $textfields = $localtextfields;
	}
	else{
	  // need to select all the objects from object_$relationfield->label (remote)
	  $textfields = $relationtextfields;
	}

	// generate the SELECT statement for the generated script
	$ret .= '      $query = \'SELECT id';
	foreach($textfields as $t){
		$ret .= ', '.$t;
	}
	$ret .= ' FROM object_';
	if($foreign)
	  $ret .= $this->name;
	else
	  $ret .= $relationfield->label;
	$ret .= ' ORDER BY id ASC\';'."\n"
	  .'      $resultObjects = mysql_query($query) or die(\'Error while selecting objects list.<br />\'.$query);'."\n"
	  .'      '."\n"
	  .'      // now select all the ones that are already checked from relation'."\n"
	  .'      $query = \'SELECT id_'.$relationfield->label.', id_'.$this->name.' FROM object_'.$relationfield->label.'_'.$this->name.'_nmrelation WHERE ';
	if($foreign)
	  $ret .= 'id_'.$relationfield->label.'="\'.$_GET[\'selected\'].\'"\';'."\n";
	else
	  $ret .= 'id_'.$this->name.'="\'.$_GET[\'selected\'].\'"\';'."\n";
	$ret .= '      $resultCheckedObjects = mysql_query($query) or die(\'Error while selecting objects list.<br />\'.$query);'."\n"
	  .'      '."\n"
	  .'      // generate an array with all checked items'."\n"
	  .'      $checkedItems = array();'."\n"
	  .'      while($row = mysql_fetch_array($resultCheckedObjects)){'."\n";
	if($foreign)
	  $ret .= '        array_push($checkedItems, $row[\'id_'.$this->name.'\']);'."\n";
	else
	  $ret .= '        array_push($checkedItems, $row[\'id_'.$relationfield->label.'\']);'."\n";
	$ret .= '      }'."\n"
	  .'      '."\n"
	  .'      // generate the table with checkboxes for selecting the objects to relate'."\n"
	  .'      include \'adminheader.php\';'."\n"
	  .'      echo \'<form action="\'.$_SERVER[\'PHP_SELF\'].\'?\'.CURRENTMENU.\'=\'.$_GET[CURRENTMENU].\'" method="post">\'."\n";'."\n"
	  .'      echo \'<table class="form">\'."\n";'."\n"
	  .'      while($line = mysql_fetch_array($resultObjects)){'."\n"
	  .'        echo \'  <tr>\'."\n";'."\n"
	  .'        echo \'    <td><input type="checkbox" class="inputcheckbox" name="relationids[]" value="\'.$line[\'id\'].\'"\'.(in_array($line[\'id\'], $checkedItems) ? \' checked="checked"\' : \'\').\'/></td>\'."\n";'."\n"
	  .'        echo \'    <td>\'.$line[\'id\'].\'';
	foreach($textfields as $t){
	  $ret .= ' - \'.$line[\''.$t.'\'].\'';
	}
	$ret .= '</td>\'."\n";'."\n"
	  .'        echo \'  </tr>\'."\n";'."\n"
	  .'      } // end of while'."\n"
	  .'      echo \'  <tr>\'."\n";'."\n"
	  .'      echo \'    <td><input type="hidden" name="selected" value="\'.$_GET[\'selected\'].\'" /></td>\'."\n";'."\n"
	  .'      echo \'    <td><input class="bouton" type="submit" name="validateRelations" value="\'.htmlentities($translator->translate(\'create_relations\')).\'" /></td>\'."\n";'."\n"
	  .'      echo \'  </tr>\'."\n";'."\n"
	  .'      echo \'</table>\'."\n";'."\n"
	  .'      echo \'</form>\'."\n";'."\n"
	  .'      include \'adminfooter.php\';'."\n"
	  .'    } // end of if(mysql_num_rows() > 0)'."\n"
	  .'    else{'."\n"
	  .'      // there is no object with this id, redirect on the default page'."\n"
 	  .'      header(\'Location: \'.$_SERVER[\'PHP_SELF\'].\'?\'.CURRENTMENU.\'=\'.$_GET[CURRENTMENU]);'."\n"
	  .'      exit;'."\n"
	  .'    }'."\n"
	  .'  }// end of if(isset($_GET[selected]))'."\n"
	  .'  else{'."\n"
	  .'    // the selected item is not set, redirect on the default page'."\n"
 	  .'    header(\'Location: \'.$_SERVER[\'PHP_SELF\'].\'?\'.CURRENTMENU.\'=\'.$_GET[CURRENTMENU]);'."\n"
	  .'    exit;'."\n"
	  .'  }'."\n"
	  .'} '."\n"
	  .'else{ // default case, print all the objects'."\n"
	  .'  include \'adminheader.php\';'."\n";

	// depending on $foreign, need to select the local objects or foreign ones
	$textfields = array(); // the fields of the database which can be diplayed as text. 
	if($foreign){
	  // need to select all the objects from the relation
	  $textfields = $relationtextfields;
	}
	else{
	  // need to select all the objects from object_$this->name
	  $textfields = $localtextfields;
	}

	// generate the SELECT statement for the generated script
	$ret .= '  $query = \'SELECT id';
	foreach($textfields as $t){
		$ret .= ', '.$t;
	}
	$ret .= ' FROM object_';
	if($foreign)
	  $ret .= $relationfield->label;
	else
	  $ret .= $this->name;
	$ret .= ' ORDER BY id ASC\';'."\n"
	  .'  $result = mysql_query($query) or die(\'Error while selecting objects list.<br />\'.$query);'."\n"
	  //generate the select list with all objects selected
	  .'  // generate the list of objects'."\n"
	  .'  echo \'<table class="form">\'."\n";'."\n"
	  .'  while($line = mysql_fetch_array($result)){'."\n"
	  .'    echo \'  <tr>\'."\n";'."\n"
	  .'    echo \'    <td>\'.$line[\'id\'].\'';
	foreach($textfields as $t){
	  $ret .= ' - \'.$line[\''.$t.'\'].\'';
	}
	$ret .= '</td>\'."\n";'."\n"
	  .'    echo \'    <td><a class="default" href="\'.$_SERVER[\'PHP_SELF\'].\'?\'.CURRENTMENU.\'=\'.$_GET[CURRENTMENU].\'&action=displayRelationObjects&selected=\'.$line[\'id\'].\'">\'.$translator->translate(\'select\').\'</a></td>\'."\n";'."\n"
	  .'    echo \'  </tr>\'."\n";'."\n"
	  .'  }'."\n"
	  .'  echo \'</table>\'."\n";'."\n"
	  .'  '."\n"
	  .'  // include the footer of the page'."\n"
	  .'  include \'adminfooter.php\';'."\n"
	  .'}'."\n"
	  .'?>';
	
	return $ret;
  }// end of htmlNMRelationGenerator
}

?>