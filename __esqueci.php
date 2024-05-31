<?
require_once("includes/_core/protecao.php");

if (!isset($_GET["pagina"])) $pagina= "home";
else $pagina= $_GET["pagina"];

session_start();

if (isset($_GET["redireciona"]))
	echo
	"
	<script language='javascript' type='text/javascript'>
		window.top.location.href='./index2.php?pagina=login';
	</script>
	";
	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	
		<title><?= NOME .' '. SLOGAN; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		
		<meta name="mobile-web-app-capable" content="yes">
		
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		
	    <!-- iPhone ICON -->
        <link href="images/60.png" sizes="60x60" rel="apple-touch-icon">
        <!-- iPad ICON-->
        <link href="images/76.png" sizes="76x76" rel="apple-touch-icon">
        <!-- iPhone (Retina) ICON-->
        <link href="images/120.png" sizes="120x120" rel="apple-touch-icon">
        <!-- iPad (Retina) ICON-->
        <link href="images/152.png" sizes="152x152" rel="apple-touch-icon">
		
		<link rel="apple-touch-startup-image" href="images/splash.png">
		
		<?
		/*if (($_SESSION['tema']!='') && ($_SESSION['tema']!='Normal'))
			$tema_css= '_'. strtolower($_SESSION['tema']);
		elseif (($_COOKIE['tema']!='') && ($_COOKIE['tema']!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE['tema']);
		*/
		
		$tema_css='_flatly_original';
		?>
		<link href="includes/bootstrap/css/bootstrap<?=$tema_css;?>.css" rel="stylesheet" />
		<link href="includes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		
		<link href="style.css?v=25" rel="stylesheet" />
		<link href="style_tablet.css?s=22" rel="stylesheet" />
		<link href="style_mobile.css?s=22" rel="stylesheet" />
		
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="includes/bootstrap/js/html5shiv.js"></script>
		<![endif]-->
		
		<link rel="shortcut icon" href="images/ico3.png" />
		
		<script language="javascript" type="text/javascript" src="includes/js/jquery-1.10.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.file-input.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/bloodhound.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/handlebars-v1.3.0.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/geral.js"></script>
		
		<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
mixpanel.init("<?=MIXPANEL_TOKEN;?>");</script><!-- end Mixpanel -->

		<script>
			mixpanel.track("Acessou o esqueci a senha");
		</script>
		
	</head>
	<body class="pg_login">
		
		<div class="container">
			<div class="row-fluid">
				
				<div class="caixa_login">
					<div class="span12">
						<div class="span5">
							<h2 class="tit_login"><img class="logod" src="images/cao4.png" width="40" /> <?= NOME; ?></h2>
							<p class="p_login"><?= SLOGAN_QUEBRA; ?></p>
							<p class="p_ir_site"><a href="http://ttz.med.br" target="_blank">Ir ao site &raquo;</a></p>
						</div>
						
						<div class="span7">
							<? if ($_COOKIE[id_usuario]=="") { ?>
							<div class="form-signin">
								<h3>Recupere sua senha:</h3>
								
							  <form method="post" action="<?=AJAX_FORM;?>formEsqueci&pagina=esqueci&pre=index2.php">
							    
							    <input type="hidden" name="redirecionar" value="<?=$_GET['redirecionar'];?>" />
							    
							    <input name="email" type="email" class="input-block-level" value="<?=$_COOKIE['email'];?>" placeholder="Endereço de e-mail" required="required" />
							    
							    <div class="row-fluid">
							    	<div class="span6">
							    		<button class="btn btn-primary" type="submit" data-loading-text="Aguarde">Recuperar</button>
							    	</div>
							    	<div class="span6 span_esqueci">
							    		<a href="index2.php?pagina=login">&laquo; Voltar ao Login</a>
							    	</div>
							    </div>
							    
							  </form>
							  
							</div>
							<? }  else { ?>
							<h3>Já identificado!</h3>
							<br />
							<p>Você já está logado como <strong><?=$_SESSION['nome'];?></strong>. ;)</p>
							<br />
							
							<a class="btn btn-large btn-primary" href="./?pagina=lancamento/lancamento">Redirecionando...</a>
							
							<script>
								window.top.location.href='./?pagina=lancamento/lancamento';
							</script>
							<? } ?>
							
							<?
							if ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) {
								if ($_GET['erro']=='t') $dv_cl='alert-warning';
								elseif (($_GET['erro']=='i') || ($_GET['erro']=='j') || ($_GET['erro']=='l') || ($_GET['erro']=='s') ) $dv_cl= 'alert-error';
								else $dv_cl='alert-success';
							?>
							<div class="container">
								<div class="alert esconde <?=$dv_cl;?>">
									<a class="close" data-dismiss="alert" href="#">&times;</a>
									
									<?
						            if ($_GET["erro"]=='j') echo "<h5 class='alert-heading'>Seu acesso está inativo</h5>";
									if ($_GET["erro"]=='l') echo "<h5 class='alert-heading'>E-mail não encontrado no sistema.</h5>";
									if ($_GET["erro"]=='t') echo "<h5 class='alert-heading'>Você saiu do sistema.</h5>";
									if ($_GET["erro"]=='a') echo "<h5 class='alert-heading'>Não foi possível enviar.</h5>";
									
									if ($_GET[erros]==='0') echo "<h5 class='alert-heading'>Solicitação concluída com sucesso.</h5>";
									elseif ($_GET[erros]>0) echo "<h5 class='alert-heading'>Não foi possível enviar. Por favor, tente novamente.</h5>";
									?>
								</div>
							</div>

							<? } ?>
						</div>
					</div>
				</div>
			</div>
	    </div> <!-- /container -->
		
		<script type="text/javascript">
			$("input[type=email]").focus();
		</script>
		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', '<?=$_SESSION["sess_analytics_id"];?>', 'otimize.in');
		  ga('send', 'pageview');
		
		</script>
		
	</body>
</html>

<?php
/*$_SESSION["sess_cliente_auth"]= "";
$_SESSION["sess_cliente_id"]= "";
$_SESSION["sess_cliente_sigla"]= "";
$_SESSION["sess_cliente_nome"]= "";
$_SESSION["sess_cliente_desde"]= "";
$_SESSION["sess_url_atalho"]= "";
$_SESSION["sess_analytics_id"]= "";
*/
?>