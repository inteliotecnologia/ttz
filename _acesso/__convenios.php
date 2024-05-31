<?
if (pode("1", $_COOKIE["perfil"])) {
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status!="") $str= "and   status = '". $status ."' ";
	
	/*
	$result= mysqli_query($conexao1, "select * from convenios
							where status <> '2'
							". $str ."
							order by convenios.convenio asc
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
			<h2>Convênios <small>Listando todos</small></h2>
		</div>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/convenio&amp;acao=i">Novo convênio</a>
		</div>
		<br /><br />
		
		<?
	    for ($t=1; $t<4; $t++) {
	    	$result= mysqli_query($conexao1, "select * from convenios
									where convenios.status <> '2'
									and   tipo_convenio = '$t'
									order by convenios.convenio asc
									");
	    ?>
	    
	    <h3><?= pega_tipo_convenio($t); ?></h3>
	    
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="5%">#</th>
		            <th width="30%" align="left">Convênio</th>
		            <th width="20%">Por</th>
		            <th width="10%">Qtde</th>
		            <th width="20%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysqli_fetch_object($result)) {
		            if ($rs->status==1) $status= 0;
		            else $status= 1;
		        ?>
		        <tr id="linha_<?=$rs->id_convenio;?>">
		            <td align="center"><?= $rs->id_convenio; ?></td>
		            <td><?= $rs->convenio; ?>
		            
		            <? if ($rs->label!='') { ?>
		            &nbsp; <span class="label label-mini"><?=$rs->label;?></span>
		            <? } ?>
		            
		            </td>
		            <td><?= pega_usuario($rs->id_usuario); ?></td>
		            <td><?
			        $result_qtde= mysqli_query($conexao1, "select * from atendimentos_uni
			        										where id_convenio = '". $rs->id_convenio ."'
			        										and   status_atendimento = '1'
			        										") or die(mysqli_error());
			        
			        $num_qtde= mysqli_num_rows($result_qtde);
			        
			        echo $num_qtde;
		            ?></td>
		            <td align="center">
		                <a class="btn btn-mini btn-success" href="./?pagina=acesso/convenio&amp;acao=e&amp;id_convenio=<?= $rs->id_convenio; ?>">
		                	<i class="icon-white icon-pencil"></i> Editar
		                </a>
		                <? /*
		                <a href="javascript:void(0);" onclick="situacaoLinha('usuarioStatus', '<?= $rs->id_usuario; ?>', '<?= $status; ?>');">
		                    <img border="0" id="situacao_link_<?=$rs->id_usuario;?>" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
		                    */ ?>
		                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('convenioExcluir',<?=$rs->id_convenio;?>);" onclick="return confirm('Tem certeza que deseja excluir?');">
		                    <i class="icon-white icon-trash"></i> Excluir
		                </a>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
		<? } ?>
		
	</div>
	

<? } ?>