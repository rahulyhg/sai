<?php

global $app;

$app->post('/presence/setStatusPresenceStudent',

	function () use ($app) {

        /* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */

		$retorno    = array( "error" => "",	"data"	=>	array());
		$parametros = json_decode ($app->request()->getBody());

		try{

			$data = date("Y-m-d");
			$presenca = new Sai_presencas();
			$presenca->where("usuario = {$parametros->studentId} AND dia = '{$data}'");

			if ($presenca->find(true)) {

				$presenca->status 			= $parametros->status;
				$presenca->hora_registro 	= date("Y-m-d H:m:i");
				$presenca->update();

			} else {

				$presenca = new Sai_presencas();
				$presenca->status 			= $parametros->status;
				$presenca->usuario			= $parametros->studentId;
				$presenca->dia 				= $data;
				$presenca->hora_registro 	= date("Y-m-d H:m:i");

				if (!$presenca->insert())
					$retorno["error"] = "Erro ao registrar a presença. [BD ERR]";
			}

			
        
		}catch(Lumine_Exception $e){
			$retorno["error"] = "Não foi possível registrar a presença, falha na comunicação com o banco de dados".$e;
		}

		echo json_encode($retorno);
	}
);

$app->post('/presence/setStatusPresenceWithCard',

	function () use ($app) {

		$retorno    = array( "error" => "",	"data"	=>	array());
		$parametros = json_decode ($app->request()->getBody());

		try{

			$estudante = new Sai_usuarios();
			$estudante
					->select("id")
					->where("matricula = {$parametros->registerNumber} AND excluido = 'N' AND id_perfil = 1");

			if ($estudante->find(true)) {
				
				$data = date("Y-m-d");
				$presenca = new Sai_presencas();
				$presenca->where("usuario = {$estudante->id} AND dia = '{$data}'");

				if (!$presenca->find()) {

					$presenca = new Sai_presencas();
					$presenca->status 			= 'p';
					$presenca->usuario			= $estudante->id;
					$presenca->dia 				= $data;
					$presenca->hora_registro 	= date("Y-m-d H:m:i");

					if (!$presenca->insert())
						$retorno["error"] = "Erro ao registrar a presença. [BD ERR]";
				}	
			} else {
				$retorno["error"] = "Não localizamos nenhum estudante com a matrícula informada";
			}
        
		}catch(Lumine_Exception $e){
			$retorno["error"] = "Não foi possível registrar a presença, falha na comunicação com o banco de dados".$e;
		}

		echo json_encode($retorno);
	}
);

$app->post('/presence/getPresences',

	function () use ($app) {

		/* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */
		$retorno 	= array("error" => "", "data" => array());
		$parametros = json_decode ($app->request()->getBody());

		try {

			$data = date("Y-m-d");
			$presencas = new Sai_presencas();
			$presencas->select("usuario, status")
					->where("dia = '{$data}'");

			if ($presencas->find()) {

				while($presencas->fetch()) {

					$presencasObj				= array();
					$presencasObj["studentId"]	= $presencas->usuario;
					$presencasObj["status"] 	= $presencas->status;
					$retorno["data"][]			= $presencasObj;
				}
			} else {
				$retorno["error"] = "não achou nada";
			}

		} catch (Lumine_Exception $e) {
			$retorno["error"] = "Falha na comunicação com o banco de dados".$e;
		} catch (Exception $e) {
			$retorno["error"] = "Falha na comunicação com o banco de dados";
		}

		echo json_encode($retorno);
	}
);