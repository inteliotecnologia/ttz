<? if (pode("1234", $_COOKIE["perfil"])) { ?>

	<div class="hero-unit">
		<h2>Olá, <?=primeira_palavra($_COOKIE[nome]);?>!</h2>
		
		<? if ($_COOKIE[ultimo_login]=='') { ?>
		<p>Este é o seu primeiro acesso, ajuste seus dados e configure onde e como trabalha.</p>
		<p><a class="btn btn-primary btn-large" href="./?pagina=acesso/dados">Configurações »</a></p>
		
		<? } else { ?>
		<p>Bem-vindo de volta! Seu último acesso foi em <strong><?=formata_data_timestamp($_COOKIE[ultimo_login]); ?></strong>.</p>
		
		<p><a class="btn btn-primary btn-large" href="./?pagina=lancamento/lancamento">Lançar »</a></p>
		<? } ?>
	</div>
	
	<? /*
	<div class="row-fluid">
		<div class="span4">
		<h2>Heading</h2>
		<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac. </p>
		<p><a href="#" class="btn">View details »</a></p>
		</div><!--/span-->
		
		<div class="span4">
		<h2>Heading</h2>
		<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris. </p>
		<p><a href="#" class="btn">View details »</a></p>
		</div><!--/span-->
		
		<div class="span4">
		<h2>Heading</h2>
		<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo. </p>
		<p><a href="#" class="btn">View details »</a></p>
		</div><!--/span-->
		
	</div><!--/row-->
	*/ ?>

<? } ?>