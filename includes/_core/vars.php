<?php
/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Configuração de ambiente
*/
require_once("includes/_core/ambiente.php");

/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Configuração de ambiente
*/
define("AJAX_LINK", "link.php?");
define("AJAX_FORM", "form.php?");
define("CAMINHO", "uploads/");

define("NOME", "ttz");
define("SLOGAN", "O melhor amigo do bolso do médico");
define("SLOGAN_QUEBRA", "O melhor amigo <br>do bolso do médico");
define("CHAMADA", "Aplicativo de conferência financeira para médicos");
define("VERSAO", "1.2");

define("TEMPO_COOKIE", time()+(90*24*3600));

define("SENDGRID_USERNAME", "ttz_mail");
define("SENDGRID_PASSWORD", "d02d0kj03");

define('HASH_KEY', md5('DIJ293F8H359G7H12DH298H') );

setlocale(LC_ALL, "pt_BR", "ptb");
date_default_timezone_set('America/Sao_Paulo');

/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
URLs e tals
*/

switch (AMBIENTE) {
	//Desenvolvimento
	case 1:
		
		define("CONEXAO1_HOST", "localhost");
		define("CONEXAO1_USUARIO", "root");
		define("CONEXAO1_SENHA", "");
		define("CONEXAO1_BANCO", "ttz");
		
		define("SISTEMA_URL", "http://localhost/ttz/app.ttz.med.br");
		define("SISTEMA_URLNAME", "localhost/ttz/app.ttz.med.br");
		define("SITE_URL", "http://localhost/ttz/ttz.med.br");
		define("URL", "www.ttz.med.br");
		
		define("MIXPANEL_TOKEN", "6a789eb890599bd02459fba47a563748");
		
		define("DOMINIO", "");
		define("PATH", "/");
	break;
	
	//Homologação
	case 2:
		define("CONEXAO1_HOST", "localhost");
		define("CONEXAO1_USUARIO", "?");
		define("CONEXAO1_SENHA", "?");
		define("CONEXAO1_BANCO", "ttz");
		
		define("SISTEMA_URL", "http://homologacao.intelio.com.br/ttz/app.ttz.med.br");
		define("SISTEMA_URLNAME", "homologacao.intelio.com.br/ttz/app.ttz.med.br");
		define("SITE_URL", "http://homologacao.intelio.com.br/ttz/ttz.med.br/");
		define("URL", "www.ttz.med.br");
		
		define("MIXPANEL_TOKEN", "6a789eb890599bd02459fba47a563748");
		
		define("DOMINIO", "");
		define("PATH", "/");
	break;
	
	//Produção
	case 3:
		//define("CONEXAO1_HOST", "titan.c49laku9xobf.sa-east-1.rds.amazonaws.com");
		//define("CONEXAO1_USUARIO", "edoc_admin_user");
		//define("CONEXAO1_SENHA", "crash4400");
		
		define("CONEXAO1_HOST", "enceladus.cle1tvcm29jx.us-east-1.rds.amazonaws.com");
		define("CONEXAO1_USUARIO", "intelio_user");
		define("CONEXAO1_SENHA", "2i92iS02iV3d23dm");
		define("CONEXAO1_BANCO", "ttz");
		
		define("SISTEMA_URL", "http://app.ttz.med.br");
		define("SISTEMA_URLNAME", "app.ttz.med.br");
		define("SITE_URL", "http://ttz.med.br");
		define("URL", "www.ttz.med.br");
		
		define("MIXPANEL_TOKEN", "9bc5b10beeea74f8c504b1a65191a4ac");
		
		define("DOMINIO", "app.ttz.med.br");
		define("PATH", "/");
	break;
}

/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
E-mail e tals
*/

define("EMAIL_RODAPE", "
	<span style='color: #999;'>
	<br /><br />
	Atenciosamente,
	<p>- <br />
	". NOME ." &bull; ". SLOGAN ."<br />
	<a href='". SITE_URL ."' target='_blank'>". SITE_URL ."</a>
	</p>
	</span>
");

?>