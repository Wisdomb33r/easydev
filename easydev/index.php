<?php


require_once('includes/connection.php');
require_once('includes/translator.class.php');
require_once('includes/constants.php');
require_once('includes/dbconstants.php');

session_start();
// change the language if the user wants to
if(isset($_GET[SESSION_LANGUAGE])){
  $_SESSION[SESSION_LANGUAGE] = $_GET[SESSION_LANGUAGE];
}
// initialize a new translator for this page
$translator = new translator();

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml">'."\n";
echo '<head>'."\n";
echo '<style type="text/css">'."\n";
echo '@import url("adminstyle.css");'."\n";
echo '</style>'."\n";
echo '<title>'.htmlentities($translator->translate('console_title')).'</title>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

$query = 'SELECT value FROM '.CONFIGURATION.' WHERE id="version"';
$result = mysql_query($query) or die('Error While selecting console version.<br />'.$query);
$line = mysql_fetch_array($result);
$currentVersion = $line['value'];

echo '<form action="log.php" method="post">'."\n";
echo '<table class="log">'."\n";
echo '  <tr>';
echo '    <td colspan="2" class="logheader">'.htmlentities($translator->translate('console_index_login_title')).'<br />'.htmlentities($translator->translate('version')).' : '.$currentVersion.'</td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '	  <td colspan="2" class="logheader">'.htmlentities($translator->translate('language')).' : ';

$query = 'SELECT tag FROM '.TRANSLATION_LANGUAGES;
$result = mysql_query($query) or die('Error while selecting tags from translation tables.<br />'.$query);
while($line = mysql_fetch_array($result)){
  echo '<a class="default" href="index.php?'.SESSION_LANGUAGE.'='.$line['tag'].'">'.$line['tag'].'</a> ';
}
echo '</td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '    <td class="log1">'.htmlentities($translator->translate('username')).' : </td>'."\n";
echo '    <td class="log2"><input class="textinput" type="text" name="Username" size="40" maxlength="40" /></td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '    <td class="log1">'.htmlentities($translator->translate('password')).' : </td>'."\n";
echo '    <td class="log2"><input class="passwordinput" type="password" name="Userpass" size="40" maxlength="40" /></td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '      <td></td>'."\n";
echo '      <td class="log3"><input class="bouton" type="submit" value="'.htmlentities($translator->translate('connect')).'" /></td>'."\n";
echo '   </tr>'."\n";
echo '</table>'."\n";
echo '</form>'."\n";
echo '</body>'."\n";
echo '</html>'."\n";

?>