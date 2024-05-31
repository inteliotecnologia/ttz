<?php
require_once("includes/_core/protecao.php");

if ($_SESSION["emula_id_usuario"]!='') {
	$IDENT_id_usuario= $_SESSION["emula_id_usuario"];
	$IDENT_id_pessoa= pega_usuario_dado($IDENT_id_usuario, "id_pessoa");
	$IDENT_nome= pega_usuario_dado($IDENT_id_usuario, "nome");
	
	$IDENT_id_clinica= pega_id_clinica_principal($IDENT_id_pessoa);
}
else {
	$IDENT_id_usuario= $_COOKIE["id_usuario"];
	$IDENT_id_pessoa= $_COOKIE["id_pessoa"];
	$IDENT_nome=$_COOKIE["nome"];
	$IDENT_id_clinica= $_COOKIE["id_clinica"];
}

if (pode("1234", $_COOKIE["perfil"])) {
	
	if (pode("1", $_COOKIE["perfil"])) {
		if ($_GET["chamada"]=="emulaUsuario") {
			$_SESSION["emula_id_usuario"]= $_GET[id_usuario];
			
			header("location: ./");
		}
		
		if ($_GET["chamada"]=="cancelaEmulacao") {
			$_SESSION["emula_id_usuario"]="";
			
			header("location: ./");
		}
	}
	
	if (pode("123", $_COOKIE["perfil"])) {
		if ($_GET["chamada"]=="checaHorario") {
			
			$id_dia= date("w");
			$hora= date("H");
			
			//dia de semana, chegar horas
			if ($id_dia!=0) {
				$tipo_dia=1;
				
				if (($hora>=7) && ($hora<19)) {
					$tipo_hora=1;
				}
				else {
					$tipo_hora=2;
				}
			}
			else {
				$tipo_dia=0;
				$tipo_hora=2;
			}
			
			//$tipo_hora=2;
			
			echo $id_dia ."@|@". $tipo_dia ."@|@". $tipo_hora;
		}
	}
	
	if ( ($_GET["chamada"]!="trocaClinica") && ($_GET["chamada"]!="trocaPlantonista") ) {
		header("Content-type: text/html; charset=utf-8", true);
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
	
	//Admin
	if (pode("1", $_COOKIE["perfil"])) {
		
		
		
		if ($_GET["chamada"]=="arquivoExcluir") {
			
			$apagar= @unlink($_GET["src"]);
			
			if ($apagar) {
				echo "0";
				
				$var=0;
				inicia_transacao();
			
				$result= mysqli_query($conexao1, "update pessoas set foto= ''
									  where id_pessoa= '". $_GET["id_pessoa"] ."'
									  ");
				if (!$result) $var++;
				finaliza_transacao($var);
			}
			else echo "1";
		}
		
		if ($_GET["chamada"]=="usuarioInativar") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "update usuarios set status_usuario = '2'
									where id_usuario= '". $_GET["id"] ."'
									");
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="usuarioAtivar") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "update usuarios set status_usuario = '1'
									where id_usuario= '". $_GET["id"] ."'
									");
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="clinicaExcluir") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "update clinicas set status = '2'
									where id_clinica= '". $_GET["id"] ."'
									");
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="convenioExcluir") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "update convenios set status = '2'
									where id_convenio= '". $_GET["id"] ."'
									");
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			echo $var;
		}
		
	}// Admin
	
	if (pode("123", $_COOKIE["perfil"])) {
		
		if ($_GET["chamada"]=="trocaClinica") {
			
			$erros='';
			if ($_GET[id_clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			if ( ($_COOKIE["perfil"]=="1") || (usuario_esta_na_clinica($_COOKIE[id_pessoa], $_GET[id_clinica])) ) {
				//$_COOKIE[id_clinica]= $_POST[id_clinica];
				
				setcookie("id_clinica", $_GET[id_clinica], TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
				$id_acesso= grava_acesso($_COOKIE[id_usuario], date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $_COOKIE["id_clinica"]);
				
				//$_COOKIE[id_acesso]= $id_acesso;
				setcookie("id_acesso", $id_acesso, TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
				header("location: ./?pagina=lancamento/lancamento&trocado=".$_GET[id_clinica] ."#inicio");
			} else die('Não.');
		}
		
		if ($_GET["chamada"]=="trocaPlantonista") {
			
			$erros='';
			if ($_GET[id_plantonista]=='') $erros.='Plantonista não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			if ( ($_COOKIE["perfil"]=="3") && (plantonista_esta_no_usuario($_GET["id_plantonista"], $_COOKIE["id_usuario"])) ) {
				//$_COOKIE[id_clinica]= $_POST[id_clinica];
				
				setcookie("id_plantonista", $_GET["id_plantonista"], TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
				header("location: ./?pagina=lancamento/lancamento&trocado=".$_GET["id_plantonista"]."#inicio");
			} else die('Não.');
		}
		
		if ($_GET["chamada"]=="alteraValorAtoConvenio") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "update pessoas_clinicas_convenios
										set valor = '". formata_valor($_GET[valor]) ."',
										percentual = '". formata_valor($_GET[percentual_clinica]) ."',
										nome_exibicao_convenio = '". prepara($_GET[nome_exibicao_convenio]) ."',
										label_convenio = '". prepara($_GET["label_convenio"]) ."'
										
										where id_clinica = '". $_COOKIE[id_clinica] ."'
										and   id_ato = '". $_GET[id_ato] ."'
										and   id_convenio = '". $_GET[id_convenio] ."'
										and   ordem = '". $_GET[ordem] ."'
										and   id_pessoa = '". $_COOKIE[id_pessoa] ."'
										limit 1
									") or die(mysqli_error());
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			if ($var==0) {
				echo formata_valor($_GET[valor]) .'@|@'. fnum(formata_valor($_GET[valor])).'@|@'. prepara($_GET[nome_exibicao_convenio]);
			}
		}
		
		if ($_GET["chamada"]=="pesquisaPaciente") {
			
			$retorno= array();
			
			$result= mysqli_query($conexao1, "select *, DATE_FORMAT(data_nasc, '%d/%m/%Y') as data_nasc from pessoas
									where nome like '%". prepara($_GET["query"]) ."%'
									 and   id_usuario = '". $_COOKIE[id_usuario] ."' 
									order by nome asc limit 5
									");
			$num= mysqli_num_rows($result);
			
			$i=0;
			while ($rs= mysqli_fetch_object($result)) {
				$retorno[$i]['id']= $rs->id_pessoa;
				$retorno[$i]['nome']= $rs->nome;
				
				if ($rs->data_nasc!='00/00/0000') {
					$retorno[$i]['data_nasc']= $rs->data_nasc;
					$retorno[$i]['idade']= calcula_idade($rs->data_nasc).' anos';
				}
				else {
					$retorno[$i]['data_nasc']= '-';
					$retorno[$i]['idade']= '';
				}
				
				$i++;
			}
			
			echo(json_encode($retorno));
		}
		
		if ($_GET["chamada"]=="pesquisaProcedimento") {
				
			$retorno= array();
			
			$result= mysqli_query($conexao1, "select * from atos
									where ato like '%". prepara($_GET["query"]) ."%'
									or apelido like '%". prepara($_GET["query"]) ."%'
									or codigo_cbhpm like '%". prepara($_GET["query"]) ."%'
									and   id_ato not IN
									(
									select id_ato from pessoas_clinicas_convenios
									where id_pessoa = '". $IDENT_id_pessoa ."'
									and   id_clinica = '". $IDENT_id_clinica ."'
									and   id_convenio = '-1'
									)
									order by id_ato asc limit 6
									");
			$num= mysqli_num_rows($result);
			
			$i=0;
			while ($rs= mysqli_fetch_object($result)) {
				$retorno[$i]['id_procedimento']= $rs->id_ato;
				$retorno[$i]['codigo_cbhpm']= formata_cbhpm($rs->codigo_cbhpm);
				$retorno[$i]['procedimento']= $rs->ato;
				$retorno[$i]['apelido']= $rs->apelido;
				
				$i++;
			}
			
			echo(json_encode($retorno));
		}
		
		if ($_GET["chamada"]=="pesquisaConvenio") {
			
			
			$result_pessoa_clinica= mysqli_query($conexao1, "select * from pessoas_clinicas
														where  id_pessoa = '". $IDENT_id_pessoa ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   status_pc = '1' 
														") or die(mysqli_error());
			$rs_pessoa_clinica= mysqli_fetch_object($result_pessoa_clinica);
			
			$plantonista= $rs_pessoa_clinica->plantonista;
			$convenio_proprio= $rs_pessoa_clinica->convenio_proprio;
			$identifica_atendimentos= $rs_pessoa_clinica->identifica_atendimentos;
			$modo_recebimento_convenios_pagos= $rs_pessoa_clinica->modo_recebimento_convenios_pagos;
			
			$str_pesquisa="";
			
			if ($convenio_proprio=="0")
				$str_pesquisa.= " and   tipo_convenio <> '3' ";
				
			if ($modo_recebimento_convenios_pagos=="3")
				$str_pesquisa.= " and   tipo_convenio <> '1' ";
				
			$retorno= array();
			
			$result= mysqli_query($conexao1, "select * from convenios
									where convenio like '%". prepara($_GET["query"]) ."%'
									/* and   tipo_convenio = '". $_GET['t'] ."' */
									". $str_pesquisa ."
									order by convenio asc limit 5
									");
			$num= mysqli_num_rows($result);
			
			$i=0;
			while ($rs= mysqli_fetch_object($result)) {
				$retorno[$i]['id_convenio']= $rs->id_convenio;
				$retorno[$i]['convenio']= $rs->convenio;
				$retorno[$i]['tipo_convenio']= pega_tipo_convenio($rs->tipo_convenio);
				
				$i++;
			}
			
			echo(json_encode($retorno));
		}
		
		if ($_GET["chamada"]=="pesquisaClinica") {
			
			$retorno= array();
			
			$result= mysqli_query($conexao1, "select * from clinicas
									where clinica like '%". prepara($_GET["query"]) ."%'
									/* and   tipo_convenio = '". $_GET['t'] ."' */
									order by clinica asc limit 5
									");
			$num= mysqli_num_rows($result);
			
			$i=0;
			while ($rs= mysqli_fetch_object($result)) {
				$retorno[$i]['id_clinica']= $rs->id_clinica;
				$retorno[$i]['clinica']= $rs->clinica;
				
				$i++;
			}
			
			echo(json_encode($retorno));
		}
		
		if ($_GET["chamada"]=="pegaDadosPaciente") {
			
			$retorno= array();
			
			$result= mysqli_query($conexao1, "select *, DATE_FORMAT(data_nasc, '%d/%m/%Y') as data_nasc from pessoas
									where id_pessoa = '". prepara($_GET["id"]) ."'
									order by nome asc limit 1
									");
			$num= mysqli_num_rows($result);
			
			$rs= mysqli_fetch_object($result);
			
			echo($rs->id_pessoa .'@|@'. $rs->nome .'@|@'. $rs->data_nasc .'@|@'. calcula_idade($rs->data_nasc).'@|@'. $rs->sexo.'@|@'. $rs->cpf.'@|@'. $rs->telefone.'@|@'. $rs->telefone2.'@|@'. $rs->email);
		}
		
		if ($_GET["chamada"]=="pegaAtendimento") {
			
			$retorno= array();
			
			$result= mysqli_query($conexao1, "select * from atendimentos_uni
									where id= '". $_GET[id] ."'
									and   id_clinica = '". $_COOKIE[id_clinica] ."'
									and   id_medico = '". $_COOKIE[id_pessoa] ."'
									and   status_atendimento = '1'
									limit 1
									");
			$num= mysqli_num_rows($result);
			if ($num==1) {
				$rs= mysqli_fetch_object($result);
				
				echo($rs->tipo_atendimento .'@|@'. $rs->id_paciente .'@|@'.
				 $rs->id_ato .'@|@'.  $rs->id_convenio .'@|@'.  $rs->tipo_convenio .'@|@'.  $rs->ordem.'@|@'. 
				  $rs->recebimento .'@|@'.  desformata_data($rs->data) .'@|@'.  $rs->hora .'@|@'.  $rs->valor_unitario .'@|@'.
				  $rs->valor_total .'@|@'.$rs->recebido_valor_pessoa .'@|@'.$rs->recebido_valor_clinica .'@|@'.$rs->vai_receber_valor_pessoa .'@|@'.
				  $rs->vai_receber_valor_clinica .'@|@'.$rs->pessoa_deve .'@|@'.$rs->clinica_deve .'@|@'.$rs->por_direito_valor_pessoa .'@|@'.
				  $rs->por_direito_valor_clinica .'@|@'.$rs->percentual_clinica .'@|@'.$rs->percentual_medico .'@|@'.$rs->modo_recebimento_convenios_pagos .'@|@'.
				  $rs->anamnese.'@|@'. $rs->id.'@|@'. pega_ato($rs->id_ato).'@|@'. pega_convenio($rs->id_convenio).'@|@R$'. fnumf($rs->valor_total)
				  .'@|@'. $rs->editado_infos
				  );
			 }
		}
		
		if ($_GET["chamada"]=="pegaTotalLinha") {
			
			$result_count= mysqli_query($conexao1, "select count(id) as total
	        							from atendimentos_uni
										where id_medico = '". $_COOKIE[id_pessoa] ."'
										and   id_clinica = '". $_COOKIE[id_clinica] ."'
										and   id_convenio = '". $_GET[id_convenio] ."'
										and   id_ato = '". $_GET[id_ato] ."'
										and   data = '". formata_data($_GET[data]) ."'
										and   ordem = '". $_GET[ordem] ."'
										and   tipo_atendimento = '1'
										and   status_atendimento = '1'
										") or die(mysqli_error());
			$rs_count= mysqli_fetch_object($result_count);
			$num_count= $rs_count->total;
			
			echo $num_count;
		}
		
		if ($_GET["chamada"]=="editaProcedimento") {
			
			$result_proc= mysqli_query($conexao1, "select * from pessoas_clinicas_convenios
									where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
									and   id_clinica = '". $_COOKIE["id_clinica"] ."'
									and   id_ato = '". $_GET["id_procedimento"] ."'
									and   id_convenio = '-1'
									") or die(mysqli_error());
			$num_proc= mysqli_num_rows($result_proc);
			$rs_proc= mysqli_fetch_object($result_proc);
			
			?>
			<input type="hidden" name="data" id="nc_data" value="<?=$_GET["data"];?>" />
			<input type="hidden" name="id_procedimento" id="nc_id_procedimento" value="<?=$rs_proc->id_ato;?>" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5 id="nc_modal_label">Procedimento</h5>
			</div>
				
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span5">
						<label class="muted menor" for="nc_nome_exibicao_procedimento">Procedimento:</label>
						
						<?=pega_ato($rs_proc->id_ato);?>
					</div>
					
					<div class="span3">
						<label class="muted menor" for="nc_codigo_cbhpm">Código CBHPM:</label>
						
						<?=pega_cod_ato($rs_proc->id_ato);?>
					</div>
					
					<div class="span4">
						<label class="muted menor" for="nc_apelido">Apelido:</label>
						
						<input class="input-block-level" autocomplete="off" type="text" name="apelido" id="nc_apelido" value="<?=$rs_proc->nome_exibicao_procedimento;?>" placeholder="Opcional" />
					</div>
					
				</div>
				
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
			<?
		}
		
		if ($_GET["chamada"]=="pegaHorarioPlantao") {
			
			if ($_COOKIE["perfil"]=="3") $str_add= " and   id_plantonista = '". $_COOKIE["id_plantonista"] ."' ";
			
			$result_plantao= mysqli_query($conexao1, "select * from pessoas_clinicas_plantoes
									where id_pcp = '". $_GET["id_pcp"] ."'
									and   id_pessoa = '". $_COOKIE["id_pessoa"] ."'
									and   id_clinica = '". $_COOKIE["id_clinica"] ."'
									and   status_batida = '1'
									". $str_add ."
									") or die(mysqli_error());
			$num_plantao= mysqli_num_rows($result_plantao);
			
			$rs_plantao= mysqli_fetch_object($result_plantao);
			
		?>
		<input type="hidden" name="id_pcp" id="id_pcp" value="<?=$rs_plantao->id_pcp;?>" />
		<input type="hidden" name="tipo_batida" id="tipo_batida" value="<?=$rs_plantao->tipo_batida;?>" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5>Editar horário</h5>
			</div>
			
			<div class="modal-body">
				
				<div class="row-fluid">
					<div class="span3">
						<label>Horário:</label>
						
						<input type="text" class="input-block-level" placeholder="Horário" name="hora" id="plantao_hora_edita" value="<?=$rs_plantao->hora;?>" />
						
					</div>
					<div class="span6 offset3 ">
						
						<br /><br />
						
						<a data-id_pcp="<?=$rs_plantao->id_pcp;?>" class="btn btn-mini btn-danger exclui_horario" href="javascript:void(0);" onclick="return confirm('Deseja apagar este horário?');">
			                <i class=" icon-trash"></i> Apagar horário
			            </a>
						
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
		<?
		}
		
		if ($_GET["chamada"]=="pegaAtendimentos") {
			
			if ($_GET[id_paciente]!='') {
				$str=" and   atendimentos_uni.id_paciente= '". $_GET[id_paciente] ."'
						
						";
			}
			else {
				$str=" and   ( atendimentos_uni.data= '". formata_data_hifen($_GET[data]) ."' or atendimentos_uni.data= '". formata_data_hifen(soma_data($_GET[data], 1, 0, 0)) ."' )
					   and   atendimentos_uni.id_medico = '". $_COOKIE[id_pessoa] ."'
					   and   atendimentos_uni.id_ato = '". $_GET[id_ato] ."'
					   and   atendimentos_uni.id_convenio = '". $_GET[id_convenio] ."'
					   and   atendimentos_uni.ordem = '". $_GET[ordem] ."'
						";
			}
			
			$result= mysqli_query($conexao1, "select * from atendimentos_uni, pessoas
									where 1 = 1
									$str
									and   atendimentos_uni.id_clinica = '". $_COOKIE[id_clinica] ."'
									and   pessoas.id_pessoa = atendimentos_uni.id_paciente
									and   atendimentos_uni.status_atendimento = '1'
									order by data desc, hora desc
									") or die(mysqli_error());
			$num= mysqli_num_rows($result);
			
			?>
			
			<?
			if ($_GET[id_paciente]!='') {
				$result_paciente= mysqli_query($conexao1, "select *, DATE_FORMAT(data_nasc, '%d/%m/%Y') as data_nasc from pessoas
												where id_pessoa = '". prepara($_GET[id_paciente]) ."'
												order by nome asc limit 1
												");
				$num_paciente= mysqli_num_rows($result_paciente);
				
				$rs_paciente= mysqli_fetch_object($result_paciente);
			?>
			<div class="row-fluid">
				<div class="span9">
					<h5 style="margin:0 0 18px 0;"><?=$rs_paciente->nome;?> <? if ($rs_paciente->data_nasc!='00/00/0000') { ?> &nbsp; <small><?=calcula_idade($rs_paciente->data_nasc); ?> anos</small> <? } ?></h5>
				</div>
				<div class="span3">
					<button style="margin-top:2px;" type="button" class="btn-mini btn-info edita_paciente pull-right" rel="<?=$rs_paciente->id_pessoa;?>">
						<i class="icon-pencil"></i> edita
					</button>
				</div>
			</div>
			<div class="well well-small ficha">
				<div class="row-fluid muted menor">
					<div class="span4">
						<? if ($rs_paciente->data_nasc!='00/00/0000') { ?>
						<?= ($rs_paciente->data_nasc); ?> 
						<? } else echo '-'; ?>
					</div>
					<div class="span8">
						<?=$rs_paciente->cpf;?>
					</div>
				</div>
				<div class="row-fluid muted menor">
					<div class="span4">
						<?= ($rs_paciente->telefone); ?>
					</div>
					<div class="span4">
						<?= ($rs_paciente->telefone2); ?>
					</div>
					<div class="span4">
						<?= ($rs_paciente->email); ?>
					</div>
					
				</div>
			</div>
			
			<? } else { ?>
			<div class="row-fluid">
				<div class="span12">
					<h5 style="margin:0;"><?=pega_convenio($_GET[id_convenio]);?></h5>
					<p style="margin-bottom:20px;" class="muted"><?=pega_clinica($_COOKIE[id_clinica]);?></p>
				</div>
			</div>
			<? } ?>
			
			
			<p class="menor"><strong><?=fnumi($num);?></strong> atendimento<? if ($num!=1) { ?>s<? } ?>.</p>
			
			<hr />
			
			<?
			
			if ($num==0) echo "<p style='margin-top:30px;'>Nenhum atendimento identificado.</p>";
			else {
			while ($rs= mysqli_fetch_object($result)) {
			?>
			<div class="media">
				<div class="media-body">
					<div class="row-fluid">
		            	<div class="span8 menor">
		            		<? if ($_GET[id_paciente]!='') { ?>
		            		<span class="menor muted"><?=pega_clinica($rs->id_clinica);?></span> &nbsp;
		            		<? } ?>
		            		
		            		<? //if ($_GET[id_paciente]=='') { ?>
							<? if ($rs->id_ato!='1') { ?>
							<span class="label label-mini"><?= pega_ato($rs->id_ato); ?></span> &nbsp;
							<? } ?>
							
							<? if ($_COOKIE["perfil"]!="3") { ?>
							<span class="label label-mini label-tipo_atendimento-<?=$rs->tipo_atendimento;?>"><?=pega_tipo_atendimento($rs->tipo_atendimento); ?></span> &nbsp; <? } ?>
							
							<? if ($_GET[id_paciente]!='') { ?>
							<span class="menor muted"><?=pega_convenio($rs->id_convenio);?></span>
							<? } ?>
							<? //} ?>
							
		            	</div>
		            	<div class="span4 text-right muted menor">
		            		<small>
		            		<?
		            		$data_mk= faz_mk_data($rs->data);
		            		$id_dia= date("w", $data_mk);
		            		echo desformata_data($rs->data); ?> às <?= substr($rs->hora, 0, 5);
		            		?>
		            		<small>(<?=traduz_dia($id_dia);?>)</small></small>
		            	</div>
		            </div>
		            
		            <? //if ($_GET[id_paciente]=='') { ?>
		            <a class="pull-right btn btn-mini btn-danger apaga_atendimento" href="javascript:void(0);" data-linha="<?=$_GET[id_ato];?>_<?=$_GET[i];?>" data-id="<?=$rs->id;?>" data-id_ato="<?=$rs->id_ato;?>" data-id_convenio="<?=$rs->id_convenio;?>" data-ordem="<?=$rs->ordem;?>" data-i="<?=$_GET[i];?>" data-t="<?=$_GET[t];?>">
		                <i class="icon-trash"></i> apaga
		            </a>
		            
		            <a data-html="true" data-placement="left" title="<?= nl2br($rs->e1ditado_infos);?>" style="margin-right:5px;" class="tt pull-right btn btn-mini btn-info edita_atendimento" href="javascript:void(0);" data-id_paciente="<?=$_GET[id_paciente];?>" data-linha="<?=$_GET[id_ato];?>_<?=$_GET[i];?>" data-id="<?=$rs->id;?>" data-id_ato="<?=$rs->id_ato;?>" data-id_convenio="<?=$rs->id_convenio;?>" data-ordem="<?=$rs->ordem;?>" data-i="<?=$_GET[i];?>" data-t="<?=$_GET[t];?>">
		                <i class="icon-pencil"></i> edita
		            </a>
		            <? //} ?>
		            
		            <? if ($_GET[id_paciente]=='') { ?>
					<h6 style="margin-top:0;"><a class="transfere_paciente" data-linha="<?=$_GET[id_ato];?>_<?=$_GET[i];?>" data-id="<?=$rs->id;?>" data-id_ato="<?=$rs->id_ato;?>" data-id_convenio="<?=$rs->id_convenio;?>" data-ordem="<?=$rs->ordem;?>" data-i="<?=$_GET[i];?>" data-t="<?=$_GET[t];?>" data-id_paciente="<?= $rs->id_pessoa; ?>" href="javascript:void(0);"><?= $rs->nome; ?></a> <? if ($rs->data_nasc!='0000-00-00') { ?> &nbsp; <small class="muted"><?= calcula_idade(desformata_data($rs->data_nasc)); ?> anos</small> <? } ?></h6>
					<? }?>
					
					
					<small class="menor" style="line-height: 17px;"><? if ($_GET[id_paciente]!='') { ?>
						<span class=""><strong>Dr. <?= pega_pessoa($rs->id_medico); ?></strong></span>
						<br /> <? } ?>
						
						<? if ($_GET[id_paciente]!='') { ?>
						<?=nl2br($rs->anamnese);?>
						<? } ?>
					</small>
					
					<? /*
					
					*/ ?>
					
				</div>
		    </div>
		    <hr />
		    <script>
		    	
		    </script>
			<?
			} }
		}
		
		if ($_GET["chamada"]=="silenciar") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "update usuarios set silenciar_sons = '". $_GET[silenciar_sons] ."'
									where id_usuario= '". $_COOKIE["id_usuario"] ."'
									");
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="entraSaiPlantao") {
			
			//$data= $_GET[data];
			
			$erros='';
			if ($_COOKIE[id_clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
			//if ($data=='') $erros.='Data não pode estar em branco.<br>';
			
			if ($erros=='') {
				$var=0;
				inicia_transacao();
				
				/*$result_pcd= mysqli_query($conexao1, "select * from anotacoes
											where  id_pessoa = '". $_COOKIE[id_pessoa] ."'
											and    id_clinica = '". $_COOKIE[id_clinica] ."'
											and    data = '". formata_data($data) ."'
											") or die(mysqli_error());
				$num_pcd= mysqli_num_rows($result_pcd);
				
				if ($num_pcd==0) {*/
					
					$data_plantao= date("Y-m-d");
					$hora_plantao= date("H:i:s");
					
					$result= mysqli_query($conexao1, "insert into pessoas_clinicas_plantoes (id_pessoa, id_clinica, vale_dia,
																data, hora, tipo_batida, id_acesso, status_batida, id_usuario, id_plantonista)
											values
											('". $_COOKIE[id_pessoa] ."', '". $_COOKIE[id_clinica] ."', '". formata_data($_GET["vale_dia"]) ."',
											'". $data_plantao ."', '". $hora_plantao ."', '". $_GET["tipo_batida"] ."', '". $_COOKIE[id_acesso] ."', '1', '". $_COOKIE[id_usuario] ."', '". $_COOKIE["id_plantonista"] ."' )
											");
					if (!$result) $var++;
					$id_pcp= mysqli_insert_id($conexao1);
				/*}
				else {
					$result= mysqli_query($conexao1, "update anotacoes set
											anotacao = '". prepara($_GET[anotacao]) ."',
											ultima_alteracao = '". date("Y-m-d H:i:s") ."'
											where  id_pessoa = '". $_COOKIE[id_pessoa] ."'
											and    id_clinica = '". $_COOKIE[id_clinica] ."'
											and    data = '". formata_data($data) ."'
											");
					if (!$result) $var++;
				}*/
				
				finaliza_transacao($var);
				echo $var .'@|@'. $data_plantao .'@|@'. $hora_plantao .'@|@'. converte_data_completa_utc($data_plantao .' '. $hora_plantao) .'@|@'. $id_pcp;
			}
			else {
				echo '<h4>Não foi possível completar a operação:</h4><br>'. $erros;
			}
		}
		
		if ($_GET["chamada"]=="salvaAnotacao") {
			
			$data= $_GET[data];
			
			$erros='';
			if ($_COOKIE[id_clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
			if ($data=='') $erros.='Data não pode estar em branco.<br>';
			
			if ($erros=='') {
				$var=0;
				inicia_transacao();
				
				$result_pcd= mysqli_query($conexao1, "select * from anotacoes
											where  id_pessoa = '". $_COOKIE[id_pessoa] ."'
											and    id_clinica = '". $_COOKIE[id_clinica] ."'
											and    data = '". formata_data($data) ."'
											") or die(mysqli_error());
				$num_pcd= mysqli_num_rows($result_pcd);
				
				if ($num_pcd==0) {
					$result= mysqli_query($conexao1, "insert into anotacoes (id_pessoa, id_clinica, data, 
																anotacao, id_acesso, ultima_alteracao)
											values
											('". $_COOKIE[id_pessoa] ."', '". $_COOKIE[id_clinica] ."', '". formata_data($data) ."',
											'". prepara($_GET[anotacao]) ."', '". $_COOKIE[id_acesso] ."', '". date("Y-m-d H:i:s") ."')
											");
					if (!$result) $var++;
				}
				else {
					$result= mysqli_query($conexao1, "update anotacoes set
											anotacao = '". prepara($_GET[anotacao]) ."',
											ultima_alteracao = '". date("Y-m-d H:i:s") ."'
											where  id_pessoa = '". $_COOKIE[id_pessoa] ."'
											and    id_clinica = '". $_COOKIE[id_clinica] ."'
											and    data = '". formata_data($data) ."'
											");
					if (!$result) $var++;
				}
				
				finaliza_transacao($var);
				echo '@'. $var;
			}
			else {
				echo '<h4>Não foi possível completar a operação:</h4><br>'. $erros;
			}
		}
		
		if ($_GET["chamada"]=="excluiHorarioPlantao") {
			
			$var=0;
			inicia_transacao();
			
			if ($_COOKIE["perfil"]=="3") $str_add= " and   id_plantonista = '". $_COOKIE["id_plantonista"] ."' ";
			
			$result_apaga= mysqli_query($conexao1, "update pessoas_clinicas_plantoes
										set status_batida = '0'
										where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   id_pcp = '". prepara($_GET["id_pcp"]) ."'
										and   id_clinica = '". $_COOKIE["id_clinica"] ."'
										". $str_add ."
										") or die("3: ". mysqli_error());
			if (!$result_apaga) $var++;
			$linhas_apaga= mysqli_affected_rows($conexao1);
			
			if ($linhas_apaga==0) {
				$var++;
				
				$erros.='Não foi possível excluir o registro.<br>';
			}
			
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="excluiProcedimentoPessoaClinica") {
			$var=0;
			inicia_transacao();
			$result_apaga= mysqli_query($conexao1, "delete from pessoas_clinicas_convenios
										where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   id_ato = '". prepara($_GET[id_procedimento]) ."'
										and   id_convenio = '-1'
										and   id_clinica = '". $_COOKIE["id_clinica"] ."'
										") or die("3: ". mysqli_error());
			if (!$result_apaga) $var++;
			$linhas_apaga= mysqli_affected_rows($conexao1);
			
			if ($linhas_apaga==0) {
				$var++;
				
				$erros.='Não foi possível excluir o registro.<br>';
			}
			
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="excluiConvenioPessoaClinica") {
			$var=0;
			inicia_transacao();
			$result_apaga= mysqli_query($conexao1, "delete from pessoas_clinicas_convenios
										where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   id_ato = '". prepara($_GET[id_ato]) ."'
										and   id_convenio = '". prepara($_GET[id_convenio]) ."'
										and   id_clinica = '". $_COOKIE["id_clinica"] ."'
										") or die("3: ". mysqli_error());
			if (!$result_apaga) $var++;
			$linhas_apaga= mysqli_affected_rows($conexao1);
			
			/*if ($linhas_apaga==0) {
				$var++;
				
				$erros.='Não foi possível excluir o registro.<br>';
			}*/
			
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="carregaProcedimentos") {
			
			$result_pessoa_clinica= mysqli_query($conexao1, "select * from pessoas_clinicas
														where  id_pessoa = '". $IDENT_id_pessoa ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   status_pc = '1' 
														") or die(mysqli_error());
			$rs_pessoa_clinica= mysqli_fetch_object($result_pessoa_clinica);
			
			$plantonista= $rs_pessoa_clinica->plantonista;
			$convenio_proprio= $rs_pessoa_clinica->convenio_proprio;
			$identifica_atendimentos= $rs_pessoa_clinica->identifica_atendimentos;
			$modo_recebimento_convenios_pagos= $rs_pessoa_clinica->modo_recebimento_convenios_pagos;
			
			$a=0;
			gera_tela_lancamento_procedimentos($_GET["data"], $IDENT_id_pessoa, $IDENT_id_clinica, $a, $_SESSION["emula_id_usuario"]);
		}
		
		if ($_GET["chamada"]=="carregaAtoTipoConvenio") {
			
			$result_pessoa_clinica= mysqli_query($conexao1, "select * from pessoas_clinicas
														where  id_pessoa = '". $IDENT_id_pessoa ."'
														and   id_clinica = '". $IDENT_id_clinica ."'
														and   status_pc = '1' 
														") or die(mysqli_error());
			$rs_pessoa_clinica= mysqli_fetch_object($result_pessoa_clinica);
			
			$plantonista= $rs_pessoa_clinica->plantonista;
			$convenio_proprio= $rs_pessoa_clinica->convenio_proprio;
			$identifica_atendimentos= $rs_pessoa_clinica->identifica_atendimentos;
			$modo_recebimento_convenios_pagos= $rs_pessoa_clinica->modo_recebimento_convenios_pagos;
			
			gera_tela_lancamento_ato($_GET[data], $_COOKIE[id_pessoa], $_COOKIE[id_clinica], $_GET[id_ato], $_GET[a], '');
		}
		
		if ($_GET["chamada"]=="apagaAtendimento") {
			$var=0;
			inicia_transacao();
			
										
			$result_apaga= mysqli_query($conexao1, "update atendimentos_uni
										set status_atendimento = '2'
										where id_medico = '". $_COOKIE["id_pessoa"] ."'
										and   id = '". prepara($_GET[id]) ."'
										and   id_clinica = '". $_COOKIE["id_clinica"] ."'
										") or die("3: ". mysqli_error());
			if (!$result_apaga) $var++;
			$linhas_apaga= mysqli_affected_rows($conexao1);
			
			if ($linhas_apaga==0) {
				$var++;
				
				$erros.='Não foi possível excluir o registro.<br>';
			}
			
			finaliza_transacao($var);
			
			echo $var;
		}
		
		if ($_GET["chamada"]=="atualiza") {
			
			$var=0;
			inicia_transacao();
			
			//echo 'qwe:'.$_POST[paciente_atendimento_id];
			
			if ( ($_POST[paciente_atendimento_id]!='') && ($_POST[edicao]=='1') ) {
				$result_apaga= mysqli_query($conexao1, "update atendimentos_uni
											set status_atendimento = '2'
											where id_medico = '". $_COOKIE["id_pessoa"] ."'
											and   id = '". prepara($_POST[paciente_atendimento_id]) ."'
											and   id_clinica = '". $_COOKIE["id_clinica"] ."'
											") or die("3: ". mysqli_error());
				if (!$result_apaga) $var++;
				
				$hora= $_POST['hora'];
			}
			else {
				$hora= date('H:i:s');
			}
			
			if ($_GET[tipo_atendimento]!='') $tipo_atendimento= $_GET[tipo_atendimento];
			else $tipo_atendimento= $_POST[tipo_atendimento];
			
			//1 consulta 2 retorno
			if ($tipo_atendimento=='') $tipo_atendimento='1';
			
			if ($_GET[modo]!='') $modo= $_GET[modo];
			else $modo= $_POST[modo];
			
			if ($_GET[data]!='') $data= $_GET[data];
			else $data= $_POST[data];
			
			if ($_COOKIE["perfil"]=="3") $data= date("d/m/Y");
			
			$id_clinica= $_COOKIE[id_clinica];//$_GET[id_clinica];
			
			if ($_GET[id_paciente]!='') $id_paciente= $_GET[id_paciente];
			else $id_paciente= $_POST[id_paciente];
			
			if ($_GET[modo_recebimento_convenios_pagos]!='') $modo_recebimento_convenios_pagos= $_GET[modo_recebimento_convenios_pagos];
			else $modo_recebimento_convenios_pagos= $_POST[modo_recebimento_convenios_pagos];
			
			if ($_GET[id_ato]!='') $id_ato= $_GET[id_ato];
			else $id_ato= $_POST[id_ato];
			
			if ($_GET[id_convenio]!='') $id_convenio= $_GET[id_convenio];
			else $id_convenio= $_POST[id_convenio];
			
			if ($_GET[tipo_convenio]!='') $tipo_convenio= $_GET[tipo_convenio];
			else $tipo_convenio= $_POST[tipo_convenio];
			
			if ($_GET[recebimento]!='') $recebimento= $_GET[recebimento];
			else $recebimento= $_POST[recebimento];
			
			if ($_GET[valor]!='') $valor= $_GET[valor];
			else $valor= $_POST[valor];
			
			if ($_GET[ordem]!='') $ordem= $_GET[ordem];
			else $ordem= $_POST[ordem];
			
			if ($_GET[percentual_clinica]!='') $percentual_clinica= $_GET[percentual_clinica];
			else $percentual_clinica= $_POST[percentual_clinica];
			
			if ($_GET[percentual_medico]!='') $percentual_medico= $_GET[percentual_medico];
			else $percentual_medico= $_POST[percentual_medico];
			
			if ($_GET[anamnese]!='') $anamnese= $_GET[anamnese];
			else $anamnese= $_POST[anamnese];
			
			if ($_GET[editado_infos]!='') $editado_infos= $_GET[editado_infos];
			else $editado_infos= $_POST[editado_infos];
			
			$erros='';
			if ($data=='') $erros.='Data não pode estar em branco.<br>';
			if (!usuario_esta_na_clinica($_COOKIE[id_pessoa], $id_clinica)) $erros.='Clínica inválida.<br>';
			if ($id_ato=='') $erros.='Ato não pode estar em branco.<br>';
			if ($id_convenio=='') $erros.='Convênio não pode estar em branco.<br>';
			if ($tipo_convenio=='') $erros.='Tipo de convênio não pode estar em branco.<br>';
			if ($recebimento=='') $erros.='Recebimento não pode estar em branco.<br>';
			if ($valor=='') $erros.='Valor não pode estar em branco.<br>';
			if ($ordem=='') $erros.='Ordem não pode estar em branco.<br>';
			if ($percentual_clinica==='') $erros.='Percentual da clínica não pode estar em branco.<br>';
			if ($percentual_medico==='') $erros.='Percentual do médico não pode estar em branco.<br>';
			//if ( ($lancamento_quantidade=='') && ($modo=='1') ) $erros.='Quantidade não pode estar em branco.<br>';
			
			//echo $percentual_clinica ." | ". $percentual_medico;
			
			if ($erros!='')  {
				$var++;
			}
			else {
				$ultima_alteracao= date('Y-m-d H:i:s');
				$ultima_alteracao_hora= date('H:i:s');
						
				//apagar a linha
				if ( ($modo=='0') || ($modo=='2') ) {
					
					$result_apaga= mysqli_query($conexao1, "update atendimentos_uni
												set status_atendimento = '2'
												where id_medico = '". $_COOKIE["id_pessoa"] ."'
												and   data= '". formata_data_hifen($data) ."'
												and   id_clinica = '". $id_clinica ."'
												/* and   modo_recebimento_convenios_pagos = '". $modo_recebimento_convenios_pagos ."' */
												and   id_ato = '". $id_ato ."'
												and   id_convenio = '". $id_convenio ."'
												and   tipo_convenio = '". $tipo_convenio ."'
												and   recebimento = '". $recebimento ."'
												/*and   valor_unitario = '". $valor ."'*/
												and   ordem = '". $ordem ."'
												/*and   percentual_clinica =  '". $percentual_clinica ."' */
												/* and   percentual_medico = '". $percentual_medico ."' */
												") or die("3: ". mysqli_error());
					if (!$result_apaga) $var++;
					$linhas_apaga= mysqli_affected_rows($conexao1);
					
					/*if ($linhas_apaga==0) {
						$var++;
						
						$erros.='Não foi possível excluir o registro.<br>';
					}*/
					
				}
				//inserir ou atualizar
				
				if ($modo!='0') {
					
					//echo $linhas_pre;
					
					//data: data, modo_recebimento_convenios_pagos: modo_recebimento_convenios_pagos, id_ato: id_ato, id_convenio: id_convenio, tipo_convenio: tipo_convenio, recebimento: recebimento, valor: valor, percentual_clinica: percentual_clinica, percentual_medico: percentual_medico, lancamento_quantidade: lancamento_quantidade
					//echo 1;
					
					//valor total dos atendimentos = quantidade de atendimentos * valor do ato
					//echo $valor_total; die();
					
					
					//retorno
					if ($tipo_atendimento=='2') {	
						$valor_total=0;
					}
					//consulta (cobrado)
					else {
						$valor_total= ($valor);
						$por_direito_valor_clinica= formata_valor(($valor_total*$percentual_clinica)/100);
						$por_direito_valor_pessoa= formata_valor($valor_total-$por_direito_valor_clinica);
						
						//echo $valor_total .'|'. $lancamento_quantidade .'|'. $valor;
						//echo ' '. $por_direito_valor_clinica .'|'. $por_direito_valor_pessoa;
						
						
						switch ($tipo_convenio) {
							//pagos
							case '1':
								//se paga 100% no dia
								if ($modo_recebimento_convenios_pagos=='1') {
									
									//o médico recebe o valor total do ato, se a consulta for R$56,00, ele levará R$56,00 embora
									$recebido_valor_pessoa= ($valor_total);
									//echo $recebido_valor_pessoa; die();
									//a clínica não recebe nada, 0!
									$recebido_valor_clinica= formata_valor(0);
									
									$vai_receber_valor_pessoa=0;
									$vai_receber_valor_clinica=0;
									
									//saldo a receber pela clínica é o valor total dos atendimentos * o percentual acertado entre eles
									//se deu R$100,00 em consultas e a clínica fica com 30%, aqui ficará R$30,00
									$saldo_valor_clinica= formata_valor(($valor_total*$percentual_clinica)/100);
									
									//então a pessoa tem a pagar para a clínica R$30,00... vai salvar no banco -30.00 referente a este(s) atendimento(s)
									$saldo_valor_pessoa= '-'. $saldo_valor_clinica;
									
									$clinica_deve= 0;
									$pessoa_deve= $saldo_valor_clinica;
								}
								//se paga descontado no dia (já acertado)
								elseif ($modo_recebimento_convenios_pagos=='2') {
									
									//já feito as contas de % aqui
									$recebido_valor_clinica= formata_valor(($valor_total*$percentual_clinica)/100);
									$recebido_valor_pessoa= formata_valor($valor_total-$recebido_valor_clinica);
									
									$vai_receber_valor_pessoa=0;
									$vai_receber_valor_clinica=0;
									
									//ninguem deve nada a ninguém referente a estes atendimentos
									$saldo_valor_clinica=0;
									$saldo_valor_pessoa=0;
									
									$clinica_deve= 0;
									$pessoa_deve= 0;
								}
								//se nao paga nada no dia
								elseif ($modo_recebimento_convenios_pagos=='3') {
									
									//o médico não recebe nada
									$recebido_valor_pessoa= formata_valor(0);
									//a clínica fica com tudo
									$recebido_valor_clinica= ($valor_total);
									
									$vai_receber_valor_pessoa=0;
									$vai_receber_valor_clinica=0;
									
									//saldo a receber pelo médico é uma conta feita assim:
									//valor total da consulta * porcentagem que o médico tem direito
									$saldo_valor_pessoa= formata_valor(($valor_total*(100-$percentual_clinica))/100);
									
									//então a clínica precisa pagar 70,00 para o médico
									$saldo_valor_clinica= '-'. $saldo_valor_pessoa;
									
									$clinica_deve= $saldo_valor_pessoa;
									$pessoa_deve= 0;
									
								}
								
							break;
							
							//guia
							case '2':
							//eletronico
							case '3':
								//não interessa salvar isso
								$modo_recebimento_convenios_pagos=0;
								
								//recebimento posterior na conta da clínica
								//entao a clinica precisa pagar o médico posteriormente
								if ($recebimento=='1') {
									
									//o médico não recebe nada
									$recebido_valor_pessoa= formata_valor(0);
									//a clínica fica com tudo
									$recebido_valor_clinica= formata_valor(0);
									
									$vai_receber_valor_pessoa=formata_valor(0);
									$vai_receber_valor_clinica=($valor_total);
									
									//saldo a receber pelo médico é uma conta feita assim:
									//valor total da consulta * porcentagem que o médico tem direito
									$saldo_valor_pessoa= formata_valor(($valor_total*(100-$percentual_clinica))/100);
									
									//echo $valor_total .' | '. $percentual_clinica; die();
									
									//então a clínica precisa pagar 70,00 para o médico
									$saldo_valor_clinica= '-'. $saldo_valor_pessoa;
									
									$clinica_deve= $saldo_valor_pessoa;
									$pessoa_deve= 0;
									
								}
								//recebimento na conta do médico - UNIMED
								//entao o médico precisa devolver a % da clínica
								elseif ($recebimento=='2') {
									
									//o médico recebe o valor total do ato, se a consulta for R$56,00, ele levará R$56,00 embora
									$recebido_valor_pessoa= formata_valor(0);
									//a clínica não recebe nada, 0!
									$recebido_valor_clinica= formata_valor(0);
									
									$vai_receber_valor_pessoa= $valor_total;
									$vai_receber_valor_clinica=formata_valor(0);
									
									//saldo a receber pela clínica é o valor total dos atendimentos * o percentual acertado entre eles
									//se deu R$100,00 em consultas e a clínica fica com 30%, aqui ficará R$30,00
									$saldo_valor_clinica= formata_valor(($valor_total*$percentual_clinica)/100);
									
									//então a pessoa tem a pagar para a clínica R$30,00... vai salvar no banco -30.00 referente a este(s) atendimento(s)
									$saldo_valor_pessoa= '-'. $saldo_valor_clinica;
									
									$clinica_deve= 0;
									$pessoa_deve= $saldo_valor_clinica;
								}
							
							break;
						}
					}
					
					/*$result_pre= mysqli_query($conexao1, "select id from atendimentos
												where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
												and   data= '". formata_data($data) ."'
												and   id_clinica = '". $id_clinica ."'
												and   modo_recebimento_convenios_pagos = '". $modo_recebimento_convenios_pagos ."'
												and   id_ato = '". $id_ato ."'
												and   id_convenio = '". $id_convenio ."'
												and   tipo_convenio = '". $tipo_convenio ."'
												and   recebimento = '". $recebimento ."'
												and   valor_unitario = '". $valor ."'
												and   ordem = '". $ordem ."'
												and   percentual_clinica =  '". $percentual_clinica ."'
												and   percentual_medico = '". $percentual_medico ."'
												and   recebimento = '". $recebimento ."'
												") or die("3: ". mysqli_error());
					if (!$result_pre) $var++;*/
					
					$linhas_pre= 0;
					
					//$recebido_valor_clinica= formata_valor(($valor_total*$_POST[percentual])/100);
					//$recebido_valor_pessoa= formata_valor($valor_total-$recebido_valor_clinica);
					
					//echo ' qtde: '. $_POST["qtde"] .' | '. $_POST[valor] .' | '. $_POST[percentual] .' | '. $valor_clinica .'<br />';
					
					//nao tem nada ainda, vai inserir
					if ($linhas_pre==0) {
						
						$editado_infos= $editado_infos .'Em '. date('d/m/Y \à\s H:i:s') .' por '. pega_pessoa($_COOKIE[id_pessoa]) .'\n';
						
						if ( ($_GET["identifica_atendimentos"]=="1") && ($_GET["modo"]=="2") ) {
							$repeticoes= (int)$_GET["lancamento_quantidade"];	
						}
						else {
							$repeticoes= 1;
						}
						
						for ($r=1; $r<=$repeticoes; $r++) {
						
							$result1= mysqli_query($conexao1, "insert into atendimentos_uni
														(original_id, tipo_atendimento, id_medico, id_plantonista, id_paciente, id_clinica, id_ato,
														id_convenio, tipo_convenio, recebimento,
														data, hora, ordem,
														valor_unitario, recebido_valor_pessoa, recebido_valor_clinica,
														vai_receber_valor_pessoa, vai_receber_valor_clinica,
														pessoa_deve, clinica_deve,
														por_direito_valor_pessoa, por_direito_valor_clinica,
														valor_total, percentual_clinica, percentual_medico,
														modo_recebimento_convenios_pagos, anamnese, editado_infos,
														id_acesso) values
													('0', '". $tipo_atendimento ."', '". $_COOKIE[id_pessoa] ."', '". $_COOKIE["id_plantonista"] ."', '". $id_paciente ."', '". $id_clinica ."', '". $id_ato ."',
													'". $id_convenio ."', '". $tipo_convenio ."', '". $recebimento ."',
													'". formata_data($data) ."', '". $hora ."', '". $ordem ."',
													'". $valor ."', '". $recebido_valor_pessoa ."', '". $recebido_valor_clinica ."',
													'". $vai_receber_valor_pessoa ."', '". $vai_receber_valor_clinica ."',
													'". $pessoa_deve ."', '". $clinica_deve ."',
													'". $por_direito_valor_pessoa ."', '". $por_direito_valor_clinica ."',
													'". $valor_total ."', '". $percentual_clinica ."', '". $percentual_medico ."',
													'". $modo_recebimento_convenios_pagos ."', '". prepara($anamnese) ."', '". prepara($editado_infos) ."',
													'". $_COOKIE[id_acesso] ."' ) ") or die("3.1: ". mysqli_error());
						}
					}
					//já tem, vai atualizar
					/*else {
						$result1= mysqli_query($conexao1, "update atendimentos
												set qtde = '". $lancamento_quantidade ."',
												ultima_alteracao= '". $ultima_alteracao ."',
												modo_recebimento_convenios_pagos = '". $modo_recebimento_convenios_pagos ."',
												
												recebido_valor_pessoa = '". $recebido_valor_pessoa ."',
												recebido_valor_clinica = '". $recebido_valor_clinica ."',
												vai_receber_valor_pessoa = '". $vai_receber_valor_pessoa ."',
												vai_receber_valor_clinica = '". $vai_receber_valor_clinica ."',
												
												pessoa_deve = '". $pessoa_deve ."',
												clinica_deve = '". $clinica_deve ."',
												por_direito_valor_pessoa = '". $por_direito_valor_pessoa ."',
												por_direito_valor_clinica = '". $por_direito_valor_clinica ."',
												
												valor_total = '". $valor_total ."',
												valor_unitario = '". $valor ."',
												percentual_clinica = '". $percentual_clinica ."',
												percentual_medico = '". $percentual_medico ."'
												where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
												and   data= '". formata_data($data) ."'
												and   id_clinica = '". $id_clinica ."'
												and   id_ato = '". $id_ato ."'
												and   id_convenio = '". $id_convenio ."'
												and   tipo_convenio = '". $tipo_convenio ."'
												and   recebimento = '". $recebimento ."'
												and   ordem = '". $ordem ."'
												and   recebimento = '". $recebimento ."'
												and   id = '". $rs_pre->id ."'
												") or die("3.2: ". mysqli_error());
					}*/
					
					if (!$result1) $var++;
				
				}//fim else modo
				
				finaliza_transacao($var);
				
			}//fim else sem erros de validação
			
			
			if ($var==0) {
				echo '@'. fnum($recebido_valor_pessoa) .'@'. fnum($recebido_valor_clinica) .'@'. fnum($vai_receber_valor_pessoa) .'@'. fnum($vai_receber_valor_clinica) .'@'. fnum($pessoa_deve) .'@'. fnum($clinica_deve) .'@'. fnum($por_direito_valor_pessoa) .'@'. fnum($por_direito_valor_clinica) .'@'. date('H:i:s') .'@'. $tipo_atendimento;
			}
			else {
				echo '<h4>Não foi possível completar a operação:</h4><br>'. $erros;
			}
			
		}
		
		if ($_GET["chamada"]=="pegaValorAtoConvenio") {
			
			$result= mysqli_query($conexao1, "select * from convenios, pessoas_clinicas_convenios
										where convenios.id_convenio = pessoas_clinicas_convenios.id_convenio
										and   pessoas_clinicas_convenios.id_clinica = '". $_COOKIE[id_clinica] ."'
										and   pessoas_clinicas_convenios.id_ato = '". $_GET[id_ato] ."'
										and   pessoas_clinicas_convenios.id_convenio = '". $_GET[id_convenio] ."'
										and   pessoas_clinicas_convenios.ordem = '". $_GET[ordem] ."'
										and   pessoas_clinicas_convenios.id_pessoa = '". $_COOKIE[id_pessoa] ."'
									") or die(mysqli_error());
			if (!$result) $var++;
			$rs= mysqli_fetch_object($result);
			
			echo fnum($rs->valor) .'@|@'. fnum($rs->percentual).'@|@'. ($rs->nome_exibicao_convenio).'@|@'. ($rs->convenio);
			
		}
		
		if ($_GET["chamada"]=="mostraDia") {
			
			$result_soma= mysqli_query($conexao1, "select   sum(recebido_valor_pessoa) as recebido_valor_pessoa,
												sum(recebido_valor_clinica) as recebido_valor_clinica,
												sum(vai_receber_valor_pessoa) as vai_receber_valor_pessoa,
												sum(vai_receber_valor_clinica) as vai_receber_valor_clinica,
												sum(pessoa_deve) as pessoa_deve,
												sum(clinica_deve) as clinica_deve,
												sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
												sum(por_direito_valor_clinica) as por_direito_valor_clinica
												from atendimentos_uni
												where data= '". formata_data_hifen($_GET[data]) ."'
												and   id_clinica = '". $_COOKIE[id_clinica] ."'
												and   id_medico = '". $_COOKIE[id_pessoa] ."'
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
												where data= '". formata_data_hifen($_GET[data]) ."'
												and   id_clinica = '". $_COOKIE[id_clinica] ."'
												and   id_medico = '". $_COOKIE[id_pessoa] ."'
												and   tipo_convenio = '2'
												and   status_atendimento = '1'
												") or die(mysqli_error());
			$rs_soma_guias= mysqli_fetch_object($result_soma_guias);
			
			/*
			1 - recebido_valor_pessoa
			2 - recebido_valor_clinica
			3 - vai_receber_valor_pessoa
			4 - vai_receber_valor_clinica
			5 - pessoa_deve
			6 - clinica_deve
			7 - por_direito_valor_pessoa
			8 - por_direito_valor_clinica
			*/
			
			//echo '@R$ '. fnum($rs_soma->recebido_valor_pessoa) .'@R$ '. fnum($rs_soma->recebido_valor_clinica) .'@R$ '. fnum($rs_soma->vai_receber_valor_pessoa) .'@R$ '. fnum($rs_soma->vai_receber_valor_clinica) .'@R$ '. fnum($rs_soma->pessoa_deve) .'@R$ '. fnum($rs_soma->clinica_deve) .'@R$ '. fnum($rs_soma->por_direito_valor_pessoa) .'@R$ '. fnum($rs_soma->por_direito_valor_clinica);
			
			echo '@R$'. fnum($rs_soma->recebido_valor_pessoa) .'@R$'. fnum($rs_soma->vai_receber_valor_pessoa) .'@R$'. fnum($rs_soma_guias->por_direito_valor_pessoa+$rs_soma_guias->por_direito_valor_clinica) .'@R$'. fnum($rs_soma->por_direito_valor_pessoa).'@R$'. fnum($rs_soma->por_direito_valor_clinica+$rs_soma->por_direito_valor_pessoa);
		}
		
		if ($_GET["chamada"]=="mostraTotalAcumulado") {
			
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
												and   id_clinica = '". $_COOKIE[id_clinica] ."'
												and   id_medico = '". $_COOKIE[id_pessoa] ."'
												and   status_atendimento = '1'
												") or die(mysqli_error());
			$rs_soma= mysqli_fetch_object($result_soma_acumulado);
			
			/*
			1 - recebido_valor_pessoa
			2 - recebido_valor_clinica
			3 - vai_receber_valor_pessoa
			4 - vai_receber_valor_clinica
			5 - pessoa_deve
			6 - clinica_deve
			7 - por_direito_valor_pessoa
			8 - por_direito_valor_clinica
			*/
			
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
			
			//echo '@R$ '. fnum($rs_soma->recebido_valor_pessoa) .'@R$ '. fnum($rs_soma->recebido_valor_clinica) .'@R$ '. fnum($rs_soma->vai_receber_valor_pessoa) .'@R$ '. fnum($rs_soma->vai_receber_valor_clinica) .'@R$ '. fnum($rs_soma->pessoa_deve) .'@R$ '. fnum($rs_soma->clinica_deve) .'@R$ '. fnum($rs_soma->por_direito_valor_pessoa) .'@R$ '. fnum($rs_soma->por_direito_valor_clinica);
			
			echo '@R$'. fnum($rs_soma->recebido_valor_pessoa) .'@R$'. fnum($rs_soma->vai_receber_valor_pessoa) .'@R$'. fnum($rs_soma_guias->por_direito_valor_pessoa+$rs_soma_guias->por_direito_valor_clinica) .'@R$'. fnum($rs_soma->por_direito_valor_pessoa).'@R$'. fnum($rs_soma->por_direito_valor_clinica+$rs_soma->por_direito_valor_pessoa).'@'.fnumi($rs_soma_acumulado2->total);
		}
		
		if ($_GET["chamada"]=="mostraDiaConvenio") {
			
			$result_soma= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
												sum(por_direito_valor_clinica) as por_direito_valor_clinica,
												count(id) as qtde
												from atendimentos_uni
												where data= '". formata_data_hifen($_GET[data]) ."'
												and   id_clinica = '". $_COOKIE[id_clinica] ."'
												and   id_medico = '". $_COOKIE[id_pessoa] ."'
												and   id_ato = '". $_GET[id_ato] ."'
												and   tipo_convenio = '". $_GET[tipo_convenio] ."'
												and   status_atendimento = '1'
												") or die(mysqli_error());
			$rs_soma= mysqli_fetch_object($result_soma);
			
			
			
			/*
			1 - recebido_valor_pessoa+recebido_valor_clinica
			2 - total_dia / idem 1
			*/
			
			$result_soma_dia= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
												sum(por_direito_valor_clinica) as por_direito_valor_clinica,
												count(id) as qtde
												from atendimentos_uni
												where data= '". formata_data_hifen($_GET[data]) ."'
												and   id_clinica = '". $_COOKIE[id_clinica] ."'
												and   id_medico = '". $_COOKIE[id_pessoa] ."'
												and   status_atendimento = '1'
												") or die(mysqli_error());
			$rs_soma_dia= mysqli_fetch_object($result_soma_dia);
			
			$result_soma_dia1= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
												sum(por_direito_valor_clinica) as por_direito_valor_clinica,
												count(id) as qtde
												from atendimentos_uni
												where data= '". formata_data_hifen($_GET[data]) ."'
												and   id_clinica = '". $_COOKIE[id_clinica] ."'
												and   id_medico = '". $_COOKIE[id_pessoa] ."'
												and   tipo_atendimento = '1'
												and   status_atendimento = '1'
												") or die(mysqli_error());
			$rs_soma_dia1= mysqli_fetch_object($result_soma_dia1);
			
			$result_soma_dia2= mysqli_query($conexao1, "select   sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
											sum(por_direito_valor_clinica) as por_direito_valor_clinica,
											count(id) as qtde
											from atendimentos_uni
											where data= '". formata_data_hifen($_GET[data]) ."'
											and   id_clinica = '". $_COOKIE[id_clinica] ."'
											and   id_medico = '". $_COOKIE[id_pessoa] ."'
											and   tipo_atendimento = '2'
											and   status_atendimento = '1'
											") or die(mysqli_error());
			$rs_soma_dia2= mysqli_fetch_object($result_soma_dia2);
			
			echo '@R$'. fnum($rs_soma->por_direito_valor_pessoa+$rs_soma->por_direito_valor_clinica) . '@R$'. fnum($rs_soma_dia->por_direito_valor_pessoa+$rs_soma_dia->por_direito_valor_clinica). '@'. fnumi($rs_soma_dia->qtde).'@'. fnumi($rs_soma_dia1->qtde).'@'. fnumi($rs_soma_dia2->qtde).'@R$'. fnum($rs_soma->por_direito_valor_pessoa);
		}
		
		if ($_GET["chamada"]=="geraSetupAto") {
			gera_setup_ato($_COOKIE[id_pessoa], $_GET[id_clinica], $_GET[id_ato], $_GET[ultimo_a], $_GET[ultimo_k]);
		}
		
		if ($_GET["chamada"]=="navegaCalendario") {
			
			if ($_SESSION["emula_id_usuario"]!='') {
				$IDENT_id_usuario= $_SESSION["emula_id_usuario"];
				$IDENT_id_pessoa= pega_usuario_dado($IDENT_id_usuario, "id_pessoa");
				$IDENT_nome= pega_usuario_dado($IDENT_id_usuario, "nome");
				
				
				$IDENT_id_clinica= pega_id_clinica_principal($IDENT_id_pessoa);
			}
			else {
				$IDENT_id_usuario= $_COOKIE["id_usuario"];
				$IDENT_id_pessoa= $_COOKIE["id_pessoa"];
				$IDENT_nome=$_COOKIE["nome"];
				$IDENT_id_clinica= $_COOKIE["id_clinica"];
			}
			
			echo desenha_calendario($_GET[data_inicio], $IDENT_id_pessoa, $IDENT_id_clinica, '');
		}
		
			
		if ($_GET["chamada"]=="desabilitaPessoaClinica") {
			$var=0;
			inicia_transacao();
			
			/*echo "select * from pessoas_clinicas
										where  id_pc = '". $_GET[id] ."'
										and    id_pessoa = '". $_COOKIE[id_pessoa] ."'
										<br><br>";
			*/							
			$result_pc= mysqli_query($conexao1, "select * from pessoas_clinicas
										where  id_pc = '". $_GET[id] ."'
										and    id_pessoa = '". $_COOKIE[id_pessoa] ."'
										") or die(mysqli_error());
			$rs_pc= mysqli_fetch_object($result_pc);
			
			/*
			echo "update pessoas_clinicas
									set status_pc = '2'
									where  id_pc = '". $_GET[id] ."'
									and    id_pessoa = '". $_COOKIE[id_pessoa] ."'
								<br><br>";
			*/
								
			$result= mysqli_query($conexao1, "update pessoas_clinicas
									set status_pc = '2'
									where  id_pc = '". $_GET[id] ."'
									and    id_pessoa = '". $_COOKIE[id_pessoa] ."'
								") or die(mysqli_error());
			if (!$result) $var++;
			
			/*$result_apaga= mysqli_query($conexao1, "delete from pessoas_clinicas_convenios
										where  id_clinica = '". $rs_pc->id_clinica ."'
										and    id_pessoa = '". $_COOKIE[id_pessoa] ."'
									") or die(mysqli_error());
			if (!$result_apaga) $var++;*/
						
			finaliza_transacao($var);
			
			$result_teste= mysqli_query($conexao1, "select * from pessoas_clinicas
											where id_pessoa = '". $_COOKIE[id_pessoa] ."'
											and   status_pc = '1'
											order by id_pc desc limit 1
											");
			$linhas_teste= mysqli_num_rows($result_teste);
			
			//echo 'p: '. $linhas_teste;
			
			if ($linhas_teste==0) {
				//echo '1';
				setcookie ("id_clinica", '', TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
				//$_COOKIE[id_clinica]= '';
				
				$id_acesso= grava_acesso($_COOKIE[id_usuario], date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], $_SERVER[REMOTE_HOST], $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $_COOKIE["id_clinica"]);
				
				setcookie ("id_acesso", $id_acesso, TEMPO_COOKIE, PATH, DOMINIO, false, true);
				//$_COOKIE[id_acesso]= $id_acesso;
				
				//redirecionando
				//echo 'r';
			}
			else {
				//echo '2';
				
				$rs_teste= mysqli_fetch_object($result_teste);
				
				setcookie ("id_clinica", $rs_teste->id_clinica, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			}
			echo $var;
		}
		
		if ($_GET["chamada"]=="setupPessoasClinicasConvenios") {
			
			//se está buscando dados de registro ja'existente
			if ($_GET[id_pc]!='') {
				$result_pessoa_clinica= mysqli_query($conexao1, "select * from clinicas, pessoas_clinicas
															where  pessoas_clinicas.id_pc = '". $_GET[id_pc] ."'
															and    pessoas_clinicas.id_pessoa = '". $_COOKIE[id_pessoa] ."'
															and    clinicas.id_clinica = pessoas_clinicas.id_clinica
															") or die(mysqli_error());
				$rs_pessoa_clinica= mysqli_fetch_object($result_pessoa_clinica);
				?>
				<input type="hidden" name="id_clinica[<?=$_GET[num];?>]" value="<?=$rs_pessoa_clinica->id_clinica;?>" />
				<?
			}
			
			?>
			
			<!--<a class="close" rel="<?=$rs_pessoa_clinica->id_pc;?>" data-dismiss="area_<?=$_GET[num];?>" href="javascript:void(0);">&times;</a>-->
			
			<?
		}//fim chamada
		
		if ($_GET["chamada"]=="apagaMinhaFoto") {
							
			$var=0;
			inicia_transacao();
		
			$result_pre= mysqli_query($conexao1, "select pessoas.foto, pessoas.id_pessoa from pessoas, usuarios
										where pessoas.id_pessoa = usuarios.id_pessoa
										and   pessoas.id_pessoa= '". $_COOKIE["id_pessoa"] ."'
										and   usuarios.hash_usuario = '". $_COOKIE["hash_usuario"] ."'
										limit 1
								  ") or die(mysqli_error());
			
			if (!$result_pre) $var++;
			$rs_pre= mysqli_fetch_object($result_pre);
			
			$apagar= @unlink($rs_pre->foto);
			
			if ($apagar) {
				setcookie ("foto", '', TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
				$result= mysqli_query($conexao1, "update pessoas set foto= ''
								  	where id_pessoa= '". $rs_pre->id_pessoa ."'
								  	");
				if (!$result) $var++;
				finaliza_transacao($var);
				
				echo $var;
			
			}
			else echo "1";
			
			
		}
		
		/*if ($_GET["chamada"]=="diaLimpar") {
			$var=0;
			inicia_transacao();
		
			$result= mysqli_query($conexao1, "delete from atendimentos
									where id_pessoa = '". $_COOKIE[id_pessoa] ."'
									and   id_clinica = '". $_COOKIE[id_clinica] ."'
									and   data= '". $_GET["id"] ."'
									");
			if (!$result) $var++;
		
			finaliza_transacao($var);
			
			echo $var;
		}*/
		
	}//fim   
}
?>