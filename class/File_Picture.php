<?php

class File_Picture extends File
{
	
	public function uploadFile()
	{
		if ($this->checkIfPicture()) 
		{
			$this->saveFile();
			
			$this->InsertIntoDB();
		}
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
		if ($this->checkIfPicture()) 
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
		$sql = 'SELECT * FROM `media_upload` 
				WHERE 	
				`content_id` = "'. $this->content_id .'" 
				AND
				`modul` = "'. $this->modul .'"
				';
		$data = Database::getList( Database::query($sql) );
		
		foreach ($data as $value)
		{
			$path = $this->dir_path . $value['name'];
			$thn_path = $this->dir_path . $value['thn_name'];
			
			return true;
		}
	}
	
	
	
	protected function checkIfPicture()
	{
		$img = getimagesize($this->Files['tmp_name']);
		if (!empty($img)) 
		{
			return true;
		}
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
		
		$this->imagickCreateThumbnail();
	}
	
	protected function imagickCreateThumbnail()
	{
		$this->thn_name = 'thn_' . $this->name;
		$this->thn_file_path = $this->dir_path . $this->thn_name;

		$image = new Imagick(DIR_ROOT . DIRECTORY_SEPARATOR . $this->file_path);
		
		$image->thumbnailimage(200, 0);

		$image->writeimage(DIR_ROOT . DIRECTORY_SEPARATOR . $this->dir_path . DIRECTORY_SEPARATOR . $this->thn_name);
	}
}