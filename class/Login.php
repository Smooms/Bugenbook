<?php

class Login
{
	
	protected  	$email;
	protected  	$passwort;
	
	private		$id;
	private		$vorname;
	private		$nachname;
	private		$css;
	private 	$nutzer_art;
	private		$geschlechter_id;

	/**
	 * Überprüft die Logindaten($e-mail , $password) und gibt True bei richtigen Eingaben und False bei falschen Eingaben zurück.
	 * 
	 * @param String $email
	 * @param String $passwort
	 */
	public function checkLogin($email, $passwort)
	{
		$this->email = $email;
		$this->passwort = $passwort;
	#	Common::dump($this->email);
	#	Common::dump($this->passwort);
	#	Common::dump($_SESSION);
		if ($email AND $passwort)
		{
			
			$data = $this->getData();
		#	Common::dump($data);
			foreach ($data as $value)
			{
				if ($value['email'] == $email )
				{
					$this->id = $value['id'];
					$this->vorname = $value['vorname'];
					$this->nachname = $value['nachname'];
					$this->css = $value['css'];
					$this->profilbild = $value['profilbild'];
					$this->nutzer_art = $value['nutzer_art'];
					$this->geschlechter_id = $value['geschlechter_id'];
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	
	private function getData()
	{
		$sql = 'SELECT 
					`id`,
					`e-mail` AS email,
					`vorname`,
					`nachname`,
					`css`,
					`profilbild`,
					`nutzer_art`,
					`geschlechter_id`
					
				FROM `nutzer` 
				WHERE		`e-mail`		=	"' . $this->email . '"
				AND			`passwort`		=	MD5("' . $this->passwort . '" + `nutzer`.`geburtsdatum` )
				AND			`e-mail`		<>	""
				AND			`passwort`		<>	""
				AND			`status`		= '. STATUS_AKTIVIERT .'
				';

		#	Common::dump($sql, 'getData');
		$data = Database::getList(Database::query($sql));
		return $data;
	}
	
	/**
	 * Setzt die $_SESSION.
	 * Die Werte sind folgendermaßen zu erreichen:
	 * E-Mail:	 	$_SESSION['nutzer']['email']
	 * Passwort:	$_SESSION['nutzer']['passwort']
	 * ID:			$_SESSION['nutzer']['id']
	 * Vorname:		$_SESSION['nutzer']['vorname']
	 * Nachname:	$_SESSION['nutzer']['nachname']
	 * CSS:			$_SESSION['nutzer']['css']
	 * Nutzer Art:	$_SESSION['nutzer']['nutzer_art']
	 * 
	 */
	public function setSession()
	{
		$_SESSION['nutzer']['email'] = $this->email;
		$_SESSION['nutzer']['passwort'] = $this->passwort;
		
		$_SESSION['nutzer']['id'] = $this->id;
		$_SESSION['nutzer']['vorname'] = $this->vorname;
		$_SESSION['nutzer']['nachname'] = $this->nachname;
		$_SESSION['nutzer']['css'] = $this->css;
		$_SESSION['nutzer']['profilbild'] = $this->profilbild;
		$_SESSION['nutzer']['nutzer_art'] = $this->nutzer_art;
		$_SESSION['nutzer']['geschlechter_id'] = $this->geschlechter_id;
	}
}

?>