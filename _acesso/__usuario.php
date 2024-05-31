<?

if (pode("1", $_COOKIE["perfil"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$st='Editar';
		$result= mysqli_query($conexao1, "select *, usuarios.id_usuario as id_usuario from  usuarios, pessoas
								where usuarios.id_usuario = '". $_GET["id_usuario"] ."'
								and   pessoas.id_pessoa = usuarios.id_pessoa
								") or die(mysqli_error());
		$rs= mysqli_fetch_object($result);
	}
	else $st='Novo';
?>
	<div class="page-header">
		<h1>Usuário <small><?=$st;?> usuário</small></h1>
	</div>
	
	<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formUsuario&amp;acao=<?= $acao; ?>" method="post" name="form">
	    
	    <? if ($acao=='e') { ?>
	    <input name="id_usuario" class="escondido" type="hidden" id="id_usuario" value="<?= $rs->id_usuario; ?>" />
	    <input name="id_pessoa" class="escondido" type="hidden" id="id_pessoa" value="<?= $rs->id_pessoa; ?>" />
	    <? } ?>
	    
        <div class="row-fluid">
        	<div class="span4">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= $rs->nome; ?>" placeholder="Nome" required="required" />
				
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" value="<?= $rs->email; ?>" placeholder="E-mail" required="required" />
				
				<label for="usuario">Login:</label>
                <input type="text" name="usuario" id="usuario" value="<?= $rs->usuario; ?>" placeholder="" />
                
				<label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" value="<?= $rs->cpf; ?>" placeholder="000.000.000/00" />
                
        	</div>
        	<div class="span4">
				<label for="registro">Registro/UF:</label>
                <input type="text" name="registro" id="registro" value="<?= $rs->registro; ?>" placeholder="CRM 0000/UF" />
				
				<label for="id_especialidade">Especialidade:</label>
				<select id="id_especialidade" name="id_especialidade">
					<option value="">- selecione -</option>
					<?
					$result_esp= mysqli_query($conexao1, "select * from  especialidades
											order by especialidade asc
											") or die(mysqli_error());
					while ($rs_esp= mysqli_fetch_object($result_esp)) {
					?>
					<option <? if ($rs->id_especialidade==$rs_esp->id_especialidade) echo 'selected="selected"'; ?> value="<?=$rs_esp->id_especialidade;?>"><?=$rs_esp->especialidade;?></option>
					<? } ?>
				</select>
				
				<label for="sexo">Sexo:</label>
				<select id="sexo" name="sexo">
					<option value="">- selecione -</option>
					<option value="m" <? if ($rs->sexo=='m') echo 'selected="selected"'; ?>>Masculino</option>
					<option value="f" <? if ($rs->sexo=='f') echo 'selected="selected"'; ?>>Feminino</option>
				</select>
				
				<label for="data_nasc">Data de nascimento:</label>
                <input type="text" name="data_nasc" id="data_nasc" value="<?= desformata_data($rs->data_nasc); ?>" placeholder="00/00/0000" maxlenght="10" />
        	</div>
			
			<div class="span4">
				
				<label for="perfil">Perfil:</label>
				<select id="perfil" name="perfil" required="required">
					<option value="">- selecione -</option>
					<?
					$result_per= mysqli_query($conexao1, "select * from  cad_perfis
											order by id_perfil asc
											") or die(mysqli_error());
					while ($rs_per= mysqli_fetch_object($result_per)) {
					?>
					<option class="tt" title="<?=$rs_per->descricao;?>" <? if ($rs->perfil==$rs_per->id_perfil) echo 'selected="selected"'; ?> value="<?=$rs_per->id_perfil;?>"><?=$rs_per->perfil;?></option>
					<? } ?>
				</select>
            	<br />
				
            	<label for="senha">Senha:</label>
            	<input type="password" name="senha" id="senha" placeholder="Senha" <? if ($acao=='i') { ?> required="required" <? } ?> />
				
            	<label for="senha2">Confirmação:</label>
            	<input type="password" name="senha2" id="senha2" placeholder="Confirmação de senha" <? if ($acao=='i') { ?> required="required" <? } ?> />
            	
            	<label for="foto">Foto:</label>
            	<input type="file" name="foto" id="foto" title="Escolher foto" />
            	<br /><br />
            	
            	<? if ($rs->foto!="") { ?>
	            
	            <div id="foto_area">
            		<img class="img-rounded" src="includes/timthumb/timthumb.php?src=<?= $rs->foto; ?>&amp;w=120&amp;h=120&amp;zc=1&amp;q=95" border="0" alt="" />
	            </div>
	            
            	<br />
	            <a id="foto_usuario_excluir" class="btn btn-mini btn-danger" href="javascript:apagaArquivo('<?=$rs->id_pessoa;?>', '<?= $rs->foto; ?>');">Apagar foto atual</a> <br /><br />
	            
	            <? } ?>
            	
			</div>
        </div>   
		<br />
		
	    <div class="form-actions">
	    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
			<a type="button" class="btn cancelar" href="./?pagina=acesso/usuarios">Cancelar</a>
	    </div>
		
	</form>

<? } ?>
