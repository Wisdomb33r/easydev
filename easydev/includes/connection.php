<?php

$hostname = 'localhost';
$username = 'username';
$password = 'password';
$nom_de_base = 'database_name';


$link = mysql_connect("$hostname", "$username", "$password") or die("Could not connect");
mysql_select_db("$nom_de_base") or die("Could not select database");

?>