<?
require_once("includes/_core/conexao.php");
session_start();

require_once 'includes/mobile-detect/Mobile_Detect.php';
$detect = new Mobile_Detect;
$device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'smartphone') : 'computador');

//if ($_SERVER['REMOTE_ADDR']=="187.115.48.222") echo "id_clinica: ". $_COOKIE["id_clinica"];

if ( (isset($_GET["formLogin"])) && ($_GET["pagina"]!="login") && ($_GET["pagina"]!="cadastro") && ($_GET["pagina"]!="recupera") && ($_GET["pagina"]!="esqueci") ) {
	$retorno= true;
	if ( ($_COOKIE["id_usuario"]=="") || ($_COOKIE["id_pessoa"]=="") )
		$retorno= false;
	else {
		
		
		/*if ($_SERVER[REMOTE_ADDR]=="201.47.45.9") {
			echo "select * from usuarios
									where id_usuario = '". $_COOKIE[id_usuario] ."'
									and   id_pessoa = '". $_COOKIE[id_pessoa] ."'
									and   hash_usuario = '". $_COOKIE[hash_usuario] ."'
									and   perfil = '". $_COOKIE[perfil] ."'
									limit 1
									";
									die();
		}*/
		
		$result_hash= mysqli_query($conexao1, "select * from usuarios
									where id_usuario = '". $_COOKIE[id_usuario] ."'
									and   id_pessoa = '". $_COOKIE[id_pessoa] ."'
									and   hash_usuario = '". $_COOKIE[hash_usuario] ."'
									and   perfil = '". $_COOKIE[perfil] ."'
									limit 1
									");
		$num_hash= mysqli_num_rows($result_hash);
		
		if ($num_hash==0) {
			@session_destroy();
			
			setcookie ("id_usuario", "", 1, PATH, DOMINIO, false, true);
			setcookie ("id_pessoa", "", 1, PATH, DOMINIO, false, true);
			setcookie ("hash_usuario", "", 1, PATH, DOMINIO, false, true);
			setcookie ("perfil", "", 1, PATH, DOMINIO, false, true);
			setcookie ("auth_usuario", "", 1, PATH, DOMINIO, false, true);
			setcookie ("nome", "", 1, PATH, DOMINIO, false, true);
			setcookie ("sexo", "", 1, PATH, DOMINIO, false, true);
			setcookie ("tema", "", 1, PATH, DOMINIO, false, true);
			setcookie ("foto", "", 1, PATH, DOMINIO, false, true);
			setcookie ("ultimo_login", "", 1, PATH, DOMINIO, false, true);
			setcookie ("data_lancamento", "", 1, PATH, DOMINIO, false, true);
			setcookie ("id_clinica", "", 1, PATH, DOMINIO, false, true);
			setcookie ("id_acesso", "", 1, PATH, DOMINIO, false, true);
			
			header("location: ./index2.php?pagina=logout&erro=h");
		}
	}
	
	if (!$retorno)
		header("location: ./index2.php?pagina=login&oi");
}
?>