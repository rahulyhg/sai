<?php
	
	$lumineConfig = array(
	    'dialect' 	 => DATA_BASE_TYPE,
	    'database' 	 => DATA_BASE_NAME,
	    'user'	 	 => DATA_BASE_USER,
	    'password'	 => DATA_BASE_PASSWORD,
	    'port' 		 => '3306',
	    'host' 		 => DATA_BASE_HOST,
	    'class_path' => PATH_SYSTEM,
	    'package' 	 => 'mapper',
	    'options' 	 => array()
	);
	
	
	$cfg = new Lumine_Configuration($lumineConfig);
	
	spl_autoload_register(array('Lumine','Import'));
	spl_autoload_register(array('Lumine','ImportDTO'));
	register_shutdown_function(array($cfg->getConnection(),'close'));
?>