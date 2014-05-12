<?php

class Freunde 
{
	
	/**
	 * Holt alle Freunde des Nutzers ($nutzer_id) aus der Datenbank.
	 * Folgende Daten werden zurückgegeben:
	 * freund_id	: Id des Freundes
	 * vorname		: Vorname des Freundes
	 * nachname		: Nachname des Freundes
	 * profilbild	: Pfad zum Profilbild des Freundes
	 * 
	 * @param int $nutzer_id
	 * @return array
	 */
	public function getFreunde($nutzer_id)
	{
		$query =	'
					SELECT 
						IF(`nutzer_id` != '. $nutzer_id .', `nutzer_id`, `nutzer2_id`)	AS freund_id		,
						n.`vorname` 													AS vorname			,
						n.`nachname` 													AS nachname			,
						n.`profilbild` 													AS profilbild		
					FROM `freunde` AS f
					RIGHT JOIN `nutzer` AS n ON (n.`id` = IF(`nutzer_id` != '. $nutzer_id .', `nutzer_id`, `nutzer2_id`))
					WHERE	
						(f.`nutzer_id` = '. $nutzer_id .' OR f.`nutzer2_id` = '. $nutzer_id .')
						AND
						f.`status` = '. STATUS_AKTIVIERT .'
						AND
						n.`status` = '. STATUS_AKTIVIERT .'
					';
		
		$data = Database::getList(Database::query($query));
		
		return $data;
	}
	
	
	/**
	 * Überprüft ob $viewer_id mit $nutzer_id befreundet ist.
	 * 
	 *  Wenn nicht befreundet 	: 1
	 *  Wenn befreundet			: 2
	 *  Wenn der selbe Nutzer	: 3
	 * 
	 * @param int $nutzer_id
	 * @param int $viewer_id
	 * @return number
	 */
	public static function getStatus($nutzer_id, $viewer_id)
	{
		if ($nutzer_id === $viewer_id) 
		{
			return 3;
		}
		
		$query_status = '
						SELECT
							IF(`nutzer_id` != '. $nutzer_id .', `nutzer_id`, `nutzer2_id`)	AS freund_id
						FROM `freunde` AS f
						RIGHT JOIN `nutzer` AS n ON (n.`id` = IF(`nutzer_id` != '. $nutzer_id .', `nutzer_id`, `nutzer2_id`))
						WHERE
							((f.`nutzer_id` = '. $nutzer_id .' AND f.`nutzer2_id` = '. $viewer_id .' ) OR (f.`nutzer2_id` = '. $nutzer_id .' AND f.`nutzer_id` = '. $viewer_id .'))
							AND
							f.`status` = '. STATUS_AKTIVIERT .'
							AND
							n.`status` = '. STATUS_AKTIVIERT .'
						HAVING freund_id = '. $viewer_id .'
						';
		
		$rows_status = Database::getNumRows(Database::query($query_status));
		
		if ($rows_status > 0)
		{
			return 2;
		}
		else
		{
			return 1;
		}
	} 
	
	
	/**
	 * Legt einen neuen Datensatz in der Tabelle `freunde` an.
	 * Die Freundschaft ist deaktiviert.
	 * Funktion wird aufgerufen, wenn ein Nutzer einem anderen ein Freundschaftanfrage schickt.
	 * 
	 * @param int $nutzer_id
	 * @param int $freund_id
	 * @return int
	 */
	public function insertNewFreund($nutzer_id, $freund_id)
	{
		$query =	'
					INSERT INTO `freunde`
					SET
						`nutzer_id` = '. $nutzer_id .',
						`nutzer2_id` = '. $freund_id .'
					';
		return Database::query($query);
	}
	
	
	/**
	 * Aktiviert eine Freundschaft.
	 * Funktion wird aufgerufen, wenn ein Freundscaftsanfrage bestätigt wird.
	 * 
	 * @param int $nutzer_id
	 * @param int $freund_id
	 * @return int
	 */
	public function activateFreund($nutzer_id, $freund_id)
	{
		$query = 	'
					UPDATE `freunde`
					SET
						`status` = '. STATUS_AKTIVIERT .'
					WHERE 
						(`nutzer_id` = '. $nutzer_id .' AND `nutzer2_id` = '. $freund_id .')
						OR
						(`nutzer_id` = '. $freund_id .' AND `nutzer2_id` = '. $nutzer_id .')
					';
		return Database::query($query);
	}
	
	
	/**
	 * Setzt den Status einer Freundschaft auf gelöscht.
	 * Funktion wird aufgerufen, wenn jemand einen Freund löscht.
	 * 
	 * @param int $nutzer_id
	 * @param int $freund_id
	 * @return int
	 */
	public function deleteFreund($nutzer_id, $freund_id)
	{
		$query = 	'
					UPDATE `freunde`
					SET
						`status` = '. STATUS_GELOESCHT .'
					WHERE 
						(`nutzer_id` = '. $nutzer_id .' AND `nutzer2_id` = '. $freund_id .')
						OR
						(`nutzer_id` = '. $freund_id .' AND `nutzer2_id` = '. $nutzer_id .')
					';
		return Database::query($query);
	}
	
}