<?
if (pode("123", $_COOKIE["perfil"])) {
?>

<ul class="nav nav-tabs tab_interna">
	
	<li class="interno <? if (strpos($pagina, "/relatorio")!==false) echo 'active'; ?>"><a href="./?pagina=lancamento/relatorio"><i class="icon-file"></i> Relatório</a></li>							
	<li class="interno <? if (strpos($pagina, "/repasses")!==false) echo 'active'; ?>"><a href="./?pagina=lancamento/repasses"><i class="icon-usd"></i> Repasses</a></li>
</ul>

<? } ?>