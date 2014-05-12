<?php

class File extends Dir
{
	public $Files;
	
	protected $old_modul;
	protected $old_content_id;
	protected $old_id;
	protected $old_name;
	protected $old_thn_name;
	
	protected $old_file_path;
	protected $old_thn_file_name;
	
	protected $modul;
	protected $content_id;
	protected $id;
	protected $name;
	
	protected $dir_path;
	protected $file_path;
	protected $url;
	
	protected $thn_name;
	protected $thn_file_path;
	protected $thn_url;
	
	protected $type; 
	
	
	function __construct($File, $content_id)
	{
		$this->Files = $File;
		
		$this->modul = Url::getURLObject(Url::getURL()->pfad)->bereich;
	#	Common::dump($this->modul, 'modul');
		
		$this->content_id = $content_id;
		
		$this->name = $this->Files['name'];
		
		$this->dir_path = 'media';
	#	Common::dump($this->upload_path);
		
		$this->file_path = $this->dir_path . DIRECTORY_SEPARATOR . $this->name;
	#	Common::dump($this->file_path);
	
		$this->url = $this->dir_path . '/' . $this->name;
		$this->thn_url = $this->dir_path . '/thn_' . $this->name;
	}
	
	
	
	public function uploadFile()
	{
		$this->saveFile();
		
		$this->InsertIntoDB();
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
		$this->id = $id;
		
		$this->getOldFile();
		
		$this->unsetFile();
	
		$this->saveFile();
	
		$this->updateDB();
	}

	
	protected function checkIfExist($file_path = NULL)
	{
		if ($file_path == NULL)
		{
			$file_path = $this->file_path;
		}
		#	Common::dump( file_exists($path), '', true);
		return file_exists($file_path);
	}
		
	protected function checkDirs()
	{
	#	Common::dump($this->dir_path);
	#	Common::dump(DIR_ROOT);
		$array = explode( DIRECTORY_SEPARATOR , $this->dir_path );
	#	Common::dump($array);
		$path = DIR_ROOT;
		
		foreach ($array as $value)
		{
			$path = $path . DIRECTORY_SEPARATOR . $value;
			if (!$this->checkIfExist($path)) 
			{
				mkdir($path);
			}
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
	}
	
	protected function InsertIntoDB()
	{
	#	Common::dump($this->file_path);
		$sql = 'INSERT INTO `media` 
				SET
				`name`			= "'. $this->name .'",
				`type`			= "'. $this->type .'",
				`url`			= "'. $this->name .'",
				`thn_url`		= "'. $this->thn_name .'",
				`path`			= "'. $this->name .'",
				`thn_path`		= "'. $this->thn_name.'"
				';
		
		$this->id = Database::query($sql);
	}
	
	protected function unsetFile()
	{
	#	Common::dump($this->old_file_path);
		if (is_writable($this->old_file_path) AND is_writable($this->old_thn_file_name)) 
		{
			unlink($this->old_file_path);
			unlink($this->old_thn_file_name);
		}
	}
	
	protected function unsetDir($path = NULL)
	{
		if ($path == NULL) 
		{
			$path = $this->dir_path;
		}
		
		if ( $this->checkIfExist($path)
			 AND
			 is_dir($path)
			 AND 
			 $this->checkIfDirEmpty($path)
			 AND
			 is_writable($path) )
		{
			rmdir($path);
			
		#	Common::dump($path);
			$array = explode(DIRECTORY_SEPARATOR, $path);
			$array = array_reverse($array);
		#	Common::dump($array);
			
			$path = '';
			$first = 1;
			foreach ($array as $value)
			{
				if (!empty($value)) 
				{
				#	Common::dump($value);
					if ($first == 0) 
					{
						$path = $value . DIRECTORY_SEPARATOR . $path;
					}
					
					$first = 0;
				}
			}
			
			$this->unsetDir($path);
		#	Common::dump($path);
			return true;
		}
	}
	
	protected function unsetDB()
	{
		$sql = 'DELETE FROM `media`
				WHERE `id` = "'. $this->id .'"
				';

	#	Common::dump($sql);
		Database::query($sql);
	}
	
	protected function updateDB()
	{
		$sql = 'UPDATE `media`
				SET
				`name`			= "'. $this->name .'"
				WHERE 
				`id` 			= "'. $this->id .'"
				';
		
		Database::query($sql);
	}
	
	protected function renameFile()
	{
	#	Common::dump($this->name);
	
		$array = explode( '.' , $this->name );
		$array = array_reverse($array);
		#	Common::dump($array);
		
		for ( $i = 1 ; $this->checkIfExist() ; $i++ )
		{
			$first = 1;
			$punkt = '';
			foreach ($array as $value)
			{
				$name_begining = '';
				
				if ($first == 0) 
				{
					$name_begining = $value . $punkt . $name_begining;
					$punkt = '.';
				}
				
				$first = 0;
				
			}
		#	Common::dump($name_begining);
			
			$this->name = $name_begining . '_' . $i . '.' . $array['0'];
			$this->file_path = $this->dir_path . DIRECTORY_SEPARATOR . $this->name;
			
			$this->url = $this->dir_path . '/' . $this->name;
			$this->thn_url = $this->dir_path . '/thn_' . $this->name;
		}
	#	Common::dump($this->name);
	#	Common::dump($this->file_path);
	}
	
	protected function getOldFile()
	{
		$sql = 'SELECT * FROM `media`
				WHERE `id` = "'. $this->id .'"
				';
		
		$data = Database::query($sql);
		$data = Database::getList($data);
		$data = $data['0'];

	#	Common::dump($data);
		
	#	foreach ($data as $key => $value)
	#	{
	#		Common::dump($key , $value);
	#		$$this->old_.$key = $value;
	#		Common::dump($key , $value);
	#	}
	
		$this->old_name = $data['path'];
		$this->old_thn_name = $data['thn_path'];
		
		$this->old_file_path = $this->dir_path . DIRECTORY_SEPARATOR . $this->old_name;
		$this->old_thn_file_name = $this->dir_path . DIRECTORY_SEPARATOR . $this->old_thn_name;
		
	#	Common::dump($this->old_modul);
	#	Common::dump($this->old_file_path);
		return true;
	}
	
	
}