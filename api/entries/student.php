<?php

global $app;

	$app->post('/student/register',

		function () use ($app) {

            /* Lumine_Log::setLevel( Lumine_Log::ERROR ); // nivel máximo
        Lumine_Log::setOutput( Lumine_Log::BROWSER); // envia para um arquivo */

			$retorno    = array( "error" => "", "data" => array());
			$parametros = json_decode ($app->request()->getBody());

			try{

				$localizar = new Sai_usuarios();
				$localizar->where("email = '".$parametros->email."' AND excluido = 'N'");

				if($localizar->find()){
					$retorno["error"] = "Esse e-mail já está sendo utilizado por outro estudante.";
					echo json_encode($retorno);
					return;
                }

                // gera a matrícula do novo aluno
                $matricula;
                $ano                = date("y");
                $semestre           = (date("m") > 6) ? 2 : 1;
                $unidade            = ((int)$parametros->unit < 10) ? '0'.$parametros->unit : $parametros->unit;
                $ultimaMatricula    = new Sai_usuarios();
                $ultimaMatricula->select("matricula")
                                ->where("matricula like '".$ano.$semestre.$unidade."%' ")
                                ->limit(1)
                                ->order("matricula DESC");

                if ($ultimaMatricula->find(true)) {

                    $matricula = (int)$ultimaMatricula->matricula;
                    $matricula++;
                } else {
                    $matricula = $ano . $semestre . $unidade . "0001";
                }
                

                // tratamento para formatar os dados de acordo com o banco
                $cpf                    = str_replace(array('.', '-'), '', $parametros->cpf);
                $emergencyPhone         = str_replace(array('(', ')', ' ', '-'), '', $parametros->emergencyPhone);
                $studentPhone           = str_replace(array('(', ')', ' ', '-'), '', $parametros->studentPhone);
                $responsiblePhone       = str_replace(array('(', ')', ' ', '-'), '', $parametros->responsiblePhone);
                $paymentCashAmounth     = str_replace(array('R$', ' '), '', $parametros->paymentCashAmounth);
                $paymentCashDiscount    = str_replace(array('%', ' '), '', $parametros->paymentCashDiscount);
                $responsibleCpf         = str_replace(array('.', '-'), '', $parametros->responsibleCpf);
                $cep                    = str_replace('-', '', $parametros->cep);
                $paymentInstallmentParcelsValue = 
                        str_replace(array('R$', ' '), '', $parametros->paymentInstallmentParcelsValue);
                
                $paymentCash            = ($parametros->paymentCash == 'true') ? 1 : 0;
                $paymentInstallment     = ($parametros->paymentInstallment == 'true') ? 1 : 0;
                $underAge               = ($parametros->underAge == 'true') ? 1 : 0;

                $birth                  = date('Y-m-d', strtotime($parametros->birth));
                // end tratamento

				$novoUsuario				                    = new Sai_usuarios();
                $novoUsuario->nome			                    = strtoupper(utf8_decode($parametros->name));
                $novoUsuario->matricula                         = $matricula;
				$novoUsuario->email			                    = $parametros->email;
				$novoUsuario->senha			                    = md5($cpf);
				$novoUsuario->cpf 			                    = $cpf;
				$novoUsuario->fone 			                    = $studentPhone;
				$novoUsuario->hora_cadastro	                    = date ("Y-m-d H:i:s");
				$novoUsuario->id_perfil		                    = 1;
                $novoUsuario->excluido		                    = "N";
                $novoUsuario->rg                                = $parametros->rg;
                $novoUsuario->shipping                          = utf8_decode($parametros->shipping);
                $novoUsuario->birth                             = $birth;
                $novoUsuario->hand                              = $parametros->hand;
                $novoUsuario->underAge                          = $underAge;
                $novoUsuario->address                           = utf8_decode($parametros->address);
                $novoUsuario->number                            = $parametros->number;
                $novoUsuario->district                          = utf8_decode($parametros->district);
                $novoUsuario->complement                        = utf8_decode($parametros->complement);
                $novoUsuario->city                              = utf8_decode($parametros->city);
                $novoUsuario->state                             = $parametros->state;
                $novoUsuario->cep                               = $cep;
                $novoUsuario->responsible_name                  = utf8_decode($parametros->responsibleName);
                $novoUsuario->responsible_parentage             = utf8_decode($parametros->responsibleParentage);
                $novoUsuario->responsible_phone                 = $responsiblePhone;
                $novoUsuario->responsible_cpf                   = $responsibleCpf;
                $novoUsuario->responsible_rg                    = $parametros->responsibleRg;
                $novoUsuario->emergency_name                    = utf8_decode($parametros->emergencyName);
                $novoUsuario->emergency_parentage               = utf8_decode($parametros->emergencyParentage);
                $novoUsuario->emergency_phone                   = $emergencyPhone;
                $novoUsuario->formed                            = $parametros->formed;
                $novoUsuario->formed_year                       = $parametros->formedYear;
                $novoUsuario->formed_school                     = utf8_decode($parametros->formedSchool);
                $novoUsuario->formed_school_city                = utf8_decode($parametros->formedSchoolCity);
                $novoUsuario->pre_entrance                      = $parametros->preEntrance;
                $novoUsuario->pre_entrance_name                 = utf8_decode($parametros->preEntranceName);
                $novoUsuario->pre_entrance_city                 = utf8_decode($parametros->preEntranceCity);
                $novoUsuario->pretension_courses                = utf8_decode($parametros->pretensionCourses);
                $novoUsuario->pretension_universities           = utf8_decode($parametros->pretensionUniversities);
                $novoUsuario->enem                              = $parametros->enem;
                $novoUsuario->controlled_medication             = $parametros->controlledMedication;
                $novoUsuario->controlled_medication_desc        = utf8_decode($parametros->controlledMedicationDescription);
                $novoUsuario->special_need                      = $parametros->specialNeed;
                $novoUsuario->special_need_desc                 = utf8_decode($parametros->specialNeedDescription);
                $novoUsuario->allergy                           = $parametros->allergy;
                $novoUsuario->allergy_desc                      = utf8_decode($parametros->allergyDescription);
                $novoUsuario->course_information                = utf8_decode($parametros->courseInformation);
                $novoUsuario->payment_cash                      = $paymentCash;
                $novoUsuario->payment_cash_discount             = $paymentCashDiscount;
                $novoUsuario->payment_cash_amounth              = $paymentCashAmounth;
                $novoUsuario->payment_installment               = $paymentInstallment;
                $novoUsuario->payment_installment_parcels       = $parametros->paymentInstallmentParcels;
                $novoUsuario->payment_installment_parcels_value = $paymentInstallmentParcelsValue;
                $novoUsuario->turma                             = $parametros->class;
                $novoUsuario->unidade                           = $parametros->unit;


				if(!$novoUsuario->insert()){
					$retorno["error"] = "Não foi possível salvar as informações, falha na comunicação com o banco de dados ";
					echo json_encode($retorno);
					return;
				} else {

                    $info           = mime_content_type($parametros->studentImage);
                    $info           = explode("/", $info);
                    $extensao       = $info[1];
                    $nome_arquivo   = date("Y")."-".$novoUsuario->id."-".date("dmY-His");
                    $arquivo = "store/students/".$nome_arquivo.".".$extensao;

                    if( copy($parametros->studentImage,"" . $arquivo) ) {

                        $novoUsuario->student_image  = $arquivo;
                        $novoUsuario->update();
                        $retorno['data'] = true;
                    } else {
                        $retorno['data'] = "Atenção! Estudante registrado com sucesso, mas hove uma falha ao armazenar sua foto. Favor contatar o suporte.";
                    }
				}

			}catch(Lumine_Exception $e){
				$retorno["error"] = $e;
			} catch(Exception $e){
				$retorno["error"] = $e;
			} 

			echo json_encode($retorno);
		}
    );
    

    $app->post('/student/getStudent',

		function () use ($app) {

			$retorno    = array( "error" => "", "data" => array());
            $parametros = json_decode ($app->request()->getBody());

			try{

                $student = new Sai_usuarios();
                
                if(isset($parametros->studentId))
                    $student->where("id = '{$parametros->studentId}' AND excluido = 'N'");
                else 
                    $student->where("email = '{$parametros->email}' AND excluido = 'N'");
				

				if($student->find(true)){
                    $studentObj                                         = array();
                    $studentObj['name']                                 = utf8_encode($student->nome);			            
                    $studentObj['email']                                = $student->email;
                    $studentObj['registerDate']			            	= $student->hora_cadastro;	            
                    $studentObj['cpf']                                  = $student->cpf;			            
                    $studentObj['studentPhone']                         = $student->fone;
                    $studentObj['rg']                                   = $student->rg;
                    $studentObj['shipping']                             = utf8_encode($student->shipping);
                    $studentObj['birth']                                = $student->birth;
                    $studentObj['hand']                                 = $student->hand;
                    $studentObj['underAge']                             = $student->underAge;
                    $studentObj['address']                              = utf8_encode($student->address);                   
                    $studentObj['number']                               = $student->number;                    
                    $studentObj['district']                             = utf8_encode($student->district);                  
                    $studentObj['complement']                           = utf8_encode($student->complement);                
                    $studentObj['city']                                 = utf8_encode($student->city);                      
                    $studentObj['state']                                = $student->state;                     
                    $studentObj['cep']                                  = $student->cep;
                    $studentObj['image']                                = $student->student_image;                   
                    $studentObj['responsibleName']                      = utf8_encode($student->responsible_name);         
                    $studentObj['responsibleParentage']                 = utf8_encode($student->responsible_parentage);     
                    $studentObj['responsiblePhone']                     = $student->responsible_phone;         
                    $studentObj['responsibleCpf']                       = $student->responsible_cpf;           
                    $studentObj['responsibleRg']                        = $student->responsible_rg;            
                    $studentObj['emergencyName']                        = utf8_encode($student->emergency_name);           
                    $studentObj['emergencyParentage']                   = utf8_encode($student->emergency_parentage);       
                    $studentObj['emergencyPhone']                       = $student->emergency_phone;           
                    $studentObj['class']                                = $student->class;              
                    $studentObj['unit']                                 = $student->unidade;              
                    $studentObj['formed']                               = $student->formed;                    
                    $studentObj['formedYear']                           = $student->formed_year;               
                    $studentObj['formedSchool']                         = utf8_encode($student->formed_school);             
                    $studentObj['formedSchoolCity']                     = utf8_encode($student->formed_school_city);        
                    $studentObj['preEntrance']                          = $student->pre_entrance;              
                    $studentObj['preEntranceName']                      = utf8_encode($student->pre_entrance_name);         
                    $studentObj['preEntranceCity']                      = utf8_encode($student->pre_entrance_city);         
                    $studentObj['pretensionCourses']                    = utf8_encode($student->pretension_courses);       
                    $studentObj['pretensionUniversities']               = utf8_encode($student->pretension_universities);   
                    $studentObj['enem']                                 = $student->enem;                      
                    $studentObj['controlledMedication']                 = $student->controlled_medication;     
                    $studentObj['controlledMedicationDescription']      = utf8_encode($student->controlled_medication_desc);
                    $studentObj['specialNeed']                          = $student->special_need;                     
                    $studentObj['specialNeedDescription']               = utf8_encode($student->special_need_desc);                
                    $studentObj['allergy']                              = $student->allergy;                          
                    $studentObj['allergyDescription']                   = utf8_encode($student->allergy_desc);                     
                    $studentObj['courseInformation']                    = utf8_encode($student->course_information);               
                    $studentObj['paymentCash']                          = $student->payment_cash;                     
                    $studentObj['paymentCashDiscount']                  = $student->payment_cash_discount;            
                    $studentObj['paymentCashAmounth']                   = $student->payment_cash_amounth;             
                    $studentObj['paymentInstallment']                   = $student->payment_installment;              
                    $studentObj['paymentInstallmentParcels']            = $student->payment_installment_parcels;      
                    $studentObj['paymentInstallmentParcelsValue']       = $student->payment_installment_parcels_value;

                    $retorno['data'] = $studentObj;
                } else {
                    $retorno['error'] = "Estudante não encontrado em nosso banco de dados!";
                }

			}catch(Lumine_Exception $e){
				$retorno["error"] = $e;
			}

			echo json_encode($retorno);
		}
    );
    

    $app->post('/student/removeStudent',

        function () use ($app) {

            $retorno    = array( "error" => "",	"data"	=>	array());
            $parametros = json_decode ($app->request()->getBody());

            try{

                $student = new Sai_usuarios();
                $student->get($parametros->studentId);

                $student->excluido = 'S';

                if (!$student->update())
                    $retorno['error'] = "Não foi possível excluír o estudante. [BD ERR]";

            }catch(Lumine_Exception $e){
                $retorno["error"] = "Não foi possível consultar as informações, falha na comunicação com o banco de dados".$e;
            } catch(Exception $e){
                $retorno["error"] = "Não foi possível consultar as informações, falha na comunicação com o banco de dados".$e;
            }

            echo json_encode($retorno);
        }
);
?>