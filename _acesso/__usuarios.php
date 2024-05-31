<?
if (pode("1", $_COOKIE["perfil"])) {
	
	if ($_GET["status_usuario"]!="") $status_usuario= $_GET["status_usuario"];
	if ($_POST["status_usuario"]!="") $status_usuario= $_POST["status_usuario"];
	if ($status_usuario=='') $status_usuario='1';
	
	if ($status_usuario!="") $str= "and   status_usuario = '". $status_usuario ."' ";
	
	if ($status_usuario=='1') $subtit= 'Ativos';
	else $subtit= 'Inativos';
	
	$result= mysqli_query($conexao1, "select *, usuarios.id_usuario as id_usuario from pessoas, usuarios
							where usuarios.id_pessoa = pessoas.id_pessoa
							". $str ."
							
							order by usuarios.id_usuario asc
							");
	
	$num= 50;
	$total = mysqli_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;
	
	$result= mysqli_query($conexao1, "select *, usuarios.id_usuario as id_usuario from pessoas, usuarios
							where usuarios.id_pessoa = pessoas.id_pessoa
							". $str ."
							
							order by usuarios.id_usuario asc
							limit $inicio, $num
							");
	
?>
	
	<? include("__acesso_menu.php"); ?>
	
	<div class="page-header">
		<h2>Usuários <small>Listando <?=$subtit;?></small></h2>
	</div>
	
	<div class="btn-group">
		<a class="btn btn-primary" href="./?pagina=acesso/usuario&amp;acao=i">Novo usuário</a>

		<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
			Listar
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<li><a href="./?pagina=acesso/usuarios&amp;status_usuario=1">Ativos</a></li>
			<li><a href="./?pagina=acesso/usuarios&amp;status_usuario=2">Inativos</a></li>
		</ul>
		
	</div>
	<br /><br />
	
	<? if ($total>0) { ?>
	<table cellspacing="0" width="100%" class="table table-striped table-hover">
		<thead>
	        <tr>
	            <th width="5%">#</th>
	            <th width="35%" align="left">Nome</th>
	            <th width="20%" align="left">Tipo</th>
	            <th width="20%" align="left">Acessos</th>
	            <th width="22%">Ações</th>
	        </tr>
	    </thead>
	    <tbody>
			<?
	        $i=0;
	        while ($rs= mysqli_fetch_object($result)) {
	            
	            if ($rs->status_usuario==1) $status= 0;
	            else $status= 1;
	            
	            
	            
	            
	        ?>
	        <tr id="linha_<?=$rs->id_usuario;?>">
	            <td align="center"><?= $rs->id_usuario; ?></td>
	            <td>
	            	<?
	            	/*$result_arruma= mysqli_query($conexao1, "select distinct(pessoas_clinicas_convenios.id_clinica) as id_clinica
		            										from pessoas_clinicas_convenios
		            										where id_pessoa = '". $rs->id_pessoa ."'
		            										");
		            while ($rs_arruma= mysqli_fetch_object($result_arruma)) {
			            echo $rs_arruma->id_clinica .' / ';
			            
			            $result_insere= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
			            											(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
			            											values
			            											('". $rs->id_pessoa ."', '". $rs_arruma->id_clinica ."', '1', '-1', '0', '0', '0')
			            											");
			            											
			            $result_insere2= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
			            											(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
			            											values
			            											('". $rs->id_pessoa ."', '". $rs_arruma->id_clinica ."', '2', '-1', '0', '0', '0')
			            											");
		            }
		            */
	            	?>
	            	
	            	<? /*<a href="link.php?chamada=emulaUsuario&amp;id_usuario=<?=$rs->id_usuario;?>">*/ ?>
	            	
	            	<?= $rs->nome; ?>
	            	
	            	<? /*</a>*/ ?> <br/>
	            	
	            	<small><?= $rs->email; ?></small> <br/>
	            	<small><?= $rs->telefone; ?></small> <br/>
	            	
	            	<small><?= formata_data_timestamp($rs->data_hora_cadastro); ?></small>
	            	
	            	<? if ($rs->cupom!='') echo '<br/><br/><small><b>Cupom:</b> '. $rs->cupom .'</small>'; ?>
	            </td>
	            <td>
	            <small>
	            <?= pega_perfil($rs->perfil); ?>
	            <br />
	            <?= $rs->registro; ?>
	            </small>
	            </td>
	            <td>
	            <?
	            $result_acessos= mysqli_query($conexao1, "select count(id_acesso) as total from acessos
	            								where id_usuario = '". $rs->id_usuario ."'
	            								");
	            $rs_acessos= mysqli_fetch_object($result_acessos);
	            
	            echo $rs_acessos->total;
	            ?>
	            </td>
	            <td align="center">
	                <a class="btn btn-mini btn-primary" href="./?pagina=acesso/usuario&amp;acao=e&amp;id_usuario=<?= $rs->id_usuario; ?>">
	                	<i class="icon-white icon-pencil"></i> Editar
	                </a>
	                
	                <? if ($rs->status_usuario=='1') { ?>
	                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('usuarioInativar', <?=$rs->id_usuario;?>);" onclick="return confirm('Inativar este usuário? Os dados serão mantidos.');">
	                    <i class="icon-white icon-arrow-down"></i> Inativar
	                </a>
	                <? } else { ?>
	                <a class="btn btn-mini btn-success" href="javascript:apagaLinha('usuarioAtivar', <?=$rs->id_usuario;?>);" onclick="return confirm('Ativar este usuário? Ele poderá logar no sistema.');">
	                    <i class="icon-white icon-arrow-up"></i> Ativar
	                </a>
	                <? } ?>
	            </td>
	        </tr>
	        <? $i++; } ?>
	    </tbody>
	</table>
	
	<?
	if ($num_paginas > 1) {
		$link_pagina= "acesso/usuarios";
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