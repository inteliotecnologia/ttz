<?
if (pode("1234", $_COOKIE["perfil"])) {
	
	if ($_SESSION["emula_id_usuario"]!='') {
		$IDENT_id_usuario= $_SESSION["emula_id_usuario"];
		$IDENT_id_pessoa= pega_usuario_dado($IDENT_id_usuario, "id_pessoa");
		$IDENT_nome= pega_usuario_dado($IDENT_id_usuario, "nome");
		$IDENT_perfil= pega_usuario_dado($IDENT_id_usuario, "perfil");
		
		$IDENT_id_clinica= pega_id_clinica_principal($IDENT_id_pessoa);
	}
	else {
		$IDENT_id_usuario= $_COOKIE["id_usuario"];
		$IDENT_id_pessoa= $_COOKIE["id_pessoa"];
		$IDENT_nome=$_COOKIE["nome"];
		$IDENT_id_clinica= $_COOKIE["id_clinica"];
		$IDENT_perfil= $_COOKIE["perfil"];
	}
	
	
	
	$result= mysqli_query($conexao1, "select * from  usuarios, pessoas
							where usuarios.id_usuario = '". $IDENT_id_usuario ."'
							and   pessoas.id_pessoa = usuarios.id_pessoa
							") or die(mysqli_error());
	$rs= mysqli_fetch_object($result);
	
	
	$clinica_nome= pega_clinica_pessoa($IDENT_id_pessoa, $IDENT_id_clinica);
	
	$result_pag= mysqli_query($conexao1, "select * from pagamentos, usuarios
									where usuarios.id_usuario = '". $_COOKIE["id_usuario"] ."'
									and   usuarios.id_pessoa = '". $_COOKIE["id_pessoa"] ."'
									and   usuarios.id_usuario = pagamentos.id_usuario
									and   usuarios.auth = '". $_COOKIE[auth_usuario] ."'
									order by data_ate desc
									") or die(mysqli_error());
	$rs_pag= mysqli_fetch_object($result_pag);
	
	$tipo_pagamento= $rs_pag->tipo_pagamento;
	$data_ate= $rs_pag->data_ate;
	
	if ($data_ate<date('Y-m-d')) {
		
		
	}
	
?>
	<script>
		<? if ($_SESSION["emula_id_usuario"]!='') { ?>
		mixpanel.track("[Emulação] Acessou lançamento", {
			"ID Usuário": "<?=$IDENT_id_usuario;?>",
			"Usuário": "<?=$IDENT_nome;?>",
			"Data": "<?=formata_data_hifen($data);?>"
		});
		<? } else { ?>
		mixpanel.track("Acessou lançamento", {
			"Data": "<?=formata_data_hifen($data);?>"
		});
		<? } ?>
	</script>
	
	<? /*
	<link rel="stylesheet" type="text/css" href="includes/js/jquery.countdown.package/jquery.countdown.css" />
	*/ ?>
	
	<script type="text/javascript" src="includes/js/jquery.countdown.package/jquery.plugin.js"></script> 
	<script type="text/javascript" src="includes/js/jquery.countdown.package/jquery.countdown.js"></script>
	
	<script>
		$('#link_registro').attr('href', './?pagina=lancamento/lancamento&data=<?=$data;?>');
	</script>
	
	<audio id="caixa_registradora" style="display:none;">
		<source type="audio/mpeg" src="uploads/caixa_registradora.mp3"></source>
		<source type="audio/ogg" src="uploads/caixa_registradora.ogg"></source>
	</audio>
	
	<audio id="som_1" style="display:none;">
		<source type="audio/mpeg" src="uploads/som_1.mp3"></source>
		<source type="audio/ogg" src="uploads/som_1.ogg"></source>
	</audio>
	
	<audio id="som_2" style="display:none;">
		<source type="audio/mpeg" src="uploads/som_2.mp3"></source>
		<source type="audio/ogg" src="uploads/som_2.ogg"></source>
	</audio>
	
	<audio id="som_3" style="display:none;">
		<source type="audio/mpeg" src="uploads/som_3.mp3"></source>
		<source type="audio/ogg" src="uploads/som_3.ogg"></source>
	</audio>
	
	<audio id="som_4" style="display:none;">
		<source type="audio/mpeg" src="uploads/som_4.mp3"></source>
		<source type="audio/ogg" src="uploads/som_4.ogg"></source>
	</audio>
	
	<audio id="som_5" style="display:none;">
		<source type="audio/mpeg" src="uploads/som_5.mp3"></source>
		<source type="audio/ogg" src="uploads/som_5.ogg"></source>
	</audio>
	
	<? /* Configurações da clínica atual */ ?>
	<div id="modal_clinica_opcoes" class="modal hide fade" tabindex="-1" role="dialog">
		
		<form id="modal_clinica_opcoes_form" action="<?=AJAX_FORM;?>formTrabalhoClinicaPeq" method="post">
			
			<input type="hidden" name="id_pc" id="id_pc" value="<?=$rs_pessoa_clinica->id_pc;?>" />
			<input type="hidden" name="id_clinica" id="id_clinica" value="<?=$rs_pessoa_clinica->id_clinica;?>" />
		
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5>Configurações de <?=$nome_clinica;?></h5>
			</div>
			
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span12">
						<label class="muted menor" for="vc_nome_exibicao_clinica">Local:</label>
						
						<input class="input-block-level" type="text" name="nome_exibicao_clinica" id="vc_nome_exibicao_clinica" value="<?=$clinica_nome;?>" placeholder="" required="required" />
					</div>
					
				</div>
				<br/>
				
				<ul class="nav nav-tabs" id="tab_opcoes">
					<li class="active">
						<a href="#contador">	
							Lançamento
						</a>
					</li>
					<li>
						<a href="#plantao">	
							Plantão
						</a>
					</li>
				</ul>
				 
				<div class="tab-content">
					<div class="tab-pane active" id="contador">
						
						<div class="row-fluid">
						
							<div class="span4">
								<label><strong>Modo de utilização:</strong></label>
								
								<label class="radio menor">
									<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_1" value="1"
									<? if (($acao=='i') || ($rs_pessoa_clinica->identifica_atendimentos=='1')) echo 'checked';?> />
									Somente contador
								</label>
								
								<label class="radio menor">
									<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_2" value="2"
									<? if ($rs_pessoa_clinica->identifica_atendimentos=='2') echo 'checked';?> />
									Contador + Nome do paciente
								</label>
								
								<label class="radio menor">
									<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_3" value="3"
									<? if ($rs_pessoa_clinica->identifica_atendimentos=='3') echo 'checked';?> />
									Contador + Nome + Prontuário Online
								</label>
								<br/>
								
							</div>
							
							<div class="span4 voce_recebe">	
								
								<label><strong>Recebe diariamente pagamentos em dinheiro?</strong></label> 
								
								<label class="radio menor">
									<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_1" value="1"
									<? if (($acao=='i') || ($rs_pessoa_clinica->modo_recebimento_convenios_pagos=='1')) echo 'checked';?> />
									Sim, levo 100% do valor dos atendimentos
								</label>
								
								<label class="radio menor">
									<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_2" value="2"
									<? if ($rs_pessoa_clinica->modo_recebimento_convenios_pagos=='2') echo 'checked';?> />
									Sim, levo minha % já acertada
								</label>
								
								<label class="radio menor">
									<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_3" value="3"
									<? if ($rs_pessoa_clinica->modo_recebimento_convenios_pagos=='3') echo 'checked';?> />
									Não
								</label>
								<br class="clearfix" />
								<!-- https://bit.ly/34daaT5 (the hint about the password is at this location: -27.417301, -48.403654 - below the floor - north corner) -->
							</div>
							
							<div class="span4">	
								
								<label><strong>Atende Convênio Próprio?</strong><br> <small class="muted">(Ex: Unimed)</small></label> 
								
								<label class="radio menor">
									<input type="radio" name="convenio_proprio" id="convenio_proprio_1" value="1"
									<? if (($acao=='i') || ($rs_pessoa_clinica->convenio_proprio=='1')) echo 'checked';?> />
									Sim
								</label>
								
								<label class="radio menor">
									<input type="radio" name="convenio_proprio" id="convenio_proprio_0" value="0"
									<? if ($rs_pessoa_clinica->convenio_proprio=='0') echo 'checked';?> />
									Não
								</label>
								
								<br class="clearfix" />
								
							</div>
							
						</div>
					</div>
					
					<div class="tab-pane" id="plantao">
						
						<div class="row-fluid">
						
							<div class="span12">
							
								<label class="checkbox">
									<input type="checkbox" name="plantonista" id="plantonista_1" value="1"
									<? if ($rs_pessoa_clinica->plantonista=='1') echo 'checked';?> />
									
									Habilitar controle de horas.
								</label>
								
								<br />
							</div>
						</div>
						
						<div class="row-fluid">
						
							<div class="span2 text-right menor muted">
								&nbsp;
							</div>
							<div class="span4 offset1 text-center">
								Horas diurnas
							</div>
							
							<div class="span4 offset1 text-center">
								Horas noturnas
							</div>
							
						</div>
						
						<?
						for ($i=1; $i<3; $i++) {
							$j=$i-1;
							
							if ($i==1) {
								$lbl="Dias úteis";
							}
							else {
								$lbl="Domingos e Feriados";
							}
						?>
						
						<input type="hidden" name="tipo_dia[<?=$j;?>]" value="<?=$i;?>" />
						
						<div class="row-fluid">
						
							<div class="span2 text-right menor muted">
								<?=$lbl;?>
							</div>
							
							<?
							for ($k=0; $k<2; $k++) {
								$l=$k+1;
								
								$result_resgata1= mysqli_query($conexao1, "select * from pessoas_clinicas_horas
																		where id_pessoa = '". $IDENT_id_pessoa ."'
																		and   id_clinica = '". $IDENT_id_clinica ."'
																		and   tipo_dia = '". $i ."'
																		and   tipo_hora = '". $l ."'
																		");
								$rs_resgata1= mysqli_fetch_object($result_resgata1);
							?>
							<input type="hidden" name="tipo_hora[<?=$j;?>][<?=$k;?>]" value="1" />
							<div class="span2 offset1 text-center">
								<input disabled="" class="input-block-level" autocomplete="off" type="number" name="de[<?=$j;?>][<?=$k;?>][0]" value="<?=$rs_resgata1->de;?>" placeholder="De"  />
							</div>
							<div class="span2 text-center">
								<input disabled="" class="input-block-level" autocomplete="off" type="number" name="ate[<?=$j;?>][<?=$k;?>][0]" value="<?=$rs_resgata1->ate;?>" placeholder="Até"  />
							</div>
							<? } ?>
							
							
						</div>
						
						<? } ?>
						
						
						<div class="row-fluid">
						
							<div class="span2 text-right menor muted">
								Valor / hora
							</div>
							
							<?
							for ($k=0; $k<2; $k++) {
								$l=$k+1;
								
								$result_resgata2= mysqli_query($conexao1, "select * from pessoas_clinicas_horas
																		where id_pessoa = '". $IDENT_id_pessoa ."'
																		and   id_clinica = '". $IDENT_id_clinica ."'
																		and   tipo_hora = '". $l ."'
																		and   tipo_dia is NULL
																		and   valor <> ''
																		");
								$rs_resgata2= mysqli_fetch_object($result_resgata2);
							?>
							<div class="span4 offset1 text-center">
								<div class="input-prepend">
									<span class="add-on">R$</span>
									<input disabled="" autocomplete="off" type="text" name="valor_hora[<?=$k;?>]" id="valor_hora<?=$l;?>" value="<?=fnumf($rs_resgata2->valor);?>" placeholder="Valor" required="required" />
								</div>
							</div>
							<? } ?>
							
						</div>
						
							
					</div>
				</div>

				
				
			</div>
			
			<div class="modal-footer">
				
				<a style="margin-top:8px;" class="pull-left btn btn-mini btn-danger exclui_clinica" href="javascript:apagaLinhaDentro('desabilitaPessoaClinica', <?=$rs_pessoa_clinica->id_pc;?>);" onclick="return confirm('Deseja remover este local de atendimento?');">
	                <i class=" icon-trash"></i> Não trabalho mais neste local
	            </a>
				
				<button type="button" class="btn cancelar hidden-phone" data-dismiss="modal">Fechar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
		</form>

	</div>
	
	<? /* Tela de edição de convênio que o médico atende */ ?>
	<div id="modal_convenio_edita" class="modal hide fade" tabindex="-1" role="dialog">
			
		<form id="modal_convenio_edita_form" action="<?=AJAX_FORM;?>alteraValorConvenio" method="post">
			<input type="hidden" name="url" value="./?<?=$_SERVER[QUERY_STRING];?>" />
			<input type="hidden" name="id_ato" id="vc_id_ato" value="" />
			<input type="hidden" name="t" id="vc_t" value="" />
			<input type="hidden" name="id_convenio" id="vc_id_convenio" value="" />
			<input type="hidden" name="ordem" id="vc_ordem" value="" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5>Alterar convênio</h5>
			</div>
				
			<div class="modal-body">
				<p class="muted">A alteração nos valores valerá a partir de agora. Lançamentos antigos não serão alterados.</p>
				
				<div class="row-fluid">
					<div class="span4">
						<label class="muted menor" for="vc_nome_exibicao_convenio">Nome do convênio:</label>
						
						<input type="text" name="nome_exibicao_convenio" id="vc_nome_exibicao_convenio" value="" placeholder="Nome do convênio" required="required" />
					</div>
					<div class="span2">
						<label class="muted menor" for="vc_label_convenio">Rótulo:</label>
						
						<input class="input-block-level" type="text" name="label_convenio" id="vc_label_convenio" value="" placeholder="Opcional" />
					</div>
					<div class="span3">
						<label class="muted menor" for="vc_valor">Valor:</label>
						
						<div class="input-prepend">
							<span class="add-on">R$</span>
							<input autocomplete="off" type="text" name="valor" id="vc_valor" value="" placeholder="Valor" required="required" />
						</div>
					</div>
					<div class="span3">
						<label class="muted menor" for="vc_percentual_clinica">Percentual da Instituição:</label>
						
						<div class="input-append">
							<input autocomplete="off" type="text" name="percentual_clinica" id="vc_percentual_clinica" value="" placeholder="Percentual Instituição" required="required" />
							<span class="add-on">%</span>
						</div>
					</div>
				</div>
				
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary" data-loading-text="OK">OK</button>
			</div>
		</form>
	</div>
	
	<? /* Tela de novo procedimento que a pessoa atende (com typeahead) */ ?>
	<div id="modal_procedimento_edita" class="modal hide fade" tabindex="-1" role="dialog">
			
		<form id="modal_procedimento_edita_form" action="<?=AJAX_FORM;?>editaProcedimento" method="post">
			
		</form>
	</div>
	
	<? /* Tela de novo procedimento que a pessoa atende (com typeahead) */ ?>
	<div id="modal_procedimento_novo" class="modal hide fade" tabindex="-1" role="dialog">
			
		<form id="modal_procedimento_novo_form" action="<?=AJAX_FORM;?>novoProcedimento" method="post">
			<input type="hidden" name="data" id="nc_data" value="<?=$data;?>" />
			<input type="hidden" name="id_procedimento" id="nc_id_procedimento" value="" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5 id="nc_modal_label">Procedimento</h5>
			</div>
				
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span5">
						<label class="muted menor" for="nc_nome_exibicao_procedimento">Procedimento:</label>
						
						<input class="input-block-level" type="text" name="nome_exibicao_procedimento" id="nc_nome_exibicao_procedimento" value="" required="required" />
					</div>
					
					<div class="span3">
						<label class="muted menor" for="nc_codigo_cbhpm">Código CBHPM:</label>
						
						<input class="input-block-level" autocomplete="off" type="text" name="codigo_cbhpm" id="nc_codigo_cbhpm" value="" required="required" />
					</div>
					
					<div class="span4">
						<label class="muted menor" for="nc_apelido">Apelido:</label>
						
						<input class="input-block-level" autocomplete="off" type="text" name="apelido" id="nc_apelido" value="" placeholder="Opcional" />
					</div>
					
					<? /*
					<div class="span3">
						<label class="muted menor" for="vc_percentual_clinica">Percentual Instituição:</label>
						
						<div class="input-append">
							<input pattern="\d*" autocomplete="off" type="text" name="percentual_clinica" id="nc_percentual_clinica" value="" required="required" />
							<span class="add-on">%</span>
						</div>
					</div>
					*/ ?>
				</div>
				
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
		</form>
	</div>
	
	<?
	if ($IDENT_perfil=="3") {
	
		if ($_COOKIE["id_plantonista"]=="")
			$plantonista_nome= "-";
		else
			$plantonista_nome= pega_pessoa($_COOKIE["id_plantonista"]);
		
	
	/* Tela de trocar plantonista */ ?>
	<div id="modal_plantonista" class="modal hide fade" role="dialog">
			
	
			
		<div class="modal-body">
			<div class="row-fluid">
				<div class="span12">
					
					<small class="label_locais muted">Plantonista:</small>
					
					<div class="btn-group btn-block">
			    	
					    <a class="btn btn-block btn-primary dropdown-toggle botao_onde" data-toggle="dropdown" href="#">
						    Quem está de plantão?
						    <span class="caret" style="margin:10px 0 0 10px;"></span>
					    </a>
					    <ul class="dropdown-menu">
					    	<?php
					    	
							$result_pc= mysqli_query($conexao1, "select * from pessoas
																	where pessoas.tipo_pessoa = '3'
																	and   pessoas.status_pessoa = '1'
																	and   pessoas.id_usuario = '". $_COOKIE["id_usuario"] ."'
																	order by pessoas.nome asc
																	") or die(mysqli_error());
							
							while ($rs_pc= mysqli_fetch_object($result_pc)) {
							?>
							<li>
								<a href="link.php?chamada=trocaPlantonista&amp;id_plantonista=<?=$rs_pc->id_pessoa; ?>" class="interno <? if ($IDENT_id_plantonista==$rs_pc->id_pessoa) echo 'atual'; ?>">
								<i class="icon-user-md "></i>&nbsp;&nbsp;
								<?=$rs_pc->nome; ?>
								</a>
								
								<? if ($rs_pc->id_clinica==$IDENT_id_clinica) { ?>
								<a data-original-title="Configurações de clínica" data-backdrop="static" data-toggle="modal" href="#modal_clinica_opcoes" class="edita_clinica"><i class="icon-gear"></i></a>
								<? } ?>
								</li>
							<?	
							}
							?>
					    </ul>
					    
					    
					</div> 
					<br/><br/>
					
				</div>
				
				<? /*
				<div class="span3">
					<label class="muted menor" for="vc_percentual_clinica">Percentual Instituição:</label>
					
					<div class="input-append">
						<input pattern="\d*" autocomplete="off" type="text" name="percentual_clinica" id="nc_percentual_clinica" value="" required="required" />
						<span class="add-on">%</span>
					</div>
				</div>
				*/ ?>
			</div>
			
		</div>
			
		
	</div>
	<? } ?>
	
	<? /* Tela de novo convênio que a pessoa atende (com typeahead) */ ?>
	<div id="modal_convenio_novo" class="modal hide fade condicao_<?=$modo_recebimento_convenios_pagos;?>_<?=$convenio_proprio;?>" tabindex="-1" role="dialog">
			
		<form id="modal_convenio_novo_form" action="<?=AJAX_FORM;?>novoConvenio" method="post">
			<input type="hidden" name="data" id="nc_data" value="" />
			<input type="hidden" name="id_convenio" id="nc_id_convenio" value="" />
			<input type="hidden" name="id_ato" id="nc_id_ato" value="" />
			<input type="hidden" name="a" id="nc_a" value="" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5 id="nc_modal_label">Novo convênio</h5>
			</div>
				
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span3">
						<label class="muted menor" for="vc_nome_exibicao_convenio">Nome do convênio:</label>
						
						<input class="input-block-level" type="text" name="nome_exibicao_convenio" id="nc_nome_exibicao_convenio" value="" required="required" />
					</div>
					<div class="span2">
						<label class="muted menor" for="vc_label_convenio">Rótulo:</label>
						
						<input class="input-block-level" type="text" name="label_convenio" id="vc_label_convenio" value="" required="required" />
					</div>
					<div class="span3 area_tipo_convenio">
						<label class="muted menor" for="nc_t">Tipo:</label>
						
						<select class="input-block-level" required="required" name="t" id="nc_t">
							<option class="tipo_convenio_v_1" value="1"><?=pega_tipo_convenio(1);?></option>
							<option selected="selected" class="tipo_convenio_v_2" value="2"><?=pega_tipo_convenio(2);?></option>
							<option class="tipo_convenio_v_3" value="3"><?=pega_tipo_convenio(3);?></option>
						</select>
					</div>
					<div class="span2">
						<label class="muted menor" for="nc_valor">Valor:</label>
						
						<div class="input-prepend">
							<span class="add-on">R$</span>
							<input autocomplete="off" type="text" name="valor" id="nc_valor" value="" required="required" />
						</div>
					</div>
					<div class="span2">
						<label class="muted menor" for="vc_percentual_clinica">% Instituição:</label>
						
						<div class="input-append">
							<input pattern="\d*" autocomplete="off" type="text" name="percentual_clinica" id="nc_percentual_clinica" value="" required="required" />
							<span class="add-on">%</span>
						</div>
					</div>
				</div>
				
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
		</form>
	</div>
	
	<? /* Modal para por a senha e fechar o dia */ ?>
	<div id="modal_fecha_dia" class="modal hide fade" tabindex="-1" role="dialog">
			
		<form id="modal_fecha_dia_form" action="<?=AJAX_FORM;?>terminarLancamento" method="post">
			<input type="hidden" name="terminado" id="fd_terminado" value="" />
			<input type="hidden" name="id_clinica" id="fd_id_clinica" value="" />
			<input type="hidden" name="data" id="fd_data" value="<?=formata_data_hifen($data);?>" />
			<input type="hidden" name="data_formatada" id="fd_data_formatada" value="<?=$data;?>" />
			<input type="hidden" name="identifica_atendimentos" id="fd_identifica_atendimentos" value="" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5 id="nc_modal_label">Caixa</h5>
			</div>
			<div class="modal-body">
				<p class="muted">Feche o caixa para travar lançamentos. Um cadeado aparecerá ao lado deste dia no Calendário.</p>
				<br/>
				
				<div class="row-fluid">
					<div class="span6 text-right" style="line-height:1.5;">
						Entre com sua senha:
					</div>
					<div class="span6">
						<input placeholder="Senha" type="password" name="senha3" id="senha3" value="" required="required" />
					</div>
				</div>
				
			</div>
			<br/>
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
		</form>
	</div>
	
	<? //if ($identifica_atendimentos!='1') { ?>
	
		<? /* Configurações da clínica atual */ ?>
		<div id="modal_plantao_hora" class="modal hide fade" tabindex="-1" role="dialog">
			
			<form id="modal_plantao_hora_form" action="<?=AJAX_FORM;?>formPlantaoHoraEdita" method="post">
				
				
				
			</form>
	
		</div>
	
	<? //} ?>
	
	<? if ($identifica_atendimentos!='1') { ?>
	
		<script type="text/javascript" src="includes/js/atendimento.js?s=1"></script>
		
		<? /* --- Pesquisa de paciente avulsa */ ?>
		<div id="modal_pacientes" class="modal hide fade" tabindex="-1" role="dialog">
			
			<input type="hidden" id="cadastro_rapido" value="0" />
						
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5>Acessar Prontuários</h5>
			</div>
				
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span12 span_typeahead" style="margin-top:5px;">
						<input autocomplete="off" type="text" class="input-block-level nome_paciente" id="nome_paciente_pesquisa" name="nome_paciente" placeholder="Nome do paciente" />
					</div>
				</div>
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Fechar</button>
			</div>
		</div>
		
		<? /* --- Cadastro e edição de pacientes */ ?>
		<div id="modal_paciente" class="modal hide fade" tabindex="-1" role="dialog">
				
			<form id="modal_paciente_form" action="<?=AJAX_FORM;?>formCadastroPaciente" method="post">
				
				<input type="hidden" name="acao" id="acao" value="i" />
				<input type="hidden" name="id_paciente" id="edita_id_paciente" value="" />
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">x</button>
					<h5>Cadastrar paciente</h5>
				</div>
				
				<div class="modal-body">
					<div class="row-fluid">
						<div class="span12">
							<input type="text" class="input-block-level" name="nome" id="paciente_nome" placeholder="Nome do paciente" required="required" />
						</div>
					</div>
					
					<div class="row-fluid">
						<div class="span6">
							<input type="text" class="input-block-level" maxlength="10" placeholder="Data de nascimento" name="data_nasc" id="paciente_data_nasc" />
						</div>
						<div class="span6 div_sexo">
							<div class="row-fluid" style="margin:8px 0 0 0;">
								<div class="span6">
									<label class="radio" style="padding-left:45px;">
										<input type="radio" name="sexo" id="paciente_sexo_m" value="m" />
										<small>Masculino</small>
									</label>
								</div>
								<div class="span6">
									<label class="radio" style="padding-left:25px;">
										<input type="radio" name="sexo" id="paciente_sexo_f" value="f" />
										<small>Feminino</small>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<input type="text" class="input-block-level" placeholder="Telefone residencial" name="telefone" id="paciente_telefone" />
							<input type="text" class="input-block-level" placeholder="Telefone celular" name="telefone2" id="paciente_telefone2" />
						</div>
						<div class="span6">
							<input type="text" class="input-block-level" name="cpf" id="paciente_cpf" maxlength="14" placeholder="CPF" />
							<input type="email" class="input-block-level" name="email" placeholder="E-mail" id="paciente_email" />
						</div>
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
				</div>
			</form>
		</div>
		
		<? /* Lista histórico de uma pessoa (em desuso) */ ?>
		<div id="modal_historico" class="modal hide fade" tabindex="-1" role="dialog">
		
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5>Histórico</h5>
			</div>
				
			<div class="modal-body">
				<div class="row-fluid">
					
					<div class="span12" id="historico_conteudo" style="padding-bottom:20px;">
						
					</div>
					
				</div>
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Fechar</button>
			</div>
		</div>
		
		<? /* Lista atendimentos, por convênio/dia/ato ou por pessoa */ ?>
		<div id="modal_atendimentos" class="modal hide fade" tabindex="-1" role="dialog">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5>Atendimentos</h5>
			</div>
			
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span12" id="atendimentos">
					    
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Fechar</button>
			</div>
	
		</div>
		
		<? /* Tela de atendimento da consulta */ ?>
		<div id="modal_atendimento" class="modal hide fade modal_atendimento_<?=$identifica_atendimentos;?>" tabindex="-1" role="dialog">
				
			<form id="modal_atendimento_form" action="link.php?chamada=atualiza" method="post">
				
				<input type="hidden" id="editado_infos" name="editado_infos" value="" />
				<input type="hidden" id="origem" name="origem" value="" />
				<input type="hidden" id="paciente_atendimento_id" name="paciente_atendimento_id" value="" />
				<input type="hidden" id="edicao" name="edicao" value="" />
				<input type="hidden" id="paciente_modo" name="modo" value="1" />
				<input type="hidden" id="paciente_data" name="data" value="" />
				<input type="hidden" id="paciente_hora" name="hora" value="" />
				<input type="hidden" id="campo_i" name="campo_i" value="" />
				<input type="hidden" id="campo_t" name="campo_t" value="" />
				<input type="hidden" id="paciente_id_clinica" name="id_clinica" value="" />
				<input type="hidden" id="id_ato" name="id_ato" value="" />
				<input type="hidden" id="id_convenio" name="id_convenio" value="" />
				<input type="hidden" id="nome_convenio" name="nome_convenio" value="" />
	        	<input type="hidden" id="tipo_convenio" name="tipo_convenio" value="" />
	        	<input type="hidden" id="paciente_modo_recebimento_convenios_pagos" name="modo_recebimento_convenios_pagos" value="" />
	        	<input type="hidden" id="recebimento" name="recebimento" value="" />
	        	<input type="hidden" id="valor" name="valor" value="" />
	        	<input type="hidden" id="percentual_clinica" name="percentual_clinica" value="" />
	        	<input type="hidden" id="percentual_medico" name="percentual_medico" value="" />
	        	<input type="hidden" id="ordem" name="ordem" value="" />
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">x</button>
					<h5>Atendimento</h5>
				</div>
					
				<div class="modal-body">
					<div class="row-fluid">	
						<div class="span6 muted" style="font-size:13px;line-height:19px;">
							<div class="pull-right">
								<a class="btn btn-mini btn-inverse ver_historico hide" rel="">histórico</a>
							</div>
							<div id="atendimento_paciente_nome">
							
							</div>
							
							<div id="atendimento_paciente_data_nasc">
							
							</div>
						</div>
						
						<div class="span6 text-right"  style="color:#999; font-size:13px;line-height:19px; margin:0 0 10px 0;">
							<span class="novo_ato">Ato</span> &raquo; <small><span class="novo_nome_convenio">Convênio</span></small> <br/><small class="novo_valor_convenio"></small>
						</div>
						
					</div>
					
					<div class="row-fluid">
						
						<div class="span12 span_typeahead" style="margin-top:5px;">
							<input autocomplete="off"  onkeypress="return checaRetorno(event);" type="text" class="input-block-level nome_paciente" id="nome_paciente" name="nome_paciente" placeholder="Nome do paciente" />
						</div>
						
						<input type="hidden" name="id_paciente" id="atendimento_paciente_id" />
						
						<div id="atendimento_paciente" class="hide">
							<div class="row-fluid" style="display:none;">
								<div class="span4">
									<span style="color:#999; font-size:11px; " id="atendimento_paciente_nome" class="muted"></span>
								</div>
								
								<div class="span4">
									<span style="color:#999; font-size:11px; " id="atendimento_paciente_data_nasc" class="menor muted"></span>
								</div>
								
								<div class="span4">
									<? /*<button type="button" class="btn-mini btn-primary ver_historico" rel="">Ver histórico</button>*/ ?>
								</div>
							</div>
							
							<div class="row-fluid">
								<div class="span12 div_tipo_atendimento" style="margin-top:8px; margin-left:0;">
								    <div class="btn-group btn-tipo_atendimento" data-toggle="buttons-radio">
									    <button type="button" class="btn btn-mini btn-mini-tipo_atendimento btn-mini-tipo_atendimento-1" data-value="1">Consulta</button>
									    <button type="button" class="btn btn-mini btn-mini-tipo_atendimento btn-mini-tipo_atendimento-2" data-value="2">Retorno</button>
									</div>
									
									<input type="hidden" name="tipo_atendimento" id="tipo_atendimento" value="" />
								</div>
								
								<div class="span12" style="margin-top:10px; margin-left:0;">
									<label class="muted menor">Descrição:</label>
									
									<textarea class="input-block-level" name="anamnese" id="anamnese" placeholder=""></textarea>
								</div>
							</div>
						</div>
						
					</div>
					
					<br />
				</div>
					
				<div class="modal-footer hidden-phone">
					<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary cadastrar hide" data-loading-text="OK">OK</button>
				</div>
			</form>
		</div>
	<? } ?>
	
	<?
	//nenhuma clínica cadastrada, redirecionada para a tela de boas-vindas
	if ( ($IDENT_id_clinica=='') && ($_SESSION["emula_id_usuario"]=='') ) {
		echo '
		<script>
			window.top.location.href="./?pagina=acesso/trabalho_clinicas";
		</script>
		';
	}
	else {
		if ($IDENT_id_pessoa!='') {
			
			$result_pc= mysqli_query($conexao1, "select * from pessoas_clinicas
									where id_pessoa = '". $IDENT_id_pessoa ."'
									and   status_pc = '1'
									") or die(mysqli_error());
			$num_pc= mysqli_num_rows($result_pc);
		
		}
	?>
	
	<div class="xpage-header">
		
		<? if ($_SESSION["emula_id_usuario"]!='') { ?>
		<div class="row-fluid">
			<div class="span12">
				<a href="link.php?chamada=cancelaEmulacao" style="display:block;" class="interno btn btn-danger">Emulando <strong><?=$IDENT_nome;?></strong>. Cancelar.</a>
				
				<br />
			</div>
		</div>
		<? } ?>
		<? if ($IDENT_id_clinica=='') { ?>
		<br />
		<center>Usuário não cadastrou nenhum local de trabalho.</center>
		<? } else { ?>
		
		<link media="screen" href="includes/bootstro.js/bootstro.css" rel="stylesheet" />
		<script language="javascript" type="text/javascript" src="includes/bootstro.js/bootstro.js"></script>
		<script>
			$(document).ready(function(){
				
				bootstro.set_bootstrap_version(2);
				
				$(document).on('click', '.tour', function(event){
	         		
	         		var tempo= $('.stopwatch_geral').val();
	         		
	         		bootstro.start(".bootstro", {
			            onComplete : function(params)
			            {
			                
			                var tempo= $('.stopwatch_geral').val();
			                
			                mixpanel.track("Terminou Tour", {
								"Tempo": ""+tempo+""
							});
							
			                //alert("Reached end of introduction with total " + (params.idx + 1)+ " slides");
			            },
			            onExit : function(params)
			            {
			                
			                var tempo= $('.stopwatch_geral').val();
			                
			                mixpanel.track("Fechou Tour", {
								"Tempo": ""+tempo+""
							});
			                
			                //alert("Introduction stopped at slide #" + (params.idx + 1));
			            },
			            onStep:  function(params) {
				            
				            var tempo= $('.stopwatch_geral').val();
				            
				            mixpanel.track("Avançou no Tour", {
								"Slide": ""+(params.idx + 1)+"",
								"Tempo": ""+tempo+""
							});
				            
				            //passou para algum slide
				            //alert("Introduction stopped at slide #" + (params.idx + 1)); 
			            },
			            finishButtonText: 'Fechar tour &nbsp',
			            nextButton: '<button style="padding-top:15px;padding-bottom;15px;" class="btn btn-success btn-mini bootstro-next-btn">Próximo &raquo;</button>',
			            prevButton: '<button style="padding-top:15px;padding-bottom;15px;" class="btn btn-info btn-mini bootstro-prev-btn">&laquo; Anterior</button>',
			        }); 

	        	});
				
			});
			
			
		</script>
		
		<? /*if ($_COOKIE["perfil"]=="2") { ?>
		<div class="row-fluid">
			<div class="span12 text-center">
				<a class="btn btn-info tour" href="javascript:void(0);">Dicas</a>
			</div>
		</div>
		<? } */ ?>
		
		<? /*
		<div class="row-fluid">
			<div class="span10 text-center">
				<div class="ato <? if ($_GET[inst]!='1') echo 'fechado'; ?>">
					
					<div style="margin:0 auto 30px auto;width:700px;height:350px;background:#ccc;">
					
					</div>
				</div>
			</div>
			<div class="span2">
				<a class="minimiza minimiza_video" href="javascript:void(0);"><i style="margin-top:4px;" class="icon icon-chevron-down"></i> <small><span class="lll"><? if ($_GET[inst]!='1') echo 'Mostrar'; else echo 'Esconder'; ?></span> instrução</small></a>
			</div>
			<br />
		</div>	
		*/ ?>	
		
		<? } ?>
	</div>
	
	<? if ($IDENT_id_clinica!='') { ?>
	<div class="row-fluid">
		<div class="span4 calendario_container">
			
			<small class="label_locais muted">Local de trabalho:</small>
			
			<div class="btn-group btn-block">
			    	
			    <a
			    
			    data-bootstro-step="0"
			  	data-bootstro-placement="bottom"
			  	data-bootstro-width="450px"
			  	data-bootstro-content="<strong>Instruções rápidas:</strong><br/><br/> Edite a lista com os locais que trabalha e configure alguns ajustes."
			  	
			    class="bootstro btn  btn-block btn-primary dropdown-toggle botao_onde" data-toggle="dropdown" href="#">
				    <i class="icon-building "></i>&nbsp;&nbsp;
				    <?= $clinica_nome; ?>
				    <span class="caret" style="margin:10px 0 0 10px;"></span>
			    </a>
			    <ul class="dropdown-menu">
			    	<?php
			    	
					$result_pc= mysqli_query($conexao1, "select * from pessoas_clinicas, clinicas
											where pessoas_clinicas.id_pessoa = '". $IDENT_id_pessoa ."'
											and   pessoas_clinicas.id_clinica = clinicas.id_clinica
											and   pessoas_clinicas.status_pc = '1'
											order by clinicas.clinica asc
											") or die(mysqli_error());
					
					while ($rs_pc= mysqli_fetch_object($result_pc)) {
						if ($rs_pc->nome_exibicao_clinica!='') $retorno= $rs_pc->nome_exibicao_clinica;
						else $retorno= $rs_pc->clinica;
					?>
					<li>
						<a href="link.php?chamada=trocaClinica&amp;id_clinica=<?=$rs_pc->id_clinica; ?>" class="interno <? if ($IDENT_id_clinica==$rs_pc->id_clinica) echo 'atual'; ?>">
						<i class="icon-building "></i>&nbsp;&nbsp;
						<?=$retorno; ?>
						</a>
						
						<? if ($rs_pc->id_clinica==$IDENT_id_clinica) { ?>
						<a data-original-title="Configurações de clínica" data-backdrop="static" data-toggle="modal" href="#modal_clinica_opcoes" class="edita_clinica"><i class="icon-gear"></i></a>
						<? } ?>
						</li>
					<?	
					}
					?>
					<li class="divider"></li>
					<li> <a href="#modal_clinica" data-toggle="modal" data-backdrop="static" class="nova_clinica"><i class="icon icon-plus"></i> Novo local de trabalho</a></li>
			    </ul>
			</div>    
		    
		    <br /><br />
		    
		    <small class="label_locais muted">Dia:</small>
		    
		    <div class="clinica_grupo">
		    <a
		    <? /*
		    datax-bootstro-step="1"
				  	data-bootstro-placement="bottom"
				  	data-bootstro-width="450px"
				  	data-bootstro-content="Navegue no calendário para ver lançamentos de outras datas."
		  	*/ ?>
		    class="xbootstro btn btn-larg btn-primary btn-block botao_quando" data-toggle="collapse" href="#calendario"
		    
		    >
			    <i class="icon-calendar "></i> &nbsp;&nbsp;<?= data_param(formata_data_hifen($data)); ?>
			    <span class="caret" style="margin:10px 0 0 10px;"></span>
		    </a>
		    </div>
		    
		    <div id="calendario" class="collapse" style="height:0;">
				<?= desenha_calendario($data_inicio, $IDENT_id_pessoa, $IDENT_id_clinica, $data); ?>
			</div>
			
			<? if ($_COOKIE["perfil"]=="3") { ?>
		    <br/>
		    
		    <small class="label_locais muted">Plantonista:</small>
			
			<div class="btn-group btn-block">
			    	
			    <a
			    
			    class="btn  btn-block btn-primary dropdown-toggle botao_onde" data-toggle="dropdown" href="#">
				    <i class="icon-user-md "></i>&nbsp;&nbsp;
				    <?= $plantonista_nome; ?>
				    <span class="caret" style="margin:10px 0 0 10px;"></span>
			    </a>
			    <ul class="dropdown-menu">
			    	<?php
			    	
					$result_pc= mysqli_query($conexao1, "select * from pessoas
															where pessoas.tipo_pessoa = '3'
															and   pessoas.status_pessoa = '1'
															and   pessoas.id_usuario = '". $_COOKIE["id_usuario"] ."'
															order by pessoas.nome asc
															") or die(mysqli_error());
					
					while ($rs_pc= mysqli_fetch_object($result_pc)) {
					?>
					<li>
						<a href="link.php?chamada=trocaPlantonista&amp;id_plantonista=<?=$rs_pc->id_pessoa; ?>" class="interno <? if ($IDENT_id_plantonista==$rs_pc->id_pessoa) echo 'atual'; ?>">
						<i class="icon-user-md "></i>&nbsp;&nbsp;
						<?=$rs_pc->nome; ?>
						</a>
						
						<? if ($rs_pc->id_clinica==$IDENT_id_clinica) { ?>
						<a data-original-title="Configurações de clínica" data-backdrop="static" data-toggle="modal" href="#modal_clinica_opcoes" class="edita_clinica"><i class="icon-gear"></i></a>
						<? } ?>
						</li>
					<?	
					}
					?>
			    </ul>
			</div>    
		    
		    <br /><br />
		    <? } ?>
		    
		    
		    <?
		    if ( ($plantonista=='1') && ($pagina=="lancamento/lancamento") ) {
				
				$str_ontem= "";
				
				$hora= date("H");
				
				if ($hora<8) {
					$str_ontem= " or vale_dia = '". formata_data(soma_data($data, -1, 0, 0)) ."' ";
				}
				
				if ($_COOKIE["perfil"]=="3") $str_add= " and   id_plantonista = '". $_COOKIE["id_plantonista"] ."' ";
				
				$result_plantao_entrada= mysqli_query($conexao1, "select *
															from pessoas_clinicas_plantoes
															where id_pessoa = '". $IDENT_id_pessoa ."'
															and   id_clinica = '". $IDENT_id_clinica ."'
															and   ( vale_dia = '". formata_data($data) ."' ". $str_ontem ." )
															and   tipo_batida = '1'
															and   status_batida = '1'
															". $str_add ."
															");    
				$num_plantao_entrada= mysqli_num_rows($result_plantao_entrada);
				$rs_plantao_entrada= mysqli_fetch_object($result_plantao_entrada);
				
				$txt_bt_entrar= "Entrar";
				
				if ($num_plantao_entrada>0) {
					$txt_bt_entrar= substr($rs_plantao_entrada->hora, 0, 5);
					
					$classe_duracao= 'te';
				}
				
				$result_plantao_saida= mysqli_query($conexao1, "select * from pessoas_clinicas_plantoes
															where id_pessoa = '". $IDENT_id_pessoa ."'
															and   id_clinica = '". $IDENT_id_clinica ."'
															and   ( vale_dia = '". formata_data($data) ."' ". $str_ontem ." )

															and   tipo_batida = '2'
															and   status_batida = '1'
															". $str_add ."
															");    
				$num_plantao_saida= mysqli_num_rows($result_plantao_saida);
				$rs_plantao_saida= mysqli_fetch_object($result_plantao_saida);
				
				$txt_bt_sair= "Sair";
				
				if ($num_plantao_saida>0) {
					$classe_duracao.= ' ts';
					
					$txt_bt_sair= substr($rs_plantao_saida->hora, 0, 5);
					
					$data_hora_saida= $rs_plantao_saida->data .' '. $rs_plantao_saida->hora;
					$data_hora_entrada= $rs_plantao_entrada->data .' '. $rs_plantao_entrada->hora;
					
					$data_hora_saida_mk= faz_mk_data_completa($data_hora_saida);
					$data_hora_entrada_mk= faz_mk_data_completa($data_hora_entrada);
					
					$data_hora_saida_mk  = new DateTime($data_hora_saida);
					$data_hora_entrada_mk = new DateTime($data_hora_entrada);
					
					$diferenca= $data_hora_saida_mk->diff($data_hora_entrada_mk);
					
					$duracao_contador= formata_saida($diferenca->h, 2) .':'. formata_saida($diferenca->i, 2) .':'. formata_saida($diferenca->s, 2);
					
					$finalizado="finalizado.";
				}
					else $finalizado=":";
		    ?>
		    
		    <?
		    if ( ($num_plantao_entrada>0) && ($num_plantao_saida==0) ) {
				$ano= substr($rs_plantao_entrada->data, 0, 4);
				$mes= substr($rs_plantao_entrada->data, 5, 2);
				$dia= substr($rs_plantao_entrada->data, 8, 2);
				
				$hora= substr($rs_plantao_entrada->hora, 0, 2);
				$minuto= substr($rs_plantao_entrada->hora, 3, 2);
				$segundo= substr($rs_plantao_entrada->hora, 6, 2);
				
				$mes--;
		    ?>
		    
		    <script type="text/javascript">
		    
			    $(function () {
				    
				    var desde= new Date();
				    var desde= new Date(<?=(int)$ano;?>, <?=(int)$mes;?>, <?=(int)$dia;?>, <?=(int)$hora;?>, <?=(int)$minuto;?>, <?=(int)$segundo;?>);
					
					//var austDay = new Date();
					//austDay = new Date(2014, 1, 1);
									
					$('.duracao .duracao_contador').countdown({
						since: desde,
						compact: true,
						format: 'HMS'
					});
					
					$('.duracao').show();
					
					
				});

		    </script>
		    <? } ?>
		    <br />
		    
		    
		    <small class="label_locais muted">Plantão <?=$finalizado;?></small>
		    
	        <div class="btn-group btn-group-block">
		    	<button data-perfil="<?=$IDENT_perfil;?>" data-id_pcp="<?=$rs_plantao_entrada->id_pcp;?>" data-existe="<?=$num_plantao_entrada;?>" data-tipo_batida="1" data-vale_dia="<?=$data;?>" data-clinica="<?=$nome_clinica;?>" class="<? if ($num_plantao_entrada=="1") echo ''; else echo 'btn-success'; ?> btn btn-large btn_plantao btn_entrar">
		    		<i class="icon-white icon-arrow-down"></i>
		    		
		    		&nbsp; <span><?=$txt_bt_entrar;?></span>
		    	</button>
		    	
				<button data-perfil="<?=$IDENT_perfil;?>" data-id_pcp="<?=$rs_plantao_saida->id_pcp;?>" data-existe="<?=$num_plantao_saida;?>" data-tipo_batida="2" data-vale_dia="<?=$data;?>" data-clinica="<?=$nome_clinica;?>" class="<? if ($num_plantao_saida=="1") echo ''; else echo 'btn-success'; ?> btn btn-large btn_plantao btn_sair">
					<i class="icon-white icon-arrow-up"></i>
					
					&nbsp; <span><?=$txt_bt_sair;?></span>
					
				</button>
		    </div>
		    <br/>
		    
		    <? /*<small class="label_locais text-center muted">Duração:</small>*/ ?>
		    <big class="duracao text-center <?=$classe_duracao;?>"><span class="duracao_contador"><?=$duracao_contador;?></span></big>
			    
		    <? } ?>
		    
		    <? if ( ($identifica_atendimentos!='1') && ($pagina=="lancamento/lancamento") ) { ?>
			<br  class="clearfix" />
			
			<small class="label_locais muted">Histórico:</small>
			<a style="text-align:left;" class="btn btn-block btn-info btn_m_pac" data-toggle="modal" data-backdrop="static" href="#modal_pacientes">
				<i class="icon-user "></i>&nbsp; Acessar Cadastros <i class="icon-caret-right "></i>
				
				</a>
			<? } ?>
			
		    <div id="sidebar_container">
				
				<br style="clear:both;" />
				
				<?php
				/*$result_anotacoes= mysqli_query($conexao1, "select * from anotacoes
												where id_pessoa = '". $IDENT_id_pessoa ."'
												and   id_clinica = '". $IDENT_id_clinica ."'
												and   data = '". formata_data($data) ."'
												") or die(mysqli_error());
				$rs_anotacoes= mysqli_fetch_object($result_anotacoes);
				?>
				
				<div class="accordion" id="accordion_anotacoes">
					<div class="accordion-group" id="anotacao_1">
						<div class="accordion-heading">
					    	<a class="accordion-toggle" data-data="<?=($data);?>" data-toggle="collapse" data-parent="#accordion_anotacoes" href="#accordion_anotacoes_anotacao">
					        	Anotações de hoje
							</a>
					    </div>
					    <div id="accordion_anotacoes_anotacao" class="accordion-body collapse out">
					    	<div class="accordion-inner">
					        	<textarea data-data="<?=($data);?>" rows="8" name="anotacao" id="anotacao" class="input-block-level"><?= mostra($rs_anotacoes->anotacao); ?></textarea>
							</div>
					    </div>
					</div>
				</div>
				*/ ?>
				
				<div id="resultado" <? /*data-spy="affix" data-offset-top="200" */ ?>>
					<br />
				</div>
				
				<? /*if ($identifica_atendimentos!='1') { ?>
				<div class="relatorio_diario" style="margin-bottom:30px;">
			    	<a href="./?pagina=lancamento/relatorio_diario&amp;data=<?=$data;?>" class="btn btn-" style="display:block;">Relatório nominal diário &raquo;</a>
			    </div>
			    <? } */ ?>
			</div>
		</div>
		<div class="span8 lancamento_container">
			

			
			<a name="hoje"></a>
			
			<div class="silenciar_dv">
				<label class="checkbox">
				<input type="checkbox" id="silenciar_sons" value="1" <? if ($rs->silenciar_sons=='1') echo 'checked="checked"'; ?> />
				<small class="muted">Silenciar</small>
				</label>
			</div>
			
			<input type="hidden" name="data" id="data" value="<?=$data;?>" />
			<input type="hidden" name="id_clinica" id="id_clinica" value="<?=$IDENT_id_clinica;?>" />
			<input type="hidden" name="nome_clinica" id="nome_clinica" value="<?=$clinica_nome;?>" />
			<input type="hidden" name="plantonista" id="plantonista" value="<?=$plantonista;?>" />
			<input type="hidden" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos" value="<?=$modo_recebimento_convenios_pagos;?>" />
			<input type="hidden" name="identifica_atendimentos" id="identifica_atendimentos" value="<?=$identifica_atendimentos;?>" />
			<input type="hidden" name="perfil" id="perfil" value="<?=$_COOKIE["perfil"];?>" />
			
			<? /*
			<ul class="nav nav-tabs atos_1" id="atos">
				<?
				for ($a=0; $a<2; $a++) {
					$id_ato= $a+1;
				?>
				<li
				
				
				class="<? if ($id_ato==1) echo 'active'; ?>">
				
				<a
				
				<? if ($a==1) { ?>
				datax-bootstro-step="3"
			  	data-bootstro-placement="bottom"
			  	data-bootstro-width="500px"
			  	data-bootstro-content="Nesta aba você registra os procedimentos."
				<? } ?>
				
				data-posicao="1" data-id_ato="<?=$id_ato; ?>" id="link_ato_<?=$id_ato; ?>_1" class="<? if ($a==1) echo 'bootstro'; ?> link_ato_<?=$id_ato; ?>" data-toggle="tab" href="#ato_pai_<?=$id_ato; ?>"><h4><?=pega_ato($id_ato);?></h4></a>
				
				</li>
				<? } ?>
			</ul>
			*/ ?>

			<div id="inicio" class="hidden-desktop hidden-tablet"> 
			</div>
						
			<hr class="hidden-desktop hidden-tablet" />
			
			<label class="muted menor">Produção:</label>
			
			<div class="accordion condicao_<?=$modo_recebimento_convenios_pagos;?>_<?=$convenio_proprio;?>" id="accordion_procedimentos">
				<?
				$a=0;
				gera_tela_lancamento_procedimentos($data, $IDENT_id_pessoa, $IDENT_id_clinica, $a, $_SESSION["emula_id_usuario"]);
				?>
				
			</div> <!-- /#accordion_procedimentos -->
			<?
			//} //fim for
			?>
		
			<br><br>
			
			<?
			if ($_COOKIE["id_plantonista"]=='') {
				if ($_SESSION["emula_id_usuario"]=='') {
				
					$result_num= mysqli_query($conexao1, "select * from pessoas_clinicas_datas
												where id_pessoa = '". $IDENT_id_pessoa ."'
												and   id_clinica = '". $IDENT_id_clinica ."'
												and   data = '". formata_data($data) ."'
												and   terminado = '1'
												") or die(mysqli_error());
					$rs_num= mysqli_fetch_object($result_num);
					$num_num= mysqli_num_rows($result_num);
					
					if ($num_num==0) {
						$tit= "<i class='icon icon-lock '></i> &nbsp;Fechar o caixa";
						$loading= "Finalizando...";
						$a_classe= "btn-success";
					}
					else {
					?>
					<script>
						$('.lancamento_quantidade').attr('disabled', 'disabled');
						$('.btn-mais, .btn-zera').hide();
					</script>
					<?
						$tit= "<i class='icon-pencil '></i> &nbsp;Liberar para edição";
						$loading= "Liberando...";
						$a_classe= "btn-danger";
					}
					
					$loading= "Carregando...";
				
				?>
				
				<div class="row-fluid">
					<div class="span6">
						<button
						
						data-bootstro-step="5"
							  	data-bootstro-placement="top"
							  	data-bootstro-width="450px"
							  	data-bootstro-content="Ao final do dia, bloqueie alterações nos dados lançados e guarde as informações de forma segura para conferir posteriormente."
						
						style="margin-bottom:60px;" id="finalizar_lancamento" type="button" class="bootstro btn <?=$a_classe;?> btn-large pull-left" data-terminado="<?=$rs_num->terminado;?>" data-id_clinica="<?=$IDENT_id_clinica;?>" data-data="<?=$data?>" data-data_formatada="<?=formata_data_hifen($data);?>" data-loading-text="<?=$loading;?>"><?=$tit;?></button>
					</div>
					<div class="span6 text-right">
						<? /*<br/>
					    <p class="muted menor hidden-phone"> <?= data_extenso_param(formata_data_hifen($data)); ?></p>*/ ?>
					</div>
				</div>
			    <? } ?>
		    <? } ?>
		    
		    <div id="resultado_fim" style="display:none;"
		    class="bootstro"
		    
		    data-bootstro-step="4"
		  	data-bootstro-placement="<? if ($device=='smartphone') { ?>top<? } else { ?>bottom<? } ?>"
		  	data-bootstro-width="520px"
		  	data-bootstro-content="Aqui você acompanha sua produção do dia."
		  	
		    >
		    	<?
		    	$result_soma= mysqli_query($conexao1, "select   sum(recebido_valor_pessoa) as recebido_valor_pessoa,
													sum(recebido_valor_clinica) as recebido_valor_clinica,
													sum(vai_receber_valor_pessoa) as vai_receber_valor_pessoa,
													sum(vai_receber_valor_clinica) as vai_receber_valor_clinica,
													sum(pessoa_deve) as pessoa_deve,
													sum(clinica_deve) as clinica_deve,
													sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
													sum(por_direito_valor_clinica) as por_direito_valor_clinica
													from atendimentos_uni
													where data= '". formata_data_hifen($data) ."'
													and   id_clinica = '". $IDENT_id_clinica ."'
													and   id_medico = '". $IDENT_id_pessoa ."'
													and   status_atendimento = '1'
													") or die(mysqli_error());
				$rs_soma= mysqli_fetch_object($result_soma);
				
				$result_soma_guias= mysqli_query($conexao1, "select   sum(recebido_valor_pessoa) as recebido_valor_pessoa,
													sum(recebido_valor_clinica) as recebido_valor_clinica,
													sum(vai_receber_valor_pessoa) as vai_receber_valor_pessoa,
													sum(vai_receber_valor_clinica) as vai_receber_valor_clinica,
													sum(pessoa_deve) as pessoa_deve,
													sum(clinica_deve) as clinica_deve,
													sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
													sum(por_direito_valor_clinica) as por_direito_valor_clinica
													from atendimentos_uni
													where data= '". formata_data_hifen($data) ."'
													and   id_clinica = '". $IDENT_id_clinica ."'
													and   id_medico = '". $IDENT_id_pessoa ."'
													and   tipo_convenio = '2'
													and   status_atendimento = '1'
													") or die(mysqli_error());
				$rs_soma_guias= mysqli_fetch_object($result_soma_guias);
		    	?>
		    	
		    	<div class="well_pai_medico">
			    	<div class="well well_medico"
				  	
			    	>
			    		<?
			    		if ($_COOKIE["perfil"]=="3") {
				    		$result_soma_acumulado= mysqli_query($conexao1, "select   sum(recebido_valor_pessoa) as recebido_valor_pessoa,
																sum(recebido_valor_clinica) as recebido_valor_clinica,
																sum(vai_receber_valor_pessoa) as vai_receber_valor_pessoa,
																sum(vai_receber_valor_clinica) as vai_receber_valor_clinica,
																sum(pessoa_deve) as pessoa_deve,
																sum(clinica_deve) as clinica_deve,
																sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
																sum(por_direito_valor_clinica) as por_direito_valor_clinica
																from atendimentos_uni
																where 1 = 1 /* *** */
																and   id_clinica = '". $IDENT_id_clinica ."'
																and   id_medico = '". $IDENT_id_pessoa ."'
																and   status_atendimento = '1'
																") or die(mysqli_error($conexao1));
							$rs_soma_acumulado= mysqli_fetch_object($result_soma_acumulado);
							
							$result_soma_acumulado2= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
															sum(por_direito_valor_clinica) as por_direito_valor_clinica,
															count(id) as total
															from atendimentos_uni
															where 1 = 1 /* *** */
															and   id_clinica = '". $IDENT_id_clinica ."'
															and   id_medico = '". $IDENT_id_pessoa ."'
															and   status_atendimento = '1'
															") or die(mysqli_error($conexao1));
							$rs_soma_acumulado2= mysqli_fetch_object($result_soma_acumulado2);
			    		?>
			    		<h5 class="tit_acumulado">Acumulado do período</h5>
			    		
			    		<div class="row-fluid">
							<div class="span12 span12_mobile">
								<p style="margin-bottom:0;" class="menor">Líquido:</p>
								<h5 class="rr_acumulado por_direito_valor_pessoa_acumulado">R$<?= fnum($rs_soma_acumulado->por_direito_valor_pessoa); ?></h5>
			    			</div>
			    		</div>
			    		
			    		<h5 class="" style="margin:20px 0 8px 0;"><span class="por_direito_valor_pessoa_acumulado_atendimentos"><?=fnumi($rs_soma_acumulado2->total); ?></span> atendimento(s)</h5>
			    		
			    		<hr style="margin:20px 0;"/>
			    		<? } ?>
			    		
				    	<? /*<h4 style="margin-bottom:10px;">Resumo de hoje</h4>*/ ?>
				    	
				    	<h5 class="tit_hoje">Hoje</h5>
				    	
				    	<p style="margin-bottom:0;" class="levar menor <? if ($modo_recebimento_convenios_pagos=="3") echo 'nao_mostra'; ?>">Levar em dinheiro:</p>
				    	<h3 class="levar rr recebido_valor_pessoa <? if ($modo_recebimento_convenios_pagos=="3") echo 'nao_mostra'; ?>">R$<?= fnum($rs_soma->recebido_valor_pessoa); ?></h3>
				    	
				    	<? /*
				    	<hr />
				    	
				    	<h5>A receber:</h5>
				    	<h4 class="rr producao_guias">R$<?= fnum($rs_soma_guias->por_direito_valor_pessoa+$rs_soma_guias->por_direito_valor_clinica); ?></h4>
				    	
				    	<hr />
				    	
				    	<h5>A receber da Unimed:</h5>
				    	<h4 class="rr vai_receber_valor_pessoa">R$<?= fnum($rs_soma->vai_receber_valor_pessoa); ?></h4>
				    	
				    	
				    	<!--
				    	<h4>Devendo para clínica:</h4>
				    	<h3 class="rr pessoa_deve">R$ <?= fnum($rs_soma->pessoa_deve); ?></h3>
				    	
				    	<hr />
				    	-->
				    	*/ ?>
				    	
				    	<br class=" <? if ($modo_recebimento_convenios_pagos=="3") echo 'nao_mostra'; ?>" />
				    	
				    	<div class="row-fluid">
			    			<div class="span6 span6_mobile">
			    				<p style="margin-bottom:0;" class="menor muted">Bruto:</p>
			    				<h5 class="muted rr por_direito_todos">R$<?= fnum($rs_soma->por_direito_valor_pessoa+$rs_soma->por_direito_valor_clinica); ?></h5>
			    			</div>
							<div class="span6 span6_mobile">
								<p style="margin-bottom:0;" class="menor">Líquido:</p>
								<h5 class="rr por_direito_valor_pessoa">R$<?= fnum($rs_soma->por_direito_valor_pessoa); ?></h5>
			    			</div>
			    		</div>
			    		
			    		<?
						$result_soma_dia= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
														sum(por_direito_valor_clinica) as por_direito_valor_clinica,
														count(id) as total
														from atendimentos_uni
														where data= '". formata_data_hifen($data) ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   id_medico = '". $IDENT_id_pessoa ."'
														and   status_atendimento = '1'
														") or die(mysqli_error());
						$rs_soma_dia= mysqli_fetch_object($result_soma_dia);
						
						$result_soma_dia1= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
														sum(por_direito_valor_clinica) as por_direito_valor_clinica,
														count(id) as total
														from atendimentos_uni
														where data= '". formata_data_hifen($data) ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   id_medico = '". $IDENT_id_pessoa ."'
														and   tipo_atendimento = '1'
														and   status_atendimento = '1'
														") or die(mysqli_error());
						$rs_soma_dia1= mysqli_fetch_object($result_soma_dia1);
						
						$result_soma_dia2= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
														sum(por_direito_valor_clinica) as por_direito_valor_clinica,
														count(id) as total
														from atendimentos_uni
														where data= '". formata_data_hifen($data) ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   id_medico = '". $IDENT_id_pessoa ."'
														and   tipo_atendimento = '2'
														and   status_atendimento = '1'
														") or die(mysqli_error());
						$rs_soma_dia2= mysqli_fetch_object($result_soma_dia2);
						
						/*$result_qtde_soma_dia= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
														sum(por_direito_valor_clinica) as por_direito_valor_clinica
														from atendimentos_uni
														where data= '". formata_data_hifen($data) ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   id_medico = '". $IDENT_id_pessoa ."'
														") or die(mysqli_error());
						$num_qtde_soma_dia= mysqli_num_rows($result_qtde_soma_dia);*/
						?>
			    		
			    		<h5 class="" style="margin:20px 0 8px 0;"><a href="./?pagina=lancamento/relatorio_diario&amp;data=<?=$data;?>"><span class="bruto_dia_qtde"><?=fnumi($rs_soma_dia->total); ?></span> atendimento(s)</a></h5>
						
						<? if ($identifica_atendimentos=='3') { ?>
						<p class="muted menor"><span class="bruto_dia_qtde_consultas"><?=$rs_soma_dia1->total;?></span> atendimentos pagos e <span class="bruto_dia_qtde_retornos"><?=$rs_soma_dia2->total;?></span> retornos</p>
						<? } ?>
			    	</div>
			    	
			    	
			    	
			    	<?
			    	/*
			    	$mes_sql= substr(formata_data_hifen($data), 0, 7);
			    	$mes_str= traduz_mes((int)substr($mes_sql, 5, 2));
			    	$ano_str= substr($mes_sql, 0, 4);
			    	
					$result_soma_mes= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
													sum(por_direito_valor_clinica) as por_direito_valor_clinica,
													count(id) as total
													from atendimentos_uni
													where DATE_FORMAT(data, '%Y-%m') = '". $mes_sql ."'
													and   id_clinica = '". $IDENT_id_clinica ."'
													and   id_medico = '". $IDENT_id_pessoa ."'
													and   tipo_atendimento = '1'
													") or die(mysqli_error());
					$rs_soma_mes= mysqli_fetch_object($result_soma_mes);
			    	?>
			    	
			    	<div class="well">
			    		<div class="row-fluid">
			    			<div class="span8">
								<h4 style="margin-bottom:10px;">Total parcial do mês</h4>
			    			</div>
			    			<div class="span4">
			    				<small class="menor muted text-right" style="display:block;margin-top:10px;"><?= $mes_str .' de '. $ano_str; ?></small>
			    			</div>
			    		</div>
			    		
			    		<div class="row-fluid">
			    			<div class="span6">
			    				<p style="margin-bottom:0;" class="menor muted">Bruto:</p>
			    				<h4 class="rr por_direito_todos">R$<?= fnum($rs_soma_mes->por_direito_valor_pessoa+$rs_soma_mes->por_direito_valor_clinica); ?></h4>
			    			</div>
							<div class="span6">
								<p style="margin-bottom:0;" class="menor muted">Líquido:</p>
								<h4 class="rr por_direito_valor_pessoa">R$<?= fnum($rs_soma_mes->por_direito_valor_pessoa); ?></h4>
			    			</div>
			    		</div>
			    						    		
			    		<h5 class="" style="margin:20px 0 8px 0;"><span class="bruto_dia_qtde"><?=fnumi($rs_soma_mes->total); ?></span> atendimento(s)</h5>
						
			    	</div>
			    	*/ ?>
		    	</div>
		    </div>
		    
			<script>
				$(document).ready(function(){
					
					<? if (($_COOKIE["perfil"]=="3") && ($_COOKIE["id_plantonista"]=="")) { ?>
					$("#modal_plantonista").modal({backdrop:'static'});
					<? } ?>
					
					<? //if ($device=='smartphone') { ?>
					//$("#resultado_fim").appendTo("footer");
					<? //} else { ?>
					$("#resultado_fim").appendTo("#resultado");
					<? //} ?>
					
					$("#resultado_fim").fadeIn();
				});
			</script>
			
			<? if ($_GET[inst]=='1') { ?>
			<script type="text/javascript">
				setTimeout(function() {
					$('a.tour').trigger('click');
				}, 1000);
			</script>
			<? } ?>
		        
		</div>
		
	</div>
	<? } //fim ident_id_clinica ?>
	<? } ?>
<? } ?>