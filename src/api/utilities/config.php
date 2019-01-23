<?php

	/**
     *  DEFINE AS CONSTANTES UTILIZADAS NO SISTEMA
     */

	//====================================//
	// SETA O AMBIENTE DE DESENVOLVIMENTO // 
	//************************************//	
		//$environment = "DEVELOPMENT"; //
		$environment = "PRODUCTION";  //
		//$environment = "HOMOLOGATION";    //
	//************************************//
	//									  //
	//====================================//
	date_default_timezone_set('America/Sao_Paulo');

	//AUTENTICACAO
	define("BASIC_AUTHENTICATION_USER"    , "sai");
	define("BASIC_AUTHENTICATION_PASSWORD", "solucao1");

	//ENVIO DE E-MAIL
	define("SMTP_AUT",     		true);
	define("SMTP_SERVER",  		"email-ssl.com.br");
	define("SMTP_PORT",    		25);
	define("SMTP_USER",    		"contato@prouniversidade.com.br");
	define("SMTP_SENHA",   		"solucao1");
	define("SMTP_FROM",   		"contato@prouniversidade.com.br");
	define("SMTP_FROM_NAME",    "SAI - sistema de Acompanhamento Integrado ");

	//BANCO DE DADOS
	define("DATA_BASE_TYPE"    , "MySQL");
	define("DATA_BASE_USER"    , "root");


	/*
	 * DESENVOLVIMENTO
	*/
	if ($environment == "DEVELOPMENT"){
		
		define("DATA_BASE_HOST"    	, 	"localhost");
		define("DATA_BASE_NAME"    	, 	"sai");
		define("ENVIRONMENT"       	, 	"LOCALHOST");
		define("DATA_BASE_PASSWORD"	, 	"");
		define("PATH_SYSTEM"		,   "C:/wamp/www/sai/api/");
		define("PATH_SYSTEM_HTTP"	,	"http://localhost:4200/sai/api/");
		define("PATH_SYSTEM_URL"	, 	"http://localhost:4200/");
	} 

	
	/*
	 * PRODUCAO
	 * */
	if ($environment == "PRODUCTION"){

		define("DATA_BASE_HOST"    	, 	"xxxdnn3407.locaweb.com.br");
		define("DATA_BASE_NAME"    	, 	"sai");
		define("ENVIRONMENT"       	, 	"PRODUCTION");
		define("DATA_BASE_PASSWORD"	, 	"");
		define("PATH_SYSTEM"		,   "C:/wamp/www/sai/api/");
		define("PATH_SYSTEM_HTTP"	,	"https://prouniversidade.com.br/sai/v2/api");
		define("PATH_SYSTEM_URL"	, 	"https://prouniversidade.com.br/sai/v2/");
	} 

	/*
	 * HOMOLOGACAO
	 * */
	if ($environment == "HOMOLOGATION"){

	}