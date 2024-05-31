<?
require_once("includes/conexao.php");

$result= mysqli_query($conexao1, "select * from  usuarios, pessoas
						where usuarios.id_usuario = '". $_COOKIE["id_usuario"] ."'
						and   pessoas.id_pessoa = usuarios.id_pessoa
						") or die(mysqli_error());
$rs= mysqli_fetch_object($result);	
?>
			
	<? include('dados_menu.php'); ?>
	
	<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formTema" method="post">
		
		<!--<div class="page-header">
			<h2>Temas</h2>
		</div>-->
	    
        <div class="row-fluid">
        	<div class="span4">
                <label for="tema">Tema:</label>
				<select id="tema" name="tema">
					<?
					$result_tem= mysqli_query($conexao1, "select * from cad_temas
											order by tema asc
											") or die(mysqli_error());
					while ($rs_tem= mysqli_fetch_object($result_tem)) {
					?>
					<option <? if ( ($rs->tema==$rs_tem->tema) || (($_COOKIE[tema]=='') && ($rs_tem->tema=='Normal')) ) echo 'selected="selected"'; ?> value="<?=$rs_tem->tema;?>"><?=$rs_tem->tema;?></option>
					<? } ?>
				</select>
				
        	</div>
			
        </div>   
		<br />
		
	    <div class="form-actions">
	    	<button class="btn btn-primary" type="submit" data-loading-text="Aplicando...">Aplicar</button>
			<a type="button" class="btn cancelar" href="./?pagina=acesso/dados">Cancelar</a>
	    </div>
		
	</form>
