<?php

/**
 * Viele Wiederkehrene Methoden
 *
 * @author Gernot Heidemann
 */

class Common
{
/**
 * Aufruf der Funktion Common::invokeArray($array, $function, $args)
 * 
 * Führt eine übergebene Funktion rekursiv durch
 *
 * @param array $array
 * @param string $function
 * @param array $args
 * @return array
 */
	public static function invokeArray(array $array, $function, array $args = null)
	{
		if(is_array($array))
		{
			foreach($array as $key => &$val)
			{
				if(is_array($val))
				{
					$val = self::invokeArray($val, $function);
				}
				else
				{
					if(function_exists($function))
					{
						$val = call_user_func($function, $val);
					}
				}
			}
		}
		return $array;
	}
	
	public static function normalizeString($name)
	{
	#	Common::dump($name);
		$name = str_replace('Ä', 'ae', $name);
		$name = str_replace('ä', 'ae', $name);
		$name = str_replace('Ö', 'ae', $name);
		$name = str_replace('ö', 'ae', $name);
		$name = str_replace('Ü', 'ae', $name);
		$name = str_replace('ü', 'ae', $name);
		$name = str_replace(' ', '_', $name);
		$name = str_replace('/', '-', $name);
		$name = str_replace('--', '-', $name);
	#	Common::dump($name);
		$name = preg_replace('/[^A-Za-z0-9\-_.\/]/', '', $name);
	#	Common::dump($name);
		return $name;
	}


/**
 * Aufruf der Funktion Common::timestring2date($timesting, $format)
 * 
 * Wandelt eine Timestring um
 * $timesting muss von strtotime geparst werden können
 * $format entspricht Der Formatierung von Der Funktion date()
 *
 * @param string $timesting
 * @param string $format
 * @return string
 */
	public static function timestring2date($timesting, $format = null)
	{
		$tmp_time	= strtotime($timesting);

		if(false == $tmp_time)
		{
			return '';
		}
		
		$tmp_format	= 'd.m.Y H:i:s';
		if(!is_null($format))
		{
			$tmp_format = $format;
		}

		return date($tmp_format, $tmp_time);
	}


	public static function array2Object(array $array)
	{
		$object	= new stdClass();
		foreach($array as $key => $value)
		{
			if(is_array($value))
			{
				$object->$key	= self::array2Object($value);
			}
			else
			{
				$object->$key	= $value;
			}
		}
		return $object;
	}


	public function object2Array($object)
	{
		$array	= array();
		foreach($object as $key => $value)
		{
			if(is_object($value))
			{
				$array[$key]	= self::object2Array($value);
			}
			else
			{
				$array[$key]	= $value;
			}
		}
		return $array;
	}


	/**
	 * Gibt die aktuelle URL zurück
	 * 
	 * @return string
	 */
	public static function getURL()
	{
		$query			= $_SERVER['QUERY_STRING'];
		$query_array	= explode('&',$query);
		$das_erste_mal	= true;
		$query			= '';
		foreach($query_array as $schluessel => $wert)
		{
			if(strpos($wert,'seite') === false)
			{
				if($das_erste_mal === true)
				{
					$query		.= '?';
				}
				else
				{
					$query		.= '&';
				}
				$query			.= $wert;
				$das_erste_mal	= true;
			}
		}

		$http	= 'http://';
		if($_SERVER['HTTPS'])
		{
			$http	= 'https://';
		}
		$urlis = $http . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . $query;
		return $urlis;
	}


	public static function dump($data, $text = '', $vardump = null, $color = '#cacaca')
	{
		echo '<div style="margin: 10px 0; padding: 5px; border: 1px solid red; font-size: 11px; background-color: #ffeeee; overflow: auto;">';
		if($text != '')
		{
			echo '<p style="font-weight: bold;">';
			echo $text;
			echo '</p>';
		}
		echo '<p style="font-weight: bold;">';
		echo 'Dieser Text dient ausschließlich Testzwecken und wird automatisch wieder entfernt.';
		echo '</p>';
		if($vardump === true)
		{
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
		}
		elseif($vardump === false)
		{
			echo '<pre>';
			print_r($data);
			echo '</pre>';
		}
		elseif(is_array($data))
		{
			echo '<pre>';
			print_r($data);
			echo '</pre>';
		}
		elseif (is_object($data))
		{
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
		}
		else
		{
			echo '<pre>';
			echo $data;
			echo '</pre>';
		}
		echo '</div>';
	}
	
	public static function isImage($pfad, $name)
	{
		if (empty($pfad) OR empty($name))
		{
			return false;
		}
		
		if (getimagesize($pfad) === false)
		{
			return false;
		}
		
		$info = pathinfo($name);
		$endung = $info['extension'];
		$endung = strtolower($endung);
		
		$endungen = array 	(
							'jpg',
							'jpeg',
							'png',
							'gif',
							'bmp',
							'tif',
							);
							
		if (in_array($endung, $endungen))
		{
			return true;
		}
		return false;
	}
	
	public static function deleteFile($pfad)
	{
		if (file_exists($pfad) AND is_writeable($pfad))
		{
			unlink($pfad);
		}
	}
	
	public static function curPageURL() 
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") 
		{$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") 
		{
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	public static function curPageName() 
	{
		return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	}
	
	public static function date2GermanDate($date)
	{
		return date('d.m.Y' , strtotime($date));
	}
	
	public static function germanDate2Date($date)
	{
		return date('Y-m-d' , strtotime($date));
	}
	
}
