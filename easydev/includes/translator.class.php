<?php

require_once('constants.php');
require_once('dbconstants.php');
require_once('connection.php');

/* Translator
 */
class Translator{
	public static function translate($identifier){
		$language = null;
		if(isset($_SESSION[SESSION_LANGUAGE])) $language = $_SESSION[SESSION_LANGUAGE];
		else{
			$query = 'SELECT value FROM '.CONFIGURATION.' WHERE id="default_language"';
			$result = mysql_query($query) or die('Error while selecting default language from configuration.');
			if($row = mysql_fetch_array($result)){ // if there is a config line in the database, take this one
				$language = $row['value'];
				$_SESSION[SESSION_LANGUAGE] = $row['value'];
			}
			else{ // take the default configuration defined in constants.php
				$language = DEFAULT_LANGUAGE_TAG;
				$_SESSION[SESSION_LANGUAGE] = DEFAULT_LANGUAGE_TAG;
			}
		}
		$query = 'SELECT '.$language.' FROM '.TRANSLATION_STRINGS.' WHERE keyword="'.addslashes($identifier).'"';
		$result = mysql_query($query) or die('Translator fetch error.');

		// If there is a result (there can be only one because keyword in TABLE_LANGUAGE_STRING is unique), we return this result.
		if($row = mysql_fetch_array($result))return $row[$language];
		else return $identifier; // Else we return the keyword
	}

	public static function languageList(){
		$query = 'DESCRIBE '.TRANSLATION_STRINGS;
		$result = mysql_query($query) or die('Error while fetching language list.');

		$list = array();
		while($row = mysql_fetch_array($result)){
			if($row['Field'] != 'keyword') $list[] = $row['Field'];
		}

		return $list;
	}
}

?>