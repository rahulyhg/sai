<?php

global $app;

	$app->post('/register/student',

		function () use ($app) {

			$retorno    = array( "error" => "", "success" => "","email" => "");
			$parametros = json_decode ($app->request()->getBody());

			try{

				$localizar = new Sai_usuarios();
				$localizar->where("email = '{$parametros->email}' AND excluido = 'N'");

				if($localizar->find()){
					$retorno["email"] = "Esse e-mail já está sendo utilizado, caso você seja o proprietário recupere a senha e realize o login normalmente.";
					echo json_encode($retorno);
					return;
                }

                // tratamento para formatar os dados de acordo com o banco
                $cpf                    = str_replace(array('.', '-'), '', $parametros->cpf);
                $emergencyPhone         = str_replace(array('(', ')', ' ', '-'), '', $parametros->emergencyPhone);
                $studentPhone           = str_replace(array('(', ')', ' ', '-'), '', $parametros->studentPhone);
                $paymentCashAmounth     = str_replace(array('R$', ' '), '', $parametros->paymentCashAmounth);
                $paymentCashDiscount    = str_replace(array('%', ' '), '', $parametros->paymentCashDiscount);
                $responsibleCpf         = str_replace(array('.', '-'), '', $parametros->responsibleCpf);
                $cep                    = str_replace('-', '', $parametros->cep);
                $paymentInstallmentParcelsValue = 
                        str_replace(array('R$', ' '), '', $parametros->paymentInstallmentParcelsValue);
                
                $paymentCash            = ($parametros->paymentCash == 'false') ? 0 : 1;
                $paymentInstallment     = ($parametros->paymentInstallment == 'false') ? 0 : 1;
                $underAge               = ($parametros->underAge == 'false') ? 0 : 1;

                $birth                  = date('Y-m-d', strtotime($parametros->birth));
                // end tratamento

				$novoUsuario				                    = new Sai_usuarios();
				$novoUsuario->nome			                    = strtoupper($parametros->name);
				$novoUsuario->email			                    = $parametros->email;
				$novoUsuario->senha			                    = md5($cpf);
				$novoUsuario->cpf 			                    = $cpf;
				$novoUsuario->fone 			                    = $studentPhone;
				$novoUsuario->hora_cadastro	                    = date ("Y-m-d H:i:s");
				$novoUsuario->id_perfil		                    = 1;
                $novoUsuario->excluido		                    = "N";
                $novoUsuario->rg                                = $parametros->rg;
                $novoUsuario->shipping                          = $parametros->shipping;
                $novoUsuario->birth                             = $birth;
                $novoUsuario->hand                              = $parametros->hand;
                $novoUsuario->underAge                          = $underAge;
                $novoUsuario->address                           = $parametros->address;
                $novoUsuario->number                            = $parametros->number;
                $novoUsuario->district                          = $parametros->district;
                $novoUsuario->complement                        = $parametros->complement;
                $novoUsuario->city                              = $parametros->city;
                $novoUsuario->state                             = $parametros->state;
                $novoUsuario->cep                               = $cep;
                $novoUsuario->responsible_name                  = $parametros->responsibleName;
                $novoUsuario->responsible_parentage             = $parametros->responsibleParentage;
                $novoUsuario->responsible_phone                 = $parametros->responsiblePhone;
                $novoUsuario->responsible_cpf                   = $responsibleCpf;
                $novoUsuario->responsible_rg                    = $parametros->responsibleRg;
                $novoUsuario->emergency_name                    = $parametros->emergencyName;
                $novoUsuario->emergency_parentage               = $parametros->emergencyParentage;
                $novoUsuario->emergency_phone                   = $emergencyPhone;
                $novoUsuario->class_period                      = $parametros->classPeriod;
                $novoUsuario->class_course                      = $parametros->classCourse;
                $novoUsuario->formed                            = $parametros->formed;
                $novoUsuario->formed_year                       = $parametros->formedYear;
                $novoUsuario->formed_school                     = $parametros->formedSchool;
                $novoUsuario->formed_school_city                = $parametros->formedSchoolCity;
                $novoUsuario->pre_entrance                      = $parametros->preEntrance;
                $novoUsuario->pre_entrance_name                 = $parametros->preEntranceName;
                $novoUsuario->pre_entrance_city                 = $parametros->preEntranceCity;
                $novoUsuario->pretension_courses                = $parametros->pretensionCourses;
                $novoUsuario->pretension_universities           = $parametros->pretensionUniversities;
                $novoUsuario->enem                              = $parametros->enem;
                $novoUsuario->controlled_medication             = $parametros->controlledMedication;
                $novoUsuario->controlled_medication_desc        = $parametros->controlledMedicationDescription;
                $novoUsuario->special_need                      = $parametros->specialNeed;
                $novoUsuario->special_need_desc                 = $parametros->specialNeedDescription;
                $novoUsuario->allergy                           = $parametros->allergy;
                $novoUsuario->allergy_desc                      = $parametros->allergyDescription;
                $novoUsuario->course_information                = $parametros->courseInformation;
                $novoUsuario->payment_cash                      = $paymentCash;
                $novoUsuario->payment_cash_discount             = $paymentCashDiscount;
                $novoUsuario->payment_cash_amounth              = $paymentCashAmounth;
                $novoUsuario->payment_installment               = $paymentInstallment;
                $novoUsuario->payment_installment_parcels       = $parametros->paymentInstallmentParcels;
                $novoUsuario->payment_installment_parcels_value = $paymentInstallmentParcelsValue;


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