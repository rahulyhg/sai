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
 * Classe generada para a tabela "sai_telas"
 * in 2018-12-06
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br/lumine
 * @package mapper
 *
 */

class Sai_telas extends Lumine_Base {

    // sobrecarga
    protected $_tablename = 'sai_telas';
    protected $_package   = 'mapper';
    
    
    public $id;
    public $descricao;
    public $icone;
    public $link;
    public $ordem;
    public $grupo;
    public $sai_perfil_telas = array();
    
    
    
    /**
     * Inicia os valores da classe
     * @author Hugo Ferreira da Silva
     * @return void
     */
    protected function _initialize()
    {
        
        
        # nome_do_membro, nome_da_coluna, tipo, comprimento, opcoes
        
        $this->_addField('id', 'id', 'int', 11, array('primary' => true, 'notnull' => true, 'autoincrement' => true));
        $this->_addField('descricao', 'descricao', 'varchar', 45, array());
        $this->_addField('icone', 'icone', 'varchar', 45, array());
        $this->_addField('link', 'link', 'varchar', 45, array());
        $this->_addField('ordem', 'ordem', 'int', 3, array());
        $this->_addField('grupo', 'grupo', 'int', 2, array());

        
        $this->_addForeignRelation('sai_perfil_telas', self::ONE_TO_MANY, 'Sai_perfil_telas', 'id_tela', null, null, null);
    }

    /**
     * Recupera um objeto estaticamente
     * @author Hugo Ferreira da Silva
     * @return Sai_telas
     */
    public static function staticGet($pk, $pkValue = null)
    {
        $obj = new Sai_telas;
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
