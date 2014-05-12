<?php

class Beitraege
{
	private $kommentare = 0;
	
	/**
	 * Gibt die anzuzeigenden Beiträge für die Timeline des Nutzers $nutzer_id zurück.
	 * Das zurückgegebene Array ist folgendermaßen aufgebaut:
	 * 
	 * Array
	 *	(
	 *	    [0] => Array
	 *	        (
	 *				[nutzer_id]
	 *	            [vorname]
	 *	            [nachname]
	 *				[profilbild]
	 *				[geschlechter_id]
	 *	            [beitrag_id]
	 *	            [beitrag]
	 *	            [bild]
	 *				[timestamp]
	 *				[self_plus]
	 *				[self_minus]
	 *	            [plus]
	 *	            [minus]
	 *				[kommentare]
	 *	        )
	 *
	 *	    [1] => Array
	 *	        (
	 *				[nutzer_id]
	 *	            [vorname]
	 *	            [nachname]
	 *				[profilbild]
	 *				[geschlechter_id]
	 *	            [beitrag_id]
	 *	            [beitrag]
	 *	            [bild]
	 *				[timestamp]
	 *				[self_plus]
	 *				[self_minus]
	 *	            [plus]
	 *	            [minus]
	 *				[kommentare]
	 *	        )
	 *	)
	 * 
	 * @param int $nutzer_id
	 * @return array $data
	 */
	public function getBeitraege($nutzer_id)
	{
		$query =	'
					SELECT 	n.`id`				AS nutzer_id		,
							n.`vorname`			AS vorname			,
							n.`nachname`		AS nachname			,
							n.`profilbild`		AS profilbild		,
							n.`geschlechter_id`	AS geschlechter_id	,
							b.`id`				AS beitrag_id		,
							b.`text`			AS beitrag			,
							b.`bild`			AS bild				,
							b.`timestamp`		AS timestamp	
					FROM `freunde` AS f
					LEFT  JOIN 	`nutzer` 		AS n ON ( f.`nutzer_id`  = n.`id` OR f.`nutzer2_id` = n.`id` )
					RIGHT JOIN 	`beitraege` 	AS b ON ( n.`id` 		 = b.`nutzer_id` )
					WHERE 
					(( f.`nutzer_id` 	= '. $nutzer_id .'  
					OR  f.`nutzer2_id` 	= '. $nutzer_id .' )
					AND f.`status` 		= '. STATUS_AKTIVIERT .')
					AND b.`beitraege_id` IS NULL
					AND b.`status` = '. STATUS_AKTIVIERT .'
					AND ( b.`sichtbarkeit` = '. BEITRAG_SICHTBARKEIT_JEDER .'
						OR b.`sichtbarkeit` = '. BEITRAG_SICHTBARKEIT_FREUNDE .'
						OR ( b.`sichtbarkeit` = '. BEITRAG_SICHTBARKEIT_ICH .' AND b.`nutzer_id` = '. $nutzer_id .' ) )
					GROUP BY beitrag_id
					ORDER BY timestamp DESC
					';
		
		
		$data = Database::getList(Database::query($query));
		
	#	Common::dump($data);
		
		foreach ($data as $key => $value)
		{
			// Hier wird das Ranking des Beitrags gecheckt.
			// Up- und Downvotes werden seperat überrgeben.
			$query2 = 	'
						SELECT 	`ranking`,
								`nutzer_id`
						FROM 	`ranking` 
						WHERE 	`content_art` 	= "beitraege" 
						AND 	`status` 		= '. STATUS_AKTIVIERT .' 
						AND 	`content_id` 	= '. $value['beitrag_id'] .' ';
			$ranking = Database::getList(Database::query($query2));
			
			$plus = 0;
			$minus = 0;
			
			foreach ($ranking as $value2)
			{
				if ($value2['ranking'] == 1) 
				{
					if ($value2['nutzer_id'] == $nutzer_id)
					{
						$data[$key]['self_plus'] = true;;
					}
					
					$plus++;
				}
				elseif ($value2['ranking'] == -1)
				{
					if ($value2['nutzer_id'] == $nutzer_id)
					{
						$data[$key]['self_minus'] = true;;
					}
					
					$minus++;
				}
			}
			
			$data[$key]['plus']  = $plus;
			$data[$key]['minus'] = $minus;
			
			// Hier wird die Anzahl der Kommentare, die zu dem Beitrag abgegeben wurden gezählt.
			
			
			$this->kommentare = 0;
			
			$query_comments =	'
								SELECT `id`
								FROM `kommentare`
								WHERE `content_art` = "beitraege"
								AND `content_id` = '. $value['beitrag_id'] .'
								';
		
			$comments = Database::getList(Database::query($query_comments));
			
		#	Common::dump($comments);

			foreach ($comments as $comment_value)
			{
				$this->kommentare++;
				$this->getChildComments($comment_value['id']);
			}
			
			$data[$key]['kommentare']  = $this->kommentare;
		}
		
		return $data;
	}
	
	protected function getChildComments($comment_id)
	{
		//Rekursiv sich selbst öffnen für Kommentar Antworten.
		$query =	'
					SELECT `id`
					FROM `kommentare`
					WHERE `content_art` = "kommentare"
					AND `content_id` = '. $comment_id .'
					';
		
		$comments = Database::getList(Database::query($query));
		
	#	Common::dump($comments);
		
		foreach ($comments as $comment_value)
		{
			$this->kommentare++;
			$this->getChildComments($comment_value['id']);
		}
	}
	

	public function getEigeneBeitraege($nutzer_id)
	{
		$query =	'
					SELECT 	n.`id`				AS nutzer_id		,
							n.`vorname`			AS vorname			,
							n.`nachname`		AS nachname			,
							n.`profilbild`		AS profilbild		,
							n.`geschlechter_id`	AS geschlechter_id	,
							b.`id`				AS beitrag_id		,
							b.`text`			AS beitrag			,
							b.`bild`			AS bild				,
							b.`timestamp`		AS timestamp
					FROM `nutzer` AS n
					RIGHT JOIN 	`beitraege` AS b ON ( n.`id` = b.`nutzer_id` )
					WHERE
						b.`nutzer_id` = '. $nutzer_id .'
					AND	b.`beitraege_id` IS NULL
					AND b.`status` = '. STATUS_AKTIVIERT .'
					GROUP BY beitrag_id
					ORDER BY timestamp DESC
					';
	
	
		$data = Database::getList(Database::query($query));
	
		#	Common::dump($data);
	
		foreach ($data as $key => $value)
		{
		// Hier wird das Ranking des Beitrags gecheckt.
			// Up- und Downvotes werden seperat überrgeben.
			$query2 = 	'
			SELECT 	`ranking`
						FROM 	`ranking`
						WHERE 	`content_art` 	= "beitraege"
						AND 	`status` 		= 2
						AND 	`content_id` 	= '. $value['beitrag_id'] .' ';
			$ranking = Database::getList(Database::query($query2));
					
				$plus = 0;
			$minus = 0;
				
			foreach ($ranking as $value2)
			{
			if ($value2['ranking'] == 1)
			{
					$plus++;
			}
			elseif ($value2['ranking'] == -1)
			{
			$minus++;
			}
			}
				
			$data[$key]['plus']  = $plus;
			$data[$key]['minus'] = $minus;
				
			// Hier wird die Anzahl der Kommentare, die zu dem Beitrag abgegeben wurden gezählt.
				
				
			$this->kommentare = 0;
				
			$query_comments =	'
			SELECT `id`
			FROM `kommentare`
			WHERE `content_art` = "beitraege"
								AND `content_id` = '. $value['beitrag_id'] .'
								';
	
			$comments = Database::getList(Database::query($query_comments));
					
				#	Common::dump($comments);
	
			foreach ($comments as $comment_value)
			{
				$this->kommentare++;
				$this->getChildComments($comment_value['id']);
			}
				
			$data[$key]['kommentare']  = $this->kommentare;
		}
	
		return $data;
	}
	
	
	
	public function getKommentare($beitrag_id, $nutzer_id)
	{
		
		$query_status = 	'
							SELECT `nutzer_id` 
							FROM `beitraege` 
							WHERE `id` = '. $beitrag_id .'
							AND `status` = '. STATUS_AKTIVIERT .'
							';
		
		$author_id = Database::getList(Database::query($query_status));
		$author_id = (int)$author_id[0]['nutzer_id'];
		
		$status = Freunde::getStatus($author_id, $nutzer_id);
		
		$query = 	'
					SELECT 	`id`			,
							`content_id`	,
							`kommentar`		,
							`timestamp`		,
							`nutzer_id`
					FROM `kommentare`
					WHERE `content_art` = "beitraege"
					AND `content_id` = '. $beitrag_id .'
					AND `status` = '. STATUS_AKTIVIERT .'
					';
		
		$data = Database::getList(Database::query($query));
		
		$Profil = new Profil();
		
		foreach ($data as $key => $value)
		{
			$nutzer_data = $Profil->getGeneralInfo($value['nutzer_id']);
			
			$data[$key]['nutzer'] = $nutzer_data;
			
			unset ($data[$key]['nutzer_id']);
		}
		
		return $data;
		
	}
	
	
	/**
	 * Legt einen neuen Beitrag in der Datenbank an.
	 * 
	 * @param int $nutzer_id
	 * @param string $text
	 * @param string $bild
	 * @param int $beitrag_id
	 * @return int
	 */
	public function insertNewBeitrag($nutzer_id, $text, $sichtbarkeit, $bild = NULL, $beitrag_id = NULL)
	{
		$query =	'
					INSERT INTO `beitraege`
					SET
						`nutzer_id` = '. mysql_escape_string($nutzer_id) .',
						`sichtbarkeit` = '. mysql_escape_string($sichtbarkeit) .' ,
					';
			
		if ($beitrag_id == NULL)
		{
			$query = $query . ' `text` = "'. mysql_escape_string($text) .'" ';
			
			if ($bild != NULL) 
			{
				$query = $query . ' , `bild` = '. mysql_escape_string($beitrag_id) .' ';
			}
		}
		else
		{
			$query = $query . ' `beitraege_id` = '. mysql_escape_string($beitrag_id) .' ';
		}
		
		return Database::query($query);
	}
	
	
	public function insertNewKommentar($content_id , $nutzer_id , $kommentar , $content_art = 'beitraege' )
	{
		$query = 	'
					INSERT INTO `kommentare`
					SET 
						`content_id` = '. mysql_escape_string($content_id) .'	,
						`content_art` = "'. mysql_escape_string($content_art) .'"	,
						`nutzer_id` = '. mysql_escape_string($nutzer_id) .'	,
						`kommentar` = "'. mysql_escape_string($kommentar) .'"
					';
		return Database::query($query);
	}
	
	
	/**
	 * Setzt den Status eines Beitrags auf gelöscht in der Datenbank.
	 * Dadurch wird der Beitrag nicht mehr sichtbar für die Nutzer.
	 * 
	 * @param int $beitrag_id
	 * @param int $nutzer_id
	 * @param int $nutzer_art
	 * @return boolean
	 */
	public function deactivateBeitrag($beitrag_id, $nutzer_id, $nutzer_art = NULL)
	{
		if ($this->getEditRechte($beitrag_id, $nutzer_id, $nutzer_art = NULL)) 
		{
			$query =	'
						UPDATE `beitraege`
						SET
							`status` = '. STATUS_GELOESCHT .'
						WHERE
							`id` = "'. $beitrag_id .'"
						';
			return Database::query($query);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Erstellt und einen Datensatz in der mit dem Upvote und updated gegebenenfalls den Alten.
	 *
	 * @param int $beitrag_id
	 * @param int $nutzer_id
	 * @param string $nutzer_art
	 * @return int
	 */
	public function upVoteBeitrag($beitrag_id, $nutzer_id, $nutzer_art = NULL)
	{
		$recht = $this->getVoteRechte($beitrag_id, $nutzer_id, 1, $nutzer_art = NULL);
		if ($recht === true)
		{
			$query =	'
						UPDATE `ranking`
						SET
							`status` = '. STATUS_DEAKTIVIERT .'
						WHERE
							`content_id` = "'. $beitrag_id .'"
						AND	`nutzer_id` = '. $nutzer_id .'
						AND `content_art` = "beitraege"
						';
			$temp = Database::query($query);
			
			$query2 =	'
						INSERT INTO `ranking`
						SET
							`content_id` 	= "'. mysql_escape_string($beitrag_id) .'"	,
							`nutzer_id` 	= '. mysql_escape_string($nutzer_id) .'		,
							`ranking`		= 1						,
							`content_art` 	= "beitraege"
						';
			Database::query($query2);
			
			return 1 + $temp;
		}
		elseif ($recht === -1)
		{
			$query =	'
						UPDATE `ranking`
						SET
							`status` = '. STATUS_DEAKTIVIERT .'
						WHERE
							`content_id` = "'. $beitrag_id .'"
						AND	`nutzer_id` = '. $nutzer_id .'
						AND `content_art` = "beitraege"
						';
			Database::query($query);
			return 0;
		}
		else
		{
			return -1;
		}
	}
	
	
	/**
	 * Erstellt und einen Datensatz in der mit dem Downvote und updated gegebenenfalls den Alten. 
	 * 
	 * @param int $beitrag_id
	 * @param int $nutzer_id
	 * @param string $nutzer_art
	 * @return int
	 */
	public function downVoteBeitrag($beitrag_id, $nutzer_id, $nutzer_art = NULL)
	{
		$recht = $this->getVoteRechte($beitrag_id, $nutzer_id, -1, $nutzer_art = NULL);
		if ($recht === true)
		{
			$query =	'
						UPDATE `ranking`
						SET
							`status` = '. STATUS_DEAKTIVIERT .'
						WHERE
							`content_id` = "'. $beitrag_id .'"
						AND	`nutzer_id` = '. $nutzer_id .'
						AND `content_art` = "beitraege"
						';
			$temp = Database::query($query);
			
				
			$query2 =	'
						INSERT INTO `ranking`
						SET
							`content_id` 	= "'. mysql_escape_string($beitrag_id) .'"	,
							`nutzer_id` 	= '. mysql_escape_string($nutzer_id) .'		,
							`ranking`		= -1						,
							`content_art` 	= "beitraege"
						';
			Database::query($query2);
			
			return 1 + $temp;
		}
		elseif ($recht === -1)
		{
			$query =	'
						UPDATE `ranking`
						SET
							`status` = '. STATUS_DEAKTIVIERT .'
						WHERE
							`content_id` = "'. $beitrag_id .'"
						AND	`nutzer_id` = '. $nutzer_id .'
						AND `content_art` = "beitraege"
						';
			Database::query($query);
			
			return 0;
		}
		else
		{
			return -1;
		}
	}
	
	
	/**
	 * Checkt ob ein Nutzer ($nutzer_id) die Rechte hat den Beitrag ($beitrag_id) zu bearbeiten/löschen.
	 * 
	 * @param int $beitrag_id
	 * @param int $nutzer_id
	 * @param string $nutzer_art
	 * @return boolean
	 */
	public function getEditRechte($beitrag_id, $nutzer_id, $nutzer_art = NULL)
	{
		if ($nutzer_art === NULL) 
		{
			$nutzer_art = $_SESSION['nutzer']['nutzer_art'];
		}
		
		$query =	'
					SELECT `id`
					FROM `beitraege`
					WHERE
						`id` = "'. $beitrag_id .'"
						AND 
						`nutzer_id` = "'. $nutzer_id .'"
					';
		$rows = Database::getNumRows(Database::query($query));
		
		if ($rows == 1
			OR
			$nutzer_art == NUTZER_GOD
			OR
			$nutzer_art == NUTZER_ADMIN) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	
	/**
	 * Guckt ob der Nutzer ($nutzer_id) das Recht hat den Beitrag ($beitrag_id) up- oder downzuvoten ($vote);
	 * 
	 * @param int $beitrag_id
	 * @param int $nutzer_id
	 * @param number $vote
	 * @param string $nutzer_art
	 * @return number|boolean
	 */
	public function getVoteRechte($beitrag_id, $nutzer_id, $vote, $nutzer_art = NULL)
	{
		
		if ($nutzer_art === NULL)
		{
			$nutzer_art = $_SESSION['nutzer']['nutzer_art'];
		}
	
		$query =	'
					SELECT 	b.`id` 
					FROM `beitraege` AS b
					LEFT  JOIN 	`freunde` AS f ON ( f.`nutzer_id`  = b.`nutzer_id` OR f.`nutzer2_id` = b.`nutzer_id` )
					WHERE 
					(( f.`nutzer_id` 	= '. $nutzer_id .'  
					OR  f.`nutzer2_id` 	= '. $nutzer_id .' )
					AND f.`status` 		= '. STATUS_AKTIVIERT .')
					AND b.`status` 		= '. STATUS_AKTIVIERT .'
					AND ( b.`sichtbarkeit` = '. BEITRAG_SICHTBARKEIT_JEDER .'
						OR b.`sichtbarkeit` = '. BEITRAG_SICHTBARKEIT_FREUNDE .')
					AND b.id = '. $beitrag_id .'
					';
		$rows = Database::getNumRows(Database::query($query));
	
		if ($rows == 1
		OR
		$nutzer_art == NUTZER_GOD)
		{
			$query2	=	'
						SELECT `content_id`
						FROM `ranking`
						WHERE 
							`content_id` 	= '. $beitrag_id .'
						AND `nutzer_id` 	= '. $nutzer_id .'
						AND `status` 		= '. STATUS_AKTIVIERT .'
						AND `ranking` 		= '. $vote .'
						AND `content_art` 	= "beitraege"
						';
			$rows2 = Database::getNumRows(Database::query($query2));
			
			if ($rows2 == 1)
			{
				return -1;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
}