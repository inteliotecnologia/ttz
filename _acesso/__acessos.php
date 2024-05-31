<?
if (pode("1", $_COOKIE["perfil"])) {
	
	if ($_GET[id_usuario]!='') {
		$str= " and   id_usuario = '". $_GET[id_usuario] ."' ";
		$subtit= pega_nome_usuario($_GET[id_usuario]);
	}
	else {
		$subtit= "Listando todos";
	}
	
	$sql= "select * from acessos
			where 1 = 1
			$str
			order by id_acesso desc";
	$result= mysqli_query($conexao1, $sql) or die(mysqli_error());
	$total_antes= mysqli_num_rows($result);
		
	$num= 30;
	$total_linhas = mysqli_num_rows($result);
	$num_paginas = ceil($total_linhas/$num);
	if (!isset($_GET[num_pagina])) $num_pagina= 1;
	else $num_pagina= $_GET[num_pagina];
	$num_pagina--;
	
	$inicio= $num_pagina*$num;
	
	$result= mysqli_query($conexao1, $sql ." limit $inicio, $num") or die(mysqli_error());

?>
	<div class="span12">
		<? include("__acesso_menu.php"); ?>
		
		<div class="page-header">
			<h2>Acessos ao sistema <small><?=$subtit;?></small></h2>
		</div>
		<br />
		
		<p>Total de <b><?=$total_antes;?></b> acessos ao sistema.</p>
		<br />
		
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="5%">#</th>
		            <th width="20%" align="left">Usuário/Local</th>
		            <th width="10%" align="left">Data/hora</th>
		            <th width="20%">IP</th>
		            <th width="30%">User Agent</th>
		        </tr>
		    </thead>
		    <tbody>
			<?
			$i= 0;
			while ($rs= mysqli_fetch_object($result)) {
			?>
			<tr>
				<td><a href="./?pagina=acesso/log&amp;id_acesso=<?= $rs->id_acesso; ?>"><?= $rs->id_acesso; ?></a></td>
				<td>
					<a href="./?pagina=acesso/log&amp;id_usuario=<?= $rs->id_usuario; ?>"><?= pega_nome_usuario($rs->id_usuario); ?></a> <br />
					<small><?= pega_clinica($rs->id_clinica); ?></small>
				</td>
				<td><small><?= desformata_data($rs->data) .'<br /> '. $rs->hora; ?></small></td>
				<td>
				<?
		        if ($rs->ip!="") {
					echo $rs->ip;
					if ($rs->ip!=$rs->ip_reverso)
						echo " <small>(". $rs->ip_reverso .")</small>";
				}
				else
					echo "anônimo";
				?>
		        </td>
		        <td><small><small><?= $rs->user_agent; ?></small></small></td>
			</tr>
			<? $i++; } ?>
			</tbody>
		</table>
		<br />
<?
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		echo "<div class='pagination pagination-centered'> <ul>"; 
		
		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i) $texto_paginacao .= "<li class='disabled'><a href='#'>". $link ."</a></li>";
			else $texto_paginacao .=  "<li><a href=\"./?pagina=acesso/acessos&num_pagina=". $link ."\">". $link ."</a></li> ";
		}

		echo $texto_paginacao .'</ul>';
	}
}
?>
<br /><br /><br /><br /><br />
<? } ?>