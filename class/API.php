<?php

/**
 * API Methoden
 *
 * @author Steffen Krohn
 */

class API
{
	public $file_name;
	public $file_path;
	public $save_titles;
	public $db_name;
	public $db_table;
	public $limit_start;
	public $limit_num;
	public $data;
	public $file_content;
	public $file;
	
	public function __construct()
	{
		
	}
	
	public function createAPI($file_name = false, $file_path = '', $save_titles, $db_name, $db_table, $limit_start = 0, $limit_num = 50)
	{
		$this->setVars($file_name, $file_path, $save_titles, $db_name, $db_table, $limit_start, $limit_num);
	#	Common::dump(file_exists($file_path.$file_name), '' , true);
		$this->setFileName();
		echo 'Der Dateiname wurde in "'. $this->file_name .'" geändert!';
	#	Common::dump($file_name);
	#	CSV::createFile($file_name, $file_path);
		$this->getData();
	#	Common::dump($data);
		$this->file_content = '';
		$this->writeFileContent();
	#	Common::dump($file_content);
		$this->file = fopen($this->file_path.$this->file_name, 'w+');
		fwrite($this->file, $this->file_content);
	}

	protected function setVars($file_name = false, $file_path = '', $save_titles = 'on', $db_name, $db_table, $limit_start = 0, $limit_num = 50)
	{
		$this->file_name = $file_name;
		$this->file_path = $file_path;
		$this->save_titles = $save_titles;
		$this->db_name = $db_name;
		$this->db_table = $db_table;
		$this->limit_start = $limit_start;
		$this->limit_num = $limit_num;
	}
	
	protected function setFileName()
	{
	#	Common::dump($this->file_name);
		$this->setFileExtension();
	#	Common::dump($this->file_name);
		if ($this->file_name == '.xml' OR file_exists($this->file_path.$this->file_name) == true)
		{
			$i = 1;
			while (file_exists($this->file_path.$this->file_name) == true)
			{
				$i++;
				$this->file_name = $i;
				$this->setFileExtension();
			}
		}
	}
	
	protected function setFileExtension()
	{
		$pos = strrpos($this->file_name, ".");
		$extension = substr($this->file_name, $pos);
		if ($extension != '.xml')
		{
			$this->file_name = $this->file_name.'.xml';
		}
		else
		{
			$this->file_name = $this->file_name;
		}
	}
	
	protected function getData()
	{
		Common::dump($this->db_name);
		Database::connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORT, $this->db_name);
		$sql = 'SELECT * FROM `'. $this->db_table .'` LIMIT '. $this->limit_start .','. $this->limit_num .'';
		$data = Database::query($sql);
		$this->data = Database::getList($data);
	}
}
