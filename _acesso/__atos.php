<?
if (pode("1", $_COOKIE["perfil"])) {
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status!="") $str= "and   status = '". $status ."' ";
	
	if ($_GET["id_ato_pai"]!="") $id_ato_pai= $_GET["id_ato_pai"];
	if ($_POST["id_ato_pai"]!="") $id_ato_pai= $_POST["id_ato_pai"];
	if ($id_ato_pai=='') $id_ato_pai=0;
	
	if ($id_ato_pai!="") {
		$str= "and   id_ato_pai = '". $id_ato_pai ."' ";
		$subtit= pega_ato($id_ato_pai);
	}
	else $subtit="Todos";
	
	/*
	$result= mysqli_query($conexao1, "select * from atos
							where status <> '2'
							". $str ."
							order by atos.ato asc
							") or die('1:'.mysqli_error());
	
	$num= 9999;
	$total = mysqli_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	*/
	
?>
	
	<div class="span12">
		
		<? include("__acesso_menu.php"); ?>
		
		<div class="page-header">
			<h2>Procedimentos <small><?=$subtit;?></small></h2>
		</div>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/ato&amp;acao=i">Novo</a>
		</div>
		<br /><br />
		
		<?
    	
	    ?>
	    	    
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="5%">#</th>
		            <th width="45%" align="left">Procedimento</th>
		            <th width="20%">Código</th>
		            <th width="20%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
				$result= mysqli_query($conexao1, "select * from atos
										where id_ato_pai = '$id_ato_pai'
										");
										
		        $i=0;
		        while ($rs= mysqli_fetch_object($result)) {
		            
		            $result_filho= mysqli_query($conexao1, "select * from atos
												where id_ato_pai = '". $rs->id_ato ."'
												");
					$num_filho= mysqli_num_rows($result_filho);
		        ?>
		        <tr id="linha_<?=$rs->id_ato;?>">
		            <td align="center"><?= $rs->id_ato; ?></td>
		            <td>
		            <? if ($num_filho>0) { ?>
		            	<a href="./?pagina=acesso/atos&id_ato_pai=<?=$rs->id_ato;?>"><?= $rs->ato; ?>
		            <? } else { ?>
		            <?= $rs->ato; ?>
		            <? } ?>
					</td>
					<td><?= formata_cbhpm($rs->codigo_cbhpm); ?></td>
		            <td align="center">
		                <a class="btn btn-mini btn-success" href="./?pagina=acesso/ato&amp;acao=e&amp;id_ato=<?= $rs->id_ato; ?>">
		                	<i class="icon-white icon-pencil"></i> Editar
		                </a>
		                <? /*
		                <a href="javascript:void(0);" onclick="situacaoLinha('usuarioStatus', '<?= $rs->id_usuario; ?>', '<?= $status; ?>');">
		                    <img border="0" id="situacao_link_<?=$rs->id_usuario;?>" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
		                    */ ?>
		                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('atoExcluir',<?=$rs->id_ato;?>);" onclick="return confirm('Tem certeza que deseja excluir?');">
		                    <i class="icon-white icon-trash"></i> Excluir
		                </a>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
	</div>
	

<? } ?>