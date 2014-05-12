<?php

class File_Video extends File
{
	
	public function uploadFile()
	{
		if ($this->checkIfVideo()) 
		{
			$this->saveFile();
			
			$this->InsertIntoDB();
		}
		
		return $this->id;
	}
	
	public function deleteFile($id)
	{
		$this->id = $id;
		
		$this->getOldFile();
		
		$this->unsetFile();
		
		$this->unsetDir();
		
		$this->unsetDB();
	}
	
	public function replaceFile($id)
	{
		if ($this->checkIfVideo()) 
		{
			$this->id = $id;
			
			$this->getOldFile();
			
			$this->unsetFile();
		
			$this->saveFile();
		
			$this->updateDB();
		}
	}
	
	public function getFileLink(&$path, &$thn_path)
	{
		$sql = 'SELECT * FROM `upload` 
				WHERE 	
				`id` = "'. $this->id .'" 
				';
		$data = Database::getList( Database::query($sql) );
		
		foreach ($data as $value)
		{
			$path = $this->dir_path . $value['name'];
			$thn_path = $this->dir_path . $value['thn_name'];
			
			return true;
		}
	}
	
	public function checkIfVideo()
	{
	#	$mime = mime_content_type($this->Files['tmp_name']);
	#	Common::dump($_FILES);

		if(strstr($this->Files['type'], "video/"))
		{
			$this->type = 'video';
			return true;
		}
		return FALSE;
	}
	
	protected function saveFile()
	{
		if ($this->checkIfExist())
		{
			$this->renameFile();
		}
		else
		{
			$this->checkDirs();
		}
		
		rename($this->Files['tmp_name'] , $this->file_path);
	}
	
	protected function unsetFile()
	{
		#	Common::dump($this->old_file_path);
		if (is_writable($this->old_file_path))
		{
			unlink($this->old_file_path);
		}
	}
	
}