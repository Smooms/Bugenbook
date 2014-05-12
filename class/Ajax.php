<?php 

class Ajax
{
	
	public function __construct()
	{
		switch ($_REQUEST['bereich'])
		{
			case 'beitraege':
				$this->beitraege();
				break;
			
			case 'profil_timeline':
				
				break;
				
			default:
				break;
		}
	}
	
	protected function beitraege()
	{
		$Beitraege = new Beitraege();
		
		switch ($_REQUEST['action'])
		{
			case 'delete': 
				
				$data = $Beitraege->deactivateBeitrag($_REQUEST['beitrag_id'], $_SESSION['nutzer']['id']);
				echo json_encode($data);
				
				break;
			
			case 'up':
				
				$data = $Beitraege->upVoteBeitrag($_REQUEST['beitrag_id'], $_SESSION['nutzer']['id']);
				echo json_encode($data);
				
				break;
				
			case 'down':
			
				$data = $Beitraege->downVoteBeitrag($_REQUEST['beitrag_id'], $_SESSION['nutzer']['id']);
				echo json_encode($data);
			
				break;
			
			case 'update':
				
				ob_start();
				$Content = new Content();
				$Content->getTimeline();
				$data = ob_get_contents();
				ob_end_clean();
				echo json_encode($data);
				
				break;
		}
		
	}
	
	protected function profil_timeline()
	{
		
	}
}