<?
require_once("includes/conexao.php");
if (pode("1", $_COOKIE["perfil"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$st='Editar';
		$result= mysqli_query($conexao1, "select * from  atos
								where atos.id_ato = '". $_GET["id_ato"] ."'
								limit 1
								") or die(mysqli_error());
		$rs= mysqli_fetch_object($result);
		$tit= $rs->ato;
	}
	else {
		$st='Novo';
		$tit='Ato';
	}
?>

		
	<div class="span12">
		
		<div class="page-header">
			<h1><?=$tit;?> <small><?=$st;?> ato</small></h1>
		</div>
		
		<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formAto&amp;acao=<?= $acao; ?>" method="post" name="form">
		    
		    <? if ($acao=='e') { ?>
		    <input name="id_ato" class="escondido" type="hidden" id="id_ato" value="<?= $rs->id_ato; ?>" />
		    <? } ?>
		    
	        <div class="row-fluid">
	        	<div class="span4">
	                <label for="ato">Ato:</label>
	                <input type="text" name="ato" id="ato" value="<?= $rs->ato; ?>" placeholder="Ato" required="required" />
	        	</div>
	        	<div class="span4">
	                <label for="id_ato_pai">Ato pai:</label>
					<select id="id_ato_pai" name="id_ato_pai" required="required">
						<option value="">- selecione -</option>
						<?
						$result_pai= mysqli_query($conexao1, "select * from atos
												where id_ato_pai = '0'
												and   id_ato = '2'
												");
						while ($rs_pai= mysqli_fetch_object($result_pai)) {
						?>
						<option <? if ($rs_pai->id_ato==$rs->id_ato_pai) echo 'selected="selected"'; ?> value="<?=$rs_pai->id_ato;?>"><?=$rs_pai->ato;?></option>
						<? } ?>
					</select>
	        	</div>
				<div class="span4">
					<label for="codigo_amb">Código AMB:</label>
	                <input type="text" name="codigo_amb" id="codigo_amb" value="<?= $rs->codigo_amb; ?>" placeholder="Código AMB" />
	        	</div>
				
	        </div>   
			<br />
			
			<? if ($acao=='e') { ?>
			
			<? } ?>
			
		    <div class="form-actions">
		    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
				<a type="button" class="btn" href="./?pagina=acesso/atos">Cancelar</a>
		    </div>
			
		</form>
	</div>

<? } ?>
