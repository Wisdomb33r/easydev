<?php

$hostname = 'localhost';
$username = 'easydev';
$password = 't53cE56n6';
$nom_de_base = 'easydev0';

// Insert here the directory path on your server where the EasyDev console is installed without your domain name. 
//For instance, enter /admin/console/ if the EasyDev console has its root on http://www.example.com/admin/console/
const CONSOLE_PATH = '/';

// Width and height limitation for all the fields of type "image".
const MAX_IMAGE_WIDTH = 2500;
const MAX_IMAGE_HEIGHT = 4000;

// max number of string fields
const MAX_NUMBER_STRING_FIELDS = 10;

global $LINK;
$LINK = mysqli_connect("$hostname", "$username", "$password") or die("Could not connect");
mysqli_set_charset($LINK, 'utf8');
mysqli_select_db($LINK, "$nom_de_base") or die("Could not select database");

?>
