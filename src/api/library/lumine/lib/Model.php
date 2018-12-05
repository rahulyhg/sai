<?php
/**
 * **********************************************************************
 * Classe base para Model, em arquiteturas MVC
 * 
 * Todas as models da aplicacao devem extender esta classe base,
 * para que a integracao com Lumine seja feita.
 * 
 * No construtor da classe filha o objeto que a model representa
 * sera instanciado para utilizacao. 
 * 
 * @author Hugo Ferreira da Silva
 * @link http://www.hufersil.com.br
 * @package Lumine
 * 
 * **********************************************************************
 */ 

/**
 * Classe abstrata para servir como base para Models
 * 
 * @author Hugo Silva
 * @link http://www.hufersil.com.br
 * @package Lumine
 */
abstract class Lumine_Model extends Lumine_EventListener {
	
	/**
	 * Objeto que sera usado nos metodos padroes 
	 * @var Lumine_Base
	 */
	protected $obj;
	
	/**
	 * Numero de linhas encontradas
	 * @var int
	 */
	protected $rows = 0;
	
	/**
	 * Variaveis geradas dinamicamente
	 * @var array
	 */
	protected $vars = array();
	
	/**
	 * Tipos de eventos disparados
	 * @var array
	 */
	protected $_event_types  = array(
		Lumine_Event::PRE_INSERT,
		Lumine_Event::POS_INSERT,
		Lumine_Event::PRE_SAVE,
		Lumine_Event::POS_SAVE,
		Lumine_Event::PRE_GET,
		Lumine_Event::POS_GET,
		Lumine_Event::PRE_UPDATE,
		Lumine_Event::POS_UPDATE,
		Lumine_Event::PRE_DELETE,
		Lumine_Event::POS_DELETE,
		Lumine_Event::PRE_FIND,
		Lumine_Event::POS_FIND,
	);
	
	/**
	 * Inicia as configuracoes
	 * 
	 * <p>Sera sobrescrito nas classes filhas.<br>
	 * Utilizado primariamente para iniciar o objeto<br>
	 * de persistencia.</p>
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @return void
	 */
	public function __construct(){
		
		$this->obj->addEventListener(Lumine_Event::PRE_INSERT, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::POS_INSERT, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::PRE_GET, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::POS_GET, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::PRE_FIND, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::POS_FIND, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::PRE_SAVE, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::POS_SAVE, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::PRE_DELETE, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::POS_DELETE, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::PRE_UPDATE, array($this,'redispatchSQLEvent'));
		$this->obj->addEventListener(Lumine_Event::POS_UPDATE, array($this,'redispatchSQLEvent'));
		
		$this->_initialize();
		
	}

	/**
	 * Recupera um objeto pela chave primaria ou chave => valor
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param mixed $pk Nome da chave ou valor
	 * @param mixed $pkValue Valor do campo
	 * @return array Dados encontrados em formato de array associativo
	 */
	public function get($pk, $pkValue=null){
		$this->obj->reset();
		$this->obj->get($pk, $pkValue);
		
		return $this->obj->toArray();
	}
	
	/**
	 * Recupera uma lista de itens
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param array $filters Filtros a serem usados
	 * @param string $order Ordenacao dos resultados
	 * @param int $offset Inicio dos resultados
	 * @param int $limit Limite de itens
	 * @param array $prefs Preferencias para busca
	 * @return array Lista de itens encontrados
	 */
	public function find(array $filters = array(), $order = '', $offset = null, $limit = null, array $prefs = array()){
		$this->obj->reset();
		$this->obj->alias('o');
		$this->obj->selectAs();
		// se indicou uma lista de join's
		if( isset($prefs['join']) && is_array($prefs['join']) ){
			// para cada item
			foreach($prefs['join'] as $item){
				// se informou o nome da classe
				if(isset($item['class'])){
					// importamos a classe
					$this->obj->_getConfiguration()->import($item['class']);
					// reflexao
					$ref = new ReflectionClass($item['class']);
					$target = $ref->newInstance();
					
					// se indicou um alias
					if(isset($item['alias'])){
						$target->alias($item['alias']);
					}
					
					// tipo de uniao
					$joinType = isset($item['type']) ? $item['type'] : 'INNER';
					
					// se indicou os campos de uniao
					if(isset($item['fieldFrom']) && isset($item['fieldTo'])){
						// se indicou um extra
						if( isset($item['extra']) ){
							// unimos as classes
							$this->obj->join($target, $joinType, $target->_getAlias(), $item['fieldFrom'], $item['fieldTo'], $item['extra'], isset($item['extraArgs']) ? $item['extraArgs'] : '');
						// se nao indicou extra
						} else {
							// une as classes sem extra
							$this->obj->join($target, $joinType, $target->_getAlias(), $item['fieldFrom'], $item['fieldTo']);
						}
					// se nao indicou os campos mas indicou extra
					} else if(isset($item['extra'])) {
						// une as classes sem indicar os campos, mas indica os argumentos extras
						$this->obj->join($target, $joinType, $target->_getAlias(), null, null, $item['extra'], isset($item['extraArgs']) ? $item['extraArgs'] : '');
					// une as classes
					} else {
						$this->obj->join($target, $joinType, $target->_getAlias());
					}
					
					// se indicou alias
					if( isset($item['alias']) ){
						// muda o selectAs
						$this->obj->selectAs($target, $item['alias'].'%s');
					}
				}
			}
		}
		
		$this->setFilters($filters);
		
		// conta os registros
		$this->rows = $this->obj->count( isset($prefs['countString']) ? $prefs['countString'] : '*' );
		
		// se informou o having
		if(isset($prefs['having'])){
			$this->obj->having($prefs['having']);
		}
		
		// se informou group by
		if(isset($prefs['group'])){
			$this->obj->group($prefs['group']);
		}
			
		// se informou uma ordem
		if(!empty($order)){
			$this->obj->order($order);
		}
		
		// limita e executa a consulta
		$this->obj->limit($offset, $limit)
			->find();
		
		return $this->obj->allToArray();
	}
	
	/**
	 * 
	 * @link http://www.hufersil.com.br
	 * @author Hugo Ferreira da Silva
	 * @param array $filters Filtros que serao aplicados
	 * @return void
	 */
	protected function setFilters(array $filters){
		foreach($filters as $key => $value){
			// iremos ignorar valores vazios
			if( $value === '' ){
				continue;
			}
			
			try {
				$target = $this->obj;
				$alias = $this->obj->_getAlias();
				
				// se indicou o alias
				if( preg_match('@^(\w+)\.(\w+)$@', $key, $reg) ){
					$list = $this->obj->_getObjectPart('_join_list');
					// para cada item de classes unidas
					foreach($list as $class){
						// se encontrar o alias
						if($class->_getAlias() == $reg[1]){
							$target = $class;
							$alias = $class->_getAlias();
							$key = $reg[2];
							break;
						}
					}
				} 
				
				
				$field = $target->_getField($key);
				
				// se o valor for nulo
				if( is_null($value) ){
					// colocamos um IS NULL como condicao
					$this->obj->where($alias.'.'.$key .' IS NULL');
					continue;
				}
				
				switch($field['type']){
					case 'char':
					case 'varchar':
					case 'text':
					case 'enum':
					case 'blob':
					case 'longblob':
					case 'tinyblob':
						$this->obj->where($alias.'.'.$key.' like ?', $value);
					break;
					
					// se nao for texto, nao fazemos por like
					// fazemos uma comparacao direta
					default:
						$this->obj->where($alias.'.'.$key.' = ?', $value);
				}
			
			} catch(Exception $e) {
				// quando o campo que a pessoa tentou pegar nao existe
				// eh disparada uma excecao, mas neste caso nao eh um erro
				// por isso capturamos a excecao para que nao de problemas para o usuario
			}
		}
	}
	
	
	/**
	 * Insere os dados no banco
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param array $data Dados a serem persistidos
	 * @return int Codigo do registro inserido
	 */
	public function insert(array $data){
		$this->obj->reset();
		$this->obj->setFrom($data);
		$this->obj->insert();
		
		// pegamos a(s) chave(s) primaria(s)
		// so retornamos quando tem uma unica chave primaria
		$pk = $this->obj->_getPrimaryKeys();
		
		if(count($pk) == 1){
			$key = $pk[0]['name'];
			
			return $this->obj->$key;
		}
		
		return 0;
		
	}
	
	/**
	 * Salva os dados do registro
	 * 
	 * <p>Insere ou atualiza. <br>
	 * Caso exista a chave primaria, atualiza.<br>
	 * Se nao tiver, insere</p>
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param array $data Dados a serem persistidos
	 * @return int Codigo do registro salvo
	 */
	public function save(array $data){
		$this->obj->reset();
		$this->obj->setFrom($data);
		$this->obj->save();
		
		// pegamos a(s) chave(s) primaria(s)
		// so retornamos quando tem uma unica chave primaria
		$pk = $this->obj->_getPrimaryKeys();
		
		if(count($pk) == 1){
			$key = $pk[0]['name'];
			
			return $this->obj->$key;
		}
		
		return 0;
	}
	
	/**
	 * Remove registros pelo id
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param int $id Codigo do registro a ser removido
	 * @return void
	 */
	public function delete($id){
		$this->obj->reset();
		
		// pegamos a(s) chave(s) primaria(s)
		// so removemos quando tem uma unica chave primaria
		$pk = $this->obj->_getPrimaryKeys();
		
		if(count($pk) == 1){
			$key = $pk[0]['name'];
			
			$this->obj->$key = $id;
			$this->obj->delete();
		}
	}
	
	/**
	 * Atualiza registros baseados pelo id
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param int $id Codigo do registro a ser atualizado
	 * @param array $data Dados a serem atualizados
	 * @return void
	 */
	public function update($id, array $data){
		$this->obj->reset();
		$this->obj->setFrom($data);
		
		// pegamos a(s) chave(s) primaria(s)
		// so removemos quando tem uma unica chave primaria
		$pk = $this->obj->_getPrimaryKeys();
		
		if(count($pk) == 1){
			$key = $pk[0]['name'];
			
			$this->obj->$key = $id;
			$this->obj->update();
		}
	}
	
	/**
	 * Efetua um delete baseado em clausulas where
	 * 
	 * <p>Qualquer parametro depois de $clause sera usado como prepared statement
	 * para remocao dos dados.
	 * 
	 * Caso for usar prepared statement, colocar o alias do objeto como a letra "o" 
	 * </p>
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param string $clause condicao para remocao
	 * @return void
	 */
	public function deleteWhere($clause){
		$this->obj->reset();
		$this->obj->alias('o');
		$args = func_get_args();
		
		// se a pessoa passou parametros a mais do que a clausula
		if($args > 1){
			// entao eh prepared statement, chamamos o where com os argumentos
			call_user_func_array(array($this->obj,'where'), $args);
			
		} else {
			// NAO eh prepared statement, chamamos o where 
			$this->obj->where($clause);
		}
		
		$this->obj->delete(true);
	}
	
	/**
	 * Efetua um update baseado em clausulas where
	 * 
	 * <p>Qualquer parametro depois de $clause sera usado como prepared statement
	 * para atualizacao dos dados.
	 * Caso for usar prepared statement, colocar o alias do objeto como a letra "o" 
	 * </p>
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param array $data Dados a serem atualizados
	 * @param string $clause condicao para atualizacao
	 * @return void
	 */
	public function updateWhere(array $data, $clause){
		$this->obj->reset();
		$this->obj->setFrom($data);
		$this->obj->alias('o');
		
		$args = func_get_args();
		array_shift($args);
		
		// se a pessoa passou parametros a mais do que a clausula
		if($args > 1){
			// entao eh prepared statement, chamamos o where com os argumentos
			call_user_func_array(array($this->obj,'where'), $args);
			
		} else {
			// NAO eh prepared statement, chamamos o where 
			$this->obj->where($clause);
		}
		
		$this->obj->update(true);
	}
	
	/**
	 * Adiciona uma validacao ao objeto
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param string $type Tipo de validacao
	 * @param string $field Nome do campo que tera a validacao
	 * @param mixed $msg Mensagem de erro quando houver problema, nome da funcao ou array com o objeto e metodo
	 * @param int $min Minimo de caracteres ou valor minimo
	 * @param int $max Maximo de caracteres ou valor maximo
	 * @return void
	 */
	public function addValidation($type, $field, $msg, $min=null, $max=null){
		Lumine_Validator_PHPValidator::addValidation($this->obj,$field,$type,$msg,$min,$max);
	}
	
	/**
	 * Valida se as entradas nos campos estao de acordo com as regras de validacao
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param array $data Dados a serem validados
	 * @return array Lista contendo os erros encontrados
	 */
	public function validate(array $data){
		$this->obj->reset();
		$this->obj->setFrom($data);
		return $this->obj->validate();
	}
	
	/**
	 * Numero de linhas encontradas na ultima consulta
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @return int
	 */
	public function rows(){
		return $this->rows;
	}
	
	/**
	 * Set implicito
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public function __set($key, $val){
		$this->vars[$key] = $val;
	}
	
	/**
	 * Get implicito
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param $key
	 * @return mixed
	 */
	public function __get($key){
		if(!isset($this->vars[$key])){
			return null;
		}
		
		return $this->vars[$key];
	}
	
	/**
	 * Redispara eventos
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @param Lumine_SQLEvent $e
	 * @return void
	 */
	public function redispatchSQLEvent(Lumine_SQLEvent $e){
		$this->dispatchEvent($e);
	}
	
	/**
	 * Inicializador
	 * 
	 * <p>Metodo utilitario chamado no construtor.
	 * Se o usuario precisar de algo que seja feito na construcao do objeto,
	 * basta sobrecarregar este metodo na classe nova, assim nao perde
	 * nada com a engenharia reversa</p>
	 * 
	 * @author Hugo Ferreira da Silva
	 * @link http://www.hufersil.com.br
	 * @return void
	 */
	protected function _initialize(){
		
	}
}


