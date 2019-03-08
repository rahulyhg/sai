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
 * Classe generada para a tabela "sai_unidades"
 * in 2019-02-27
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br/lumine
 * @package mapper
 *
 */

class Sai_unidades extends Lumine_Base {

    // sobrecarga
    protected $_tablename = 'sai_unidades';
    protected $_package   = 'mapper';
    
    
    public $id;
    public $unidade;
    public $excluido;
    public $sai_materiais = array();
    public $sai_turmas = array();
    public $sai_usuarios = array();
    
    
    
    /**
     * Inicia os valores da classe
     * @author Hugo Ferreira da Silva
     * @return void
     */
    protected function _initialize()
    {
        
        
        # nome_do_membro, nome_da_coluna, tipo, comprimento, opcoes
        
        $this->_addField('id', 'id', 'int', 11, array('primary' => true, 'notnull' => true, 'autoincrement' => true));
        $this->_addField('unidade', 'unidade', 'varchar', 50, array('notnull' => true));
        $this->_addField('excluido', 'excluido', 'char', 1, array('notnull' => true, 'default' => 'N'));

        
        $this->_addForeignRelation('sai_materiais', self::ONE_TO_MANY, 'Sai_materiais', 'unidade', null, null, null);
        $this->_addForeignRelation('sai_turmas', self::ONE_TO_MANY, 'Sai_turmas', 'unidade', null, null, null);
        $this->_addForeignRelation('sai_usuarios', self::ONE_TO_MANY, 'Sai_usuarios', 'unidade', null, null, null);
    }

    /**
     * Recupera um objeto estaticamente
     * @author Hugo Ferreira da Silva
     * @return Sai_unidades
     */
    public static function staticGet($pk, $pkValue = null)
    {
        $obj = new Sai_unidades;
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
