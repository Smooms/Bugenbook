<?php

class Standard {
	
	protected $e_mail;
	protected $password;
	
	protected $table;
	protected $sort_order;
	
	protected $request;
	protected $session;
	
	protected $data = array();
	protected $data_2 = array();
	
	protected $id;
	protected $search;
	protected $aktion;
	protected $name;
	
	protected $url_objekt;
	
	protected $uploaded_files_path;
	protected $project_path;
	
	protected $sql_rechte;
	
	protected $einschub;
	
	
	function __construct()
	{
		$this->project_path = dirname(dirname(__FILE__)) . '\\';
		$this->project_path = str_replace('\\', '\\\\', $this->project_path);
		
		$this->uploaded_files_path = $this->project_path . 'pics\\\\';
		
	#	Common::dump($this->project_path);
	#	Common::dump($this->uploaded_files_path);
	}
	
	
	/*
	 * Weist den Variablen der Klasse Werte aus einem Array($array) zu.
	 */
	public function setVariables($request = '', $session = '')
	{
	#	Common::dump($request, 'setVariables');
		
		if (!empty($session))
		{
			foreach ($session as $key => $value)
			{
				$this->$key = $value;
			}
		}
		
		if (!empty($request))
		{
			foreach ($request as $key => $value)
			{
				$this->$key = $value;
			}
		}
		$temp = Url::getURL();
		$this->url_objekt = Url::getURLObject($temp->pfad);
		
		$this->request = $request;
		$this->session = $session;
		return 1;
	}
	
	
	/*
	 * Weist einer 'protected' Variable($var) der Klasse einen Wert($value) zu.
	 */
	public function setVariable($var, $value)
	{
		$this->$var = $value;
		return 1;
	}
	
	
	/*
	 * Gibt die Daten der Tabelle( $this->table ) zurück. 
	 */
	public function getData()
	{
		$sql = 'SELECT * FROM `'. $this->table .'`';

		$where = ' WHERE ';
		$sql2 = $sql2 . ' `found` = 1 ';

		$sql = $sql . $where . $sql2;
		$sql = $sql . ' ORDER BY `sortierung` ';
	#	Common::dump($sql, 'getData');
		$data = Database::getList(Database::query($sql));
		return $data;
	}
	
	
	/*
	 * Gibt den Datensatz mit der ID( $this->id ) zurück.
	 */
	public function getDataByID()
	{
		$sql = ' SELECT * FROM `'. $this->table .'` WHERE `id` = '. $this->id .' ';
		$data = Database::getList(Database::query($sql));
	#	Common::dump($data, 'getDataByID');
		return $data['0'];
	}
	
	
	/*
	 * Gibt den Datensatz mit der ID( $id ) der Tabelle ( $table )zurück.
	 */
	public function getDataByIDFromTable($table = false, $id = false)
	{
		if ($table == false){$table = $this->table;}
		
		$sql = ' SELECT * FROM `'. $table .'` ';
		if ($id !== false){ $sql = $sql . ' WHERE `id` = '. $id .' ';}
	#	Common::dump($sql);
		$data = Database::getList(Database::query($sql));
		return $data;
	}
	
	
	/*
	 * Speichert die Daten($data) in der Tabelle($table). Bei Angabe der ID($this->id) werden die Daten in der Zeile mit dieser ID gespeichert.
	 * 
	 * return 1
	 */
	public function saveData($data)
	{
		/*
		 * Wenn keine ID( $this->id ) gesetzt ist, wird eine neuer Datensatz angelegt.
		 */
	#	Common::dump($data, 'saveData');
	#	Common::dump($this->id, 'saveData');
		if (empty($this->id))
		{
			$this->id = $this->setData();
			$this->setSortierung();
		}
		/*
		 * Der Datensatz( $this->id ) wird aktualisiert
		 */
		$this->updateData($data);
		return 1;
	}
	
	
	/*
	 * Legt einen neuen Datensatz in einer Tabelle($this->table) an.
	 * Gibt die ID des neu angelegten Datensatzes zurück.
	 */
	protected function setData()
	{
	#	Common::dump('', 'setData');
		$sql = 'INSERT INTO `'. $this->table .'` Values () ';
		$id = Database::query($sql);
		
		$sql = 'UPDATE `'. $this->table .'` SET `sortierung` = ' . $id . ', `found` = 1 WHERE `id` = ' . $id;
		Database::query($sql);
		
		$this->id = $id;
		return $id;
	}
	
	
	/*
	 * Schreibt Daten($data) in einen vorhandenen Datensatz($id) einer Tabelle($table) .
	 */
	protected function updateData($data)
	{
		$sql = 'UPDATE `'. $this->table .'` SET ';
		$komma = '';
		foreach ($data as $key => $value)
		{
			$sql = $sql . $komma . ' `'. $key .'` = "'. $value .'"';
			$komma = ' , ';
		}
		$sql = $sql . ' WHERE `id` = ' . $this->id;
	#	Common::dump($sql);
		Database::query($sql);
	}
	
	
	/*
	 * Aktualisiert die Sortierung eines Datensatzes($this->id) einer Tabelle($thia->table) in eine Richtung($aktion).
	 * 
	 * return -1 Wenn der Datensatz schon ganz oben bzw. ganz unten ist.
	 */
	public function updateSortierung($aktion)
	{
		/*
		 * Holt sich die Daten des zu verändernden Datensatzes( $this->id ).
		 */
		$sql = 'SELECT `id`, `sortierung` FROM `'. $this->table .'` WHERE `id` = ' . $this->id;
		$data1 = Database::query($sql);
		$data1 = Database::getList($data1);
		$data1 = $data1['0'];
		
		/*
		 * Holt sich die Daten des Datensatzes darüber,
		 */
		if ($aktion == 'up')
		{
			$sql = 'SELECT `id`, `sortierung` FROM `'. $this->table .'` WHERE `sortierung` < ' . $data1['sortierung'] . ' ORDER BY `sortierung` DESC LIMIT 0,1';
			$data2 = Database::getList(Database::query($sql));
			$data2 = $data2['0'];
		}
		/*
		 * oder die des Datensatzes darunter.
		 */
		elseif ($aktion == 'down')
		{
			$sql = 'SELECT `id`, `sortierung` FROM `'. $this->table .'` WHERE `sortierung` > ' . $data1['sortierung'] . ' ORDER BY `sortierung` ASC LIMIT 0,1';
			$data2 = Database::getList(Database::query($sql));
			$data2 = $data2['0'];		
		}
		
		
		/*
		 * Wenn es keinen Datensatz darüber bzw. darunter gibt, wird -1 zurückgegeben.
		 */
		if ($data2['id'] == false)
		{
			return -1;
		}
		
		/*
		 * Tauscht die `sortierung` der beiden Datensätze($data1, $data2).
		 */
		$sql1 = 'UPDATE `'. $this->table .'` SET `sortierung` = "'. $data2['sortierung'] .'" WHERE `id` = ' . $data1['id'];
		Database::query($sql1);
		$sql2 = 'UPDATE `'. $this->table .'` SET `sortierung` = "'. $data1['sortierung'] .'" WHERE `id` = ' . $data2['id'];
		Database::query($sql2);
		return 1;
	}
	
	
	/*
	 * Setzt die Sortierung eines Datensatzes($this->id) gleich der Id.
	 */
	protected function setSortierung()
	{
		$sql = ' UPDATE `'. $this->table .'` SET `sortierung` = ' . $this->id . ' WHERE `id` = ' . $this->id;
		Database::query($sql);
		return 1;
	}
	
	
	
	/*
	 * Ändert die Sichtbarkeit eines Datensatzes($this->id) einer Tabelle($this->table), je nach dem vorherigen Stand.
	 */
	public function updateVisible()
	{
	#	Common::dump($this->table);
	#	Common::dump($this->id);
		$sql = 'SELECT `visible` FROM `'. $this->table .'` WHERE `id` = '. $this->id .'';
	#	Common::dump($sql);
		$data = Database::getList(Database::query($sql));
	#	Common::dump($data);
		if ($data['0']['visible'] == 1)
		{
			$sql = 'UPDATE `'. $this->table .'` SET `visible` = 0 WHERE `id` = '. $this->id;
		#	Common::dump($sql);
			Database::query($sql);
		}
		elseif ($data['0']['visible'] == 0)
		{
			$sql = 'UPDATE `'. $this->table .'` SET `visible` = 1 WHERE `id` = '. $this->id;
			Database::query($sql);
		}
		else 
		{
			return -1;
		}
		return 1;
	}
	
	
	/*
	 * 	Setzt die Spalte `found` der Tabelle($this->table) auf 1, wenn der `name` der Suche($this->search) entspricht.
	 * 	Danach wird das Gleiche mit den Eltern gemacht.
	 */
	protected function updateFound()
	{
		$sql = 'SELECT * FROM `'. $this->table .'` WHERE `name` NOT LIKE "%'. $this->search .'%"';
	#	Common::dump($sql);
		$data = Database::query($sql);
		$data = Database::getList($data);
	#	Common::dump($data);
		foreach ($data as $value)
		{
			$sql = 'UPDATE `'. $this->table .'` SET `found` = 0 WHERE `id` = ' . $value['id'];
			Database::query($sql);
			if ($value['parent_id'] == true)
			{
				$this->updateParentsFound($value['parent_id']);
			}
		}
	}
	protected function updateParentsFound($id)
	{
		$sql = 'SELECT * FROM `'. $this->table .'` WHERE `id` = ' . $id;
		$data = Database::query($sql);
		$data = Database::getList($data);
	#	Common::dump($data, 'f');
		$sql = 'UPDATE `'. $this->table .'` SET `found` = 0 WHERE `id` = ' . $id;
		Database::query($sql);
		foreach ($data as $key => $value)
		{
			$this->updateParentsFound($value['parent_id']);
		}
	}
	
	
	/*
	 * Setzt die Spalte `found` wieder auf 1.
	 */
	protected function unsetFound()
	{
	#	Common::dump('', 'unsetFound');
		$sql = 'UPDATE `'. $this->table .'` SET `found` = 1';
		Database::query($sql);
	}
	
	
	public function updatePicUpload($form , $upload)
	{
		if ($form['aktion'] == 'save' AND !empty($upload['upload']['name']))
		{
		#	Common::dump($form, 'updatePicUpload');
		#	Common::dump($this->id, 'updatePicUpload');
			
			$FILES = new Files();
			if (empty($this->id))
			{
				$sql = 'INSERT INTO `'. $this->table .'` SET `found` = 1';
				Database::query($sql);
				
				$sql = 'SELECT `id` FROM `'. $this->table .'` ORDER BY `id` DESC';
				$data = Database::getList(Database::query($sql));
				$data = $data['0'];
				$this->id = $data['id'];
				
				$this->setSortierung();
			#	Common::dump($data, 'updatePicUpload');
			}
			else
			{
				$data = $this->getDataByID();
			}
			
		#	Common::dump($upload, 'updatePicUpload');
		#	Common::dump($data, 'updatePicUpload');
			
			if ($data['upload'] == true)
			{
			#	Common::dump($data['upload_local']);
				$FILES->deleteFile($data['upload_local']);
			}
			
			$file_path_l = $this->uploaded_files_path . $this->url_objekt->bereich . '_' . $data['id'] . '_' . $upload['upload']['name'];
		#	Common::dump($file_path_l);
			move_uploaded_file($upload['upload']['tmp_name'], $file_path_l);
			
			$file_path = 'http://127.0.0.1/entwicklung/cms test/pics/' . $this->url_objekt->bereich . '_' . $data['id'] . '_' . $upload['upload']['name'];
			$data2['upload'] = $file_path;
			
			$file_path_l = $this->uploaded_files_path . $this->url_objekt->bereich . '_' . $data['id'] . '_' . $upload['upload']['name'];
			$data2['upload_local'] = $file_path_l;
			
			$this->saveData($data2);
		}
		elseif ($form['aktion'] == 'delete_upload')
		{
			$this->deletePicUpload();
		}
	}
	
	
	/*
	 * Löscht die Datei des Datensatzes( $this->id ) und aktualisiert den Datenbankeintrag( $this->id ). 
	 */
	protected function deletePicUpload()
	{
		
		$FILES = new Files();
		$data = $this->getDataByID();
		$data = $data['0'];
		
		
	#	$sql = ' SELECT * FROM `'. $this->table .'` WHERE `id` = '. $this->id .' ';
	#	$data = Database::getList(Database::query($sql));
	#	$data = $data['0'];
		/**
		 * $this->getDataById() gibt keine Daten zurück...
		 */
		Common::dump($data, 'deleteDat');
		die;
		$FILES->deleteFile($data['upload_local']);
		
		$data2['upload'] = '';
		$data2['upload_local'] = '';
		
		$this->saveData($data2);
	}
	
	
	/*
	 * Löscht einen Datensatz($id) einer Tabelle($table).
	 */
	public function deleteData()
	{
		$sql = 'DELETE FROM `'. $this->table .'` WHERE `id` = ' . $this->id;
		Database::query($sql);
		
		return 1;
	}
	
	
	/*
	 * Überprüft die Eingaben($this->aktion, $this->request) und ruft die entsprechenden Funktionen auf.
	 */
	public function checkInput($form)
	{
	#	Common::dump($form, 'checkInput', true);
		$this->performAktions($form);
	}
	
	
	/*
	 * Überprüft die Eingaben($this->aktion, $this->request) und ruft die entsprechenden Funktionen auf.
	 */
	public function performAktions($form)
	{
		switch ($form['aktion'])
		{
			case 'save':
			#	Common::dump($form, '$form');
				unset($form['id']);
				unset($form['aktion']);
				$this->saveData($form);
				break;
			case 'up':
			#	Common::dump('', 'checkInput sortierung');
				$this->updateSortierung($this->aktion);
				break;
			case 'down':
			#	Common::dump('', 'checkInput sortierung');
				$this->updateSortierung($this->aktion);
				break;
			case 'vision':
			#	Common::dump('', 'checkInput vision');
				$this->updateVisible();
				break;
			case 'delete':
			#	header('Location: delete_massage.php');
				$this->deleteData();
				break;
			case 'logout':
			#	header('Location: delete_massage.php');
				$this->logout();
				break;
			case 'delete_msg':
			#	header('Location: delete_massage.php');
				$this->drawDeleteMsg();
				break;
			default:
			#	Common::dump('', 'check Input default');
				break;
		}
		
	}
	
	
	
	protected function logout()
	{
		session_destroy();
		
		header('Location: ' . $this->url_objekt->url);
	}
	
	
	
	/*
	 *  Listes alle Daten einer Tabelle($this->table) auf.
	 */
	public function drawList()
	{
	#	ob_start();
			require 'snippets/form_search.php';
			?><a href="http://127.0.0.1/entwicklung/cms test/sites/edit.php?aktion=create"><img src="/entwicklung/cms test/icons/page_white_add.png" style="height: 20px;" onmouseover="<?php echo 'Neuer Seite';?>" /></a> <br />
			
			<table>
					<tr>
						<th colspan="2">
							Seitenname
						</th>
						<th>
							Aktionen
						</th>
					</tr>
					<?php 
						if ($this->search == true)
						{
							$this->updateFound();
						}
						$data = $this->getData();
					#	Common::dump($data);
						foreach ($data as $value)
						{
							if ($value['visible'] == 1)
							{
								$color = 'green';
							}
							elseif ($value['visible'] == 0)
							{
								$color = 'yellow';
							}
							?>
								<tr>
									<td style="background-color: <?php echo $color?>">
									</td>
									<td>
										<a href="http://127.0.0.1/entwicklung/cms test/<?php echo $this->url_objekt->bereich?>_content/index.php?aktion=content&id=<?php echo $value['id'];?>"><?php echo $value['name'];?></a>
										<input type="hidden" name="id" value="<?php echo $value['id']?>"/>
									</td>
									<?php require 'snippets/actions.php'; ?>
								</tr>
							<?php
						}
					?>
			</table>
		<?php
			if ($this->search == true)
			{
				$this->unsetFound();
			}
			$content = ob_get_contents();
	#	ob_end_clean();
	}
	
	
	/*
	 * Zeichnet das Formular zum Ändern oder Erstellen von Seiteninhalten.
	 */
	public function drawEdit()
	{
		
		if ($this->aktion == 'edit')
		{
			$data = $this->getDataByID();
		}
	#	Common::dump($data);
		?>
			<form action="compute.php" method="post">
				<input type="hidden" name="id" value="<?php echo $data['0']['id']?>"/>
				<table class="no_border">
					<tr>
						<td class="no_border">
							Seitenname:
						</td>
						<td class="no_border">
							<input name="name" value="<?php echo $data['0']['name']?>"/>
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
	
	
	/*
	 * 
	 */
	public function drawDeleteMsg()
	{
		require 'snippets/delete_confirmation.php';
	}
}

?>