<?php
/**
 * 
 * @author Patrick Emonds
 *
 */
class Content
{
	public function header()
	{		
		?>
			<div id="head">
				<div class="headcontend">
					<div class ="headerlogo">
						<a href="<?php echo URL_ROOT?>"><?php echo SITE_NAME;?></a>
					</div>
					<div class="menu">
					<?php 
						$name = $_SESSION['nutzer']['vorname'] . " " . $_SESSION['nutzer']['nachname'];
						if (strlen($name) > 20)
						{
							$name = htmlspecialchars(substr($name, 0, 20)) . "...";
						}
					?>
						<div class="menbuttons">
							<ul>
								<li class="buttonsetting">
									<img src="<?php echo URL_ROOT;?>/images/setting.png"/>
									<ul class="mendrop">
										<li><a href="<?php echo URL_ROOT . '/settings/' ;?>">Einstellungen</a></li>
										<li><a href="<?php echo URL_ROOT . '/logout.php' ;?>">Abmelden</a></li>
									</ul>
								</li>
								<li class="button">
									<img src="<?php echo URL_ROOT;?>/images/timeline.png"/>
								</li>
								<li class="button">
									<img src="<?php echo URL_ROOT;?>/images/pm.png"/>
								</li>
								<li class="button">
									<a href=""><img src="<?php echo URL_ROOT;?>/images/friend.png"/></a>
								</li>
							</ul>
							<div class="menbuttons_name">
								<?php $this->getPicture($_SESSION['nutzer']['id'], $_SESSION['nutzer']['profilbild'], 20, $_SESSION['nutzer']['geschlecht'])?>
								<div style="float: right;">
									<a href="<?php echo URL_ROOT . "/nutzer/" . $_SESSION['nutzer']['id'] . "/";?>"><?php echo $name;?></a> &nbsp; |
								</div>
							</div>
						</div>
					</div>
					<div class="searchbar">
						<form action="<?php echo URL_ROOT;?>/suche/" method="get">
							<input type="text" name="search" style="width: 281px; margin-left: 38px;">
							<div class="searchbarbutton">
								<button style="width: 30px; height: 23px; background: white; margin-top: 0px;" 
								type="submit">
									<img src="<?php echo URL_ROOT;?>/images/search.png" style="height: 22px; "/>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php
	}
	
	public function leftMenu()
	{		
		?>
			<div class="menubar">
				<?php $this->checkLeftMenu();?>
			</div>
		<?php
	}
	
	protected function rightMenu()
	{
		$this->rightMenuTimeline();
	}
	
	public function middlebox()
	{
		$this->borderContent();
		
	}
	
	protected function checkLeftMenu()
	{
		$urlobject = Url::getURLObject();
		if ($urlobject->url_1 == 'timeline' or $urlobject->url_1 == 'bugenbook')
		{
			return $this->leftMenuTimeline();
		}
		
		if($urlobject->url_2 == "nutzer" OR ($urlobject->url_3 == "nutzer" AND $urlobject->url_1 == "info"))
		{
			if($urlobject->url_3 == "nutzer")
			{
				$uid = $urlobject->url_2;
			}
			else 
			{
				$uid = $urlobject->url_1;
			}
			return $this->leftMenuUser($uid);
		}
	}
	
	protected function checkRightMenu()
	{
		$urlobject = Url::getURLObject();
		if ($urlobject->url_1 == 'timeline' or $urlobject->url_1 == 'bugenbook')
		{
			return $this->rightMenuTimeline();
		}
		
		if($urlobject->url_1 == "info")
		{
			$uid = $urlobject->url_2;
			return $this->userInfo($uid);
		}
		
		if($urlobject->url_2 == "nutzer")
		{
			return $this->userTimeline($urlobject->url_1);
		}
	}
	
	protected function leftMenuTimeline()
	{
		?>
			<div id="leftMenu">
			<?php 
				$profil = new Profil();
			
			
			foreach($profil->getGeneralInfo($_SESSION['nutzer']['id']) as $value)
			{
				if(is_file(DIR_MEDIA . '/user/' . $_SESSION['nutzer']['id'] . '/' . $value['profilbild']))
				{
					?>
							<img src="<?php echo URL_MEDIA . '/user/' . $_SESSION['nutzer']['id'] . '/' . $value['profilbild'];?>" style="width: 50px;"/>
					<?php 
					
					
				}
				else 
				{
					if($value['geschlecht'] == "männlich")
					{
					?>
						<img src="<?php echo URL_IMAGES . '/avatarm.jpg';?>" style="width: 50px;"/>
					<?php
					}
					
					if($value['geschlecht'] == "weiblich")
					{
					?>
						<img src="<?php echo URL_IMAGES . '/avatarw.jpg';?>" style="width: 50px;"/>
					<?php
					}
				}				
				echo $value['vorname'] . " " . $value['nachname']; 
			}
				?>
				<br/><br/>
				FAVORITEN
				<ul>
					<li style="background-color: #d8dfea; font-weight: bold;"><a href="<?php echo URL_ROOT;?>" id="neu">Neuigkeiten</a></li>
					<li><a href="<?php echo URL_ROOT;?>/pm/">Nachrichten</a></li>
					<li><a href="<?php echo URL_ROOT;?>/termine/">Veranstaltungen</a></li>
					<li><a href="<?php echo URL_ROOT;?>/nutzer/<?php echo $_SESSION['nutzer']['id'];?>/bilder/">Fotos</a></li>
					<li>Freunde finden</li>
				</ul>
				
				GRUPPEN
				<?php $this->drawGruppen();?>
			</div>
		<?php
	}
	
	protected function rightMenuTimeline()
	{
		?>
			<div class="termine">
				Termine<br/>
				<div style="font-size: 12px;">
					Keine Termine... <br/>
				</div>
				<hr/>
				<div style="font-size: 10px;">
					Bugenbook © 2014 <br/>
					<a href="<?php echo URL_ROOT;?>/Datenschutz/">Datenschutz</a> · 
					<a href="<?php echo URL_ROOT;?>/Impressum/">Impressum/Nutzungsbedingungen</a>
				</div>
			</div>
		<?php
	}
	
	protected function borderContent()
	{
		?>
			<div class="bordercontend">
				<?php $this->checkRightMenu();
					$this->drawStatusform();
					?>
			</div>
		<?php
	}
	
	protected function drawGruppen()
	{
		?>		
			<ul>
		<?php
		
		foreach(Gruppen::getRand($_SESSION['nutzer']['id'], 6) as $value)
		{
 
			$gruppe = $value['gruppenname'];
			if (strlen($gruppe) > 16)
			{
				$gruppe = substr(htmlspecialchars($gruppe), 0, 16) . "...";
			}
		?>
				<li>
					<a href="<?php echo URL_ROOT; ?>/gruppen/<?php echo $value['id'];?>/">
						<?php echo htmlspecialchars($gruppe)?>
					</a>
				</li>
			<?php
		}
		?>
			<li>Gruppe gründen</li>
			</ul>
		<?php
	}
	
	protected function drawStatusform()
	{
		$urlobject = Url::getURLObject();
		if ($urlobject->url_1 == 'timeline' or $urlobject->url_1 == 'bugenbook')
		{			
		?>
			<link rel="stylesheet" href="style/timeline.css" type="text/css">
			<div class="statusfeld">
				<form action="<?php echo URL_ROOT;?>/" method="post">
					<textarea name="text" style="width: 380px; resize: none; " placeholder="Was machst du gerade?"></textarea>
					<div style="margin-left: 227px;">
						<select name="sichtbarkeit">
							<option value="<?php echo BEITRAG_SICHTBARKEIT_JEDER ?>">Jedem</option>
							<option value="<?php echo BEITRAG_SICHTBARKEIT_FREUNDE ?>">Freunden</option>
							<option value="<?php echo BEITRAG_SICHTBARKEIT_ICH ?>">nur Ich</option>
						</select>
						<input type="submit" name="status" value="Posten" style="background-color: #3B5998; color: white; border-color: black; height: 30px;">
					</div>
				</form>
				<hr style="color: lightgrey;">
			</div>
			<div class="timeline">
				<?php $this->getTimeline();?>
			</div>
		<?php
		}
	}
	
	protected function getPicture($id, $bildname, $breite, $geschlecht)
	{
		if(is_file(DIR_MEDIA . '/user/' . $id . '/' . $bildname))
		{	
			?>
			<img src="<?php echo URL_MEDIA . '/user/' . $id . '/' . $bildname;?>" style="width: <?php echo $breite;?>;"/>
			<?php 					
		}
		else 
		{
			if($geschlecht == "1" OR $geschlecht == "männlich")
			{
			?>
				<img src="<?php echo URL_IMAGES . '/avatarm.jpg';?>" style="width: <?php echo $breite;?>;"/>
			<?php
			}
			
			elseif($geschlecht == "2" OR $geschlecht == "weiblich")
			{
			?>
				<img src="<?php echo URL_IMAGES . '/avatarw.jpg';?>" style="width: <?php echo $breite;?>;"/>
			<?php
			}
		}
	}
	
	public function getTimeline()
	{
		$beitraege = new Beitraege();
		
		foreach($beitraege->getBeitraege($_SESSION['nutzer']['id']) as $value)
		{
			?>
			<div id="beitrag_<?php echo $value['beitrag_id']?>">
			
				<div class="timeline_profile_picture">
					<?php
					$this->getPicture($value['nutzer_id'], $value['profilbild'], 50, $value['geschlechter_id']);
					?>
				</div>
				<?php 
				$name = $value['vorname'] . " " . $value['nachname'];
				
				if (strlen($name) > 20)
				{
					$name = htmlspecialchars(substr($name, 0, 40)) . "...";
				}
				
				?>
				<div class="timeline_content">
					<?php 
					if($value['nutzer_id'] == $_SESSION['nutzer']['id'] OR $_SESSION['nutzer']['nutzer_art'] <= NUTZER_ADMIN)
					{
						?>
						<div style="float: right;">
							<img name="<?php echo $value['beitrag_id']?>" id="delete" src="<?php echo URL_IMAGES . '/loeschen.png';?>" style="height: 15px;"/>
						</div>
						<?php
					}
					?>
					<div style="font-size: 11pt; height: 27px;"> 
						<a href="<?php echo URL_ROOT . '/nutzer/' . $value['nutzer_id'] . '/';?>"><?php echo htmlspecialchars($name);?></a>
					</div>
					<div style="word-wrap: break-word;">
						<?php echo htmlspecialchars($value['beitrag']);?>
					</div>
					<div style="font-size: 8pt; padding-top: 8px;">
						<div class="up <?php echo ($value['self_plus'] === true) ? ' up_yes ' : '';
							 ?>" id="up" name="<?php echo $value['beitrag_id'];
							 ?>" style="float: left;"> 
							 <img src="<?php echo URL_ROOT . '/images/thumb_up.png';?>"/> 
							 <div style="margin-top: -1px; float: right;">(<span id="up_<?php echo $value['beitrag_id'];?>" ><?php echo $value['plus']  ?></span>)
							 </div>
						 </div>
						<div style="float: left;">&nbsp;|&nbsp;</div>
						<div class="down <?php echo ($value['self_minus'] === true) ? ' down_yes ' : ''; 
							?>" id="down" name="<?php echo $value['beitrag_id'];
							?>" style="float: left;"> 
							<img src="<?php echo URL_ROOT . '/images/thumb_down.png';?>"/> 
							<div style="margin-top: -1px; float: right;">(<span id="down_<?php echo $value['beitrag_id'];?>" ><?php echo $value['minus']  ?></span>)
							</div>
						</div>
						<div style="float: left;">&nbsp;|&nbsp;</div>
						<div >
							<a id="comment" name="<?php echo $value['beitrag_id'];?>" href="/entwicklung/Bugenbook/ajax/?bereich=beitraege&action=comments&beitrag_id=<?php echo $value['beitrag_id']?>">
								<img src="<?php echo URL_ROOT . '/images/comment.png';?>" style="float: left;"/><div style="margin-top: -1px; float: none;">(<?php echo $value['kommentare'];?>)</div>
							</a>
						</div>
					</div>
				</div>
					<hr style="color: lightgrey;">
			</div>
			<?php
		}
		
	}
	
	public function leftMenuUser($url_1)
	{
		$profil = new Profil();
			?>
			<div id="leftmenu">
			<?php
			foreach($profil->getGeneralInfo($url_1) as $value)
			{
				$this->getPicture($url_1, $value['profilbild'], "130px", $value['geschlecht']);
				?>
					<br/><br/>
				<?php 
				echo $value['vorname'] . " " . $value['nachname']; 
			}
				$urlobject = Url::getURLObject();
				?>
			<ul>
			 <?php echo ($urlobject->url_1 == "info") ? '<li><a href="' . URL_ROOT . '/nutzer/' . $url_1 . '/">Timeline</a></li>' : '<li><a href="info/">Info</a></li>'?>
			 <li>Nachricht senden</li>
			 <li>Freundschaftsanfrage</li>
			 <li>Bilder</li>
			 <li>Freunde</li>
			 <li>Gruppen</li>
			</ul>
			</div>
		<?php
	}

	public function userTimeline($uid)
	{
		$beitrag = new Beitraege();
		$urlobject = Url::getURLObject();
		
		
		foreach($beitrag->getEigeneBeitraege($uid) as $value)
		{
			?>
						<div id="beitrag_<?php echo $value['beitrag_id']?>">
						
							<div class="timeline_profile_picture">
								<?php
								$this->getPicture($value['nutzer_id'], $value['profilbild'], 50, $value['geschlechter_id']);
								?>
							</div>
							<?php 
							$name = $value['vorname'] . " " . $value['nachname'];
							
							if (strlen($name) > 20)
							{
								$name = htmlspecialchars(substr($name, 0, 40)) . "...";
							}
							
							?>
							<div class="timeline_content">
								<?php 
								if($value['nutzer_id'] == $_SESSION['nutzer']['id'] OR $_SESSION['nutzer']['nutzer_art'] <= NUTZER_ADMIN)
								{
									?>
									<div style="float: right;">
										<img name="<?php echo $value['beitrag_id']?>" id="delete" src="<?php echo URL_IMAGES . '/loeschen.png';?>" style="height: 15px;"/>
									</div>
									<?php
								}
								?>
								<div style="font-size: 11pt; height: 27px;"> 
									<a href="<?php echo URL_ROOT . '/nutzer/' . $value['nutzer_id'] . '/';?>"><?php echo htmlspecialchars($name);?></a>
								</div>
								<div style="word-wrap: break-word;">
									<?php echo htmlspecialchars($value['beitrag']);?>
								</div>
								<div style="font-size: 10pt; padding-top: 8px;">
									<div class="up <?php echo ($value['self_plus'] === true) ? ' up_yes ' : ''; ?>" id="up" name="<?php echo $value['beitrag_id'];?>" style="float: left;">Gefällt mir (<span id="up_<?php echo $value['beitrag_id'];?>" ><?php echo $value['plus']  ?></span>)</div>
									<div style="float: left;">&nbsp;|&nbsp;</div>
									<div class="down <?php echo ($value['self_minus'] === true) ? ' down_yes ' : ''; ?>" id="down" name="<?php echo $value['beitrag_id'];?>" style="float: left;"> Gefällt mir nicht (<span id="down_<?php echo $value['beitrag_id'];?>" ><?php echo $value['minus']  ?></span>)</div>
									<div style="float: left;">&nbsp;|&nbsp;</div>
									<div class="comment" id="comment<?php echo $value['beitrag_id'];?>">Kommentieren</div>
								</div>
							</div>
								<hr style="color: lightgrey;">
						</div>
						<?php
		}
	}
	
	protected function userInfo($uid)
	{
		$profil = new Profil();
		$alledaten = $profil->getAllInfo($uid, $_SESSION['nutzer']['id']);
		
		$monat = array(	"1"		=>	"Januar",
						"2"		=>	"Februar",
						"3"		=>	"März",
						"4"		=>	"April",
						"5"		=>	"Mai",
						"6"		=>	"Juni",
						"7"		=>	"Juli",
						"8"		=>	"August",
						"9"		=>	"September",
						"10"	=>	"Oktober",
						"11"	=>	"November",
						"12"	=>	"Dezember"			
						);
		?>
			<link rel="stylesheet" href="<?php echo URL_ROOT?>/style/profil.css" type="text/css">
		<?php
		
		?>
		<div id="infos">
			<div class="ueberschrift">Bildungsgang</div>
			<?php echo (!empty($alledaten['beruf'])) ? '<div class="bezeichner">Beruf</div><div class="text"> ' . $alledaten['beruf'] . '</div>' : '';?>
			<hr style="color: lightgrey; width: 585px;"/>
			<div class="ueberschrift">Allgemeines</div>
			<?php echo (empty($alledaten['geburtstdatum'])) ? '<div class="bezeichner">Geburtsdatum</div><div class="text"> ' . date("d.", strtotime($alledaten['geburtsdatum'])) . " " . $monat[date("n", strtotime($alledaten['geburtsdatum']))] . '</div>' : '';?>
			<?php echo (empty($alledaten['geburtstdatum'])) ? '<div class="bezeichner">Geburtsjahr</div><div class="text"> ' . date("Y", strtotime($alledaten['geburtsdatum'])) . '</div>' : '';?>
			<?php echo (!empty($alledaten['geschlecht'])) ? '<div class="bezeichner">Geschlecht</div><div class="text"> ' . $alledaten['geschlecht'] . '</div>' : '';?>
			<?php echo (!empty($alledaten['sprachen'])) ? '<div class="bezeichner">Sprachen</div>' : '';?>
			<div class="text">
				<?php 
					if(!empty($alledaten['sprachen']))
					{
						$temp = count($alledaten['sprachen']);
						foreach($alledaten['sprachen'] as $key => $sprache)
						{
							if($key == $temp - 2 or $temp == 1)
							{
								echo $sprache . " ";
							}
							elseif($key != $temp - 1)
							{
								echo $sprache . ", ";
							}
							else 
							{
								echo "und " . $sprache;
							}
						}
					}
				?>
			</div>
			<?php echo (!empty($alledaten['religion'])) ? '<div class="bezeichner">Religiöse Ansichten</div><div class="text"> ' . $alledaten['religion'] . '</div>' : '';?>
			<?php echo (!empty($alledaten['politik'])) ? '<div class="bezeichner">Politische Einstellung</div><div class="text"> ' . $alledaten['politik'] . '</div>' : '';?>
			
			<hr style="color: lightgrey; width: 585px;"/>
			
			<div class="ueberschrift">Kontakt</div>
			<?php echo (!empty($alledaten['strasse'])) ? '<div class="bezeichner">Straße</div><div class="text"> ' . $alledaten['strasse'] . ' ' . $alledaten['hausnummer'] . '</div>' : '';?>
			<?php echo (!empty($alledaten['stadt'])) ? '<div class="bezeichner">Wohnort</div><div class="text"> ' . $alledaten['stadt'] . '</div>' : '';?>
			<div class="bezeichner">Bugenbook</div><div class="text"> <a href="<?php echo URL_ROOT . '/nutzer/' . $uid . '/' ?>"><?php echo URL_ROOT . '/nutzer/' . $uid . '/' ?></a></div>
		</div>
		<?php
		
	}
	
	protected function userInfoEinstellungen($uid)
	{
		$profil = new Profil();
		$alledaten = $profil->getAllInfo($uid, $_SESSION['nutzer']['id']);
		
		$monat = array(	"1"		=>	"Januar",
						"2"		=>	"Februar",
						"3"		=>	"März",
						"4"		=>	"April",
						"5"		=>	"Mai",
						"6"		=>	"Juni",
						"7"		=>	"Juli",
						"8"		=>	"August",
						"9"		=>	"September",
						"10"	=>	"Oktober",
						"11"	=>	"November",
						"12"	=>	"Dezember"			
						);
		?>
		<link rel="stylesheet" href="<?php echo URL_ROOT?>/style/profil.css" type="text/css">
	
		<div id="infos">
			<div class="ueberschrift">Bildungsgang</div>
				<form action="" method="post">
					<div class="bezeichner">Beruf</div><div class="text"> <input type="text" name="beruf" value="<?php echo $alledaten['beruf'];?>"/></div>
					<hr style="color: lightgrey; width: 585px;"/>
				<div class="ueberschrift">Allgemeines</div>
					<div class="bezeichner">Geburtsdatum</div><div class="text"> <input type="text" name="geburtstag" value="<?php echo date("d.", strtotime($alledaten['geburtsdatum']));?> <?php echo date("n", strtotime($alledaten['geburtsdatum']));?>"/></div>'
					<div class="bezeichner">Geburtsjahr</div><div class="text"><input type="text" name="geburtsjahr" value="<?php echo date("Y", strtotime($alledaten['geburtsdatum']));?>"/></div>
					<div class="bezeichner">Geschlecht</div><div class="text"><input type="text" name="geschlecht" value="<?php echo $alledaten['geschlecht'] ?>"/></div>
					<div class="bezeichner">Sprachen</div>
					<div class="text">
						<?php 
							if(!empty($alledaten['sprachen']))
							{
								$temp = count($alledaten['sprachen']);
								foreach($alledaten['sprachen'] as $key => $sprache)
								{
									echo '<div style="float: left; width: 100px;">' . $sprache . '</div>' . '<div name="' . $sprache . '">X</div>';
								}
							}
						?>
					</div>
					<div class="bezeichner">Religiöse Ansichten</div><div class="text"> ' . $alledaten['religion'] . '</div>' : '';?>
					<?php echo (!empty($alledaten['politik'])) ? '<div class="bezeichner">Politische Einstellung</div><div class="text"> ' . $alledaten['politik'] . '</div>' : '';?>
					
					<hr style="color: lightgrey; width: 585px;"/>
					
				<div class="ueberschrift">Kontakt</div>
					<?php echo (!empty($alledaten['strasse'])) ? '<div class="bezeichner">Straße</div><div class="text"> ' . $alledaten['strasse'] . ' ' . $alledaten['hausnummer'] . '</div>' : '';?>
					<?php echo (!empty($alledaten['stadt'])) ? '<div class="bezeichner">Wohnort</div><div class="text"> ' . $alledaten['stadt'] . '</div>' : '';?>
					<div class="bezeichner">Bugenbook</div><div class="text"> <a href="<?php echo URL_ROOT . '/nutzer/' . $uid . '/' ?>"><?php echo URL_ROOT . '/nutzer/' . $uid . '/' ?></a></div>
				</form>
			</div>
		<?php
	}
}

?>