<?php

function slack($message, $room) {
    $room = ($room) ? $room : "engineering";
    $data = "payload=" . json_encode(array(
            "channel"       =>  "#{$room}",
            "username"       =>  "ttz",
            "text"          =>  $message,
            "icon_url"    =>  "https://app.ttz.med.br/images/152.png"
        ));
	if ($room=="ttz-timeline") $token="B068T0JMR/eGbcEbjvqixcaKzV1XXn82tk";
	else $token="";
	
// You can get your webhook endpoint from your Slack settings
    $ch = curl_init("https://hooks.slack.com/services/T054YJSHG/".$token);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

// Laravel-specific log writing method
    // Log::info("Sent to Slack: " . $message, array('context' => 'Notifications'));

    return $result;
}

function prepara($str) {
	
	$str= trim($str);
	$str= mysqli_real_escape_string($GLOBALS["conexao1"], $str);
	
	return($str);
}

function mostra($str) {
	
	$str= stripslashes($str);
	
	return($str);
}

function insere_convenios_padrao($id_pessoa, $id_clinica) {
	
	
	$result_insere= mysqli_query($GLOBALS["conexao1"], "insert into pessoas_clinicas_convenios
    											(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual, nome_exibicao_procedimento)
    											values
    											('". $id_pessoa ."', '". $id_clinica ."', '1', '-1', '0', '0', '0', 'Consultas')
    											");
    											
	$result_convenios= mysqli_query($GLOBALS["conexao1"], "select * from convenios, convenios_atos
									where convenios.padrao = '1'
									and   convenios.id_convenio = convenios_atos.id_convenio
									");
	while ($rs_convenios= mysqli_fetch_object($result_convenios)) {
		
		$result4= mysqli_query($GLOBALS["conexao1"], "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $id_pessoa ."', '". $id_clinica ."', '1',
									'". $rs_convenios->id_convenio ."', '". $rs_convenios->valor ."', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
		
	}
	
	/*
	$result5= mysqli_query($GLOBALS["conexao1"], "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $id_pessoa ."', '". $id_clinica ."', '2',
									'3', '100.00', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
									*/
	
}

function envia_email($para_email, $para_nome, $titulo, $conteudo, $categoria) {
	
	if (AMBIENTE!="3") {
		$para_email= array("jaisonn@gmail.com");
	}
	
	/* -------------- SENDGRID --------------- */
	require("includes/sendgrid-php/sendgrid-php.php");
	
	$sendgrid = new SendGrid(SENDGRID_USERNAME, SENDGRID_PASSWORD, array("turn_off_ssl_verification" => true));
	
	$email = new SendGrid\Email();
	$email->setTos($para_email)->
	       setFrom('notificacao@ttz.med.br')->
	       setFromName('ttz')->
	       setReplyTo('contato@ttz.med.br')->
	       setSubject($titulo)->
	       //setText('Hello World sem HTML!')->
	       setHtml($conteudo ."
	       
	      ".EMAIL_RODAPE."
	       
	      ")->
	       addCategory($categoria);
	
	$retorno= $sendgrid->send($email);
	
	return($retorno);
	
	/* -------------- SENDGRID --------------- */
	
}

function pega_ultima_alteracao($id_medico, $id_clinica, $id_convenio, $id_ato, $data, $ordem) {
	
	$result_at= mysqli_query($GLOBALS["conexao1"], "select data, hora from atendimentos_uni
								where id_medico = '". $id_medico ."'
								and   id_clinica = '". $id_clinica ."'
								and   id_convenio = '". $id_convenio ."'
								and   id_ato = '". $id_ato ."'
								and   data = '". $data ."'
								and   ordem = '". $ordem."'
								and   status_atendimento = '1'
								order by hora desc limit 1
								") or die(mysqli_error());
	$rs_at= mysqli_fetch_object($result_at);
	
	return ($rs_at->hora);
	
}

function gera_tela_lancamento_procedimentos($data, $IDENT_id_pessoa, $IDENT_id_clinica, $a, $emula_id_usuario) {
	
	$result_atos_filhos= mysqli_query($GLOBALS["conexao1"], "select distinct(id_ato), pessoas_clinicas_convenios.nome_exibicao_procedimento
									from pessoas_clinicas_convenios
									where id_pessoa = '". $IDENT_id_pessoa ."'
									and   id_clinica = '". $IDENT_id_clinica ."'
									and   id_convenio = '-1'
									order by id_ato asc
									") or die(mysqli_error());
	$num_atos_filhos= mysqli_num_rows($result_atos_filhos);
	
	$f=0;
	while ($rs_atos_filhos= mysqli_fetch_object($result_atos_filhos)) {
		
		$descricao_cbhpm= pega_ato($rs_atos_filhos->id_ato);
		
		if ($rs_atos_filhos->nome_exibicao_procedimento!="") $nome_exibicao= $rs_atos_filhos->nome_exibicao_procedimento;
		else $nome_exibicao= $descricao_cbhpm;
	?>
	<div class="accordion-group ac_<? if ($f==0) echo 'in'; ?>" id="procedimento_<?= $rs_atos_filhos->id_ato;?>">
		<div class="accordion-heading">
	    	
	    	<a class="btn btn-mini btn-danger btn-exclui_procedimento" href="javascript:void(0);" data-id_procedimento="<?=$rs_atos_filhos->id_ato;?>" data-data="<?=$data;?>">
        		
        		<i class="icon icon-minus" style="margin-top:3px;"></i>
        		
    		</a>
    		
    		<a class="btn btn-mini btn-info btn-edita_procedimento" href="javascript:void(0);" data-id_procedimento="<?=$rs_atos_filhos->id_ato;?>" data-data="<?=$data;?>">
        		
        		<i class="icon icon-pencil"></i>
        		
    		</a>
    		
	    	<h5 class="tit_procedimento">
	    		
	    		<a class="accordion-toggle semu" data-toggle="collapse" data-parent="#accordion_procedimentos" href="#collapse_procedimento_<?= $rs_atos_filhos->id_ato;?>">
		    		<i class="icon icon-caret-<? if ($f==0) echo 'down'; else echo 'right'; ?> pull-left" style="margin-top:3px;margin-right:5px;"></i>
		    		
		        	<?= $nome_exibicao; ?>
		        	
		        	<i style="padding-left:20px;color:#fff;" class="pull-right icon-question-sign tt" data-placement="left" data-toggle="tooltip" title="<?='CBHPM: '. pega_cod_ato($rs_atos_filhos->id_ato) .' - '. $descricao_cbhpm;?>"></i>
		        	
				</a>
			</h5>
	    </div>
	    <div id="collapse_procedimento_<?= $rs_atos_filhos->id_ato;?>" class="accordion-body collapse <? if ($f==0) echo 'in'; else echo 'out'; ?>">
	    	<div class="accordion-inner">
	        	<? gera_tela_lancamento_ato($data, $IDENT_id_pessoa, $IDENT_id_clinica, $rs_atos_filhos->id_ato, $a, $emula_id_usuario); ?>
			</div>
	    </div>
	</div>
	<? $f++; } ?>
	<br style="clear:both;" />
	<div style="width:100%;height:10px;">
	</div>
	
	<button class="bootstro novo_procedimento btn btn-mini btn-success"
	
		data-bootstro-step="3"
	  	data-bootstro-placement="top"
	  	data-bootstro-width="450px"
	  	data-bootstro-content="<strong>Adicione novos procedimentos via CBHPM.</strong><br/><br/> E depois escolha por quais convênios atende e valor cobrado."
	
	>
		<i class="icon icon-white icon-plus" style="margin-top:3px;"></i>
		procedimento
	</button>
	
	<button class="btn btn-mini btn-primary alterar_procedimento">
		<i style="margin-top:2px;" class="icon icon-white icon-pencil"></i>
		&nbsp;<span class="lbl">edita lista</span>
	</button>
	<?
}

function gera_tela_lancamento_ato($data, $id_pessoa, $id_clinica, $id_ato, &$a, $id_usuario_emulado) {
	$k=0;
	$nome_ato= pega_ato($id_ato);
?>
	<input type="hidden" name="id_ato[<?=$a;?>]" value="<?=$id_ato;?>" />
							
	<?
	$i=0;
	
	$identifica_atendimentos= pega_identifica_atendimentos($id_pessoa, $id_clinica);
	
	$total_recebido_valor_pessoa=0;
	$total_recebido_valor_clinica=0;
	
	$total_pessoa_deve=0;
	$total_clinica_deve=0;
	
	$total_por_direito_valor_pessoa=0;
	$total_por_direito_valor_clinica=0;
	
	for ($y=1; $y<4; $y++) {
		
		$mostra=1;
		
		switch ($y) {
			case 1:
				$t=2;
			break;
			case 2:
				$t=1;
				
				if ($GLOBALS["modo_recebimento_convenios_pagos"]=="3") $mostra=0;
			break;
			case 3:
				//se marcar a opção que não atende via Convenio Particular, não mostra o bloco Unimed
				if ($GLOBALS["convenio_proprio"]=="0") $mostra=0;
				
				$t=3;
			break;
		}
	
		$j=0;
		
		$bruto[$t]=0;
		$liquido[$t]=0;
		
		$result_convenio= mysqli_query($GLOBALS["conexao1"], "select * from convenios, pessoas_clinicas_convenios
											where  convenios.id_convenio = pessoas_clinicas_convenios.id_convenio
											and   pessoas_clinicas_convenios.id_pessoa = '". $id_pessoa ."'
											and   pessoas_clinicas_convenios.id_clinica = '". $id_clinica ."'
											and   pessoas_clinicas_convenios.id_ato = '". $id_ato ."'
											and   convenios.tipo_convenio = '". $t ."'
											and   pessoas_clinicas_convenios.id_convenio <> '-1'
											order by convenio asc, valor asc
											") or die(mysqli_error());
		$linhas_convenio= mysqli_num_rows($result_convenio);
		
		if ( ($linhas_convenio>0) && ($mostra>0) ) {
		?>
	
		<div class="well well-<?=$t;?> well-lancamento well_bloco_<?=$id_ato;?>_<?=$a;?>">
			
			<h5 class="tit_convenio"><?= pega_tipo_convenio($t); ?></h5>
			
			<a class="minimiza minimiza_grupo" href="javascript:void(0);"><i class="icon icon-chevron-down"></i></a>
			
			<div id="ato_<?= $id_ato; ?>" class="ato">
				<? if ($linhas_convenio==0) { ?>
				<span class="muted"><em>&nbsp; (nenhum)</em></span>
				<? } ?>
				
				<table cellspacing="0" width="95%" class="table table-striped table-condensed table-lancamento">
				    <tbody>
						<?
						$c=0;
				        while ($rs_convenio= mysqli_fetch_object($result_convenio)) {
				        	
				        	if ($rs_convenio->nome_exibicao_convenio!='')
				        		$label_convenio= $rs_convenio->nome_exibicao_convenio;
				        	else
				        		$label_convenio= $rs_convenio->convenio;
				        	
				        	$percentual_clinica= ($rs_convenio->percentual);
				        	$percentual_medico= formata_valor(fnum(100-$percentual_clinica));
				        	
				        	$percentual_clinica= formata_valor(fnum($percentual_clinica));
				        	
				        	/*echo("select * from atendimentos
														where id_pessoa = '". $_COOKIE[id_pessoa] ."'
														and   id_clinica = '". $_COOKIE[id_clinica] ."'
														and   id_convenio = '". $rs_convenio->id_convenio ."'
														and   id_ato = '". $id_ato ."'
														and   data = '". formata_data($data) ."'
														and   ordem = '". $rs_convenio->ordem ."'
														");*/
							$result_count_todos= mysqli_query($GLOBALS["conexao1"], "select count(id) as total
					        							from atendimentos_uni
														where id_medico = '". $id_pessoa ."'
														and   id_clinica = '". $id_clinica ."'
														and   id_convenio = '". $rs_convenio->id_convenio ."'
														and   id_ato = '". $id_ato ."'
														and   data = '". formata_data($data) ."'
														and   ordem = '". $rs_convenio->ordem ."'
														and   status_atendimento = '1'
														") or die(mysqli_error());
							$rs_count_todos= mysqli_fetch_object($result_count_todos);
							$qtde_todos=$rs_count_todos->total;
							
							$result_count= mysqli_query($GLOBALS["conexao1"], "select count(id) as total
					        							from atendimentos_uni
														where id_medico = '". $id_pessoa ."'
														and   id_clinica = '". $id_clinica ."'
														and   id_convenio = '". $rs_convenio->id_convenio ."'
														and   id_ato = '". $id_ato ."'
														and   data = '". formata_data($data) ."'
														and   ordem = '". $rs_convenio->ordem ."'
														and   tipo_atendimento = '1'
														and   status_atendimento = '1'
														") or die(mysqli_error());
							$rs_count= mysqli_fetch_object($result_count);
							$num_count= $rs_count->total;
							
							//já tem dados cadastrados
							if ($num_count>0) {
								
								$result_at= mysqli_query($GLOBALS["conexao1"], "select sum(recebido_valor_pessoa) as recebido_valor_pessoa,
					        							sum(recebido_valor_clinica) as recebido_valor_clinica,
					        							sum(vai_receber_valor_pessoa) as vai_receber_valor_pessoa,
					        							sum(vai_receber_valor_clinica) as vai_receber_valor_clinica,
					        							sum(pessoa_deve) as pessoa_deve,
					        							sum(clinica_deve) as clinica_deve,
					        							
					        							sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
					        							sum(por_direito_valor_clinica) as por_direito_valor_clinica
					        							
					        							from atendimentos_uni
														where id_medico = '". $id_pessoa ."'
														and   id_clinica = '". $id_clinica ."'
														and   id_convenio = '". $rs_convenio->id_convenio ."'
														and   id_ato = '". $id_ato ."'
														and   data = '". formata_data($data) ."'
														and   ordem = '". $rs_convenio->ordem ."'
														and   tipo_atendimento = '1'
														and   status_atendimento = '1'
														") or die(mysqli_error());
														
								$rs_at= mysqli_fetch_object($result_at);
								$qtde= $num_count;//$rs_at->qtde;
								
								$recebido_valor_pessoa= 'R$ '. fnum($rs_at->recebido_valor_pessoa);
								$recebido_valor_clinica= 'R$ '. fnum($rs_at->recebido_valor_clinica);
								$total_recebido_valor_pessoa+=$rs_at->recebido_valor_pessoa;
								$total_recebido_valor_clinica+=$rs_at->recebido_valor_clinica;
								
								$vai_receber_valor_pessoa= 'R$ '. fnum($rs_at->vai_receber_valor_pessoa);
								$vai_receber_valor_clinica= 'R$ '. fnum($rs_at->vai_receber_valor_clinica);
								$total_vai_receber_valor_pessoa+=$rs_at->vai_receber_valor_pessoa;
								$total_vai_receber_valor_clinica+=$rs_at->vai_receber_valor_clinica;
								
								$pessoa_deve= 'R$ '. fnum($rs_at->pessoa_deve);
								$clinica_deve= 'R$ '. fnum($rs_at->clinica_deve);
								$total_pessoa_deve+=$rs_at->pessoa_deve;
								$total_clinica_deve+=$rs_at->clinica_deve;
								
								$por_direito_valor_pessoa= 'R$ '. fnum($rs_at->por_direito_valor_pessoa);
								$por_direito_valor_clinica= 'R$ '. fnum($rs_at->por_direito_valor_clinica);
								$total_por_direito_valor_pessoa+=$rs_at->por_direito_valor_pessoa;
								$total_por_direito_valor_clinica+=$rs_at->por_direito_valor_clinica;
								
								//$ultima_alteracao= substr($rs_at->ultima_alteracao, 11, 8);
								
								$ultima_alteracao= pega_ultima_alteracao($id_pessoa, $id_clinica, $rs_convenio->id_convenio,
																		$id_ato, formata_data($data), $rs_convenio->ordem
																		);
								
								$bruto[$t]+= $rs_at->por_direito_valor_pessoa+$rs_at->por_direito_valor_clinica;
								$liquido[$t]+= $rs_at->por_direito_valor_pessoa;
							}
							else {
								$qtde= '';
								
								$recebido_valor_pessoa='R$ 0,00';
								$recebido_valor_clinica='R$ 0,00';
								
								$vai_receber_valor_pessoa= 'R$ 0,00';
								$vai_receber_valor_clinica= 'R$ 0,00';
								
								$pessoa_deve= 'R$ 0,00';
								$clinica_deve= 'R$ 0,00';
								
								$por_direito_valor_pessoa= 'R$ 0,00';
								$por_direito_valor_clinica= 'R$ 0,00';
								
								$ultima_alteracao='';
							}
				        ?>
				        <tr id="linha_<?=$id_ato;?>_<?=$i;?>" class="linha_<?=$id_ato;?>_<?=$t;?>_<?=$i;?> linha_<?= strtolower($rs_convenio->label_convenio); ?>">
				            <td class="celula1">
				            	
				            	<input type="hidden" id="id_convenio_<?=$id_ato;?>_<?=$i;?>" name="id_convenio[<?=$i;?>]" value="<?=$rs_convenio->id_convenio;?>" />
				            	<input type="hidden" id="nome_convenio_<?=$id_ato;?>_<?=$i;?>" name="nome_convenio[<?=$i;?>]" value="<?=$rs_convenio->convenio;?>" />
				            	<input type="hidden" id="tipo_convenio_<?=$id_ato;?>_<?=$i;?>" name="tipo_convenio[<?=$i;?>]" value="<?=$rs_convenio->tipo_convenio;?>" />
				            	<input type="hidden" id="recebimento_<?=$id_ato;?>_<?=$i;?>" name="recebimento[<?=$i;?>]" value="<?=$rs_convenio->recebimento;?>" />
				            	<input type="hidden" class="vc_valor_2_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>" id="valor_<?=$id_ato;?>_<?=$i;?>" name="valor[<?=$i;?>]" value="<?=$rs_convenio->valor;?>" />
				            	<input type="hidden" id="percentual_clinica_<?=$id_ato;?>_<?=$i;?>" name="percentual[<?=$i;?>]" value="<?=$percentual_clinica;?>" />
				            	<input type="hidden" id="percentual_medico_<?=$id_ato;?>_<?=$i;?>" name="percentual_medico[<?=$i;?>]" value="<?=$percentual_medico;?>" />
				            	<input type="hidden" id="ordem_<?=$id_ato;?>_<?=$i;?>" name="ordem[<?=$i;?>]" value="<?=$rs_convenio->ordem;?>" />
				            	
				            	<? /*
				            	<a class="btn btn-info btn-mini vc_link" id="vc_link_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>" title="Clique para alterar o valor" href="#modal_convenio_edita" data-toggle="modal" data-convenio="<?= $rs_convenio->convenio; ?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" data-ordem="<?=$rs_convenio->ordem;?>" data-id_ato="<?=$id_ato;?>" data-nome_ato="<?=$nome_ato;?>">
			            			<i class="icon icon-pencil icon-white"></i>
			            		</a> */ ?>
			            		
			            		<a class="btn btn-mini btn-danger btn-exclui_convenio" href="javascript:void(0);"  data-nome_convenio="<?=$rs_convenio->convenio;?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" data-id_ato="<?=$id_ato;?>" data-a="<?=$a;?>" data-t="<?=$t;?>" data-i="<?=$i;?>">
				            		
				            		<i class="icon icon-minus icon-white"></i>
				            		
			            		</a>
				            	
				            	<strong class="pull-left mr nome_convenio_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>"><a class="vc_link_atalho" id="vc_link_atalho_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>" title="Clique para alterar o valor" href="#modal_convenio_edita" data-toggle="modal" data-convenio="<?= $rs_convenio->convenio; ?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" data-ordem="<?=$rs_convenio->ordem;?>" data-id_ato="<?=$id_ato;?>" data-nome_ato="<?=$nome_ato;?>"><?= $label_convenio; ?></a></strong>
				            	
				            	<? if ($rs_convenio->label_convenio!='') { ?>
					            &nbsp; <span class="label label-mini vc_label_label_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>"><?=$rs_convenio->label_convenio;?></span>
					            <? } ?>
				            	
				            	<a class="vc_link_atalho vc_link_atalho_valor" id="vc_link_atalho_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>" title="Clique para alterar o valor" href="#modal_convenio_edita" data-toggle="modal" data-convenio="<?= $rs_convenio->convenio; ?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" data-ordem="<?=$rs_convenio->ordem;?>" data-id_ato="<?=$id_ato;?>" data-nome_ato="<?=$nome_ato;?>"><span class="badge badge-invert badge-porcentagem vc_label_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$rs_convenio->ordem;?>"><small>R$ <?= fnum($rs_convenio->valor); ?></small></span></a>
				            	
				            	<?
				            	$resumo_linha= "
				            					<small>
				            					<strong>Médico</strong> - <em>". fnumi(100-$rs_convenio->percentual) ."%</em><br />
				            					<strong>Levar em dinheiro:</strong> ". $recebido_valor_pessoa ."<br />
				            					<strong>Unimed:</strong> ". $vai_receber_valor_pessoa ."<br />
				            					<strong>Devendo para a clínica:</strong> ". $pessoa_deve ."<br />
				            					<strong>Líquido:</strong> ". $por_direito_valor_pessoa ."<br /><br />
				            					
				            					<strong>Clínica</strong> - <em>". fnumi($rs_convenio->percentual) ."%</em><br />
				            					<strong>Recebeu em dinheiro:</strong> ". $recebido_valor_clinica ."<br />
				            					<strong>Receberá dos convênios guias:</strong> ". $vai_receber_valor_clinica ."<br />
				            					<strong>Deve para o médico:</strong> ". $clinica_deve ."<br />
				            					<strong>Líquido:</strong> ". $por_direito_valor_clinica ."<br />
				            					</small>
				            					
				            					";
				            	?>
				            	
				            	<button type="button" style="margin-top:5px;" id="detalhamento_<?=$id_ato;?>_<?=$i;?>" class="btn btn-mini btn-detalhamento" data-trigger="hover" data-html="true" data-placement="right" data-content="<?=$resumo_linha;?>" href="javascript:void(0);"><i class="icon-eye-open"></i></button>
				            	
				            	<?
				            	/*<span class="badge badge-important"><?= fnumf($rs_convenio->percentual); ?> %</span>
				            	*/
				            	
				            	//<span class='badge badge-important mr'>". fnumi($percentual_medico) ."% / ". fnumi($percentual_clinica) ."%</span><br /><br />
				            	
				            	?>
				            	
				            </td>
				            
				            <?
			            	//$porcentagem_mostra[$c]=$rs_convenio->percentual;
			            	?>
			            	
				            <td class="celula2">
			            		<div class="bloco_botoes pull-right" style="position:relative;">
			            			<? if ($id_usuario_emulado=='') { ?>
				            		<a
				            		
				            		<? if (($c==0) && ($y==1)) { ?>
				            		data-bootstro-step="1"
								  	data-bootstro-placement="top"
								  	data-bootstro-width="450px"
								  	data-bootstro-content="<strong>Este é o principal botão.</strong><br/><br/> Ao atender, clique e registre de acordo com convênio do paciente."
				            		<? } ?>
				            		
				            		data-nome_ato="<?=$nome_ato;?>" data-id_ato="<?=$id_ato;?>" data-i="<?=$i;?>" data-t="<?=$t;?>" href="javascript:void(0);" class="<? if (($c==0) && ($y==1)) { ?>bootstro<? } ?> btn btn-info btn-mais"><i class="icon-plus"></i></a>
				            		<? } ?>
				            		
				            		<? if ($identifica_atendimentos==1) { ?>
				            		<input <? if ($id_usuario_emulado!='') { ?>disabled="disabled"<? } ?> data-id_ato="<?=$id_ato;?>" data-i="<?=$i;?>" data-t="<?=$t;?>" id="lancamento_quantidade_<?=$id_ato;?>_<?=$i;?>" autocomplete="off" type="text" class="input-mini lancamento_quantidade lancamento_quantidade_<?=$id_ato;?>_<?=$i;?>" name="qtde[<?=$i;?>]" data-valor="<?=$qtde;?>" value="<?=$qtde;?>" rel="<?=$rs_convenio->id_convenio;?>" />
				            		
				            		<? if ($id_usuario_emulado=='') { ?>
				            		<a <? if ($qtde=='') echo "disabled='disabled'"; ?> id="zera_<?=$id_ato;?>_<?=$i;?>" data-id_ato="<?=$id_ato;?>" data-i="<?=$i;?>" data-t="<?=$t;?>" href="javascript:void(0);" class="btn btn-warning btn-zera" rel="lancamento_quantidade_<?=$id_ato;?>_<?=$i;?>"><i class="icon-trash icon-white"></i></a>
				            		<!--<a href="javascript:void(0);" class="btn btn-info btn-zera mr" rel="lancamento_quantidade_<?=$t;?>_<?=$i;?>"><i class="icon-remove icon-white"></i></a>-->
				            		<? } ?>
				            		
				            		<? } else { ?>
				            		
				            		<input data-id_ato="<?=$id_ato;?>" data-i="<?=$i;?>" data-t="<?=$t;?>" id="lancamento_quantidade_<?=$id_ato;?>_<?=$i;?>" autocomplete="off" type="text" class="input-mini lancamento_quantidade lancamento_quantidade_<?=$id_ato;?>_<?=$i;?>" name="qtde[<?=$i;?>]" data-valor="<?=$qtde;?>" value="<?=$qtde;?>" rel="<?=$rs_convenio->id_convenio;?>" disabled="disabled" />
				            		
				            		<a <? if ($qtde_todos==0) echo "disabled='disabled'"; ?> id="zera_<?=$id_ato;?>_<?=$i;?>" data-ordem="<?=$rs_convenio->ordem;?>" data-id_ato="<?=$id_ato;?>" data-i="<?=$i;?>" data-t="<?=$t;?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" href="javascript:void(0);" class="btn btn-success btn-lista" rel="lancamento_quantidade_<?=$id_ato;?>_<?=$i;?>"><i class="icon-list icon-white"></i></a>
				            		
				            		<? } ?>
				            		
				            		<div class="flexinha">
				            			<? /*&rarr;*/ ?>
				            			<i class="icon-share-alt"></i>
				            		</div>
				            		<div class="ultima_alteracao" <? if ($ultima_alteracao=='') echo "style='display:none;'"; ?>>
				            			<?=$ultima_alteracao;?>
				            		</div>
				            		
			            		</div>
				            </td>
				        </tr>
				        <? $i++; $c++; }//fim while convenios ?>
				    </tbody>
				</table>
				<table class="table table-striped table-condensed table-lancamento" width="100%">
				    <tfoot>
				    	<tr>
				    		<th
				    		
				    		class="celula1b"
				    		
				    		
				    		
				    		>
					    		
				    		</th>
				    		<th class="celula2b">
				    			<div class="row-fluid">
				    				<div class="span6 vbruto">
										<p class="menor muted text-right" style="font-weight:normal;margin-top:15px;">Bruto:</p>
										<h5 style="margin-top:0;" class="muted text-right br bruto_<?=$id_ato;?>_<?=$t;?>">R$<?=fnum($bruto[$t]);?></h5>
				    				</div>
				    				<div class="span6 vliquido">
				    					<p class="menor text-right" style="font-weight:normal;margin-top:15px;">Líquido:</p>
				    					<h5 style="margin-top:0;" class="text-right br liquido_<?=$id_ato;?>_<?=$t;?>">R$<?=fnum($liquido[$t]);?></h5>
				    				</div>
				    			</div>
				    		</th>
				    	</tr>
				    </tfoot>
				</table>
				
			</div> <!-- /.ato -->
		</div> <!-- /.well -->
		
		<?
		}//fim if mostra
	}//fim for
	?>
	
	<div class="well well_fim <? if ( $id_ato=='1' ) { ?>bootstro<? } ?>"
	
	<? if ( $id_ato=='1' ) { ?>
		
		data-bootstro-step="2"
	  	data-bootstro-placement="bottom"
	  	data-bootstro-width="450px"
	  	data-bootstro-content="Para cada procedimento, configure os convênios que atende."
		
		
		<? } ?>
	>
		<button style="transition:none;" class="novo_convenio btn btn-mini btn-success" data-id_ato="<?=$id_ato;?>" data-a="<?=$a;?>">
			<i style="margin-top:3px;" class="icon icon-white icon-plus"></i>
			convênio
		</button>
		
		<button style="" class="btn btn-mini btn-primary alterar_convenio alterar_convenio_" data-id_ato="<?=$id_ato;?>" data-a="<?=$a;?>">
			<i style="margin-top:3px;" class="icon icon-white icon-minus"></i>
			&nbsp;<span class="lbl">editar lista</span>
		</button>
	</div>
    		
<?
}

function gera_setup_ato($id_pessoa, $id_clinica, $id_ato, &$a, $k) {
	$k=0;
?>
	<div id="ato_<?=$a;?>">
		<input type="hidden" name="id_ato[<?=$a;?>]" value="<?=$id_ato;?>" />
					
		<?
		//$k=0;
	    for ($t=1; $t<4; $t++) {
	    	$j=0;
	    ?>
		
		<div class="well">
	
			<h5><?= pega_tipo_convenio($t); ?></h5>
			
			<table style="margin-bottom:0 !important;" cellspacing="0" width="95%" class="table table-striped table-hover table-condensed">
				<thead>
			        <tr>
			            <th width="3%">
			            	<input type="checkbox" id="atendo_<?=$t;?>" rel="#ato_<?= $a; ?> .area_<?= $id_ato; ?>_<?=$t;?>" class="atendo" value="1" />
			            </th>
			            <th width="50%">Selecionar todos</th>
			            <th width="15%" align="left"><div class="pull-left">Valor</div> <i title="Informe o valor para o convênio informado" class="tt tt-ajuda pull-left icon-question-sign"></i></th>
			            <th width="25%" align="left"><div class="pull-left">% clínica</div> <i title="Informe a % acertada para a clínica" class="tt tt-ajuda pull-left icon-question-sign"></th>
			            <th width="5%">&nbsp;</th>
			        </tr>
			    </thead>
			</table>
						    
			<div class="ato area_<?= $id_ato; ?>_<?=$t;?>">
				<table cellspacing="0" width="95%" class="table table-striped table-hover table-condensed">
				    <!--
				    <thead>
				        <tr>
				            <th width="12%"><input type="checkbox" rel=".area_<?=$_GET[num];?> #ato_<?= $rs_atos->id_ato; ?>" class="atendo" /> </th>
				            <th width="30%"><?= pega_tipo_convenio($t); ?></th>
				            <th width="17%" align="left">Valor</th>
				            <th width="17%" align="left">% clínica</th>
				        </tr>
				    </thead>
					-->
					
				    <tbody>
						<?
				        $result_convenio= mysqli_query($GLOBALS["conexao1"], "select * from convenios
														where status <> '2'
														and   tipo_convenio = '$t'
														order by convenio asc
														") or die(mysqli_error());
				        while ($rs_convenio= mysqli_fetch_object($result_convenio)) {
				        	$result_pcc=  mysqli_query($GLOBALS["conexao1"], "select * from pessoas_clinicas_convenios
																	where id_pessoa = '". $id_pessoa ."'
																	and   id_convenio = '". $rs_convenio->id_convenio ."'
																	and   id_clinica = '". $id_clinica ."'
																	and   id_ato = '". $id_ato ."'
																	and   ordem = '0'
																	") or die(mysqli_error());
							$rs_pcc= mysqli_fetch_object($result_pcc);
							$linhas_pcc= mysqli_num_rows($result_pcc);
							
							//já tem dados cadastrados
							if ($linhas_pcc>0) {
								$valor= fnum_naozero($rs_pcc->valor);
								$percentual= fnumf($rs_pcc->percentual);
							}
							//não tem, pegar valores padrão
							else {
								$result_convenio_ato= mysqli_query($GLOBALS["conexao1"], "select * from convenios_atos
																	where id_convenio = '". $rs_convenio->id_convenio ."'
																	and   id_ato = '". $id_ato ."'
																	") or die(mysqli_error());
								$rs_convenio_ato= mysqli_fetch_object($result_convenio_ato);
													
								$valor= fnum_naozero($rs_convenio_ato->valor);
								$percentual= '';
							}
				        ?>
				        <tr class="<? if ($rs_convenio->valores_multiplos=='1') { ?>valores_multiplos_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_0 <? } ?>">
				            <td width="3%">
					            <input type="hidden" name="id_convenio[<?=$a;?>][<?=$k;?>]" value="<?=$rs_convenio->id_convenio;?>" />
					            
					            <input type="checkbox" class="checkbox_atendo" name="atendo[<?=$a;?>][<?=$k;?>]" value="1" <? if ($linhas_pcc>0) echo 'checked="checked"'; ?> />
				            </td>
				            <td width="50%">
				            	<?= $rs_convenio->convenio; ?>
				            	
				            	<? if ($rs_convenio->label_convenio!='') { ?>
					            &nbsp; <span class="label label-mini"><?=$rs_convenio->label_convenio;?></span>
					            <? } ?>
				            	
				            	<? if ($rs_convenio->valores_multiplos=='1') { ?>
				            	&nbsp; <a class="btn btn-mini btn-info btn-valores_multiplos" data-a="<?=$a;?>" data-id_ato="<?=$id_ato;?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" href="javascript:void(0);">adicionar outro valor</a>
								<? } ?>
				            </td>
				            <td width="15%">
				            	<div class="input-prepend">
				            		<span class="add-on">R$</span>
				            		<input autocomplete="off" type="text" class="input-mini valor_campo valor_campo_<?=$j;?> regiao_<?=$t;?>" name="valor[<?=$a;?>][<?=$k;?>]" value="<?=$valor;?>" <? if ($j==0) { ?>rel=".area_<?= $id_ato; ?>_<?=$t;?>" <? } ?> />
				            		
				            		<input type="hidden" name="ordem[<?=$a;?>][<?=$k;?>]" value="0" />
				            	</div>
				            </td>
				            <td width="25%">
					            <div class="input-append pull-left">
				            		<input id="percentual_<?=$a;?>_<?=$t;?>_<?=$k;?>" autocomplete="off" type="text" class="input-mini percentual_campo percentual_campo_<?=$j;?> regiao_<?=$t;?>" name="percentual[<?=$a;?>][<?=$k;?>]" value="<?=$percentual;?>" <? if ($j==0) { ?>rel=".area_<?= $id_ato; ?>_<?=$t;?>" <? } ?> />
				            		<span class="add-on">%</span>
				            	</div>
				            	
				            	<? if ($j==0) { ?>
				            	<a href="javascript:void(0);" data-a="<?=$a;?>" data-rel="percentual_<?=$a;?>_<?=$t;?>_<?=$k;?>" data-id_ato="<?=$id_ato;?>" data-id_convenio="<?=$rs_convenio->id_convenio;?>" rel=".area_<?= $id_ato; ?>_<?=$t;?>" title="Aplicar esta porcentagem a todos os campos desta área." class="tt btn btn-mini btn-info pull-left aplicar_todos">aplicar a todos</a>
				            	<? } ?>
				            </td>
				            <td width="5%">&nbsp;</td>
				        </tr>
				        <? $k++; $i++; $j++; ?>
				        
				        <?
				        if ($rs_convenio->valores_multiplos=='1') {
					        $result_pcc2=  mysqli_query($GLOBALS["conexao1"], "select * from pessoas_clinicas_convenios
																	where id_pessoa = '". $id_pessoa ."'
																	and   id_convenio = '". $rs_convenio->id_convenio ."'
																	and   id_clinica = '". $id_clinica ."'
																	and   id_ato = '". $id_ato ."'
																	and   ordem <> '0'
																	") or die(mysqli_error());
							$linhas_pcc2= mysqli_num_rows($result_pcc2);
							$r=1;
							while ($rs_pcc2= mysqli_fetch_object($result_pcc2)) {
				        ?>
				        <tr class='valores_multiplos_pai_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?> valores_multiplos_<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$r;?> valor_multiplo'>
				        	<td>
				        		<input type="hidden" name="id_convenio[<?=$a;?>][<?=$k;?>]" value="<?=$rs_convenio->id_convenio;?>" />
								<input type="hidden" name="atendo[<?=$a;?>][<?=$k;?>]" value="1" /></td>
				        	<td>&nbsp;</td>
				        	<td>
					        	<div class="input-prepend">
				            		<span class="add-on">R$</span>
				            		<input autocomplete="off" type="text" class="input-mini valor_campo valor_campo_<?=$j;?>" name="valor[<?=$a;?>][<?=$k;?>]" value="<?=fnum_naozero($rs_pcc2->valor);?>" />
				            		
				            		<input type="hidden" name="ordem[<?=$a;?>][<?=$k;?>]" value="<?=$rs_pcc2->ordem;?>" />
				            	</div>
				        	</td>
				        	<td>
					        	<div class="input-append">
				            		<input autocomplete="off" type="text" class="input-mini percentual_campo percentual_campo_<?=$j;?>" name="percentual[<?=$a;?>][<?=$k;?>]" value="<?=fnumi($rs_pcc2->percentual);?>" />
				            		<span class="add-on">%</span>
				            	</div>
				        	</td>
				        	<td><a class='btn btn-danger btn-mini btn-remove-vm' rel='<?=$id_ato;?>_<?=$rs_convenio->id_convenio;?>_<?=$r;?>' href='javascript:void(0);'>remover</a></td>
				        </tr>
				        <?
				        	$k++; $r++; $i++;
				        	}//fim while
				        }//fim if
				        ?>
				        
				        <? }//fim while convenios ?>
				    </tbody>
				    
				</table>
			</div>
		</div>
		<?
		}//fim for
		$a++;
	?>
		<input type="hidden" class="ultimo_k" value="<?=$k;?>" />
	</div>
	
	<script>
		$('#ultimo_a').val('<?=$a;?>');
	</script>
	<?
	
	//return($k);
}

function escreve_json($tipo) {
	switch ($tipo) {
		case 'clinicas':
			$result_clinicas= mysqli_query($GLOBALS["conexao1"], "select * from clinicas
											order by clinica asc
											") or die(mysqli_error());
			$dados= array();
			while ($rs_clinicas= mysqli_fetch_object($result_clinicas)) {
				/*$dados[] = array(
							      'id_clinica' => $rs_clinicas->id_clinica,
							      'clinica' => htmlentities($rs_clinicas->clinica),
							      'endereco' => htmlentities($rs_clinicas->endereco),
							      'id_cidade' => $rs_clinicas->id_cidade,
							      'cidade' => htmlentities(pega_cidade($rs_clinicas->id_cidade))
							     );*/
				$dados[] = array(
							      'clinica' => htmlentities($rs_clinicas->clinica)
							     );			     
			}
		break;		
	}
	
	//$conserta= array_map("htmlentities", $dados);
	$json= json_encode($dados);
	
	$arquivo= 'uploads/'. $tipo .'.json';
	$escreve= file_put_contents($arquivo, $json);
	
	return($escreve);
}

function plantonista_esta_no_usuario($id_planotnista, $id_usuario) {
	$result= mysqli_query($GLOBALS["conexao1"], "select * from pessoas
						 		where id_pessoa = '$id_planotnista'
								and   id_usuario = '$id_usuario'
								and   status_pessoa = '1'
								");
	$num= mysqli_num_rows($result);
	
	if ($num>0) return(true);
	else return(false);
}

function usuario_esta_na_clinica($id_pessoa, $id_clinica) {
	$result= mysqli_query($GLOBALS["conexao1"], "select * from pessoas_clinicas
						 		where id_pessoa = '$id_pessoa'
								and   id_clinica = '$id_clinica'
								and   status_pc = '1'
								");
	$num= mysqli_num_rows($result);
	
	if ($num>0) return(true);
	else return(false);
}

function grava_acesso($id_usuario, $data, $hora, $ip, $ip_reverso, $user_agent, $referer, $id_clinica) {
	
	$result_acesso= mysqli_query($GLOBALS["conexao1"], "insert into acessos
								(id_usuario, data, hora, ip, ip_reverso, user_agent, referer, id_clinica)
								values
								('$id_usuario', '". $data ."', '". $hora ."',
								'". $ip ."', '". $ip_reverso ."', '". $user_agent ."',
								'". $referer ."', '". $id_clinica ."'
								)
								") or die('2:'.mysqli_error());
	$id_acesso= mysqli_insert_id($GLOBALS["conexao1"]);
	return($id_acesso);
}

function ajeita_datas($data1, $data2, $periodo) {
	if ( ($data1!="") && ($data2!="") ) {
		$data1f= $data1;
		$data2f= $data2;
		
		$data1= formata_data_hifen($data1); 
		$data2= formata_data_hifen($data2); 
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo= explode('/', $periodo);
		
		$data1_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$total_dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(23, 0, 0, $periodo[0], $total_dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	$data_mk[0]= $data1_mk;
	$data_mk[1]= $data2_mk;
	
	return($data_mk);
}

function format_bytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}

function inverte_0_1($valor) {
	if ($valor==1) $valor=0;
	else $valor=1;
	
	return($valor);
}

function converteEncode($item) { return mb_convert_encoding($item, "UTF-8", "ISO-8859-1"); }

function faz_embed_video($video, $largura, $altura) {
	
	if (strpos($video, "vimeo")) {
		$parte_video= explode("/", $video);
		$count_video= count($parte_video);
		
		$id_video= $parte_video[$count_video-1];
		
		$retorno= '<iframe src="http://player.vimeo.com/video/'. $id_video .'?portrait=0&amp;color=22B2BA" width="'. $largura .'" height="'. $altura .'" frameborder="0"></iframe>';
	} elseif (strpos($video, "youtube")) {
		$id_video= extrai_link_youtube($video);
		
		$retorno= pega_video_youtube($id_video, $largura, $altura);
	}
	
	return($retorno);
}

function pega_id_clinica_principal($id_pessoa) {
	$result= mysqli_query($GLOBALS["conexao1"], "select * from pessoas_clinicas
							where id_pessoa = '". $id_pessoa ."'
							and   status_pc = '1'
							order by id_pc desc limit 1
							");
	$rs= mysqli_fetch_object($result);
	
	return($rs->id_clinica);
}

function pega_dimensao_padrao_video($tipo, $tipo_dimensao) {
	switch ($tipo) {
		case 'p':
			$largura= 940;
			$altura= 530;
		break;
		case 'a':
			$largura= 620;
			$altura= 350;
		break;
	}
	
	if ($tipo_dimensao=='l') $retorno= $largura;
	else $retorno= $altura;
	
	return($retorno);
}

function extrai_link_youtube($link) {
	//http://www.youtube.com/watch?v=lsO6D1rwrKc&v1
	//http://www.youtube.com/watch?v=Dji8M2oBVTo&mode=related&search=
	
	$novo= explode("?v=", $link);
	if (strpos($novo[1], "&")) {
		$novo= explode("&", $novo[1]);
		$link_novo= $novo[0];
	}
	else
		$link_novo= $novo[1];
	
	return($link_novo);
}

function converte_data_completa_utc($data_local) {
					
	$data_utc = new DateTime($data_local);
	$data_utc->setTimeZone(new DateTimeZone("UTC"));
	
	$data_utc= $data_utc->format('Y-m-d H:i:s');
	
	return($data_utc);
}

function pega_video_youtube($codigo, $largura, $altura) {
	if ($codigo!="")
		$retorno= ' <object width="'. $largura .'" height="'. $altura .'">
						<param name="movie" value="http://www.youtube.com/v/'. $codigo .'"></param>
						<param name="wmode" value="transparent"></param>
						<embed src="http://www.youtube.com/v/'. $codigo .'" type="application/x-shockwave-flash" wmode="transparent" width="'. $largura .'" height="'. $altura .'"></embed>
					</object>';
	//else $retorno= "Sem vídeo.";
	
	return($retorno);
}

function pega_tags_campo($campo, $tipo_tag, $l, $tags, $replicar) {
	
	if ($tipo_tag=="1") $str= " and   tipo_tag = '1' ";
	elseif ($tipo_tag=="2") $str= " ";
	
	$result_tag= mysqli_query($GLOBALS["conexao1"], "select *, tag_". $l ." as tag from tags
								where 1=1
								". $str ."
								order by tag_". $l ." asc
								") or die(mysqli_error());
	$linhas_tag= mysqli_num_rows($result_tag);
	
	if ($linhas_tag>0) {
		echo '<ul class="lista_tags_curadoria">';
		$i=0;
		while ($rs_tag= mysqli_fetch_object($result_tag)) {
			
			if (strpos($tags, $rs_tag->tag .", ")!==false) $checado= "checked=\"checked\"";
			else $checado= "";
			
			if ($replicar==1) $valor= $rs_tag->tag ."|". $rs_tag->tag_en;
			else $valor= $rs_tag->tag;
			
			echo '
				<li>
					<label class="tamanho_auto" for="'. $campo .'_tag_'. $i .'">'. $rs_tag->tag .'</label>
					<input '. $checado .' class="tamanho30" type="checkbox" name="tag[]" onclick="adicionaTagCampo(this, \''. $campo .'\', \''. $valor .'\', \''. $replicar .'\');" id="'. $campo .'_tag_'. $i .'" value="'. $valor .'" />
				</li>
				';
			  $i++;
		}
		echo '</ul>';
	}
	
}

function cria_imagem_site($site, $id_imagem) {
	
	$result= mysqli_query($GLOBALS["conexao1"], "select * from imagens
							where id_imagem = '". $id_imagem ."'
							");
							
	$linhas= mysqli_num_rows($result);
	
	if ($linhas>0) {
		$rs= mysqli_fetch_object($result);
		
		if ($rs->tipo_imagem=="a") {
			$pasta= "artista_";
			
			if ($rs->miniatura_destaque=="3") $largura= 940;
			else $largura= 620;
		}
		else {
			$pasta= "projeto_";
			$largura= 940;
		}
		
		if ($site==1) {
			
			$files = array(CAMINHO . $rs->tipo_imagem ."_". $rs->id_externo ."/". $rs->nome_arquivo);
			
			include("includes/phpthumb/phpthumb.class.php");
			
			$phpThumb = new phpThumb();
	
			foreach( $files as $file ) { // here's part 1 of your answer
				$phpThumb->setSourceFilename($file); 
				$phpThumb->setParameter('w', $largura);
				//$phpThumb->setParameter('h', $rs->altura);
				//$phpThumb->setParameter('zc', 1);
				$phpThumb->setParameter('q', 94);
				
				if ($rs->nome_arquivo_site!="") $nome_arquivo_site= $rs->nome_arquivo_site;
				else $nome_arquivo_site= gera_auth() ."_". $rs->nome_arquivo;
					
				$outputDir = "../uploads/". $pasta . $rs->id_externo;
				
				if (!is_dir($outputDir)) {
					mkdir(str_replace('//','/',$outputDir), 0755, true);
				}
				
				$outputFilename= $outputDir ."/". $nome_arquivo_site;
				
				if ($phpThumb->GenerateThumbnail()) { 
					
					$phpThumb->RenderOutput();
					if ($phpThumb->RenderToFile($outputFilename)) { // here's part 2 of your answer
					  
					  //echo $outputFilename;
					   
					   $tamanhos= @getimagesize($outputFilename);
					   
					   if ($rs->nome_arquivo_site=="") {
						   $result= mysqli_query($GLOBALS["conexao1"], "update imagens set nome_arquivo_site= '". $nome_arquivo_site ."',
						   							largura_site = '". $tamanhos[0] ."',
													altura_site = '". $tamanhos[1] ."'
													where id_imagem = '". $id_imagem ."'
													");  
					   }
					}
					else {
					   echo 'Failed: '. implode("<br />", $phpThumb->debugmessages);
					}
				}
				else {
					echo "ERRO 2";
				}
			}
	
		}
		else {
			
			$outputDir = "../uploads/". $pasta . $rs->id_externo;
			$outputFilename= $outputDir ."/". $rs->nome_arquivo_site;
			
			if (file_exists($outputFilename))
				@unlink($outputFilename);
				
			$result= mysqli_query($GLOBALS["conexao1"], "update imagens set nome_arquivo_site= ''
													where id_imagem = '". $id_imagem ."'
													");  
		}
	}
}

function pega_miniatura($id, $oque) {
	switch ($id) {
		case 1:
			$miniatura= "Miniatura - 300x160";
			$largura= 300;
			$altura= 160;
		break;
		case 2:
			$miniatura= "Grande - 620x330";
			$largura= 620;
			$altura= 330;
		break;
		case 3:
			$miniatura= "Destaque - 940x380";
			$largura= 940;
			$altura= 380;
		break;
		case 4:
			$miniatura= "Avatar - 32x26";
			$largura= 32;
			$altura= 26;
		break;
	}
	
	switch($oque) {
		case "n": $retorno= $miniatura; break;
		case "l": $retorno= $largura; break;
		case "a": $retorno= $altura; break;
		case "p": $retorno= ($largura/$altura); break;
	}
	
	return($retorno);
}

function pega_perfil($perfil) {
	$result= mysqli_query($GLOBALS["conexao1"],
							"select * from cad_perfis
							where id_perfil= '$perfil'
							") or die(mysqli_error());
	$rs= mysqli_fetch_object($result);
	return($rs->perfil);	
}

function cpc($px) {
	return(($px*29.7)/1900);
}

function retira_acentos($texto) {
  $array1 = array(   "#", " ", "&", "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
                     , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
  $array2 = array(   "_", "_", "e", "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
                     , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );
  return @str_replace( $array1, $array2, $texto );
}

function faz_url($str) {
	return(retira_acentos(strtolower(str_replace(" ", "-", $str))));
}

function string_maior_que($string, $tamanho) {
	if (strlen($string)>$tamanho) $var= $string ."...";
	else $var= $string;
	
	return($var);
}

function pega_tipo_atendimento($tipo_atendimento) {
	switch ($tipo_atendimento) {
		case 2: $tipo= "Retorno"; break;
		default: $tipo="Consulta"; break;
	}
	return($tipo);
}

function pega_tipo($i) {
	switch ($i) {
		case 'f': $tipo= "Física"; break;
		case 'j': $tipo= "Jurídica"; break;
		default: $tipo=""; break;
	}
	return($tipo);
}

function pega_tipo_convenio($tipo_convenio) {
	if ($tipo_convenio=="1") return("Pagos em espécie");
	elseif ($tipo_convenio=="2") return("Convênios");
	else return("Convênios Próprios");
}

function pega_recebimento_convenio($recebimento) {
	if ($recebimento=="1") return("Conta da clínica");
	elseif ($recebimento=="2") return("Conta do médico");
}

function pega_sexo($sexo) {
	if ($sexo=="m") return("Masculino");
	else return("Feminino");
}

function fnum($numero) {
	$quebra= explode(".", $numero);
	//$tamanho= strlen($quebra[1]);
	
	return(number_format($numero, 2, ',', '.'));
}

function fnum_naozero($numero) {
	if (($numero!=0) && ($numero!="")) {
		$quebra= explode(".", $numero);
		$tamanho= strlen($quebra[1]);
		
		return(number_format($numero, 2, ',', '.'));
	}
}

function fnum2($numero) {
	$quebra= explode(".", $numero);
	$tamanho= strlen($quebra[1]);
	
	return(number_format($numero, $tamanho, ',', '.'));
}

function fnumi($numero) {
	return(number_format($numero, 0, ',', '.'));
}

function fnumf($numero) {
	if ($numero!="") {
		$decimal= substr($numero, -2, 2);
		if (strpos($numero, ".")) return(fnum($numero));
		else return(fnumi($numero));
	}
}

function fnumf_naozero($numero) {
	if (($numero!=0) && ($numero!="")) {
		$decimal= substr($numero, -2, 2);
		if ((strpos($numero, ".")) && ($decimal!="00")) return(fnum($numero));
		else return(fnumi($numero));
	}
}

function pega_numero_semana($ano, $mes, $dia) {
   return ceil(($dia + date("w", mktime(0, 0, 0, $mes, 1, $ano)))/7);   
} 


function eh_decimal($numero) {
	$decimal= substr($numero, -2, 2);
	if ($decimal!="00") return(true);
	else return(false);
}

function primeira_palavra($frase) {
	$retorno= explode(" ", $frase);
	return($retorno[0]);
}

function formata_saida($valor, $tamanho_saida) {
	//3, 5
	$tamanho= strlen($valor);
	
	for ($i=$tamanho; $i<$tamanho_saida; $i++)
		$saida .= '0';
		
	return($saida . $valor);
}

function formata_cbhpm($codigo) {
	//10101012
	//1.01.01.01-2
	
	$novo_codigo= substr($codigo, 0, 1);
	$novo_codigo.='.';
	$novo_codigo.= substr($codigo, 1, 2);
	$novo_codigo.='.';
	$novo_codigo.= substr($codigo, 3, 2);
	$novo_codigo.='.';
	$novo_codigo.= substr($codigo, 5, 2);
	$novo_codigo.='-';
	$novo_codigo.= substr($codigo, 7, 1);
	
	return($novo_codigo);
}

function calcula_idade($data_nasc) {
	$var= explode("/", $data_nasc, 3);
	
	if (strlen($var[2])==2)
		$var[2]= "20". $var[2];
	
	$dia=$var[0];
	$mes=$var[1];
	$ano=$var[2];

	if (($data_nasc!="") && ($data_nasc!="00/00/0000") && ($ano<=date("Y"))) {
		
		$idade= date("Y")-$ano;
		if ($mes>date("m"))
			$idade--;
		if (($mes==date("m")) && ($dia>date("d")) )
			$idade--;
		return($idade);
	}
	//else
	///	return("<span class=\"vermelho\">Não disponível!</span>");
}

// ------------------------------- funcoes do ponto -------------------------------------------

function pega_num_ultima_pessoa($id_empresa, $tipo_pessoa) {
	$result= mysqli_query($GLOBALS["conexao1"], "select * from pessoas_tipos
						 		where id_empresa = '$id_empresa'
								and   tipo_pessoa = '$tipo_pessoa'
								order by id_pessoa desc limit 1
								");
	$rs= mysqli_fetch_object($result);
	return($rs->num_pessoa);
}

function enviar_mensagem($id_empresa, $de, $para, $titulo, $mensagem) {
	$result= mysqli_query($GLOBALS["conexao1"], "insert into com_mensagens
						 	(id_empresa, de, para, titulo, mensagem, data_mensagem, hora_mensagem,
							 lida, situacao_de, situacao_para, auth)
							values
							('$id_empresa', '$de', '$para', '$titulo', '$mensagem', '". date("Ymd") ."', '". date("His") ."',
							 '0', '1', '1', '". gera_auth() ."')
							");
	if ($result) return(mysqli_insert_id($GLOBALS["conexao1"]));
	else return(0);
}

function mensagem_nova($id_usuario) {
	$id_pessoa= pega_id_pessoa_do_usuario($id_usuario);
	
	$result= mysqli_query($GLOBALS["conexao1"], "select id_mensagem from com_mensagens
								 	where situacao_para='1'
									and para= '". $id_pessoa ."'
									and   lida= '0' ") or die(mysqli_error());
	
	if (mysqli_num_rows($result)>0) return(true);
	else return(false);
}

function verifica_backup() {
	//$data= date("Y-m-d");
	//$result_pre= mysqli_query($GLOBALS["conexao1"], "select * from backups where data_backup = '". $data ."' ");
	
	//if (mysqli_num_rows($result_pre)==0)
		header("location: includes/backup/backup.php");
		
	//else echo "Backup já feito no dia de hoje!";
		
}

function pega_porcentagem($valor, $total) {
	if ($total>0)
		return(($valor*100)/$total);
	else return(0);
}

function formata_diferenca_horas_mk($diferenca) {
	//$anos= floor($diferenca / (365*60*60*24));
	//$meses= floor(($diferenca - $anos * 365*60*60*24) / (30*60*60*24));
	//$dias= floor(($diferenca - $anos * 365*60*60*24 - $meses*30*60*60*24)/ (60*60*24));
	$horas= floor(($diferenca - $anos * 365*60*60*24 - $meses*30*60*60*24 - $dias*60*60*24)/ (60*60)); 
	$minutos= floor(($diferenca - $anos * 365*60*60*24 - $meses*30*60*60*24 - $dias*60*60*24 - $horas*60*60)/ 60); 							
	$segundos= floor(($diferenca - $anos * 365*60*60*24 - $meses*30*60*60*24 - $dias*60*60*24 - $horas*60*60 - $minutos*60));
	
	$retorno="";
	
	/*if ($anos>0) $retorno.= $anos ."a ";
	if ($meses>0) $retorno.= $meses ."m ";
	if ($dias>0) {
		if ($dias==1)
			$retorno.= $dias ."d ";
		else
			$retorno.= $dias ."d ";
	}*/
	
	$retorno.= formata_saida($horas, 2) .":";
	$retorno.= formata_saida($minutos, 2) .":";
	$retorno.= formata_saida($segundos, 2) ."";
	
	return($retorno);
}

function soma_data($data, $dias, $meses, $anos) {
	if (strpos($data, "-")) {
		$dia_controle= explode('-', $data);
		$data= date("Y-m-d", mktime(0, 0, 0, $dia_controle[1]+$meses, $dia_controle[2]+($dias), $dia_controle[0]+$anos));
	}
	elseif (strpos($data, "/")) {
		$dia_controle= explode('/', $data);
		$data= date("d/m/Y", mktime(0, 0, 0, $dia_controle[1]+$meses, $dia_controle[0]+($dias), $dia_controle[2]+$anos));
	}
    
    return($data);
}

function soma_data_hora($data_hora, $dias, $meses, $anos, $horas, $minutos, $segundos) {
	
	//2009-10-10 10:11:12
	if (strpos($data_hora, "-")) {
		$ano= substr($data_hora, 0, 4);
		$mes= substr($data_hora, 5, 2);
		$dia= substr($data_hora, 8, 2);
		$hora= substr($data_hora, 11, 2);
		$minuto= substr($data_hora, 14, 2);
		$segundo= substr($data_hora, 17, 2);
		
		$data= date("Y-m-d H:i:s", mktime($hora+$horas, $minuto+$minutos, $segundo+$segundos, $mes+$meses, $dia+$dias, $ano+$anos));
	}
	//10/10/2009 10:11:12
	elseif (strpos($data_hora, "/")) {
		$ano= substr($data_hora, 6, 4);
		$mes= substr($data_hora, 3, 2);
		$dia= substr($data_hora, 0, 2);
		$hora= substr($data_hora, 11, 2);
		$minuto= substr($data_hora, 14, 2);
		$segundo= substr($data_hora, 17, 2);
		
		$data= date("Y-m-d H:i:s", mktime($hora+$horas, $minuto+$minutos, $segundo+$segundos, $mes+$meses, $dia+$dias, $ano+$anos));
	}
    
    return($data);
}

function pega_id_empresa_da_pessoa($id_pessoa) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select id_empresa from empresas
											where id_pessoa = '$id_pessoa' "));
	return($rs->id_empresa);
}

function pega_clinica_pessoa($id_pessoa, $id_clinica) {
	$rs_pre= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select clinicas.clinica, pessoas_clinicas.nome_exibicao_clinica from clinicas, pessoas_clinicas
												where clinicas.id_clinica = '$id_clinica'
												and   clinicas.id_clinica = pessoas_clinicas.id_clinica
												and   pessoas_clinicas.id_pessoa = '". $id_pessoa ."'
												and   pessoas_clinicas.status_pc = '1'
												"));
	
	if ($rs_pre->nome_exibicao_clinica!='') $retorno= $rs_pre->nome_exibicao_clinica;
	else $retorno= $rs_pre->clinica;
	
	return($retorno);
}

function pega_clinica($id_clinica) {
	$rs_pre= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select clinica from clinicas
												where id_clinica = '$id_clinica' "));
	
	return($rs_pre->clinica);
}

function pega_ato($id_ato) {
	$rs_pre= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select ato from atos
												where id_ato = '$id_ato' "));
	
	return($rs_pre->ato);
}

function pega_cod_ato($id_ato) {
	$rs_pre= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select codigo_cbhpm from atos
												where id_ato = '$id_ato' "));
	
	return(formata_cbhpm($rs_pre->codigo_cbhpm));
}

function pega_convenio($id_convenio) {
	$rs_pre= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select convenio from convenios
												where id_convenio = '$id_convenio' "));
	
	return($rs_pre->convenio);
}

function pega_nome_usuario($id_usuario) {
	$rs_pre= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select nome from usuarios
												where id_usuario = '$id_usuario' "));
	
	return($rs_pre->nome);
}

function traduz_periodicidade($p) {
	
	switch ($p[1]) {
		case "d": $periodo= "dia"; break;
		case "m": $periodo= "mês"; break;
		case "a": $periodo= "ano"; break;
	}
	
	return($p[0] ."x/". $periodo);
}

function valor_extenso($valor=0) {

	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "trÍs", "quatro", "cinco", "seis","sete", "oito", "nove");

	$z=0;

	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
	
		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($inteiro)-1-$i;
		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ($valor == "000")$z++; elseif ($z > 0) $z--;
		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
		if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : "") . $r;
	}

	return($rt ? $rt : "zero");
}

function formata_hora($var) {
	//transformando em segundos
	$var= explode(":", $var, 3);
	
	$total_horas= $var[0]*3600;
	$total_minutos= $var[1]*60;
	$total_segundos= $var[2];
	
	$var= $total_horas+$total_minutos+$total_segundos;
	
	return($var);
}

function pode_um($area, $permissao) {
	$contem= strpos($permissao, $area);

	if ($contem!==false) $retorno= true;
	else $retorno= false;
		
	return($retorno);
}

function pode($areas, $permissao) {
	$tamanho= strlen($areas);
	$retorno= false;
	
	for ($i=0; $i<$tamanho; $i++) {
		
		if (pode_um($areas[$i], $permissao)) {
			$retorno=true;
			break;
		}
	}

	return($retorno);
}

function pode_algum($areas, $permissao) {
	$tamanho= strlen($areas);
	$retorno= false;
	
	for ($i=0; $i<$tamanho; $i++) {
		if (pode_um($areas[$i], $permissao)) {
			$retorno=true;
			break;
		}
	}

	return($retorno);
}

function logs($id_acesso, $id_usuario, $perfil, $tipo, $id_referencia, $area, $acao, $descricao, $descricao_oculta, $ip, $ip_reverso, $user_agent, $referer) {
	
	$id_referencia= $_COOKIE[id_clinica];
	
	$descricao= str_replace('|', '\r\n', $descricao);
	$descricao_oculta= str_replace('|', '\r\n', $descricao_oculta);
	
	$result= mysqli_query($GLOBALS["conexao1"], "insert into logs (id_acesso, id_usuario, perfil, tipo, id_referencia, area, acao, descricao, descricao_oculta, data, hora, ip, ip_reverso, user_agent, referer)
							values
							('$id_acesso', '$id_usuario', '$perfil', '$tipo', '$id_referencia', '$area', '$acao', '$descricao', '$descricao_oculta', '". date("Y-m-d") ."', '". date("H:i:s") ."', '$ip', '$ip_reverso', '$user_agent', '$referer')
							") or die("logs: ". mysqli_error());
}


function traduz_mes($mes) {
	switch($mes) {
		case 1: $retorno= "Janeiro"; break;
		case 2: $retorno= "Fevereiro"; break;
		case 3: $retorno= "Março"; break;
		case 4: $retorno= "Abril"; break;
		case 5: $retorno= "Maio"; break;
		case 6: $retorno= "Junho"; break;
		case 7: $retorno= "Julho"; break;
		case 8: $retorno= "Agosto"; break;
		case 9: $retorno= "Setembro"; break;
		case 10: $retorno= "Outubro"; break;
		case 11: $retorno= "Novembro"; break;
		case 12: $retorno= "Dezembro"; break;
		default: $retorno= ""; break;
	}
	return($retorno);
}

function inverte($num) {
	if ($num==1) return(0);
	else return(1);
}

function excluido_ou_nao($var) {
	if ($var==0) $retorno_msg= "Excluído com sucesso!";
	else $retorno_msg= "Não foi possível excluir!";
	
	return("<script language=\"javascript\">alert('". $retorno_msg ."');</script>");
}

function sim_nao($situacao) {
	if (($situacao==0) || ($situacao==2)) return("<span class=\"vermelho\">NÃO</span>");
	else return("<span class=\"verde\">SIM</span>");
}

function ativo_inativo($situacao) {
	if ($situacao==1) return("<span class=\"verde\">ATIVO</span>");
	elseif ($situacao==-1) return("<span class=\"vermelho\">EM ESPERA</span>");
	else return("<span class=\"vermelho\">INATIVO</span>");
}

function sim_nao_simples($situacao) {
	if ($situacao==1) return("Sim");
	else return("Não");
}

function pega_cidade($id_cidade) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select cidades.cidade, ufs.uf from cidades, ufs
											where cidades.id_uf = ufs.id_uf
											and   cidades.id_cidade = '$id_cidade'
											"));
	return($rs->cidade ."/". $rs->uf);
}

function pega_pessoa($id_pessoa) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select nome from pessoas where id_pessoa = '$id_pessoa' "));
	return($rs->nome);
}

function pega_especialidade($id_especialidade) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select * from  especialidades where id_especialidade = '$id_especialidade' "));
	return($rs->especialidade);
}

function pega_usuario($id_usuario) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select pessoas.* from pessoas, usuarios
											where usuarios.id_pessoa = pessoas.id_pessoa
											and   usuarios.id_usuario = '$id_usuario' "));
	return($rs->nome);
}

function pega_pessoa_dado($id_pessoa, $dado) {
	if ( ($dado!='senha') && ($dado!='senha_sem') ) {
		$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select pessoas.".$dado." as dado from pessoas
												where id_pessoa = '$id_pessoa' "));
		return($rs->dado);
	}
}

function pega_usuario_dado($id_usuario, $dado) {
	if ( ($dado!='senha') && ($dado!='senha_sem') ) {
		$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select usuarios.".$dado." as dado from usuarios
												where id_usuario = '$id_usuario' "));
		return($rs->dado);
	}
}

function pega_uf($id_uf) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select uf from ufs where id_uf = '$id_uf' "));
	return($rs->uf);
}

function pega_id_uf($id_cidade) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select id_uf from cidades
											where id_cidade = '$id_cidade'
											"));
	return($rs->id_uf);
}

// --------------------------------------------------------

function inicia_transacao() {
	mysqli_query($GLOBALS["conexao1"], "set autocommit=0;");
	mysqli_query($GLOBALS["conexao1"], "start transaction;");
}

function finaliza_transacao($var) {
	if ($var==0) mysqli_query($GLOBALS["conexao1"], "commit;");
	else mysqli_query($GLOBALS["conexao1"], "rollback;");
}

function gera_auth() {
	return(substr(strtoupper(md5(uniqid(rand(), true))), 0, 24));
}

function tira_caracteres($char) {
	return(str_replace("'", "xxx", str_replace('"', 'xxx', str_replace('/', '', str_replace('.', '', str_replace('-', '', $char))))));
}

function formata_cpf($cpf) {
	$cpfn= substr($cpf, 0, 3) .".". substr($cpf, 3, 3) .".". substr($cpf, 6, 3) ."-". substr($cpf, 9, 2);
	return($cpfn);
}

function pega_horario($horario, $tipo) {
	
	switch($tipo) {
		case 'h': $retorno= substr($horario, 0, 2); break;
		case 'm': $retorno= substr($horario, 3, 2); break;
		case 's': $retorno= substr($horario, 5, 2); break;
	}
	
	return($retorno);
}

function formata_cnpj($cnpj) {
	//99.999.999/9999-99
	//99 999 999 9999 99
	$cnpj= substr($cnpj, 0, 2) .".". substr($cnpj, 2, 3) .".". substr($cnpj, 5, 3) ."/". substr($cnpj, 8, 4) ."-". substr($cnpj, 12, 2);
	return($cnpj);
}

function formata_data($var) {
	$var= explode("/", $var, 3);
	
	if (strlen($var[2])==2)
		$var[2]= "20". $var[2];
		
	$var= $var[2] . $var[1] . $var[0];
	return($var);
}

function formata_data_timestamp($var) {
	$var= explode(" ", $var, 2);
	
	return(desformata_data($var[0]) . " ". $var[1]);
	
}


function formata_data_hifen($var) {
	$var= explode("/", $var, 3);
	
	if (strlen($var[2])==2)
		$var[2]= "20". $var[2];
		
	$var= $var[2] .'-'. $var[1] .'-'. $var[0];
	return($var);
}


function faz_mk_data($var) {
	if (strpos($var, "-")) {
		$var= explode("-", $var, 3);
		$mk= mktime(14, 0, 0, $var[1], $var[2], $var[0]);
		return($mk);
	}
	else {
		$var= explode("/", $var, 3);
		$mk= mktime(14, 0, 0, $var[1], $var[0], $var[2]);
		return($mk);
	}
}

function faz_mk_hora($var) {
	$var= explode(":", $var, 3);
	$mk= mktime($var[0], $var[1], $var[2], 0, 0, 0);
	return($mk);
}

function faz_mk_hora_simples($var) {
	$var= explode(":", $var, 3);
	$mk= (($var[0]*3600)+($var[1]*60)+$var[2]);
	return($mk);
}

function faz_mk_data_completa($var) {
	
	if (strpos($var, "-")) {
		//2008-07-31 13:25:05 
		$data_completa= explode(" ", $var, 2);
		
		$data= explode("-", $data_completa[0], 3);
		$hora= explode(":", $data_completa[1], 3);
		
		$mk= mktime($hora[0], $hora[1], $hora[2], $data[1], $data[2], $data[0]);
	}
	elseif (strpos($var, "/")) {
		//31/07/2008 13:25:05 
		$data_completa= explode(" ", $var, 2);
		
		$data= explode("/", $data_completa[0], 3);
		$hora= explode(":", $data_completa[1], 3);
		
		$mk= mktime($hora[0], $hora[1], $hora[2], $data[1], $data[0], $data[2]);
	}
	
	return($mk);
}

function desformata_data($var) {
	if (($var!="") && ($var!="0000-00-00")) {
		//2006-10-12
		$var= explode("-", $var, 3);
		
		//10/10/2007
		$var= $var[2] .'/'. $var[1] .'/'. $var[0];
		return($var);
	}
	//else
	//	return("<span class='menor vermelho'>não informado</span>");
}

function pega_dia($var) {
	return(substr($var, 6, 2));
}

function pega_mes($var) {
	return(substr($var, 4, 2));
}

function pega_ano($var) {
	return(substr($var, 0, 4));
}

function aumenta_dia($var) {
	//22-10-2007
	$var= explode("-", $var, 3);
	
	$data_ano= date("Y", mktime(0, 0, 0, $var[1], $var[0]+1, $var[2]));
	$data_mes= date("m", mktime(0, 0, 0, $var[1], $var[0]+1, $var[2]));
	$data_dia= date("d", mktime(0, 0, 0, $var[1], $var[0]+1, $var[2]));
	
	$var[0]= $data_dia;
	$var[1]= $data_mes;
	$var[2]= $data_ano;
	
	//2006-10-12
	//$var= $var[2] . $var[1] . $var[0];
	return($var);
}

function soma_mes($var, $valor) {
	
	if (strpos($var, "-")) {
		//2008-07-31
		$data_completa= explode(" ", $var, 2);
		$data= explode("-", $data_completa[0], 3);
		
		$mk= mktime(0, 0, 0, $data[1]+($valor), $data[2], $data[0]);
	}
	elseif (strpos($var, "/")) {
		//31/07/2008
		$data_completa= explode(" ", $var, 2);
		$data= explode("/", $data_completa[0], 3);
		
		$mk= mktime(0, 0, 0, $data[1]+($valor), $data[0], $data[2]);
	}
	
	$var= date("Y-m-d", $mk);
	//2006-10-12
	//$var= $var[2] . $var[1] . $var[0];
	return($var);
}

function formata_valor($var) {

	$var= str_replace(',', '.', str_replace('.', '', $var));
	return($var);
}

function pega_modo_recebimento_convenios_pagos($id_pessoa, $id_clinica) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select modo_recebimento_convenios_pagos from pessoas_clinicas
											where id_pessoa = '$id_pessoa'
											and   id_clinica = '$id_clinica'
											and   status_pc = '1'
											"));
	return($rs->modo_recebimento_convenios_pagos);
}

function traduz_identifica_atendimentos($str) {
	if ($str=='1') return("Somente contador");
	elseif ($str=='2') return("Contador + Nome do paciente");
	else return("Contador + Nome + Prontuário Online");
}

function pega_identifica_atendimentos($id_pessoa, $id_clinica) {
	$rs= mysqli_fetch_object(mysqli_query($GLOBALS["conexao1"], "select identifica_atendimentos from pessoas_clinicas
											where id_pessoa = '$id_pessoa'
											and   id_clinica = '$id_clinica'
											and   status_pc = '1'
											"));
	return($rs->identifica_atendimentos);
}

function desenha_calendario($data_inicio, $id_pessoa, $id_clinica, $data){
	
	$hoje= date('Y-m-d');
	$hoje_mk= faz_mk_data($hoje);
	$hoje_des= date('d/m/Y');
	
	$month= substr($data_inicio, 3, 2);
	$year= substr($data_inicio, 6, 4);
	
	$data_inicio_mes_anterior= desformata_data(soma_mes($data_inicio, -1));
	$data_inicio_proximo_mes= desformata_data(soma_mes($data_inicio, 1));
	
	/* draw table */
	$calendar = '<table class="calendario table-condensed table-striped">';

	/* table headings */
	$headings = array('Dom','Seg','Ter','Qua','Qui','Sex','Sáb');
	
	$mes_anterior='';
	$proximo_mes='';
	
	$calendar.= '<thead>';
	$calendar.= '<tr>
					<th colspan="7">
					<a rel="'. $data_inicio_mes_anterior .'" class="muda_mes btn"><i class="icon-chevron-left"></i></a>
					<a class="interno mes_atual btn active">'. traduz_mes($month) .'/'. $year .'</a>
					<a rel="'. $data_inicio_proximo_mes .'" class="muda_mes btn"><i class="icon-chevron-right"></i></a>
					</th>
				</tr>';
	
	$calendar.= '<tr class="calendar-row"><th class="calendar-day-head">'.implode('</th><th style="width:14.285%;" class="calendar-day-head">',$headings).'</th></tr>';
	$calendar.= '</thead>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		
		$data_aqui= formata_saida($list_day, 2) .'/'. $month .'/'. $year;
		$data_aqui2= $year.'-'. $month .'-'. formata_saida($list_day, 2);
		
		//if (date('d/m/Y')==$data_aqui) $classe_aux='dia_atual ';
		$data_aqui_mk= faz_mk_data($year.'-'. $month .'-'. formata_saida($list_day, 2));
		
		$result_num1= mysqli_query($GLOBALS["conexao1"], "select count(id) as total from atendimentos_uni
									where id_medico = '". $id_pessoa ."'
									and   id_clinica = '". $id_clinica ."'
									and   data = '". formata_data($data_aqui) ."'
									and   status_atendimento = '1'
									") or die(mysqli_error());
		$rs_num1= mysqli_fetch_object($result_num1);
		$total1= $rs_num1->total;
		
		if ($total1>0) $cl_add= "negrito";
		else $cl_add= "";
		
		/*echo "select * from pessoas_clinicas_datas
									where id_pessoa = '". $id_pessoa ."'
									and   id_clinica = '". $id_clinica ."'
									and   data = '". formata_data($data_aqui) ."'
									";*/
		
		$result_num= mysqli_query($GLOBALS["conexao1"], "select * from pessoas_clinicas_datas
									where id_pessoa = '". $id_pessoa ."'
									and   id_clinica = '". $id_clinica ."'
									and   data = '". formata_data_hifen($data_aqui) ."'
									") or die(mysqli_error());
		$rs_num= mysqli_fetch_object($result_num);
		//$total= $rs_num->total;
		
		$antes= '';
		$depois= '';
		
		$icon_cor='';
		
		if ($data==$data_aqui) {
			$hoje='hoje';
			//$icon_cor= 'icon-white';
			/*$antes= 'badge badge-info';
			$icon_cor='white';*/
		}
		else {
			$hoje= '';
			$icon_cor='';
		}
		
		if ($rs_num->terminado=='1') $pre= '<img src="images/cadeado.png" /> '; //'<i class="icon icon-ok '. $icon_cor .'"></i> ';
		else $pre='';
		
		//if ($rs_num->bloqueado=='1') $pre= '<i class="icon icon-lock '. $icon_cor .'"></i> ';
		//else $pre='';
		
		$calendar.= '<td class="calendar-day '. $hoje .'">';
		
		if ($data_aqui_mk<=$hoje_mk)
			$calendar.= '<div class="day-number-no-link '. $cl_add .'"><a class="interno" href="./?pagina=lancamento/lancamento&data='. $data_aqui .'"><span class="clinica_'. $id_clinica .'_dia_'. $data_aqui2 .'">'. $pre .'</span>'. $list_day .'</a></div>';
		else
			$calendar.= '<div class="day-number">'. $pre . $list_day .'</div>';
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	//$calendar.= '<div style="margin-top:10px;max-width:100%;"><a class="btn pull-right btn-info btn-mini" href="./?pagina=lancamento/lancamento&data='.$hoje_des.'#hoje">Ir para o dia de hoje</a></div>';
	
	/* all done, return result */
	return $calendar;
}

function data_extenso_param($data) {
	$data_mk= faz_mk_data($data);
	$data= explode('-', $data);
	
	switch(date('D', $data_mk)) {
		case 'Sun': $data_extenso="Domingo"; break;
		case 'Mon': $data_extenso="Segunda"; break;
		case 'Tue': $data_extenso="Terça"; break;
		case 'Wed': $data_extenso="Quarta"; break;
		case 'Thu': $data_extenso="Quinta"; break;
		case 'Fri': $data_extenso="Sexta"; break;
		case 'Sat': $data_extenso="Sábado"; break;
	}
	$data_extenso .= ", ";
	
	$data_extenso .= $data[2];
	$data_extenso .= " de ";
	
	switch($data[1]) {
		case 1: $data_extenso .= "Janeiro"; break;
		case 2: $data_extenso .= "Fevereiro"; break;
		case 3: $data_extenso .= "Março"; break;
		case 4: $data_extenso .= "Abril"; break;
		case 5: $data_extenso .= "Maio"; break;
		case 6: $data_extenso .= "Junho"; break;
		case 7: $data_extenso .= "Julho"; break;
		case 8: $data_extenso .= "Agosto"; break;
		case 9: $data_extenso .= "Setembro"; break;
		case 10: $data_extenso .= "Outubro"; break;
		case 11: $data_extenso .= "Novembro"; break;
		case 12: $data_extenso .= "Dezembro"; break;
	}
	$data_extenso .= " de ";
	$data_extenso .= $data[0];
	return($data_extenso);
}

function data_param($data) {
	$data_mk= faz_mk_data($data);
	$data= explode('-', $data);
	
	switch(date('D', $data_mk)) {
		case 'Sun': $data_extenso="Domingo"; break;
		case 'Mon': $data_extenso="Segunda"; break;
		case 'Tue': $data_extenso="Terça"; break;
		case 'Wed': $data_extenso="Quarta"; break;
		case 'Thu': $data_extenso="Quinta"; break;
		case 'Fri': $data_extenso="Sexta"; break;
		case 'Sat': $data_extenso="Sábado"; break;
	}
	$data_extenso .= ", ";
	
	$data_extenso .= $data[2] ."/". $data[1] ."/". $data[0];

	return($data_extenso);
}

function traduz_dia($dia) {
	switch($dia) {
		case 0: $retorno= "domingo"; break;
		case 1: $retorno= "segunda"; break;
		case 2: $retorno= "terça"; break;
		case 3: $retorno= "quarta"; break;
		case 4: $retorno= "quinta"; break;
		case 5: $retorno= "sexta"; break;
		case 6: $retorno= "sábado"; break;
	}
	return($retorno);
}

function enviar_email($email, $titulo, $corpo) {
	$enviado= @mail($email, $titulo, $corpo, "From: Prospital.com <prospital@prospital.com> \nContent-type: text/html\n");
}

function pega_tipo_usuario($tipo) {
	switch($tipo) {
		case "a": $retorno= "Administrador"; break;
		case "e": $retorno= "Usuário"; break;
		default: $retorno= ""; break;
	}
	return($retorno);
}
?>