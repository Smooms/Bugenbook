<?php

/**
 * XML Methoden
 *
 * @author Steffen Krohn
 */

class XML extends API
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function createXML($file_name, $file_path, $db_name, $db_table, $limit_start, $limit_num)
	{
		$this->createAPI($file_name, $file_path, '', $db_name, $db_table, $limit_start, $limit_num);
	}
	
	protected function writeFileContent()
	{
		$this->writeHead();
		$this->writeData();
		$this->writeFoot();
	}
	
	protected function writeHead()
	{
		$this->file_content = $this->file_content . '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . "\r\n" . "\r\n";
		$this->file_content = $this->file_content . '<!-- Datensätze '. $this->limit_start .' bis '. $this->limit_num .' aus der Tabelle "'. $this->db_table .'", der Datenbank "'. $this->db_name .'" -->' . "\r\n" . "\r\n";
		$this->file_content = $this->file_content . '<'. $this->db_table .'>' . "\r\n";
		
	}
	
	protected function writeData()
	{
	#	Common::dump($this->data);
		foreach ($this->data as $key => $value)
		{
			$id = $key + 1;
			$this->file_content = $this->file_content . '	<datensatz'. $id.'>' . "\r\n";
			foreach ($value as $key2 => $value2)
			{
				$this->file_content = $this->file_content . '		<'. $key2 .'>' . $value2 . '</'. $key2 .'>' . "\r\n";
			}
			$this->file_content = $this->file_content . '	</datensatz'. $id .'>' . "\r\n" . "\r\n";
		}
	}
	
	protected function writeFoot()
	{
		$this->file_content = $this->file_content . '</'. $this->db_table .'>';
	}
}
