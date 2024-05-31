<?
require_once("includes/conexao.php");
if (pode("1", $_COOKIE["perfil"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$st='Editar';
		$result= mysqli_query($conexao1, "select * from  clinicas
								where clinicas.id_clinica = '". $_GET["id_clinica"] ."'
								limit 1
								") or die(mysqli_error());
		$rs= mysqli_fetch_object($result);
	}
	else $st='Nova';
?>

		
	<div class="span12">
		<div class="page-header">
			<h1>Clínica <small><?=$st;?> clínica</small></h1>
		</div>
		
		<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formClinica&amp;acao=<?= $acao; ?>" method="post" name="form">
		    
		    <? if ($acao=='e') { ?>
		    <input name="id_clinica" class="escondido" type="hidden" id="id_clinica" value="<?= $rs->id_clinica; ?>" />
		    <? } ?>
		    
	        <div class="row-fluid">
	        	<div class="span4">
	                <label for="clinica">Nome da clínica:</label>
	                <input type="text" name="clinica" id="clinica" value="<?= $rs->clinica; ?>" placeholder="Nome da clínica" required="required" />
	                
	                <label for="endereco">Endereço:</label>
	                <input type="text" name="endereco" id="endereco" value="<?= $rs->endereco; ?>" placeholder="Endereço" required="required" />
	        	</div>
	        	<div class="span4">
	                <label for="id_cidade">Cidade:</label>
					<select id="id_cidade" name="id_cidade"  required="required">
						<option value="">- selecione -</option>
						<?
						$result_cid= mysqli_query($conexao1, "select * from cidades, ufs
													where cidades.id_uf = ufs.id_uf
													order by cidades.cidade asc
												") or die(mysqli_error());
						while ($rs_cid= mysqli_fetch_object($result_cid)) {
						?>
						<option <? if ( ($rs->id_cidade==$rs_cid->id_cidade) || (($acao=='i') && ($rs_cid->id_cidade==4500)) ) echo 'selected="selected"'; ?> value="<?=$rs_cid->id_cidade;?>"><?=$rs_cid->cidade .'/'. $rs_cid->uf;?></option>
						<? } ?>
					</select>
	        	</div>
				<div class="span4">
					<label for="latitude">Latitude:</label>
	                <input type="text" name="latitude" id="latitude" value="<?= $rs->latitude; ?>" placeholder="Latitude" />
	                
	                <label for="longitude">Longitude:</label>
	                <input type="text" name="longitude" id="longitude" value="<?= $rs->longitude; ?>" placeholder="Longitude" />
	        	</div>
				
	        </div>   
			<br />
			
		    <div class="form-actions">
		    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
				<a type="button" class="btn" href="./?pagina=acesso/clinicas">Cancelar</a>
		    </div>
			
		</form>
	</div>

<? } ?>
