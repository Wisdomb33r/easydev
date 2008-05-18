<?php

$hostname = 'localhost';
$username = 'easydev';
$password = 't53cE56n6';
$nom_de_base = 'easydev0';


$link = mysql_connect("$hostname", "$username", "$password") or die("Could not connect");
mysql_select_db("$nom_de_base") or die("Could not select database");

?>