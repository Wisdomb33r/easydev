<?php

// regexp for compiler to verify the validity of the user object code
define('COMPILER_ACCEPTED_CHAR'     , '/^[a-zA-Z0-9_\.\<\>\,\=\*\(\)\"\s\{\};!]+$/');
define('CLASSNAME_REGEXP'           , '/^[a-zA-Z]([a-zA-Z0-9_]*[a-zA-Z0-9]+)*$/');
define('FIELDNAME_REGEXP'           , '/^[a-zA-Z]([a-zA-Z0-9_]*[a-zA-Z0-9]+)*$/');
define('USERNAME_ACCEPTED_CHARS'    , '/^[a-zA-Z0-9��������������������]+$/');

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

// taken from http://dev.mysql.com/doc/refman/5.1/en/reserved-words.html
global $mysql_reserved_tokens;
$mysql_reserved_tokens = array(
	'ACCESSIBLE',
	'ADD',
	'ALL',
	'ALTER',
	'ANALYZE',
	'AND',
	'AS',
	'ASC',
	'ASENSITIVE',
	'BEFORE',
	'BETWEEN',
	'BIGINT',
	'BINARY',
	'BLOB',
	'BOTH',
	'BY',
	'CALL',
	'CASCADE',
	'CASE',
	'CHANGE',
	'CHAR',
	'CHARACTER',
	'CHECK',
	'COLLATE',
	'COLUMN',
	'CONDITION',
	'CONSTRAINT',
	'CONTINUE',
	'CONVERT',
	'CREATE',
	'CROSS',
	'CURRENT_DATE',
	'CURRENT_TIME',
	'CURRENT_TIMESTAMP',
	'CURRENT_USER',
	'CURSOR',
	'DATABASE',
	'DATABASES',
	'DAY_HOUR',
	'DAY_MICROSECOND',
	'DAY_MINUTE',
	'DAY_SECOND',
	'DEC',
	'DECIMAL',
	'DECLARE',
	'DEFAULT',
	'DELAYED',
	'DELETE',
	'DESC',
	'DESCRIBE',
	'DETERMINISTIC',
	'DISTINCT',
	'DISTINCTROW',
	'DIV',
	'DOUBLE',
	'DROP',
	'DUAL',
	'EACH',
	'ELSE',
	'ELSEIF',
	'ENCLOSED',
	'ESCAPED',
	'EXISTS',
	'EXIT',
	'EXPLAIN',
	'FALSE',
	'FETCH',
	'FLOAT',
	'FLOAT4',
	'FLOAT8',
	'FOR',
	'FORCE',
	'FOREIGN',
	'FROM',
	'FULLTEXT',
	'GRANT',
	'GROUP',
	'HAVING',
	'HIGH_PRIORITY',
	'HOUR_MICROSECOND',
	'HOUR_MINUTE',
	'HOUR_SECOND',
	'IF',
	'IGNORE',
	'IN',
	'INDEX',
	'INFILE',
	'INNER',
	'INOUT',
	'INSENSITIVE',
	'INSERT',
	'INT',
	'INT1',
	'INT2',
	'INT3',
	'INT4',
	'INT8',
	'INTEGER',
	'INTERVAL',
	'INTO',
	'IS',
	'ITERATE',
	'JOIN',
	'KEY',
	'KEYS',
	'KILL',
	'LEADING',
	'LEAVE',
	'LEFT',
	'LIKE',
	'LIMIT',
	'LINEAR',
	'LINES',
	'LOAD',
	'LOCALTIME',
	'LOCALTIMESTAMP',
	'LOCK',
	'LONG',
	'LONGBLOB',
	'LONGTEXT',
	'LOOP',
	'LOW_PRIORITY',
	'MASTER_SSL_VERIFY_SERVER_CERT',
	'MATCH',
	'MEDIUMBLOB',
	'MEDIUMINT',
	'MEDIUMTEXT',
	'MIDDLEINT',
	'MINUTE_MICROSECOND',
	'MINUTE_SECOND',
	'MOD',
	'MODIFIES',
	'NATURAL',
	'NOT',
	'NO_WRITE_TO_BINLOG',
	'NULL',
	'NUMERIC',
	'ON',
	'OPTIMIZE',
	'OPTION',
	'OPTIONALLY',
	'OR',
	'ORDER',
	'OUT',
	'OUTER',
	'OUTFILE',
	'PRECISION',
	'PRIMARY',
	'PROCEDURE',
	'PURGE',
	'RANGE',
	'READ',
	'READS',
	'READ_WRITE',
	'REAL',
	'REFERENCES',
	'REGEXP',
	'RELEASE',
	'RENAME',
	'REPEAT',
	'REPLACE',
	'REQUIRE',
	'RESTRICT',
	'RETURN',
	'REVOKE',
	'RIGHT',
	'RLIKE',
	'SCHEMA',
	'SCHEMAS',
	'SECOND_MICROSECOND',
	'SELECT',
	'SENSITIVE',
	'SEPARATOR',
	'SET',
	'SHOW',
	'SMALLINT',
	'SPATIAL',
	'SPECIFIC',
	'SQL',
	'SQLEXCEPTION',
	'SQLSTATE',
	'SQLWARNING',
	'SQL_BIG_RESULT',
	'SQL_CALC_FOUND_ROWS',
	'SQL_SMALL_RESULT',
	'SSL',
	'STARTING',
	'STRAIGHT_JOIN',
	'TABLE',
	'TERMINATED',
	'THEN',
	'TINYBLOB',
	'TINYINT',
	'TINYTEXT',
	'TO',
	'TRAILING',
	'TRIGGER',
	'TRUE',
	'UNDO',
	'UNION',
	'UNIQUE',
	'UNLOCK',
	'UNSIGNED',
	'UPDATE',
	'USAGE',
	'USE',
	'USING',
	'UTC_DATE',
	'UTC_TIME',
	'UTC_TIMESTAMP',
	'VALUES',
	'VARBINARY',
	'VARCHAR',
	'VARCHARACTER',
	'VARYING',
	'WHEN',
	'WHERE',
	'WHILE',
	'WITH',
	'WRITE',
	'XOR',
	'YEAR_MONTH',
	'ZEROFILL',

	'ENUM',
	'DATE',
	'BIT',
	'NO',
	'ACTION',
	'TEXT',
	'TIME',
	'TIMESTAMP',
);


?>