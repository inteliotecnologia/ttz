<? if (pode("1234", $_COOKIE["perfil"])) { ?>
<ul class="nav nav-tabs tab_interna">
	<li class="interno <? if ($pagina=='acesso/dados') echo 'active'; ?>"><a href="./?pagina=acesso/dados"><i class="icon-user"></i> Meus dados</a></li>
	
	<? if ($_COOKIE["perfil"]=="3") { ?>
	<li class="interno <? if ( ($pagina=='acesso/equipe_listar') || ($pagina=='acesso/equipe') ) echo 'active'; ?>"><a href="./?pagina=acesso/equipe_listar"><i class="icon-building"></i> Equipe</a></li>
	<? } else { ?>
	<li class="interno <? if ( ($pagina=='acesso/trabalho_clinicas') || ($pagina=='acesso/trabalho_clinica') ) echo 'active'; ?>"><a href="./?pagina=acesso/trabalho_clinicas"><i class="icon-building"></i> Locais de trabalho</a></li>
	<li class="interno <? if ($pagina=='acesso/faturamento') echo 'active'; ?>"><a href="./?pagina=acesso/faturamento"><i class="icon-usd"></i> Cobrança</a></li>
	<? } ?>
	
	<? /*<li class="<? if ($pagina=='acesso/temas') echo 'active'; ?>"><a href="./?pagina=acesso/temas">Temas</a></li>*/ ?>
</ul>
<br />
<? } ?>