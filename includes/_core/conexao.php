<?
require_once("includes/_core/vars.php");
require_once("includes/_core/funcoes.php");

/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Conecta ao BD
*/

$conexao1= @mysqli_connect(CONEXAO1_HOST, CONEXAO1_USUARIO, CONEXAO1_SENHA, CONEXAO1_BANCO);

if (mysqli_connect_error()) {
	if (AMBIENTE==3)
		die("Manuten‹o. Voltamos em breve: ". mysqli_connect_error());
	else
		die("Erro: ". mysqli_connect_error());
}

mysqli_query($conexao1, "set names utf8");
?>