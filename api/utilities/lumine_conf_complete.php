<?php
	
	$lumineConfig = array(
	    'dialect' 	 => "MySQL",
	    'database' 	 => "sai",
	    'user'	 	 => "root",
	    'password'	 => "",
	    'port' 		 => '3306',
	    'host' 		 => "localhost",
	    'class_path' => "C:/wamp/www/sai/api/",
	    'package' 	 => 'mapper',
	    'options' 	 => array()
	);
	
	
	$cfg = new Lumine_Configuration($lumineConfig);
	
	spl_autoload_register(array('Lumine','Import'));
	spl_autoload_register(array('Lumine','ImportDTO'));
	register_shutdown_function(array($cfg->getConnection(),'close'));
?>