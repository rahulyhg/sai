<?php

	//IMPORTA OS FRAMEWORK
	require 'library/Slim/Slim.php';
	require 'library/Slim/Middleware.php';
	require 'library/Slim/Middleware/HttpBasicAuth.php';
	require 'library/lumine/Lumine.php';
	require 'library/mailer/class.phpmailer.php';
	require 'library/log4php/Logger.php';

	require 'vendor/autoload.php';

	require 'utilities/config.php';
	require 'utilities/functions.php';
	require 'utilities/lumine_conf.php';

	//ROTAS PUBLICAS
	$ROUTE_PUBLIC = array(
							"/","/access/signup","/access/signin"
						 );

	$FILES_PUBLIC	= array(".jpg",".jpeg",".png",".pdf","temporary");
	$PUBLICO		= FALSE;

	Logger::configure('utilities/logginConfiguration.xml');

	//REGISTRA A WEB API
	\Slim\Slim::registerAutoloader();

	// DISPONIBILIZA OS SERVICOS
	$app = new \Slim\Slim();

	if(in_array($app->request()->getPathInfo(),$ROUTE_PUBLIC)){
		$PUBLICO = TRUE;
	}else{
		foreach ($FILES_PUBLIC  as $file){
			if(strpos($app->request()->getPathInfo(),$file) > -1){
				$PUBLICO = TRUE;
				break;
			}
		}
	}


	if(ENVIRONMENT == "LOCALHOST"){

		if (isset($_SERVER['HTTP_ORIGIN'])) {
			//header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header("Access-Control-Allow-Origin: *");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');
		}

		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
				header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
			}

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			}
		}

		$app->options('/(:x+)', function() use ($app) {
			$app->response->setStatus(200);
		});
	}else{

		//BASIC AUTHENTICATION
		if (!$PUBLICO){
			$app->add(new \Slim\Extras\Middleware\HttpBasicAuth(BASIC_AUTHENTICATION_USER,BASIC_AUTHENTICATION_PASSWORD));
		}
	}

	includeArquivosDir("mapper/");
	includeArquivosDir("entries/");

	$app->run();
