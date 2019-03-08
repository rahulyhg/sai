<?php

global $app;


	$app->post('/access/signup',

		function () use ($app) {

			$retorno    = array( "error" => "", "success" => "","email" => "");
			$parametros = json_decode ($app->request()->getBody());
			$grupoAnt   = NULL;

			try{

				$localizar = new Sai_usuarios();
				$localizar->where("email = '{$parametros->email}' AND excluido = 'N'");

				if($localizar->find()){
					$retorno["email"] = "Esse e-mail já está sendo utilizado, caso você seja o proprietário recupere a senha e realize o login normalmente.";
					echo json_encode($retorno);
					return;
				}

				$novoUsuario				= new Sai_usuarios();
				$novoUsuario->nome			= strtoupper($parametros->fullName);
				$novoUsuario->email			= $parametros->email;
				$novoUsuario->senha			= md5($parametros->password);
				$novoUsuario->cpf 			= $parametros->cpf;
				$novoUsuario->fone 			= $parametros->phone;
				$novoUsuario->hora_cadastro	= date ("Y-m-d H:i:s");
				$novoUsuario->id_perfil		= 1;
				$novoUsuario->excluido		= "N";

				if(!$novoUsuario->insert()){
					$retorno["error"] = "Não foi possível salvar as informações, falha na comunicação com o banco de dados ";
					echo json_encode($retorno);
					return;
				} else {
					$retorno['success'] = true;
				}

			}catch(Lumine_Exception $e){
				$retorno["error"] = $e;
			}

			echo json_encode($retorno);
		}
	);
?>