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
 * Classe generada para a tabela "sai_presencas"
 * in 2019-02-27
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br/lumine
 * @package mapper
 *
 */

class Sai_presencas extends Lumine_Base {

    // sobrecarga
    protected $_tablename = 'sai_presencas';
    protected $_package   = 'mapper';
    
    
    public $id;
    public $usuario;
    public $status;
    public $dia;
    public $hora_registro;
    
    
    
    /**
     * Inicia os valores da classe
     * @author Hugo Ferreira da Silva
     * @return void
     */
    protected function _initialize()
    {
        
        
        # nome_do_membro, nome_da_coluna, tipo, comprimento, opcoes
        
        $this->_addField('id', 'id', 'int', 11, array('primary' => true, 'notnull' => true, 'autoincrement' => true));
        $this->_addField('usuario', 'usuario', 'int', 11, array('notnull' => true, 'foreign' => '1', 'onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT', 'linkOn' => 'id', 'class' => 'Sai_usuarios'));
        $this->_addField('status', 'status', 'char', 1, array('notnull' => true, 'default' => 'p'));
        $this->_addField('dia', 'dia', 'date', null, array('notnull' => true));
        $this->_addField('hora_registro', 'hora_registro', 'datetime', null, array('notnull' => true));

        
    }

    /**
     * Recupera um objeto estaticamente
     * @author Hugo Ferreira da Silva
     * @return Sai_presencas
     */
    public static function staticGet($pk, $pkValue = null)
    {
        $obj = new Sai_presencas;
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
