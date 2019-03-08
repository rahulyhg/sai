<?php
/**
 * Conexao com MySQL
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 * @package Lumine_Connection
 */

Lumine::load('Connection_IConnection');

/**
 * Conexao com MySQL
 * @package Lumine_Connection
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 */
class Lumine_Connection_MySQL extends Lumine_EventListener implements ILumine_Connection 
{

	/**
	 * Estado fechado
	 * @var int
	 */
	const CLOSED           = 0;
	/**
	 * Estado aberto
	 * @var int
	 */
	const OPEN             = 1;

	/**
	 * Constante para versao do servidor
	 * @var int
	 */
	const SERVER_VERSION   = 10;
	/**
	 * Constante para versao do cliente
	 * @var int
	 */
	const CLIENT_VERSION   = 11;
	/**
	 * Constante para informacoes do host
	 * @var int
	 */
	const HOST_INFO        = 12;
	/**
	 * tipo de protocolo
	 * @var int
	 */
	const PROTOCOL_VERSION = 13;
	/**
	 * funcao para retorno de registros aleatorios do banco
	 * @var string
	 */
	const RANDOM_FUNCTION  = 'rand()';
	/**
	 * caractere de escape de strings
	 * @var string
	 */
	const ESCAPE_CHAR      = '\\';
	
	/**
	 * Tipos de eventos disparados pela classe
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
	 * ID da conexao
	 * @var resource
	 */
	private $conn_id;
	/**
	 * nome do banco de dados
	 * @var string
	 */
	private $database;
	/**
	 * nome do usuario
	 * @var string
	 */
	private $user;
	/**
	 * senha do usuario
	 * @var string
	 */
	private $password;
	/**
	 * porta de conexao
	 * @var integer
	 */
	private $port;
	/**
	 * host do banco de dados
	 * @var string
	 */
	private $host;
	/**
	 * opcoes
	 * @var array
	 */
	private $options;
	/**
	 * Estado atual
	 * @var int
	 */
	private $state;
	
	/**
	 * Instancia de conexao
	 * @var ILumine_Connection
	 */
	private static $instance = null;
	
	/**
	 * Retorna a conexao
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
			mysql_select_db($this->getDatabase(), $this->conn_id);
			return true;
		}

		$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::PRE_CONNECT, $this));
		
		$hostString = $this->getHost();
		if($this->getPort() != '') 
		{
			$hostString .=  ':' . $this->getPort();
		}
		if(isset($this->options['socket']) && $this->options['socket'] != '')
		{
			$hostString .= ':' . $this->options['socket'];
		}
		$flags = isset($this->options['flags']) ? $this->options['flags'] : null;
					
		if(isset($this->options['persistent']) && $this->options['persistent'] == true)
		{
			Lumine_Log::debug( 'Criando conexao persistente com '.$this->getDatabase());
			$this->conn_id = @mysql_pconnect($hostString, $this->getUser(), $this->getPassword(), $flags);
		} else {
			Lumine_Log::debug( 'Criando conexao com '.$this->getDatabase());
			$this->conn_id = @mysql_connect($hostString, $this->getUser(), $this->getPassword(), true, $flags);
		}
		
		if( !$this->conn_id )
		{
			$this->state = self::CLOSED;
			$msg = 'nao foi possivel conectar no banco de dados: ' . $this->getDatabase().' - '.$this->getErrorMsg();
			Lumine_Log::error( $msg );
			
			$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::CONNECTION_ERROR, $this, $msg));
			throw new Exception( $msg );
			
			return false;
		}
		
		// seleciona o banco
		mysql_select_db($this->getDatabase(), $this->conn_id);
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
			mysql_close($this->conn_id);
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
			$msg = mysql_error($this->conn_id);
		} else {
			$msg = mysql_error();
		}
		return $msg;
	}
	
	public function getErrorId()
	{
		$idErro = '';
		if($this->conn_id) 
		{
			$idErro = mysql_errno($this->conn_id);
		} else {
			$idErro = mysql_errno();
		}
		return $idErro;
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
		
		$rs = $this->executeSQL("show tables");
		
		$list = array();
		
		while($row = mysql_fetch_row($rs))
		{
			$list[] = $row[0];
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
		$rs = $this->executeSQL("SHOW CREATE TABLE ".$tablename);
		
		$result = mysql_fetch_row($rs);
		$result[0] = preg_replace("(\r|\n)",'\n', $result[0]);
		$matches = array();

		preg_match_all('@FOREIGN KEY \(`([a-z,A-Z,0-9,_]+)`\) REFERENCES `([a-z,A-Z,0-9,_]+)` \(`([a-z,A-Z,0-9,_]+)`\)(.*?)(\r|\n|\,)@i', $result[1], $matches);
		
		for($i=0; $i<count($matches[0]); $i++)
		{
			$name = $matches[2][$i];
			if(isset($fks[ $name ]))
			{
				$name = $name . '_' . $matches[3][$i];
			}
			
			$fks[ $name ]['from'] = $matches[1][$i];
			$fks[ $name ]['to'] = $matches[2][$i];
			$fks[ $name ]['to_column'] = $matches[3][$i];
			
			$reg = array();
			if(preg_match('@(.*?)ON UPDATE (RESTRICT|CASCADE)@i', $matches[4][$i], $reg))
			{
				$fks[ $name ]['update'] = strtoupper($reg[2]);
			} else {
				$fks[ $name ]['update'] = 'RESTRICT';
			}
			if(preg_match('@(.*?)ON DELETE (RESTRICT|CASCADE)@i', $matches[4][$i], $reg))
			{
				$fks[ $name ]['delete'] = strtoupper($reg[2]);
			} else {
				$fks[ $name ]['delete'] = 'RESTRICT';
			}
			
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
			switch($type)
			{
				case self::CLIENT_VERSION:
					return mysql_get_client_info();
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
			}
			return '';
			
		} 
		throw new Lumine_Exception('A conexao nao esta aberta', Lumine_Exception::WARNING);
	}
	/**
	 * @see ILumine_Connection::describe()
	 */
	public function describe($tablename)
	{
		$sql = "DESCRIBE ". $tablename;
		$rs = $this->executeSQL( $sql );
		
		$data = array();
		while($row = mysql_fetch_row($rs))
		{
			$name           = $row[0];
			$type_native    = $row[1];
			if(preg_match('@(\w+)\((\d+)\)@', $row[1], $r))
			{
				$type       = $r[1];
				$length     = $r[2];
			} else {
				$type       = $row[1];
				$length     = null;
			}
			
			switch( strtolower($type) )
			{
				case 'tinyblob': $length = 255; break;
				case 'tinytext': $length = 255; break;
				case 'blob': $length = 65535; break;
				case 'text': $length = 65535; break;
				case 'mediumblob': $length = 16777215; break;
				case 'mediumtext': $length = 16777215; break;
				case 'longblob': $length = 4294967295; break;
				case 'longtext': $length = 4294967295; break;
				case 'enum': $length = 65535; break;
			}
			
			$notnull        = $row[2] == 'YES' ? false : true;
			$primary        = $row[3] == 'PRI' ? true : false;
			$default        = $row[4] == 'NULL' ? null : $row[4];
			$autoincrement  = $row[5] == 'auto_increment' ? true : false;
			
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
		$rs = @mysql_query($sql, $this->conn_id);
		
		if( ! $rs )
		{
			$msg = $this->getErrorMsg();
			$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::EXECUTE_ERROR, $this, $msg, $sql));
			throw new Lumine_Exception("Falha na consulta: " . $msg, Lumine_Exception::QUERY_ERROR);
		}
		$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::POS_EXECUTE, $this, '', $sql));
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
			return sprintf("LIMIT %d, %d", $offset, $limit);
		}
	}
	/**
	 * @see ILumine_Connection::escape()
	 */
	public function escape($str) 
	{
		if($this->state == self::OPEN)
		{
			return mysql_real_escape_string($str, $this->conn_id);
		} else {
			return mysql_escape_string($str);
		}
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
			return mysql_affected_rows($this->conn_id);
		}
		throw new Lumine_Exception('Conexao nao esta aberta', Lumine_Exception::ERRO);
	}
	/**
	 * @see ILumine_Connection::num_rows()
	 */
	public function num_rows($rs)
	{
		return mysql_num_rows($rs);
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
		$this->executeSQL("BEGIN");
	}
	/**
	 * @see ILumine_Connection::commit()
	 */
	public function commit($transactionID=null)
	{
		$this->executeSQL("COMMIT");
	}
	/**
	 * @see ILumine_Connection::rollback()
	 */
	public function rollback($transactionID=null)
	{
		$this->executeSQL("ROLLBACK");
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
        
        parent::__destruct();
    }
}


?>