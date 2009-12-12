<?php

	/* USE DB or NOT*/
	define('DB_ENABLE', TRUE);

	/* DB Connection */
	define('DB_HOSTNAME', 'localhost');
	define('DB_USERNAME', '');
	define('DB_PASSWORD', '');
	define('DB_DATABASE', '');
	define('DB_CHARSET', 'utf8');
	define('DB_COLLATION', 'utf8_general_ci');

	/* for path */
	define('BASE_PATH', dirname(__FILE__) . '/');
	define('PLURK_COOKIE_PATH', BASE_PATH . '/cookie');
	define('PLURK_LOG_PATH', BASE_PATH . '/log');

	define('PLURK_NOT_LOGIN', '尚未登入 Plurk.');
	define('PLURK_FIELD_NOT_EMPTY', 'field can\'t be empty');


?>