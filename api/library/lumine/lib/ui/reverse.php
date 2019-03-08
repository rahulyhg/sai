<?php

date_default_timezone_set('America/Sao_Paulo');

ini_set('display_errors','On');
ini_set('error_reporting',E_ALL);
require_once dirname(dirname(dirname(__FILE__))) . '/Lumine.php';
require_once LUMINE_INCLUDE_PATH . '/lib/Templates/Configurations.php';

function post(){
	$list = func_get_args();
	$atual = $_POST;
	$valor = '';
	
	foreach($list as $key){
		if(isset($atual[$key])){
			$atual = $atual[$key];
			$valor = $atual;
		} else {
			$valor = '';
		}
	}
	
	echo $valor;
}

function checked(){
	ob_start();
	$args = func_get_args();
	$compare = array_shift($args);
	call_user_func_array('post', $args);
	
	$val = ob_get_clean();
	
	if($val == $compare){
		echo ' checked="checked"';
	}
}

function selected(){
	ob_start();
	$args = func_get_args();
	$compare = array_shift($args);
	call_user_func_array('post', $args);
	
	$val = ob_get_clean();
	
	if($val == $compare){
		echo ' selected="selected"';
	}
}

// valores padrao
if($_SERVER['REQUEST_METHOD'] == 'GET'){
	$_POST = array(
		'package' => 'entidades',
		'host' => 'localhost',
		'port' => 3306,
		'user' => 'root',
		'class_path' => str_replace('\\', '/', dirname(LUMINE_INCLUDE_PATH)),
		
		'options' => array(
			'create_paths' => 1,
			'camel_case' => 1,
			'usar_dicionario' => 1,
			'many_to_many_style' => '%s_%s',
			'create_entities_for_many_to_many' => 0,
			'tipo_geracao' => 1,
			'keep_foreign_column_name' => 1,
			'remove_count_chars_start' => 0,
			'remove_count_chars_end' => 0,
			'dto_package' => 'entidades',
			'dto_format' => '%sDTO',
			'create_controls' => 'White',
			'create_models' => 0,
			'model_path' => '',
			'model_format' => '%sModel',
			'model_context' => 0,
			'model_context_path' => '',
			'format_classname' => '%s'
		)
	);
}

// se enviou o arquivo de configuracao
if( isset($_FILES['arquivo']['tmp_name']) && is_uploaded_file($_FILES['arquivo']['tmp_name'])) {
	// inclui o arquivo
	include($_FILES['arquivo']['tmp_name']);
	// pega a variavel lumineConfig, se existir
	if(isset($lumineConfig)){
		$_POST = $lumineConfig;
	}
	unset($_POST['acao']);
}

if(!empty($_POST['acao'])){
	switch($_POST['acao']){
		case 'tabelas':
				$res = false;
				$message = '';
				$list = array();

				try  {
					$dbh = new Lumine_Configuration( $_POST );
					$conn = $dbh->getConnection();
					$res = $conn->connect();
					$list = $conn->getTables();
					$conn->close();
					
					if(!empty($list)){
						echo '<input type="checkbox" onclick="checarTodas(this)" />Selecionar todas as tabelas<br /><br />';
						foreach($list as $table){
							printf('<input type="checkbox" class="table" name="tables[]" value="%1$s" /> %1$s <br />', $table);
						}
						
						echo '<br />';
						echo '<input type="button" id="btnConcluir" value="Gerar Classes" onclick="concluir()" />';
					} else {
						echo 'Nenhuma tabela encontrada';
					}
					
				} catch(Exception $e) {
					$message = $e->getMessage();
					$res = false;
					
					echo $message;
				}
				
				exit;
		break;
		
		case 'gerar':
			try {
				Lumine::load('Reverse');
				$table_list = !empty($_POST['tables']) ? $_POST['tables'] : array();
				
				unset($_POST['tables']);
				
				$_POST['options']['overwrite'] = 0;
				$_POST['options']['create_dtos'] = !empty($_POST['options']['create_dtos']);
				$_POST['options']['create_paths'] = !empty($_POST['options']['create_paths']);
				$_POST['options']['generateAccessors'] = !empty($_POST['options']['generateAccessors']);
				$_POST['options']['create_entities_for_many_to_many'] = !empty($_POST['options']['create_entities_for_many_to_many']);
				
				// ajusta algumas configuracoes
				if($_POST['options']['tipo_geracao'] == 1)	{
					$_POST['options']['generate_files'] = 1;
					$_POST['options']['generate_zip'] = 0;
					
				} else if($_POST['options']['tipo_geracao'] == 2) { 
					$_POST['options']['generate_files'] = 0;
					$_POST['options']['generate_zip'] = 1;
				}
				
				Lumine_Log::setLevel(Lumine_Log::ERROR);
				$cfg = new Lumine_Reverse($_POST);
				$cfg->setTables($table_list);
				$cfg->start();
				
				echo '<span style="color: #006600; font-weight: bold;">Engenharia reversa terminada!</span>';
				
			} catch (Exception $e) {
				echo "Falha na engenharia reversa: " . $e->getMessage();
			}
			exit;
		break;
		
		case 'loadTemplate':
			if(isset($_POST['options']['configID']) && $_POST['options']['configID'] != ''){
				$id = $_POST['options']['configID'];
				$_POST = $configTemplates[$id]['template'];
				$_POST['options']['configID'] = $id;
			}
		break;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Lumine - Engenharia Reversa</title>
<link href="estilos.css" rel="stylesheet" type="text/css" />
<link href="css/cupertino/jquery.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#tabs').tabs();
	
	$('.btnRecuperarTabelas').click(function(){
		var data = $('#formReverse').serialize() + '&acao=tabelas';
		
		$('#tabs').tabs('select','tabelas');
		
		$('#lista_tabelas').html('Aguarde...');
		
		$.post('<?php echo $_SERVER['PHP_SELF']; ?>', data, function(json){
			$('#lista_tabelas').html(json);
		});
	});
	
	$('#btnImportar').click(function(){
		$('#formReverse').submit();
	});
	
	$('#configID').change(function(){
		$('#acao').val('loadTemplate');
		$('#formReverse').submit();
	});
	
});

function checarTodas(ref){
	$('.table').attr('checked', ref.checked);
}

function concluir(){
	$('#tabs').tabs('select','logs');
	$('#log_geracao').html('<iframe src="" style="width:100%; height: 300px;" id="logFrame" name="logFrame" />');
	
	$('#acao').val('gerar');
	$('#formReverse').attr('target','logFrame')
		.submit();
		
	$('#formReverse').attr('target','_self');
	$('#acao').val('');
	
}
</script>
</head>

<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="form1" id="formReverse">
  <p align="center"><img src="imagens/lumine.gif" alt="Lumine" width="156" height="45" /></p>

<div id="tabs">
  <ul>
  <li><a href="#principal">Dados principais</a></li>
  <li><a href="#personalizacao">Personaliza&ccedil;&atilde;o</a></li>
  <li><a href="#integracao">Integra&ccedil;&atilde;o</a></li>
  <li><a href="#tabelas">Tabelas</a></li>
  <li><a href="#logs">Log de gera&ccedil;&atilde;o</a></li>
  <li><a href="#importar">Importar Configura&ccedil;&atilde;o</a></li>
  </ul>
  
  <div id="principal">
  	<p>
      <label for="configID">Modelo de configura&ccedil;&atilde;o</label>
      <select name="options[configID]" id="configID">
      <option value=""></option>
        <?php
		foreach($configTemplates as $idx => $item){
			echo '<option value="'.$idx.'"';
			selected($idx,'options','configID');
			echo '>'.$item['name'] . '</option>';

		}
        ?>
      </select>
      <p>
      <label for="dialect">Dialeto</label>
      <select name="dialect" id="dialect">
        <?php
            $dh = opendir(LUMINE_INCLUDE_PATH.'/lib/Connection');
            $nopes = array('.','..','IConnection.php','.cvs','.svn');
            
            while($file = readdir($dh))
            {
                if(in_array($file , $nopes))
                {
                    continue;
                }
                
                $name = str_replace('.php','',$file);
                
                echo '<option value="'.$name.'"';
                if(@$_POST['dialect'] == $name)
                {
                    echo ' selected="selected"';
                }
                echo '>'.$name.'</option>'.PHP_EOL;
            }
            
            ?>
      </select>
      </p>
      <p>
        <label for="database">Nome do banco de dados</label>
        <input type="text" name="database" id="database" value="<?php post('database'); ?>" />
      </p>
      <p>
        <label for="user">Usu&aacute;rio do banco de dados</label>
        <input type="text" name="user" id="user" value="<?php post('user'); ?>" />
      </p>
      <p>
        <label for="password">Senha do banco de dados</label>
        <input type="password" name="password" id="password" value="<?php post('password'); ?>" />
      </p>
      <p>
        <label for="port">Porta do banco de dados</label>
        <input type="text" name="port" id="port" value="<?php post('port'); ?>" style="width: 60px;" />
      </p>
      <p>
        <label for="host">Host do banco de dados</label>
        <input type="text" name="host" id="host" value="<?php post('host'); ?>" />
      </p>
      <p>
        <label for="class_path">Class path (diret&oacute;rio raiz da aplica&ccedil;&atilde;o)</label>
        <input type="text" name="class_path" id="class_path" value="<?php post('class_path'); ?>" />
      </p>
      <p>
        <label for="package">Pacote das classes</label>
        <input type="text" name="package" id="package" value="<?php post('package'); ?>" />
      </p>
      <p>
        <label for="tipo_geracao">Tipo de gera&ccedil;&atilde;o dos arquivos</label>
        <select name="options[tipo_geracao]" id="tipo_geracao">
          <option value="1">Gerar arquivos direto na class-path</option>
          <option value="2"<?php selected(2, 'options','tipo_geracao'); ?>>Gerar arquivos em um ZIP na class-path</option>
        </select>
      </p>
      <p align="center"><input name="btn" type="button" class="btnRecuperarTabelas" value="Recuperar Tabelas" /></p>
  </div>
  
  <div id="personalizacao">
	<p>
     <label for="remove_prefix">Remover prefixo das tabelas</label>
     <input id="remove_prefix" name="options[remove_prefix]" type="text" value="<?php post('options', 'remove_prefix'); ?>" />
    </p>
    <p>
     <label for="remove_count_chars_start">Remover </label>
     <input style="width: 60px;" id="remove_count_chars_start" name="options[remove_count_chars_start]" type="text" value="<?php post('options', 'remove_count_chars_start'); ?>" />
     caracteres do inicio das tabelas
    </p>
    <p>
     <label for="remove_count_chars_end">Remover </label>
     <input style="width: 60px;" id="remove_count_chars_end" name="options[remove_count_chars_end]" type="text" value="<?php post('options', 'remove_count_chars_end'); ?>" />
     caracteres do final das tabelas
    </p>
    <p>
     <label for="format_classname">Formatar nome de classe </label>
     <input style="width: 60px;" id="format_classname" name="options[format_classname]" type="text" value="<?php post('options', 'format_classname'); ?>" />
     (utiliza sprintf para formatar o nome da classe)
    </p>
    <p>
     <label for="schema_name">Schema</label>
     <input id="schema_name" name="options[schema_name]" type="text" value="<?php post('options', 'schema_name'); ?>" />
    </p>
    <p>
     <label for="many_to_many_style">Formato para auto-identificar tabelas M-N:</label>
     <input id="many_to_many_style" name="options[many_to_many_style]" type="text" value="<?php post('options', 'many_to_many_style'); ?>" />
    </p>
    <p>
     <label for="plural">String para converter em plural relac. M-N e 1-N:</label>
     <input id="plural" name="options[plural]" type="text" value="<?php post('options', 'plural'); ?>" />
    </p>
    <p><label for="create_controls">Usar controles usando</label> 
      <select name="options[create_controls]" id="create_controls">
        <option value="">N&atilde;o gerar controles</option>
        <?php
				$dir = LUMINE_INCLUDE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR;
				$files = glob($dir . '*.php');
				
				foreach($files as $file)
				{
					preg_match('@([\w,\.]+)\.php$@', $file, $reg);
					if($reg[1] != 'IForm')
					{
						printf('<option value="%s"%s>%s</option>',
							$reg[1],
							!empty($_POST['options']['create_controls']) && $reg[1] == $_POST['options']['create_controls'] ? ' selected="selected"' : '',
							$reg[1]);
					}
				}
				
				?>
      </select>
    </p>
    <p>
      <label for="class_sufix">Utilizar sufixo na cria&ccedil;&atilde;o dos arquivos:</label>
      <select name="options[class_sufix]" id="class_sufix">
        <option value="">Nenhum</option>
        <option value="class"<?php selected('class','options','class_sufix'); ?>>class</option>
        <option value="inc"<?php selected('inc','options','class_sufix'); ?>>inc</option>
      </select>
    </p>
    <p>
      <label for="generateAccessors">Gerar getters/setters</label>
      <input id="generateAccessors" name="options[generateAccessors]" type="checkbox" value="1" <?php checked(1, 'options','generateAccessors'); ?> />
    </p>
    <p>
      <label for="create_entities_for_many_to_many">Gerar entidades para tabelas M-N</label>
      <input id="create_entities_for_many_to_many" name="options[create_entities_for_many_to_many]" type="checkbox" value="1" <?php checked(1, 'options','create_entities_for_many_to_many'); ?> />
    </p>
    <p>
      <label for="keep_foreign_column_name">Manter nomes das colunas nas chaves estrangeiras?</label>
      <input id="keep_foreign_column_name" name="options[keep_foreign_column_name]" type="checkbox" value="1" <?php checked(1, 'options','keep_foreign_column_name'); ?> />
    </p>
    <p>
     <label for="camel_case">Usar CamelCase?</label>
     <input id="camel_case" name="options[camel_case]" type="checkbox" value="1" <?php checked(1, 'options','camel_case'); ?> />
    </p>
    <p>
      <label for="usar_dicionario">Dicion&aacute;rio de plural?</label>
      <input id="usar_dicionario" name="options[usar_dicionario]" type="checkbox" value="1" <?php checked(1, 'options','usar_dicionario'); ?> />
    </p>
    <p>
      <label for="create_paths">Deseja criar os diret&oacute;rios?</label>
      <input id="create_paths" name="options[create_paths]" type="checkbox" value="1" <?php checked(1, 'options', 'create_paths'); ?> />
    </p>
  </div>
  
  <div id="integracao">
    <p>
      <label for="create_dtos">Criar DTO's?</label>
      <input id="create_dtos" name="options[create_dtos]" type="checkbox" value="1" <?php checked(1, 'options','create_dtos'); ?> />
    </p>
    <p>
      <label for="auto_cast_dto">Auto-Cast para o DTO quando usar toObject/allToObject?</label>
      <input id="auto_cast_dto" name="options[auto_cast_dto]" type="checkbox" value="1" <?php checked(1, 'options','auto_cast_dto'); ?> />
    </p>
    <p>
      <label for="dto_format">Formato do nome do DTO</label>
      <input name="options[dto_format]" type="text" id="dto_format" value="<?php post('options','dto_format'); ?>" />
    </p>
    <p>
      <label for="dto_package">Nome do pacote para DTO's</label>
      <input name="options[dto_package]" type="text" id="dto_package" value="<?php post('options','dto_package'); ?>" />
    </p>
    <hr />
    <p><strong>Cria&ccedil;&atilde;o de models</strong></p>
    <p>
      <label for="create_models">Criar models?</label>
      <input id="create_models" name="options[create_models]" type="checkbox" value="1" <?php checked(1, 'options','create_models'); ?> />
    </p>
    <p>
      <label for="model_path">Pasta para as models</label>
      <input name="options[model_path]" type="text" id="model_path" value="<?php post('options','model_path'); ?>" />
    </p>
    <p>
      <label for="model_format">Formato de nomes para models</label>
      <input name="options[model_format]" type="text" id="model_format" size="10" value="<?php post('options','model_format'); ?>" />
    </p>
    <p>
      <label for="model_context">Criar arquivo de contexto para MVC?</label>
      <input id="model_context" name="options[model_context]" type="checkbox" value="1" <?php checked(1, 'options','model_context'); ?> />
    </p>
    <p>
      <label for="model_context_path">Pasta grava&ccedil;&atilde;o do arquivo de contexto</label>
      <input name="options[model_context_path]" type="text" id="model_context_path" size="10" value="<?php post('options','model_context_path'); ?>" />
    </p>
  </div>
  
  <div id="tabelas">
     <div id="lista_tabelas" style="padding-left: 200px;">Ap&oacute;s preencher os dados, clique em "Recuperar tabelas"</div>
     <p>&nbsp;</p>
     <p align="center"><input name="btn" type="button" class="btnRecuperarTabelas" value="Atualizar Tabelas" /></p>
  </div>
  
  <div id="importar">
     <p align="center" style="padding: 30px;">Utilize esta tela para importar as configura&ccedil;&otilde;es do seu arquivo lumine-conf.php, assim voc&ecirc; n&atilde;o precisar&aacute; indicar as informa&ccedil;&otilde;es j&aacute; definidas na primeira engenharia reversa.</p>
     <p align="center">
       <input name="arquivo" type="file" size="40" />
       <input name="btnImportar" type="button" value="Importar" id="btnImportar" />
     </p>
  </div>
  
  <div id="logs">
     <div id="log_geracao">
        Nesta aba voc&ecirc; poder&aacute; acompanhar o log de gera&ccedil;&atilde;o de suas classes.
     </div>
     
  </div>
</div>
<input name="acao" type="hidden" value="" id="acao" />
</form>
</body>
</html>
