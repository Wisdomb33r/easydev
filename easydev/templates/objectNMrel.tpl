<?php
/*********************************************************************************
 * Autogenerated script
 * EasyDev v.0.x copyright Patrick Mingard 2007
 * Any modification of this code can alter the behaviour of EasyDev v.0.x console
 ********************************************************************************/

require_once('includes.php');
require_once('object_<% echo $this->name;%>.class.php');
require_once('object_<% echo $foreignobject->name;%>.class.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
$adminMainMenu = <% echo $mainmenuid; %>;

// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: main.php');
  exit;
}

// if server method request == POST
if($_SERVER[REQUEST_METHOD] == 'POST'){
  $relationids = isset($_POST['relationids']) && is_array($_POST['relationids']) ? $_POST['relationids'] : array();

  // first find the object we want to modify<%
if($foreign){%>
  $object = <% echo $foreignobject->name;%>::findByPrimaryId($_POST['selected']);<%
}
else{%>
  $object = <% echo $this->name;%>::findByPrimaryId($_POST['selected']);<%
}
%>

  // remove all existing relations
  foreach($object->relationNM<% echo $relationfield->options['relationname']; %> as $linkedobject){
    $objecttoremove = <% if($foreign) echo $this->name; else echo $foreignobject->name; %>::findByPrimaryId($linkedobject);
    $object->removerelation<% echo $relationfield->options['relationname']; %>($objecttoremove);
  }

  // add all new relations
  foreach($relationids as $newrelation){
    $newobject = <% if($foreign) echo $this->name; else echo $foreignobject->name; %>::findByPrimaryId($newrelation);
    $object->addrelation<% echo $relationfield->options['relationname']; %>($newobject);
  }

  // store the object with the new relations
  $object->store();

  // make a redirection on the same page
  header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
  exit;
}
else if(isset($_GET['action']) && $_GET['action'] == 'displayRelationObjects' && isset($_GET['selected'])){
  // verify that $_GET['selected'] exists
  $selected = null;
  if($selected = <% if($foreign) echo $foreignobject->name; else echo $this->name;%>::findByPrimaryId($_GET['selected'])){
  }
  else{
    // the selected object do not exist, redirect on the default page
    header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
    exit;
  }
  
  // s�lection de tout les objets avec qui faire la relation
  $objectlist = <% if($foreign) echo $this->name; else echo $foreignobject->name;%>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0));
  $objectnumber = <% if($foreign) echo $this->name; else echo $foreignobject->name;%>::count();

  // print the form to make the links
  include 'adminheader.php';
	
  echo '<form action="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'" method="post">'."\n";
  echo '<table class="form">'."\n";
  foreach($objectlist as $object){
    echo '  <tr>'."\n";
    echo '    <td><input type="checkbox" class="inputcheckbox" name="relationids[]" value="'.$object->id.'" '.(in_array($object->id, $selected->relationNM<% echo $relationfield->options['relationname'];%>) ? ' checked="checked"' : '').' /> '.$object->id.'</td>'."\n";<%
if($foreign)$obj = $this;
else $obj = $foreignobject;
foreach($obj->fieldlist as $field){
  switch($field->type){
  case 'string':
  case 'integer':
  case 'double':
  case 'date':
  case 'datetime':%>
    echo '    <td>'.$object-><% echo $field->label;%>.'</td>'."\n";<%
    break;
  case 'image':%>
    echo '    <td><img src="object_image_<% echo $this->name; %>_<% echo $field->label; %>.php?id='.$object->id.'" width="50" /></td>'."\n";<%
    break;
  }
}
%>
    echo '  </tr>'."\n";
  }
  
  echo '  <tr>'."\n";
  echo '    <td><input type="hidden" name="selected" value="'.$_GET['selected'].'" /></td>'."\n";
  echo '    <td><input class="bouton" type="submit" name="validateRelations" value="'.htmlentities($translator->translate('create_relations')).'" /></td>'."\n";
  echo '  </tr>'."\n";
  echo '</table>'."\n";
  echo '</form>'."\n";

  // add "next" and "previous" buttons to navigate between the pages of the objects
  echo '<p>'.$translator->translate('pages').' : '.(isset($_GET[NAVIGATION]) && $_GET[NAVIGATION] > 0 ? '<a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.($_GET[NAVIGATION] - 20 <= 0 ? 0 : $_GET[NAVIGATION] - 20).'&amp;action=displayRelationObjects&amp;selected='.$selected->id.'"><img src="lfleche.jpg" alt="<" /></a> ' : '').((! isset($_GET[NAVIGATION]) && $objectnumber > 20) || $_GET[NAVIGATION] + 20 < $objectnumber ? ' <a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.($_GET[NAVIGATION] + 20).'&amp;action=displayRelationObjects&amp;selected='.$selected->id.'"><img src="rfleche.jpg" alt=">" /></a>' : '').'</p>'."\n";
} 
else{ // default case, print all the objects
  include 'adminheader.php';
<%
if($foreign){%>
  $objectlist = <% echo $foreignobject->name; %>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0));
  $objectnumber = <% echo $foreignobject->name; %>::count();<%
}
else{%>
  $objectlist = <% echo $this->name; %>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0));
  $objectnumber = <% echo $this->name; %>::count();<%
}
%>

  //generate the select list with all objects
  echo '<table class="form">'."\n";
  foreach($objectlist as $object){
    echo '  <tr>'."\n";
    echo '    <td>'.$object->id.'</td>'."\n";<%
if($foreign)$obj = $foreignobject;
else $obj = $this;
foreach($obj->fieldlist as $field){
  switch($field->type){
  case 'string':
  case 'integer':
  case 'double':
  case 'date':
  case 'datetime':%>
    echo '    <td>'.$object-><% echo $field->label;%>.'</td>'."\n";<%
    break;
  case 'image':%>
    echo '    <td><img src="object_image_<% echo $this->name; %>_<% echo $field->label; %>.php?id='.$object->id.'&amp;width=50" /></td>'."\n";<%
    break;
  }
}
%>
    echo '    <td><a class="default" href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;action=displayRelationObjects&amp;selected='.$object->id.'">'.$translator->translate('select').'</a></td>'."\n";
    echo '  </tr>'."\n";
  }
  echo '</table>'."\n";

  // add "next" and "previous" buttons to navigate between the pages of the objects
  echo '<p>'.$translator->translate('pages').' : '.(isset($_GET[NAVIGATION]) && $_GET[NAVIGATION] > 0 ? '<a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.($_GET[NAVIGATION] - 20 <= 0 ? 0 : $_GET[NAVIGATION] - 20).'"><img src="lfleche.jpg" alt="<" /></a> ' : '').((! isset($_GET[NAVIGATION]) && $objectnumber > 20) || $_GET[NAVIGATION] + 20 < $objectnumber ? ' <a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.($_GET[NAVIGATION] + 20).'"><img src="rfleche.jpg" alt=">" /></a>' : '').'</p>'."\n";

  // include the footer of the page
  include 'adminfooter.php';
}
?>
