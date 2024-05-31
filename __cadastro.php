<?
require_once("includes/_core/protecao.php");

if (!isset($_GET["pagina"])) $pagina= "home";
else $pagina= $_GET["pagina"];

if (isset($_GET["redireciona"]))
	echo
	"
	<script language='javascript' type='text/javascript'>
		window.top.location.href='./?pagina=lancamento/lancamento';
	</script>
	";

	logs(0, 0, 0, 1, 0, 'cadastro', 'Abriu tela de cadastro', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
?>
<!DOCTYPE html>
<html style="display:table;" lang="pt-br">
	<head>
	
		<title><?= NOME .' &bull; '. SLOGAN; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		
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
		<link href="style_tablet.css?s=22" rel="stylesheet" />
		<link href="style_mobile.css?s=22" rel="stylesheet" />
		
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
	
		<? if ($_GET[email]!='') { ?>
		
		<? /*
		<!-- Facebook Conversion Code for Novos cadastros -->
		<script>(function() {
		var _fbq = window._fbq || (window._fbq = []);
		if (!_fbq.loaded) {
		var fbds = document.createElement('script');
		fbds.async = true;
		fbds.src = '//connect.facebook.net/en_US/fbds.js';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(fbds, s);
		_fbq.loaded = true;
		}
		})();
		window._fbq = window._fbq || [];
		window._fbq.push(['track', '6017728322588', {'value':'0.01','currency':'BRL'}]);
		</script>
		<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6017728322588&amp;cd[value]=0.01&amp;cd[currency]=BRL&amp;noscript=1" /></noscript>
		*/ ?>
		
		<script type="text/javascript">
			mixpanel.track("Pré-cadastro", {
				"Nome": "<?=$_GET[nome];?>",
				"E-mail": "<?=$_GET[email];?>"
			});
		</script>
		<? } else { ?>
		<script type="text/javascript">
			mixpanel.track("Acessa página de cadastro");
		</script>
		<? } ?>
		
	</head>
	<body class="pg_<?= str_replace("/", "-", $pagina); ?>">
		
		<div class="container">
			
			<div class="caixa_cadastro">
				
				<br/>
				
				<div class="row-fluid">
					<div class="span5">
						
						
						<div class="row-fluid">
				
							<div class="span12">
								<div class="row-fluid">
						
									<div class="span4">
									
										<h2 class="">
											<img class="logod" src="images/cao4.png" width="40" />
											<?= NOME; ?></h2>
									</div>
									<div class="span8">
										<? /*<p style="margin-top:10px;" class="p_login"><small>Aplicativo para conferência pessoal de consultas e procedimentos médicos.</small><br/>
										<small><em>WEB, Tablet, Smartphone</em></small></p>*/ ?>
									</div>
								</div>
								
								<br>
							</div>
						</div>
						<br/>
						
						<? /*
						<p>Ao criar uma conta, você receberá <br>um material <u>grátis</u>
						de "Simplificando o entendimento de suas receitas e controlando".</p>
						<br>*/ ?>
						
						<h4 class="text-warning1"><i class="icon-lock"></i> &nbsp;Inteligência e <span class="">Segurança</span> </h4>
						<p>Organize sua vida financeira, identifique inconsistências em seus pagamentos. <br>Teste por um período e comprove.</p>
						<br>
						
						<? /*
						<h4 class="text-warning1"><i class="icon-phone"></i> &nbsp;Consultoria por telefone</h4>
						<p>Vire um Guru e domine suas contas <br>em um treinamento inicial rápido.</p>
						<br/>
						*/ ?>
						
						<h4 class="text-warning1"><i class="icon-phone"></i> &nbsp;Consultoria avançada</h4>
						<p>Caso necessário, oferecemos um serviço personalizado de análise de sua logística de recebimentos
						e montamos a ferramenta para você somente lançar e conferir.<br/> <em>Valor de Setup: R$320,00.</em></p>
						<br/>
						
						<h3 class="text-warning1"><i class="icon-money"></i> &nbsp;Acesso grátis</h3>
						<p>Até o primeiro repasse. Você só começa <br/>
						a pagar se quiser continuar usando.</p>
						<p><em>Não solicitamos dados de Cartão de Crédito.</em></p>
						<Br/>
						
						<? /*<a class="menor" href="https://ttz.med.br" target="_blank">Veja site completo e blog &raquo;</a>*/ ?>
						    
						<? /*
						<p class="muted"><small><a target="_blank" href="<?=SITE_URL;?>">Ir ao site &raquo;</a></small></p>*/ ?>
					</div>
					
					<div class="span6 offset1">
						
						<div class="row-fluid">
							<div class="span12 text-center">
								<h3>Médico, comece hoje mesmo a organizar seus recebimentos.</h3>
								<br/>
							</div>
						</div>
						
						<? //if ($_SERVER["REMOTE_ADDR"]=="177.96.33.225") { ?>
						<div class="row-fluid">
							<div class="span8 offset2 text-center" >
										
								<br/>
	
								<a href="<?=SISTEMA_URL;?>/index2.php?pagina=social&provider=Google" class="btn btn-lg btn-block btn-social btn-google-plus">
									<i class="icon icon-google-plus"></i> Entrar via Google
								</a>
								
								<a href="<?=SISTEMA_URL;?>/index2.php?pagina=social&provider=Facebook" class="btn btn-lg btn-block btn-social btn-facebook">
									<i class="icon icon-facebook"></i> Entrar via Facebook
								</a>
								
							</div>
						</div>
						<Br class="clearfix"/>
						
						<div class="text-center">
							ou
						</div>
						<? //} ?>
						
						<? //if ($_COOKIE[id_usuario]=="") { ?>
						<div class="form-signin" style="z-index:1;">
							
							
							
						  <form method="post" action="<?=AJAX_FORM;?>formCadastro&pagina=cadastro&pre=index2.php">
						    
						    <div class="row-fluid">
						    	<div class="span12 text-center">
						    		<? /*<em>Informe seus dados para começar com nossa ferramenta:</em>*/ ?>
						    		
						    	</div>
						    </div>
						    <div class="row-fluid">
						    	<div class="span6">
						    	
								    <? /*if ($_GET[email]!='') { ?>
								    <h5 style="margin-bottom:18px;"><?=$_GET[nome];?></h5>
								    <input type="hidden" name="nome" id="nome" value="<?= $_GET[nome]; ?>" required="required" />
								    <? /*<small><?= $_GET[email]; ?></small> <br/><br/>*/ ?>
								    <? //} else { ?>
								    <label class="lca">Nome completo<span class="text-error">*</span>:</label>
								    <input class="input-block-level"  type="text" name="nome" id="nome" value="<?= $_GET[nome]; ?>" placeholder="Seu nome" required="required" />
								    <? //} ?>
						    	</div>
						    	<div class="span6">
								    <?
								    if ($_GET[email]!='') {
										$result_pre= mysqli_query($conexao1, "insert into usuarios_pre
																	(email, data_cadastro, hora_cadastro, user_agent, ip)
																	values
																	('". $_GET[email] ."', '". date('Y-m-d') ."', '". date('H:i:s') ."', '". $_SERVER[HTTP_USER_AGENT] ."', '". $_SERVER[REMOTE_ADDR] ."' )
																	");    
								    /*
								    ?>
								    <h5 style="margin-bottom:18px;"><?=$_GET[email];?></h5>
								    <? } else { ?>
								    
								    <?*/ } ?>
								    
								    <? /*if ($_GET[email]!='') { ?>
								    <input type="hidden" name="email" id="email" value="<?= $_GET[email]; ?>" required="required" />
								    <? /*<small><?= $_GET[email]; ?></small> <br/><br/>*/ ?>
								    <? //} else { ?>
								    <label>E-mail<span class="text-error">*</span>:</label>
								    <input class="input-block-level"  type="email" name="email" id="email" value="<?= $_GET[email]; ?>" placeholder="Seu e-mail" required="required" />
								    <? //} ?>
						    	</div>
						    </div>
						    
						    
						    
						    <? /*<input class="input-block-level"  type="text" name="registro" id="registro" value="<?= $rs->registro; ?>" placeholder="CRM, CRO, CRF 0000/UF" />*/ ?>
						    
						    <div class="row-fluid">
						    	<div class="span6">
						    		<label>Telefone:</label>
									<input class="input-block-level"  type="text" name="telefone" id="telefone" value="<?= $rs->telefone; ?>" placeholder="Seu telefone" />
						    	</div>
						    	<div class="span6">
						    		<label>Melhor horário p/ contato:</label>
						    		<select  style="height:40px;" name="melhor_horario" id="melhor_horario" class="input-block-level">
						    			<option value="-">-</option>
						    			<option value="Agora">Agora</option>
						    			<option value="">-</option>
						    			<option value="Manhã">Manhã</option>
						    			<option value="Tarde">Tarde</option>
						    			<option value="Noite">Noite</option>
						    			<option value="Madrugada">Madrugada</option>
						    			<option value="">-</option>
						    			<option value="Somente WhatsApp">Somente WhatsApp</option>
						    		</select>
						    	</div>
						    </div>
						    
						    <div class="row-fluid">
						    	<div class="span6">
						    	
								    <label>Especialidade:</label>
									<select style="height:40px;" class="input-block-level"  id="id_especialidade" name="id_especialidade">
										<option value="">-</option>
										<?
										$result_esp= mysqli_query($conexao1, "select * from  especialidades
																order by especialidade asc
																") or die(mysqli_error());
										while ($rs_esp= mysqli_fetch_object($result_esp)) {
										?>
										<option <? if ($rs->id_especialidade==$rs_esp->id_especialidade) echo 'selected="selected"'; ?> value="<?=$rs_esp->id_especialidade;?>"><?=$rs_esp->especialidade;?></option>
										<? } ?>
									</select>
						    	</div>
						    	<div class="span6">
						    		<label>Registro CRM / UF:</label>
									<input class="input-block-level" type="text" name="registro" id="registro" placeholder="9999/UF" />
						    	</div>
						    </div>
						    
							
							<label>Cupom de desconto:</label>
						    <input class="input-block-level"  type="text" name="cupom" id="cupom" value="<?= $rs->cupom; ?>" placeholder="Opcional" />
						    <br/>
						    
						    <? /*<div class="well well-small" style="margin-bottom:15px !important;">*/ ?>
						    
			            	<? /*
			            	<label>Escolha uma senha:</label>
			            	<input class="input-block-level span4" type="password" name="senha" id="senha" placeholder="" required="required" />
								*/ ?>
								
				            	<? /*<input style="width:93%;margin-bottom:0;" type="password" name="senha2" id="senha2" placeholder="Confirmação de senha" required="required" />*/ ?>
			            	<? /*</div>*/ ?>
						    
						    <? /*<div style="height:150px;width:100%">
							    <?php
						          $publickey= "6Lct2_ESAAAAAGiojmChxk2_UJVnSy-G0t2mT34G"; // SUA CHAVE PRIVATE KEY
						          echo recaptcha_get_html($publickey);        // MOSTRA O RECAPTCHA NA PÁGINA
						        ?>
						        &nbsp;
						    </div>*/ ?>
						    
						    <div class="row-fluid">
						    	<div class="span6">
						    		<button class="btn btn-success btn-large cadastrar" type="submit" data-loading-text="Continuar &raquo;">Continuar &raquo;</button>
						    	</div>
						    	
						    	<? /*
						    	<div class="span6 span_esqueci">
						    		<a href="index2.php?pagina=login">&laquo; Voltar ao login</a>
						    	</div>*/ ?>
						    </div>
						    
						  </form>
						  
						</div>
						<? /*}  else { ?>
						<br />
						<p>Você já está logado como <strong><?=$_COOKIE['nome'];?></strong>. ;)</p>
						<br />
						
						<a class="btn btn-large btn-primary" href="./?pagina=lancamento/lancamento">Redirecionando...</a>
						
						<script>
							window.top.location.href='./?pagina=lancamento/lancamento';
						</script>
						<? } */ ?>
						
						<?
						if ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) {
							if ( ($_GET[erro]=='t') || ($_GET[erro]=='h') ) $dv_cl='alert-warning';
							elseif (($_GET[erro]=='i') || ($_GET[erro]=='j') || ($_GET[erro]=='l') ) $dv_cl= 'alert-error';
							else $dv_cl='alert-success';
						?>
						<div class="container caixa_login_erro">
							<div class="alert xesconde <?=$dv_cl;?>">
								<a class="close" data-dismiss="alert" href="#">&times;</a>
								
								<?
					            if ($_GET["erro"]=='j') echo "<h5 class='alert-heading'>Seu acesso está inativo</h5>";
								if ($_GET["erro"]=='l') echo "<h5 class='alert-heading'>E-mail e/ou senha inválidos!</h5>";
								if ($_GET["erro"]=='t') echo "<h5 class='alert-heading'>Você saiu do sistema.</h5>";
								if ($_GET["erro"]=='h') echo "<h5 class='alert-heading'>Identificação inválida, refaça o login.</h5>";
								if ($_GET["erro"]=='n') echo "<h5 class='alert-heading'>Faça o login para continuar.</h5>";
								if ($_GET["erro"]=='a') echo "<h5 class='alert-heading'>Um e-mail foi enviado com instruções para alterar a sua senha.</h5>";
								if ($_GET["erro"]=='b') echo "<h5 class='alert-heading'>A senha foi alterada.</h5>";
								
								if ($_GET[erros]==='0') echo "<h5 class='alert-heading'>Solicitação concluída com sucesso.</h5>";
								elseif ($_GET[erros]>0) echo "<h5 class='alert-heading'>Não foi possível cadastrar. Por favor, tente novamente.</h5>";
								?>
							</div>
						</div>
						<br class="clearfix" />
						<? } ?>
					</div>
				</div>
			</div>
				
			
			
			<script>
				
				$(document).ready(function() {
					$("#telefone").focus();
					$(".cadastrar").button('reset');
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