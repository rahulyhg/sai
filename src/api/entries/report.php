<?php
//Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
//Lumine_Log::setOutput( Lumine_Log::BROWSER, '../log/log-lumine.txt' ); // envia para um arquivo

global $app;

/*
 *  REALIZA A AUTENTICACAO DO USUARIO
 * */

$app->get('/report/students',

	function () use ($app) {

        /* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */

		$retorno    = array( "error" => "",	"data"	=>	array());

		try{

            $students = new Sai_usuarios();
            
            $students->where('excluido = "N" && id_perfil = 1')
                        ->order('hora_cadastro ASC');

            if($students->find()) {
                
                while($students->fetch()){

                    $studentsObj                            = array();
                    $studentsObj['id']                      = $students->id;
                    $studentsObj['name']                    = utf8_encode($students->nome);
                    $studentsObj['email']                   = $students->email;
                    $studentsObj['phone']                   = $students->fone;
                    $studentsObj['date']                    = $students->hora_cadastro;
                    $studentsObj['city']                    = utf8_encode($students->cidade);
                    $studentsObj['classPeriod']             = $students->class_period;
                    $studentsObj['classCourse']             = $students->class_course;
                    $studentsObj['paymentMethod']           = ($students->payment_cash == 1) ? 'À vista' : 'Parcelado';
                    $studentsObj['discountPercent']         = $students->payment_cash_discount . '%';

                    if($studentsObj['paymentMethod'] == 'Parcelado') {
                        
                        $studentsObj['paymentCashAmounth']      = '-';
                        $studentsObj['installments']            = '1 + ' . $students->payment_installment_parcels;
                        $studentsObj['installmentsValue']       = 'R$' . $students->payment_installment_parcels_value;
                    } else {

                        $studentsObj['paymentCashAmounth']      = 'R$' . $students->payment_cash_amounth;
                        $studentsObj['installments']            = '-';
                        $studentsObj['installmentsValue']       = '-';
                    }

                    $retorno['data'][] = $studentsObj;
                }

            }

		}catch(Lumine_Exception $e){
			$retorno["error"] = "Não foi possível consultar as informações, falha na comunicação com o banco de dados";
		}

		echo json_encode($retorno);
	}
);
