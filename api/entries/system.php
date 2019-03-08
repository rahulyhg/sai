<?php


global $app;

$app->get('/system/version',

    function () use ($app) {

        $retorno    = array( "error" => "", "data" => array());

        try{
           
            $system = new Sai_system();
            $system->limit(1)
                    ->order("build_date DESC");

            if ($system->find(true)) {

                $systemObj                  = array();
                $systemObj["version"]       = $system->version;
                $systemObj["buildDate"]     = $system->build_date;
                $retorno["data"]            = $systemObj;
            } else 
                $retorno["error"] = "[Get Versyon error]";

        }catch(Lumine_Exception $e){
            $retorno["error"] = $e;
        }

        echo json_encode($retorno);
    }
);