<?php

/**
 * CSV Methoden
 *
 * @author Steffen Krohn
 */

class CSV extends API
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function createCSV($file_name, $file_path, $save_titles, $db_name, $db_table, $limit_start, $limit_num)
	{
		$this->createAPI($file_name, $file_path, $save_titles, $db_name, $db_table, $limit_start, $limit_num);
	}
	
	protected function writeFileContent()
	{
		if ($this->save_titles == 'on') 
		{
			$this->writeTitles();
		}
		$this->writeData();
	}
	
	protected function writeTitles()
	{
		foreach ($this->data['0'] as $key => $value)
		{
			$this->file_content = $this->file_content . '"' . $key . '";';
		}
		$this->file_content = $this->file_content . "\r\n";
	}
	
	protected function writeData()
	{
	#	Common::dump($this->data);
		foreach ($this->data as $key => $value)
		{
			foreach ($value as $key2 => $value2)
			{
				$this->file_content = $this->file_content . '"' . $value2 . '";';
			}
			$this->file_content = $this->file_content . "\r\n";
		}
	}
}
