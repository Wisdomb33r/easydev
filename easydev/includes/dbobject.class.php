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
		$ret .= $field->label.' varchar(255) collate latin1_german1_ci NULL,'."\n";
		break;
	  case 'bool':
		$ret .= $field->label.' tinyint(1) NULL,'."\n";
		break;
	  case 'integer':
		$ret .= $field->label.' int(20) NULL,'."\n";
		break;
	  case 'double':
		$ret .= $field->label.' double NULL,'."\n";
		break;
	  case 'text':
		$ret .= $field->label.' text collate latin1_german1_ci NULL,'."\n";
		break;
	  case 'image':
		$ret .= $field->label.' longblob NULL,'."\n";
		$ret .= $field->label.'_type tinyint(1) NULL,'."\n";
		break;
	  case 'date':
		$ret .= $field->label.' date NULL,'."\n";
		break;
	  case 'datetime':
		$ret .= $field->label.' datetime NULL,'."\n";
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
			' REFERENCES object_'.$field->label.' (id)'.
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
  public function htmlNMRelationGenerator($mainmenuid, $relationfield, $foreignobject, $foreign=false){
	$predefinedvariables = array();
	$predefinedvariables['mainmenuid'] = $mainmenuid;
	$predefinedvariables['relationfield'] = $relationfield;
	$predefinedvariables['foreignobject'] = $foreignobject;
	$predefinedvariables['foreign'] = $foreign;
	$script = $this->useTemplate('objectNMrel.tpl', $predefinedvariables);
	
	return $script;
  }

  /* Return the html/php code for the script that print an image from database.
   * @param string $fieldname The name of the field that contains the image.
   */
  public function imagescriptgenerator($fieldname){
	$predefinedvariables = array();
	$predefinedvariables['fieldname'] = $fieldname;
	$script = $this->useTemplate('objectimage.tpl', $predefinedvariables);

	return $script;
  }
}

?>