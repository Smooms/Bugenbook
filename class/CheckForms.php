<?php

class CheckForms
{
	public function processInput()
	{
		$beitraege = new Beitraege();
		
		if($_REQUEST['status'] == "Posten")
		{
			$beitraege->insertNewBeitrag($_SESSION['nutzer']['id'], $_REQUEST['text'], $_REQUEST['sichtbarkeit']);
		}
	}
}

?>