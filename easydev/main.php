<?php
// this include will include all the files that are needed for the console
// it will also start or reactivate the session and make some standards verifications on the variables
// it also make a verification that the user is logged
require_once('includes.php');

// include the HTML header
include 'adminheader.php';

// default page (very simple one now)
echo '<p class="center largemargintop">'.htmlentities($translator->translate('console_main_default_content')).'</p>'."\n";

// include the HTML footer
include 'adminfooter.php';
?>