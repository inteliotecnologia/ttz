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
	
		<title><?= NOME .' &bull; '. SLOGAN; ?></title>
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
		
		$tema_css='_flatly';
		?>
		<link href="includes/bootstrap/css/bootstrap<?=$tema_css;?>.css" rel="stylesheet" />
		<link href="includes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
		
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
		
		<script language="javascript" type="text/javascript" src="includes/js/functions.js"></script>
	
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
							<? /*<img class="logo" width="300" src="images/logo.png" alt="" />*/ ?>
						</div>
						
						<div class="span7">
							<?
							$result_auth= mysqli_query($conexao1, " select * from usuarios
														where auth = '". $_GET[auth] ."'
														limit 1
														");
							$num_auth= mysqli_num_rows($result_auth);
							
							if ($num_auth==0) {
								?>
								<br /><br />
								<h3>Alteração de senha</h3>
								
								<p>Este link de recuperação de senha não é mais válido.</p>
								<br />
								
								<a href="index2.php?pagina=login&amp;s=<?=$_GET['s'];?>">Ir para o Login &raquo;</a>
								<?
							}
							else {
								$rs_auth= mysqli_fetch_object($result_auth);
								
							?>
							<div class="form-signin">
							  <form enctype="multipart/form-data" method="post" action="<?=AJAX_FORM;?>formRecupera&pagina=recupera&pre=index2.php">
							    
							    <input type="hidden" name="redirecionar" value="<?=$_GET['redirecionar'];?>" />
							    <input type="hidden" name="auth" value="<?=$rs_auth->auth;?>" />
							    <input type="hidden" name="id_usuario" value="<?=$rs_auth->id_usuario;?>" />
							    
							    <h5 style="margin-top:23px;margin-bottom:30px;">Faça uma nova senha:</h5>
							    
							    <strong><?=$rs_auth->nome ." (". $rs_auth->email .")";?></strong>
							    <br /><br />
							    
							    <input name="senha" id="senha" type="password" class="input-block-level" value="" placeholder="Nova senha" required="required" />
							    <input name="senha2" id="senha2" type="password" class="input-block-level" value="" placeholder="Confirmação de nova senha" required="required" />
							    
							    <div class="row-fluid">
							    	<div class="span6">
							    		<button class="btn btn-primary" type="submit" data-loading-text="Alterando...">Alterar</button>
							    	</div>
							    	<div class="span6 span_esqueci">
							    		<a href="index2.php?pagina=login&amp;s=<?=$_GET['s'];?>">&laquo; Ir para o Login</a>
							    	</div>
							    </div>
							    
							  </form>
							  
							</div>
							<? } //fim else ?>
							
							<?
							if ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) {
								if ($_GET['erro']=='t') $dv_cl='alert-warning';
								elseif (($_GET['erro']=='i') || ($_GET['erro']=='j') || ($_GET['erro']=='l') ) $dv_cl= 'alert-error';
								else $dv_cl='alert-success';
							?>
							<div class="container">
								<div class="alert esconde <?=$dv_cl;?>">
									<a class="close" data-dismiss="alert" href="#">&times;</a>
									
									<?
									if ($_GET["erro"]=='t') echo "<h4 class='alert-heading'>Não foi possível enviar o e-mail.</h4>";
									
									if ($_GET[erros]==='0') echo "<h4 class='alert-heading'>Solicitação concluída com sucesso.</h4>";
									elseif ($_GET[erros]>0) echo "<h4 class='alert-heading'>Não foi possível cadastrar. Por favor, tente novamente.</h4>";
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
			$("#senha").focus();
		</script>
		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', '<?=$_SESSION["sess_analytics_id"];?>', 'edoc.otimize.in');
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