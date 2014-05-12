<?php
	
class Funktionen
{
	public $loginname;
	public $passwort;
	public $page;
	public $info;
	public $request;
	public $session;
	
	public $einschub;
	public $data;
	public $id;
	
	public function createUrl()
	{
	#	Common::dump($this->request);
		$string = Common::normalizeString($this->request['name']);
		$url = $string;
		$data = Database::getList(Database::query('SELECT `parent_id` FROM `sites` WHERE `id` = "'. $this->request['id'] .'"'));
	#	Common::dump($data);
		$parent_id = $data['0']['parent_id'];
		while ($parent_id > 0)
		{
			$data = $this->getParent($parent_id);
		#	Common::dump($data);
			$parent_id = $data['parent_id'];
			$name = Common::normalizeString($data['name']);
			$url = $name . '/' . $url;
		}
	#	$url = 'http://127.0.0.1/entwicklung/CMS/' . $url;
	#	Common::dump($url);
		$numrows = Database::getNumRows(Database::query('SELECT `id` FROM `sites` WHERE `url` = "'. $url .'"'));
		if ($numrows > 0)
		{
			$url = $url . 'id=' .$this->request['id'];
		}
		return $url;
	}
	
	public function checkLogin()
	{
	#	Common::dump($this->session['e_mail']);
	#	Common::dump($this->session['password']);
		if ($this->session['e_mail'] AND $this->session['password'])
		{
			$Standard = new Standard();
			$data = $Standard->getData();
		#	Common::dump($data);
			foreach ($data as $value)
			{
				if ($value['e_mail'] = $this->password AND $value['password'] == $this->password)
				{
					$_SESSION['e_mail'] = $this->e_mail;
					$_SESSION['password'] = $this->password;
					return 1;
				}
			}
		}
		return 0;
	}
	
	public function drawPage()
	{
		$this->drawBody();
	}
	
	public function drawHeader()
	{
		?>
			<div class="header">
				Content-Managmentsystem
			</div>
		<?php 
	}
	
	public function drawBody()
	{
		$this->drawContent();	
	}
	
	public function drawMenuLeft()
	{
		foreach ($this->getData('menu') as $value)
		{
			if ($value['position'] == 'left')
			{
			?>
				<a href="/entwicklung/cms test/<?php echo $value['name']?>/list.php"><?php echo $value['mehrzahl']?></a> <br />
			<?php
			}
		}
	}
	
	public function drawContent()
	{
	#	Common::dump($this->page);
		switch ($this->page)
		{
			case 'index':
				$this->drawIndex();
				break;
			case 'sites':
			#	$this->drawsites();
				
				$sites = new sites();
				$sites->request = $this->request;
				$sites->drawSites();
				break;
			case 'create_site':
				$this->drawEditSite();
				break;
			case 'sitemap':
				$this->drawSitemap();
				break;
			case 'sort':
				$this->drawConfirmation();
				break;
			case 'sites_content':
				if ($this->request['aktion'] == 'save_content')
				{
					$sitesEdit = new sites_Content_Edit();
					$sitesEdit->request = $_REQUEST;
					$sitesEdit->saveContent();
				}

				#	Common::dump($this->request, 'drawContent');
					$sitesContent = new sites_Content_Edit();
					$sitesContent->request = $this->request;
					$sitesContent->drawsitesContent();
				break;
			case 'create_content':
				$sitesContent_Edit = new sites_Content_Edit();
				$sitesContent_Edit->request = $this->request;
				$sitesContent_Edit->drawEditContent();
				break;
			default:
				$this->draw404();
				break;
		}
	}
	
	public function draw404()
	{
		$error404 = 'Diese Seite existiert nicht.';
		Common::dump($error404);
	}
	
	public function drawIndex()
	{
		Url::getUrlObjekt();
	}
	
	public function drawConfirmation()
	{
		?>
			<form action="index.php">
				Der Befehl wurde ausgeführt.<br />
				<button>Weiter</button>
			</form>
		<?php
	}
	
	public function drawsites()
	{
		
		?>
			<form action="" method="get">
				<input name="search" value="<?php $this->request['search']?>"/> <button>Suchen</button>
			</form>
			<form action="create_site.php" method="post">
				<button type="submit" style="visibility: hidden;" name="aktion" id="new_site" value="create"></button>
				<label for="new_site">Neue Seite erstellen</label>
			</form>
			<table>
				<thead>
					<tr>
						<th>
							sitesname
						</th>
						<th>
							Aktionen
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$this->drawsites2();
					?>
				</tbody>
			</table>
		<?php
	}
	
	public function drawTableRows($parent_id = '0')
	{
		$this->einschub = $this->einschub + 1;
	#	$data[$parent_id] = $this->drawsites2($parent_id);
		$data[$parent_id] = $this->getSitesFromParent($parent_id);
		Common::dump($parent_id, 'drawTableRows');
		Common::dump($data, 'drawTableRows');
		if ($data[$parent_id] == false)
		{
			return;
		}
		foreach ($data[$parent_id] as $key => $value)
		{
			?>
				<form action="create_site.php" method="get">
					<tr>
						<td>
							<?php
								$this->drawEinschub($this->einschub);
								echo $value['name'];
							?>
							<input type="hidden" name="id" value="<?php echo $value['id']?>"/>
						</td>
						<td>
							<?php 
								$this->drawAktionen();
							?>
						</td>
					</tr>
				</form>
			<?php
		#	Common::dump($value['id'], 'drawTableRows');
			$this->drawTableRows($value['id']);
		}
		$this->einschub = $this->einschub - 1;
	}
	
	public function drawsites2($parent_id = 0)
	{
		if ($this->request['search'] == true)
		{
			$data = $this->getSites('', $parent_id);
		}
		Common::dump($data, 'drawsites2');
		if ($this->request['search'] == true)
		{
			foreach ($data as $key => $value)
			{
				?>
					<form action="create_site.php" method="get">
						<tr>
							<td>
								<?php
									echo $value['name'];
								?>
								<input type="hidden" name="id" value="<?php echo $value['id']?>"/>
							</td>
							<td>
								<?php 
									$this->drawAktionen();
								?>
							</td>
						</tr>
					</form>
				<?php
				$this->drawTableRows($value['id']);
			}
		}
		else
		{
			$this->drawTableRows();
		}
		return $data;
	}
	
	public function drawEditSite()
	{
	#	Common::dump($this->request);
		if ($this->request['aktion'] == 'delete' AND $this->request['delete'] == 'true')
		{
			$sql = 'DELETE FROM `sites` WHERE `id` = '. $this->request['id'];
			Database::query($sql);
			$loc = 'sites/index.php?id=' . $this->request['id'];
			header ("Location: " . $loc); 
			exit;
		}
		elseif ($this->request['aktion'] == 'delete') 
		{
			echo 'Wollen Sie die Seite wirklich löschen?';
			?>	
				<br />
				<a href="http://127.0.0.1/entwicklung/cms test/sites/edit.php?delete=true&aktion=delete&id=<?php echo $this->request['id'];?>">JA</a>
				<a href="http://127.0.0.1/entwicklung/cms test/sites/index.php?">NEIN</a>
			<?php
			return;
		}
		elseif ($this->request['aktion'] == 'doit')
		{
			$loc = 'sites/index.php?id=' . $this->request['id'];
			header ("Location: " . $loc); 
			exit;
			return;
		}
		elseif ($this->request['aktion'] == 'edit')
		{
			$sql = 'SELECT * FROM `sites` WHERE `id` = ' . $this->request['id'];
			$data = Database::query($sql);
			$data = Database::getList($data);
		}
	#	Common::dump($data);
	#	Common::dump($this->request['id']);
		?>
			<form action="" method="post">
				<input type="hidden" name="id" value="<?php echo $data['0']['id']?>"/>
				<table class="no_border">
					<tr>
						<td class="no_border">
							sitesname:
						</td>
						<td class="no_border">
							<input name="name" value="<?php echo $data['0']['name']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Ort:
						</td>
						<td class="no_border">
							<?php 
							$sites = new sites();
							$sites->drawSelectSiteMap($data['0']['parent_id']);
							?>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Sichtbar:
						</td>
						<td class="no_border">
							<label for="visible1"><input id="visible1"  <?php if ($data['0']['visible'] == '1'){ echo ' checked="checked" '; }?> type="radio" name="visible" value="1">Ja</input></label>
							<label for="visible0"><input id="visible0" <?php if ($data['0']['visible'] == '0'){ echo ' checked="checked" '; }?> type="radio" name="visible" value="0">Nein</input></label>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Startseite:
						</td>
						<td class="no_border">
							<label for="index1"><input id="index1" <?php if ($data['0']['index'] == '1'){ echo ' checked="checked" '; }?> type="radio" name="index" value="1">Ja</input></label>
							<label for="index0"><input id="index0" <?php if ($data['0']['index'] == '0'){ echo ' checked="checked" '; }?> type="radio" name="index" value="0">Nein</input></label>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Title:
						</td>
						<td class="no_border">
							<input name="title" value="<?php echo $data['0']['title']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Keywords:
						</td>
						<td class="no_border">
							<input name="keywords" value="<?php echo $data['0']['keywords']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Beschreibung:
						</td>
						<td class="no_border">
							<textarea name="description" ><?php echo $data['0']['description']?></textarea>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Inhalt:
						</td>
						<td class="no_border">
							<textarea name="content" ><?php echo $data['0']['content']?></textarea>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Verlinkung:
						</td>
						<td class="no_border">
							<label for="link_type1"><input id="link_type1" <?php if ($data['0']['link_type'] == '1'){ echo ' checked="checked" '; }?> type="radio" name="link_type" value="1">Interne Verlinkung</input></label><br />
							<label for="link_type2"><input id="link_type2" <?php if ($data['0']['link_type'] == '2'){ echo ' checked="checked" '; }?> type="radio" name="link_type" value="2">Externe Verlinkung</input></label><br />
							<label for="link_type3"><input id="link_type3" <?php if ($data['0']['link_type'] == '3'){ echo ' checked="checked" '; }?> type="radio" name="link_type" value="3">Anker Verlinkung</input></label><br />
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Link:
						</td>
						<td class="no_border">
							<input name="link_adress" value="<?php echo $data['0']['link_adress']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Link Art:
						</td>
						<td class="no_border">
							<label for="link_open1"><input id="link_open1" <?php if ($data['0']['link_open'] == '1'){ echo ' checked="checked" '; }?> type="radio" name="link_open" value="1">Im gleichen Fenster</input></label><br />
							<label for="link_open2"><input id="link_open2" <?php if ($data['0']['link_open'] == '2'){ echo ' checked="checked" '; }?> type="radio" name="link_open" value="2">Neues Fenster</input></label><br />
							<label for="link_open3"><input id="link_open3" <?php if ($data['0']['link_open'] == '3'){ echo ' checked="checked" '; }?> type="radio" name="link_open" value="3">Popup-Fenster</input></label><br />
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Datum vom:
						</td>
						<td class="no_border">
							<input name="date_from" value="<?php echo $data['0']['date_from']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Datum bis:
						</td>
						<td class="no_border">
							<input name="date_to" value="<?php echo $data['0']['date_to']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Uhrzeit von:
						</td>
						<td class="no_border">
							<input name="time_from" value="<?php echo $data['0']['time_from']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
							Uhrzeit bis:
						</td>
						<td class="no_border">
							<input name="time_to" value="<?php echo $data['0']['time_to']?>"/>
						</td>
					</tr>
					<tr>
						<td class="no_border">
						</td>
						<td class="no_border">
							<button name="aktion" value="doit">Speichern</button>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}
	
	public function drawAktionen()
	{
		?>
			<button name="aktion" value="edit">Bearbeiten</button>
			<button name="aktion" value="delete">Löschen</button>
		<?php
	}
	
	public function drawSitemap()
	{
		?>
			<h2>Sitemap</h2>
			<table>
				<?php 
					$this->drawSitemapRows('0');
				?>
			</table>
		<?php
	}
	
	public function drawEinschub($einschub)
	{
		while ($einschub > 0)
		{
			echo '&nbsp;&nbsp;&nbsp;';
			$einschub--;
		}
	}
	
	public function drawSitemapRows($parent_id = '0')
	{
		$this->einschub = $this->einschub + 1;
		$data[$parent_id] = $this->getSitesFromParent($parent_id);
	#	Common::dump($data);
		foreach ($data[$parent_id] as $key => $value)
		{
			if ($value['visible'] == 0)
			{
				return;
			}
			?>
				<form action="create_site.php" method="get">
					<tr>
						<td>
							<?php
								$this->drawEinschub($this->einschub);
								echo $value['name'];
							?>
							<input type="hidden" name="id" value="<?php echo $value['id']?>"/>
						</td>
					</tr>
				</form>
			<?php
		#	Common::dump($value['parent_id']);
			$this->drawSitemapRows($value['id']);
		}
		$this->einschub = $this->einschub - 1;
	}
	
	public function getData($table)
	{
		$sql = 'SELECT * FROM `'. $table .'`';
		$data = Database::getList(Database::query($sql));
		return $data;
	}
	
	public function getSites($visible = '0', $id = '')
	{
		$sql = 'SELECT * FROM `sites` WHERE 1';
		if ($visible == 1)
		{
			$sql = $sql . ' AND `visible` = 1 ';
		}
		if ($this->request['search'] == true)
		{
			$sql = $sql . ' AND `name` LIKE "%'. $this->request['search'] .'%"';
		}
	#	if ($id == true)
	#	{
	#		$sql = $sql . ' AND `id` = ' . $id;
	#	}
		$data = Database::query($sql);
		$data = Database::getList($data);
	#	Common::dump($data, 'getSites');
		if ($this->id == $id)
		{
			$data = $this->getSitesFromParent($id);
		}
		else 
		{
			$data = $this->getSites2($data);
		}
		return $data;
	}
	
	public function getSites2($data)
	{
		$c = 0;
		foreach ($data as $key => $value)
		{
			if ($value['parent_id'] != 0)
			{
				$parent = $this->getDatabaseDataFromId($value['parent_id']);
				if (in_array($parent, $data) == false)
				{
					$data[$key] = $parent;
				}
				else 
				{
					unset($data[$key]);
				}				
				$c = 1;
			}
		}
		if ($c == 1)
		{
			$data = $this->getSites2($data);
		}
	#	Common::dump($c);
	#	Common::dump($data, 'getSites2');
		return $data;
	}
	
	public function getSitesFromParent($parent_id = '0', $visible ='0')
	{
		$sql = 'SELECT * FROM `sites` WHERE `parent_id` = '. $parent_id;
		if ($visible == 1)
		{
			$sql = $sql . ' AND `visible` = 1 ';
		}
	#	if ($this->request['search'] == true)
	#	{
	#		$sql = $sql . ' AND `name` LIKE "%'. $this->request['search'] .'%"';
	#	}
		$data = Database::query($sql);
		$data = Database::getList($data);
	#	Common::dump($data);
		return $data;
	}
	
	public function getDatabaseDataFromId($id)
	{
		$sql = 'SELECT * FROM `sites` WHERE `id` = ' . $id;
		$data = Database::query($sql);
		$data = Database::getList($data);
		return $data['0'];
	}
	
	public function getDataHeadline($table, $name)
	{
		$sql = 'SELECT * FROM `'. $table .'` WHERE `name` = "' . $name .'"';
	#	Common::dump($sql);
		$data = Database::query($sql);
		$data = Database::getList($data);
		return $data['0'];
	}
	
	public function getParent($parent_id)
	{
		$sql = 'SELECT * FROM `sites` WHERE `id` = ' . $parent_id;
		$data = Database::query($sql);
		$data = Database::getList($data);
		return $data['0'];
	}
	
	public function updateDatabase()
	{
	#	Common::dump($this->request);
		$url = $this->createUrl();
		if ($this->request['id'] == true)
		{
			$this->updateDatabaseIndex();
			$sql = 	'UPDATE `sites` 
					SET
					`name` = "'. $this->request['name'] .'",
					`parent_id` = "'. $this->request['parent_id'] .'",
					`url` = "'. $url .'",
					`visible` = "'. $this->request['visible'] .'",
					`index` = "'. $this->request['index'] .'",
					`title` = "'. $this->request['title'] .'",
					`keywords` = "'. $this->request['keywords'] .'",
					`description` = "'. $this->request['description'] .'",
					`content` = "' . $this->request['content'] . '",
					`link_type` = "'. $this->request['link_type'] .'",
					`link_adress` = "'. $this->request['link_adress'] .'",
					`link_open` = "'. $this->request['link_open'] .'",
					`date_from` = "' . $this->request['date_from'] . '",
					`date_to` = "'. $this->request['date_to'] .'",
					`time_from` = "'. $this->request['time_from'] .'",
					`time_to` = "'. $this->request['time_to'] .'"
					WHERE
					`id` = "' . $this->request['id'] . '"
					';
			Database::query($sql);
		}
		else
		{
			$this->updateDatabaseIndex();
			$sql = 	'INSERT INTO `sites` 
					SET
					`name` = "'. $this->request['name'] .'",
					`parent_id` = "'. $this->request['parent_id'] .'",
					`url` = "'. $url .'",
					`visible` = "'. $this->request['visible'] .'",
					`index` = "'. $this->request['index'] .'",
					`title` = "'. $this->request['title'] .'",
					`keywords` = "'. $this->request['keywords'] .'",
					`description` = "'. $this->request['description'] .'",
					`content` = "' . $this->request['content'] . '",
					`link_type` = "'. $this->request['link_type'] .'",
					`link_adress` = "'. $this->request['link_adress'] .'",
					`link_open` = "'. $this->request['link_open'] .'",
					`date_from` = "' . $this->request['date_from'] . '",
					`date_to` = "'. $this->request['date_to'] .'",
					`time_from` = "'. $this->request['time_from'] .'",
					`time_to` = "'. $this->request['time_to'] .'"
					';
			Database::query($sql);
			$sql = 'SELECT `id` FROM `sites` ORDER BY `id` DESC LIMIT 0,1 ';
			$data = Database::getList(Database::query($sql));
			$id = $data['0']['id'];
			$sql = 'UPDATE `sites` SET `sortierung` = `id` WHERE `id` = ' . $id;
			Database::query($sql);
		}
	}
	
	public function updateDatabaseIndex()
	{
		if ($this->request['index'] == '1')
		{
			$sql = 'UPDATE `sites` SET `index` = 0 WHERE `index` = 1';
			Database::query($sql);
		}
	}
	
}
?>