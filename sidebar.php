<? if ($_COOKIE["id_usuario"]!="") { ?>
<div class="span3">
	<div class="well sidebar-nav">
		<? if ($_COOKIE[tipo_usuario]=='a') { ?>
		<ul class="nav nav-list">
			<li><label class="nav-header">Administração</label></li>
			<li><a href="./?pagina=acesso/usuarios">Usuários</a></li>
			<!--<li><a href="./?pagina=clinicas">Clínicas</a></li>
			<li><a href="./?pagina=convenios">Convênios</a></li>
			<li><a href="./?pagina=procedimentos">Procedimentos</a></li>
			<li><a href="./?pagina=procedimentos">Logs</a></li>-->
		</ul>
		<? } ?>
		
		<hr />
		
		<ul class="nav nav-list">
			<li><label class="nav-header">Painel</label></li>
			<li class="active"><a href="./?pagina=dashboard">Dashboard</a></li>
		</ul>
		
		<hr />
		
		<ul class="nav nav-list">
			<li><label class="nav-header">Lançamento</label></li>
			<li><a href="./?pagina=lancamento/lancamento_listar">Lançar dados</a></li>
		</ul>
		
		<hr />
		<!--
		<ul class="nav nav-list">
			<li><label class="nav-header">Relatórios</label></li>
			<li><a href="#">Balanço mensal</a></li>
		</ul>
		
		<hr />
		-->
		<ul class="nav nav-list">
			<li><label class="nav-header">Configurações</label></li>
			<li><a href="./?pagina=acesso/dados">Dados pessoais</a></li>
			<li><a href="./?pagina=acesso/trabalho">Dados de trabalho</a></li>
		</ul>
		
		<hr />
		<!--
		<ul class="nav nav-list">
			<li><label class="nav-header">Suporte</label></li>
			<li><a href="#">Contato</a></li>
			<li><a href="#">Ajuda</a></li>
		</ul>
		
		<hr />
		-->
		<a class="btn btn-danger" href="./index2.php?pagina=logout">Logout</a>
		
	</div><!--/.well -->
</div>
<? } ?>