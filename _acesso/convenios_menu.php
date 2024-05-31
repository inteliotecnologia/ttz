<? if (pode("1", $_COOKIE["perfil"])) { ?>
<ul class="nav nav-tabs">
	<li class="<? if ($pagina=='acesso/convenio') echo 'active'; ?>"><a href="./?pagina=acesso/convenio&acao=e&id_convenio=<?=$_GET[id_convenio];?>">Dados do convênio</a></li>
	<li class="<? if ($pagina=='acesso/convenio_valores') echo 'active'; ?>"><a href="./?pagina=acesso/convenio_valores&acao=e&id_convenio=<?=$_GET[id_convenio];?>">Valores padrão</a></li>
</ul>
<? } ?>