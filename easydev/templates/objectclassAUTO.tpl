<?php
/*********************************************************************************
 * Autogenerated class
 * EasyDev v 0.x copyright Patrick Mingard 2007
 * Any modification of this code can alter the behaviour of EasyDev v 0.x console
 ********************************************************************************/

require_once 'includes/connection.php';
<% foreach($this->fieldlist as $field){
  if($field->type == 'relation1N' || $field->type == 'relationNM'){
    %>require_once 'object_<% echo $field->label; %>.class.php';
<%
  }
}%>

class <% echo $this->name; %>{
  // variables of the class
  var $id;<%
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
%>
  var <% echo '$'.$field->label; %>;<%
    break;
  case 'relation1N':
%>
  var $relation1N<% echo $field->options['relationname']; %>;<%
    break;
  case 'relationNM':
%>
  var $relationNM<% echo $field->options['relationname']; %>;<%
	break;
  }
}
%>
  /* 
   * Public constructor of the class. The id is set to zero because the object still do not exist in database.
   * The finders can call a private function defined here after to initialize the id to a different value (private function so the user cannot access it, only finders from this class).
   */
  function __construct(<% 
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo ', $'.$field->label;
    }
    else{
      echo '$'.$field->label;
    }
    $first = false;
    break;
  case 'relation1N':
  case 'relationNM':
    break;
  } 
}%>){
    $this->id = 0;<% 
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
%>
    $this-><% echo $field->label; %> = $<% echo $field->label; %>;<%
    break;
  case 'relation1N':
%>
    $this->relation1N<% echo $field->options['relationname']; %> = null;<%
	break;
  case 'relationNM':
%>
    $this->relationNM<% echo $field->options['relationname']; %> = array();<%
    break;
  }
}%>
  }

  /* Getter for the different attributes
   * @param string $name the name of the attribute to get.
   */
  public function __get($name){
    return $this->$name;
  }

  /* Setter for the different attributes
   * @param string $name the name of the attribute to set.
   * @param mixed $value the value to associate to the attribute.
   */
  public function __set($name, $value){
    if($name == 'id'){
      die('EasyDev FATAL ERROR : trying to set manually a primary id.');<%
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){%>
    if($name == 'relation1N<% echo $field->options['relationname']; %>')
      die('EasyDev FATAL ERROR : trying to set manually a relation. Please use auto-generated functions instead.');<%
  }
  if($field->type == 'relationNM'){%>
    if($name == 'relationNM<% echo $field->options['relationname']; %>')
      die('EasyDev FATAL ERROR : trying to set manually a relation. Please use auto-generated functions instead.');<%
  }
}%>
    }
    $this->$name = $value;
  }

  /* Special setter for the id for finder initialization.
   * @param integer $id the value of the id to set to the object.
   */
  private function setId($id){
    $this->id = $id;
  }
<% 
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'relation1N':%>
  /* Create a relation 1:N with an external object as an external key.
   * NOTE : This private function is used only by finders (that do not have a pointer on the external object) to initialize the object.
   * @param integer $foreignobjectid the foreign object identifier.
   */
  private function set<% echo $field->options['relationname']; %>id($foreignobjectid){
    if($foreignobjectid){
      $this->relation1N<% echo $field->options['relationname']; %> = $foreignobjectid;
    }
  }
<%  break;
  case 'relationNM':%>
  /* Initialization of a N:M relation by taking as parameter an array containing all the id's of the linked objects.
   * NOTE : This private function is used only by finders (that do not have pointers on the external objects).
   * @param array $foreignobjectidlist the array of all linked objects.
   */
  private function set<% echo $field->options['relationname']; %>ids($foreignobjectidlist){
    if(is_array($foreignobjectidlist)){
	  $this->relationNM<% echo $field->options['relationname']; %> = $foreignobjectidlist;
    }
  }
<%
	break;
  }
}%>
<% 
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'relation1N':%>
  /* Create a relation 1:N with an external object as an external key.
   * NOTE : This public function has to be called by user application.
   * @param <% echo $field->label; %> $foreignobject the foreign object.
   * @return boolean true if the foreign key was properly updated, false otherwise.
   */
  public function setrelation<% echo $field->options['relationname']; %>(<% echo $field->label; %> $foreignobject){
    if($foreignobject && $foreignobject->id != 0){
      $this->relation1N<% echo $field->options['relationname']; %> = $foreignobject->id;
	  return true;
    }
	else{
	  return false;
	}
  }
<%  break;
  case 'relationNM':%>
  /* Let the user add an external object to the relation N:M.
   * NOTE : This function do not call the symetrical one in foreign object. The user has to call it himself.
   * @param <% echo $field->label; %> $foreignobject the foreign object to add in the relation.
   * @param boolean true if the object id was added to the list, false otherwise.
   */
  public function addrelation<% echo $field->options['relationname']; %>(<% echo $field->label; %> $foreignobject){
	if($foreignobject->id != 0 && ! in_array($foreignobject->id, $this->relationNM<% echo $field->options['relationname']; %>)){
	  array_push($this->relationNM<% echo $field->options['relationname']; %>, $foreignobject->id);
	  return true;
	}
	else{
	  return false;
	}
  }

  /* Let the user remove an external object from the relation N:M.
   * NOTE : This function do not call the symetrical one in foreign object. The user has to call it himself.
   * @param <% echo $field->label; %> $foreignobject the foreign object to remove from the relation.
   * @return boolean true if the link has been properly removed, false otherwise.
   */
  public function removerelation<% echo $field->options['relationname']; %>id(<% echo $field->label; %> $foreignobject){
	if($foreignobject->id == 0){
	  return false;
	}
	$arraykey = array_search($foreignobject->id, $this->relationNM<% echo $field->options['relationname']; %>);
	if($arraykey !== false){
	  array_splice($this->relationNM<% echo $field->options['relationname']; %>, $arraykey, 1);
	  return true;
	}
	else{
	  return false;
	}
  }
<%
	break;
  }
}%>

  /* Persist the object in the database. 
   * The object is inserted if the primary id is equal to zero (zero init is done by the constructor), 
   * or updated if the primary id is not equal to zero (non-zero init is done by the finders).
   */
  public function store(){
<% 
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){
%>    if($this->relation1N<% echo $field->options['relationname']; %> == null){
     die('Error - Trying to store an object before setting a relation 1:N');
    }<%
  }
}
%>
    $query = '';
    if($this->id == '0'){
      $query = 'INSERT INTO object_<% echo $this->name; %> (<%
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo ', ';
    }
    echo $field->label;
    $first = false;
    break;
  case 'relation1N':
    if(!first){
      echo ', ';
    }
    echo '1n_rel_'.$field->options['relationname'];
    $first = false;
    break;
  }
}
%>) VALUES ("<%
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo '", "';
    }
    echo '\'.$this->'.$field->label.'.\'';
    $first = false;
    break;
  case 'relation1N':
    if(!$first){
      echo '", "';
    }
    echo '\'.$this->relation1N'.$field->options['relationname'].'.\'';
    $first = false;
  }
}
%>")';
    }
    else{
      $query = 'UPDATE object_<% echo $this->name; %> SET <%
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo ', ';
    }
    echo $field->label.'="\'.$this->'.$field->label.'.\'"';
    $first = false;
    break;
  case 'relation1N':
    if(!$first){
      echo ', ';
    }
    echo '1n_rel_'.$field->options['relationname'].'="\'.$this->relation1N'.$field->options['relationname'].'.\'"';
    $first = false;
    break;
  }
}

%> WHERE id="'.$this->id.'"';
    }
    mysql_query($query) or die('Error while updating objects.<br />'.$query);

    // if the object was inserted and not updated, need to retrieve its primary id
    if($this->id == '0'){
      $query = 'SELECT LAST_INSERT_ID()';
      $result = mysql_query($query);
      $line = mysql_fetch_array($result);
      $this->id = $line[0];
    }

    // if the object has some relationNM, need to store the collection of id's which are part of the relation
<%
foreach($this->fieldlist as $field){
  if($field->type == 'relationNM'){
    if(isset($field->options['secondobject']) && $field->options['secondobject']){
%>    $query = 'DELETE FROM object_<% echo $field->label; %>_<% echo $this->name; %>_<% echo $field->options['relationname']; %>_nmrelation '.
             'WHERE id_<% echo $this->name; %>="'.$this->id.'"';
    mysql_query($query) or die('Error while deleting relations N:M in second object.<br />'.$query);

    foreach($this->relationNM<% echo $field->options['relationname']; %> as $newlink){
      $query = 'INSERT INTO object_<% echo $field->label; %>_<% echo $this->name; %>_<% echo $field->options['relationname']; %>_nmrelation '.
               '(id_<% echo $field->label; %>, id_<% echo $this->name; %>) '.
               'VALUES ("'.$newlink.'", "'.$this->id.'")';
      mysql_query($query) or die('Error while inserting relations N:M in second object.<br />'.$query);
    }
<%  }
    else{
%>    $query = 'DELETE FROM object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '.
             'WHERE id_<% echo $this->name; %>="'.$this->id.'"';
    mysql_query($query) or die('Error while deleting relations N:M in first object.<br />'.$query);

    foreach($this->relationNM<% echo $field->options['relationname']; %> as $newlink){
      $query = 'INSERT INTO object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '.
               '(id_<% echo $this->name; %>, id_<% echo $field->label; %>) '.
               'VALUES ("'.$this->id.'", "'.$newlink.'")';
      mysql_query($query) or die('Error while inserting relations N:M in first object.<br />'.$query);
    }
<%  }
  }
}
%>
  }

  /* Let the user delete any object of the database.
   */
  public function remove(){
    $query = 'DELETE FROM object_<% echo $this->name; %> WHERE id="'.$this->id.'"';
    mysql_query($query) or die('Error while removing an object.<br />'.$query);
    $this->id = 0; // the object is removed from database but volatile object is not destroyed. if the user call store() afterwards, it will make new insert
  }


  /* General finder. Returns all the objects of the database.
   */
  public static function find(){
    $query = 'SELECT * FROM object_<% echo $this->name; %>';
    $result = mysql_query($query) or die('Error while loading objects.<br />'.$query);
    $objectlist = array();
    while($line = mysql_fetch_array($result)){
      // create the object with the database values
      $object = new <% echo $this->name; %>(<% 
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\']';
    $first = false;
    break;
  } 
}
%>);
      // set the relations<%
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){%>
      $object->set<% echo $field->options['relationname']; %>id($line['1n_rel_<% echo $field->options['relationname']; %>']);<%
  }
  if($field->type == 'relationNM'){
    if(isset($field->options['secondobject']) && $field->options['secondobject']){%>
      $query = 'SELECT id_<% echo $field->label; %> FROM object_<% echo $field->label; %>_<% echo $this->name; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $this->name; %>="'.$line['id'].'"';<%
    }
    else{%>
      $query = 'SELECT id_<% echo $this->name; %> FROM object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $field->label; %>="'.$line['id'].'"';<%
    }%>
      $result = mysql_query($query) or die('Error while selecting N:M relations.<br />'.$query);

      $relationNMidlist = array();
      while($row = mysql_fetch_array($result)){
        $relationNMidlist[] = $row[0];
      }
      $object->set<% echo $field->options['relationname']; %>ids($relationNMidlist);<%
  }
}%>
      $object->setId($line['id']);
      array_push($objectlist, $object);
    }

    return $objectlist;
  }

  /* Finder for the primary id
   * @param integer id the identifier of the object to return
   */
  public static function findByPrimaryId($id){
    $query = 'SELECT * FROM object_<% echo $this->name; %> WHERE id="'.$id.'"';
    $result = mysql_query($query) or die('Error while selecting objects.<br />'.$query);
    $line = mysql_fetch_array($result);
    $object = new <% echo $this->name; %>(<%
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\']';
    $first = false;
    break;
  } 
}
%>);
    // set the relations<%
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){%>
    $object->set<% echo $field->options['relationname']; %>id($line['1n_rel_<% echo $field->options['relationname']; %>']);<%
  }
  if($field->type == 'relationNM'){
    if(isset($field->options['secondobject']) && $field->options['secondobject']){%>
    $query = 'SELECT id_<% echo $field->label; %> FROM object_<% echo $field->label; %>_<% echo $this->name; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $this->name; %>="'.$line['id'].'"';<%
    }
    else{%>
    $query = 'SELECT id_<% echo $this->name; %> FROM object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '
            .'WHERE id_<% echo $field->label; %>="'.$line['id'].'"';<%
    }%>
    $result = mysql_query($query) or die('Error while selecting N:M relations.<br />'.$query);

    $relationNMidlist = array();
    while($row = mysql_fetch_array($result)){
      $relationNMidlist[] = $row[0];
    }
    $object->set<% echo $field->options['relationname']; %>ids($relationNMidlist);<%
  }
}%>
    $object->setId($line['id']);
    return $object;
  }

<% foreach($this->fieldlist as $field){
  if($field->type == 'finder'){
%>  /* User defined finder.
   */
  public static function finder<% echo $field->label; %>(<% 
$first = true;
foreach($field->options['finderparameters'] as $param){
  if(!$first){
    echo ', $'.$param;
  }
  else{
    $first = false;
    echo '$'.$param;
  }
}
%>){<%
$userquery = $field->options['finderquery'];
foreach($field->options['finderparameters'] as $param){
  $userquery = str_replace($param, '"\'.$'.$param.'.\'"', $userquery);
}
%>
    $query = '<% echo $userquery; %>';
    $result = mysql_query($query) or die('Error while selecting objects.<br />'.$query);

    $objectlist = array();
    while($line = mysql_fetch_array($result)){
      // create the object with the database values
      $object = new <% echo $this->name; %>(<% 
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\']';
    $first = false;
    break;
  } 
}
%>);
      // set the relations<%
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){%>
      $object->set<% echo $field->options['relationname']; %>id($line['1n_rel_<% echo $field->options['relationname']; %>']);<%
  }
  if($field->type == 'relationNM'){
    if(isset($field->options['secondobject']) && $field->options['secondobject']){%>
      $query = 'SELECT id_<% echo $field->label; %> FROM object_<% echo $field->label; %>_<% echo $this->name; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $this->name; %>="'.$line['id'].'"';<%
    }
    else{%>
      $query = 'SELECT id_<% echo $this->name; %> FROM object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $field->label; %>="'.$line['id'].'"';<%
    }%>
      $result = mysql_query($query) or die('Error while selecting N:M relations.<br />'.$query);

      $relationNMidlist = array();
      while($row = mysql_fetch_array($result)){
        $relationNMidlist[] = $row[0];
      }
      $object->set<% echo $field->options['relationname']; %>ids($relationNMidlist);<%
  }
}%>
      $object->setId($line['id']);
      array_push($objectlist, $object);
    }

    return $objectlist;
  }
<%
  }
}
%>

<% foreach($this->fieldlist as $field){
  if($field->type == 'updater'){
%>  /* User defined finder.
   */
  public static function updater<% echo $field->label; %>(<% 
$first = true;
foreach($field->options['finderparameters'] as $param){
  if(!$first){
    echo ', $'.$param;
  }
  else{
    $first = false;
    echo '$'.$param;
  }
}
%>){<%
$userquery = $field->options['finderquery'];
foreach($field->options['finderparameters'] as $param){
  $userquery = str_replace($param, '"\'.$'.$param.'.\'"', $userquery);
}
%>
    $query = '<% echo $userquery; %>';
    $result = mysql_query($query) or die('Error while updating objects.<br />'.$query);
  }
<%
  }
}
%>



}
?>
