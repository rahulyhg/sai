<?php


global $app;

$app->get('/material/getDisciplines',

    function () use ($app) {

        $retorno    = array( "error" => "", "data" => array());

        try{
            $disciplines = new Sai_disciplinas();
            $disciplines->where("ativo like 'S'");

            if ($disciplines->find()) {

                while ($disciplines->fetch()) {
                    
                    $disciplinesObj              = array();
                    $disciplinesObj["id"]        = $disciplines->id;
                    $disciplinesObj["name"]      = utf8_encode($disciplines->nome);

                    $retorno["data"][]      = $disciplinesObj;
                }
            } else {
                $retorno["error"] = "Não localizamos nenhuma disciplinas registrada na sua unidade.";
            }

        }catch(Lumine_Exception $e){
            $retorno["error"] = $e;
        }

        echo json_encode($retorno);
    }
);

$app->post('/material/saveMaterial',

    function () use ($app) {

        /* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */

        $retorno    = array( "error" => "", "data" => array());
        $parametros = $app->request()->getBody();

        try{

            $unidade            = $_POST["unitId"];
            $turmas             = $_POST["classes"];
            $titulo             = $_POST["title"];
            $disciplina         = $_POST["discipline"];
            $root_path          = "store/material";
            $unit_path          = $root_path ."/". $unidade;
            $discipline_path    = $unit_path ."/". $disciplina;

            if (!file_exists($unit_path)) 
                @mkdir($unit_path, 0777);
            
            if (!file_exists($discipline_path))
                @mkdir($discipline_path, 0777);

            $temp       = explode('.', $_FILES['material']['name']);
            $filename   = $unidade . $disciplina . "-" . date("Ymd-His") . "." . end($temp);

            if (move_uploaded_file($_FILES["material"]["tmp_name"], $discipline_path . "/" . $filename)) {

                $material               = new Sai_materiais();
                $material->titulo       = utf8_decode($titulo);
                $material->caminho      = $discipline_path . "/" . $filename;
                $material->disciplina   = (int)$disciplina;
                $material->turmas       = $turmas;
                $material->unidade      = (int)$unidade;
                $material->registro     = date("Y-m-d H:i:s");

                if (!$material->insert()) 
                    $retorno["error"] = "Não foi possível salvar o material. Falha na comuicação com o Banco de Dados";

            } else
                $retorno["error"] = "Não foi possível salvar o arquivo. Falha ao gravar no servidor";


        }catch(Lumine_Exception $e){
            $retorno["error"] = $e;
        }

        echo json_encode($retorno);
    }
);

$app->post('/material/getMaterials',

    function () use ($app) {

        /* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */

        $retorno    = array( "error" => "", "data" => array());
        $parametros = json_decode( $app->request()->getBody() );

        try{

            $unidade    = $parametros->unitId;
            $userId     = $parametros->userId;

            $user       = new Sai_usuarios();
            $user->get($userId);
            

            $materials  = new Sai_materiais();
            $materials->where("unidade = {$unidade} AND excluido = 'N' ")
                    ->order("registro DESC");
            
            if ($materials->find()) {
                
                while($materials->fetch()) {

                    $turmas = explode(",", $materials->turmas);

                    if (($user->id_perfil == 3 || $user->id_perfil == 4) || in_array($user->turma, $turmas)) {

                        $materialsObj               = array();
                        $materialsObj["id"]         = $materials->id;
                        $materialsObj["title"]      = utf8_encode($materials->titulo);
                        $materialsObj["path"]       = $materials->caminho;
                        $materialsObj["discipline"] = $materials->disciplina;
                        $retorno["data"][]            = $materialsObj;
                    }
                }
            
            } else {

                $retorno["error"] = "Nenhum material encontrado";
            }

        }catch(Lumine_Exception $e){
            $retorno["error"] = $e;
        }

        echo json_encode($retorno);
    }
);

$app->post('/material/deleteMaterial',

    function () use ($app) {

        /* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */

        $retorno    = array( "error" => "", "data" => array());
        $parametros = json_decode( $app->request()->getBody() );

        try{

            $material = new Sai_materiais();
            $material->get($parametros->materialId);

            $material->excluido = "S";

            if (!$material->update()) {
                $retorno["error"] = "Não foi possível excluir o material";
            }
           
        }catch(Lumine_Exception $e){
            $retorno["error"] = $e;
        }

        echo json_encode($retorno);
    }
);