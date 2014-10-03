<?php
/*********************************************************************************
 * Autogenerated script
 * EasyDev 1.x copyright Patrick Mingard 2007-2010
 * Any modification of this code may alter the behaviour of EasyDev 2.x console
 ********************************************************************************/

require_once('../includes.php');
require_once('object_<% echo $this->name;%>.class.php');
require_once('object_<% echo $foreignobject->name;%>.class.php');

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
$adminMainMenu = <% echo $mainmenuid; %>;

// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: '.CONSOLE_PATH.'index.php');
  exit;
}

// if server method request == POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  // if the "cancel" button is pressed, back to the previous page
  if(isset($_POST['cancel'])){
    header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].(isset($_GET[NAVIGATION]) ? '&'.NAVIGATION.'='.$_GET[NAVIGATION] : ''));
    exit;
  }

  $relationids = isset($_POST['relationids']) && is_array($_POST['relationids']) ? $_POST['relationids'] : array();

  // first find the object we want to modify<%
if($foreign){%>
  $object = <% echo $foreignobject->name;%>::findByPrimaryId($_POST['selected']);<%
}
else{%>
  $object = <% echo $this->name;%>::findByPrimaryId($_POST['selected']);<%
}
%>

  // find the objects that were printed on the previous page
  $objectonpage = <% if($foreign) echo $this->name; else echo $foreignobject->name; %>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0), 20);

  // remove all existing relations within the range of the printed page
  foreach($objectonpage as $relobjecttoremove){
    $object->removerelation<% echo $relationfield->options['relationname']; %>($relobjecttoremove);
  }

  // add all new relations
  foreach($relationids as $newrelation){
    $newobject = <% if($foreign) echo $this->name; else echo $foreignobject->name; %>::findByPrimaryId($newrelation);
    $object->addrelation<% echo $relationfield->options['relationname']; %>($newobject);
  }

  // store the object with the new relations
  $object->store();

  // make a redirection on the same page
  header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].(isset($_GET[NAVIGATION]) ? '&'.NAVIGATION.'='.$_GET[NAVIGATION] : '').'&selected='.$_POST['selected'].'&action=displayRelationObjects');
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

  // select all the objects possible for the relation
  $objectlist = <% if($foreign) echo $this->name; else echo $foreignobject->name;%>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0), 20);
  $objectnumber = <% if($foreign) echo $this->name; else echo $foreignobject->name;%>::count();

  // print the form to make the links
  include '../adminheader.php';

  // print a title
  echo '<p><strong>'.Translator::translate('generator_nm_rel_page_second_title').'</strong></p>'."\n";
	
  echo '<form action="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].(isset($_GET[NAVIGATION]) ? '&amp;'.NAVIGATION.'='.$_GET[NAVIGATION] : '').'" method="post">'."\n";
  echo '<table class="easydevform marginleft">'."\n";
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
    echo '    <td><img src="'.$object->getImage<% echo $field->label;%>Path(50).'" width="50" /></td>'."\n";<%
    break;
  }
}
%>
    echo '  </tr>'."\n";
  }
  
  echo '  <tr>'."\n";
  echo '    <td><input type="hidden" name="selected" value="'.$_GET['selected'].'" /></td>'."\n";
  echo '    <td><input class="bouton" type="submit" name="validateRelations" value="'.htmlentities(Translator::translate('create_relations'), ENT_COMPAT, 'UTF-8').'" /> <input type="submit" name="cancel" value="'.htmlentities(Translator::translate('cancel'), ENT_COMPAT, 'UTF-8').'" /></td>'."\n";
  echo '  </tr>'."\n";
  echo '</table>'."\n";
  echo '</form>'."\n";

  // add "next" and "previous" buttons to navigate between the pages of the objects
  echo '<p>'.Translator::translate('pages').' : '.(isset($_GET[NAVIGATION]) && $_GET[NAVIGATION] > 0 ? '<a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.(!isset($_GET[NAVIGATION]) || $_GET[NAVIGATION] - 20 <= 0 ? 0 : $_GET[NAVIGATION] - 20).(isset($_GET['action']) ? '&amp;action='.$_GET['action'] : '').(isset($_GET['selected']) ? '&amp;selected='.$_GET['selected'] : '').'"><img src="'.CONSOLE_PATH.'lfleche.jpg" alt="<" /></a> ' : '').((! isset($_GET[NAVIGATION]) && $objectnumber > 20) || isset($_GET[NAVIGATION]) && $_GET[NAVIGATION] + 20 < $objectnumber ? ' <a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0) + 20).(isset($_GET['action']) ? '&amp;action='.$_GET['action'] : '').(isset($_GET['selected']) ? '&amp;selected='.$_GET['selected'] : '').'"><img src="'.CONSOLE_PATH.'rfleche.jpg" alt=">" /></a>' : '').'</p>'."\n";
} 
else{ // default case, print all the objects
  include '../adminheader.php';
<%
if($foreign){%>
  $objectlist = <% echo $foreignobject->name; %>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0), 20);
  $objectnumber = <% echo $foreignobject->name; %>::count();<%
}
else{%>
  $objectlist = <% echo $this->name; %>::find((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0), 20);
  $objectnumber = <% echo $this->name; %>::count();<%
}
%>
  // title of the page
  echo '<p><strong>'.Translator::translate('generator_nm_rel_page_main_title').'</strong></p>'."\n";

  //generate the select list with all objects
  echo '<table class="easydevform marginleft">'."\n";
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
    echo '    <td><img src="'.$object->getImage<% echo $field->label;%>Path(50).'" width="50" /></td>'."\n";<%
    break;
  }
}
%>
    echo '    <td><a class="default" href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;action=displayRelationObjects&amp;selected='.$object->id.'">'.Translator::translate('select').'</a></td>'."\n";
    echo '  </tr>'."\n";
  }
  echo '</table>'."\n";

  // add "next" and "previous" buttons to navigate between the pages of the objects
  echo '<p>'.Translator::translate('pages').' : '.(isset($_GET[NAVIGATION]) && $_GET[NAVIGATION] > 0 ? '<a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.(!isset($_GET[NAVIGATION]) || $_GET[NAVIGATION] - 20 <= 0 ? 0 : $_GET[NAVIGATION] - 20).'"><img src="'.CONSOLE_PATH.'lfleche.jpg" alt="<" /></a> ' : '').((! isset($_GET[NAVIGATION]) && $objectnumber > 20) || isset($_GET[NAVIGATION]) && $_GET[NAVIGATION] + 20 < $objectnumber ? ' <a href="'.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;'.NAVIGATION.'='.((isset($_GET[NAVIGATION]) ? $_GET[NAVIGATION] : 0) + 20).'"><img src="'.CONSOLE_PATH.'rfleche.jpg" alt=">" /></a>' : '').'</p>'."\n";

  // include the footer of the page
  include '../adminfooter.php';
}
?>
