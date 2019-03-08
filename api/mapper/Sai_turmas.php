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
 * Classe generada para a tabela "sai_turmas"
 * in 2019-02-27
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br/lumine
 * @package mapper
 *
 */

class Sai_turmas extends Lumine_Base {

    // sobrecarga
    protected $_tablename = 'sai_turmas';
    protected $_package   = 'mapper';
    
    
    public $id;
    public $turma;
    public $curso;
    public $turno;
    public $unidade;
    public $excluido;
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
        $this->_addField('turma', 'turma', 'varchar', 50, array('notnull' => true));
        $this->_addField('curso', 'curso', 'varchar', 50, array('notnull' => true));
        $this->_addField('turno', 'turno', 'varchar', 50, array('notnull' => true));
        $this->_addField('unidade', 'unidade', 'int', 11, array('notnull' => true, 'foreign' => '1', 'onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT', 'linkOn' => 'id', 'class' => 'Sai_unidades'));
        $this->_addField('excluido', 'excluido', 'char', 1, array('notnull' => true, 'default' => 'N'));

        
        $this->_addForeignRelation('sai_usuarios', self::ONE_TO_MANY, 'Sai_usuarios', 'turma', null, null, null);
    }

    /**
     * Recupera um objeto estaticamente
     * @author Hugo Ferreira da Silva
     * @return Sai_turmas
     */
    public static function staticGet($pk, $pkValue = null)
    {
        $obj = new Sai_turmas;
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
