<?php
	$temp	= Url::getURL();
	$url	= Url::getURLObject($temp->pfad);
#	Common::dump($temp);
#	Common::dump($url);

	if(empty($url->datei))
	{
		$url->datei	= 'index.php';
	}

	if(empty($url->bereich))
	{
		$url->bereich	= 'backend';
	}

	$file				= DIR_BACKEND . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . $url->bereich . DIRECTORY_SEPARATOR . $url->datei;
#	Common::dump($url, 'URL');
#	Common::dump($file, 'FILE');

	if(is_file($file))
	{
		$modul_load[]	= DIR_BACKEND . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . $url->bereich . DIRECTORY_SEPARATOR . $url->datei;
	}
	else
	{
		$modul_load[]	= DIR_BACKEND . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'standard' . DIRECTORY_SEPARATOR . $url->datei;
	}

#	Common::dump($modul_load, 'MODUL LOAD');
