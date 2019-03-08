<?php
#### START AUTOCODE
################################################################################
#  Lumine - Database Mapping for PHP
#  Copyright (C) 2005  Hugo Ferreira da Silva
#  
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#  
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#  
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>
################################################################################
/**
 * Classe generada para a tabela "sai_usuarios"
 * in 2019-02-27
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br/lumine
 * @package mapper
 *
 */

class Sai_usuarios extends Lumine_Base {

    // sobrecarga
    protected $_tablename = 'sai_usuarios';
    protected $_package   = 'mapper';
    
    
    public $id;
    public $hora_cadastro;
    public $nome;
    public $matricula;
    public $unidade;
    public $turma;
    public $email;
    public $cpf;
    public $fone;
    public $senha;
    public $id_perfil;
    public $rg;
    public $shipping;
    public $birth;
    public $hand;
    public $underAge;
    public $address;
    public $number;
    public $district;
    public $complement;
    public $city;
    public $state;
    public $cep;
    public $student_image;
    public $responsible_name;
    public $responsible_parentage;
    public $responsible_phone;
    public $responsible_cpf;
    public $responsible_rg;
    public $emergency_name;
    public $emergency_parentage;
    public $emergency_phone;
    public $formed;
    public $formed_year;
    public $formed_school;
    public $formed_school_city;
    public $pre_entrance;
    public $pre_entrance_name;
    public $pre_entrance_city;
    public $pretension_courses;
    public $pretension_universities;
    public $enem;
    public $controlled_medication;
    public $controlled_medication_desc;
    public $special_need;
    public $special_need_desc;
    public $allergy;
    public $allergy_desc;
    public $course_information;
    public $payment_cash;
    public $payment_cash_discount;
    public $payment_cash_amounth;
    public $payment_installment;
    public $payment_installment_parcels;
    public $payment_installment_parcels_value;
    public $excluido;
    public $sai_presencas = array();
    public $sai_usuarios_acesso = array();
    
    
    
    /**
     * Inicia os valores da classe
     * @author Hugo Ferreira da Silva
     * @return void
     */
    protected function _initialize()
    {
        
        
        # nome_do_membro, nome_da_coluna, tipo, comprimento, opcoes
        
        $this->_addField('id', 'id', 'int', 11, array('primary' => true, 'notnull' => true, 'autoincrement' => true));
        $this->_addField('hora_cadastro', 'hora_cadastro', 'datetime', null, array('notnull' => true));
        $this->_addField('nome', 'nome', 'varchar', 100, array('notnull' => true));
        $this->_addField('matricula', 'matricula', 'int', 11, array('notnull' => true));
        $this->_addField('unidade', 'unidade', 'int', 11, array('notnull' => true, 'foreign' => '1', 'onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT', 'linkOn' => 'id', 'class' => 'Sai_unidades'));
        $this->_addField('turma', 'turma', 'int', 11, array('notnull' => true, 'foreign' => '1', 'onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT', 'linkOn' => 'id', 'class' => 'Sai_turmas'));
        $this->_addField('email', 'email', 'varchar', 50, array('notnull' => true));
        $this->_addField('cpf', 'cpf', 'varchar', 20, array('notnull' => true));
        $this->_addField('fone', 'fone', 'varchar', 20, array('notnull' => true));
        $this->_addField('senha', 'senha', 'varchar', 32, array('notnull' => true));
        $this->_addField('id_perfil', 'id_perfil', 'int', 11, array('notnull' => true, 'foreign' => '1', 'onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT', 'linkOn' => 'id', 'class' => 'Sai_idperfil'));
        $this->_addField('rg', 'rg', 'int', 11, array('notnull' => true));
        $this->_addField('shipping', 'shipping', 'varchar', 100, array('notnull' => true));
        $this->_addField('birth', 'birth', 'date', null, array('notnull' => true));
        $this->_addField('hand', 'hand', 'char', 1, array('notnull' => true));
        $this->_addField('underAge', 'underAge', 'boolean', 1, array('notnull' => true));
        $this->_addField('address', 'address', 'varchar', 250, array('notnull' => true));
        $this->_addField('number', 'number', 'int', 11, array('notnull' => true));
        $this->_addField('district', 'district', 'varchar', 200, array('notnull' => true));
        $this->_addField('complement', 'complement', 'varchar', 256, array('notnull' => true));
        $this->_addField('city', 'city', 'varchar', 200, array('notnull' => true));
        $this->_addField('state', 'state', 'char', 2, array('notnull' => true));
        $this->_addField('cep', 'cep', 'varchar', 8, array('notnull' => true));
        $this->_addField('student_image', 'student_image', 'varchar', 256, array());
        $this->_addField('responsible_name', 'responsible_name', 'varchar', 250, array('notnull' => true));
        $this->_addField('responsible_parentage', 'responsible_parentage', 'varchar', 100, array('notnull' => true));
        $this->_addField('responsible_phone', 'responsible_phone', 'varchar', 11, array('notnull' => true));
        $this->_addField('responsible_cpf', 'responsible_cpf', 'varchar', 11, array('notnull' => true));
        $this->_addField('responsible_rg', 'responsible_rg', 'varchar', 20, array('notnull' => true));
        $this->_addField('emergency_name', 'emergency_name', 'varchar', 100, array('notnull' => true));
        $this->_addField('emergency_parentage', 'emergency_parentage', 'varchar', 50, array('notnull' => true));
        $this->_addField('emergency_phone', 'emergency_phone', 'varchar', 11, array('notnull' => true));
        $this->_addField('formed', 'formed', 'char', 1, array('notnull' => true));
        $this->_addField('formed_year', 'formed_year', 'year', 4, array('notnull' => true));
        $this->_addField('formed_school', 'formed_school', 'varchar', 100, array('notnull' => true));
        $this->_addField('formed_school_city', 'formed_school_city', 'varchar', 100, array('notnull' => true));
        $this->_addField('pre_entrance', 'pre_entrance', 'char', 1, array('notnull' => true));
        $this->_addField('pre_entrance_name', 'pre_entrance_name', 'varchar', 100, array('notnull' => true));
        $this->_addField('pre_entrance_city', 'pre_entrance_city', 'varchar', 100, array('notnull' => true));
        $this->_addField('pretension_courses', 'pretension_courses', 'varchar', 200, array('notnull' => true));
        $this->_addField('pretension_universities', 'pretension_universities', 'varchar', 250, array('notnull' => true));
        $this->_addField('enem', 'enem', 'char', 1, array('notnull' => true));
        $this->_addField('controlled_medication', 'controlled_medication', 'char', 1, array('notnull' => true));
        $this->_addField('controlled_medication_desc', 'controlled_medication_desc', 'varchar', 150, array('notnull' => true));
        $this->_addField('special_need', 'special_need', 'char', 1, array('notnull' => true));
        $this->_addField('special_need_desc', 'special_need_desc', 'varchar', 150, array('notnull' => true));
        $this->_addField('allergy', 'allergy', 'char', 1, array('notnull' => true));
        $this->_addField('allergy_desc', 'allergy_desc', 'varchar', 150, array('notnull' => true));
        $this->_addField('course_information', 'course_information', 'varchar', 50, array('notnull' => true));
        $this->_addField('payment_cash', 'payment_cash', 'boolean', 1, array('notnull' => true));
        $this->_addField('payment_cash_discount', 'payment_cash_discount', 'boolean', 4, array('notnull' => true));
        $this->_addField('payment_cash_amounth', 'payment_cash_amounth', 'varchar', 11, array('notnull' => true));
        $this->_addField('payment_installment', 'payment_installment', 'boolean', 1, array('notnull' => true));
        $this->_addField('payment_installment_parcels', 'payment_installment_parcels', 'boolean', 4, array('notnull' => true));
        $this->_addField('payment_installment_parcels_value', 'payment_installment_parcels_value', 'varchar', 11, array('notnull' => true));
        $this->_addField('excluido', 'excluido', 'char', 1, array('notnull' => true, 'default' => 'N'));

        
        $this->_addForeignRelation('sai_presencas', self::ONE_TO_MANY, 'Sai_presencas', 'usuario', null, null, null);
        $this->_addForeignRelation('sai_usuarios_acesso', self::ONE_TO_MANY, 'Sai_usuarios_acesso', 'id_usuario', null, null, null);
    }

    /**
     * Recupera um objeto estaticamente
     * @author Hugo Ferreira da Silva
     * @return Sai_usuarios
     */
    public static function staticGet($pk, $pkValue = null)
    {
        $obj = new Sai_usuarios;
        $obj->get($pk, $pkValue);
        return $obj;
    }

	/**
	 * chama o destrutor pai
	 *
	 */
	function __destruct()
	{
		parent::__destruct();
	}
	
    #------------------------------------------------------#
    # Coloque todos os metodos personalizados abaixo de    #
    # END AUTOCODE                                         #
    #------------------------------------------------------#
    #### END AUTOCODE


}
