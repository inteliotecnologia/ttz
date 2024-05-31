<?
require_once("includes/conexao.php");
if (pode("1", $_COOKIE["perfil"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$st='Editar';
		$result= mysqli_query($conexao1, "select * from  convenios
								where convenios.id_convenio = '". $_GET["id_convenio"] ."'
								limit 1
								") or die(mysqli_error());
		$rs= mysqli_fetch_object($result);
	}
	else $st='Novo';
?>

		
	<div class="span12">
		
		<? include("_acesso/convenios_menu.php"); ?>
		
		<div class="page-header">
			<h1><?= $rs->convenio; ?> <small>Valores padrão</small></h1>
		</div>
		
		<? /*<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formConvenio&amp;acao=<?= $acao; ?>" method="post" name="form">
		    
		    <? if ($acao=='e') { ?>
		    <input name="id_convenio" class="escondido" type="hidden" id="id_convenio" value="<?= $rs->id_convenio; ?>" />
		    <? } ?>
		    
	        <div class="row-fluid">
	        	<div class="span4">
	                <label for="convenio">Convênio:</label>
	                <input type="text" name="convenio" id="convenio" value="<?= $rs->convenio; ?>" placeholder="Convênio" required="required" />
	        	</div>
	        	<div class="span4">
	                <label for="tipo_convenio">Tipo de convênio:</label>
					<select id="tipo_convenio" name="tipo_convenio" required="required">
						<option value="">- selecione -</option>
						<? for ($t=1; $t<4; $t++) { ?>
						<option <? if ($rs->tipo_convenio==$t) echo 'selected="selected"'; ?> value="<?=$t;?>"><?=pega_tipo_convenio($t);?></option>
						<? } ?>
					</select>
	        	</div>
				<div class="span4 recebimento_convenio <? if ( ($acao=='i') || ($rs->tipo_convenio=='1')) echo 'hide'; ?>">
					<label for="recebimento">Recebimento:</label>
					<select id="recebimento" name="recebimento">
						<option value="">- selecione -</option>
						<? for ($t=1; $t<3; $t++) { ?>
						<option <? if ($rs->recebimento==$t) echo 'selected="selected"'; ?> value="<?=$t;?>"><?=pega_recebimento_convenio($t);?></option>
						<? } ?>
					</select>
	        	</div>
				
	        </div>   
			<br />
			
			<? if ($acao=='e') { ?>
			
			<? } ?>
			
		    <div class="form-actions">
		    	<button class="btn btn-primary" type="submit">Salvar</button>
				<a type="button" class="btn" href="./?pagina=acesso/convenios">Cancelar</a>
		    </div>
			
		</form>*/ ?>
		
		Em breve.
	</div>

<? } ?>
