<?php

define( 'MYSQL_HOST' , 'localhost' );
define( 'MYSQL_USER' , 'root' );
define( 'MYSQL_PASSWORT' , '' );
define( 'MYSQL_DATENBANK' , 'bugenbook' );

define( 'DIR_ROOT' , 'C:' . DIRECTORY_SEPARATOR . 'xampp' . DIRECTORY_SEPARATOR . 'htdocs' . DIRECTORY_SEPARATOR . 'entwicklung' . DIRECTORY_SEPARATOR . 'Bugenbook' );
define( 'DIR_CLASSES' , DIR_ROOT . DIRECTORY_SEPARATOR . 'class');
define( 'DIR_INI' , DIR_ROOT . DIRECTORY_SEPARATOR . 'ini');
define( 'DIR_MEDIA', DIR_ROOT . DIRECTORY_SEPARATOR . 'media');

define( 'URL_ROOT', '/entwicklung/bugenbook');
define( 'URL_STARTSEITE', URL_ROOT . '/liste/list');
define( 'URL_MEDIA', URL_ROOT . '/media');
define( 'URL_ICONS', URL_ROOT . '/icons' );
define( 'URL_IMAGES', URL_ROOT . '/images' );
define( 'URL_CSS', URL_ROOT . '/style' );

define( 'SITE_NAME', 'bugenbook');

define( 'STATUS_DEAKTIVIERT', 1 );
define( 'STATUS_AKTIVIERT', 2 );
define( 'STATUS_GELOESCHT', 3 );