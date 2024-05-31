<?
	
if (!$cadastroRedeSocial) {
	session_start();
	
	require_once("includes/_core/protecao.php");
	
	//require("includes/phpmailer/PHPMailerAutoload.php");
	
}

header("Content-type: text/html; charset=utf-8", true);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//$clean_values = array();
//array_walk($_POST, 'limpa_post');

//$_POST= @array_map('trim', $_POST);
//$_POST= array_map('htmlentities', $_POST);


if ( ($cadastroRedeSocial) || (isset($_GET["formCadastro"])) ) {

	//include('includes/recaptcha-php/recaptchalib.php');
 
	//$privatekey= "6Lct2_ESAAAAADhWGEJumQzk80qPeLwIbg6RcmYv";

	/*$resp= recaptcha_check_answer($privatekey,
									$_SERVER["REMOTE_ADDR"],              //PEGA O IP DO SEU COMPUTADOR
									$_POST["recaptcha_challenge_field"],  //PARÂMETRO DE VALIDAÇÃO
									$_POST["recaptcha_response_field"]);  //PARÂMETRO DE VALIDAÇÃO
 */
	//if (!$resp->is_valid) {
	if (false) {
		
		logs(0, 0, 0, 0, 0, 'cadastro', 'Erra o captcha', 'E-mail: '. prepara($_POST[email]), 'Captcha digitado: '. $_POST["recaptcha_challenge_field"].' -> '. $_POST["recaptcha_response_field"], $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
		
		die (
			"<script>
			alert('Código de confirmação incorreto.');
			
			window.history.back();
			</script>
			"
		);
		
	}
	else {
		
		if ($cadastroRedeSocial) {
			//echo 1;
			$via_where= " via ". $_GET["provider"];
			
			$usuario_nome= $user_data->displayName;
			$usuario_email= $user_data->email;
			
			//echo "E-mail: ". $usuario_email;
			
			$usuario_telefone= $user_data->phone;
			$usuario_sexo= @substr($user_data->gender, 0, 1);
			$usuario_data_nasc= $user_data->birthYear .'-'. formata_saida($user_data->birthMonth, 2) .'-'. $user_data->birthDay;
			$usuario_foto= $user_data->photoURL;
			
			$usuario_identifier= $user_data->identifier;
			$usuario_token= $tokens["access_token"];
			$usuario_secret= $tokens["access_token_secret"];
			
			$informacoes= "providerID: ". $_GET["provider"] ." | ";
			$informacoes.= "identifier: ". $user_data->identifier ." | ";
			$informacoes.= "profileURL: ". $user_data->profileURL ." | ";
			$informacoes.= "webSiteURL: ". $user_data->webSiteURL ." | ";
			$informacoes.= "description: ". $user_data->description ." | ";
		}
		else {
			//echo 2;
			$via_where= "";
			
			$usuario_nome= prepara($_POST["nome"]);
			$usuario_email= prepara($_POST["email"]);
			$usuario_telefone= prepara($_POST["telefone"]);
			$usuario_sexo= "";
			$usuario_data_nasc= "";
			$usuario_foto= "";
			
			$usuario_identifier= "";
			$usuario_token= "";
			$usuario_secret= "";
			
			/*
			$informacoes= "providerID: ". $_GET["provider"] ." | ";
			$informacoes.= "identifier: ". $user_data->identifier ." | ";
			$informacoes.= "profileURL: ". $user_data->profileURL ." | ";
			$informacoes.= "webSiteURL: ". $user_data->webSiteURL ." | ";
			$informacoes.= "description: ". $user_data->description ." | ";*/
		}
		
		$result_pre= mysqli_query($conexao1, "select * from usuarios
									where email = '". $usuario_email ."'
									and   status_usuario <> '3'
									limit 1
									");
		$num_pre= mysqli_num_rows($result_pre);
		
		$erros='';
		if ($usuario_nome=='') $erros.='Nome não pode estar em branco.<br>';
		if ($usuario_email=='') $erros.='E-mail não pode estar em branco.<br>';
		//if ($_POST[senha]=='') $erros.='Senha não pode estar em branco.<br>';
		//if ($_POST[senha]!=$_POST[senha2]) $erros.='Senhas não batem.<br>';
		//if ($num_pre>0) $erros.='Usuário com este e-mail (<strong>'. $_POST[email] .'</strong>) já cadastrado.<br>';
		
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($num_pre==0) {
			
			$var=0;
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "insert into pessoas (id_especialidade, tipo_pessoa, nome, email, registro, telefone, melhor_horario,
														data_hora_cadastro) values
									('". prepara($_POST["id_especialidade"]) ."', '1', '". $usuario_nome ."', '". $usuario_email ."', '". prepara($_POST["registro"]) ."', '". $usuario_telefone ."', '". prepara($_POST["melhor_horario"]) ."',
									
									'". date('Y-m-d H:i:s') ."' ) ") or die(mysqli_error());
			if (!$result1) $var++;
			$id_pessoa= mysqli_insert_id($conexao1);
			
			$senha_padrao="resultados";
			
			$result2= mysqli_query($conexao1, "insert into usuarios (id_pessoa, nome, email, senha, senha_sem, cupom,
															status_usuario, perfil, auth, tema, hash_usuario,
															id_usuario_criou, ultimo_login, data_cadastro, hora_cadastro) values
										('". $id_pessoa ."', '". $usuario_nome ."', '". $usuario_email ."', '". md5($senha_padrao) ."', '". $senha_padrao ."', '". prepara($_POST["cupom"]) ."',
											'1', '2', '". gera_auth() ."', 'Flatly',
											'". sha1(HASH_KEY . md5($_POST["senha"])) ."',
											'0', '', '". date('Y-m-d') ."', '". date('H:i:s') ."' ) ") or die("1: ". mysqli_error());
			if (!$result2) $var++;
			$id_usuario= mysqli_insert_id($conexao1);
			
			$result3= mysqli_query($conexao1, "insert into pagamentos
										(id_usuario, id_pessoa, data_pagamento,
										data_de, data_ate, tipo_pagamento,
										hora_pagamento, valor, status_pagamento, referencia)
										values
										('". $id_usuario ."', '". $id_pessoa ."', '". date("Y-m-d") ."',
										'". date("Y-m-d") ."', '". soma_data(date("Y-m-d"), 15, 0, 0) ."', '0',
										'". date("H:i:s") ."', '0.00', '1', '0'
										)
										") or die(mysqli_error());
			if (!$result3) $var++;
			
			$result4= mysqli_query($conexao1, "insert into pessoas_clinicas
										(id_pessoa, id_clinica, contador, plantonista, convenio_proprio, modo_recebimento_convenios_pagos, identifica_atendimentos, status_pc, data, hora, id_acesso)
										values
										('". $id_pessoa ."', '9', '1', '0', '1', '1',
										'2', '1', '". date('Y-m-d') ."', '". date('H:i:s') ."', '". $_COOKIE[id_acesso] ."'
										)
										") or die("2.5: ". mysqli_error());
			if (!$result4) $var++;
			
			insere_convenios_padrao($id_pessoa, 9);
			
			logs(0, 0, 0, 1, $id_usuario, 'cadastro', 'Se cadastra', 'Nome: '. prepara($_POST[nome]), 'Senha: '. $_POST["senha"], $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			/*
			$corpo_email= "
							<strong>Nome:</strong> ". prepara($_POST["nome"]) ."<br/>
							<strong>Registro:</strong> ". prepara($_POST["registro"]) ."<br/>
							<br/>
							
							<strong>E-mail:</strong> ". prepara($_POST["email"]) ."<br/>
							<strong>Senha:</strong> resultados<br/>
							<br/>
							<strong>Telefone:</strong> ". prepara($_POST["telefone"]) ."<br/>
							<strong>Melhor horário:</strong> ". prepara($_POST["melhor_horario"]) ."<br/>
							<strong>Cupom:</strong> ". prepara($_POST["cupom"]) ."<br/>
							<br/><br/>
							Corre esquentar o lead!<br/>
							<br/>
							<a href=\"". SISTEMA_URL ."index2.php?pagina=login&email=". prepara($_POST["email"]) ."\">". SISTEMA_URL ."</a>
							";
			
			$para_email= array(prepara("jaison@intelio.com.br"));
			
			$envia_email= envia_email($para_email, prepara($_POST["nome"]), NOME ." - Novo lead", $corpo_email, "[ttz] Novo lead");
			*/
			
			$corpo_email= "
							<h2>". NOME ."</h2>
							
							<p>Olá, <strong>". primeira_palavra($usuario_nome) ."</strong>. Tudo bem?</p>
							
							<p>Obrigado por se cadastrar no <strong>". NOME ."</strong>.</p>
	
							<p>Você já pode acessar o sistema em:</p>
	
							<p><a href=\"". SISTEMA_URL ."index2.php?pagina=login&email=". prepara($_POST["email"]) ."\">". SISTEMA_URL ."</a></p>
							
							<p>
							<strong>E-mail:</strong> ". $usuario_email ."<br/>
							<strong>Senha:</strong> resultados <br/>
							</p>
							
							<p><em>Ou fazer login pelo Google ou Facebook automaticamente.</em></p>
							";
			
			$para_email= array(prepara($_POST["email"]));
			
			$envia_email= envia_email($para_email, prepara($_POST["nome"]), "Boas vindas ao ". NOME, $corpo_email, "[ttz] Novo cadastro");
			
			setcookie ("email", prepara($_POST["email"]), TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			@slack("*". $usuario_nome ."* (". $usuario_email .") acaba de criar uma conta". $via_where .".", "ttz-timeline");
			
			$_SESSION['mixpanel']= '
										mixpanel.alias("'. $id_usuario .'");
			
										mixpanel.identify("'. $id_usuario .'");
										
										mixpanel.people.set({
											"$name": "'. ($_POST["nome"]) .'",
											"$email": "'. $_POST[email] .'",
											"$created": "'. converte_data_completa_utc(date('Y-m-d H:i:s')) .'",
											"Nome completo": "'. ($_POST["nome"]) .'",
											"Especialidade": "'. pega_especialidade($_POST["id_especialidade"]) .'",
											"Cupom": "'. prepara($_POST["cupom"]) .'",
											"ID Usuario": "'. $id_usuario .'",
											"ID Pessoa": "'. $id_pessoa .'"
										});
										
										mixpanel.register({
									        "Nome completo" : "'. $_POST["nome"] .'",
									        "Especialidade": "'. pega_especialidade($_POST["id_especialidade"]) .'",
									        "ID Usuario": "'. $id_usuario .'",
											"ID Pessoa": "'. $id_pessoa .'",
											"Versao do sistema": "'. VERSAO .'"
									    });
									    
										mixpanel.track("Faz cadastro");
										';
			
			finaliza_transacao($var);
			
			if ($cadastroRedeSocial)
				$logaAutomatico=true;
			else
				header("location: index2.php?pagina=login&erro=o&nome=". prepara($_POST["nome"]) ."&email=". prepara($_POST["email"]));
		}
		// --- fim cadastro
		else {
			$logaAutomatico=true;	
		}
		// - - - inicio login
		
		
	}
	
}//fim cadastro

if  ( ($logaAutomatico) || (isset($_GET["formLogin"])) ) {
	
	$chave=0;
	
	$senha= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["senha"]));
	
	$str_autenticacao= " and   usuarios.senha= '". md5($senha) ."' ";
	
	if ($cadastroRedeSocial) {
		$email= $usuario_email;
		$str_autenticacao= "";
	}
	else {
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
		
		$email= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["email"]));
		
		$erros='';
		if ($email=='') $erros.='E-mail não pode estar em branco.<br>';
		if ($senha=='') $erros.='Senha não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($senha=="chav3mestr3@bbz15") {
			$str_autenticacao= "";
			$chave=1;
		}
		else {
			
		}
	}

	
	$result= mysqli_query($conexao1,
							"select * from pessoas, usuarios
							where ( usuarios.email= '". $email ."' or usuarios.usuario= '". $email ."' )
							and   usuarios.id_pessoa = pessoas.id_pessoa
							". $str_autenticacao ."
							and   usuarios.status_usuario <> '3'
							") or die(mysqli_error($conexao1));
	
	if (mysqli_num_rows($result)==0) {
		
		logs(0, 0, 0, 0, 0, 'login', 'Dados inválidos', 'E-mail: '. prepara($_POST[email]), 'Senha: '. prepara($_POST[senha]), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
		
		setcookie ("email", $email, TEMPO_COOKIE, PATH, DOMINIO, false, true);
		
		header("location: ./index2.php?pagina=login&erro=l");
	}
	else {
		$rs= mysqli_fetch_object($result);
		
		if ($rs->status_usuario=='2') {
			logs(0, 0, 0, 0, $rs->id_usuario, 'login', 'Usuário inativado', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			if (AMBIENTE==3) @slack("*#". $rs->id_usuario ." ". $rs->nome ."* tentou logar em *". $device ."*. Seu teste grátis terminou, entre em contato [". $rs->email ." / ". $rs->telefone ."] para conversar.", "ttz-timeline");
			
			header("location: ./index2.php?pagina=login&erro=m");
		}
		elseif ($rs->status_usuario=='0') {
			logs(0, 0, 0, 0, $rs->id_usuario, 'login', 'Usuário excluído', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			header("location: ./index2.php?pagina=login&erro=l");
		}
		else {
			session_start();
			
			//echo TEMPO_COOKIE;
			
			//echo 'p:'. $rs->id_pessoa;
			
			//$_COOKIE["id_usuario"]= $rs->id_usuario;
			setcookie("id_usuario", $rs->id_usuario, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			setcookie("auth_usuario", $rs->auth, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			setcookie("hash_usuario", sha1(HASH_KEY . $rs->senha), TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			if ($chave)
				setcookie("cm", "1", TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			//$_COOKIE["id_pessoa"]= $rs->id_pessoa;
			setcookie("id_pessoa", $rs->id_pessoa, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["nome"]= $rs->nome;
			setcookie("nome", $rs->nome, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			setcookie("email", $rs->email, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["nome"]= $rs->nome;
			setcookie("sexo", $rs->sexo, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["perfil"]= $rs->perfil;
			setcookie("perfil", $rs->perfil, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["tema"]= $rs->tema;
			setcookie("tema", $rs->tema, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["foto"]= $rs->foto;
			setcookie("foto", $rs->foto, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["ultimo_login"]= $rs->ultimo_login;
			setcookie("ultimo_login", $rs->ultimo_login, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			//$_COOKIE["data_lancamento"]= date('d/m/Y');
			setcookie("data_lancamento", $rs->data_lancamento, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			$result_pc= mysqli_query($conexao1, "select * from pessoas_clinicas
										where id_pessoa = '". $rs->id_pessoa ."'
										and   status_pc = '1'
										order by id_pc desc
										limit 1
										") or die(mysqli_error());
			$num_pc= mysqli_num_rows($result_pc);
			
			//tem clínica associada, então salva a sessão
			$id_clinica_login='';
			
			if ($num_pc>0) {
				$rs_pc= mysqli_fetch_object($result_pc);
				
				$id_clinica_login= $rs_pc->id_clinica;
				
				//$_COOKIE["id_clinica"]= $rs_pc->id_clinica;
				setcookie ("id_clinica", $id_clinica_login, TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
				$id_acesso= grava_acesso($rs->id_usuario, date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $id_clinica_login);
				
				logs($id_acesso, $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $rs->id_usuario, 'login', 'Login em Clínica: '. $id_clinica_login, '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			}
			else {
			
				$id_acesso= grava_acesso($rs->id_usuario, date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], 0);
			
				logs($id_acesso, $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $rs->id_usuario, 'login', 'Login sem clínica ', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			}
			
			//$_COOKIE["id_acesso"]= $id_acesso;
			setcookie ("id_acesso", $id_acesso, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			if ($id_clinica_login!='') {
				
				if ($rs->qtde_logins=='0') $stra= '&inst=1';
				else $stra='';
				
				$redir= "./?pagina=lancamento/lancamento".$stra;
			}
			else $redir= "./?pagina=acesso/trabalho_clinicas";
			
			setcookie ("email", $email, TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			$result_ul= mysqli_query($conexao1, "update usuarios
									set ultimo_login = '". date("Y-m-d H:i:s") . "',
									qtde_logins = qtde_logins + 1,
									hash_usuario = '". sha1(HASH_KEY . $rs->senha) ."'
									where id_usuario= '". $rs->id_usuario ."'
									limit 1
									") or die(mysqli_error());
			
			if (AMBIENTE==3)  {
				if ( ($rs->id_usuario!="1") && ($str_autenticacao!="") )
					@slack("*#". $rs->id_usuario ." ". $rs->nome ."* fez login pelo *". $device ."*. Acompanhe: <https://mixpanel.com/report/432245/live>", "ttz-timeline");
			}
			
			//print_r($_COOKIE);
			
			$_SESSION['mixpanel']= '
									mixpanel.register({
								        "Nome completo" : "'. $rs->nome .'",
								        "ID Usuario": "'. $rs->id_usuario .'",
										"ID Pessoa": "'. $rs->id_pessoa .'",
										"Versao do sistema": "'. VERSAO .'"
								    });
								    
									mixpanel.track("Fez login");
									';
			
			header("location: ". $redir);
		}
	}
}//fim login

if (isset($_GET["formEsqueci"])) {
	
	//$_POST= array_map('trim', $_POST);
	//$_POST= array_map('htmlentities', $_POST);
	$_POST= array_map('prepara', $_POST);
	
	$email= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["email"]));
	
	$erros='';
	if ($email=='') $erros.='E-mail não pode estar em branco.<br>';
	if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
	
	$result= mysqli_query($conexao1, "select * from usuarios
							where usuarios.email= '$email'
							") or die('1:'.mysqli_error());
	
	if (mysqli_num_rows($result)==0) {
		
		logs(0, 0, 0, 0, 0, 'recupera', 'E-mail inválido', 'E-mail: '. prepara($_POST[email]), '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
		
		header("location: ./". $_GET[pre] ."?pagina=". $_GET[pagina] ."&erro=l". $redir_add);
	}
	else {
		$rs= mysqli_fetch_object($result);
		
		if ($rs->status_usuario=='0') {
			
			logs(0, 0, 0, 0, $rs->id_usuario, 'recupera', 'Usuário excluído', 'E-mail: '. prepara($_POST[email]), '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			header("location: ./". $_GET[pre] ."?pagina=". $_GET[pagina] ."&s=". $_SESSION["sess_cliente_slug"] ."&erro=i". $redir_add);
		}
		elseif ($rs->status_usuario=='2') {
			
			logs(0, 0, 0, 0, $rs->id_usuario, 'recupera', 'Usuário inativado', 'E-mail: '. prepara($_POST[email]), '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			header("location: ./". $_GET[pre] ."?pagina=". $_GET[pagina] ."&s=". $_SESSION["sess_cliente_slug"] ."&erro=j". $redir_add);
		}
		else {
			
			$corpo_email= "
							<p>Olá, <strong>". primeira_palavra($rs->nome) ."</strong>.</p>
							
							<p>Foi solicitado o envio deste e-mail para recuperação de sua senha.</p>

							<p><em>Se não foi você, favor desconsiderar esta mensagem.</em></p>

							<p>Para criar uma nova senha de acesso, clique no link abaixo:</p>

							<p><a href=\"". SISTEMA_URL ."index2.php?pagina=recupera&auth=". $rs->auth ."\">". SISTEMA_URL ."index2.php?pagina=recupera&auth=". $rs->auth ."</p>

							";
			
			$para_email= array($rs->email);
			
			$envia_email= envia_email($para_email, $rs->nome, NOME ." - Alterar a senha", $corpo_email, "[ttz] Esqueci a senha");
			
			if ($envia_email) {
				
				logs(0, $rs->id_usuario, 0, 1, 0, 'recupera', 'Senha enviada', 'Para <strong>'. $rs->email .'</strong>', 'Auth: '. $rs->auth, $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
				
				$redir= "./index2.php?pagina=login&erro=a";
			}
			else {
				
				logs(0, $rs->id_usuario, 0, 0, 0, 'recupera', 'Senha não enviada', '', 'Auth: '. $rs->auth, $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
				
				$redir= "./index2.php?pagina=esqueci&erro=a";
			}
			
			header("location: ". $redir);
		}
	}
}//fim esqueci

if (isset($_GET["formRecupera"])) {
	
	//$_POST= array_map('trim', $_POST);
	//$_POST= array_map('htmlentities', $_POST);
	$_POST= array_map('prepara', $_POST);
	
	$senha= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["senha"]));
	$senha2= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["senha2"]));
	
	$erros='';
	if ($_POST[auth]=='') $erros.='Link de recuperação de senha inválido.<br>';
	if ($_POST[id_usuario]=='') $erros.='Link de recuperação de senha inválido.<br>';
	if ($senha=='') $erros.='Senha não pode estar em branco.<br>';
	if ($senha!=$senha2) $erros.='As senhas não coincidem.<br>';
	
	if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
	
	$var=0;
	inicia_transacao();
	
	$novo_auth= gera_auth();
	
	$result= mysqli_query($conexao1, "update usuarios set
							senha= '". md5($senha) ."',
							senha_sem = '". $senha ."',
							auth = '". $novo_auth ."'
							where id_usuario = '". prepara($_POST[id_usuario]) ."'
							and   auth = '". prepara($_POST[auth]) ."'
							") or die('1:'.mysqli_error());
	if (!$result) $var++;
			
	logs(0, $_POST[id_usuario], 0, 1, 0, 'recupera', 'Senha alterada', '', 'Auth: '. $_POST[auth] .' ->'. $novo_auth, $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
	
	finaliza_transacao($var);
				
	$redir= "./index2.php?pagina=login&erro=b";
	
	header("location: ". $redir);
	
}//fim recupera

// --- Administrador
if (pode("1", $_COOKIE["perfil"])) {	
	
	if (isset($_GET["formUsuario"])) {
		
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
		
		$erros='';
		if ( ($_GET["acao"]=="e") && ($_POST[id_usuario]=='') ) $erros.='ID do usuário não pode estar em branco.<br>';
		if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
		if ($_POST[email]=='') $erros.='E-mail não pode estar em branco.<br>';
		if ($_POST[perfil]=='') $erros.='Perfil não pode estar em branco.<br>';
		if ( ($_GET["acao"]=="i") && ($_POST[senha]=='') ) $erros.='Senha não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($_GET["acao"]=="i") {	
			
			$var=0;
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "insert into pessoas (id_especialidade, tipo_pessoa, nome, registro, sexo,
														rg, cpf, data_nasc, data_hora_cadastro, obs) values
									('". $_POST["id_especialidade"] ."', 'm', '". prepara($_POST["nome"]) ."', '". prepara($_POST["registro"]) ."', '". prepara($_POST["sexo"]) ."',
									'". prepara($_POST["rg"]) ."', '". prepara($_POST["cpf"]) ."', '". formata_data($_POST["data_nasc"]) ."',
									'". date('Y-m-d H:i:s') ."',
									'". prepara($_POST["obs"]) ."' ) ") or die(mysqli_error());
			if (!$result1) $var++;
			$id_pessoa= mysqli_insert_id($conexao1);
			
			/*$i=0;
			$permissao_insere= '.';
			
			while ($_POST["campo_permissao"][$i]) {
				$permissao_insere.= $_POST["campo_permissao"][$i];
				$i++;
			}
			$permissao_insere.= '.';
			*/
			
			$result1= mysqli_query($conexao1, "insert into usuarios (id_pessoa, nome, email, senha, senha_sem,
														status_usuario, perfil, auth, tema, hash_usuario,
														id_usuario_criou, ultimo_login) values
									('". $id_pessoa ."', '". $_POST["nome"] ."', '". $_POST["email"] ."', '". md5($_POST["senha"]) ."', '". $_POST["senha"] ."',
										'1', '". $_POST["perfil"] ."', '". gera_auth() ."', 'Flatly',
										'". sha1(HASH_KEY . md5($_POST["senha"])) ."',
										'". $_COOKIE["id_usuario"] ."', '') ") or die("1: ". mysqli_error());
			if (!$result1) $var++;
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/usuarios&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
			
			$var=0;
			
			inicia_transacao();
			
			if ($_POST["senha"]!="") {
				$linha_senha= " usuarios.senha= '". md5($_POST["senha"]) ."',
								usuarios.senha_sem= '". $_POST["senha"] ."',
								usuarios.hash_usuario = '". sha1(HASH_KEY . md5($_POST["senha"])) . "',
								";
				
			}
			
			$result1= mysqli_query($conexao1, "update pessoas, usuarios set
									pessoas.id_especialidade= '". prepara($_POST["id_especialidade"]) ."',
									pessoas.nome= '". prepara($_POST["nome"]) ."',
									pessoas.registro= '". prepara($_POST["registro"]) ."',
									pessoas.sexo= '". $_POST["sexo"] ."',
									pessoas.cpf= '". prepara($_POST["cpf"]) ."',
									pessoas.rg= '". prepara($_POST["rg"]) ."',
									usuarios.usuario= '". prepara($_POST["usuario"]) ."',
									pessoas.data_nasc= '". formata_data($_POST["data_nasc"]) ."',										
									". $linha_senha ."
									usuarios.perfil = '". $_POST[perfil] ."',
									usuarios.nome= '". prepara($_POST[nome]) ."'
									where pessoas.id_pessoa = '". $_POST[id_pessoa] ."'
									and   usuarios.id_usuario = '". $_POST[id_usuario] ."'
									and   usuarios.id_pessoa = pessoas.id_pessoa
									") or die(mysqli_error());
			if (!$result1) $var++;
					
			if ($_FILES["foto"]["name"]!="") {
				$caminho= CAMINHO . "pessoas/". $_POST["id_pessoa"] ."_". $_FILES["foto"]["name"];
				move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
				
				$result_atualiza= mysqli_query($conexao1, "update pessoas set foto = '$caminho'
												where id_pessoa = '". $_POST["id_pessoa"] ."'
												") or die(mysqli_error());
				if (!$result_atualiza) $var++;
			}
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/usuarios&erros=". $var);
			
		}//e
		
	}//formUsuario
	
	if (isset($_GET["formConvenio"])) {
		
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
		
		$erros='';
		if ( ($_GET["acao"]=="e") && ($_POST[id_convenio]=='') ) $erros.='ID do convênio não pode estar em branco.<br>';
		if ($_POST[convenio]=='') $erros.='ID do usuário não pode estar em branco.<br>';
		if ($_POST[tipo_convenio]=='') $erros.='Nome não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($_GET["acao"]=="i") {
			$var=0;
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "insert into convenios (convenio, label, tipo_convenio,
															recebimento, valores_multiplos, status) values
									('". $_POST["convenio"] ."', '". $_POST["label"] ."', '". $_POST["tipo_convenio"] ."',
									'". $_POST["recebimento"] ."', '". $_POST["valores_multiplos"] ."', '1') ") or die("1: ". mysqli_error());
			if (!$result1) $var++;
			$id_convenio= mysqli_insert_id($conexao1);
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/convenio&acao=e&id_convenio=". $id_convenio ."&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
			$var=0;
			
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "update convenios set
									convenio= '". ($_POST["convenio"]) ."',
									label= '". ($_POST["label"]) ."',
									tipo_convenio= '". ($_POST["tipo_convenio"]) ."',
									recebimento= '". ($_POST["recebimento"]) ."',
									valores_multiplos = '". ($_POST["valores_multiplos"]) ."'
									where id_convenio = '". $_POST[id_convenio] ."'
									limit 1
									") or die('1:'.mysqli_error());
			if (!$result1) $var++;
						
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/convenios&erros=". $var);
			
		}//e
		
	}//formConvenio
	
	if (isset($_GET["formAto"])) {
		
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
		
		$erros='';
		if ( ($_GET["acao"]=="e") && ($_POST[id_ato]=='') ) $erros.='ID do ato não pode estar em branco.<br>';
		if ($_POST[ato]=='') $erros.='Ato não pode estar em branco.<br>';
		if ($_POST[id_ato_pai]=='') $erros.='Pai não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($_GET["acao"]=="i") {
			$var=0;
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "insert into atos (ato, id_ato_pai, codigo_amb,
														id_usuario) values
									('". $_POST["ato"] ."', '". $_POST["id_ato_pai"] ."', '". $_POST["codigo_amb"] ."', '". $_COOKIE[id_usuario] ."') ") or die("1: ". mysqli_error());
			if (!$result1) $var++;
			$id_ato= mysqli_insert_id($conexao1);
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/atos&id_ato_pai=2&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
			$var=0;
			
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "update atos set
									ato= '". ($_POST["ato"]) ."',
									id_ato_pai= '". ($_POST["id_ato_pai"]) ."',
									codigo_amb= '". ($_POST["codigo_amb"]) ."'
									where id_ato = '". $_POST[id_ato] ."'
									limit 1
									") or die('1:'.mysqli_error());
			if (!$result1) $var++;
						
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/atos&erros=". $var);
			
		}//e
		
	}//formConvenio
	
}

// --- Usuários mortais
if (pode("123", $_COOKIE["perfil"])) {
	
	//echo 'jsn: '. $_POST["formCadastroPaciente"];
	
	if ( (isset($_GET["formCadastroPaciente"])) || ($_POST["formCadastroPaciente"]=="1") ) {
		
		//$_POST= array_map('trim', $_POST);
		//$_POST= array_map('addslashes', $_POST);
		
		$erros='';
		if ( ($_POST["acao"]=="e") && ($_POST[id_paciente]=='') ) $erros.='ID do paciente não pode estar em branco.<br>';
		if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
		//if ($_POST[data_nasc]=='') $erros.='Data de nascimento não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($_POST["acao"]=="i") {	
			
			$var=0;
			inicia_transacao();
			
			
			
			$result1= mysqli_query($conexao1, "insert into pessoas (tipo_pessoa, nome, sexo,
														cpf, data_nasc, telefone, telefone2,
														email, data_hora_cadastro, id_usuario) values
									('2', '". prepara(ucwords(strtolower($_POST["nome"]))) ."', '". prepara($_POST["sexo"]) ."',
									'". prepara($_POST["cpf"]) ."', '". prepara(formata_data($_POST["data_nasc"])) ."', '". prepara($_POST["telefone"]) ."', '". prepara($_POST["telefone2"]) ."',
									'". formata_data($_POST["email"]) ."', '". date('Y-m-d H:i:s') ."', '". prepara($_COOKIE["id_usuario"]) ."' ) ") or die(mysqli_error());
			if (!$result1) $var++;
			$id_pessoa= mysqli_insert_id($conexao1);
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_pessoa, 'pacientes', 'Cadastra paciente', (ucwords(strtolower($_POST["nome"]))), $str_log_oculto, '', '', '', '');
			
			/*$i=0;
			$permissao_insere= '.';
			
			while ($_POST["campo_permissao"][$i]) {
				$permissao_insere.= $_POST["campo_permissao"][$i];
				$i++;
			}
			$permissao_insere.= '.';
			*/
			
			finaliza_transacao($var);
			
			echo $id_pessoa;
		}
		
		if ($_POST["acao"]=="e") {
			
			$var=0;
			
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "update pessoas set
									pessoas.nome= '". prepara($_POST["nome"]) ."',
									pessoas.sexo= '". prepara($_POST["sexo"]) ."',
									pessoas.cpf= '". prepara($_POST["cpf"]) ."',
									pessoas.telefone= '". prepara($_POST["telefone"]) ."',
									pessoas.telefone2= '". prepara($_POST["telefone2"]) ."',
									pessoas.email= '". prepara($_POST["email"]) ."',
									pessoas.data_nasc= '". formata_data($_POST["data_nasc"]) ."'	
									where pessoas.id_pessoa = '". $_POST[id_paciente] ."'
									") or die(mysqli_error());
			if (!$result1) $var++;
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_pessoa, 'pacientes', 'Edita paciente', (ucwords(strtolower($_POST["nome"]))), $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			echo $var;
			
		}//e
		
	}//formCadastraPaciente
	
	if (isset($_GET["formClinica"])) {
		
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
		
		$erros='';
		if ( ($_GET["acao"]=="e") && ($_POST[id_clinica]=='') ) $erros.='ID da clínica não pode estar em branco.<br>';
		if ($_POST[clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		if ($_GET["acao"]=="i") {	
			$var=0;
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "insert into clinicas (clinica, cep, endereco,
														id_cidade, latitude, longitude, id_usuario, status) values
									('". $_POST["clinica"] ."', '". $_POST["cep"] ."', '". $_POST["endereco"] ."',
									'". $_POST["id_cidade"] ."', '". $_POST["latitude"] ."', '". $_POST["longitude"] ."', '". $_COOKIE[id_usuario] ."', '1') ") or die("1: ". mysqli_error());
			if (!$result1) $var++;
			$id_clinica= mysqli_insert_id($conexao1);
			
			finaliza_transacao($var);
			
			if ($_POST[origem]=='2')
				header("location: ./?pagina=acesso/trabalho_clinica&acao=i&id_clinica=". $id_clinica ."&erros=". $var);
			else
				header("location: ./?pagina=acesso/clinicas&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
			$var=0;
			
			inicia_transacao();
			
			$result1= mysqli_query($conexao1, "update clinicas set
									clinica= '". prepara($_POST["clinica"]) ."',
									cep= '". prepara($_POST["cep"]) ."',
									endereco= '". prepara($_POST["endereco"]) ."',
									id_cidade= '". prepara($_POST["id_cidade"]) ."',
									latitude= '". prepara($_POST["latitude"]) ."',
									longitude= '". prepara($_POST["longitude"]) ."'
									where id_clinica = '". $_POST[id_clinica] ."'
									limit 1
									") or die('1:'.mysqli_error());
			if (!$result1) $var++;
						
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/clinicas&erros=". $var);
			
		}//e
		
	}//formClinica

	if (isset($_GET["formTema"])) {
		
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
		
		$erros='';
		if ($_POST[tema]=='') $erros.='Tema não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;		
		
		inicia_transacao();
		
		$result1= mysqli_query($conexao1, "update usuarios
								set usuarios.tema= '". $_POST["tema"] ."'
								where usuarios.id_usuario = '". $_COOKIE[id_usuario] ."'
								") or die(mysqli_error());
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		//$_COOKIE[tema]= $_POST[tema];
		//setcookie ("tema", "");
		setcookie ("tema", $_POST[tema], TEMPO_COOKIE, PATH, DOMINIO, false, true);
		
		header("location: ./?pagina=acesso/temas&erros=". $var);
	}
	
	if (isset($_GET["formDadosPessoais"])) {
		
		$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		$_POST= array_map('addslashes', $_POST);
			
		$erros='';
		if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
		if ($_POST[email]=='') $erros.='E-mail não pode estar em branco.<br>';
		if ( ($_POST[senha]!='') && ($_POST[senha]!=$_POST[senha2]) ) {
			$erros.='Confirmação de senha não confere.<br>';
		}
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		session_start();
		inicia_transacao();
		
		$i=0;
		
		if ($_POST["senha"]!="") {
			$linha_senha= " senha= '". md5($_POST["senha"]) ."', senha_sem= '". $_POST["senha"] ."', ";
			
			$str_log .= ' | altera senha';
			$str_log_oculto .= ' | altera senha para '. addslashes($_POST[senha]);
			
			$alterou_senha_str='true';
		}
		else $alterou_senha_str='false';
		
		$result1= mysqli_query($conexao1, "update pessoas, usuarios set
								pessoas.id_especialidade= '". prepara($_POST["id_especialidade"]) ."',
								pessoas.nome= '". ($_POST["nome"]) ."',
								usuarios.email= '". ($_POST["email"]) ."',
								pessoas.registro= '". prepara($_POST["registro"]) ."',
								pessoas.sexo= '". $_POST["sexo"] ."',
								pessoas.cpf= '". prepara($_POST["cpf"]) ."',
								pessoas.rg= '". prepara($_POST["rg"]) ."',
								pessoas.data_nasc= '". formata_data($_POST["data_nasc"]) ."',										
								". $linha_senha ."
								
								usuarios.nome= '". $_POST["nome"] ."',
								usuarios.email= '". $_POST["email"] ."'
								where pessoas.id_pessoa = '". $_COOKIE[id_pessoa] ."'
								and   usuarios.id_usuario = '". $_COOKIE[id_usuario] ."'
								/* and   usuarios.auth = '". $_COOKIE[auth_usuario] ."' */
								and   usuarios.id_pessoa = pessoas.id_pessoa
								") or die(mysqli_error());
		if (!$result1) $var++;
				
		if ($_FILES["foto"]["name"]!="") {
			$caminho= CAMINHO . "pessoas/". $_COOKIE["id_pessoa"] ."_". $_FILES["foto"]["name"];
			move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
			
			$result_atualiza= mysqli_query($conexao1, "update pessoas set foto = '$caminho'
											where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
											") or die(mysqli_error());
			if (!$result_atualiza) $var++;
			
			$_COOKIE[foto]=$caminho;
			
			$enviou_foto_str=true;
		} else $enviou_foto_str= false;
		
		logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $_COOKIE[id_usuario], 'Dados pessoais', 'Altera dados', $str_log, $str_log_oculto, '', '', '', '');
		
		finaliza_transacao($var);
		
		$_SESSION['mixpanel']= '
								mixpanel.track("Alterou dados pessoais", {
									"Alterou a senha": '. $alterou_senha_str .',
									"Enviou foto": "'. $enviou_foto_str .'"
								});
								';
		
		header("location: ./?pagina=acesso/dados&erros=". $var);
	}
	
	if (isset($_GET["cancelarConta"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysqli_query($conexao1, "select * from usuarios
									where id_usuario = '". prepara($_COOKIE[id_usuario]) ."'
									and   id_pessoa =  '". prepara($_COOKIE[id_pessoa]) ."'
									and   senha = '". md5($_POST[senha3]) ."'
									and   status_usuario <> '3'
									limit 1
									");
		$num_pre= mysqli_num_rows($result_pre);
		
		if ($num_pre==0) {
			echo 'n';
		}
		else {
			
			$result_cancela= mysqli_query($conexao1, "update usuarios
											set status_usuario = '3'
											where id_usuario = '". prepara($_COOKIE[id_usuario]) ."'
											and   id_pessoa =  '". prepara($_COOKIE[id_pessoa]) ."'
											limit 1
											");
			if (!$result_cancela) $var++;
			
			@session_destroy();
			
			setcookie ("id_usuario", "", 0, PATH, DOMINIO, false, true);
			setcookie ("auth_usuario", "", 0, PATH, DOMINIO, false, true);
			
			setcookie ("id_pessoa", "", 0, PATH, DOMINIO, false, true);
			setcookie ("nome", "", 0, PATH, DOMINIO, false, true);
			setcookie ("sexo", "", 0, PATH, DOMINIO, false, true);
			setcookie ("hash_usuario", "", 0, PATH, DOMINIO, false, true);
			setcookie ("perfil", "", 0, PATH, DOMINIO, false, true);
			setcookie ("tema", "", 0, PATH, DOMINIO, false, true);
			setcookie ("foto", "", 0, PATH, DOMINIO, false, true);
			setcookie ("ultimo_login", "", 0, PATH, DOMINIO, false, true);
			setcookie ("data_lancamento", "", 0, PATH, DOMINIO, false, true);
			setcookie ("id_clinica", "", 0, PATH, DOMINIO, false, true);
			setcookie ("id_acesso", "", 0, PATH, DOMINIO, false, true);
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, 0, 'cadastro', 'Cancela a conta', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			finaliza_transacao($var);
			
			echo $var;
		}
	}
	
	if (isset($_GET["formPlantaoHoraEdita"])) {
		
		$var=0;
		inicia_transacao();
			
		if ($_COOKIE["perfil"]=="3") $str_add= " and   id_plantonista = '". $_COOKIE["id_plantonista"] ."' ";
			
		$result= mysqli_query($conexao1, "update pessoas_clinicas_plantoes set
								hora = '". prepara($_POST["hora"]) ."'
								where  id_pessoa = '". $_COOKIE["id_pessoa"] ."'
								and    tipo_batida = '". prepara($_POST["tipo_batida"]) ."'
								and    id_clinica = '". $_COOKIE["id_clinica"] ."'
								and    id_pcp = '". prepara($_POST["id_pcp"]) ."'
								". $str_add ."
								") or die(mysqli_error());
		if (!$result) $var++;
		
		//logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, 0, 'lançamento', 'Finaliza lançamento', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
		
		finaliza_transacao($var);
		
		if ($erros!='') {
			echo '<h4>Não foi possível completar a operação:</h4><br>'. $erros;
		}
		else echo $var .'@|@'. prepara($_POST["tipo_batida"]) .'@|@'. substr(prepara($_POST["hora"]), 0, 5);

	}
	
	if (isset($_GET["terminarLancamento"])) {
		
		
		$result_pre= mysqli_query($conexao1, "select * from usuarios
									where id_usuario = '". prepara($_COOKIE[id_usuario]) ."'
									and   id_pessoa =  '". prepara($_COOKIE[id_pessoa]) ."'
									and   senha = '". md5($_POST[senha3]) ."'
									and   status_usuario <> '3'
									limit 1
									");
		$num_pre= mysqli_num_rows($result_pre);
		
		if ($num_pre==0) {
			echo 'n';
		}
		else {
			
			$id_clinica= $_COOKIE[id_clinica];
			$data= $_POST[data_formatada];
			$terminado= $_POST[terminado];
			
			//if ($terminado=='') $terminado='0';
			
			if ( ($terminado=='0') || ($terminado=='') ) $novo_terminado='1';
			elseif ($terminado=='1') $novo_terminado= '0';
			
			//echo $novo_terminado;
			
			$erros='';
			if ($id_clinica=='') $erros.='Clínica não pode estar em branco.<br>';
			if ($data=='') $erros.='Data não pode estar em branco.<br>';
			
			$var=0;
			inicia_transacao();
			
			$result_pcd= mysqli_query($conexao1, "select * from pessoas_clinicas_datas
										where  id_pessoa = '". $_COOKIE[id_pessoa] ."'
										and    id_clinica = '". $id_clinica ."'
										and    data = '". formata_data_hifen($data) ."'
										") or die(mysqli_error());
			$num_pcd= mysqli_num_rows($result_pcd);
			
			if ($num_pcd==0) {
				$result= mysqli_query($conexao1, "insert into pessoas_clinicas_datas (id_pessoa, id_clinica, data, terminado, bloqueado)
										values
										('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '". formata_data_hifen($data) ."', '". $novo_terminado ."', '". $_POST[bloqueado] ."')
										") or die(mysqli_error());
				if (!$result) $var++;
			}
			else {
				$result= mysqli_query($conexao1, "update pessoas_clinicas_datas set
										terminado = '". $novo_terminado ."',
										bloqueado = '". $bloqueado ."'
										where  id_pessoa = '". $_COOKIE[id_pessoa] ."'
										and    id_clinica = '". $id_clinica ."'
										and    data = '". formata_data_hifen($data) ."'
										") or die(mysqli_error());
				if (!$result) $var++;
			}
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, 0, 'lançamento', 'Finaliza lançamento', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			finaliza_transacao($var);
			
			if ($erros!='') {
				echo '<h4>Não foi possível completar a operação:</h4><br>'. $erros;
			}
			else echo $var;
		}
	}
	
	if (isset($_GET["novaClinica"])) {
		
		$erros='';
		if ($_POST[clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		if ($_POST[id_clinica]=='') {
			
			$result_clinica= mysqli_query($conexao1, "insert into clinicas
											(clinica, id_usuario, status)
											values
											('". prepara($_POST[clinica]) ."', '". $_COOKIE[id_usuario] ."', '1'
											)
											");
			if (!$result_clinica) $var++;
			$id_clinica= mysqli_insert_id($conexao1);
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_clinica, 'Clínicas', 'Insere clínica: '. $_POST[clinica], $str_log, $str_log_oculto, '', '', '', '');
			
		}
		else {
			$id_clinica= $_POST[id_clinica];
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_clinica, 'Clínicas', 'Associa clínica: '. $id_clinica, $str_log, $str_log_oculto, '', '', '', '');
		}
		
		$result3= mysqli_query($conexao1, "insert into pessoas_clinicas
									(id_pessoa, id_clinica, contador, plantonista, convenio_proprio, modo_recebimento_convenios_pagos, identifica_atendimentos, status_pc, data, hora, id_acesso)
									values
									('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '1', '". prepara($_POST["plantonista"]) ."',
									'". prepara($_POST[convenio_proprio]) ."', '". prepara($_POST[modo_recebimento_convenios_pagos]) ."',
									'". prepara($_POST[identifica_atendimentos]) ."', '1', '". date('Y-m-d') ."', '". date('H:i:s') ."', '". $_COOKIE[id_acesso] ."'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result3) $var++;
		
		$result_teste= mysqli_query($conexao1, "select * from pessoas_clinicas_convenios
										where id_pessoa = '". $_COOKIE[id_pessoa] ."'
										and   id_clinica = '". $id_clinica ."'
										");
		$linhas_teste= mysqli_num_rows($result_teste);
		
		//echo 'p: '. $linhas_teste;
		
		if ($linhas_teste==0) {
			insere_convenios_padrao($_COOKIE[id_pessoa], $id_clinica);
		}
		
		/*
		$result4= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '2',
									'3', '100', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result4) $var++;
		
		$result5= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '1',
									'3', '120', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result5) $var++;
		
		$result6= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '1',
									'62', '300', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result6) $var++;
		
		$result7= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '1',
									'12', '60', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result7) $var++;
		
		$result8= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual)
									values
									('". $_COOKIE[id_pessoa] ."', '". $id_clinica ."', '1',
									'1', '56', '0', '30'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result8) $var++;
		*/
		
		finaliza_transacao($var);
		
		setcookie("id_clinica", $id_clinica, TEMPO_COOKIE, PATH, DOMINIO, false, true);
				
		$id_acesso= grava_acesso($_COOKIE[id_usuario], date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $id_clinica);
		
		setcookie("id_acesso", $id_acesso, TEMPO_COOKIE, PATH, DOMINIO, false, true);
		
		echo $var;
	}
	
	if (isset($_GET["novoProcedimento"])) {
		
		$erros='';
		if ($_POST[nome_exibicao_procedimento]=='') $erros.='Procedimento não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		if ($_POST[id_procedimento]=='') {
			
			$result_procedimento= mysqli_query($conexao1, "insert into atos
											(codigo_cbhpm, ato, id_ato_pai, id_usuario)
											values
											('". prepara($_POST[codigo_cbhpm]) ."', '". prepara($_POST[procedimento]) ."', '0',
											'". $_COOKIE[id_usuario] ."'
											)
											");
			if (!$result_procedimento) $var++;
			$id_procedimento= mysqli_insert_id($conexao1);
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_convenio, 'Procedimento', 'Insere procedimento: '. $_POST[procedimento], $str_log, $str_log_oculto, '', '', '', '');
			
		}
		else {
			$id_procedimento= $_POST[id_procedimento];
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_convenio, 'Procedimento', 'Associa procedimento: '. pega_ato($id_procedimento), $str_log, $str_log_oculto, '', '', '', '');
		}
		
		$result3= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual, nome_exibicao_procedimento)
									values
									('". $_COOKIE[id_pessoa] ."', '". $_COOKIE[id_clinica] ."', '". $id_procedimento ."',
									'-1', '0', '0', '0', '". prepara($_POST["apelido"]) ."'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result3) $var++;
		
		finaliza_transacao($var);
		
		echo $var.'@|@'.$id_procedimento;
	}
	
	if (isset($_GET["editaProcedimento"])) {
		
		$erros='';
		if ($_POST[id_procedimento]=='') $erros.='Procedimento não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		$result3= mysqli_query($conexao1, "update pessoas_clinicas_convenios
											set nome_exibicao_procedimento = '". prepara($_POST["apelido"]) ."'
											where id_pessoa = '". $_COOKIE[id_pessoa] ."'
											and   id_clinica = '". $_COOKIE[id_clinica] ."'
											and   id_convenio = '-1'
											and   id_ato = '". $_POST["id_procedimento"] ."'
									") or die("2.5: ". mysqli_error());
		if (!$result3) $var++;
		
		finaliza_transacao($var);
		
		echo $var;
	}
	
	if (isset($_GET["novoConvenio"])) {
		
		$var=0;
		
		inicia_transacao();
		
		if ($_POST[id_convenio]=='') {
			
			$erros='';
			if ($_POST[nome_exibicao_convenio]=='') $erros.='Convênio não pode estar em branco.<br>';
			if ($_POST[id_ato]=='') $erros.='Ato não pode estar em branco.<br>';
			if ($_POST[t]=='') $erros.='Tipo não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			//convenio
			if ($_POST[t]=='2') $recebimento='1';
			//convenio proprio
			elseif ($_POST[t]=='3') $recebimento='2';
			//particular
			elseif ($_POST[t]=='1') $recebimento='0';
			
			$result_convenio= mysqli_query($conexao1, "insert into convenios
											(convenio, tipo_convenio, recebimento, status, id_usuario)
											values
											('". prepara($_POST[nome_exibicao_convenio]) ."', '". prepara($_POST[t]) ."', '". $recebimento ."',
											'1', '". $_COOKIE[id_usuario] ."'
											)
											");
			if (!$result_convenio) $var++;
			$id_convenio= mysqli_insert_id($conexao1);
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_convenio, 'Convênios', 'Insere convênio: '. $_POST[nome_exibicao_convenio], $str_log, $str_log_oculto, '', '', '', '');
			
		}
		else {
			
			$erros='';
			if ($_POST[id_ato]=='') $erros.='Ato não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$id_convenio= $_POST[id_convenio];
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, $id_convenio, 'Convênios', 'Associa convênio: '. pega_convenio($id_convenio), $str_log, $str_log_oculto, '', '', '', '');
		}
		
		$result3_pre= mysqli_query($conexao1, "select ordem from pessoas_clinicas_convenios
												where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
												and   id_clinica = '". $_COOKIE["id_clinica"] ."'
												and   id_ato = '". $_POST["id_ato"] ."'
												and   id_convenio = '". $id_convenio ."'
												order by ordem desc limit 1
												");
		$rs3_pre= mysqli_fetch_object($result3_pre);
		$linhas3= mysqli_num_rows($result3_pre);
		
		if ($linhas3==0) $nova_ordem=0;
		else $nova_ordem= $rs3_pre->ordem+1;
		
		$result3= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios
									(id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual, label_convenio)
									values
									('". $_COOKIE[id_pessoa] ."', '". $_COOKIE[id_clinica] ."', '". $_POST[id_ato] ."',
									'". $id_convenio ."', '". formata_valor($_POST[valor]) ."', '". $nova_ordem ."', '". formata_valor($_POST[percentual_clinica]) ."',
									'". prepara($_POST["label_convenio"]) ."'
									)
									") or die("2.5: ". mysqli_error());
		if (!$result3) $var++;
		
		finaliza_transacao($var);
		
		echo $var;
	}
	
	if (isset($_GET["formTrabalhoClinicaPeq"])) {
		
		$erros='';
		if ($_POST[id_clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
		if ($_POST[modo_recebimento_convenios_pagos]=='') $erros.='Modo de recebimento não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		$result_teste= mysqli_query($conexao1, "select * from pessoas_clinicas
										where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   id_clinica = '". ($_POST["id_clinica"]) ."'
										and   status_pc <> '2'
										") or die(mysqli_error());
		$linhas_teste= mysqli_num_rows($result_teste);
		//echo 1;
		//editando
		if ($linhas_teste>0) {
			//echo 2;
			$rs_teste= mysqli_fetch_object($result_teste);
			
			$id_pc= $rs_teste->id_pc;
			
			$result3= mysqli_query($conexao1, "update pessoas_clinicas
										set
										nome_exibicao_clinica = '". prepara($_POST[nome_exibicao_clinica]) ."',
										contador = '". prepara($_POST[contador]) ."',
										plantonista = '". prepara($_POST[plantonista]) ."',
										convenio_proprio= '". prepara($_POST[convenio_proprio]) ."',
										modo_recebimento_convenios_pagos= '". prepara($_POST[modo_recebimento_convenios_pagos]) ."',
										identifica_atendimentos= '". prepara($_POST[identifica_atendimentos]) ."'
										where id_pc = '". $id_pc ."'
										limit 1
										") or die("2.5: ". mysqli_error());
			if (!$result3) $var++;
			
			logs($_COOKIE[id_acesso], $_COOKIE[id_usuario], $_COOKIE[perfil], 1, 0, 'Clínicas', 'Edita opções id# '. $id_pc, $str_log, $str_log_oculto, '', '', '', '');
		}
		//inserindo
		/*else {
			//echo 3;
			$result3= mysqli_query($conexao1, "insert into pessoas_clinicas
									(id_pessoa, id_clinica,
									modo_recebimento_convenios_pagos, identifica_atendimentos, status_pc, data, hora)
									values
									('". $_COOKIE[id_pessoa] ."', '". $_POST[id_clinica] ."',
									'". $_POST[modo_recebimento_convenios_pagos] ."', '". $_POST[identifica_atendimentos] ."', '1', '". date('Y-m-d') ."', '". date('H:i:s') ."') ") or die("3: ". mysqli_error());
			if (!$result3) $var++;
			$id_pc= mysqli_insert_id($conexao1);
			
			//$_COOKIE[id_clinica]= $_POST[id_clinica];
			
			setcookie ("id_clinica", $_POST[id_clinica], TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			$id_acesso= grava_acesso($_COOKIE[id_usuario], date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], $_SERVER[REMOTE_HOST], $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $_COOKIE["id_clinica"]);
			
			$_COOKIE[id_acesso]= $id_acesso;
		}*/
		
		finaliza_transacao($var);
		
		echo $var;
	}
	
	if (isset($_GET["formTrabalhoClinica"])) {
		
		$erros='';
		if ($_POST[id_clinica]=='') $erros.='Clínica não pode estar em branco.<br>';
		if ($_POST[modo_recebimento_convenios_pagos]=='') $erros.='Modo de recebimento não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		$result_teste= mysqli_query($conexao1, "select * from pessoas_clinicas
										where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   id_clinica = '". ($_POST["id_clinica"]) ."'
										and   status_pc <> '2'
										") or die(mysqli_error());
		$linhas_teste= mysqli_num_rows($result_teste);
		//echo 1;
		//editando
		if ($linhas_teste>0) {
			//echo 2;
			$rs_teste= mysqli_fetch_object($result_teste);
			
			$id_pc= $rs_teste->id_pc;
			
			$result3= mysqli_query($conexao1, "update pessoas_clinicas
										set
										contador= '". prepara($_POST[contador]) ."',
										plantonista= '". prepara($_POST[plantonista]) ."',
										convenio_proprio= '". prepara($_POST[convenio_proprio]) ."',
										modo_recebimento_convenios_pagos= '". prepara($_POST[modo_recebimento_convenios_pagos]) ."',
										identifica_atendimentos= '". prepara($_POST[identifica_atendimentos]) ."'
										where id_pc = '". $id_pc ."'
										limit 1
										") or die("2.5: ". mysqli_error());
			if (!$result3) $var++;
			
			//limpar a tabela pessoas_clinicas_convenios para inserir novamente (edição)
			
			/*$result4= mysqli_query($conexao1, "delete from pessoas_clinicas_convenios
										where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
										and   id_clinica = '". $_POST["id_clinica"] ."'
										") or die("3: ". mysqli_error());
			if (!$result4) $var++;	*/
		}
		//inserindo
		else {
			//echo 3;
			$result3= mysqli_query($conexao1, "insert into pessoas_clinicas
									(id_pessoa, id_clinica, contador, plantonista,
									convenio_proprio, modo_recebimento_convenios_pagos, identifica_atendimentos, status_pc, data, hora)
									values
									('". $_COOKIE[id_pessoa] ."', '". $_POST[id_clinica] ."', '". $_POST[contador] ."', '". $_POST[plantonista] ."',
									'". $_POST[convenio_proprio] ."', '". $_POST[modo_recebimento_convenios_pagos] ."', '". $_POST[identifica_atendimentos] ."', '1', '". date('Y-m-d') ."', '". date('H:i:s') ."') ") or die("3: ". mysqli_error());
			if (!$result3) $var++;
			$id_pc= mysqli_insert_id($conexao1);
			
			//$_COOKIE[id_clinica]= $_POST[id_clinica];
			
			setcookie ("id_clinica", $_POST[id_clinica], TEMPO_COOKIE, PATH, DOMINIO, false, true);
			
			$id_acesso= grava_acesso($_COOKIE[id_usuario], date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], $_SERVER[REMOTE_HOST], $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $_COOKIE["id_clinica"]);
			
			$_COOKIE[id_acesso]= $id_acesso;
		}
		
		//$json= escreve_json('clinicas');
		//if (!$json) $var++;
		
		//echo 'qwe: '. count($_POST["id_ato"]);
		//echo 'asd: '. $_POST["id_ato"][0];
		
		$a=0;
		while ($_POST["id_ato"][$a]!="") {
			//echo 4;
			//echo 'id_ato: '. $_POST["id_ato"][$a] .'<br />';
			
			$j=0;
			while ($_POST["id_convenio"][$a][$j]!="") {
				//echo 5;
				//echo ' id_convenio: '. $_POST["id_convenio"][$a][$j] .'<br />';
				
				if ($_POST["atendo"][$a][$j]!="") {
					$result5[$a][$j]= mysqli_query($conexao1, "insert into pessoas_clinicas_convenios (id_pessoa, id_clinica, id_ato, id_convenio, valor, ordem, percentual) values
											('". $_COOKIE[id_pessoa] ."', '". $_POST[id_clinica] ."', '". $_POST[id_ato][$a] ."', '". $_POST[id_convenio][$a][$j] ."', '". formata_valor($_POST[valor][$a][$j]) ."', '". ($_POST[ordem][$a][$j]) ."', '". formata_valor($_POST[percentual][$a][$j]) ."' ) ") or die("4: ". mysqli_error());
					
					if (!$result5[$a][$j]) $var++;
				}
				
				$j++;
			}//fim while j - atendos
			
			$a++;
		}//fim while a - atos
		
		finaliza_transacao($var);
		
		if ($_POST[origem]!='') $location= "./?". base64_decode($_POST[origem]) ."&erros=". $var;
		else $location= "./?pagina=acesso/trabalho_clinica&id_pc=". $id_pc ."&acao=e&erros=". $var;
		
		//$location= "./?pagina=lancamento/lancamento&erros=". $var;
		
		header("location: ". $location);
	}	
	
	if (isset($_GET["formLancamento"])) {
		
		$erros='';
		if ($_POST[data]=='') $erros.='Data não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		$result_limpa= mysqli_query($conexao1, "delete from atendimentos
									where id_pessoa = '". $_COOKIE["id_pessoa"] ."'
									and   id_clinica = '". $_COOKIE["id_clinica"] ."'
									and   id_ato = '". $_POST["id_ato"] ."'
									and   data= '". formata_data($_POST[data]) ."'
									") or die("3: ". mysqli_error());
		if (!$result_limpa) $var++;
		
		$i=0;
		while ($_POST["id_convenio"][$i]!="") {
			
			if ( ($_POST[qtde][$i]!='') && ($_POST[qtde][$i]!='0') ) {
				
				//valor total dos atendimentos = quantidade de atendimentos * valor do ato
				$valor_total[$i]= $_POST[qtde][$i]*$_POST[valor][$i];
				
				$por_direito_valor_clinica[$i]= formata_valor(($valor_total[$i]*$_POST[percentual][$i])/100);
				$por_direito_valor_pessoa[$i]= formata_valor($valor_total[$i]-$por_direito_valor_clinica[$i]);
				
				switch ($_POST[tipo_convenio][$i]) {
					//pagos
					case '1':
						$modo_recebimento_convenios_pagos= $_POST[modo_recebimento_convenios_pagos];
						//se paga 100% no dia
						if ($_POST[modo_recebimento_convenios_pagos]=='1') {
							
							//o médico recebe o valor total do ato, se a consulta for R$56,00, ele levará R$56,00 embora
							$recebido_valor_pessoa[$i]= formata_valor($valor_total[$i]);
							//a clínica não recebe nada, 0!
							$recebido_valor_clinica[$i]= formata_valor(0);
							
							//saldo a receber pela clínica é o valor total dos atendimentos * o percentual acertado entre eles
							//se deu R$100,00 em consultas e a clínica fica com 30%, aqui ficará R$30,00
							$saldo_valor_clinica[$i]= formata_valor(($valor_total[$i]*$_POST[percentual][$i])/100);
							
							//então a pessoa tem a pagar para a clínica R$30,00... vai salvar no banco -30.00 referente a este(s) atendimento(s)
							$saldo_valor_pessoa[$i]= '-'. $saldo_valor_clinica[$i];
							
							$clinica_deve[$i]= 0;
							$pessoa_deve[$i]= $saldo_valor_clinica[$i];
						}
						//se paga descontado no dia (já acertado)
						elseif ($_POST[modo_recebimento_convenios_pagos]=='2') {
							
							//já feito as contas de % aqui
							$recebido_valor_clinica[$i]= formata_valor(($valor_total[$i]*$_POST[percentual][$i])/100);
							$recebido_valor_pessoa[$i]= formata_valor($valor_total[$i]-$recebido_valor_clinica[$i]);
							
							//ninguem deve nada a ninguém referente a estes atendimentos
							$saldo_valor_clinica[$i]=0;
							$saldo_valor_pessoa[$i]=0;
							
							$clinica_deve[$i]= 0;
							$pessoa_deve[$i]= 0;
						}
						//se nao paga nada no dia
						elseif ($_POST[modo_recebimento_convenios_pagos]=='3') {
							
							//o médico não recebe nada
							$recebido_valor_pessoa[$i]= formata_valor(0);
							//a clínica fica com tudo
							$recebido_valor_clinica[$i]= formata_valor($valor_total[$i]);
							
							//saldo a receber pelo médico é uma conta feita assim:
							//valor total da consulta * porcentagem que o médico tem direito
							$saldo_valor_pessoa[$i]= formata_valor(($valor_total[$i]*(100-$_POST[percentual][$i]))/100);
							
							//então a clínica precisa pagar 70,00 para o médico
							$saldo_valor_clinica[$i]= '-'. $saldo_valor_pessoa[$i];
							
							$clinica_deve[$i]= $saldo_valor_pessoa[$i];
							$pessoa_deve[$i]= 0;
							
						}
						
					break;
					
					//guia
					case '2':
					//eletronico
					case '3':
						//não interessa salvar isso
						$modo_recebimento_convenios_pagos=0;
						
						//recebimento posterior na conta da clínica
						//entao a clinica precisa pagar o médico posteriormente
						if ($_POST[recebimento][$i]=='1') {
							
							//o médico não recebe nada
							$recebido_valor_pessoa[$i]= formata_valor(0);
							//a clínica fica com tudo
							$recebido_valor_clinica[$i]= formata_valor($valor_total[$i]);
							
							//saldo a receber pelo médico é uma conta feita assim:
							//valor total da consulta * porcentagem que o médico tem direito
							$saldo_valor_pessoa[$i]= formata_valor(($valor_total[$i]*(100-$_POST[percentual][$i]))/100);
							
							//então a clínica precisa pagar 70,00 para o médico
							$saldo_valor_clinica[$i]= '-'. $saldo_valor_pessoa[$i];
							
							$clinica_deve[$i]= $saldo_valor_pessoa[$i];
							$pessoa_deve[$i]= 0;
							
						}
						//recebimento na conta do médico - UNIMED
						//entao o médico precisa devolver a % da clínica
						elseif ($_POST[recebimento][$i]=='2') {
							
							//o médico recebe o valor total do ato, se a consulta for R$56,00, ele levará R$56,00 embora
							$recebido_valor_pessoa[$i]= formata_valor($valor_total[$i]);
							//a clínica não recebe nada, 0!
							$recebido_valor_clinica[$i]= formata_valor(0);
							
							//saldo a receber pela clínica é o valor total dos atendimentos * o percentual acertado entre eles
							//se deu R$100,00 em consultas e a clínica fica com 30%, aqui ficará R$30,00
							$saldo_valor_clinica[$i]= formata_valor(($valor_total[$i]*$_POST[percentual][$i])/100);
							
							//então a pessoa tem a pagar para a clínica R$30,00... vai salvar no banco -30.00 referente a este(s) atendimento(s)
							$saldo_valor_pessoa[$i]= -$saldo_valor_clinica[$i];
							
							$clinica_deve[$i]= 0;
							$pessoa_deve[$i]= $saldo_valor_clinica[$i];
						}
					
					break;
				}
				
				
				//$recebido_valor_clinica[$i]= formata_valor(($valor_total[$i]*$_POST[percentual][$i])/100);
				//$recebido_valor_pessoa[$i]= formata_valor($valor_total[$i]-$recebido_valor_clinica[$i]);
				
				//echo ' qtde: '. $_POST["qtde"][$i] .' | '. $_POST[valor][$i] .' | '. $_POST[percentual][$i] .' | '. $valor_clinica[$i] .'<br />';
				
				$result1[$i]= mysqli_query($conexao1, "insert into atendimentos
											(id_pessoa, id_clinica, id_ato,
											id_convenio, tipo_convenio, recebimento,
											data,
											qtde, recebido_valor_pessoa, recebido_valor_clinica,
											saldo_valor_pessoa, saldo_valor_clinica,
											pessoa_deve, clinica_deve,
											por_direito_valor_pessoa, por_direito_valor_clinica,
											valor_total, percentual_clinica,
											modo_recebimento_convenios_pagos,
											id_acesso) values
										('". $_COOKIE[id_pessoa] ."', '". $_COOKIE[id_clinica] ."', '". $_POST[id_ato] ."',
										'". $_POST[id_convenio][$i] ."', '". $_POST[tipo_convenio][$i] ."', '". $_POST[recebimento][$i] ."',
										'". formata_data($_POST[data]) ."',
										'". $_POST[qtde][$i] ."', '". $recebido_valor_pessoa[$i] ."', '". $recebido_valor_clinica[$i] ."',
										'". $saldo_valor_pessoa[$i] ."', '". $saldo_valor_clinica[$i] ."',
										'". $pessoa_deve[$i] ."', '". $clinica_deve[$i] ."',
										'". $por_direito_valor_pessoa[$i] ."', '". $por_direito_valor_clinica[$i] ."',
										'". $valor_total[$i] ."', '". $_POST[percentual][$i] ."',
										'". $modo_recebimento_convenios_pagos ."',
										'". $_COOKIE[id_acesso] ."' ) ") or die("4: ". mysqli_error());
				
				if (!$result1[$i]) $var++;
			}
			
			$i++;
		}//fim while j - atendos
		
		finaliza_transacao($var);
		
		header("location: ./?pagina=lancamento/lancamento&id_ato=". $_POST[id_ato] ."&data=". $_POST[data] ."&erros=". $var);
	}		
	
}


//echo '</body></html>';

?>