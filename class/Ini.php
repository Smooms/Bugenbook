<?php

/**
 * Die Klasse liest eine ini-Datei aus und gibt den Inhalt als Objekt zurück.
 * 
 * Aufruf der Klasse:
 * $ini = new Ini(Dateiname, Pfad der Datei);
 * 
 * @author Gernot Heidemann
 */

class Ini
{
	public $datei;
	public $pfad;
	private $inifile;


	public function __construct($datei, $pfad)
	{
		$this->datei	= $datei;
		$this->pfad		= $pfad;
		$this->setPfad();
	}


	private function setPfad()
	{
		$temp	= $this->pfad . DIRECTORY_SEPARATOR . $this->datei;
		if(file_exists($temp))
		{
			$this->inifile	= $temp;
		}
	}


	public function getIni()
	{
		$ini		= parse_ini_file($this->inifile, true);
		$ini		= Common::array2Object($ini);
		return $ini;
	}
}
