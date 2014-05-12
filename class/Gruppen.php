<?php
class Gruppen {
	
	
	/**
	 * Holt zufällig ausgewählte Gruppen aus der Datenbank, die der angegebene Nutzer ($nutzer_id) agehört.
	 * Die $anzahl entspricht der Anzahl der ausgesuchten Gruppen.
	 * 
	 * @param int $nutzer_id
	 * @param int $anzahl
	 * @return array
	 */
	public static function getRand($nutzer_id, $anzahl)
	{
		$sql = 	'
				SELECT 
					`gruppen`.`id` AS id,
					`gruppen`.`gruppenname` AS gruppenname
				FROM 
					`gruppen_nutzer`
				RIGHT JOIN
					`gruppen` ON (`gruppen_nutzer`.`gruppen_id` = `gruppen`.`id`)
				WHERE 
					`gruppen_nutzer`.`nutzer_id` = '. $nutzer_id .'
				ORDER BY 
					RAND()
				LIMIT 
					0, '. $anzahl .'
				';
		
		$data = Database::getList(Database::query($sql));
		
		return $data;
	}
	
}

?>