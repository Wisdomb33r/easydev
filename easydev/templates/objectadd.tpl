<?php
/*********************************************************************************
 * Autogenerated script
 * EasyDev 2.x copyright Patrick Mingard 2007-2017
 * Any modification of this code may alter the behaviour of EasyDev 2.x console
 ********************************************************************************/

// require the includes
require_once 'object_<% echo $this->name; %>.class.php';
require_once '../includes.php';<%
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){%>
require_once 'object_<% echo $field->label; %>.class.php';<%
  }
}%>

// global link to the database
global $LINK;

// first set a variable to indicate to which "mainmenu" this script belongs to in the administration console.
$adminMainMenu = <% echo $mainmenuid; %>;
 
// verify that the logged user has right to see this page
if(! $session_permissions[$adminMainMenu]){ // the user should not see this page because he do not has rights
  header('Location: '.CONSOLE_PATH.'index.php');
  exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){ // if server method request == POST<%
foreach($this->fieldlist as $field){
  if($field->type == 'datetime'){%>
  // for datetime, check if the "now" checkbox has been checked
  if(isset($_POST['<% echo $field->label;%>now']) && $_POST['<% echo $field->label; %>now'] == 'now'){
    $_POST['<% echo $field->label;%>date'] = date('Y-m-d');
    $_POST['<% echo $field->label;%>hour'] = date('H');
    $_POST['<% echo $field->label;%>mins'] = date('i');
  }<%
  }
}%>

  $object = null;
  if(isset($_GET['objectid']) && ($object = <% echo $this->name; %>::findByPrimaryId($_GET['objectid']))){ // modifying an object
    $object->updateObject($_POST);
  }
  else{
  	$object = new <% echo $this->name; %>($_POST);
  }
  
  // set all the relations1N that have been chosen<%
foreach($this->fieldlist as $field){
  if($field->type == 'relation1N'){
    if(isset($field->options['nullable']) && $field->options['nullable']){%>
  if($_POST['<% echo $field->options['relationname']; %>'] != ''){
    $object->setrelation<% echo $field->options['relationname'];%>(<% echo $field->label;%>::findByPrimaryId($_POST['<% echo $field->options['relationname'];%>']));
  }
  else{
  	$object->setrelation<% echo $field->options['relationname']; %>(null);
  }<%
    }
    else{%>
  $object->setrelation<% echo $field->options['relationname'];%>(<% echo $field->label;%>::findByPrimaryId($_POST['<% echo $field->options['relationname'];%>']));<%
    }
  }
}%>

  // store the object in the database
  if($object->store()){
    // fill the log
    $query = 'INSERT INTO '.LOGS.' (log) VALUES ("'.date('Y-m-d H:i').' : '.(isset($_GET['objectid']) ? 'Modification on object \"<% echo $this->name;%>\" with id \"'.$object->id.'\" done by '.$_SESSION[SESSION_NAME].'.' : 'New object \"<% echo $this->name; %>\" with id \"'.$object->id.'\" added by '.$_SESSION[SESSION_NAME].'.').'")';
    mysqli_query($LINK, $query) or die('Error while inserting log.');
    
    // redirect with a message for indicating that the entry was done successfully
    header('Location: '.(isset($_GET['objectid']) ? 'objectdelete_<% echo $this->name; %>.php' : $_SERVER['PHP_SELF']).'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].'&action='.(isset($_GET['objectid']) ? 'confirmmodify' : 'confirminsert').(isset($_GET['returnpage']) ? '&pagenavigation='.$_GET['returnpage'] : ''));
    exit;
  }
  else{ // there is some errors // case there is some errors in the generated script
    $_SESSION[SESSION_ERRORS] = $object->errors;
    $_SESSION[SESSION_POSTED] = $object;
    header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].(isset($_GET['objectid']) ? '&objectid='.$_GET['objectid'] : ''));
    exit;
  }
}
else{ // GET request method // if server method request == GET
  // initialize a new object for the case of edition of an existing object or if there is some POST data
  $object = null;

  // verify if there is a $_GET['objectid'] set. if there is one, the script is called to modify an existing object, otherwise to create a new one
  if(isset($_GET['objectid'])){
    $object = <% echo $this->name; %>::findByPrimaryId($_GET['objectid']);

    if(! $object){
      header('Location: '.$_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU]);
      exit;
    }
  }

  // includes the header of the page
  include '../adminheader.php';
  
  // if $_GET['action'] is set and is equal to "confirminsert", print a message
  if(isset($_GET['action']) && $_GET['action'] == 'confirminsert'){
    echo '<p><strong>'.Translator::translate('generator_confirm_insert').'</strong></p>';
  }

  // Verify if there is some errors and posted values.
  if(isset($_SESSION[SESSION_ERRORS]) && isset($_SESSION[SESSION_POSTED])){
    
    $errors = $_SESSION[SESSION_ERRORS];
    $object = $_SESSION[SESSION_POSTED];
    
    // print the errors
    echo '<ul class="errors">';
    foreach ($errors as $error){
      echo '<li class="errors">'.$error."</li>\n";
    }
    echo "</ul>\n";

    // do not forget to remove these two variables
    unset($_SESSION[SESSION_ERRORS]);
    unset($_SESSION[SESSION_POSTED]);
  }

  // add a title to the page
  if(isset($_GET['objectid'])){
    echo '<p><strong>'.Translator::translate('generator_modify_page_title').'</strong></p>';
  }
  else{
    echo '<p><strong>'.Translator::translate('generator_add_page_title').'</strong></p>';
  }
  
  $action = $_SERVER['PHP_SELF'].'?'.CURRENTMENU.'='.$_GET[CURRENTMENU].(isset($_GET['objectid']) ? '&objectid='.$_GET['objectid'] : '').(isset($_GET['returnpage']) ? '&returnpage='.$_GET['returnpage'] : '');
  echo <% echo $this->name; %>::getForm($object, $action);

  echo '<script type="text/javascript" language="javascript">'."\n";
  echo 'document.forms[0].elements[0].focus();';
  echo '</script>'."\n";

  // include the footer of the page
  include '../adminfooter.php';
}
?>
