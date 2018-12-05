<?php
//Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
//Lumine_Log::setOutput( Lumine_Log::BROWSER, '../log/log-lumine.txt' ); // envia para um arquivo

global $app;

/*
 *  REALIZA A AUTENTICACAO DO USUARIO
 * */

$app->post('/access/signin',

	function () use ($app) {

		$retorno    = array( "error" => "",	"data"	=>	array());
		$parametros = json_decode ($app->request()->getBody());
		$grupoAnt   = NULL;

		try{

			$usuario  = new Sai_usuarios();
			$usuario->where("email ='{$parametros->email}' AND senha = '".md5($parametros->password)."' AND excluido = 'N'");

			if($usuario->find(TRUE)){

				$usuarioObj					= array();
				$usuarioObj["id"]			= $usuario->id;
				$usuarioObj["name"]			= utf8_encode($usuario->nome);
				$usuarioObj["email"]		= $usuario->email;
				$usuarioObj["cpf"]			= $usuario->cpf;
				$usuarioObj["phone"]		= $usuario->fone;
				$usuarioObj["profileId"]	= $usuario->id_perfil;


				// CARREGA AS TELAS DISPONIVEIS PARA O TIPO DE PERFIL
				$perfilTelas				= new Sai_perfil_telas();
				$telas						= new Sai_telas();

				$perfilTelas
					->alias("p")
					->select("t.id as id, t.descricao as descricao, 
								t.icone as icone, t.link as link, t.ordem as ordem")
					->join($telas, "INNER", "t", "id_tela", "id")
					->where("p.id_perfil = {$usuario->id_perfil}")
					->orderBy("t.ordem");

				if ($perfilTelas->find()) {

					while($perfilTelas->fetch()) {
						$telaObj  					= array();
						$telaObj["id"]    			= $perfilTelas->id;
						$telaObj["description"]		= utf8_encode($perfilTelas->descricao);
						$telaObj["icon"]			= $perfilTelas->icone;
						$telaObj["link"]			= $perfilTelas->link;
						$telaObj["order"]			= $perfilTelas->ordem;

						$usuarioObj["screens"][]	= $telaObj;
					}
					
				} else {
					$usuarioObj["error"]	= "Falha ao carregar telas";
				}
				
				$retorno["data"] = $usuarioObj;

				// NÃO TA FUNCIONANDO AINDA
				//registraAcesso($usuario->id);

			}else{
				$retorno["error"] = "Senha ou usuário inválidos.";
			}

		}catch(Lumine_Exception $e){
			$retorno["error"] = "Não foi possível consultar as informações, falha na comunicação com o banco de dados";
		}

		echo json_encode($retorno);
	}
);


$app->post('/access/forgotpwd',

	function () use ($app) {

		$retorno    = array( "error" => "", "dados" => array());
		$parametros = json_decode ($app->request()->getBody());

		try{

			$usuario = new LkUsuarios ();
			$usuario->where("email = '{$parametros->email}'");

			if($usuario->find(TRUE)){

				    $senha 			= rand(100000, 900000);
					$usuario->senha = md5($senha);

					if(!$usuario->save()){
						$retorno["error"] = "Não foi possível trocar a senha, falha na comunicação com o banco de dados";
						echo json_encode($retorno);
						return;
					}

					$html = "<table width='100%' align='center'>
								<tr>
									<td colspan='2'>
										<img style='height: 50px;' src='https://prouniversidade.com.br/aulasonline/app/images/logo.png'>
									</td>
								</tr>
								<tr>
									<td colspan='2'>
										<hr/>
									</td>
								</tr>
								<tr>
									<td colspan='2' style='line-height:32px;font-size:24px;color:#878787;'>
										Óla, ".utf8_encode($usuario->nome)."
									</td>
								</tr>
								<tr>
									<td colspan='2' style='line-height:28px;font-size:16px;color:#878787;'>
										Pelo jeito, você esqueceu a sua senha. Sem problemas!<br/>
		                                Abaixo estamos lhe enviando seus dados de acesso.
									</td>
								</tr>
								<tr>
									<td colspan='2'>
										<br/>
									</td>
								</tr>
								<tr>
									<td width='40' style='font-weight:bold;font-size:16px;color:#878787;'>
										USUÁRIO:
									</td>
									<td>
										{$parametros->email}
									</td>
								</tr>
								<tr>
									<td width='40' style='font-weight:bold;font-size:16px;color:#878787;'>
										SENHA:
									</td>
									<td>
										{$senha}
									</td>
								</tr>
					         </table>";


					$mail 		= new PHPMailer();
					$mail->Host = SMTP_SERVER;
					$mail->Port = SMTP_PORT;

					if(SMTP_AUT){
						$mail->SMTPAuth = true;
						$mail->Username = SMTP_USER;
						$mail->Password = SMTP_SENHA;
					}


					$mail->From     = SMTP_FROM;
					$mail->FromName = SMTP_FROM_NAME;

					$mail->AddAddress($usuario->email, utf8_decode($usuario->nome));

					$mail->IsSMTP();
					$mail->IsHTML(true);

					$mail->WordWrap = 50;
					$mail->Subject 	= utf8_decode("Recuperação de Senha");
					$mail->Body 	= utf8_decode($html);


					if(!$mail->Send()) {
						$retorno["error"] = "Não foi possível enviar as informações para o E-mail, falha no SMTP";
					}
			}else{
				$retorno["error"] = "Não foi possível recuperar a senha, E-mail não localizado.";
			}

		}catch(Lumine_Exception $e){
			$retorno["error"] = "Não foi possível enviar as informações, falha na comunicação com o banco de dados";
		}

		echo json_encode($retorno);
	}
);

$app->post('/access/register',

	function () use ($app) {
		$parametros = json_decode ($app->request()->getBody());
		registraAcesso($parametros->id);
	}
);

?>