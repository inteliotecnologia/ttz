<?
if (pode("1", $_COOKIE["perfil"])) {
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status!="") $str= "and   status = '". $status ."' ";
	
	$result= mysqli_query($conexao1, "select * from clinicas
							where status <> '2'
							". $str ."
							order by clinicas.clinica asc
							") or die('1:'.mysqli_error());
	
	$num= 9999;
	$total = mysqli_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	$result= mysqli_query($conexao1, "select * from clinicas
							where clinicas.status <> '2'
							". $str ."
							order by clinicas.id_clinica desc
							limit $inicio, $num
							");
	
?>
	
	<div class="span12">
		
		<? include("__acesso_menu.php"); ?>
		
		<div class="page-header">
			<h2>Clínicas <small>Listando todas</small></h2>
		</div>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/clinica&amp;acao=i">Nova clinica</a>
		</div>
		<br /><br />
		
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="8%">#</th>
		            <th width="35%" align="left">Clínica</th>
		            <th width="30%" align="left">Por</th>
		            <th width="10%" align="left">Qtde</th>
		            <th width="26%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysqli_fetch_object($result)) {
		            if ($rs->status==1) $status= 0;
		            else $status= 1;
		        ?>
		        <tr id="linha_<?=$rs->id_clinica;?>">
		            <td align="center"><?= $rs->id_clinica; ?></td>
		            <td><?= $rs->clinica; ?></td>
		            <td><?= pega_usuario($rs->id_usuario); ?></td>
		            <td>
		            <?
			        $result_qtde= mysqli_query($conexao1, "select * from pessoas_clinicas
			        										where id_clinica = '". $rs->id_clinica ."'
			        										and   status_pc = '1'
			        										") or die(mysqli_error());
			        
			        $num_qtde= mysqli_num_rows($result_qtde);
			        
			        echo $num_qtde;
		            ?>
		            </td>
		            <td align="center">
		                <a class="btn btn-mini btn-success" href="./?pagina=acesso/clinica&amp;acao=e&amp;id_clinica=<?= $rs->id_clinica; ?>">
		                	<i class="icon-white icon-pencil"></i> Editar
		                </a>
		                <? /*
		                <a href="javascript:void(0);" onclick="situacaoLinha('usuarioStatus', '<?= $rs->id_usuario; ?>', '<?= $status; ?>');">
		                    <img border="0" id="situacao_link_<?=$rs->id_usuario;?>" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
		                    */ ?>
		                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('clinicaExcluir',<?=$rs->id_clinica;?>);" onclick="return confirm('Tem certeza que deseja excluir?');">
		                    <i class="icon-white icon-trash"></i> Excluir
		                </a>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/clinicas";
		?>
			<div class="pagination pagination-small">
				<ul>
					<? if ($num_pagina > 0) {
						$menos = $num_pagina - 1;
						echo "<li><a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $menos. "\">&laquo; Anterior</a></li>";
					}
				
					for ($i=0; $i<$num_paginas; $i++) {
						$link = $i + 1;
						if ($num_pagina==$i)
							echo "<li> <b>". $link ."</b>";
						else
							echo "<li> <a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $i. "\">". $link ."</a></li>";
					}
				
					if ($num_pagina < ($num_paginas - 1)) {
						$mais = $num_pagina + 1;
						echo "<li> <a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $mais ."\">Pr&oacute;xima &raquo;</a></li>";
					}
					?>
				</ul>
		    </div>
		<? } ?>
	</div>
	

<? } ?>