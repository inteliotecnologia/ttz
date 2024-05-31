<?
if (pode("1234", $_COOKIE["perfil"])) {
	
	$result= mysqli_query($conexao1, "select * from  pessoas, usuarios
							where usuarios.id_usuario = '". $_COOKIE["id_usuario"] ."'
							and   pessoas.id_pessoa = '". $_COOKIE["id_pessoa"] ."'
							and   pessoas.id_pessoa = usuarios.id_pessoa
							") or die(mysqli_error());
	$rs= mysqli_fetch_object($result);	
?>
			
	<? include('dados_menu.php'); ?>
	
	<script>
		mixpanel.track("Acessou dados");
	</script>
	
	<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formDadosPessoais" method="post" name="form">
		    
	    <input name="id_pessoa" class="escondido" type="hidden" id="id_pessoa" value="<?= $rs->id_pessoa; ?>" />
	    
        <div class="row-fluid">
        	<div class="span4">
                <label for="nome" class="menor muted">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= $rs->nome; ?>" placeholder="Nome" required="required" />
				
                <label for="email" class="menor muted">E-mail:</label>
                <input type="email" name="email" id="email" value="<?= $rs->email; ?>" placeholder="E-mail" required="required" />
				
				<? if ($_COOKIE["perfil"]!="3") { ?>
				<label for="cpf" class="menor muted">CPF:</label>
                <input type="text" name="cpf" id="cpf" value="<?= $rs->cpf; ?>" placeholder="000.000.000/00" />
                
                <label for="rg" class="menor muted">RG:</label>
                <input type="text" name="rg" id="rg" value="<?= $rs->rg; ?>" placeholder="0.000.000/UF" />
				
				<label for="registro" class="menor muted">CRM/UF:</label>
                <input type="text" name="registro" id="registro" value="<?= $rs->registro; ?>" placeholder="CRM 0000/UF" />
                <? } ?>
        	</div>
			
			<div class="span3">
				
				<? if ($_COOKIE["perfil"]!="3") { ?>
				<label for="id_especialidade" class="menor muted">Especialidade:</label>
				<select id="id_especialidade" name="id_especialidade"">
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
				
				<label for="sexo" class="menor muted">Sexo:</label>
				<select id="sexo" name="sexo">
					<option value="">- selecione -</option>
					<option value="m" <? if ($rs->sexo=='m') echo 'selected="selected"'; ?>>Masculino</option>
					<option value="f" <? if ($rs->sexo=='f') echo 'selected="selected"'; ?>>Feminino</option>
				</select>
            	
            	<label for="data_nasc" class="menor muted">Data de nascimento:</label>
                <input type="text" name="data_nasc" id="data_nasc" value="<?= desformata_data($rs->data_nasc); ?>" placeholder="00/00/0000" maxlenght="10" />
                <br /><br />
                <? } ?>
                
                <div class="well well-small">
	                <label for="senha" class="menor muted">Senha:</label>
	            	<input style="width:90%;" type="password" name="senha" id="senha" placeholder="Caso queira alterar" />
					
	            	<label for="senha2" class="menor muted">Confirmação:</label>
	            	<input style="width:90%;" type="password" name="senha2" id="senha2" placeholder="Confirmação de senha" />
            	</div>
			</div>
			
			<div class="span4 offset1">
				
            	
            	
            	<label for="foto">Foto:</label>
            	<input type="file" name="foto" id="foto" title="Escolher foto" />
            	<br /><br />
            	
            	<? if ($rs->foto!="") { ?>
	            
	            <div id="foto_area">
            		<img class="img-rounded" src="includes/timthumb/timthumb.php?src=<?= $rs->foto; ?>&amp;w=120&amp;h=120&amp;zc=1&amp;q=95" border="0" alt="" />
	            </div>
	            
            	<br />
	            <a id="foto_usuario_excluir" class="btn btn-mini btn-danger" href="javascript:apagaMinhaFoto();">Apagar foto atual</a> <br /><br />
	            
	            <? } ?>
			</div>
        </div>   
		<br />
		
	    <div class="form-actions">
	    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
			<a type="button" class="btn cancelar" href="./?pagina=lancamento/lancamento">Cancelar</a>
	    </div>
		
	</form>
	
<? } ?>