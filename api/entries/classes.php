<?php


global $app;

$app->post('/classes/getUnitClasses',

    function () use ($app) {

        $retorno    = array( "error" => "", "data" => array());
        $parametros = json_decode ($app->request()->getBody());

        try{

            $turmas = new Sai_turmas();
            $turmas->where("excluido like 'N' AND unidade = ". $parametros->unitId);

            if ($turmas->find()) {

                while ($turmas->fetch()) {
                    
                    $turmasObj              = array();
                    $turmasObj["id"]        = $turmas->id;
                    $turmasObj["name"]      = $turmas->turma;
                    $turmasObj["period"]    = utf8_encode($turmas->turno);

                    $retorno["data"][]      = $turmasObj;
                }
            } else {
                $retorno["error"] = "Não localizamos nenhuma turma registrada na sua unidade.";
            }

        }catch(Lumine_Exception $e){
            $retorno["error"] = $e;
        }

        echo json_encode($retorno);
    }
);

$app->post('/classes/getClassStudents',

    function () use ($app) {

        $retorno    = array( "error" => "", "data" => array());
        $parametros = json_decode ($app->request()->getBody());

        try{

            $estudantes = new Sai_usuarios();
            $estudantes->alias("e")
                        ->select("id, nome, student_image, matricula")
                        ->order("nome ASC")
                        ->where("id_perfil = 1 AND excluido = 'N' AND turma = ". $parametros->classId);

            if ($estudantes->find()) {

                while($estudantes->fetch()) {

                    $estudantesObj                  = array();
                    $estudantesObj["id"]            = $estudantes->id;
                    $estudantesObj["name"]          = utf8_encode($estudantes->nome);
                    $estudantesObj["studentImage"]  = $estudantes->student_image;
                    $estudantesObj["registerNumber"]= $estudantes->matricula;
                    $retorno["data"][]              = $estudantesObj;
                }
            } else {
                $retorno["error"] = "Não foi possível encontrar nenhum estudante da turma informada";
            }

        }catch(Lumine_Exception $e){
            $retorno["error"] = "Houve um erro ao buscar os estudantes selecionados. [DB Exeption]";
        }

        echo json_encode($retorno);
    }
);