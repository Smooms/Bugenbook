<?php

/**
 * Neue Instanz der Klasse erzeugen 
 * $mydb = new DB_MySQL('Servername','Datenbankname','Benutzername','Passwort');
 * 
 * Abfrage schicken
 * $mydb->query('SELECT * FROM tabelle');
 *
 * Anzahl Datens�tze ausgeben
 * $mydb->count();
 *
 * @author Gernot Heidemann
 */

class Database
{
	private	$host			= '';
	private	$user			= '';
	private	$pass			= '';
	private	$database		= '';

	private	$connection 	= NULL;
	private	$db				= '';
	private	$result;
	private	$data;
	private	$counter		= NULL;
	static	$instance;
	static	$last_error		= false;
	static	$last_error_num	= false;

/**
 * Konstruktor
 * 
 * Initialisierung der Datenbankverbindung
 * 
 * @param string $host
 * @param string $database
 * @param string $user
 * @param string $pass
 * @param string $charset
 */
	private function __construct()
	{
		
	}


	public static function connect($host = NULL, $user = NULL, $pass = NULL, $database = NULL, $charset = 'utf8')
	{
		$connection	= new mysqli
		(
				$host,
				$user,
				$pass,
				$database
		);

		if($connection->connect_errno !== 0)
		{
			throw new myException( 'Die Datenbankverbindung ist fehlgeschlagen.' );
		}

		self::$instance = $connection;
		$connection->set_charset($charset);
		return true;
	}


/**
 * Schließt die Datenbankverbindung
 */
	public static function disconnect()
	{
		if(is_resource(self::$instance))
		{
			mysql_close(self::$instance);
		}
	}


/**
 * F�hrt einen SQL-Befehl aus.
 * 
 * @param string $query
 */
	public static function query($query)
	{
		$db			=& self::$instance;

		$query		= trim($query);
		$query		= str_ireplace("\t"," ",$query);
		$query		= str_ireplace("\r\n"," ",$query);
		$querytype	= substr( $query, 0, strpos( $query, ' '));
		$querytype	= strtoupper( $querytype );

		switch($querytype)
		{
			case 'SELECT':
			case 'EXPLAIN':
			case 'DESCRIBE':
			case 'SHOW':
				$result	= $db->query($query);
				$return	= $result;
				break;

			case 'INSERT':
				$db->query($query);
				$return	= $db->insert_id;
				break;

			default:
				$db->query($query);
				$return	= $db->affected_rows;
				break;
		}

		if ( $db->errno !== 0 )
		{
			self::$last_error		= $db->error;
			self::$last_error_num	= $db->errno;
			echo '<pre>';
			throw new myException(self::$last_error);
		}

		self::$last_error		= false;
		self::$last_error_num	= false;
		return $return;
	}


	public static function getNumRows(mysqli_result $result)
	{
		$rows	= $result->num_rows;
		return $rows;
	}


/**
 * Gibt ein Objekt eines Datensatzes zur�ck.
 */
	public static function getObject(mysqli_result $result)
	{
		$data = $result->fetch_object();
		return $data;
	}


/**
 * Gibt ein Array der Ergebnisse zurück
 * 
 * @param mysqli_result $result
 * 
 * return array
 */
	public static function getList(mysqli_result $result)
	{
		$data	= array();
		while(is_array($line = $result->fetch_assoc()))
		{
			$data[]	= $line;
		}
		return $data;
	}


/**
 * Gibt ein Objekt der Ergebnisse zurück
 * 
 * @param mysqli_result $result
 * 
 * return object
 */
	public static function getObjectList(mysqli_result $result)
	{
		$data	= self::getList($result);
		$data	= Common::array2object($data);
		return $data;
	}


/**
 * Gibt alle Tabellen einer Datenbank als Array zurück
 * 
 * return array
 */
 	public static function getTables()
	{
		$sql	= "SHOW TABLES";
		$result	= self::query($sql);
		while(is_array($line = $result->fetch_assoc()))
		{
			$tables[] = $line[key($line)];
		}
		sort($tables);
		return $tables;
	}


/**
 * Gibt alle Felder einer Tabelle als Array zurück
 * @param string $table
 * 
 * return array
 */
	public static function getColumns($table)
	{
		$sql	= "SHOW COLUMNS FROM " . $table;
		$result	= self::query($sql);
		while(is_array($line = $result->fetch_assoc()))
		{
			$data[] = $line;
		}
		return $data;
	}
	
	public static function setData($table, $array)
	{
		$query = 	'
					INSERT INTO `'. $table .'`
					SET
					';
		
		$first = TRUE;
		foreach ($array as $key => $value)
		{
			if (!$first) 
			{
				$query = $query . ' , ';
			}
			
			$query = $query . " `$key` = '$value' 
					 ";
			$first = FALSE;
		}
		
		$query = $query . 	'
							ON DUPLICATE KEY UPDATE
							';
		$first = TRUE;
		foreach ($array as $key => $value)
		{
			if (!$first)
			{
				$query = $query . ' , ';
			}
				
			$query = $query . " `$key` = '$value'
			";
			$first = FALSE;
		}
		
	#	Common::dump($query);
		return Database::query($query);
	}
	
}
