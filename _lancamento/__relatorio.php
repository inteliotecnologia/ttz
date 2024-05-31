<?
if (pode("1234", $_COOKIE["perfil"])) {
	
	if ($_SESSION["emula_id_usuario"]!='') {
		$IDENT_id_usuario= $_SESSION["emula_id_usuario"];
		$IDENT_id_pessoa= pega_usuario_dado($IDENT_id_usuario, "id_pessoa");
		$IDENT_nome= pega_usuario_dado($IDENT_id_usuario, "nome");
		$IDENT_id_clinica= $_COOKIE["id_clinica"];
	}
	else {
		$IDENT_id_usuario= $_COOKIE["id_usuario"];
		$IDENT_id_pessoa= $_COOKIE["id_pessoa"];
		$IDENT_nome=$_COOKIE["nome"];
		$IDENT_id_clinica= $_COOKIE["id_clinica"];
	}
	
	$result_pessoa_clinica= mysqli_query($conexao1, "select * from pessoas_clinicas
												where  id_pessoa = '". $IDENT_id_pessoa ."'
												and   id_clinica = '". $IDENT_id_clinica ."'
												and   status_pc = '1' 
												") or die(mysqli_error());
	$rs_pessoa_clinica= mysqli_fetch_object($result_pessoa_clinica);
	
	$identifica_atendimentos= $rs_pessoa_clinica->identifica_atendimentos;
?>

	<?
	if ($IDENT_id_clinica=='') {
		
		echo '
			<script>
				window.top.location.href="./?pagina=acesso/trabalho_clinicas";
			</script>
			';
		
		//echo '<h3>Nenhuma clínica cadastrada.</h3><p>Cadastre/escolha uma clínica para fazer o registro de atendimentos.</p> <a class="btn btn-large btn-info" href="./?pagina=acesso/trabalho_clinica&acao=i">Cadastrar agora</a>';
	}
	else {
	?>	
	
	<script>
		mixpanel.track("Acessou relatório");
	</script>
	
	<? if ($_SESSION["emula_id_usuario"]!='') { ?>
	<div class="row-fluid">
		<div class="span12">
			<a href="link.php?chamada=cancelaEmulacao" style="display:block;" class="interno btn btn-danger">Emulando <strong><?=$IDENT_nome;?></strong>. Cancelar.</a>
			
			<br />
		</div>
	</div>
	<? } ?>
	
	<? /* Tela de novo procedimento que a pessoa atende (com typeahead) */ ?>
	<div id="modal_repasse" class="modal hide fade" tabindex="-1" role="dialog">
			
		<form id="modal_procedimento_repasse" action="<?=AJAX_FORM;?>formRepasse" method="post">
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5 id="nc_modal_label">Registrar repasse</h5>
			</div>
				
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span6">
						<label class="muted menor" for="data_de">De:</label>
						
						<input class="input-block-level" type="text" name="data_de" id="data_de" value="" required="required" />
					</div>
					
					<div class="span6">
						<label class="muted menor" for="data_ate">Até:</label>
						
						<input class="input-block-level" type="text" name="data_ate" id="data_ate" value="" required="required" />
					</div>
					
				</div>
				
				<div class="row-fluid">
					<div class="span6">
						<label class="muted menor" for="valor_calculado">Valor Calculado:</label>
						
						<input class="input-block-level" type="text" name="valor_calculado" id="valor_calculado" value="" required="required" />
					</div>
					
					<div class="span6">
						<label class="muted menor" for="valor_recebido">Valor Recebido:</label>
						
						<input class="input-block-level" type="text" name="valor_recebido" id="valor_recebido" value="" required="required" />
					</div>
					
				</div>
				
			</div>
				
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="OK">OK</button>
			</div>
		</form>
	</div>
	
	<?
	if ($_POST[intervalo]!='') {
		$datas= explode(' - ', $_POST[intervalo], 2);
		
		$data2= $datas[1];
		$data1= $datas[0];
	}
	elseif ( ($_GET[data1]!='') && ($_GET[data2]!='') ) {
		$data2= $_GET[data2];
		$data1= $_GET[data1];
	}
	else {
		$data2= soma_data(date("d/m/Y"), 1, 0, 0);
		$data1= soma_data($data2, 0, -1, 0);
	}
	
	$data_mk= ajeita_datas($data1, $data2, '');
	
	if ( ($data1!="") && ($data2!="") ) {
	?>
	<div class="visible-print">
		<h4><small><?=$data1;?> - <?=$data2;?></small></h4>
	</div>
	<? } ?>
	
	<? //include("__relatorio_menu.php"); ?>
	
	<div class="page-header">
		<h3 class="hidden-print">
			Relatório por período <Br/>
			<small><?= pega_clinica_pessoa($IDENT_id_pessoa, $IDENT_id_clinica); ?></small>
		</h3>
		<h4>
			<?= $IDENT_nome; ?>
			<small><?=pega_pessoa_dado($_COOKIE[id_pessoa], 'registro'); ?> &bull; <?=pega_especialidade(pega_pessoa_dado($_COOKIE[id_pessoa], 'id_especialidade')); ?></small>
		</h4>
	</div>
	
	<? /*
	<form action="./?pagina=lancamento/relatorio" method="post" class="form_relatorio">
		<div id="mes_escolhe" data-date="<?=$periodo;?>" class="input-append date" data-date-format="mm/yyyy" data-date-viewmode="months" data-date-minviewmode="months">
		    <span class="add-on">
		      <i class="icon-calendar"></i>
		    </span>
		    <input type="text" name="periodo" placeholder="Escolha um mês" value="<?=$periodo;?>" /></input>
		    <button class="btn" type="submit" data-loading-text="...">
		    	<i class="icon-search"></i>
		    </button>
		</div>
	</form>
	*/ ?>
	
	<script type="text/javascript" src="includes/bootstrap-daterangepicker/moment.js"></script>
	<script type="text/javascript" src="includes/bootstrap-daterangepicker/daterangepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="includes/bootstrap-daterangepicker/daterangepicker-bs2.css" />
	
    <script src="includes/select2/select2.js"></script>
    <link rel="stylesheet" type="text/css" href="includes/select2/select2.css"/>
    <link rel="stylesheet" type="text/css" href="includes/select2/select2-bootstrap.css"/>
	
	
	
	<form id="daterange" class="hidden-print well well_relatorio form-horizontal" enctype="multipart/form-data" action="./?pagina=lancamento/relatorio" method="post">
		
	     <div class="row-fluid">
	     	<div class="span5">
	     	
			      <div class="control-group">
			        <small class="muted">Competência</small><br/><br/>
			         
			           <? /*<span class="add-on input-group-addon"><i class="icon icon-calendar"></i></span>*/ ?>
			           <input type="text" name="intervalo" id="intervalo" class="form-control" value="<?=$data1;?> - <?=$data2;?>" /> 
			        
			      </div>
	     	</div>
		 	<div class="span5">
	      
			      <div class="control-group">
			        <small class="muted">Filtrar por convênios</small><br/><br/>
			        
			          <select name="id_convenio[]" id="id_convenio" class="select2" multiple="multiple" style="" placeholder="Todos">
			            
			            <?
			            for ($t=1; $t<4; $t++) {
			            ?>
			            <optgroup label="<?= strip_tags(pega_tipo_convenio($t)); ?>">
			            
			            <?
			            $count_post_convenio= count($_POST[id_convenio]);
			            
			            $result_convenio= mysqli_query($conexao1, "select distinct(convenios.id_convenio) from convenios, pessoas_clinicas_convenios
															where  convenios.id_convenio = pessoas_clinicas_convenios.id_convenio
															and   pessoas_clinicas_convenios.id_pessoa = '". $_COOKIE[id_pessoa] ."'
															and   pessoas_clinicas_convenios.id_clinica = '". $_COOKIE[id_clinica] ."'
															/* and   pessoas_clinicas_convenios.id_ato = '". $id_ato ."' */
															and   convenios.tipo_convenio = '". $t ."'
															order by convenio asc, valor asc
															") or die(mysqli_error());
						$linhas_convenio= mysqli_num_rows($result_convenio);
						
						$c=0;
				        while ($rs_convenio= mysqli_fetch_object($result_convenio)) {
				        	
				        	$selected="";
				        	
				        	if ($count_post_convenio>0) {
					        	
					        	$i=0;
					        	
					        	while ($_POST[id_convenio][$i]!='') {
						        	
						        	if ($_POST[id_convenio][$i]==$rs_convenio->id_convenio) {
						        		$selected="selected='selected'";
						        		break;
						        	}
						        	
						        	$i++;
					        	}
					        	
				        	}
				        	
			            ?>
				            <option <?=$selected;?> value="<?=$rs_convenio->id_convenio;?>"><?=pega_convenio($rs_convenio->id_convenio);?></option>
				        <?
				        }
				        ?>
			            </optgroup>
			            <? } ?>
			            
			            
			          </select>
			       
			      </div>
		 	</div>
		 	<div class="span2">
		 		<button type="submit" class="btn btn-success botao_mostrar" data-loading-text="Mostrar">Mostrar</button>
		 	</div>
	     </div>
     
   </form>
	
	<script>
      $('.select2').select2({
      	placeholder : '',
      	closeOnSelect: false
      	});
      
      $('.select2-remote').select2({ data: [{id:'A', text:'A'}]});

      $('button[data-select2-open]').click(function(){
        $('#' + $(this).data('select2-open')).select2('open');
      });
    </script>
	
	<script type="text/javascript">
	$(document).ready(function() {
      $('#intervalo').daterangepicker({
            format: 'DD/MM/YYYY',
            locale: { cancelLabel: 'Limpar', applyLabel: 'Aplicar', fromLabel: 'De', toLabel: 'Até' }
          }, function(start, end, label) {
        //console.log(start.toISOString(), end.toISOString(), label);
      });
      
      $('#intervalo').on('apply.daterangepicker', function(ev, picker) {
	  //do something, like clearing an input
	  //$('#daterange').submit();
	});
      
   });
	</script>
	
	<? include('dados_relatorio.php'); ?>
	<div class="row-fluid">
		<div class="span12"> 
				
			<table cellspacing="0" width="100%" class="table table-striped table- table-hover">
				<thead>
			        
			        <tr>
			            <th width="20%">Data</th>
			            
			            <? if ($modo_recebimento_convenios_pagos!="3") { ?>
			            <th width="17%" align="left" class="td_bg2"><div class="text-right">Já recebeu</div></th>
			            <? } ?>
			            
			            <? if ($convenio_proprio=='1') { ?>
			            <th width="26%" align="left" class="td_bg2"><div class="text-right">Vai receber (Conv. próprio)</div></th>
			            <? } ?>
			            
			            <? /*<th width="9%" align="left"><div class="text-right">Deve</div></th>*/ ?>
			            
			            <th width="20%" align="left" class="br td_bg2"><div class="text-right">Bruto</div></th>
			            
			            <th width="20%" align="left" class="br td_bg2"><div class="text-right">Líquido</div></th>
			            
			            
			            <th width="9%" align="left"><div class="text-right">Ficou em dinheiro</div></th>
			            <th width="9%" align="left"><div class="text-right">Vai receber dos Planos</div></th>
			            
			            <? /*<th width="9%" align="left"><div class="text-right">Deve</div></th>*/ ?>
			            <? /*<th width="9%" align="left" class="br"><div class="text-right">Líquido</div></th>*/ ?>
			            <? /*
			            <th width="15%">&nbsp;</th>*/ ?>
			        </tr>
			    </thead>
			    <tbody>
					<?
					
					$total_convenios= count($_POST[id_convenio]);
					
					//echo "<strong>Selecionados:</strong> ";
					
					if ($total_convenios>0) {
						
						
						
						$str_convenio= "and   ( ";
						
						$i=0;
						while ($_POST[id_convenio][$i]!='') {
							$j=$i+1;
							
							$str_convenio.= " id_convenio = '". $_POST[id_convenio][$i] ."' or ";
							
							//echo " <em>". pega_convenio($_POST[id_convenio][$i]) ."</em>";
							
							//if ($total_convenios!=$j) echo ', ';
							
							$i++;
						}
						
						$str_convenio= substr($str_convenio, 0, -3);
						
						$str_convenio.= " ) ";
					}
					else {
						//echo "<em>Todos os convênios</em>";
					}
					
					echo "<br/>";
					
					
					$diferenca= $data_mk[1]-$data_mk[0];
					$diferenca= (int)floor( $diferenca / (60 * 60 * 24));
					
					for ($d= 0; $d<=$diferenca; $d++) {
						$calculo_data= $data_mk[0]+(86400*$d);
			
						$data= date("d/m/Y", $calculo_data);
						$id_dia= date("w", $calculo_data);
						$vale_dia= date("Y-m-d", $calculo_data);
						
						$result_num= mysqli_query($conexao1, "select sum(recebido_valor_pessoa) as recebido_valor_pessoa,
													sum(recebido_valor_clinica) as recebido_valor_clinica,
													sum(vai_receber_valor_pessoa) as vai_receber_valor_pessoa,
													sum(vai_receber_valor_clinica) as vai_receber_valor_clinica,
													sum(pessoa_deve) as pessoa_deve,
													sum(clinica_deve) as clinica_deve,
													sum(por_direito_valor_pessoa) as por_direito_valor_pessoa,
													sum(por_direito_valor_clinica) as por_direito_valor_clinica,
													count(id) as qtde
													from atendimentos_uni
													where data= '". $vale_dia ."'
													and   id_clinica = '". $IDENT_id_clinica ."'
													and   id_medico = '". $IDENT_id_pessoa ."'
													and   status_atendimento = '1'
													". $str_convenio ."
												") or die(mysqli_error());
													
						$rs_num= mysqli_fetch_object($result_num);
						
						if ($rs_num->qtde=='') $qtde=0;
						else $qtde=$rs_num->qtde;
						
						if ($qtde>0) {
						
						$total_qtde+=$qtde;
						
						$total_recebido_valor_pessoa+=$rs_num->recebido_valor_pessoa;
						$total_recebido_valor_clinica+=$rs_num->recebido_valor_clinica;
						
						$total_vai_receber_valor_pessoa+=$rs_num->vai_receber_valor_pessoa;
						$total_vai_receber_valor_clinica+=$rs_num->vai_receber_valor_clinica;
						
						$total_pessoa_deve+=$rs_num->pessoa_deve;
						$total_clinica_deve+=$rs_num->clinica_deve;
						
						$total_por_direito_valor_pessoa+=$rs_num->por_direito_valor_pessoa;
						$total_por_direito_valor_clinica+=$rs_num->por_direito_valor_clinica;
						
						$total_bruto+=$rs_num->por_direito_valor_pessoa+$rs_num->por_direito_valor_clinica;
						$total_liquido+=$rs_num->por_direito_valor_pessoa;
			        ?>
			        <tr id="linha_<?=$d;?>" class="<? if ( ($id_dia==0) || ($id_dia==6) ) echo 'warning'; ?> <? if ($_POST[data]==$data) echo 'success'; ?> ">
			            <td align="center">
			            	<small><a class="interno" href="./?pagina=lancamento/relatorio_diario&amp;data=<?= $data; ?>"><?= $data; ?></a> <span class="muted"><small><?=traduz_dia($id_dia);?></small></span></small> (<?= fnumi($qtde); ?>) <?/*atendimento<? if ($qtde>1) echo "s"; ?>*/ ?>
			            	
			            </td>
			            <? if ($modo_recebimento_convenios_pagos!="3") { ?>
			            <td class=" td_bg2"><div class="text-right">R$ <?= fnum($rs_num->recebido_valor_pessoa); ?></div></td>
			            <? } ?>
			            
			            <? if ($convenio_proprio=='1') { ?>
			            <td class=" td_bg2"><div class="text-right">R$ <?= fnum($rs_num->vai_receber_valor_pessoa); ?></div></td>
			            <? } ?>
			            
			            <? /*<td><div class="text-right">R$ <?= fnum($rs_num->pessoa_deve); ?></div></td>*/ ?>
			            
			            <td class="br td_bg2"><div class="text-right">R$ <?= fnum($rs_num->por_direito_valor_pessoa+$rs_num->por_direito_valor_clinica); ?></div></td>
			            <td class="br td_bg2"><div class="text-right">R$ <?= fnum($rs_num->por_direito_valor_pessoa); ?>
			            
			            <small class="muted">
			            <? /*if ( ($identifica_atendimentos=='2') || ($identifica_atendimentos=='3') ) { ?>
			            
			            
		            	<a target="_blank" href="./?pagina=lancamento/relatorio_diario&amp;data=<?=$data;?>"><?= fnumi($qtde); ?> atendimento<? if ($qtde>1) echo "s"; ?></a>
		            	<? } else { */ ?>
		            	
		            	
		            	<? //} ?>
		            	
			            </small>
			            
			            </div></td>
			            
			            <td><div class="text-right">R$ <?= fnum($rs_num->recebido_valor_clinica); ?></div></td>
			            <td><div class="text-right">R$ <?= fnum($rs_num->vai_receber_valor_clinica); ?></div></td>
			            
			            <? /*<td><div class="text-right">R$ <?= fnum($rs_num->clinica_deve); ?></div></td>*/ ?>
			            <? /*<td class="br"><div class="text-right">R$ <?= fnum($rs_num->por_direito_valor_clinica); ?></div></td>*/ ?>
			            
			            <? /*
			            <td>
			                <a class="btn btn-mini btn-primary pull-right" href="./?pagina=lancamento/lancamento&amp;data=<?= $data; ?>">
			                	<i class="icon-white icon-eye-open"></i> Ver dia
			                </a>
			                <!--
			                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('diaLimpar', <?=$data;?>);" onclick="return confirm('Tem certeza que deseja limpar os lançamentos deste dia?');">
			                    <i class="icon-white icon-trash"></i> Limpar dia
			                </a>
			                -->
			            </td>
			            */ ?>
			        </tr>
			        <? } } ?>
			    </tbody>
			    <tfoot>
			    	<td>&nbsp;</td>
			    	<? if ($modo_recebimento_convenios_pagos!="3") { ?>
			    	<td><h4 class="text-right"><big>R$ <?= fnum($total_recebido_valor_pessoa); ?></big></h4></td>
			    	<? } ?>
			    	
			    	<? if ($convenio_proprio=='1') { ?>
			    	<td><h4 class="text-right"><big>R$ <?= fnum($total_vai_receber_valor_pessoa); ?></big></h4></td>
			    	<? } ?>
			    	
			    	<td><h4 class="text-right"><big>R$ <?= fnum($total_por_direito_valor_pessoa+$total_por_direito_valor_clinica); ?></big></h4></td>
			    	<td>
			    		<h4 class="text-right"><big>R$ <?= fnum($total_por_direito_valor_pessoa); ?></big></h4>
			    		<p class="text-right"><small class="1muted"><?= fnumi($total_qtde); ?> atendimento<? if ($total_qtde>1) echo "s"; ?></small></p>
			    	</td>
			    	<? /*<td>&nbsp;</td>*/ ?>
			    </tfoot>
			</table>
		</div>
	</div>

	<? /*
	<center>
		<h3><?=fnumi($total_qtde);?> atendimentos</h3>
	</center>
	<br />
	*/ ?>
	<div class="row-fluid">
		<div class="span6 offset3 well">
			
			<div class="row-fluid">
				<div class="span6">
					<small>Total Bruto:</small> <br/>
					<h3>R$ <?=fnum($total_bruto);?></h3>
				</div>
				<div class="span6">
					<small>Total Líquido:</small> <br/>
					<h3>R$ <?=fnum($total_liquido);?></h3>
				</div>
			</div>
			
		</div>
		
		
		<div class="span6 well">
			<h3 class="text-center">Clínica</h3>
			
			<div class="row-fluid">
				<div class="span4">
					<small>Ficou em dinheiro:</small> <br/>
					<h3>R$ <?=fnum($total_recebido_valor_clinica);?></h3>
				</div>
				<div class="span4">
					<small>Vai receber (Planos e Convênios):</small> <br/>
					<h3>R$ <?=fnum($total_vai_receber_valor_clinica);?></h3>
				</div>
				<div class="span4">
					<small>Líquido:</small> <br/>
					<h3>R$ <?=fnum($total_por_direito_valor_clinica);?></h3>
				</div>
			</div>
		</div>
		
		
	</div>	
	<br />
	<div class="row-fluid">
		<div class="span6 offset3 well">
			<?
			$a_receber= $total_clinica_deve-$total_pessoa_deve;
			
			if ($a_receber<0) {
				$tit= "Pagar para Instituição:";
			}
			else {
				$tit= "A receber da Instituição:";
			}
			?>
			<center>
				<h3 style="margin-top:0;">Acerto do período</h3>
				
				<h5>Instituição deve:</h5>
				<h2>R$ <?=fnum(abs($total_clinica_deve));?></h2>
				
				<h5>Médico deve:</h5>
				<h2>R$ <?=fnum(abs($total_pessoa_deve));?></h2>
				
				<h4><?=$tit;?></h4>
				<h1>R$ <?=fnum(abs($a_receber));?></h1>
				<br/>
				<? /*
				<button class="btn btn-success">Registrar repasse</button>
				*/ ?>
			</center>
		</div>
	</div>
	<br/>
	
	<? if ($_COOKIE["perfil"]=="3") { ?>
	
	<div class="row-fluid">
		<div class="span12"> 
				
			<table cellspacing="0" width="100%" class="table table-striped table- table-hover">
				<thead>
			        
			        <tr>
			            <th width="40%">Plantonista</th>
			            <th width="30%" align="left" class="td_bg2"><div class="text-right">Horas registradas</div></th>
			            <th width="30%" align="left" class="br td_bg2"><div class="text-right">Valor proporcional</div></th>
			        </tr>
			    </thead>
			    <tbody>
					<?
					
					$result_pl= mysqli_query($conexao1, "select distinct(id_plantonista) from atendimentos_uni, pessoas
																where atendimentos_uni.data >= '". formata_data($data1) ."'
																and   atendimentos_uni.data <= '". formata_data($data2) ."'
																and   atendimentos_uni.id_plantonista = pessoas.id_pessoa
																and   atendimentos_uni.id_clinica = '". $IDENT_id_clinica ."'
																and   atendimentos_uni.id_medico = '". $IDENT_id_pessoa ."'
																and   atendimentos_uni.id_plantonista <> '0'
																and   atendimentos_uni.id_plantonista is not NULL
																order by pessoas.nome asc
												") or die(mysqli_error());
					
					$total_horas=0;
					$p=1;
												
					while ($rs_pl= mysqli_fetch_object($result_pl)) {
						
						$horas_plantonista[$p]=0;
						
						
						//pegar as saídas de plantão durante o período
						$result_saida= mysqli_query($conexao1, "select * from pessoas_clinicas_plantoes
																where id_plantonista = '". $rs_pl->id_plantonista ."'
																and   tipo_batida = '2'
																and   vale_dia >= '". formata_data($data1) ."'
																and   vale_dia <= '". formata_data($data2) ."'
												") or die(mysqli_error());
						
						while ($rs_saida= mysqli_fetch_object($result_saida)) {
							
							$result_entrada= mysqli_query($conexao1, "select * from pessoas_clinicas_plantoes
																where id_plantonista = '". $rs_pl->id_plantonista ."'
																and   tipo_batida = '1'
																and   vale_dia = '". $rs_saida->vale_dia ."'
																and   id_pcp < '". $rs_saida->id_pcp ."'
																order by id_pcp desc limit 1
												") or die(mysqli_error());	
							
							$num_entrada= mysqli_num_rows($result_entrada);
							
							//se achou, faz o cálculo, o resto é ruído
							if ($num_entrada==1) {
								$rs_entrada= mysqli_fetch_object($result_entrada);
								
								//echo $rs_saida->data .' '. $rs_saida->hora .' - ';
								//echo $rs_entrada->data .' '. $rs_entrada->hora .' - ';
								
								$saida= faz_mk_data_completa($rs_saida->data .' '. $rs_saida->hora);
								$entrada= faz_mk_data_completa($rs_entrada->data .' '. $rs_entrada->hora);
								
								$diferenca= $saida-$entrada;
								$horas_plantonista[$p]+=$diferenca;
								
								$total_horas+=$diferenca;
							}
							
						}
			        ?>
			        <tr id="linha_<?=$d;?>">
			            <td align="center">
			            	<small><?=pega_pessoa($rs_pl->id_plantonista);?></small>
			            	
			            </td>
			            
			            <td class=" td_bg2"><div class="text-right"><?=formata_diferenca_horas_mk($horas_plantonista[$p]);?></div></td>
			            
			            <td class=" td_bg2"><div class="text-right"><div id="valor_plantonista_<?=$p;?>"></div></div></td>
			            
			        </tr>
			        <? $p++; } ?>
			    </tbody>
			    <tfoot>
			    	<td>&nbsp;</td>
			    	
			    	<td><h4 class="text-right"><?=formata_diferenca_horas_mk($total_horas);?></h4></td>
			    	
			    	<td><h4 class="text-right">R$ <?=fnum(abs($a_receber));?></h4></td>
			    	
			    </tfoot>
			</table>
			
			
			<script>
				<?php
				//$a_receber= formata_valor($a_receber);
				
				//echo $a_receber;
				
				for ($r=1; $r<$p; $r++) {
					$percent= pega_porcentagem($horas_plantonista[$r], $total_horas);
					
					//$percent= formata_valor($percent);
				?>
				$("#valor_plantonista_<?=$r;?>").html("R$ <?=fnum((($a_receber)*$percent)/100);?>");
				<? } ?>
			</script>
			
		</div>
	</div>
	
	<? } ?>
	
	<script>
		$('#mes_escolhe').datepicker();
	</script>
	<? } ?>
<? } ?>
