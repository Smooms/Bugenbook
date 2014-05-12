<?php

class SignUp
{
	
	/**
	 * Aktiviert einen Nutzer wenn $nutzer_id und $code mit einem Eintrag in der datenbank übereinstimen.
	 *  
	 * @param int $nutzer_id
	 * @param string $code
	 */
	public function activateUser($nutzer_id, $code)
	{
		$query =	'
					SELECT `nutzer_id`
					FROM `registrierungs_code` 
					WHERE `nutzer_id` = '. $nutzer_id .' AND `code` = "'. $code .'"
					';

		$rows = Database::getNumRows(Database::query($query));
		
		if ($rows == 1) 
		{
			$update = 	'
						UPDATE `nutzer`
						SET `status` = '. STATUS_AKTIVIERT .'
						WHERE `id` = '. $nutzer_id .'
						';
			Database::query($update);
		}
	}
	
	
	/**
	 * Checkt ob die übergebene E-Mail Adresse ($email) bereits vorhanden ist.
	 * Gibt True wenn die E-Mail bereits existiert.
	 * Gibt False wenn die E-Mail noch nicht existiert.
	 * 
	 * @param string $email
	 * @return boolean
	 */
	public function ifDuplicateEmail($email)
	{
		
		$query = 'SELECT `e-mail` FROM `nutzer` WHERE `e-mail` = "'. $email .'" ';
		
		$rows = Database::getNumRows(Database::query($query));
		
		if ($rows !== 0) {
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	
	/**
	 * Legt einen neuen Nutzer in der Datenbank an.
	 * $geburtstag mussin folgendem Format übergeben werden: YYYY-MM-DD
	 * 
	 * @param string $vorname
	 * @param string $nachname
	 * @param string $email
	 * @param string $passwort
	 * @param string $geburtstag
	 * @param string $geschlecht
	 * 
	 * @return string
	 */
	public function insertNewUser($vorname, $nachname, $email, $passwort, $geburtstag, $geschlecht)
	{
		$query =	'
					INSERT INTO `nutzer`
					SET
						`vorname` 		= "'. mysql_escape_string($vorname) .'",
						`nachname` 		= "'. mysql_escape_string($nachname) .'",
						`e-mail` 		= "'. mysql_escape_string($email) .'",
						`geburtsdatum` 	= "'. mysql_escape_string($geburtstag) .'",
						`geschlechter_id` 	= "'. mysql_escape_string($geschlecht) .'"
					';
		$id = Database::query($query);
		
		$query2 =	'
					UPDATE `nutzer`
					SET
						`passwort` 	= MD5( "'. $passwort .'" + `nutzer`.`geburtsdatum` )
					WHERE
						`id` 		= '. $id .'
					';
		Database::query($query2);
		
		$query_sichtbarkeit =	'
								INSERT INTO `nutzer_sichtbarkeit`
								SET `nutzer_id` = '. $id .'
								';
		
		Database::query($query_sichtbarkeit);
		
		$code = $this->getRandString(20);
		
		$query_code = 'INSERT INTO `registrierungs_code`
						SET `nutzer_id` = '. $id .' ,
							`code` = "'. $code .'" ';
		Database::query($query_code);
		
		// Ab hier wird die E-Mail versendet
		
		$message = file_get_contents(URL_ROOT . "/messages/anmeldung.html");
		$message = str_replace('ANMELDEVORNAME', $vorname, $message);
		$message = str_replace('AKTIVIERUNGSLINK', URL_ROOT . "/signup/?status=activate&id=". $id ."&code=" . $code , $message);
		
		$subject = 'Ihre Anmeldung bei bugenbook';
		
		mail($email, $subject, $message);
		
		return $code;
		
	}
	
	
	
	
	/**
	 * Erstellt einen zufälligen String mit der übergebenen Länge ($lenght).
	 * 
	 * @param int $length
	 * @return string
	 */
	protected function getRandString($length)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	
	/**
	 * Überprüft die Nutzereingaben auf Richtigkeit und Vollständigkeit.
	 * 
	 * @param string $vorname
	 * @param string $nachname
	 * @param string $email
	 * @param string $passwort
	 * @param string $geburtstag
	 * @param string $geschlech
	 * @return boolean
	 */
	public function checkInput($vorname, $nachname, $email, $email2, $passwort, $geburtstag, $geschlecht)
	{
		
		if (!$this->checkVorname($vorname))
		{
			return false;
		}
		
		if (!$this->checkNachname($nachname)) 
		{
			return false;
		}
		
		if (!$this->checkEmail($email, $email2)) 
		{
			return false;
		}
		
		if (!$this->checkPasswort($passwort)) 
		{
			return false;
		}
		
		
		if (!$this->checkGeburtstag($geburtstag)) 
		{
			return false;
		}
		
		if (!$this->checkGeschlecht($geschlecht)) 
		{
			return false;
		}
		
		
		return true;
	}
	
	
	/**
	 * Überprüft ob der Vorname korrekt und vollständig ist.
	 * 
	 * @param string $vorname
	 * @return boolean
	 */
	public function checkVorname($vorname)
	{
		if (strlen(htmlspecialchars($vorname)) > 200
		OR
		!is_string($vorname)
		OR 
		!$vorname)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Überprüft ob der Nachname korrekt und vollständig ist.
	 * 
	 * @param string $nachname
	 * @return boolean
	 */
	public function checkNachname($nachname)
	{
		if (strlen(htmlspecialchars($nachname)) > 200
		OR
		!is_string($nachname)
		OR
		!$nachname)
		{
			return false;
		}
		else 
		{
			return true;
		}
	}
	
	/**
	 * Überprüft ob die E-Mail korrekt und vollständig ist.
	 * 
	 * @param string $email
	 * @return boolean
	 */
	public function checkEmail($email, $email2)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) OR $email !== $email2)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	
	/**
	 * Überprüft ob das Passwort korrekt und vollständig ist.
	 * 
	 * @param string $passwort
	 * @return boolean
	 */
	public function checkPasswort($passwort)
	{
		if (!is_string($passwort)
		OR
		!$passwort)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Überprüft ob der Geburtstag korrekt und vollständig ist.
	 * 
	 * @param string $geburtstag
	 * @return boolean
	 */
	public function checkGeburtstag($geburtstag)
	{
		$date = explode('-', $geburtstag);
		
		if (!is_numeric($date[0]) OR !is_numeric($date[1]) OR !is_numeric($date[2]) OR !$geburtstag)
		{
			return false;
		}
		
		if (!checkdate($date[1], $date[2], $date[0])) 
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Überprüft ob das Geschlecht korrekt und vollständig ist.
	 * 
	 * @param int $geschlecht
	 * @return boolean
	 */
	public function checkGeschlecht($geschlecht)
	{
		if (!is_numeric($geschlecht)
		OR
		!$geschlecht)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
}