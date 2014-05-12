<?php

/**
 * Config speichert und verwaltet Datenbank gestütze Wertepaare.
 * Config areitet dabei nach dem Singelton-Pattern
 * Config geht davon aus das es die Klasse Database verfügbar ist. 
 * Daten werden in DATENBANK.config gespeichert
 */

class Config
{
	private static $instance = null;
	public $_config_data =  array ();

	public function __construct()
	{
		$this->_get_config();
	}


/**
 * Liefert die einzige Instanz von Config zurück
 * Bei ersten aufruf wird diese Instanz erzeugt
 *
 * @return object
 */
	public static function getInstance()
	{
		if(!self::$instance instanceof Config)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}


/**
 * Magische setter Methode.
 * $CONFIG->EIGENSCHAFT = WERT
 * wird in der sowohl Instanz als auch in in der Datenbank geschrieben 
 *
 * @param string $name
 * @param string $value
 * @return bool
 */
	public function __set($name, $value)
	{
			$this->_config_data[$name] = $value;
			if($this->_write_value($name, $value))
			{
				return true;
			}
			return false;
	}


/**
 * Magische getter Methode.
 * $CONFIG->EIGENSCHAFT liefert den Wert der Eigenschaft
 * Gibt es die Eigenschaft nicht wir null zurueck geliefert
 *
 * @param string $name
 * @return mixed;
 */
	public function __get($name)
	{
		if(isset($this->_config_data[$name]))
		{
			return $this->_config_data[$name];
		}
		else
		{	
			return null;
		}
	}


/**
 * Liest alle Eigenschafften aus der Datenbank und schreibt diese in das Objekt Config
 */
	private function _get_config()
	{
		$sql	= "
		SELECT		`eigenschaft`	AS	Eigenschaft,
					`wert`			AS	Wert
		FROM		`config`
		";

#		$result	= Database::query($sql);
		$result	= mysql_query($sql);

		if(!mysql_error())
		{
			while(false !== ($row = mysql_fetch_assoc($result)))
			{
				$this->_config_data[$row['Eigenschaft']] = unserialize($row['Wert']);
			}
		}
	}


/**
 * Schreibt Wertepaare in die Datenbank
 *
 * @param string $name
 * @param mixed $value
 * @return bool
 */
	private function _write_value($name, $value)
	{
		if(get_magic_quotes_gpc())
		{
			$value	= stripslashes($value);
		}

		$value	= serialize($value);
		$value	= mysql_real_escape_string($value);

		$sql	= "
		SELECT		*
		FROM		config
		WHERE		eigenschaft	=	'" . $name . "'
		";
		$result	= mysql_query($sql);

		if(!mysql_num_rows($result))
		{
			$sql ="
			INSERT INTO	config
			SET			eigenschaft 	=	'" . $name . "',
						wert			=	'" . $value . "'
			";
			mysql_query($sql);
		}
		else
		{
			$sql = "
			UPDATE		config
			SET			wert			= '" . $value . "'
			WHERE		eigenschaft		= '" . $name . "'
			";
			mysql_query($sql);
			if(mysql_error())
			{
				return false;
			}
		}
		return true;
	}
}
