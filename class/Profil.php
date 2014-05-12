<?php

class Profil {
	
	/**
	 * Gibt Vorname, Nachname, Profilbild Pfad und das Geschlecht des Nutzers ($nutzer_id) zurück. 
	 * 
	 * @param int $nutzer_id
	 * @param int $viewer_id
	 * @return array
	 */
	public function getGeneralInfo($nutzer_id, $viewer_id = NULL)
	{
		if ($viewer_id == NULL)  
		{
			$viewer_id = $_SESSION['nutzer_id'];
		}
		
		$query =	'
					SELECT 
						n.`id` ,
						IF ( n_s.`vorname` 			= '. BEITRAG_SICHTBARKEIT_JEDER .' ,	n.`vorname`		, NULL )	AS vorname		,
						IF ( n_s.`nachname` 		= '. BEITRAG_SICHTBARKEIT_JEDER .' ,	n.`nachname`	, NULL )	AS nachname		,
						IF ( n_s.`profilbild` 		= '. BEITRAG_SICHTBARKEIT_JEDER .' ,	n.`profilbild`	, NULL )	AS profilbild	,
						IF ( n_s.`geschlechter_id` 	= '. BEITRAG_SICHTBARKEIT_JEDER .' ,	g.`geschlecht`	, NULL )	AS geschlecht
					FROM `nutzer` AS n
					RIGHT JOIN 	`geschlechter` 			AS g 	ON ( g.`id` = n.`geschlechter_id` )
					LEFT JOIN 	`nutzer_sichtbarkeit` 	AS n_s 	ON ( n.`id` = n_s.`nutzer_id` )
					WHERE 
						n.`id` 		= '. $nutzer_id .'
					AND n.`status` 	= '. STATUS_AKTIVIERT .'
					';
		$data = Database::getList(Database::query($query));
		
		return $data;
	}
	
	
	public function getAllInfo($nutzer_id, $viewer_id)
	{
		
		$status = Freunde::getStatus($nutzer_id, $viewer_id);
		
		// Normale Infos
		
		$query = 	'
					SELECT 
						n.`e-mail`,
						IF ( ns.`e-mail` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`e-mail` 			= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`e-mail` 			, NULL  )	AS 	`e-mail`		,
						IF ( ns.`vorname` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`vorname` 			= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`vorname` 			, NULL  )	AS 	vorname			,
						IF ( ns.`nachname` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`nachname` 			= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`nachname` 		, NULL  )	AS 	nachname		,
						IF ( ns.`land` 				= '. SICHTBARKEIT_JEDER .' OR ( ns.`land` 				= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`land` 			, NULL  )	AS 	land			,
						IF ( ns.`stadt` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`stadt` 				= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`stadt` 			, NULL  )	AS 	stadt			,
						IF ( ns.`bundesland` 		= '. SICHTBARKEIT_JEDER .' OR ( ns.`bundesland` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`bundesland` 		, NULL  )	AS 	bundesland		,
						IF ( ns.`strasse` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`strasse` 			= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`strasse` 			, NULL  )	AS 	strasse			,
						IF ( ns.`hausnummer` 		= '. SICHTBARKEIT_JEDER .' OR ( ns.`hausnummer` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`hausnummer` 		, NULL  )	AS 	hausnummer		,
						IF ( ns.`geburtsdatum` 		= '. SICHTBARKEIT_JEDER .' OR ( ns.`geburtsdatum` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`geburtsdatum` 	, NULL  )	AS 	geburtsdatum	,
						IF ( ns.`profilbild` 		= '. SICHTBARKEIT_JEDER .' OR ( ns.`profilbild` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`profilbild` 		, NULL  )	AS 	profilbild		,
						IF ( ns.`beruf` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`beruf` 				= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`beruf` 			, NULL  )	AS 	beruf			,
						IF ( ns.`geschlechter_id`	= '. SICHTBARKEIT_JEDER .' OR ( ns.`geschlechter_id` 	= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , g.`geschlecht` 		, NULL  )	AS 	geschlecht		,
						IF ( ns.`handynummer` 		= '. SICHTBARKEIT_JEDER .' OR ( ns.`handynummer` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`handynummer` 		, NULL  )	AS 	handynummer		,
						IF ( ns.`beschreibung` 		= '. SICHTBARKEIT_JEDER .' OR ( ns.`beschreibung` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`beschreibung` 	, NULL  )	AS 	beschreibung	,
						IF ( ns.`religion` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`religion` 			= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`religion` 		, NULL  )	AS 	religion		,
						IF ( ns.`politik` 			= '. SICHTBARKEIT_JEDER .' OR ( ns.`politik`			= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`politik` 			, NULL  )	AS 	politik			,
						IF ( ns.`sex_interesse` 	= '. SICHTBARKEIT_JEDER .' OR ( ns.`sex_interesse` 		= '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2 ) OR ( n.`id` = '. $viewer_id .' ) , n.`sex_interesse` 	, NULL  )	AS 	sex_interesse		
					FROM `nutzer` AS n
					LEFT JOIN `nutzer_sichtbarkeit` AS ns 	ON ( n.`id` = ns.`nutzer_id` )
					LEFT JOIN `geschlechter` 		AS g 	ON ( n.`geschlechter_id` = g.`id` )
					WHERE n.`id` = '. $nutzer_id .'
					';

		$data = Database::getList(Database::query($query));
		$data = $data[0];
		
		// Sprachen
		
		$query_sprachen = 	'SELECT s.`sprache`
							FROM `nutzer_sprachen` AS ns
							LEFT JOIN `sprachen` AS s ON ( s.`id` = ns.`sprachen_id` )
							WHERE 	ns.`nutzer_id` = '. $nutzer_id .'
							AND		s.`status` = '. STATUS_AKTIVIERT .'
							AND 	ns.`status` = '. STATUS_AKTIVIERT .'
							AND		( ns.`sichtbarkeit` = '. SICHTBARKEIT_JEDER .' OR 
									( '. $status .' = 2 AND ns.`sichtbarkeit` = '. SICHTBARKEIT_FREUNDE .' ) OR 
									( ns.`nutzer_id` = '. $viewer_id .' ) )
							';
		$data_sprachen = Database::getList(Database::query($query_sprachen));
		
		foreach ($data_sprachen as $value)
		{
			$data['sprachen'][] = $value['sprache'];
		}
		
		
		// Hobbys
		
		$query_hobbys = '
						SELECT `hobby`
						FROM `nutzer_hobbys` AS nh
						WHERE
							nh.`nutzer_id` = '. $nutzer_id .'
						AND nh.`status` = '. STATUS_AKTIVIERT .'
						AND ( nh.`sichtbarkeit` = '. SICHTBARKEIT_JEDER .' OR
							  ( nh.`sichtbarkeit` = '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2  ) OR
							  ( nh.`nutzer_id` = '. $viewer_id .' ) )
						';
		
		$data_hobbys = Database::getList(Database::query($query_hobbys));
		
		foreach ($data_hobbys as $value)
		{
			$data['hobbys'][] = $value['hobby'];
		}
		
		
		// Kontaktdaten
		
		$query_hobbys = '
						SELECT `dienst`, `kontaktdaten`
						FROM `nutzer_kontaktdaten` AS nk
						WHERE
							nk.`nutzer_id` = '. $nutzer_id .'
						AND nk.`status` = '. STATUS_AKTIVIERT .'
						AND ( nk.`sichtbarkeit` = '. SICHTBARKEIT_JEDER .' OR
							  ( nk.`sichtbarkeit` = '. SICHTBARKEIT_FREUNDE .' AND '. $status .' = 2  ) OR
							  ( nk.`nutzer_id` = '. $viewer_id .' ) )
						';
		
		$data_kontaktdaten = Database::getList(Database::query($query_hobbys));
		
		foreach ($data_kontaktdaten as $key => $value)
		{
			$data['kontaktdaten'][] = $value;
		}
		
		return $data;
	}
}

?>