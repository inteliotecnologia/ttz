<?
if (pode("1", $_COOKIE["perfil"])) {
	//require_once("includes/conexao.php");
	
	$acao= $_GET[acao];
	
	/*$result= mysqli_query($conexao1, "select * from  usuarios, pessoas
							where usuarios.id_usuario = '". $_COOKIE["id_usuario"] ."'
							and   pessoas.id_pessoa = usuarios.id_pessoa
							") or die(mysqli_error());
	$rs= mysqli_fetch_object($result);	*/
	
	$result_pc= mysqli_query($conexao1, "select * from clinicas, pessoas_clinicas
								where  pessoas_clinicas.id_pessoa = '". $_COOKIE[id_pessoa] ."'
								/* and   pessoas_clinicas.status_pc = '1' */
								and   pessoas_clinicas.id_clinica = clinicas.id_clinica
								and   pessoas_clinicas.id_pc = '". $_GET[id_pc] ."'
								") or die(mysqli_error());
	$rs_pc= mysqli_fetch_object($result_pc);
?>
	
	<div id="modal_nova_clinica" class="modal hide fade" role="dialog">
	    <div class="modal-header">
	        <a href="#" data-dismiss="modal" aria-hidden="true" class="close">x</a>
	         <h3>Clínica <small>Nova clínica</small></h3>
	    </div>
	    <form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formClinica&amp;acao=i" method="post" name="form">
	    
	    	<div class="modal-body">
	    	
			    <input type="hidden" name="origem" value="2" />
			    
		        <div class="row-fluid">
		        	<div class="span12">
		                <label for="clinica">Nome da clínica:</label>
		                <input class="input-block-level" type="text" name="clinica" id="clinica" value="<?= $rs->clinica; ?>" placeholder="Nome da clínica" required="required" />
		                <span class="help-block">Preencha com o nome Comercial da Clínica. Atenção, pois este nome poderá ser editado restritamente.</span>
		                
		                <label for="endereco">Endereço:</label>
		                <input class="input-block-level" type="text" name="endereco" id="endereco" value="<?= $rs->endereco; ?>" placeholder="Endereço" required="required" />
		                
		                <label for="id_cidade">Cidade:</label>
						<select class="input-block-level" id="id_cidade" name="id_cidade"  required="required">
							<option value="">- selecione -</option>
							<?
							$result_cid= mysqli_query($conexao1, "select * from cidades, ufs
														where cidades.id_uf = ufs.id_uf
														order by cidades.cidade asc
													") or die(mysqli_error());
							while ($rs_cid= mysqli_fetch_object($result_cid)) {
							?>
							<option <? if ( ($rs->id_cidade==$rs_cid->id_cidade) || (($acao=='i') && ($rs_cid->id_cidade==4500)) ) echo 'selected="selected"'; ?> value="<?=$rs_cid->id_cidade;?>"><?=$rs_cid->cidade .'/'. $rs_cid->uf;?></option>
							<? } ?>
						</select>
		        	</div>
		        	
					<div class="span12" style="display:none;">
						<label for="latitude">Latitude:</label>
		                <input type="text" name="latitude" id="latitude" value="<?= $rs->latitude; ?>" placeholder="Latitude" />
		                
		                <label for="longitude">Longitude:</label>
		                <input type="text" name="longitude" id="longitude" value="<?= $rs->longitude; ?>" placeholder="Longitude" />
		        	</div>
					
		        </div>
		        
		    </div>
		    <div class="modal-footer">
		      <a href="#" id="nao" data-dismiss="modal" aria-hidden="true" class="btn cancelar">Cancelar</a>
		      <button class="btn btn-primary" type="submit" data-loading-text="Cadastrando...">Cadastrar</button>
		    </div>
	    </form>
	</div>
	
	<div id="modal_novo_procedimento" class="modal hide fade" role="dialog">
	    <div class="modal-header">
	        <a href="#" data-dismiss="modal" aria-hidden="true" class="close">x</a>
	         <h3>Procedimento <small>Novo procedimento</small></h3>
	    </div>
	    <form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formProcedimento&amp;acao=i" method="post" name="form">
	    
	    	<div class="modal-body">
	    	
			    <input type="hidden" name="origem" value="2" />
			    
		        <div class="row-fluid">
		        	<div class="span12">
		                <label for="procedimento">Procedimento:</label>
		                <input class="input-block-level" type="text" name="procedimento" id="procedimento" value="<?= $rs->ato; ?>" placeholder="Procedimento" required="required" />
		                
		                <label for="codigo_amb">Código AMB:</label>
		                <input class="input-block-level" type="text" name="codigo_amb" id="codigo_amb" value="<?= $rs->codigo_amb; ?>" placeholder="Código AMB" required="required" />
		                
		        	</div>
					
		        </div>
		        
		    </div>
		    <div class="modal-footer">
		      <a href="#" id="nao" data-dismiss="modal" aria-hidden="true" class="btn cancelar">Cancelar</a>
		      <button class="btn btn-primary" type="submit" data-loading-text="Cadastrando...">Cadastrar</button>
		    </div>
	    </form>
	</div>
		
	<? include('dados_menu.php'); ?>
	
	<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formTrabalhoClinica" method="post" name="form">
		
		<!--<div class="page-header">
			<h2>Clínica onde trabalho</h2>
		</div>-->
		
		<input type="hidden" name="origem" value="<?=$_GET[origem];?>" />
				
		<div class="row-fluid">
			<div class="span6">
				
				<? /*<input autocomplete="off" class="span8 input-block-level" id="clinica_ta" name="clinica" type="text" placeholder="Nome da clínica" required="required" value="<?=$rs_pc->clinica;?>" />
				
				<input type="hidden" name="id_clinica" id="id_clinica" value="<?= $rs_pc->id_clinica; ?>" required="required" />*/ ?>
				
				<label class="pull-left" for="clinica">Clínica:</label> 
				
				<? if ($acao=='i') { ?>
				<i title="Escolha uma clínica na lista ou cadastre uma nova, caso não esteja nesta relação." class="tt tt-ajuda pull-left icon-question-sign"></i>
				
				<select id="id_clinica" name="id_clinica" class="input-block-level" required="required">
					<option value="">- selecione -</option>
					<?
					$result_cli= mysqli_query($conexao1, "select * from clinicas
												where status <> '2'
												and   id_clinica not in
												(
												select id_clinica from pessoas_clinicas
												where id_pessoa = '". $_COOKIE[id_pessoa] ."'
												and   status_pc = '1'
												)
												order by clinica asc
											") or die(mysqli_error());
					while ($rs_cli= mysqli_fetch_object($result_cli)) {
					?>
					<option <? if ( ($rs_pc->id_clinica==$rs_cli->id_clinica) || ($_GET[id_clinica]==$rs_cli->id_clinica) ) echo 'selected="selected"'; ?> value="<?=$rs_cli->id_clinica;?>"><?=$rs_cli->clinica;?> - <?= pega_cidade($rs_cli->id_cidade); ?></option>
					<? } ?>
				</select>
				
				<a id="nova_clinica" class="btn btn-warning btn-mini" href="javascript:void(0);">A clínica não está na lista. Cadastrar uma nova.</a>
				
				<? } else { ?>
				<br />
				<br><strong><?= pega_clinica($rs_pc->id_clinica); ?></strong>
				<input type="hidden" id="id_clinica" name="id_clinica" value="<?=$rs_pc->id_clinica; ?>" />
				
				<br /><br />
				
				<a class="btn btn-mini btn-danger" href="javascript:apagaLinhaDentro('desabilitaPessoaClinica', <?=$rs_pc->id_pc;?>);" onclick="return confirm('Tem certeza que deseja remover esta clínica? Não se preocupe, os dados não serão apagados. Você somente não verá mais esta clínica na listagem para realizar os lançamentos diários.');">
	                <i class="icon-white icon-trash"></i> Não trabalho mais nesta clínica
	            </a>
				<? } ?>
				<br />
			</div>
			<div class="span6">
				<label class="pull-left"><strong>Modo de utilização:</strong></label> <i title="Escolha entre somente contabilidade ou identificar os atendimentos." class="tt tt-ajuda pull-left icon-question-sign"></i>
				<br class="clearfix" /><br class="clearfix" />
				
				<label class="radio">
					<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_1" value="1" <? if (($acao=='i') || ($rs_pc->identifica_atendimentos=='1')) echo 'checked';?> />
					Somente contador
				</label>
				
				<label class="radio">
					<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_2" value="2" <? if ($rs_pc->identifica_atendimentos=='2') echo 'checked';?> />
					Contador + Nome do paciente
				</label>
				
				<label class="radio">
					<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_3" value="3" <? if ($rs_pc->identifica_atendimentos=='3') echo 'checked';?> />
					Contador + Nome + Prontuário Online
				</label>
				<br class="clearfix" /><br class="clearfix" />
				
				
				<label class="pull-left"><strong>Modo de recebimento de <u>convênios pagos</u>:</strong></label> <i title="Como a clínica te paga os convênios em dinheiro?" class="tt tt-ajuda pull-left icon-question-sign"></i>
				<br class="clearfix" /><br class="clearfix" />
				
				<label class="radio">
					<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_1" value="1" <? if (($acao=='i') || ($rs_pc->modo_recebimento_convenios_pagos=='1')) echo 'checked';?> />
					Sim, levo 100% do valor dos atendimentos
				</label>
				
				<label class="radio">
					<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_2" value="2" <? if ($rs_pc->modo_recebimento_convenios_pagos=='2') echo 'checked';?> />
					Sim, levo minha % já acertada
				</label>
				
				<label class="radio">
					<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_3" value="3" <? if ($rs_pc->modo_recebimento_convenios_pagos=='3') echo 'checked';?> />
					Não, recebo somente no repasse mensal
				</label>
				<br />
			</div>
			
		</div>
		<br />
		
		<h4>Valores das consultas e procedimentos que atendo nesta clínica:</h4>
		
		<p>Para consultas e procedimentos, selecione quais convênios atende, confirme o valor e informe a porcentagem acertada com a clínica. <strong>Dica:</strong> você pode usar o botão "aplicar a todos" para economizar tempo ao digitar.</p>
		
		<br />
		
		<?
		$result_atos= mysqli_query($conexao1, "select * from atos
									where id_ato_pai = '0'
									order by id_ato") or die(mysqli_error());
		$num_atos= mysqli_num_rows($result_atos);
		
		if ($num_atos>0) {
		?>
		<ul class="nav nav-tabs" id="atos">
			<?
			$a=0;
			while ($rs_atos= mysqli_fetch_object($result_atos)) {
			?>
			<li class="<? if ($a==0) echo 'active'; ?>"><a data-toggle="tab" href="#ato_pai_<?=$rs_atos->id_ato; ?>"><h4><?=$rs_atos->ato;?></h4></a></li>
			<? $a++; } ?>
			<!--<li><a href="#">+ Novo procedimento</a></li>-->
		</ul>
		<? } ?>
		
		<?
		$result_atos= mysqli_query($conexao1, "select * from atos
									where id_ato_pai = '0'
									order by ato") or die(mysqli_error());
		$num_atos= mysqli_num_rows($result_atos);
		
		if ($num_atos>0) {
		?>
		<div class="tab-content">
			<?
			$k=0;
			$a=0;
			while ($rs_atos= mysqli_fetch_object($result_atos)) {
				$i=0;
			?>
			<div class="tab-pane <? if ($a==0) echo 'active'; ?>" id="ato_pai_<?=$rs_atos->id_ato;?>">
				
				<?
				//se for aba de procedimentos, carrega todos aqui dentro
				if ($rs_atos->tem_filhos=='1') {
				?>
					<h4>Adicionar novo procedimento</h4>
					
					<div class="form-inline">
						<label>Procedimento: &nbsp; </label>
						<select name="id_procedimento" id="id_procedimento" class="input-level-block">
							<option value="">- selecione -</option>
							<?
							$result_atos_filhos= mysqli_query($conexao1, "select * from atos
															where id_ato_pai = '". $rs_atos->id_ato ."'
															and id_ato not in (
															select distinct(id_ato) from pessoas_clinicas_convenios
															where id_pessoa = '". $_COOKIE[id_pessoa] ."'
															and   id_clinica = '". $rs_pc->id_clinica ."'
															and   id_ato <> '1'
															)
															order by ato") or die(mysqli_error());
							$num_atos_filhos= mysqli_num_rows($result_atos_filhos);
							
							while ($rs_atos_filhos= mysqli_fetch_object($result_atos_filhos)) {
							?>
							<option value="<?=$rs_atos_filhos->id_ato; ?>"><?= $rs_atos_filhos->ato; ?></option>
							<? } //fim while atos filhos ?>
							<option value="">-</option>
							<option value="-1" class="cadastrar_novo">Cadastrar novo</option>
						</select>
						
						<button class="btn btn-mini btn-adicionar_procedimento" type="button">Adicionar</button>
					</div>
					<br />
					<a id="novo_procedimento" class="btn btn-warning btn-mini" href="javascript:void(0);">O procedimento não está na lista. Cadastrar um novo.</a>
					<br /><br />
					
					<h4>Procedimentos que atendo</h4>
					<br />
					
					<div class="accordion" id="accordion_procedimentos">
						<?
						$result_atos_filhos= mysqli_query($conexao1, "select distinct(id_ato) from pessoas_clinicas_convenios
														where id_pessoa = '". $_COOKIE[id_pessoa] ."'
														and   id_clinica = '". $rs_pc->id_clinica ."'
														and   id_ato <> '1'
														") or die(mysqli_error());
						$num_atos_filhos= mysqli_num_rows($result_atos_filhos);
						
						if ($num_atos_filhos==0) {
						?>
							<p class="nenhum_procedimento">Você ainda não tem nenhum procedimento cadastrado para esta clínica.</p>
						<?
						}
						else {
						?>
						
							<?
							while ($rs_atos_filhos= mysqli_fetch_object($result_atos_filhos)) {
							?>
							<div class="accordion-group" id="procedimento_<?= $rs_atos_filhos->id_ato;?>">
								<div class="accordion-heading">
							    	<h5><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_procedimentos" href="#collapse_procedimento_<?= $rs_atos_filhos->id_ato;?>">
							        	<?= pega_ato($rs_atos_filhos->id_ato); ?>
									</a></h5>
									<a class="pull-right btn btn-mini btn-danger btn-remove_procedimento" data-id_ato="<?= $rs_atos_filhos->id_ato;?>" href="javascript:void(0);">remover procedimento</a>
							    </div>
							    <div id="collapse_procedimento_<?= $rs_atos_filhos->id_ato;?>" class="accordion-body collapse out">
							    	<div class="accordion-inner">
							        	<?= gera_setup_ato($_COOKIE[id_pessoa], $rs_pc->id_clinica, $rs_atos_filhos->id_ato, $a, $k); ?>
									</div>
							    </div>
							</div>
							<? } ?>
						
						<? } ?>
					</div> <!-- /#accordion_procedimentos -->
				
				<?
				}
				else {
					echo gera_setup_ato($_COOKIE[id_pessoa], $rs_pc->id_clinica, $rs_atos->id_ato, $a, $k);
				} //fim else
				?>
				
			</div> <!-- / .tab-pane -->
			<? } ?>
		</div>
		<? }// fim num atos  ?>
		
		<input class="ultimo_a" value="<?=$a;?>" type="hidden" />
		
	    <div class="form-actions">
	    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
			<a type="button" class="btn cancelar" href="./?pagina=acesso/trabalho_clinicas">Cancelar</a>
	    </div>
		
	</form>
<? } ?>