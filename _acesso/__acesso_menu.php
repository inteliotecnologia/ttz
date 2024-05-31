<?
if (pode("1", $_COOKIE["perfil"])) {
?>

<ul class="nav nav-tabs tab_interna">
	
	<li class="interno <? if (strpos($pagina, "/usuario")!==false) echo 'active'; ?>"><a href="./?pagina=acesso/usuarios"><i class="icon-user"></i> Usuários</a></li>							
	<li class="interno <? if (strpos($pagina, "/clinica")!==false) echo 'active'; ?>"><a href="./?pagina=acesso/clinicas"><i class="icon-briefcase"></i> Clínicas</a></li>
	<li class="interno <? if (strpos($pagina, "/convenio")!==false) echo 'active'; ?>"><a href="./?pagina=acesso/convenios"><i class="icon-th-large"></i> Convênios</a></li>
	<li class="interno <? if (strpos($pagina, "/ato")!==false) echo 'active'; ?>"><a href="./?pagina=acesso/atos"><i class="icon-tint"></i> Procedimentos</a></li>
	<li class="interno <? if (strpos($pagina, "/acesso")!==false) echo 'active'; ?>"><a href="./?pagina=acesso/acessos"><i class="icon-road"></i> Acessos</a></li>
	<li class="interno <? if (strpos($pagina, "/log")!==false) echo 'active'; ?>"><a href="./?pagina=acesso/log"><i class="icon-file"></i> Logs</a></li>
</ul>
<br/>

<? } ?>