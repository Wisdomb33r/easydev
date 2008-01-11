<?php

/* Translator
*/
class translator{
  var $languageTag;
  
  public function __construct(){
    require_once('constants.php');
    require_once('dbconstants.php');
    require_once('connection.php');

    $this->languageTag = '';
    if(isset($_SESSION[SESSION_LANGUAGE])){
      $this->languageTag = $_SESSION[SESSION_LANGUAGE];
    }
    else{
	  $query = 'SELECT value FROM '.CONFIGURATION.' WHERE id="default_language"';
	  $result = mysql_query($query) or die('Error while selecting default language from configuration.<br />'.$query);
	  if($line = mysql_fetch_array($result)){ // if there is a config line in the database, take this one
		$this->languageTag = $line['value'];
		$_SESSION[SESSION_LANGUAGE] = $this->languageTag;
	  }
	  else{ // take the default configuration defined in constants.php
		$this->languageTag = DEFAULT_LANGUAGE_TAG;
		$_SESSION[SESSION_LANGUAGE] = $this->languageTag;
	  }
    }
  }
  
  public function translate($param){
    $query = 'SELECT '.$this->languageTag.' FROM '.TRANSLATION_STRINGS.' WHERE keyword="'.$param.'"';
    $result = mysql_query($query) or die('Translator fetch error.<br />'.$query);
    
    // If there is a result (there can be only one because keyword in TABLE_LANGUAGE_STRING is unique), we return this result.
    if($line = mysql_fetch_array($result)){
      return $line[$this->languageTag];
    }
    else{ // Else we return the keyword
      return $param;
    }
  }
}

?>