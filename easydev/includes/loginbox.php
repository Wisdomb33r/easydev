<?php
// requirements
require_once('includes/connection.php');
require_once('includes/translator.class.php');
require_once('includes/constants.php');
require_once('includes/dbconstants.php');

// render the login page
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml">'."\n";
echo '<head>'."\n";
echo '<style type="text/css">'."\n";
echo '@import url("adminstyle.css");'."\n";
echo '</style>'."\n";
echo '<title>'.htmlentities(Translator::translate('console_title')).'</title>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

$query = 'SELECT value FROM '.CONFIGURATION.' WHERE id="version"';
$result = mysql_query($query) or die('Error While selecting console version.');
$row = mysql_fetch_array($result);
$currentVersion = $row['value'];

echo '<form action="log.php" method="post">'."\n";
echo '<table class="log">'."\n";
echo '  <tr>';
echo '    <td colspan="2" class="logheader">'.htmlentities(Translator::translate('console_index_login_title')).'<br />'.htmlentities(Translator::translate('version')).' : '.$currentVersion.'</td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '	  <td colspan="2" class="logheader">'.htmlentities(Translator::translate('language')).' : ';

$query = 'SELECT tag FROM '.TRANSLATION_LANGUAGES;
$result = mysql_query($query) or die('Error while selecting tags from translation tables.');
while($line = mysql_fetch_array($result)){
  echo '<a class="default" href="index.php?'.SESSION_LANGUAGE.'='.$line['tag'].'">'.$line['tag'].'</a> ';
}
echo '</td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '    <td class="log1">'.htmlentities(Translator::translate('username')).' : </td>'."\n";
echo '    <td class="log2"><input class="textinput" type="text" name="Username" size="40" maxlength="40" /></td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '    <td class="log1">'.htmlentities(Translator::translate('password')).' : </td>'."\n";
echo '    <td class="log2"><input class="passwordinput" type="password" name="Userpass" size="40" maxlength="40" /></td>'."\n";
echo '  </tr>'."\n";
echo '  <tr>'."\n";
echo '      <td></td>'."\n";
echo '      <td class="log3"><input class="bouton" type="submit" value="'.htmlentities(Translator::translate('connect')).'" /></td>'."\n";
echo '   </tr>'."\n";
echo '</table>'."\n";
echo '</form>'."\n";
echo '</body>'."\n";
echo '</html>'."\n";
?>