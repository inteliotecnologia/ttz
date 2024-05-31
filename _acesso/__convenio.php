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
		$tit= $rs->convenio;
	}
	else {
		$st='Novo';
		$tit='Convênio';
	}
?>

	<div class="span12">
		
		<? if ($acao=='e') include("_acesso/convenios_menu.php"); ?>
		
		<div class="page-header">
			<h1><?=$tit;?> <small><?=$st;?> convênio</small></h1>
		</div>
		
		<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formConvenio&amp;acao=<?= $acao; ?>" method="post" name="form">
		    
		    <? if ($acao=='e') { ?>
		    <input name="id_convenio" class="escondido" type="hidden" id="id_convenio" value="<?= $rs->id_convenio; ?>" />
		    <? } ?>
		    
	        <div class="row-fluid">
	        	<div class="span3">
	                <label for="convenio">Convênio:</label>
	                <input type="text" name="convenio" id="convenio" value="<?= $rs->convenio; ?>" placeholder="Convênio" required="required" />
	        	</div>
	        	<div class="span3">
	                <label for="label">Label:</label>
	                <input type="text" name="label" id="label" value="<?= $rs->label; ?>" placeholder="Rótulo" />
	        	</div>
	        	<div class="span3">
	                <label for="tipo_convenio">Tipo de convênio:</label>
					<select id="tipo_convenio" name="tipo_convenio" required="required">
						<option value="">- selecione -</option>
						<? for ($t=1; $t<4; $t++) { ?>
						<option <? if ($rs->tipo_convenio==$t) echo 'selected="selected"'; ?> value="<?=$t;?>"><?=pega_tipo_convenio($t);?></option>
						<? } ?>
					</select>
					
					<div class="recebimento_convenio <? if ( ($acao=='i') || ($rs->tipo_convenio=='1')) echo 'hide'; ?>">
						<label for="recebimento">Recebimento:</label>
						<select id="recebimento" name="recebimento">
							<option value="">- selecione -</option>
							<? for ($t=1; $t<3; $t++) { ?>
							<option <? if ($rs->recebimento==$t) echo 'selected="selected"'; ?> value="<?=$t;?>"><?=pega_recebimento_convenio($t);?></option>
							<? } ?>
						</select>
					</div>
	        	</div>
				<div class="span3">
					<label for="valores_multiplos">Aceita valores múltiplos:</label>
					<select id="valores_multiplos" name="valores_multiplos" required="required">
						<option value="0" <? if ( ($acao=='i') || ($rs->valores_multiplos=='0') ) echo 'selected="selected"' ?>>Não</option>
						<option value="1" <? if ($rs->valores_multiplos=='1') echo 'selected="selected"' ?>>Sim</option>
						
					</select>
	        	</div>
				
	        </div>   
			<br />
			
			<? if ($acao=='e') { ?>
			
			<? } ?>
			
		    <div class="form-actions">
		    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
				<a type="button" class="btn cancelar" href="./?pagina=acesso/convenios">Cancelar</a>
		    </div>
			
		</form>
	</div>

<? } ?>
