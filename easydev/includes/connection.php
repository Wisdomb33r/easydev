<?php

$hostname = 'localhost';
$username = 'easydev';
$password = 't53cE56n6';
$nom_de_base = 'easydev0';

// Insert here the directory path on your server where the EasyDev console is installed without your domain name. 
//For instance, enter /admin/console/ if the EasyDev console has its root on http://www.example.com/admin/console/
define('CONSOLE_PATH', '/EasyDev/easydev2.0/');

// This parameter let you adjust the number of different sizes you want to allow for all objects fields of type "image".
// The native size of the image count as one.
// This parameter serves as a protection against an attacker that would call all the images of your web site with thousands of different sizes.
// As the images are resized and then stored on the disk, this would lead to hard disk storage possible overload / crash.
// Default parameter is 3.
define('MAX_IMAGE_RESIZE_WIDTH', 5);

// Width and heigth limitation for all the fields of type "image".
define('MAX_IMAGE_WIDTH', 2500);
define('MAX_IMAGE_HEIGTH', 4000);

$link = mysql_connect("$hostname", "$username", "$password") or die("Could not connect");
mysql_select_db("$nom_de_base") or die("Could not select database");

?>