<?

if (pode("1234", $_COOKIE["perfil"])) {
	
	if ($_GET["valor"]!="")
		$_SESSION["relatorio_valor"]= $_GET["valor"];
	
	if ($_COOKIE[id_clinica]=='') {
		
		echo '
			<script>
				window.top.location.href="./?pagina=acesso/trabalho_clinicas";
			</script>
			';
		
		//echo '<h3>Nenhuma clínica cadastrada.</h3><p>Cadastre/escolha uma clínica para fazer o registro de atendimentos.</p> <a class="btn btn-large btn-info" href="./?pagina=acesso/trabalho_clinica&acao=i">Cadastrar agora</a>';
	}
	else {
	
	$str=" and   atendimentos_uni.data= '". formata_data_hifen($_GET[data]) ."'
			   and   atendimentos_uni.id_medico = '". $_COOKIE[id_pessoa] ."'
				";
	
	$result= mysqli_query($conexao1, "select * from atendimentos_uni, pessoas
							where atendimentos_uni.id_clinica = '". $_COOKIE[id_clinica] ."'
							$str
							and   pessoas.id_pessoa = atendimentos_uni.id_paciente
							and   atendimentos_uni.status_atendimento = '1'
							order by id asc
							") or die(mysqli_error());
	$num= mysqli_num_rows($result);
	
	if ($num==0) echo "<h3>Ops</h3><p>Nenhum atendimento encontrado.</p><p class='muted'>PS: aqui só aparecem os atendimentos caso você esteja identificando o nome dos pacientes.</p>";
	else {		
	?>		
	
	<script>
		mixpanel.track("Acessou relatório diário");
	</script>
		
	<div class="page-header">
		<h5 class="pull-right"><?= $_GET[data]; ?></h5>
		
		<? if ($_SESSION["relatorio_valor"]!="0") { ?>
		<h3>Bruto x Líquido <br><small><?= pega_clinica_pessoa($IDENT_id_pessoa, $IDENT_id_clinica); ?></small></h3>
		<? } ?>
		
		<h4>Dr. <?= pega_usuario($_COOKIE[id_usuario]); ?> &nbsp;<small><?=pega_pessoa_dado($_COOKIE[id_pessoa], 'registro'); ?> &bull; <?=pega_especialidade(pega_pessoa_dado($_COOKIE[id_pessoa], 'id_especialidade')); ?></small>
		</h4>
		
		
	</div>
	<br class="clearfix"/>
	
	<table cellspacing="0" width="100%" class="table table-hover table-bordered table-condensed ">
		<thead>
	        <tr>
	            <th width="15%">Hora</th>
	            <th width="28%" align="left" class="br">Paciente</th>
	            
	            <? if ($_COOKIE["perfil"]=="3") { ?>
	            <th width="20%"><div class="text-left">Plantonista</div></th>
	            <? } ?>
	            
	            <th width="15%" align="left">Convênio</th>
	            
	            <? if ($_SESSION["relatorio_valor"]!="0") { ?>
		            <th width="34%"><div class="text-right">Valor</div></th>
		            
		            <? if ($modo_recebimento_convenios_pagos!="3") { ?>
		            <th width="20%"><div class="text-right">Em dinheiro</div></th>
		            <? } ?>
	            
	            <? } ?>
	        </tr>
	    </thead>
	    <tbody>
			<?
			$valor_total=0;
			$valor_pago=0;
			$valor_liquido=0;
			
			while ($rs= mysqli_fetch_object($result)) {
				$valor_liquido+=$rs->por_direito_valor_pessoa;
				
				$valor_total+=$rs->valor_total;
				$valor_pago+=$rs->recebido_valor_pessoa;
	        ?>
	        <tr id="linha_<?=$i;?>">
	            <? /*<td align="center"><small><?= $_GET[data]; ?></small></td>*/ ?>
	            <td class="br">
	            	<small><?= desformata_data($rs->data); ?>
	            	
	            	<? if ($_SESSION["relatorio_valor"]!="0") { ?>
	            	
	            	<? echo substr($rs->hora, 0, 5); ?>
	            	
	            	<? } ?>
	            	
	            	</small>
	            	
	            	
	            </td>
	            <td class="br">
	            	<small><?=$rs->nome; ?></small> -
	            	<small class="muted">
	            	<? if ($rs->id_ato!='1') {
		            	echo pega_ato($rs->id_ato);	
	            	}
	            	else {
		            	echo pega_tipo_atendimento($rs->tipo_atendimento);
	            	}
	            	?>
	            	</small>
	            </td>
	            
	            <? if ($_COOKIE["perfil"]=="3") { ?>
	            <td><div class="text-left"><?= pega_pessoa($rs->id_plantonista);?></div></td>
	            <? } ?>
	            
	            <td>
		            <small><?= pega_convenio($rs->id_convenio); ?></small>
	            </td>
	            
	            <? if ($_SESSION["relatorio_valor"]!="0") { ?>
		            <td><div class="text-right">
			            
			            <span>
			            	<span class="badge badge-success text-center pull-right" style="width:10px;padding: 2px 6px;">L</span>
			            	
			            	R$ <?= fnum($rs->por_direito_valor_pessoa); ?> &nbsp;
			            </span>
			            <br>
			            
			            <span>
			            	<span class="badge badge- text-center pull-right" style="width:10px;padding: 2px 6px;">B</span>
			            	
			            	<small class="muted">R$ <?= fnum($rs->valor_total); ?> &nbsp;</small>
			            	
			            </span>
			            
			            
			            
			            
			            
		            </div></td>
		            
		            
		            <? if ($modo_recebimento_convenios_pagos!="3") { ?>
		            <td><div class="text-right">R$ <?= fnum($rs->recebido_valor_pessoa); ?></div></td>
		            <? } ?>
	            
	            <? } ?>
	        </tr>
	        <? } ?>
	    </tbody>
	    
	    <tfoot>
	        <tr>
	            <th>&nbsp;</th>
	            <th>&nbsp;</th>
	            
	            <th>&nbsp; <? /*<div class="text-right">
	            	<span class="badge badge-success text-center" style="width:10px;float:left;">L</span>
	            	<strong>R$ <?= fnum($valor_liquido); ?></strong>
	            	<br/>
	            	
	            	<span class="badge badge- text-center" style="width:10px;float:left;">B</span>
	            	<span class="muted">R$ <?= fnum($valor_total); ?></span>
	            </div> */ ?>
	            </th>
	            
	            <? if ($_SESSION["relatorio_valor"]!="0") { ?>
	            
	            <? if ($modo_recebimento_convenios_pagos!="3") { ?>
	            <th><div class="text-right"><big>R$ <?= fnum($valor_pago); ?></big></big></div></th>
	            <? } ?>
	            
	            <? } ?>
	        </tr>
	    </tfoot>
	</table>
	
	<? //if ($_SESSION["relatorio_valor"]!="0") { ?>
	<div class="row-fluid">
		<div class="span6 offset3 well">
			<?
			$a_receber= $total_clinica_deve-$total_pessoa_deve;
			
			if ($a_receber<0) {
				$tit= "Pagar:";
			}
			else {
				$tit= "A receber:";
			}
			?>
			<center>
				<h3 style="margin-top:0;">Resumo do dia:</h3>
				
				<div class="row-fluid">
					<div class="span6">
						<big><?=$num;?></big> atendimentos
					</div>
					<div class="span6">
						<strong>Líquido:</strong> <br/>
						<big>R$ <?= fnum($valor_liquido); ?></big>
						<br><br>
						
						<strong class="muted">Bruto:</strong> <br/>
						<span class="muted">R$ <?= fnum($valor_total); ?></span>
						
						
					</div>
				</div>
			</center>
		</div>
	</div>
	<? //} ?>
	
	<p class="text-center"></p>
	
	<? } } ?>
<? } ?>
