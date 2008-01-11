
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
  @import url("adminstyle.css");
</style>

<title><?php echo htmlentities($translator->translate('console_title')); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>

<table class="console">
  <tr>
    <td class="banniere" colspan="2"></td>
  </tr>
  <tr>
    <td class="menu">
<?php
echo '      <p>'.$_SESSION[SESSION_NAME].' : [<a class="default" href="logout.php">'.htmlentities($translator->translate('log_out')).'</a>]'
.'<br />'.htmlentities($translator->translate('language')).' : ';

// get all the languages installed in the console
$query = 'SELECT tag FROM '.TRANSLATION_LANGUAGES;
$result = mysql_query($query) or die('Error while selecting tags from translation tables.<br />'.$query);
while($line = mysql_fetch_array($result)){
  // print a link per language in the left menu to change the language of the console
  echo '<a class="default" href="'.$_SERVER['PHP_SELF'].'?'.(isset($_GET[CURRENTMENU]) ? CURRENTMENU.'='.$_GET[CURRENTMENU].'&amp;' : '').SESSION_LANGUAGE.'='.$line['tag'].'">'.$line['tag'].'</a> ';
}
echo '</p>'."\n";
echo '      <ul class="adminmenu">'."\n";

// -------------- START OF MENU GENERATION -------------------	  
$query = 'SELECT id, text FROM '.ADMINMAIN.' ORDER BY id ASC';
$result = mysql_query($query) or die('Error while selecting main sections.<br />'.$query);

while($line = mysql_fetch_array($result)) {
  if($session_permissions[$line['id']] == 0) { //if the user has no permissions to see the content of the menu
    echo '        <li class="menumain"><img class="adminmenu" src="cadena.jpg" alt="locked" />'.$translator->translate($line['text']).'</li>'."\n";
  }
  else{ // if the user has permission to see the menu
	// if the menu id is the one that user wants to see
	if(isset($_GET[CURRENTMENU]) && $_GET[CURRENTMENU] == $line['id']) {
	  // print the opened menu
	  echo '        <li class="menumain"><a href="main.php"><img class="adminmenu" src="moins.jpg" alt="fermer" /></a>'.$translator->translate($line['text']).'</li>'."\n";
	  
	  $query2 = 'SELECT text, url FROM '.ADMINSUB.' WHERE id_mainmenu="'.$line['id'].'"';
	  $result2 = mysql_query($query2) or die('Error while selecting sub sections.<br />'.$query);
	  
	  echo '        <li class="menumain">'."\n";
	  // print the submenu
	  echo '          <ul class="adminsubmenu">'."\n";
	  while($line2 = mysql_fetch_array($result2)){
		echo '            <li class="menusub"><a class="default" href="'.$line2['url'].'.php?'.CURRENTMENU.'='.$line['id'].'">'.$translator->translate($line2['text']).'</a></li>'."\n";
	  }
      echo '          </ul>'."\n";
      echo '        </li>'."\n"; 
    }
	else{
	  // print the closed menu
	  echo '        <li class="menumain"><a href="main.php?'.CURRENTMENU.'='.$line['id'].'"><img class="adminmenu" src="plus.jpg" alt="ouvrir" /></a>'.$translator->translate($line['text']).'</li>'."\n";
	}
  }
}
// ----------------- END OF MENU GENERATION ---------------
echo '      </ul>'."\n";
echo '    </td>'."\n";
echo '    <td class="content">'."\n";
echo '    <!-- START OF MAIN CONTENT -->'."\n";

?> 