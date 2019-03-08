<?php
/**
 * classe de conexao com o PostgreSQL
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 * @package Lumine_Connection
 */

Lumine::load('Connection_IConnection');
/**
 * classe de conexao com o PostgreSQL
 * @package Lumine_Connection
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 */
class Lumine_Connection_PostgreSQL extends Lumine_EventListener implements ILumine_Connection
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
	 * funcao para retorno de registros aleatorios do banco
	 * @var string
	 */
	const PROTOCOL_VERSION = 13;
	/**
	 * funcao para retorno de registros aleatorios do banco
	 * @var string
	 */
	const RANDOM_FUNCTION  = 'random()';
	/**
	 * caractere de escape de strings
	 * @var string
	 */
	const ESCAPE_CHAR      = '\'';
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
	 * conexao
	 * @var resource
	 */
	private $conn_id;
	/**
	 * nome do banco
	 * @var string
	 */
	private $database;
	/**
	 * usuario de conexao
	 * @var string
	 */
	private $user;
	/**
	 * senha de conexao
	 * @var string
	 */
	private $password;
	/**
	 * porta de conexao
	 * @var string
	 */
	private $port;
	/**
	 * nome do host
	 * @var string
	 */
	private $host;
	/**
	 * lista de opcoes
	 * @var array
	 */
	private $options;
	/**
	 * ultima consulta
	 * @var resource
	 */
	private $last_rs;
	/**
	 * Estado atual
	 * @var int
	 */
	private static $state;
	
	/**
	 * instancia da classe
	 * @var ILumine_Connection
	 */
	private static $instance = null;
	
	/**
	 * recupera a instancia da classe
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
		if($this->conn_id && self::$state == self::OPEN)
		{
			Lumine_Log::debug( 'Utilizando conexao cacheada com '.$this->getDatabase());
			return true;
		}

		$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::PRE_CONNECT, $this));
		
		$hostString = 'host='.$this->getHost();
		$hostString .=  ' dbname=' . $this->getDatabase();
		if($this->getPort() != '') 
		{
			$hostString .=  ' port=' . $this->getPort();
		}
		
		if($this->getUser() != '') 
		{
			$hostString .=  ' user=' . $this->getUser();
		}
		
		if($this->getPassword() != '') 
		{
			$hostString .=  ' password=' . $this->getPassword();
		}
		
		if(isset($this->options['socket']) && $this->options['socket'] != '')
		{
			$hostString .= ' socket=' . $this->options['socket'];
		}
		$flags = isset($this->options['flags']) ? $this->options['flags'] : null;
					
		if(isset($this->options['persistent']) && $this->options['persistent'] == true)
		{
			Lumine_Log::debug('Criando conexao persistente com '.$this->getDatabase());
			$this->conn_id = pg_pconnect($hostString);
		} else {
			Lumine_Log::debug('Criando conexao com '.$this->getDatabase());
			$this->conn_id = pg_connect($hostString);
		}
		
		if( !$this->conn_id )
		{
			self::$state = self::CLOSED;
			$msg = 'nao foi possivel conectar no banco de dados: ' . $this->getDatabase().' - '.$this->getErrorMsg();
			Lumine_Log::error( $msg );
			
			$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::CONNECTION_ERROR, $this, $msg));
			throw new Exception( $msg );
			
			return false;
		}
		
		self::$state = self::OPEN;
		
		$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::POS_CONNECT, $this));
		
		return true;
	}
	/**
	 * @see ILumine_Connection::close()
	 */
	public function close()
	{
		$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::PRE_CLOSE, $this));
		if($this->conn_id && self::$state != self::CLOSED)
		{
			self::$state = self::CLOSED;
			Lumine_Log::debug( 'Fechando conexao com '.$this->getDatabase());
			pg_close($this->conn_id);
		}
		$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::POS_CLOSE, $this));
	}
	/**
	 * @see ILumine_Connection::getState()
	 */
	public function getState()
	{
		return self::$state;
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
			$msg = pg_last_error($this->conn_id);
		} else {
			$msg = pg_last_error();
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
		
		$sql = "select tablename from pg_tables where tablename not like 'pg\_%'
				and tablename not in ('sql_features', 'sql_implementation_info', 'sql_languages',
				'sql_packages', 'sql_sizing', 'sql_sizing_profiles','sql_parts') ";
				
		$rs = $this->executeSQL($sql);
		
		$list = array();
		
		while($row = pg_fetch_row($rs))
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
		
		$sql = "SELECT pg_catalog.pg_get_constraintdef(r.oid, true) as condef
				FROM pg_catalog.pg_constraint r, pg_catalog.pg_class c
				WHERE r.conrelid = c.oid AND r.contype = 'f'
				AND c.relname = '".$tablename."'";

		$fks = array();
		$rs = $this->executeSQL($sql);
		
		
		while($row = pg_fetch_row($rs))
		{
//						FOREIGN KEY (idusuario) REFERENCES usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE
			preg_match('@FOREIGN KEY \((\w+)\) REFERENCES (\w+)\((\w+)\)(.*?)$@i', str_replace('"', '', $row[0]), $matches);

			$name = $matches[2];
			if(isset($fks[ $name ]))
			{
				$name = $name . '_' . $matches[3];
			}
			
			$fks[ $name ]['from'] = $matches[1];
			$fks[ $name ]['to'] = $matches[2];
			$fks[ $name ]['to_column'] = $matches[3];
			
			$reg = array();
			if(preg_match('@(.*?)ON UPDATE (RESTRICT|CASCADE)@i', $matches[4], $reg))
			{
				$fks[ $name ]['update'] = strtoupper($reg[2]);
			} else {
				$fks[ $name ]['update'] = 'RESTRICT';
			}
			if(preg_match('@(.*?)ON DELETE (RESTRICT|CASCADE)@i', $matches[4], $reg))
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
		if($this->conn_id && self::$state == self::OPEN)
		{
			switch($type)
			{

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
	
		$sql = "
			SELECT
				f.attname AS name,
				pg_catalog.format_type(f.atttypid,f.atttypmod) AS type,
			
				CASE
					WHEN t.typlen < 0 THEN CASE WHEN f.atttypmod > 0 THEN f.atttypmod - 4 ELSE NULL END
					ELSE t.typlen
				END as length,
				
			
				CASE
				WHEN p.contype = 'p'
					THEN 't'
					ELSE 'f'
				END AS primarykey,
			
				CASE
				WHEN f.atthasdef = 't'
					THEN d.adsrc
				END AS default,
			
				f.attnotnull AS notnull,
				
				f.attnum AS number,
				f.attnum,
				
				CASE
				WHEN p.contype = 'u'
					THEN 't'
					ELSE 'f'
				END AS uniquekey,
				
				CASE
				WHEN p.contype = 'f'
					THEN g.relname
				END AS foreignkey,
			
				CASE
				WHEN p.contype = 'f'
					THEN p.confkey
				END AS foreignkey_fieldnum,
			
				CASE
				WHEN p.contype = 'f'
					THEN g.relname
				END AS foreignkey,
			
				CASE
				WHEN p.contype = 'f'
					THEN p.conkey
				END AS foreignkey_connnum
			
				FROM pg_attribute f
				JOIN pg_class c ON c.oid = f.attrelid
				JOIN pg_type t ON t.oid = f.atttypid
				LEFT JOIN pg_attrdef d ON d.adrelid = c.oid AND d.adnum = f.attnum
				LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
				LEFT JOIN pg_constraint p ON p.conrelid = c.oid AND f.attnum = ANY ( p.conkey )
				LEFT JOIN pg_class AS g ON p.confrelid = g.oid
				WHERE c.relkind = 'r'::char
					AND c.relname = '".$tablename."' AND f.attnum > 0
				ORDER BY number";
		
		$rs = $this->executeSQL( $sql );
		
		$data = array();
		while($row = pg_fetch_row($rs))
		{
			$name           = $row[0];
			$type_native    = $row[1];

			$type       = preg_replace('@(\(\d+\)|\d+)@','',$row[1]);
			$length     = $row[2] == '' ? null : $row[2];

			$notnull        = $row[5] == 't' ? true : false;
			$primary        = $row[3] == 't' ? true : false;
			$default        = preg_match('@^nextval@i', $row[4]) ? null : $row[4];
			$autoincrement  = preg_match('@^nextval@i', $row[4]) ? true : false;
			
			// removemos o cast se tiver
			if(preg_match('@(.+?)::.+?$@', $default, $reg)){
				$default = str_replace("'", '', $reg[1]);
			}
			
			$options = array();
			
			$row[4] = str_replace('"',"'",$row[4]);
			
			if( preg_match("@^nextval\('(\w+)'@i", $row[4], $reg) ) {
				$options['sequence'] = $reg[1];
				
			} else if(preg_match("@'(\w+)_seq'@i", $row[4], $reg)){
				$options['sequence'] = $reg[1].'_seq';
				
			}
			
			$data[] = array($name, $type_native, $type, $length, $primary, $notnull, $default, $autoincrement, $options);
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
		$rs = @pg_query($this->conn_id, $sql);
		
		if( ! $rs )
		{
			$msg = $this->getErrorMsg();
			$this->dispatchEvent(new Lumine_ConnectionEvent(Lumine_Event::EXECUTE_ERROR, $this, $msg, $sql));
			throw new Lumine_Exception("Falha na consulta: " . $msg."<br>" . $sql, Lumine_Exception::QUERY_ERROR);
		}
		$this->last_rs = $rs;
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
			return sprintf("LIMIT %d OFFSET %d", $limit, $offset);
		}
	}
	/**
	 * @see ILumine_Connection::escape()
	 */
	public function escape($str) 
	{
		$var = pg_escape_string($str);
		return $var;
	}
	/**
	 * @see ILumine_Connection::escapeBlob()
	 */
	public function escapeBlob($blob)
	{
		return pg_escape_bytea($blob);
	}
	/**
	 * @see ILumine_Connection::affected_rows()
	 */
	public function affected_rows()
	{
		if(self::$state == self::OPEN && $this->last_rs)
		{
			return pg_affected_rows($this->last_rs);
		}
		throw new Lumine_Exception('conexao nao est� aberta', Lumine_Exception::ERRO);
	}
	/**
	 * @see ILumine_Connection::num_rows()
	 */
	public function num_rows($rs)
	{
		return pg_num_rows($rs);
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
        self::$state = null;
        unset($this->transactions);
        unset($this->transactions_count);
        //unset(self::$instance);
        
        parent::__destruct();
    }
}


?>