<?php
/* This class represent a field of the database. It has a label and a type.
 *
 */

class field{
  var $label; // the label of the database field
  var $type; // the type of the database field
  var $options; // the options of the database field. this array is of type : Array(option1 => value1, option2 => value2, ...)

  function __construct($label, $type, $options=null){
	$this->label = $label;
	$this->type = $type;
	if($options != null){
	  $this->options = $options;
	}
  }

  /* Getter for the differents attributes.
   * @param name : the name of the attribute to get.
   */
  public function __get($name){
	return $this->$name;
  }

  /* Setter for the differents attributes.
   * @param name : the name of the attribute to set.
   * @param value : the new value of the attribute.
   */
  public function __set($name, $value){
	$this->$name = $value;
  }

  /* Print the field in html format.
   */
  public function htmlPrint(){
	switch($this->type){
	case 'relation1N':
	  echo '<br />&nbsp;&nbsp;&nbsp;'.$this->type.' id_'.$this->label;
	  break;
	default:
	  echo '<br />&nbsp;&nbsp;&nbsp;'.$this->type.' '.$this->label;
	  break;
	}
  }
}
?>