<?php

// regexp for compiler to verify the validity of the user object code
define('COMPILER_ACCEPTED_CHAR'     , '/^[a-zA-Z0-9_\.\<\>\,\=\*\(\)\"\s\{\};]+$/');
define('CLASSNAME_REGEXP'           , '/^[a-zA-Z]([a-zA-Z0-9_]*[a-zA-Z0-9]+)*$/');
define('FIELDNAME_REGEXP'           , '/^[a-zA-Z]([a-zA-Z0-9_]*[a-zA-Z0-9]+)*$/');
define('USERNAME_ACCEPTED_CHARS'    , '/^[a-zA-Z0-9אבגהטיךכםלמןףעצפתשüû]+$/');

// session variables
define('SESSION_LOGIN'              , 'userid');
define('SESSION_NAME'               , 'username');
define('SESSION_LANGUAGE'           , 'user_language');
define('SESSION_ERRORS'             , 'errors');
define('SESSION_POSTED'             , 'posted');

// default configuration
define('DEFAULT_LANGUAGE_TAG'       , 'fr');

// variables used several times in the scripts
define('CURRENTMENU'                , 'menuid');
define('NAVIGATION'                 , 'pagenavigation');

// the menu id's for those who will not change (basic administration pages)
define('ADMIN_MENU_ID'              , '1');
define('HELP_MENU_ID'               , '3');
define('LOG_MENU_ID'                , '4');
define('COMPILER_MENU_ID'           , '6');
define('PERSONAL_INFO_MENU_ID'      , '2');
define('CONFIG_MENU_ID'             , '5');


?>