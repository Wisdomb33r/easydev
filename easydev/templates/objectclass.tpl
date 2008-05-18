<?php
/*********************************************************************************
 * Autogenerated class
 * EasyDev v 0.x copyright Patrick Mingard 2007
 * Any modification of this code can alter the behaviour of EasyDev v 0.x console
 ********************************************************************************/

require_once 'includes/connection.php';
require_once 'includes/constants.php';
require_once 'includes/translator.class.php';
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
  case 'date':
  case 'text':
  case 'datetime':
%>
  var <% echo '$'.$field->label; %>;<%
    break;
  case 'image':
%>
  var <% echo '$'.$field->label; %>;
  var <% echo '$'.$field->label; %>_type;<%
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
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo ', ';
    }
    echo '$'.$field->label;
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo ', ';
    }
    echo '$'.$field->label.', $'.$field->label.'_type';
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
  case 'date':
  case 'text':
  case 'datetime':
%>
    $this-><% echo $field->label; %> = $<% echo $field->label; %>;<%
    break;
  case 'image':
%>
    $this-><% echo $field->label; %> = $<% echo $field->label; %>;
    $this-><% echo $field->label; %>_type = $<% echo $field->label; %>_type;<%
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
  public function removerelation<% echo $field->options['relationname']; %>(<% echo $field->label; %> $foreignobject){
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
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo ', ';
    }
    echo $field->label;
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo ', ';
    }
    echo $field->label.', '.$field->label.'_type';
    $first = false;
    break;
  case 'relation1N':
    if(!$first){
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
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo '", "';
    }
    echo '\'.addslashes($this->'.$field->label.').\'';
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo '", "';
    }
    echo '\'.addslashes($this->'.$field->label.').\'", "\'.$this->'.$field->label.'_type.\'';
    $first = false;
    break;
  case 'relation1N':
    if(!$first){
      echo '", "';
    }
    echo '\'.$this->relation1N'.$field->options['relationname'].'.\'';
    $first = false;
    break;
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
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo ', ';
    }
    echo $field->label.'="\'.addslashes($this->'.$field->label.').\'"';
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo ', ';
    }
    echo $field->label.'="\'.addslashes($this->'.$field->label.').\'", '.$field->label.'_type="\'.$this->'.$field->label.'_type.\'"';
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


  /* Count the number of elements in the table of the object.
   */
  public static function count(){
    $query = 'SELECT COUNT(*) FROM object_<% echo $this->name; %>';
    $result = mysql_query($query) or die('Error while counting objects.<br />'.$query);
    $line = mysql_fetch_array($result);
    return $line[0];
  }

  /* General finder. Returns all the objects of the database.
   */
  public static function find($lim = 0){
    $query = 'SELECT * FROM object_<% echo $this->name; %> ORDER BY id DESC LIMIT '.$lim.', 20';
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
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\']';
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\'], $line[\''.$field->label.'_type\']';
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
      $query = 'SELECT id_<% echo $field->label; %> FROM object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $this->name; %>="'.$line['id'].'"';<%
    }%>
      $result2 = mysql_query($query) or die('Error while selecting N:M relations.<br />'.$query);

      $relationNMidlist = array();
      while($row = mysql_fetch_array($result2)){
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
    if($line = mysql_fetch_array($result)){
     $object = new <% echo $this->name; %>(<%
$first = true;
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double':
  case 'bool':
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\']';
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\'], $line[\''.$field->label.'_type\']';
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
      $query = 'SELECT id_<% echo $field->label; %> FROM object_<% echo $this->name; %>_<% echo $field->label; %>_<% echo $field->options['relationname']; %>_nmrelation '
              .'WHERE id_<% echo $this->name; %>="'.$line['id'].'"';<%
    }%>
      $result2 = mysql_query($query) or die('Error while selecting N:M relations.<br />'.$query);

      $relationNMidlist = array();
      while($row = mysql_fetch_array($result2)){
        $relationNMidlist[] = $row[0];
      }
      $object->set<% echo $field->options['relationname']; %>ids($relationNMidlist);<%
  }
}%>
      $object->setId($line['id']);
      return $object;
    }
    else{
      return false;
    }
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
  case 'date':
  case 'text':
  case 'datetime':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\']';
    $first = false;
    break;
  case 'image':
    if(!$first){
      echo ', ';
    }
    echo '$line[\''.$field->label.'\'], $line[\''.$field->label.'_type\']';
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
      $result2 = mysql_query($query) or die('Error while selecting N:M relations.<br />'.$query);

      $relationNMidlist = array();
      while($row = mysql_fetch_array($result2)){
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
  }<%
  }
}
%>
  /* Display a form to fill all the fields of the object.
   * @param array $posted If the form should contains some posted values (for example $_POST to fill the form in case of an error in format), $posted contains these values in an array indexed by the names of the field.
   * @param string $action If the form should not post the values on the same script ($_SERVER['PHP_SELF']), $action can be specified as a GET URL.
   * @return string The HTML code of the form.
   */
  public static function getForm($submittext, $postedobject=null, $action=null){
    $translator = new translator();<%
// find if there is any image field
$isTherePic = false;
foreach($this->fieldlist as $field){
  if($field->type == 'image'){
    $isTherePic = true;
  }
}
%>
    $ret = '<form class="easydevform marginleft" name="<% echo $this->name;%>form" action="'.($action == null ? $_SERVER['PHP_SELF'] : $action).'" method="post" <% if($isTherePic == true) echo 'enctype="multipart/form-data"'; %>>'."\n";
    $ret .= '<table>'."\n";<%
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':
  case 'string':
  case 'double': %>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->label;%> : </td>'."\n".
            '    <td><input type="text" name="<% echo $field->label;%>" '.($postedobject != null ? 'value="'.htmlentities($postedobject-><% echo $field->label;%>).'"' : '').'/>'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  case 'text': %>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->label;%> : </td>'."\n".
            '    <td><textarea name="<% echo $field->label;%>">'.($postedobject != null ? htmlentities($postedobject-><% echo $field->label;%>) : '').'</textarea>'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  case 'bool': %>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->label;%> : </td>'."\n".
            '    <td><select name="<% echo $field->label; %>">'."\n".
            '      <option value=""></option>'."\n".
            '      <option value="1" '.($postedobject != null && $postedobject-><% echo $field->label; %> == '1' ? 'selected' : '').'>'.$translator->translate('true').'</option>'."\n".
            '      <option value="0" '.($postedobject != null && $postedobject-><% echo $field->label; %> == '0' ? 'selected' : '').'>'.$translator->translate('false').'</option>'."\n".
            '    </td>'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  case 'image': %>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->label;%> : </td>'."\n".
            '    <td><input type="file" name="<% echo $field->label;%>">'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  case 'date': %>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->label;%> : </td>'."\n".
            '    <td><input type="text" name="<% echo $field->label;%>" '.($postedobject != null ? 'value="'.$postedobject-><% echo $field->label;%>.'"' : '').'/>'."\n".
            '    <script language="JavaScript" type="text/javascript">'."\n".
            '    <!-- '."\n".
            '    function dynFunc<% echo $field->label;%>(date, month, year) {'."\n".
            '      if (document.<% echo $this->name;%>form.<% echo $field->label;%>.disabled == false) {'."\n".
            '        document.<% echo $this->name;%>form.<% echo $field->label;%>.value = year + "-" + subrstr("00" + month, 2) + "-" + subrstr("00" + date, 2);'."\n".
	      '      }'."\n".
            '    }'."\n".
            '    dynVar<% echo $field->label;%> = new dynCalendar("dynVar<% echo $field->label;%>", "dynFunc<% echo $field->label;%>");'."\n".
            '    dynVar<% echo $field->label;%>.setOffset(20, 10);'."\n".
            '    //-->'."\n".
            '    </script><div style="visibility: hidden;" class="dynCalendar" id="dynCalendar_layer_1" onmouseover="dynVar<% echo $field->label;%>._mouseover(true)" onmouseout="dynVar<% echo $field->label;%>._mouseover(false)"></div>'."\n".
            '    <noscript><span><i>(yyyy-mm-dd)</i></span></noscript>'."\n".
            '    </td>'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  case 'datetime': %>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->label;%> : </td>'."\n".
            '    <td><input type="text" name="<% echo $field->label;%>date" '.($postedobject != null && $postedobject-><% echo $field->label;%> != '' && date('Y-m-d', strtotime($postedobject-><% echo $field->label;%>)) != '1970-01-01' ? 'value="'.date('Y-m-d', strtotime($postedobject-><% echo $field->label;%>)).'"' : '').'/>'."\n".
            '    <script language="JavaScript" type="text/javascript">'."\n".
            '    <!-- '."\n".
            '    function dynFunc<% echo $field->label;%>(date, month, year) {'."\n".
            '      if (document.<% echo $this->name;%>form.<% echo $field->label;%>date.disabled == false) {'."\n".
            '        document.<% echo $this->name;%>form.<% echo $field->label;%>date.value = year + "-" + subrstr("00" + month, 2) + "-" + subrstr("00" + date, 2);'."\n".
	      '      }'."\n".
            '    }'."\n".
            '    dynVar<% echo $field->label;%> = new dynCalendar("dynVar<% echo $field->label;%>", "dynFunc<% echo $field->label;%>");'."\n".
            '    dynVar<% echo $field->label;%>.setOffset(20, 10);'."\n".
            '    //-->'."\n".
            '    </script><div style="visibility: hidden;" class="dynCalendar" id="dynCalendar_layer_1" onmouseover="dynVar<% echo $field->label;%>._mouseover(true)" onmouseout="dynVar<% echo $field->label;%>._mouseover(false)"></div>'."\n".
            '    <noscript><span><i>(yyyy-mm-dd)</i></span></noscript>'."\n".
            '    <select class="datetime" name="<% echo $field->label;%>hour">'."\n".
            '       <option value=""></option>'."\n";
    for($i = 0; $i <= 24; $i++){
      $ret .= '      <option value="'.($i < 10 ? '0'.$i : $i).'"'.($postedobject != null && $postedobject-><% echo $field->label;%> != '' && date('H', strtotime($postedobject-><% echo $field->label;%>)) == $i ? ' selected="selected"' : '').'>'.$i.'</option>'."\n";
    }
    $ret .= '    </select> h '."\n".
            '    <select class="datetime" name="<% echo $field->label;%>mins">'."\n".
            '       <option value=""></option>'."\n";
    for($i = 0; $i <= 60; $i++){
      $ret .= '      <option value="'.($i < 10 ? '0'.$i : $i).'"'.($postedobject != null && $postedobject-><% echo $field->label;%> != '' && date('i', strtotime($postedobject-><% echo $field->label;%>)) == $i ? ' selected="selected"' : '').'>'.$i.'</option>'."\n";
    }
    $ret .= '    </select>'."\n".
            '    </td>'."\n".
            '  </tr>'."\n".
            '  <tr valign="top">'."\n".
            '    <td></td><td><input type="checkbox" name="<% echo $field->label;%>now" value="now" /> CURRENT TIME</td>'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  case 'relation1N': 
	// find the labels which can be displayed as text from the table it is linked to
	$query = 'DESCRIBE object_'.$field->label;
	$result = mysql_query($query) or die('Error while getting table description.<br />'.$query);
	
	$textfields = array(); // the fields of the database which can be diplayed as text. 
	// this list will be used later to print a select list, so ids and other relations should not be taken into account.
	while($line = mysql_fetch_array($result)){
	  $triplet = substr($line['Type'], 0, 3);
	  switch($triplet){
	  case 'int':
	  case 'tin':
	  case 'var':
        case 'dat':
	  case 'dou':
		// if the type of the field is int, tinyint, text or double, it can be printed as text
		if($line['Field'] != 'id' && substr($line['Field'], 0, 3) != 'id_'){// remove the primary key and relations
		  array_push($textfields, $line['Field']);
		}
		break;
	  default:
		break;
	  }
	}
	
	// now we have in $textfields all the fields that can be represented as text in a select list
%>
    $ret .= '  <tr>'."\n";
    $ret .= '    <td><% echo $field->options['relationname']; %> : </td>';
    // we can make the select list
    $query = 'SELECT * FROM object_<% echo $field->label; %> ORDER BY id';
    $result = mysql_query($query) or die('Error while selecting object list.<br />'.$query);
    
    $ret .= '    <td><select class="selectinput" name="<% echo $field->options['relationname']; %>">'."\n";
    while($line = mysql_fetch_array($result)){
	$ret .= '      <option value="'.$line['id'].'"'.($postedobject != null && $postedobject->relation1N<% echo $field->options['relationname'];%> == $line['id'] ? ' selected="selected"' : '').'>'.$line['id'];
<%
foreach($textfields as $textfield){%>
      $ret .=  ' - '.$line['<% echo $textfield; %>'];<%
}%>
    $ret .= '</option>'."\n";
  }
  $ret .= '    </select></td>'."\n";
    $ret .= '  </tr>'."\n";<%
    break;
  default:
    break;
  }
}
%>
    $ret .= '  <tr>'."\n".
            '    <td></td>'."\n".
            '    <td><input type="submit" name="formsubmit" value="'.$submittext.'" /></td>'."\n".
            '  </tr>'."\n".
            '</table>'."\n".
            '</form>'."\n";

    return $ret;
  }

  /* Verify the values entered in a form according to the types of the fields.
   * @param array $posted Contains the values entered in the form in an array indexed by the names of the field.
   * @return array An array of textual errors that occured during verifications.
   */
  public static function verifyForm($posted){
    $translator = new translator();
    $errors = array();<%
foreach($this->fieldlist as $field){
  switch($field->type){
  case 'integer':%>
    if(! preg_match('/^[\-]?[0-9]+$/', $posted['<% echo $field->label;%>'])){
      $errors[] = $translator->translate('generator_add_object_expected_integer').'<% echo $field->label;%>';
    }<%
    break;
  case 'string':
    break;
  case 'double': %>
    if(! preg_match('/^[\-]?[0-9]+([\.][0-9]+)?$/', $posted['<% echo $field->label;%>'])){
      $errors[] = $translator->translate('generator_add_object_expected_double').'<% echo $field->label;%>';
    }<%
    break;
  case 'bool': %>
    if($posted['<% echo $field->label; %>'] !== '1' && $posted['<% echo $field->label; %>'] !== '0'){
      $errors[] = $translator->translate('generator_add_object_boolean_unset').'<% echo $field->label;%>';
    }<%
    break;
  case 'date': %>
    $exploded = explode('-', $posted['<% echo $field->label; %>']);
    if(count($exploded) != 3 || $exploded[0] < 1900 || $exploded[0] > 2050 || $exploded[1] > 12 || $exploded[1] < 1 || $exploded[2] > 31 || $exploded[2] < 1){
      $errors[] = $translator->translate('generator_add_object_date_format_error').'<% echo $field->label;%>';
    }<%
    break;
  case 'datetime': %>
    $exploded = explode('-', $posted['<% echo $field->label; %>date']);
    if(count($exploded) != 3 || $exploded[0] < 1900 || $exploded[0] > 2050 || $exploded[1] > 12 || $exploded[1] < 1 || $exploded[2] > 31 || $exploded[2] < 1){
      $errors[] = $translator->translate('generator_add_object_date_format_error').'<% echo $field->label;%>';
    }
    if($posted['<% echo $field->label; %>hour'] > 24 || $posted['<% echo $field->label; %>hour'] < 0 || $posted['<% echo $field->label; %>hour'] === ''){
      $errors[] = $translator->translate('generator_add_object_hour_format_error').'<% echo $field->label;%>';
    }
    if($posted['<% echo $field->label; %>mins'] > 60 || $posted['<% echo $field->label; %>mins'] < 0 || $posted['<% echo $field->label; %>mins'] === ''){
      $errors[] = $translator->translate('generator_add_object_mins_format_error').'<% echo $field->label;%>';
    }<%
    break;
  default:
    break;
  }
}
%>
    return $errors;
  }

  /* Get an image from a form and return it as a string to save in the database.
   * @param string $name The name of the sent file.
   */
  public static function getImageFromForm($name, $maxwidth=null, $maxheight=null){
    $translator = new translator();
	$image = null;
	$imagetype = null;
    if(isset($_FILES[$name]) && $_FILES[$name]['error'] == 0){
      $imageinfos = getimagesize($_FILES[$name]['tmp_name']);
	  $width = $imageinfos[0];
	  $height = $imageinfos[1];
	  $imagetype = $imageinfos[2];

	  // if the image already satisfies the width and height constraints, just return it to the user
	  if(($maxwidth == null && $maxheight == null) // no constraints
		 || ($maxwidth == null && $maxheight != null && $maxheight >= $height) // only height constraint, but satisfied
		 || ($maxwidth != null && $maxwidth >= $width && $maxheight == null) // only width constraint, but satisfied
		 || ($maxwidth != null && $maxheight != null && $maxwidth >= $width && $maxheight >= $height)// both constraint and both satisfied
		 ){
		$imagearray = file($_FILES[$name]['tmp_name']);
            foreach($imagearray as $line){
              $image .= $line;
            }
		return array($image, $imagetype, array()); // WARNING : As the image is part of a form, add the slashes to escape special chars
	  }
   
	  // create the image resource
	  $resource = null;
	  switch($imagetype){
	  case IMAGETYPE_GIF:
		$resource = imagecreatefromgif($_FILES[$name]['tmp_name']);
		break;
	  case IMAGETYPE_JPEG:
		$resource = imagecreatefromjpeg($_FILES[$name]['tmp_name']);
		break;
	  case IMAGETYPE_PNG:
		$resource = imagecreatefrompng($_FILES[$name]['tmp_name']);
		break;
	  default:
		$errors[] = $translator->translate('generator_add_object_image_bad_type').'<% echo $name;%>';
		break;
	  }

	  // if everything is fine up to now
	  if(count($errors) == 0 && $resource != null){
		$newwidth = null;
		$newheight = null;
		
		// compute new size of the image
		if($maxwidth < $width){
		  $newwidth = $maxwidth;
		  $newheight = $height * ($newwidth / $width); // multiply the height by the factor of reduction we applied to the width
		}
		else{
		  $newwidth = $width;
		  $newheight = $height;
		}

		// resize the height of the image
		if($maxheight < $newheight){
		  $newwidth = $newwidth * ($maxheight / $newheight);
		  $newheight = $maxheight;
		}
		else{
		  // nothing to do, keep $newwidth and $newheight of the previous if clause
		}

		// resize the image
		$resizedresource = imagecreatetruecolor($newwidth, $newheight);
		$status = imagecopyresized($resizedresource, $resource, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		if($status == true){
		  ob_start();
		  switch($imagetype){
		  case IMAGETYPE_GIF:
			imagegif($resizedresource);
			break;
		  case IMAGETYPE_JPEG:
			imagejpeg($resizedresource, null, 95);
			break;
		  case IMAGETYPE_PNG:
			imagepng($resizedresource, null, 2);
			break;
		  default:
			$errors[] = $translator->translate('generator_add_object_image_bad_type').'<% echo $name;%>';
			break;
		  }
		  $image = ob_get_clean();
		}
		else{
		  $errors[] = $translator->translate('generator_add_object_image_resize_error').'<% echo $name;%>';
		}
	  }
    }
    else{
      if(isset($_FILES[$name])){
        switch($_FILES[$name]['error']){
		case UPLOAD_ERR_INI_SIZE:
		  $errors[] = $translator->translate('generator_add_object_image_too_large_file').'<% echo $name;%>';
		  break;
		case UPLOAD_ERR_FORM_SIZE:
		  $errors[] = $translator->translate('generator_add_object_image_html_too_large_file').'<% echo $name;%>';
		  break;
		case UPLOAD_ERR_PARTIAL:
		  $errors[] = $translator->translate('generator_add_object_image_partial_file').'<% echo $name;%>';
		  break;
		case UPLOAD_ERR_NO_FILE:
		  $errors[] = $translator->translate('generator_add_object_image_no_file').'<% echo $name;%>';
		  break;
		case UPLOAD_ERR_NO_TMP_DIR:
		  $errors[] = $translator->translate('generator_add_object_image_no_tmp_dir').'<% echo $name;%>';
		  break;
		case UPLOAD_ERR_CANT_WRITE:
		  $errors[] = $translator->translate('generator_add_object_image_no_tmp_dir').'<% echo $name;%>';
		  break;
		case UPLOAD_ERR_EXTENSION:
		  $errors[] = $translator->translate('generator_add_object_image_extention').'<% echo $name;%>';
		  break;
		default:
		  $errors[] = $translator->translate('generator_add_object_image_unknown_err').'<% echo $name;%>';
		  break;
        }
	  }
      else{
        $errors[] = $translator->translate('generator_add_object_image_no_such_file').'<% echo $name;%>';
      }
    }

    return array($image, $imagetype, $errors);
  }

}
?>
