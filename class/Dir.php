<?php
class Dir extends Standard
{
	public function deleteDirAndContents($path)
	{
		if ( $this->checkIfExist($path)
		AND
		is_dir($path)
		AND
		is_writable($path) )
		{
			$dir = scandir($path);
			
			unset($dir['0']);
			unset($dir['1']);
			foreach ($dir as $value)
			{
				if ( is_dir($path . DIRECTORY_SEPARATOR . $value)	)
				{
					$this->deleteDirAndContents($path . DIRECTORY_SEPARATOR . $value);
				}
				else
				{
					unlink($path. DIRECTORY_SEPARATOR . $value);
				}
			}
			rmdir($path);
				
			return true;
		}
	
		return false;
	}
	
	protected function checkIfExist($file_path = NULL)
	{
		return file_exists($file_path);
	}
	
	protected function checkIfDirEmpty($path)
	{
		if (is_dir($path) AND is_readable($path))
		{
			$handle = opendir($path);
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != "." && $entry != "..")
				{
					return false;
				}
			}
			return true;
		}
		return -1;
	}
	
	
}