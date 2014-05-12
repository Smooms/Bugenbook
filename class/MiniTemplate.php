<?php

class MiniTemplate
{
	protected $data				= array();
	private $delimiter_left		= '*****';
	private $delimiter_right	= '*****';


/**
 * Konstruktor
 * 
 * @param $left_delimter
 * @param $right_delimter
 */
	function __construct($delimter_left = '*****', $delimter_right = '*****')
	{
		$this->delimiter_left	= (string)$delimter_left;
		$this->delimiter_right	= (string)$delimter_right;
		$data				= array();
	}


/**
 * Setzt den Delimiter links
 * 
 * @param string $left_delimter
 */
	public function setDelimiterLeft($left_delimter)
	{
		$this->delimiter_left	= (string)$left_delimter;
	}


/**
 * Setzt den Delimiter rechts
 * 
 * @param string $left_delimter
 */
	public function setDelimiterRight($right_delimter)
	{
		$this->delimiter_right	= (string)$right_delimter;
	}


/**
 * Magische Setter-Methode
 * 
 * @param $key
 * @param $value
 * 
 * @return bool
 */
	public function __set($key, $value)
	{
		$key	= (string)$key;
		$value	= (string)$value;

		if($key == 'data')
		{
			return false;
		}

		$this->data[$key] = $value;
		return true;
	}


/**
 * Magische Getter-Methode
 * 
 * @param $key
 * 
 * @return mixed
 */
	public function __get($key)
	{
		$key = (string)$key;

		if(isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		return null; 
	}


/**
 * Parst den Text und führt die Ersetzung aus.
 * 
 * @param string $text
 * 
 * @return string
 */
	public function parseText($text)
	{
		foreach($this->data as $key => $value)
		{
			$text = str_ireplace($this->delimiter_left . $key . $this->delimiter_right, $value, $text);
		}
		return $text;
	}


/**
 * Leert das Data-Array
 */
	public function reset()
	{
		$this->data = array();
	}
}
