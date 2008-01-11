<?php

require_once 'includes/constants.php';
require_once 'includes/dbobject.class.php';
require_once 'includes/field.class.php';
require_once 'includes/translator.class.php';

/*
 * Parser for the compiler. Makes all the verifications on the grammar of the language. Also construct the field objects and dbobjects.
 */

class parser{
  var $errorslist;
  var $userstring; // the compiled code
  var $tokenslist; // token list back from tokenizer
  var $currenttoken; // position of the current token in token list
  var $dbobjectlist; // list of the generated dbobjects
  var $typelist = array('string', 'integer', 'bool', 'double'); // constant array defining the different types of fields
  var $finderlist = array('updater', 'finder'); // constant array defining the different types of function that user can define
  var $relationlist = array('relation1N', 'relationNM'); // constant array defining the different types of relations
  var $specialcharlist = array('{', '}', ';', '"', '(', ')', ','); // constant array defining the special chars accepted by the compiler
  var $otherreservedtokens = array('class', '*'); // constant array defining some other reserved tokens. here "*" is for EOF
  var $translator; // translator 
  var $pendingrelationNMverifications; // relationNM needs to be defined in both objects. this variable contains all relations that have already been encountered but the object was not already compiled. at the end of the compilation before returning the dbobject list, verify the these pending verifications are satisfied.

  function __construct($tokenslist, $userstring){
	$this->tokenslist = $tokenslist;
	$this->userstring = $userstring;
	$this->currenttoken = 0;
	$this->errorslist = array();
	$this->dbobjectlist = array();
	$this->translator = new translator();
	$this->pendingrelationNMverifications = array();
  }

  /*
   * Verify that the current token is a type.
   * @return bool True if the current token is a type identifier, false otherwise.
   */
  private function isTypeToken(){
	if(in_array($this->tokenslist[$this->currenttoken], $this->typelist)){
	  return true;
	}
	else{
	  return false;
	}
  }

  /*
   * Verify that the current token is a sql function definition.
   * @return bool True if the current token is a finder or updater, false otherwise.
   */
  private function isFinderToken(){
	if(in_array($this->tokenslist[$this->currenttoken], $this->finderlist)){
	  return true;
	}
	else{
	  return false;
	}
  }

  /*
   * Verify that the current token is a special token.
   * @return bool True if the current token is a special token, false otherwise.
   */
  private function isSpecialToken($expected){
	if($this->tokenslist[$this->currenttoken] == $expected){
	  return true;
	}
	else{
	  array_push($this->errorslist, $this->translator->translate('compile_token_mismatch_error').'<br />'
				 .$this->translator->translate('expected').' : '.$expected.'<br />'
				 .$this->translator->translate('found').' : '.$this->tokenslist[$this->currenttoken]);
	  return false;
	}
  }

  /*
   * Verify that the current token is an identifier.
   * @return bool True if the current token is an identifier, false otherwise.
   */
  private function isTokenIdentifier(){
	// check if the current token is a reserved token
	if(in_array($this->tokenslist[$this->currenttoken], $this->typelist)
	   || in_array($this->tokenlist[$this->currenttoken], $this->specialcharlist)
	   || in_array($this->tokenslist[$this->currenttoken], $this->otherreservedtokens)){
	  array_push($this->errorslist, $this->translator->translate('compile_token_mismatch_error').'<br />'
				 .$this->translator->translate('compile_expected_identifier').'<br />'
				 .$this->translator->translate('found').' : '.$this->tokenslist[$this->currenttoken]);
	  return false;
	}
	// test if the identifier contains only letters and numbers and start with a letter
	$regexpresult = preg_match(CLASSNAME_REGEXP, $this->tokenslist[$this->currenttoken]);
	if($regexpresult == 0 || $regexpresult == false){
	  array_push($this->errorslist, $this->translator->translate('compile_identifier_error').$this->tokenslist[$this->currenttoken]);
	  return false;
	}
	
	return true;
  }

  /*
   * Verify that the current token is defining a relation with other objects.
   * @return bool True if the current token is defining a relation with other objects, false otherwise.
   */
  private function isTokenRelation(){
	// check if the current token is in the relation list
	if(in_array($this->tokenslist[$this->currenttoken], $this->relationlist)){
	  return true;
	}
	else{
	  return false;
	}
  }

  /*
   * Parse the tokens one by one and create the dbobjects for each class definition.
   * @return array The array containing all the dbobjects compiled.
   */
  public function parse(){
	// add an EOF identifier at the end of the list
	array_push($this->tokenslist, '*');
	
	// first verify there is at least one class definition
	if($this->tokenslist[$this->currenttoken] != 'class'){
	  array_push($this->errorslist, $this->translator->translate('compile_no_class_def_found_error'));
	  return array();
	}
	else{
	  // for all classes
	  while($this->tokenslist[$this->currenttoken] == 'class'){
		// initialize an array to push all the fields
		$fieldslist = array();
		$relationslist = array();

		// accept the class reserved word
		$this->currenttoken++;

		$classname = '';
		// accept an identifier as classname
		if($this->isTokenIdentifier()){
		  $classname = $this->tokenslist[$this->currenttoken];
		  $this->currenttoken++;
		}
		else{
		  return array();
		}

		// accept a left brace
		if($this->isSpecialToken('{')){
		  $this->currenttoken++;
		}
		else{
		  return array();
		}
		// -------- start of relations parsing -----------------------------------

		if($this->isTokenRelation()){
		  while($this->isTokenRelation()){
			$relationtype = '';
			$relationobject = '';

			// accept the relation token
			$relationtype = $this->tokenslist[$this->currenttoken];
			$this->currenttoken++;

			// accept the identifier of the object for the relation
			if($this->isTokenIdentifier()){
			  $relationobject = $this->tokenslist[$this->currenttoken];
			  $this->currenttoken++;
			}
			else{
			  return array();
			}

			// accept the identifier of the name of the relation
			if($this->isTokenIdentifier()){
			  $relationname = $this->tokenslist[$this->currenttoken];
			  $this->currenttoken++;
			}
			else{
			  return array();
			}

			// accept the semicolumn that finish the field
			if($this->isSpecialToken(';')){
			  $this->currenttoken++;
			}
			else{
			  return array();
			}
			
			// verify if there is duplicate relations with same object for this object
			// if it is the case, verify that the two relations have different names
			foreach($relationslist as $relation){
			  if($relationobject == $relation->label){
				if($relationname == $relation->options['relationname']){
				  array_push($this->errorslist, $this->translator->translate('compile_duplicate_relation_error'));
				  return array();
				}
			  }
			}
			
			// verify that the relation is not with itself
			if($relationobject == $classname){
			  array_push($this->errorslist, $this->translator->translate('compile_self_relation_error'));
			  return array();
			}

			
			// verify that the relation is with an existing object, that the relation also exists in the foreign object and that it has the same name
			$objectfound = false;
			$recursiverelationfound = false;
			foreach($this->dbobjectlist as $dbobject){
			  if($relationobject == $dbobject->name){
				$objectfound = true; // we found the right object
				
				if($relationtype == 'relation1N'){
				  $recursiverelationfound = true; // relation1N do not need to be recursively defined, so consider it as found
				}
				
				foreach($dbobject->fieldlist as $field){
				  if($field->type == 'relationNM' && $field->label == $classname && $field->options['relationname'] == $relationname){
					$recursiverelationfound = true; // we found the recursive relation
				  }
				}
			  }
			}
			if(! $objectfound){ // if the object was not found
			  if($relationtype == 'relationNM'){
				// if the object is not found and relation N:M, add it the the pendingrelationNMverification list
				array_push($this->pendingrelationNMverifications, array($classname, $relationobject, $relationname));
			  }
			  else{
				// nothing to verify for relation1N
			  }
			}
			else if(! $recursiverelationfound){ // if the object was found, but no recursive definition for the relation
			  array_push($this->errorslist, $this->translator->translate('compile_relationnm_not_recursive_error').$relationname);
			  return array();
			}
			
			// construct the $options list of the Field object
			$fieldoptions = array();
			$fieldoptions['relationname'] = $relationname;
			if($recursiverelationfound){
			  $fieldoptions['secondobject'] = true;
			}
			
			// if everything was fine, construct the field object representing the relation
			$fieldobject = new field($relationobject, $relationtype, $fieldoptions);
			array_push($relationslist, $fieldobject);
		  }
		}
		// -------- end of relations parsing -------------------------------------

		// -------- start of fields parsing --------------------------------------

		// verify there is at least one field object
		if($this->isTypeToken()){
		  while($this->isTypeToken()){
			// initialize two variables for name and type of the field
			$fieldname = '';
			$fieldtype = '';

			// accept the type token
			$fieldtype = $this->tokenslist[$this->currenttoken];
			$this->currenttoken++;

			// accept the identifier of the field
			if($this->isTokenIdentifier()){
			  $fieldname = $this->tokenslist[$this->currenttoken];
			  $this->currenttoken++;
			}
			else
			  return array();

			// accept the semicolumn that finish the field
			if($this->isSpecialToken(';'))
			  $this->currenttoken++;
			else
			  return array();

			// verify if there is a duplicate field for this object
			foreach($fieldslist as $field){
			  if($fieldname == $field->label){
				array_push($this->errorslist, $this->translator->translate('compile_duplicate_field_name_error').$fieldname);
				return array();
			  }
			}

			// if everything was fine, construct the field object and add it to the fieldlist of this object
			$fieldobject = new field($fieldname, $fieldtype);
			array_push($fieldslist, $fieldobject);
		  }
		}
		else{
		  array_push($this->errorslist, $this->translator->translate('compile_no_field_def_found_error').$classname);
		  return array();
		}

		// --------- end of fields parsing -----------------------------------
		
		// --------- beginning of finders / updaters parsing -----------------
		
		while($this->isFinderToken()){
		  // accept the finder token
		  $findertype = $this->tokenslist[$this->currenttoken];
		  $this->currenttoken++;

		  // accept the identifier which represent the name of the finder
		  if($this->isTokenIdentifier()){
			$findername = $this->tokenslist[$this->currenttoken];
			$this->currenttoken++;
		  }
		  else
			return array();

		  // accept the left parenthesis which represent the start of the different parameters of the function
		  if($this->isSpecialToken('('))
			$this->currenttoken++;
		  else
			return array();

		  $parameters = array();
		  // if there is an identifier, accept it, it is the first parameter of the function, then accept all other parameters if we encounter a comma
		  if($this->tokenslist[$this->currenttoken] != ')'){
			if($this->isTokenIdentifier()){
			  array_push($parameters, $this->tokenslist[$this->currenttoken]);
			  $this->currenttoken++;
			}
			else
			  return array();

			// accept all other parameters
			while($this->tokenslist[$this->currenttoken] == ','){
			  $this->currenttoken++;
			  
			  if($this->isTokenIdentifier()){
				array_push($parameters, $this->tokenslist[$this->currenttoken]);
				$this->currenttoken++;
			  }
			  else
				return array();
			} // end of while(next char is a comma)
		  }// end of if(there is at least one parameter for the function

		  // accept the right parenthesis which
		  if($this->isSpecialToken(')'))
			$this->currenttoken++;
		  else
			return array();

		  // accept the left brace
		  if($this->isSpecialToken('{'))
			$this->currenttoken++;
		  else
			return array();

		  // accept the opening double quote
		  if($this->isSpecialToken('"'))
			$this->currenttoken++;
		  else
			return array();

		  // take everything before the first special token, and stack it into $query
		  $query = '';
		  while($this->tokenslist[$this->currenttoken] != '"'){
			$query .= $this->tokenslist[$this->currenttoken].' ';
			$this->currenttoken++;
		  }

		  // accept the closing double quote
		  if($this->isSpecialToken('"'))
			$this->currenttoken++;
		  else
			return array();
		  
		  // accept the semi column for closing the SQL query
		  if($this->isSpecialToken(';'))
			$this->currenttoken++;
		  else
			return array();

		  // accept the right brace to finish the finder definition
		  if($this->isSpecialToken('}'))
			$this->currenttoken++;
		  else
			return array();

		  // verify that there is not duplicate sql functions with same name and same type
		  foreach($fieldslist as $field){
			if($field->type == $findertype && $field->label == $findername){
			  array_push($this->errorslist, $this->translator->translate('compile_duplicate_sql_function_error').$findername);
			  return array();
			}
		  }

		  // construct the field object
		  $fieldoptions = array();
		  $fieldoptions['finderquery'] = $query;
		  $fieldoptions['finderparameters'] = $parameters;
		  $fieldobject = new field($findername, $findertype, $fieldoptions);
		  array_push($fieldslist, $fieldobject);
		} // end of while($this->isFinderToken())

		// --------- end of finders / updaters parsing -----------------------

		// if there is no more type token to start a field, let's conclude by accepting a right brace
		if($this->isSpecialToken('}')){
		  $this->currenttoken++;
		}
		else{
		  return array();
		}

		// verify if there is a duplicate name for classnames
		foreach($this->dbobjectlist as $dbobject){
		  if($classname == $dbobject->name){
			array_push($this->errorslist, $this->translator->translate('compile_duplicate_object_error'));
			return array();
		  }
		}

		// if everything was fine, construct the object and add it to the objectlist of this class
		$totalfieldslist = array_merge($relationslist, $fieldslist);
		$dbobject = new dbobject($classname, $totalfieldslist, $this->userstring, $this->translator);
		array_push($this->dbobjectlist, $dbobject);
	  }// while(token is equal to 'class')

	  // verify that no tokens are left (verify that the token left is '*', the added token at beginning of this function
	  if($this->tokenslist[$this->currenttoken] != '*'){
		array_push($this->errorslist, $this->translator->translate('compile_unexpected_token_error').$this->tokenslist[$this->currenttoken]);
		return array();
	  }
	} // end of else{} (there is at least one class definition)

	// before returning the object list, verify the pending verifications
	foreach($this->pendingrelationNMverifications as $verif){
	  // restore back the informations about the verifications that could not be done
	  $class = $verif[0];
	  $relobject = $verif[1];
	  $relname = $verif[2];

	  // find the object in the object list
	  $verification = false;

	  foreach($this->dbobjectlist as $dbobject){
		foreach($dbobject->fieldlist as $field){
		  if($field->label == $class && $field->options['relationname'] == $relname && $dbobjet->name != $class){
			$verification = true;
		  }
		}
	  }

	  // print an error if the verification failed
	  if(! $verification){
		array_push($this->errorslist, $this->translator->translate('compile_relationnm_not_recursive_error').$relname);
		return array();
	  }
	}
	  

	return $this->dbobjectlist;
  }
}