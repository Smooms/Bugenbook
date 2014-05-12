<?php

/**
 */

class Url
{
	/**
	 * Rückgabe des aktuellen URL-Objekts
	 * 
	 * @return object
	 */
	public static function getURL()
	{
		$data						= new stdClass();
		$parameter					= new stdClass();

		$temp_domain				= $_SERVER['SERVER_NAME'];	// www.example.com
		$temp_pfad					= $_SERVER['REQUEST_URI'];	// entwicklung/CMS_neu/backend/user/list.php?id=44&ddd=rrr
		$temp_parameter				= $_SERVER['QUERY_STRING'];	// id=44&ddd=rrr
		
		$http	= 'http://';
		if($_SERVER['HTTPS'])
		{
			$http					= 'https://';
		}

		$temp						= explode('&', $temp_parameter);

		foreach($temp as $value)
		{
			$temp2					= explode('=', $value);
			if(is_array($temp2) AND isset($temp2[1]))
			{
				$parameter->$temp2[0]	= $temp2[1];
			}
		}

		$temp						= explode('?', $temp_pfad);

		$data->domain				= $temp_domain;
		$data->pfad					= $temp[0];
		$data->http					= $http;
		$data->url					= $http . $temp_domain . $temp_pfad;
		$data->parameter_string		= $temp_parameter;
		$data->parameter			= $parameter;

		return $data;
	}


/**
 * Gibt die Url als Object zurück.
 * 
 * @param object $pfad
 * 
 * @return object
 */
	public static function getURLObject($pfad = NULL)
	{
		if ($pfad == NULL)
		{
			$pfad = Url::getURL()->pfad;
		}
		$data				= new stdClass();
		$temp				= explode('/',$pfad);
		$temp				= array_reverse($temp);
		
	#	Common::dump($temp);
		foreach ($temp as $key => $value)
		{
			$temp_key = "url_" . $key;
			$data->$temp_key = $value;
		}
		return $data;
	}
}
