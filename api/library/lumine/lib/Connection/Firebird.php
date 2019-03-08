<?php
/**
 * Conexao com o firebird
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 * @package Lumine_Connection
 */


Lumine::load('Connection_IConnection');

/**
 * Conexao com o firebird
 * @package Lumine_Connection
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 */
class Lumine_Connection_Firebird extends Lumine_EventListener implements ILumine_Connection
{
	/**
	 * conexao fechada
	 * @var int
	 */
    const CLOSED           = 0;
    /**
     * conexao aberta
     * @var int
     */
    const OPEN             = 1;
	/**
	 * versao do servidor
	 * @var int
	 */
    const SERVER_VERSION   = 10;
    /**
     * versao do cliente
     * @var int
     */
    const CLIENT_VERSION   = 11;
    /**
     * informacoes do host
     * @var int
     */
    const HOST_INFO        = 12;
    /**
     * codigo do protocolo
     * @var int
     */
    const PROTOCOL_VERSION = 13;
    /**
     * nome da funcao para gerar dados resultados aleatorios
     * @var string
     */
    const RANDOM_FUNCTION  = '';
    /**
     * caracter de escape
     * @var string
     */
    const ESCAPE_CHAR      = '\\';
    
    /**
     * tipos de eventos suportados
     * @var array
     */
    protected $_event_types = array(
    	Lumine_Event::PRE_EXECUTE,
    	Lumine_Event::POS_EXECUTE,
    	Lumine_Event::PRE_CONNECT,
    	Lumine_Event::POS_CONNECT,
    	Lumine_Event::PRE_CLOSE,
    	Lumine_Event::POS_CLOSE,
    	Lumine_Event::EXECUTE_ERROR,
    	Lumine_Event::CONNECTION_ERROR
    );
    
    /**
     * conexao aberta
     * @var resource
     */
    private $conn_id;
    /**
     * nome do banco de dados
     * @var string
     */
    private $database;
    /**
     * usuario do banco
     * @var string
     */
    private $user;
    /**
     * senha do banco de dados
     * @var string
     */
    private $password;
    /**
     * porta de conexao com o banco
     * @var string
     */
    private $port;
    /**
     * host de conexao
     * @var string
     */
    private $host;
    /**
     * opcoes de conexao
     * @var array
     */
    private $options;
    /**
     * estado atual da conexao
     * @var int
     */
    private $state;
    /**
     * referencias de transacoes abertas
     * @var array
     */
    private $transactions = array();
    /**
     * numero de transacoes abertas
     * @var int
     */
    private $transactions_count = 0;
    
    /**
     * instancia da conexao
     * @var ILumine_Connection
     */
    private static $instance = null;

    /**
     * formato de data
     * @var string
     */
    private $ibase_datefmt = '%Y-%m-%d';
    /**
     * formato de horas
     * @var string
     */
    private $ibase_timefmt = '%H:%M:%S';
    /**
     * formato do timestamp
     * @var string
     */
    private $ibase_timestampfmt = '%Y-%m-%d %H:%M:%S';
    
    /**
     * Recupera a instancia
     * 
     * @author Hugo Ferreira da Silva
     * @link http://www.hufersil.com.br/
     * @return ILumine_Connection
     */
    static public function getInstance()
    {
        if(self::$instance == null)
        {
            self::$instance = new Lumine_Connection();
        }
        
        return self::$instance;
    }
    
    /**
     * @see ILumine_Connection::connect()
     */
    public function connect()
    {
        if($this->conn_id && $this->state == self::OPEN)
        {
            Lumine_Log::debug( 'Utilizando conexao cacheada com '.$this->getDatabase());
            return true;
        }

        $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::PRE_CONNECT, $this));
        
        $hostString = $this->getHost();
        if($this->getPort() != '') 
        {
            $hostString .=  ':' . $this->getPort();
        }
        
        $hostString = empty($hostString) ? $this->getDatabase() : $hostString . ':' . $this->getDatabase();
        
        if(isset($this->options['socket']) && $this->options['socket'] != '')
        {
            $hostString .= ':' . $this->options['socket'];
        }

        $flags = isset($this->options['flags']) ? $this->options['flags'] : null;

        if(isset($this->options['persistent']) && $this->options['persistent'] == true)
        {
            Lumine_Log::debug( 'Criando conexao persistente com '.$this->getDatabase());

            $this->conn_id = @ibase_pconnect($hostString, $this->getUser(), $this->getPassword());
        } else {
            Lumine_Log::debug( 'Criando conexao com '.$this->getDatabase());
            $this->conn_id = @ibase_connect($hostString, $this->getUser(), $this->getPassword());
        }
        
        if( !$this->conn_id )
        {
            $this->state = self::CLOSED;
            $msg = 'Nao foi possivel conectar no banco de dados: ' . $this->getDatabase().' - '.$this->getErrorMsg();
            Lumine_Log::error( $msg );
            
            $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::CONNECTION_ERROR, $this, $msg));
            throw new Exception( $msg );
            
            return false;
        }
        
        if (function_exists('ibase_timefmt'))
        {
            ibase_timefmt($this->ibase_datefmt,IBASE_DATE );
            if ($this->dialect == 1) ibase_timefmt($this->ibase_datefmt,IBASE_TIMESTAMP );
            else ibase_timefmt($this->ibase_timestampfmt,IBASE_TIMESTAMP );
            ibase_timefmt($this->ibase_timefmt,IBASE_TIME );
            
        } else {
            ini_set("ibase.timestampformat", $this->ibase_timestampfmt);
            ini_set("ibase.dateformat", $this->ibase_datefmt);
            ini_set("ibase.timeformat", $this->ibase_timefmt);
        }
        
        $this->state = self::OPEN;
        $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::POS_CONNECT, $this));
        
        return true;
    }
    
    /**
     * @see ILumine_Connection::close()
     */
    public function close()
    {
        $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::PRE_CLOSE, $this));
        if($this->conn_id && $this->state != self::CLOSED)
        {
            $this->state = self::CLOSED;
            Lumine_Log::debug( 'Fechando conexao com '.$this->getDatabase());
            ibase_close($this->conn_id);
        }
        $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::POS_CLOSE, $this));
    }
    /**
     * @see ILumine_Connection::getState()
     */
    public function getState()
    {
        return $this->state;
    }
    /**
     * @see ILumine_Connection::setDatabase()
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }
    /**
     * @see ILumine_Connection::getDatabase()
     */
    public function getDatabase()
    {
        return $this->database;
    }
    /**
     * @see ILumine_Connection::setUser()
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    /**
     * @see ILumine_Connection::getUser()
     */
    public function getUser()
    {
        return $this->user;
    }
	/**
	 * @see ILumine_Connection::setPassword()
	 */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * @see ILumine_Connection::getPassword()
     */
    public function getPassword()
    {
        return $this->password;
    }
	/**
	 * @see ILumine_Connection::setPort()
	 */
    public function setPort($port)
    {
        $this->port = $port;
    }
    /**
     * @see ILumine_Connection::getPort()
     */
    public function getPort()
    {
        return $this->port;
    }
    /**
     * @see ILumine_Connection::setHost()
     */
    public function setHost($host)
    {
        $this->host = $host;
    }
    /**
     * @see ILumine_Connection::getHost()
     */
    public function getHost()
    {
        return $this->host;
    }
    /**
     * @see ILumine_Connection::setOptions()
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
    /**
     * @see ILumine_Connection::getOptions()
     */
    public function getOptions()
    {
        return $this->options;
    }
    /**
     * @see ILumine_Connection::setOption()
     */
    public function setOption($name, $val)
    {
        $this->options[ $name ] = $val;
    }
    /**
     * @see ILumine_Connection::getOption()
     */
    public function getOption($name)
    {
        if(empty($this->options[$name]))
        {
            return null;
        }
        return $this->options[$name];
    }
	/**
	 * @see ILumine_Connection::getErrorMsg()
	 */
    public function getErrorMsg()
    {
        $msg = '';
        if($this->conn_id) 
        {
            $msg = ibase_errmsg();
        } else {
            $msg = ibase_errmsg();
        }
        return $msg;
    }
    /**
     * @see ILumine_Connection::getTables()
     */
    public function getTables()
    {
        if( ! $this->connect() )
        {
            return false;
        }
        
        $rs = $this->executeSQL("SELECT RDB\$RELATION_NAME FROM RDB\$RELATIONS WHERE RDB\$SYSTEM_FLAG=0 AND RDB\$VIEW_BLR IS NULL;");
        
        $list = array();
        
        while($row = ibase_fetch_row($rs))
        {
            $list[] = trim($row[0]);
        }
        
        return $list;
    }
    /**
     * @see ILumine_Connection::getForeignKeys()
     */
    public function getForeignKeys($tablename)
    {
        if( ! $this->connect() )
        {
            return false;
        }
        
        $fks = array();
        $rs = $this->executeSQL("
               SELECT rc.RDB\$CONSTRAINT_NAME,
                      s.RDB\$FIELD_NAME AS \"field_name\",
                      refc.RDB\$UPDATE_RULE AS \"on_update\",
                      refc.RDB\$DELETE_RULE AS \"on_delete\",
                      i2.RDB\$RELATION_NAME AS \"references_table\",
                      s2.RDB\$FIELD_NAME AS \"references_field\",
                      (s.RDB\$FIELD_POSITION + 1) AS \"field_position\"
                 FROM RDB\$INDEX_SEGMENTS s
            LEFT JOIN RDB\$INDICES i ON i.RDB\$INDEX_NAME = s.RDB\$INDEX_NAME
            LEFT JOIN RDB\$RELATION_CONSTRAINTS rc ON rc.RDB\$INDEX_NAME = s.RDB\$INDEX_NAME
            LEFT JOIN RDB\$REF_CONSTRAINTS refc ON rc.RDB\$CONSTRAINT_NAME = refc.RDB\$CONSTRAINT_NAME
            LEFT JOIN RDB\$RELATION_CONSTRAINTS rc2 ON rc2.RDB\$CONSTRAINT_NAME = refc.RDB\$CONST_NAME_UQ
            LEFT JOIN RDB\$INDICES i2 ON i2.RDB\$INDEX_NAME = rc2.RDB\$INDEX_NAME
            LEFT JOIN RDB\$INDEX_SEGMENTS s2 ON i2.RDB\$INDEX_NAME = s2.RDB\$INDEX_NAME
                WHERE i.RDB\$RELATION_NAME='".$tablename."'
                  AND rc.RDB\$CONSTRAINT_TYPE = 'FOREIGN KEY'
             ORDER BY s.RDB\$FIELD_POSITION");
        
        while( $row = ibase_fetch_assoc($rs, IBASE_FETCH_BLOBS) )
        {
            $name = trim($row['references_table']);
            
            if(isset($fks[ $name ]))
            {
                $name = $name . '_' . trim($row['references_field']);
            }
            
            $fks[ $name ]['from'] = trim($row['field_name']);
            $fks[ $name ]['to'] = trim($row['references_table']);
            $fks[ $name ]['to_column'] = trim($row['references_field']);
            $fks[ $name ]['delete'] = empty($row['on_delete']) ? 'RESTRICT' : trim(strtoupper($row['on_delete']));
            $fks[ $name ]['update'] = empty($row['on_update']) ? 'RESTRICT' : trim(strtoupper($row['on_update']));
        }
        
        return $fks;
    }
    /**
     * @see ILumine_Connection::getServerInfo()
     */
    public function getServerInfo($type = null)
    {
        if($this->conn_id && $this->state == self::OPEN)
        {
            /*switch($type)
            {
                case self::CLIENT_VERSION:
                    return ibase_server_info($this->conn_id);
                    break;
                case self::HOST_INFO:
                    return mysql_get_host_info($this->conn_id);
                    break;
                case self::PROTOCOL_VERSION:
                    return mysql_get_proto_info($this->conn_id);
                    break;
                case self::SERVER_VERSION:
                default:
                    return mysql_get_server_info($this->conn_id);
                    break;
            }*/
            return '';
            
        } 
        throw new Lumine_Exception('A conexao nao esta aberta', Lumine_Exception::WARNING);
    }
    /**
     * @see ILumine_Connection::describe()
     */
    public function describe($tablename)
    {
        $sql = "
            SELECT r.RDB\$FIELD_NAME AS field_name,
                    r.RDB\$DESCRIPTION AS field_description,
                    r.RDB\$DEFAULT_VALUE AS field_default_value,
                    r.RDB\$NULL_FLAG AS field_not_null_constraint,
                    f.RDB\$FIELD_LENGTH AS field_length,
                    f.RDB\$FIELD_PRECISION AS field_precision,
                    f.RDB\$FIELD_SCALE AS field_scale,
                    CASE f.RDB\$FIELD_TYPE
                      WHEN 261 THEN 'BLOB'
                      WHEN 14 THEN 'CHAR'
                      WHEN 40 THEN 'VARCHAR'
                      WHEN 11 THEN 'FLOAT'
                      WHEN 27 THEN 'FLOAT'
                      WHEN 10 THEN 'FLOAT'
                      WHEN 16 THEN 'INT64'
                      WHEN 8 THEN 'INTEGER'
                      WHEN 9 THEN 'QUAD'
                      WHEN 7 THEN 'SMALLINT'
                      WHEN 12 THEN 'DATE'
                      WHEN 13 THEN 'TIME'
                      WHEN 35 THEN 'DATETIME'
                      WHEN 37 THEN 'VARCHAR'
                      ELSE 'UNKNOWN'
                    END AS field_type,
                    f.RDB\$FIELD_SUB_TYPE AS field_subtype,
                    coll.RDB\$COLLATION_NAME AS field_collation,
                    cset.RDB\$CHARACTER_SET_NAME AS field_charset,
                    
                    (SELECT count(*)
                     FROM RDB\$INDEX_SEGMENTS s
                LEFT JOIN RDB\$RELATION_CONSTRAINTS rc ON rc.RDB\$INDEX_NAME = s.RDB\$INDEX_NAME
                LEFT JOIN RDB\$INDICES i ON i.RDB\$INDEX_NAME = s.RDB\$INDEX_NAME
                    WHERE i.RDB\$RELATION_NAME = r.RDB\$RELATION_NAME
                      AND rc.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY'
                      AND s.RDB\$FIELD_NAME = r.RDB\$FIELD_NAME
                  ) as primary_key
                  
               FROM RDB\$RELATION_FIELDS r
               LEFT JOIN RDB\$FIELDS f ON r.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
               LEFT JOIN RDB\$COLLATIONS coll ON r.RDB\$COLLATION_ID = coll.RDB\$COLLATION_ID
                AND f.RDB\$CHARACTER_SET_ID = coll.RDB\$CHARACTER_SET_ID
               LEFT JOIN RDB\$CHARACTER_SETS cset ON f.RDB\$CHARACTER_SET_ID = cset.RDB\$CHARACTER_SET_ID
              WHERE r.RDB\$RELATION_NAME='".$tablename."'
            ORDER BY r.RDB\$FIELD_POSITION
        ";
        $rs = $this->executeSQL( $sql );
        
        $data = array();
        while($row = ibase_fetch_assoc($rs, IBASE_FETCH_BLOBS))
        {
            $name           = trim($row['FIELD_NAME']);
            $type_native    = trim(strtolower($row['FIELD_TYPE']));
            $type           = $type_native;
            $length         = $row['FIELD_LENGTH'];

            $notnull        = empty($row['FIELD_NOT_NULL_CONSTRAINT']) ? false : true;
            $primary        = ! empty($row['PRIMARY_KEY'])  ? true : false;
            $default        = empty($row['FIELD_DEFAULT_VALUE'])  ? null : $row[4];
            $autoincrement  =  $this->checaAutoIncrement( $tablename, $name );
            
            $data[] = array($name, $type_native, $type, $length, $primary, $notnull, $default, $autoincrement, array());
        }
        return $data;
    }
    /**
     * @see ILumine_Connection::executeSQL()
     */
    public function executeSQL($sql)
    {
        $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::PRE_EXECUTE, $this, '', $sql));
        $this->connect();
        
        if( preg_match('@\s*(LIMIT.+?)$@i', $sql, $reg))
        {
            $sql = str_replace($reg[1], '', $sql);
            
            $limite = strtoupper($reg[1]);
            $limite = str_replace('LIMIT','FIRST', $limite);
            $limite = str_replace('OFFSET','SKIP', $limite);
            
            $sql = preg_replace('@^SELECT\s+@i', 'SELECT ' . $limite. ' ', $sql);
            
            Lumine_Log::debug('Consulta transformada para Firebird: ' . $sql);
        }
        
        $rs = @ibase_query($sql, $this->conn_id);
        
        if( ! $rs )
        {
            $msg = $this->getErrorMsg();
            $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::EXECUTE_ERROR, $this, $msg, $sql));
            throw new Lumine_Exception("Falha na consulta: " . $msg, Lumine_Exception::QUERY_ERROR);
        }
        $this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::CONNECTION_ERROR, $this, '', $sql));
        return $rs;
    }
    /**
     * @see ILumine_Connection::setLimit()
     */
    public function setLimit($offset = null, $limit = null) 
    {
        if($offset == null && $limit == null)
        {
            return;
        } else if($offset == null && $limit != null) {
            return sprintf("LIMIT %d", $limit);
        } else if($offset != null && $limit == null) {
            return sprintf("LIMIT %d", $offset);
        } else {
            return sprintf("LIMIT %d OFFSET %d", $limit, $offset);
        }
    }
    /**
     * @see ILumine_Connection::escape()
     */
    public function escape($str) 
    {
        /**
         * TODO: fazer um escape decente, uma vez que o Firebird nao tem um
         */
        return addslashes( $str );
        
    }
    /**
     * @see ILumine_Connection::escapeBlob()
     */
    public function escapeBlob($blob)
    {
        return $this->escape( $blob );
    }
    /**
     * @see ILumine_Connection::affected_rows()
     */
    public function affected_rows()
    {
        if($this->state == self::OPEN)
        {
            return ibase_affected_rows($this->conn_id);
        }
        throw new Lumine_Exception('conexao nao esta aberta', Lumine_Exception::ERRO);
    }
    /**
     * @see ILumine_Connection::num_rows()
     */
    public function num_rows($rs)
    {
        /**
         * TODO: um algoritimo mais otimizado para fazer isso
         */
        
        $rows = 0;
        while( ibase_fetch_row($rs) )
        {
            $rows++;
        }
        
        return $rows;
    }
    /**
     * @see ILumine_Connection::random()
     */
    public function random()
    {
        return self::RANDOM_FUNCTION;
    }
    /**
     * @see ILumine_Connection::getEscapeChar()
     */
    public function getEscapeChar()
    {
        return self::ESCAPE_CHAR;
    }
    
   /**
    * @see ILumine_Connection::begin()
    */
    public function begin($transactionID=null)
    {
        $id = $this->transactions_count++;
        $this->transactions[ $id ] = ibase_trans( IBASE_DEFAULT, $this->conn_id );
        
        return $id;
    }
	/**
	 * @see ILumine_Connection::commit()
	 */
    public function commit($transactionID=null)
    {
        if( is_null($transactionID) )
        {
            $id = $this->transactions_count-1;
        } else {
            $id = $transactionID;
        }
        
        if( isset($this->transactions[$id]) )
        {
            ibase_commit($this->conn_id);
            unset($this->transactions[$id]);
        }
    }
	/**
	 * @see ILumine_Connection::rollback()
	 */
    public function rollback($transactionID=null)
    {
        if( is_null($transactionID) )
        {
            $id = $this->transactions_count-1;
        } else {
            $id = $transactionID;
        }
        
        if( isset($this->transactions[$id]) )
        {
            ibase_rollback($this->conn_id, $this->transactions[$id]);
            unset($this->transactions[$id]);
        }
    }
    
    /**
     * funcao que checa se o campo possui uma trigger que pega um contador 
     *
     * @author Hugo Ferreira da Silva
     * @link http://www.hufersil.com.br/
     * @param string $tablename Nome da tabela
     * @param string $fieldname Nome do campo
     * @return boolean True se encontrar a trigger com o gerador do contador do contrario false 
     */
    public function checaAutoIncrement( $tablename, $fieldname )
    {
        $sql = "SELECT RDB\$TRIGGER_SOURCE AS triggers FROM RDB\$TRIGGERS
                 WHERE (RDB\$SYSTEM_FLAG IS NULL
                    OR RDB\$SYSTEM_FLAG = 0)
                   AND RDB\$RELATION_NAME='".$tablename."'";

        $rs = $this->executeSQL($sql);
        
        while( $row = ibase_fetch_assoc($rs, IBASE_FETCH_BLOBS) )
        {
            $exp = '@new\.'.$fieldname.'\s+=\s+gen_id\((\w+)@is';
            $res = preg_match($exp, trim($row['TRIGGERS']), $reg);

            // oba! achamos o lance            
            if( $res )
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Recupera o proximo ID para o gerador
     *
     * @author Hugo Ferreira da Silva
     * @link http://www.hufersil.com.br/
     * @param string $generator Nome do gerador
     * @param int $step Step para incrementar no gerador
     * @return int Novo valor 
     */
    public function genID( $generator, $step = 1 )
    {
        $sql = sprintf("SELECT gen_id(%s, %d) as CODIGO FROM RDB\$DATABASE ", $generator, $step);

        $rs = $this->executeSQL( $sql );
        
        if( $row = ibase_fetch_row($rs) )
        {
            return $row[0];
        }
        return 1;
    }
    /**
     * @see Lumine_EventListener::__destruct()
     */
    function __destruct()
    {
        unset($this->conn_id);
        unset($this->database);
        unset($this->user);
        unset($this->password);
        unset($this->port);
        unset($this->host);
        unset($this->options);
        unset($this->state);
        unset($this->transactions);
        unset($this->transactions_count);
        //unset(self::$instance);
    }
}


?>