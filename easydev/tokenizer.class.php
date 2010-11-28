<?php

require_once 'includes/constants.php';
require_once 'includes/translator.class.php';
/* class tokenizer parse the user entries and initialize a list of errors and a list of tokens.
 *
 */
class tokenizer{
	protected $errorslist;
	protected $tokenslist;
	protected $userstring;

	function __construct($userstring){
		$this->userstring = $userstring;
		$this->tokenslist = array();
		$this->errorslist = array();
	}

	public function __get($name){
		return $this->$name;
	}

	public function __set($name, $value){
		$this->$name = $value;
	}

	/*
	 *Delete all comments from the userstring
	 */
	private function deleteComments(){
		$this->userstring = preg_replace('//\*[a-zA-Z0-9_\s\{\}]*/U', $this->userstring);
	}

	/*
	 * Delete all additional white spaces (end of lines, tabulations, multi spaces).
	 */
	private function deleteAdditionalSpaces(){

		// remove all end of lines and replace them by spaces
		$this->userstring = str_replace("\r\n", ' ', $this->userstring);
		$this->userstring = str_replace("\r", ' ', $this->userstring);
		$this->userstring = str_replace("\n", ' ', $this->userstring);

		// remove all tabs and replace them by spaces
		$this->userstring = str_replace("\t", ' ', $this->userstring);

		// replace all double spaces by a single spaces (as long as there is some places with double consecutive spaces)
		$test = true;
		while($test){
			$this->userstring = str_replace('  ', ' ', $this->userstring);
			$offset = strpos($this->userstring, '  ');
			if($offset == false){
				$test = false;
			}
		}

		// finally use trim() in case there was only a space left
		$this->userstring = trim($this->userstring);
	}

	/*
	 * Isolate the special chars that are allowed for easy tokenization
	 */
	private function isolateSpecialChars(){
		$this->userstring = str_replace('{', ' { ', $this->userstring);
		$this->userstring = str_replace('}', ' } ', $this->userstring);
		$this->userstring = str_replace(';', ' ; ', $this->userstring);
		$this->userstring = str_replace(')', ' ) ', $this->userstring);
		$this->userstring = str_replace('(', ' ( ', $this->userstring);
		$this->userstring = str_replace('"', ' " ', $this->userstring);
		$this->userstring = str_replace('*', ' * ', $this->userstring);
		$this->userstring = str_replace(',', ' , ', $this->userstring);
		$this->userstring = str_replace('=', ' = ', $this->userstring);
		$this->userstring = str_replace('.', ' . ', $this->userstring);
		$this->userstring = str_replace('<', ' < ', $this->userstring);
		$this->userstring = str_replace('>', ' > ', $this->userstring);
		$this->userstring = str_replace('!', ' ! ', $this->userstring);
	}

	/*
	 * First tokenizer check : verify that the string do not contains any illegal character.
	 */
	private function verifyCharacters(){
		$regexpresult = preg_match(COMPILER_ACCEPTED_CHAR, $this->userstring);
		if($regexpresult == 0 || $regexpresult == false){
			array_push($this->errorslist, Translator::translate('compile_character_error'));
		}
	}

	/*
	 * Tokenize the string and return a list of tokens
	 */
	public function tokenize(){
		// verify the characters
		$this->verifyCharacters();
		if(count($this->errorslist) > 0){
			return array();
		}

		// isolate the special chars, that is if we have for example "string myString;}" we want to replace it by "string myString ; }" for tokenization
		$this->isolateSpecialChars();

		// delete additional whitespaces
		$this->deleteAdditionalSpaces();

		// tokenize directly with spaces, tabulations and end of line chars and fill an array
		$tempTokensList = array();
		$token = strtok($this->userstring, " \n\t");
		while ($token !== false) {
			$tempTokensList[] = $token;
			$token = strtok(" \n\t");
		}

		// delete remaining whitespaces if there is still some and fill the final tokens list
		foreach($tempTokensList as $token){
			array_push($this->tokenslist, trim($token));
		}

		// if there is no token at all, raise an error
		if(count($this->tokenslist) == 0){
			array_push($this->errorslist, Translator::translate('compile_no_token_error'));
			return array();
		}

		return $this->tokenslist;
	}
}

?>