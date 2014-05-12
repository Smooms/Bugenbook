<?php

class Nachrichten 
{
	
	/**
	 * Gibt alle Nachrichten zurück die zwischen $nutzer_id und $nutzer2_id gsendet wurden.
	 * In dem Array sind folgende Werte enthalten:
	 * id				: Id des Datensatzes
	 * nutzer_id		: Id des Nutzers, der die Nachricht versandt hat
	 * empfaenger_id	: Id des Empfängers
	 * text				: Der Inhalt der NAchricht
	 * timestamp		: Timestamp des Zeitpunktes als die Nachricht gesendet wurde.
	 * 
	 * @param int $nutzer_id
	 * @param int $nutzer2_id
	 * @return array
	 */
	public function getNachrichten($nutzer_id, $nutzer2_id)
	{
		$query =	'
					SELECT 
						`id`,
						`nutzer_id`,
						`empfaenger_id`,
						`text`,
						`timestamp`
					FROM `nachrichten`
					WHERE 
						( `nutzer_id` = '. $nutzer_id .' AND `empfaenger_id` = '. $nutzer2_id .' )
						OR
						( `nutzer_id` = '. $nutzer2_id .' AND `empfaenger_id` = '. $nutzer_id .' )
					ORDER BY `timestamp`
					';
		$data = Database::getList(Database::query($query));
		return $data;
	}
	
}