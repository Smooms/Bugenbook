<?php
	setlocale(LC_ALL, 'de_DE' );
	date_default_timezone_set('Europe/Berlin');
	error_reporting( E_ALL & ~E_NOTICE);
	
	
	// Config Loader
	$config_dir = scandir('config' . DIRECTORY_SEPARATOR);
	foreach ($config_dir as $config_file)
	{
		$config_file_path = 'config' . DIRECTORY_SEPARATOR . $config_file;
		
		if (is_file($config_file_path)) 
		{
			require_once $config_file_path;
		}
	}
	
	
	require_once 'init_function.php';
	
	
#	Common::dump('','3');
	
	session_start();
	
	
	$database	= array
	(
		'host'		=> 	MYSQL_HOST,
		'user'		=>	MYSQL_USER,
		'pass'		=>	MYSQL_PASSWORT,
		'db'		=>	MYSQL_DATENBANK,
	);
	
	// Datenbankverbindung erstellen.
	Database::connect($database['host'], $database['user'], $database['pass'], $database['db']);
