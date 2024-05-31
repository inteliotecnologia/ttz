<?
session_start();

if (!isset($_GET["pagina"])) $pagina= "lancamento/lancamento";
else $pagina= $_GET["pagina"];

require_once("includes/_core/protecao.php");

session_start();

if (isset($_GET["redireciona"]))
	echo
	"
	<script language='javascript' type='text/javascript'>
		window.top.location.href='./?pagina=home';
	</script>
	";

if ($_COOKIE[id_pessoa]!='') {
	
	$result_pc= mysqli_query($conexao1, "select * from pessoas_clinicas
							where id_pessoa = '". $_COOKIE[id_pessoa] ."'
							and   status_pc = '1'
							") or die(mysqli_error());
	$num_pc= mysqli_num_rows($result_pc);

	if ($_GET[data]=='') {
		$data=date('d/m/Y');
		$data_inicio= '01/'. date('m/Y');
	}
	else {
		$data= $_GET[data];
		setcookie ("data_lancamento", $data, TEMPO_COOKIE, PATH, DOMINIO, false, true);
		
		$data_inicio= '01/'. substr($_GET[data], 3, 7);
	}
	
	if ($_SESSION["emula_id_usuario"]!='') {
		$IDENT_id_usuario= $_SESSION["emula_id_usuario"];
		$IDENT_id_pessoa= pega_usuario_dado($IDENT_id_usuario, "id_pessoa");
		$IDENT_nome= pega_usuario_dado($IDENT_id_usuario, "nome");
		
		
		$IDENT_id_clinica= pega_id_clinica_principal($IDENT_id_pessoa);
	}
	else {
		$IDENT_id_usuario= $_COOKIE["id_usuario"];
		$IDENT_id_pessoa= $_COOKIE["id_pessoa"];
		$IDENT_nome=$_COOKIE["nome"];
		$IDENT_id_clinica= $_COOKIE["id_clinica"];
	}
	
	$result_pessoa_clinica= mysqli_query($conexao1, "select * from pessoas_clinicas
												where  id_pessoa = '". $IDENT_id_pessoa ."'
												and   id_clinica = '". $IDENT_id_clinica ."'
												and   status_pc = '1' 
												") or die(mysqli_error());
	$rs_pessoa_clinica= mysqli_fetch_object($result_pessoa_clinica);
	
	$identifica_atendimentos= $rs_pessoa_clinica->identifica_atendimentos;
	$nome_clinica= pega_clinica($rs_pessoa_clinica->id_clinica);
	$plantonista= $rs_pessoa_clinica->plantonista;
	$convenio_proprio= $rs_pessoa_clinica->convenio_proprio;
	$modo_recebimento_convenios_pagos= $rs_pessoa_clinica->modo_recebimento_convenios_pagos;
	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	
		<title><?= NOME; ?> &bull; <?= SLOGAN; ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no;" />
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
		<meta name="mobile-web-app-capable" content="yes">
		
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-mobile-web-app-title" content="<?= NOME; ?>">
		
		
	    <!-- iPhone ICON -->
        <link href="images/60.png" sizes="60x60" rel="apple-touch-icon">
        <!-- iPad ICON-->
        <link href="images/76.png" sizes="76x76" rel="apple-touch-icon">
        <!-- iPhone (Retina) ICON-->
        <link href="images/120.png" sizes="120x120" rel="apple-touch-icon">
        <!-- iPad (Retina) ICON-->
        <link href="images/152.png" sizes="152x152" rel="apple-touch-icon">
        
        <!-- iOS 6 & 7 iPad (retina, portrait) -->
        <link href="images/apple-touch-startup-image-1536x2008.png"
              media="(device-width: 768px) and (device-height: 1024px)
                 and (orientation: portrait)
                 and (-webkit-device-pixel-ratio: 2)"
              rel="apple-touch-startup-image">
 
        <!-- iOS 6 & 7 iPad (retina, landscape) -->
        <link href="images/apple-touch-startup-image-1496x2048.png"
              media="(device-width: 768px) and (device-height: 1024px)
                 and (orientation: landscape)
                 and (-webkit-device-pixel-ratio: 2)"
              rel="apple-touch-startup-image">
 
        <!-- iOS 6 iPad (portrait) -->
        <link href="images/apple-touch-startup-image-768x1004.png"
              media="(device-width: 768px) and (device-height: 1024px)
                 and (orientation: portrait)
                 and (-webkit-device-pixel-ratio: 1)"
              rel="apple-touch-startup-image">
 
        <!-- iOS 6 iPad (landscape) -->
        <link href="images/apple-touch-startup-image-748x1024.png"
              media="(device-width: 768px) and (device-height: 1024px)
                 and (orientation: landscape)
                 and (-webkit-device-pixel-ratio: 1)"
              rel="apple-touch-startup-image">
 
        <!-- iOS 6 & 7 iPhone 5 -->
        <link href="images/apple-touch-startup-image-640x1096.png"
              media="(device-width: 320px) and (device-height: 568px)
                 and (-webkit-device-pixel-ratio: 2)"
              rel="apple-touch-startup-image">
 
        <!-- iOS 6 & 7 iPhone (retina) -->
        <link href="images/apple-touch-startup-image-640x920.png"
              media="(device-width: 320px) and (device-height: 480px)
                 and (-webkit-device-pixel-ratio: 2)"
              rel="apple-touch-startup-image">
 
        <!-- iOS 6 iPhone -->
        <link href="images/apple-touch-startup-image-320x460.png"
              media="(device-width: 320px) and (device-height: 480px)
                 and (-webkit-device-pixel-ratio: 1)"
              rel="apple-touch-startup-image">
              
		
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

		<?
		/*if (($_COOKIE[tema]!='') && ($_COOKIE[tema]!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE[tema]);
		elseif (($_COOKIE[tema]!='') && ($_COOKIE[tema]!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE[tema]);*/
			
			$tema_css='_flatly_original';
		?>
		
		<link media="screen,print" href="includes/bootstrap/css/bootstrap<?=$tema_css;?>.css" rel="stylesheet" />
		<link href="includes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
		
		<link href="style.css?v=25" rel="stylesheet" />
		<link href="style_tablet.css?v=25" rel="stylesheet" />
		<link href="style_mobile.css?v=25" rel="stylesheet" />
		
		<link media="print" href="style_print.css?v=24" rel="stylesheet" />
		
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="includes/bootstrap/js/html5shiv.js"></script>
		<![endif]-->
		
		<link rel="shortcut icon" href="images/ico4.png" />
		
		
		
		<script language="javascript" type="text/javascript" src="includes/js/jquery-1.10.1.min.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/jquery.inputmask.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.file-input.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/geral.js?s=2"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/typeahead.jquery.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/bloodhound.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/handlebars-v1.3.0.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/bootstrap-datepicker.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootbox.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/jquery-ui-1.10.3.custom.min.js"></script>
		
		<?
		if ($_COOKIE["cm"]!="1") {
		?>
		<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
mixpanel.init("<?=MIXPANEL_TOKEN;?>");</script><!-- end Mixpanel -->
		
		<?
		$data_hora_cadastro= pega_pessoa_dado($_COOKIE[id_pessoa], 'data_hora_cadastro');
		$perfil= pega_perfil($COOKIE["perfil"]);
		
		//se não for chave mestre...
		?>
		<script type="text/javascript">
			var locali;
			
			if (window.navigator.standalone)
				locali="Web-app";
			else
				locali="Navegador";
			
			
			mixpanel.people.set({
				"$name": "<?= ($_COOKIE["nome"]);?>",
				"$email": "<?= $_COOKIE["email"];?>",
				"$created": "<?= converte_data_completa_utc($data_hora_cadastro);?>",
				"Nome completo": "<?= ($_COOKIE["nome"]);?>",
				"Perfil": "<?=$perfil;?>",
				"ID Usuario": "<?=$_COOKIE["id_usuario"];?>",
				"ID Pessoa": "<?=$_COOKIE["id_pessoa"];?>",
				"Navegador": ""+locali+""
			});
			
			mixpanel.identify('<?=$_COOKIE[id_usuario];?>');
			
			mixpanel.register({
						        "Nome completo": "<?= ($_COOKIE["nome"]);?>",
						        "ID Usuario": "<?=$_COOKIE["id_usuario"];?>",
								"ID Pessoa": "<?=$_COOKIE["id_pessoa"];?>",
								"ID Clinica": "<?=$_COOKIE["id_clinica"];?>",
								"Nome Clinica": "<?=$nome_clinica;?>",
								"Plantonista": "<?=sim_nao_simples($plantonista);?>",
								"ID Plantonista": "<?=($_COOKIE["id_plantonista"]);?>",
								"Nome Plantonista": "<?=pega_pessoa($_COOKIE["id_plantonista"]);?>",
								"Versao do sistema": "<?= VERSAO; ?>",
								"Navegador": ""+locali+"",
								"Módulo": "<?=traduz_identifica_atendimentos($identifica_atendimentos);?>"
						    });
		</script>
		<? } ?>
		
		<? /* if ( (AMBIENTE==3) && ($_COOKIE["cm"]!="1") ) { ?>
		<script>
		  window.intercomSettings = {
		    
		    "user_id": "<?=$_COOKIE["id_usuario"];?>",
		    name: "<?= ($_COOKIE["nome"]);?>",
		    email: "<?= $_COOKIE["email"];?>",
		    created_at: <?php echo strtotime(converte_data_completa_utc($data_hora_cadastro)); ?>,
		    "Perfil": "<?=$perfil; ?>",
		    "user_hash": "<?php echo hash_hmac("sha256", $_COOKIE["id_usuario"], "hWbUDlLBHhE525ZN-T71qRzPBwhPRoCehDECavNm"); ?>",
		    
		    "Cupom": "<?=pega_usuario_dado($_COOKIE["id_usuario"], "cupom"); ?>",
		    
		    "ID Usuario": "<?=$_COOKIE["id_usuario"];?>",
			"ID Pessoa": "<?=$_COOKIE["id_pessoa"];?>",
		    "Navegador": ""+locali+"",
		    
		    "widget": {
		    	"activator": "#Intercom<? if ($device!="smartphone") { ?>DefaultWidget<? } ?>"
		    },
		    
		    app_id: "xoqab2s8"
		};
		</script>
		<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/xoqab2s8';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
		
		<?  } */ ?>
		
		<?
		if ($_COOKIE["cm"]!="1") {
		if ($_SESSION[mixpanel]!='') {
		?>		
		<script type="text/javascript">
			
			<?
			echo $_SESSION['mixpanel'];
			?>
			
		</script>
		<?
		}
		}
		
		$_SESSION['mixpanel']='';	
		?>
		
		<script>
			$(document).ready(function() {
				
				<? /*if ($_COOKIE["perfil"]=="3") { ?>
				pegaHorario();
				
				setInterval(function() {
		      		pegaHorario();
			      }, 30000);
			    
				<? }*/ ?>
			});
			
		</script>
	</head>
	<body class="dentro pg_<?= str_replace("/", "-", $pagina); ?>">
		
		<input type="hidden" name="id_modulo" id="id_modulo" value="<?=$identifica_atendimentos;?>" />
		
		<? /* Tela de edição de convênio que o médico atende */ ?>
		<div id="modal_clinica" class="modal hide fade" tabindex="-1" role="dialog">
				
			<form id="modal_clinica_form" action="<?=AJAX_FORM;?>novaClinica" method="post">
				
				<input type="hidden" name="id_clinica" id="nc_id_clinica" value="" />
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">x</button>
					<h5>Local de trabalho</h5>
				</div>
					
				<div class="modal-body">
					<div class="row-fluid">
						<div class="span12 span_typeahead" style="margin-top:5px;">
							<input type="text" name="clinica" id="nc_clinica" value="" class="input-block-level" placeholder="Nome do local de trabalho" required="required" />
						</div>
					</div>
					<br/>
					
					<ul class="nav nav-tabs" id="tab_opcoes1">
						<li class="active">
							<a href="#contador1">	
								Contador
							</a>
						</li>
						<li>
							<a href="#plantao1">	
								Plantão
							</a>
						</li>
					</ul>
					
					<div class="tab-content">
						<div class="tab-pane active" id="contador1">
							<div class="row-fluid">
								<div class="span4">
									<label><strong>Modo de utilização:</strong></label>
									<br class="clearfix" />
									
									<label class="radio menor">
										<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_1" value="1" <? if (($acao=='i') || ($rs_pessoa_clinica->identifica_atendimentos=='1')) echo 'checked';?> />
										Somente contador
									</label>
									
									<label class="radio menor">
										<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_2" value="2" <? if ($rs_pessoa_clinica->identifica_atendimentos=='2') echo 'checked';?> />
										Contador + Nome do paciente
									</label>
									
									<label class="radio menor">
										<input type="radio" name="identifica_atendimentos" id="identifica_atendimentos_3" value="3" <? if ($rs_pessoa_clinica->identifica_atendimentos=='3') echo 'checked';?> />
										Contador + Nome + Prontuário Online
									</label>
									
								</div>
								
								<div class="span4">	
									
									<label><strong>Recebe diariamente pagamentos neste local?</strong></label> 
									<br class="clearfix" />
									
									<label class="radio menor">
										<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_1" value="1" checked="checked" <? if (($acao=='i') || ($rs_pessoa_clinica->modo_recebimento_convenios_pagos=='1')) echo 'checked';?> />
										Sim, levo 100% do valor dos atendimentos
									</label>
									
									<label class="radio menor">
										<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_2" value="2" <? if ($rs_pessoa_clinica->modo_recebimento_convenios_pagos=='2') echo 'checked';?> />
										Sim, levo minha % já acertada
									</label>
									
									<label class="radio menor">
										<input type="radio" name="modo_recebimento_convenios_pagos" id="modo_recebimento_convenios_pagos_3" value="3" <? if ($rs_pessoa_clinica->modo_recebimento_convenios_pagos=='3') echo 'checked';?> />
										Não
									</label>
									<br />
									
								</div>
								
								<div class="span4">	
								
									<label><strong>Atende Convênio Próprio?</strong><br> <small class="muted">(Ex: Unimed)</small></label> 
									
									<label class="radio menor">
										<input type="radio" name="convenio_proprio" id="convenio_proprio_1" value="1"
										<? if (($acao=='i') || ($rs_pessoa_clinica->convenio_proprio=='1')) echo 'checked';?> />
										Sim
									</label>
									
									<label class="radio menor">
										<input type="radio" name="convenio_proprio" id="convenio_proprio_0" value="0"
										<? if ($rs_pessoa_clinica->convenio_proprio=='0') echo 'checked';?> />
										Não
									</label>
									
									<br class="clearfix" />
									
								</div>
								
							</div>
						</div>
					
						<div class="tab-pane" id="plantao1">
							
							<div class="row-fluid">
							
								<div class="span12">
								
									<label class="checkbox">
										<input type="checkbox" name="plantonista" id="plantonista_1" value="1"
										<? if ($rs_pessoa_clinica->plantonista=='1') echo 'checked';?> />
										
										Habilitar controle de horas.
									</label>
									
									<br />
								</div>
							</div>
								
						</div>
					</div>
				</div>
					
				<div class="modal-footer">
					<button type="button" class="btn cancelar" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary" data-loading-text="...">OK</button>
				</div>
			</form>
		</div>
		
		<div class="topo_impressao visible-print">
			<div class="pull-right"><em><?=URL;?></em></div>
			
			<img class="logod" src="images/cao1_invert.png" width="20" />
			<p class="logod_texto"><big><?=NOME;?></big><br/> <span class="muted"><?=SLOGAN;?></span></p>
			<br class="clearfix" />
			<hr />
		</div>
		
	    <div class="navbar hidden-print navbar-inverse" id="topo">
			<div class="navbar-inner">
				<div class="container">

				  	<a href="./?pagina=lancamento/lancamento&data=<?=$_COOKIE["data_lancamento"];?>#inicio" class="brand interno">
				  		<img src="images/cao3.png" width="24" alt="" />
				  		<span><?=NOME;?></span>
				  	</a>
					
				    <div class="">
				    	
				    	<ul class="nav">
							
							<? if ($_COOKIE["id_clinica"]!='') { ?>
							<li>
								<a class=" interno
								<?
								if ( 
									(strpos($pagina, "/lancamento")!==false)
									)
									echo "current";
								?>
								" id="link_registro" href="./?pagina=lancamento/lancamento&data=<?=$_COOKIE["data_lancamento"];?>#inicio"><i class="icon-user"></i> &nbsp;Lançar </a>
								
								<b class="current_caret
								<?
								if ( 
									(strpos($pagina, "/lancamento")!==false)
									)
									echo "current_caret_on";
								?>
								"></b>
							</li>
							
							<li>
								<a class="bootstro interno
								
								<?
								if ( 
									(strpos($pagina, "/relatorio")!==false)
									)
									echo "current";
								?>
								"
								
								data-bootstro-step="6"
							  	data-bootstro-placement="bottom"
							  	data-bootstro-width="450px"
							  	data-bootstro-content="<strong>Pronto</strong> <br/><br/> Você já pode conferir um extrato de seus rendimentos."
								
								href="./?pagina=lancamento/relatorio"><i class="icon-usd"></i> Relatório </a>
								
								<b class="current_caret
								<?
								if ( 
									(strpos($pagina, "/relatorio")!==false)
									)
									echo "current_caret_on";
								?>
								"></b>
							</li>
							<? } ?>
						</ul>
						
						<? if ($_COOKIE["id_pessoa"]!="") { ?>
						<div class="pull-right">
							<ul class="nav pull-right">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle text-right caret_menu" data-toggle="dropdown">
										
										<? /*if ($_COOKIE[foto]!='') { ?>
										<img style="margin-right:10px;" class="img-rounded fotinho" src="includes/timthumb/timthumb.php?src=<?= $_COOKIE[foto]; ?>&amp;w=20&amp;h=20&amp;zc=1&amp;q=95" border="0" alt="" />
										<? } */ ?>
										
										<span class="hidden-phone"><? if ($_COOKIE["cm"]=="1") { ?>  <i class="icon icon-key"></i> &nbsp; <? } ?> <?=primeira_palavra($_COOKIE[nome]);?> </span>
										
										<b class="caret"></b>
									</a>
								
									<ul class="dropdown-menu">
										
										<li class="hidden-desktop hidden-tablet">
											<a href="javascript:void(0);"><? if ($_COOKIE["cm"]=="1") { ?>  <i class="icon icon-key"></i> &nbsp; <? } ?> <?=primeira_palavra($_COOKIE[nome]);?></a>
										</li>
										<li class="hidden-desktop hidden-tablet divider"></li>
										
										<? if (pode("1", $_COOKIE["perfil"])) { ?>
										<li><a class="interno" href="./?pagina=acesso/usuarios"><i class="icon-lock "></i> Administração</a></li>
										<li class="divider"></li>
										<? } ?>
										<li><a class="interno" href="./?pagina=acesso/dados"><i class="icon-user"></i> Minha conta</a></li>
										
										<? /*if ( ($device=="smartphone") && (AMBIENTE=="3") && ($_COOKIE["cm"]!="1") ) { ?>
										<li><a id="Intercom" class="interno" href="mailto:pzthv8fq@incoming.intercom.io"><i class="icon-thumbs-up"></i> Dúvidas</a></li>
										<? }*/ ?>
										
										<? if ($pagina=="lancamento/lancamento") { ?>
										<li><a class="tour" href="javascript:void(0);"><i class="icon-signal "></i> &nbsp;Dicas</a></li>
										<? } ?>
										<? /*<li><a href="./?pagina=lancamento/instrucao"><i class="icon-user icon-play-circle"></i> Como utilizar?</a></li>
										<li><a
										
										href="mailto:contato@ttz.med.br?subject=Suporte"><i class="icon-question-sign "></i> Suporte</a></li>*/ ?>
										<li class="divider"></li>
										<li><a class="interno" href="./index2.php?pagina=logout"><i class="icon-off"></i> Sair</a></li>
									</ul>
								</li>
							</ul>
						</div>
						<? } ?>
				    </div>    
				</div>
			</div>
			
	    </div><!--/.navbar -->
		
		<script type="text/javascript">
			$(document).ready(function() {	
				
				$('.current_caret').each(function () {
			        var largura_pai= parseInt($(this).parent().width());
			        
			        var margem= ((largura_pai-51)/2);
			        
			        $(this).css('marginLeft', margem+'px');
			        
			    });
			    
			    $( ".nav-collapse ul.nav li a" ).hover(
				  function() {
				    $(this).parent().find(".current_caret").addClass("current_caret_hover");
				  }, function() {
				    $(this).parent().find(".current_caret").removeClass("current_caret_hover");
				  }
				);
				
				$('.current_caret_on').fadeIn('fast');
				
				setTimeout(function() { $('.container-relative, footer').fadeIn(400); }, 600);
			});
		</script>
		
		<div class="container container-relative">
			<?
			/*if ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) {
				if ($_GET[erros]==0) $dv_cl='alert-success';
				else $dv_cl= 'alert-error';
			?>
			<div class="alert <? if ($_GET[esconde]!='nao') { ?>esconde<? } ?> <?=$dv_cl;?>">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				
				<?
				if ($_GET[erros]==='0') echo "<h4 class='alert-heading'>Solicitação concluída com sucesso</h4>";
				elseif ($_GET[erros]>0) echo "<h4 class='alert-heading'>Não conseguimos salvar o que você fez :(</h4>";
				?>
			</div>
			<? } */ ?>
			
			<div class="row-fluid page_inner">
				<div class="span12">
					<?php
					$paginar= $pagina;
					if (strpos($paginar, "/")) {
						$parte_pagina= explode("/", $paginar);
						
						if (file_exists("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php"))
							include("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php");
						else include("404.php");
					}
					else {
						if (file_exists("__". $paginar .".php"))
							include("__". $paginar .".php");
						else include("404.php");
					}
					?>
				</div>
			</div>
			
			
			
		</div>
		
		<footer id="footer">
			
			
			<? /*if ( ($device!="smartphone") && (AMBIENTE=="3") && ($_COOKIE["cm"]!="1") ) { ?>
			<div class="feedback">
				<em class="muted">Dúvidas</em>
			</div>
			<? }*/ ?>
			
			<? if ( ($tipo_pagamento=='0') && (($pagina=='lancamento/lancamento') || ($pagina=='lancamento/relatorio')) ) { ?>
			<div class="tarjeta">
				<small>Em teste grátis até <?=desformata_data($data_ate);?>. &nbsp;<a href="./?pagina=acesso/faturamento">Assinar &raquo;</a></small>
			</div>
			<? } ?>
			
			<div class="container">
		    	<div class="row-fluid">
		    		<? if ($pagina!='lancamento/lancamento') { ?>
		    		<div class="span12">
		    		<? } else { ?>
		    		<div class="span4">
		    		
		    		</div>
		    		<div class="span8">
		    		<? } ?>
		    			<hr />
		    			<Br />
		    			<small>
		    			<p class="visible-print pull-right text-right muted" style="margin-bottom:85px;"><small>Todos os direitos reservados &bull; <?=date("Y");?>
						<br />Intelio Tecnologia
						</small> </p>
						
		    			<p class="muted credit"><strong><?= NOME; ?></strong><br/>
		    			<?= SLOGAN; ?> <br/>
		    			<em class="muted"><?=URL;?></em> <br/><br/>
		    			
		    			<small class="muted"><?=CHAMADA;?></small>
		    			</p>
						
		    			</small>
		    		</div>
		    	</div>
		    	
		    </div>
		    
		    <input type="hidden" name="tempo" class="stopwatch_geral stopwatch" value="00:00:00" />
		
			<script language="javascript" type="text/javascript" src="includes/js/jquery.timer.js"></script>
			
			<script type="text/javascript">
				$(document).ready(function() {	
					
					var Example1 = new (function() {
					    var $stopwatch;
					    var incrementTime = 1000;
					    var currentTime = 0;
					    
					    $(function() {
					        $stopwatch = $('.stopwatch');
					        Example1.Timer = $.timer(updateTimer, incrementTime, true);  
					    });
					
					    function updateTimer() {
					        var timeString = formatTime(currentTime);
					        $stopwatch.val(timeString);
					        currentTime += incrementTime;
					    }
						
					    this.resetStopwatch = function() {
					        currentTime = 0;
					        Example1.Timer.stop().once();
					    };
					
					});
				});
			</script>
		    
		</footer>
		
		<div id="res">
		
		</div>
			
		<? if ($_COOKIE[id_clinica]=="") { ?>
		<script>
			$('#modal_clinicas').modal({backdrop:'static'});
		</script>
		<? } ?>
		
		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-46442942-1', 'app.ttz.med.br');
		  ga('send', 'pageview');
		
		</script>
		
		<link href="includes/js/datepicker.css" rel="stylesheet" />
		
		<? if ($device!='computador') { ?>
		
		<link rel="stylesheet" type="text/css" href="includes/js/cubiq-add-to-homescreen/style/addtohomescreen.css">
		<script src="includes/js/cubiq-add-to-homescreen/src/addtohomescreen.js"></script>
		<script>
			addToHomescreen();
		</script>
		
		<script type="text/javascript">
	        $(document).on( "click", "a", function(event){
					if($(this).hasClass("interno")){
						
						$('.container-relative, footer').fadeOut('fast');
						
						event.preventDefault();
						//if(!$(event.target).attr("href")){
						//	location.href = $(event.target).parent().attr("href");
						//}
						//else{
							location.href = $(event.target).attr("href");
						//}
					}
					else {
					
					}
				});
	    </script>
		<? } ?>
		
		<?php
		mysqli_close($conexao1);
		?>
	</body>
</html>
<?
}
else {
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
	?>
	<script type="text/javascript">
		
		window.top.location.href='<?=SISTEMA_URL;?>/index2.php?pagina=login';
		
	</script>
	<?
}
?>