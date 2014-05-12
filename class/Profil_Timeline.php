<?php
class Profil_Timeline extends Profil {
	
	
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
					SELECT * 
					FROM `beitraege`
					WHERE `nutzer`
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
	
}

?>