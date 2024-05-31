<?
if (pode("1234", $_COOKIE["perfil"])) {
	
?>

<? /* Modal para por a senha e fechar o dia */ ?>
	<div id="modal_cancela_conta" class="modal hide fade" tabindex="-1" role="dialog">
			
		<form id="modal_cancela_conta_form" action="<?=AJAX_FORM;?>cancelarConta" method="post">
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h5 id="nc_modal_label">Cancelar conta</h5>
			</div>
			<br/>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span6 text-right" style="line-height:1.5;">
						Entre com sua senha para cancelar sua conta<br/>
						(operação irreversível):
					</div>
					<div class="span6">
						<input placeholder="Senha" type="password" name="senha3" id="senha3" value="" required="required" />
					</div>
				</div>
				
			</div>
			<br/>
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary cadastrar" data-loading-text="...">Excluir meus dados</button>
			</div>
		</form>
	</div>

<? include('dados_menu.php'); ?>

<table cellspacing="0" width="95%" class="table table-striped table-condensed">
	<tbody>
		<tr>
			<th>Data</th>
			<th>Competência</th>
			<th>Valor</th>
			<th>Situação</th>
		</tr>
		<?
		$result_pag= mysqli_query($conexao1, "select * from pagamentos, usuarios
										where usuarios.id_usuario = '". $_COOKIE["id_usuario"] ."'
										and   usuarios.id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   usuarios.id_usuario = pagamentos.id_usuario
										and   usuarios.auth = '". $_COOKIE[auth_usuario] ."'
										order by data_pagamento asc
										");
		while ($rs_pag= mysqli_fetch_object($result_pag)) {
		?>
		<tr>
			<td><?= desformata_data($rs_pag->data_pagamento); ?></td>
			<td><?= desformata_data($rs_pag->data_de); ?> a <?= desformata_data($rs_pag->data_ate); ?></td>
			<td>
				<?
				if ($rs_pag->tipo_pagamento!='0') {
				?>
				<?=fnumf($rs_pag->valor); ?>
				<? } else { ?>
				<small>Período de homologação</small>
				<? } ?>
			</td>
			<td>
				<?
				//se for pagamento normal...
				if ($rs_pag->tipo_pagamento=='1') {
				?>
				
					<?
					//se for pagamento normal...
					if ($rs_pag->status_pagamento=='0') {
					?>
					
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="GEE275C95XJRL">
						<input style="border:none !important;" type="image" src="https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - A maneira fácil e segura de enviar pagamentos online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/pt_BR/i/scr/pixel.gif" width="1" height="1">
					</form>
					
					<? } else { ?>
					<small class="text-success">OK</small>
					<? } ?>
				<?
				//período de homologação
				} else { ?>
				<small class="text-success">OK</small>
				<? } ?>
			</td>
		</tr>
		<?
		}
		?>
	</tbody>
</table>

<br />

<small class="muted">Após o período de testes, caso queira continuar, você receberá um boleto em seu e-mail.</small>

<br /><br />

<hr />
<br/>

<div class="row-fluid">
	<div class="span12 text-center">
		<button id="cancelar_conta" class="btn btn-danger">Cancelar minha conta</button>
		
		<br />
	</div>
</div>

<? } ?>