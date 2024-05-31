<?
//session_start();

require_once("includes/_core/protecao.php");

/*
$_COOKIE["id_usuario"]="";
$_COOKIE["id_pessoa"]="";
$_COOKIE["id_acesso"]="";
$_COOKIE["nome"]="";
$_COOKIE["perfil"]="";
$_COOKIE["foto"]="";
$_COOKIE["ultimo_login"]="";
$_COOKIE["id_clinica"]="";
*/

@session_destroy();

//setcookie("id_clinica", $_POST[id_clinica], TEMPO_COOKIE, PATH, DOMINIO, false, true);

setcookie ("id_usuario", "", 0, PATH, DOMINIO, false, true);
setcookie ("auth_usuario", "", 0, PATH, DOMINIO, false, true);
setcookie ("id_pessoa", "", 0, PATH, DOMINIO, false, true);
setcookie ("cm", "", 0, PATH, DOMINIO, false, true);
setcookie ("nome", "", 0, PATH, DOMINIO, false, true);
setcookie ("sexo", "", 0, PATH, DOMINIO, false, true);
setcookie ("id_plantonista", "", 0, PATH, DOMINIO, false, true);
setcookie ("hash_usuario", "", 0, PATH, DOMINIO, false, true);
setcookie ("perfil", "", 0, PATH, DOMINIO, false, true);
setcookie ("tema", "", 0, PATH, DOMINIO, false, true);
setcookie ("foto", "", 0, PATH, DOMINIO, false, true);
setcookie ("ultimo_login", "", 0, PATH, DOMINIO, false, true);
setcookie ("data_lancamento", "", 0, PATH, DOMINIO, false, true);
setcookie ("id_clinica", "", 0, PATH, DOMINIO, false, true);
setcookie ("id_acesso", "", 0, PATH, DOMINIO, false, true);

if ($_GET[erro]=='') $erro= 't';
else $erro= $_GET[erro];

header("location: ./index2.php?pagina=login&erro=". $erro);


/*<script>
	window.top.location.href='./index2.php?pagina=login&erro=<?=$erro;?>';
</script>*/

?>