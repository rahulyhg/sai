<?php

    function round_up($number, $precision = 2){
        $fig = (int) str_pad('1', $precision, '0');
        return (ceil($number * $fig) / $fig);
    }

    function includeArquivosDir($pathDir){
        $dirLumine  = opendir($pathDir);

        while ($nome_itens = readdir($dirLumine)) {
            if($nome_itens!="." && $nome_itens!=".." && $nome_itens!="teste.php" && $nome_itens!=".svn"){
                include_once($pathDir.$nome_itens);
            }
        }
        closedir($dirLumine);
    }

    function getDescMes($texto){
		$retorno = "";

		switch($texto){
			case 1:
				$retorno = "Jan";
			break;
			case 2:
				$retorno = "Fev";
			break;
			case 3:
				$retorno = "Mar";
			break;
			case 4:
				$retorno = "Abr";
			break;
			case 5:
				$retorno = "Maio";
			break;
			case 6:
				$retorno = "Jun";
			break;
			case 7:
				$retorno = "Jul";
			break;
			case 8:
				$retorno = "Ago";
			break;
			case 9:
				$retorno = "Set";
			break;
			case 10:
				$retorno = "Out";
			break;
			case 11:
				$retorno = "Nov";
			break;
			case 12:
				$retorno = "Dez";
			break;
		}

		return $retorno;
	}

	function getDescSemana($texto){
		$retorno = "";

		switch($texto){
			case 1:
				$retorno = "Dom";
			break;
			case 2:
				$retorno = "Seg";
			break;
			case 3:
				$retorno = "Ter";
			break;
			case 4:
				$retorno = "Qua";
			break;
			case 5:
				$retorno = "Qui";
			break;
			case 6:
				$retorno = "Sex";
			break;
			case 7:
				$retorno = "Sáb";
			break;
		}

		return $retorno;
    }
    
    function registraAcesso($id){
		try{
			$dtAtual = date("Y-m-d H:i:s");

			$lkUsuarioAcesso = new Sai_usuarios_acesso();
			$lkUsuarioAcesso->id_usuario = $id;
			$lkUsuarioAcesso->dt_acesso  = $dtAtual;

			if(!$lkUsuarioAcesso->insert()){
				error_log("FAILED TO SAVE HISTORIC ACCESS USER:".$id,3,"./debug.log");
			}

		}catch(Lumine_Exception $e){
			error_log("FAILED TO SAVE ACCESS\n".$e->getMessage(),3,"./debug.log");
		}
	}