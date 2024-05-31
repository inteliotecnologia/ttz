<?
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

//unset($_COOKIE['id_clinica']);
//setcookie('id_clinica', null, -1, '/');

require_once("includes/_core/protecao.php");

if (!isset($_GET["pagina"])) $pagina= "gome";
else $pagina= $_GET["pagina"];

session_start();

if (isset($_GET["redireciona"]))
	echo
	"
	<script language='javascript' type='text/javascript'>
		window.top.location.href='./?pagina=lancamento/lancamento';
	</script>
	";
	

?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	
		<title><?= NOME .' &bull; '. SLOGAN; ?></title>
		
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

		<? /*
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="mobile-web-app-capable" content="yes">
		
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		*/ ?>
		
		<!-- iPhone ICON -->
        <link href="images/60.png" sizes="60x60" rel="apple-touch-icon">
        <!-- iPad ICON-->
        <link href="images/76.png" sizes="76x76" rel="apple-touch-icon">
        <!-- iPhone (Retina) ICON-->
        <link href="images/120.png" sizes="120x120" rel="apple-touch-icon">
        <!-- iPad (Retina) ICON-->
        <link href="images/152.png" sizes="152x152" rel="apple-touch-icon">
		
		<link rel="apple-touch-startup-image" href="images/splash.png">
		
		<? /*

        <!-- iPhone SPLASHSCREEN-->
        <link href="images/start_320x460.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
        <!-- iPhone (Retina) SPLASHSCREEN-->
        <link href="images/start_640x920.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
        <!-- iPad (portrait) SPLASHSCREEN-->
        <link href="images/start_768x1004.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
        <!-- iPad (landscape) SPLASHSCREEN-->
        <link href="images/start_1024x748.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
        <!-- iPad (Retina, portrait) SPLASHSCREEN-->
        <link href="images/start_1536x2008.png" media="(device-width: 1536px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
        <!-- iPad (Retina, landscape) SPLASHSCREEN-->
        <link href="images/start_2048x1496.png" media="(device-width: 1536px)  and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
		*/ ?>
		
		<?
		if (($_COOKIE[tema]!='') && ($_COOKIE[tema]!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE[tema]);
		elseif (($_COOKIE[tema]!='') && ($_COOKIE[tema]!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE[tema]);
		$tema_css= '_flatly_original';
		?>
		<link href="includes/bootstrap/css/bootstrap<?=$tema_css;?>.css" rel="stylesheet" />
		<link href="includes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="includes/bootstrap/css/bootstrap-social.css" />
		
		<link href="style.css?v=25" rel="stylesheet" />
		<link href="style_tablet.css?v=25" rel="stylesheet" />
		<link href="style_mobile.css?v=25" rel="stylesheet" />
		
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="includes/bootstrap/js/html5shiv.js"></script>
		<![endif]-->
		
		<link rel="shortcut icon" href="images/ico3.png" />
		
		<script language="javascript" type="text/javascript" src="includes/js/jquery-1.10.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/jquery.inputmask.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.file-input.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/bloodhound.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/handlebars-v1.3.0.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/geral.js"></script>
		
		<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
mixpanel.init("<?=MIXPANEL_TOKEN;?>");</script><!-- end Mixpanel -->
		
		
		
		<?
		if ($_SESSION[mixpanel]!='') {
		?>		
		<script type="text/javascript">
			
			<?
			echo $_SESSION['mixpanel'];
			?>
			
		</script>
		<?
		}
		
		$_SESSION['mixpanel']='';	
		?>
		
		
		<script>
			<? if ($_GET[erro]=='') { ?>
			mixpanel.track("Acessou a página de login");
			<?
			}
			else {
				switch($_GET[erro]) {	
				
				case 't':
			?>
			mixpanel.track("Fez logout");
			<?
				break;
				case 'l':
			?>
			mixpanel.track("Errou a senha");
			<?
				break;
				case 'm':
			?>
			mixpanel.track("Usuário inativado");
			<?
				break;
				case 'c':
			?>
			mixpanel.track("Cancelou a conta");
			<?
				break;
				}
			}
			?>
		</script>
		
	</head>
	<body class="pg_<?= str_replace("/", "-", $pagina); ?>">
		
		<? /*
		<div class="row">
		    <div class="span5 well">
		        <legend>Sign in to WebApp</legend>
		        <form method="POST" action="" accept-charset="UTF-8">
		            <div class="alert alert-error">
		                <a class="close" data-dismiss="alert" href="#">x</a>Incorrect Username or Password!
		            </div>      
		            <input class="span3" placeholder="Username" type="text" name="username">
		            <input class="span3" placeholder="Password" type="password" name="password"> 
		            <label class="checkbox">
		                <input type="checkbox" name="remember" value="1"> Remember Me
		            </label>
		            <button class="btn-info btn" type="submit">Login</button>      
		        </form>
		    </div>
		</div>
		*/ ?>
		
		<div class="container">
			<div class="row-fluid">
				
				<div class="caixa_login">
					<div class="span12">
						<div class="span5">
							<h2 class="tit_login">
							<img class="logod" src="images/cao4.png" width="40" />
							<?= NOME; ?></h2>
							<p class="p_login"><?= SLOGAN_QUEBRA; ?></p>
							
							<p class="p_ir_site"><a href="https://ttz.med.br" target="_blank">Ir ao site &raquo;</a></p>
							<? /*<img class="logo" width="300" src="images/logo.png" alt="" />*/ ?>
						</div>
						
						<div class="span7">
							
							<? if ($_GET[erro]!="o") { ?>
							<div class="form-signin">
							  <form method="post" action="<?=AJAX_FORM;?>formLogin&pagina=login&pre=index2.php">
							    <h3>Faça o login:</h3>
							    
							    <input id="email" name="email" type="text" class="input-block-level" value="<?=$_COOKIE["email"];?>" placeholder="E-mail ou Login" required="required" />
							    <input id="senha" name="senha" type="password" class="input-block-level" placeholder="Senha" required="required" />
							    
							    <? /*<label class="checkbox">
							      <input type="checkbox" value="remember-me"> Remember me
							    </label>*/ ?>
							    
							    <div class="row-fluid">
							    	<div class="span6">
							    		<button class="btn btn-primary btn-large" type="submit" data-loading-text="Entrando...">Entrar</button>
							    	</div>
							    	
							    	<div class="span6 span_esqueci">
							    		<a href="index2.php?pagina=esqueci">Esqueci a senha</a> <br />
							    		
							    		<a href="index2.php?pagina=cadastro">Experimente grátis</a>
							    	</div>
							    </div>
							    
							  </form>
							  
							</div>
							
							
							<?
							if ( ($_GET["erro"]!="t") && ($_GET["erro"]!="o") &&  ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) ) {
								if ( ($_GET[erro]=='t') || ($_GET[erro]=='h') || ($_GET[erro]=='c') ) $dv_cl='alert-warning';
								elseif (($_GET[erro]=='i') || ($_GET[erro]=='j') || ($_GET[erro]=='l') ) $dv_cl= 'alert-error';
								else $dv_cl='alert-success';
							?>
							<div class="container caixa_login_erro">
								<div class="alert xesconde <?=$dv_cl;?>">
									<a class="close" data-dismiss="alert" href="#">&times;</a>
									
									<?
						            if ($_GET["erro"]=='m') echo "<h5 class='alert-heading'><a style='color:#fff;text-decoration:underline;' href='mailto:contato@ttz.med.br'>Entre em contato conosco</a> para continuar.</h5>";
						            //if ($_GET["erro"]=='o') echo "<h5 class='alert-heading'>Cadastro realizado.<br/> Você já pode acessar.</h5>";
						            if ($_GET["erro"]=='j') echo "<h5 class='alert-heading'>Seu acesso está inativo</h5>";
									if ($_GET["erro"]=='l') echo "<h5 class='alert-heading'>E-mail e/ou senha inválidos!</h5>";
									if ($_GET["erro"]=='t') echo "<h5 class='alert-heading'>Você saiu do sistema.</h5>";
									if ($_GET["erro"]=='h') echo "<h5 class='alert-heading'>Identificação inválida, refaça o login.</h5>";
									if ($_GET["erro"]=='n') echo "<h5 class='alert-heading'>Faça o login para continuar.</h5>";
									if ($_GET["erro"]=='a') echo "<h5 class='alert-heading'>Um e-mail foi enviado com instruções para alterar a sua senha.</h5>";
									if ($_GET["erro"]=='b') echo "<h5 class='alert-heading'>A senha foi alterada.</h5>";
									if ($_GET["erro"]=='c') echo "<h5 class='alert-heading'>Sua conta foi cancelada. <br/>Obrigado por nos prestigiar :-)</h5>";
									
									if ($_GET[erros]==='0') echo "<h5 class='alert-heading'>Solicitação concluída com sucesso.</h5>";
									elseif ($_GET[erros]>0) echo "<h5 class='alert-heading'>Não foi possível cadastrar. Por favor, tente novamente.</h5>";
									?>
								</div>
							</div>
							<br class="clearfix" />
							<?
							}
							
							if ($_GET[erro]=='t') {
								?>
								
								<?
							}	
							?>
							
							
							<? //if ($_SERVER["REMOTE_ADDR"]=="177.96.33.225") { ?>
							
							
							<div class="">
								ou
							</div>
							
							<div class="row-fluid">
								<div class="span8" >
											
									<br/>
		
									<a href="<?=SISTEMA_URL;?>/index2.php?pagina=social&provider=Google" class="btn btn-lg btn-block btn-social btn-google-plus">
										<i class="icon icon-google-plus"></i> Entrar via Google
									</a>
									
									<a href="<?=SISTEMA_URL;?>/index2.php?pagina=social&provider=Facebook" class="btn btn-lg btn-block btn-social btn-facebook">
										<i class="icon icon-facebook"></i> Entrar via Facebook
									</a>
									
									  
								</div>
							</div>
							
							<? //} ?>
							
							
							
							
							
							<? }  else { ?>
							
							<h3>Tudo certo, <?=primeira_palavra($_GET['nome']);?>.</h3>
							
							<p>
							Um e-mail foi enviado ao endereço especificado. Acesse para confirmar o seu cadastro e continuar.
							</p>
							<br/>
							
							<p>
								<a class="btn btn-primary btn-large" href="./index2.php?pagina=login&email=<?=$_GET['email'];?>">Acessar</a>
							</p>
							
							<br />
							
							<? } ?>
							
						</div>
					</div>
				</div>
				
			</div>
			
			<script>
				
				$(document).ready(function() {
				<? if ($_COOKIE["email"]!='') {  ?>
				$("#senha").focus();
				<? } else { ?>
				$("#email").focus();
				<? } ?>
				});
				
			</script>
			
			
	    </div> <!-- /container -->
		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-46442942-1', 'ttz.med.br');
		  ga('send', 'pageview');
		
		</script>
		
	</body>
</html>