<?php

/**
 * Autoload
 * 
 * @param string $className 
 */
function __autoload( $className )
{
	$class	= DIR_CLASSES . DIRECTORY_SEPARATOR . $className . '.php';
	if(file_exists($class))
	{
		require_once($class);
	}
	else
	{
	#	ezcBase::autoload($className);
	}
}
