<?php
	header('Content-type: text/html; charset="utf-8"');
	require_once 'init.php';
	session_start();

	$url = Url::getURLObject();
	$signup = new SignUp();
	
#	Common::dump($temp);
#	Common::dump($url);
	

	$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . $url->url_1 . DIRECTORY_SEPARATOR . $url->url_0 . '.php';
	
	if ($url->url_1 == 'ajax')
	{
		new Ajax();
	}
	else 
	{
		
		if (is_file($file))
		{
			$modul_load[]	=	$file;
		}
		else
		{
			// Error 404
			$modul_load[]	=	dirname(__FILE__) . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '404.php';
		}
		
		foreach ($modul_load as $value)
		{
			require $value;
		}
		
		$geburtstag = Date::toMysqlDate($_REQUEST['tag'], $_REQUEST['monat'], $_REQUEST['jahr']);
		$urlobject = Url::getURLObject();
		
		if ($urlobject->url_1 == 'signup')
		{
			if(($signup->checkInput($_REQUEST['vorname'], $_REQUEST['nachname'], $_REQUEST['email'], $_REQUEST['email_check'], $_REQUEST['passwort'], $geburtstag, $_REQUEST['geschlecht'])
									AND	!$signup->ifDuplicateEmail($_REQUEST['email'])) OR $_REQUEST['status'] == 'activate')
			{
				require 'signup.php';	
			}
			else 
			{
				require 'login.php';
			}
		}
		else
		{
			require 'main.php';
		}
	}