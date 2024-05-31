<?
if (pode("123", $_COOKIE["perfil"])) {
	
	
	
	$result= mysqli_query($conexao1, "select * from pessoas
							order by pessoas.nome asc
							") or die(mysqli_error());
	$num= mysqli_num_rows($result);
	
	$num= 50;
	$total = mysqli_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;
	
	$result= mysqli_query($conexao1, "select * from pessoas
							order by pessoas.nome asc
							limit $inicio, $num
							");
	$subtit='todos';
?>

	<div class="page-header">
		<h1>Pacientes <small>Listando <?=$subtit;?></small></h1>
	</div>
	
	<? if ($total>0) { ?>
	<table cellspacing="0" width="100%" class="table table-striped table-hover">
		<thead>
	        <tr>
	            <th width="5%">#</th>
	            <th width="25%" align="left">Nome</th>
	            <th width="25%" align="left">Data de nascimento</th>
	            <th width="25%" align="left">Contato</th>
	            <th width="20%">Ações</th>
	        </tr>
	    </thead>
	    <tbody>
			<?
	        $i=0;
	        while ($rs= mysqli_fetch_object($result)) {
	        ?>
	        <tr id="linha_<?=$rs->id_pessoa;?>">
	            <td align="center"><?= $rs->id_pessoa; ?></td>
	            <td><abbr class="tt" title="Cadastrado por <?=pega_usuario($rs->id_usuario);?> em <?=formata_data_timestamp($rs->data_hora_cadastro);?>"><?= $rs->nome; ?></abbr></td>
	            <td><?= desformata_data($rs->data_nasc); ?> &nbsp; <small class="muted"><?=calcula_idade(desformata_data($rs->data_nasc));?> anos</small></td>
	            <td><small><?= $rs->telefone;?> <br /><?= $rs->email; ?></small></td>
	            <td align="center">
	                <? /*<a class="btn btn-mini btn-success" href="./?pagina=acesso/usuario&amp;acao=e&amp;id_usuario=<?= $rs->id_usuario; ?>">
	                	<i class="icon-white icon-pencil"></i> Editar
	                </a>
	                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('usuarioExcluir', <?=$rs->id_usuario;?>);" onclick="return confirm('Tem certeza que deseja suspender este usuário? Os dados serão mantidos.');">
	                    <i class="icon-white icon-trash"></i> Suspender
	                </a>
	                */ ?>
	                &nbsp;
	            </td>
	        </tr>
	        <? $i++; } ?>
	    </tbody>
	</table>
	
	<?
	if ($num_paginas > 1) {
		$link_pagina= "lancamentos/pacientes";
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
	<? } } else echo '<p>Nenhum usuário encontrado.</p>'; ?>

<? } ?>