<?php
// this include will include all the files that are needed for the console
// it starts or reactivates the session and make some standards verifications on the variables
// it makes a verification that the user is logged
require_once('includes.php');

// include the HTML header
include 'adminheader.php';

// default page (very simple one now)
echo '<p class="center largemargintop">'.htmlentities(Translator::translate('console_main_default_content'), ENT_COMPAT, 'UTF-8').'</p>'."\n";

// include the HTML footer
include 'adminfooter.php';
?>