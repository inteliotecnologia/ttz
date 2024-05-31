<?
require_once("includes/_core/protecao.php");

if (!isset($_GET["pagina"])) $pagina= "home";
else $pagina= $_GET["pagina"];

session_start();
$retornou=0;

//Cadastro normal

/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
ROTINA DE INSERÇÃO
*/

//se retornou dados válidos da rede social ou se submeteu o cadastro do site
if (
	($retornou) || ( (strpos($_SERVER["HTTP_REFERER"], "1doc.com.b")!==false) && ($_POST["cadast"]=="1") )
	) {
	
	//social
	if ($retornou) {
		$usuario_nome= $user_data->displayName;
		$usuario_email= $user_data->email;
		$usuario_telefone= $user_data->phone;
		$usuario_organizacao= "";
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
	elseif ($_POST["cadast"]=="1") {
		$usuario_nome= prepara($_POST["nome"]);
		$usuario_email= prepara($_POST["email"]);
		$usuario_telefone= prepara($_POST["telefone"]);
		$usuario_organizacao= prepara($_POST["organizacao"]);
		$usuario_sexo= '';
		$usuario_data_nasc= '';
		$usuario_foto='';
		
		$usuario_identifier= '';
		$usuario_token= '';
		$usuario_secret= '';
	}
	
	if (($usuario_nome!='') && ($usuario_email!='') ) {

		if ($usuario_organizacao=='') $organizacao= 'Organização';
		else $organizacao= $usuario_organizacao;
		
		$plano= "Grátis";
		
		//die('oie');
		
		$var=0;
		inicia_transacao();
		
		$slug= substr(gera_auth(), 0, 5);
		
		$result1= mysqli_query($conexao2,"insert into clientes (cliente, email, contato, telefone, slug, cliente_auth, status,
								id_canal, plano, visivel, tipo_cliente, id_usuario, id_acesso) values
								('". $organizacao ."', '". $usuario_email ."', '". $usuario_nome ."', '". $usuario_telefone ."',
								'". $slug ."', '". gera_auth() ."', '1',
								'0', '". prepara($plano) ."', '4', '". prepara($_POST["tipo_organizacao"]) ."', '0', '0' ) ") or die("1: ".  mysqli_error($conexao2));
		if (!$result1) $var++;
		$id_cliente= mysqli_insert_id($conexao2);
		
		
		$result_at0= mysqli_query($conexao2,"insert into usuarios_clientes
							(id_cliente, slug, id_usuario, status, email,
							nome, data_cadastro, ultimo_login)
							values
							('". $id_cliente ."', '". $slug ."', '4', '1', '". $usuario_email ."',
							'". $usuario_nome ."', '". date('Y-m-d') ."', ''
							)
							");
							
		if (!$result_at0) $var++;
		
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'nome_organizacao', '". $organizacao ."', '0')
								");
		if (!$result_at) $var++;
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'telefone_organizacao', '". $usuario_telefone ."', '0')
								");
		if (!$result_at) $var++;
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'cliente_desde', '". date('Y-m-d') ."', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'data_cadastro', '". date('Y-m-d') ."', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'hora_cadastro', '". date('H:i:s') ."', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'analytics_id', 'UA-43013142-8', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'mixpanel_id', 'bb9b85eb49275ef9b21151e17414d7a6', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'maturidade', '1', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2,"insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'grupos', '123', '0')
								");
		if (!$result_at) $var++;
		$result_at= mysqli_query($conexao2, "insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'teste_ate', '". soma_data(date("Y-m-d"), 15, 0, 0) ."', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2, "insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'testeab', '". prepara($_POST['testeab']) ."', '0')
								");
		if (!$result_at) $var++;
		
		$result_at= mysqli_query($conexao2, "insert into clientes_meta (id_cliente, meta, valor, id_usuario)
									values ('". $id_cliente ."', 'informacoes', '". $informacoes ."', '0')
								");
		if (!$result_at) $var++;
		
		finaliza_transacao($var);
		
		// cria novo banco a partir de template padrão
		$novo_bd= "edoc_". $id_cliente;
		
		$result_novo= mysqli_query($conexao2, "create database ". $novo_bd ." ") or die("3: ".  mysqli_error($conexao2));
		if (!$result_novo) $var++;
		
		$seleciona_novo= mysqli_select_db($conexao2, $novo_bd);
		
		import_sql(PATH_SQLS ."edoc_template.sql");
		
		/* *** */
		
		$__SESSION["id_usuario"]=4;
		
		evento(1, "php", $__SESSION, 0, 4,
									"Cliente se cadastra",
									array()
								);
								
		/*
		switch ($_POST["tipo_organizacao"]) {
			case 1:
			import_sql(PATH_SQLS ."edoc_template_setores_1.sql");
			break;
			
			case 2:
			case 3:
			case 4:
			case 5:
			import_sql(PATH_SQLS ."edoc_template_setores_2.sql");
			break;
		}*/
		
		// ------------------------------
		
		if ($usuario_foto!='') {
			$usuario_foto2= rand(1,1000) ."_avatar.jpg";
			
			cria_pastas_iniciais_cliente_param($id_cliente);
			
			//$file = 'http://3.bp.blogspot.com/-AGI4aY2SFaE/Tg8yoG3ijTI/AAAAAAAAA5k/nJB-mDhc8Ds/s400/rizal001.jpg';
			//$newfile = $_SERVER['DOCUMENT_ROOT'] . '/img/submitted/yoyo.jpg';
			
			$caminho= CAMINHO . "uploads_". $id_cliente ."/avatars/". $usuario_foto2;
			
			@copy($usuario_foto, $caminho);
			
		}
		
		$result11= mysqli_query($conexao2, "insert into usuarios (nome, email, senha,
									identifier, token, secret, sexo,
									cpf, matricula, cargo, ramal, telefone, foto, data_nasc, relatorio, auth, id_usuario_criou, id_acesso,
									ultimo_login, data_cadastro, hora_cadastro, tema, status, alertas_email) values
									('". $usuario_nome ."', '". $usuario_email ."', '". md5('1doc') ."',
										'". $usuario_identifier ."', '". $usuario_token ."', '". $usuario_secret ."', '". $usuario_sexo ."',
										
										'". prepara($_POST["cpf"]) ."', '". prepara($_POST["matricula"]) ."',
										'". prepara($_POST["cargo"]) ."', '". prepara($_POST["ramal"]) ."', '". $usuario_telefone ."', '". $usuario_foto2 ."', '". $usuario_data_nasc ."', '". prepara($_POST["relatorio"]) ."', '". gera_auth() ."',
										'0', '0', '0', '". date('Y-m-d') ."', '". date('H:i:s') ."', 'Normal', '1', '2') ") or die("1: ".  mysqli_error($conexao2));
		if (!$result11) $var++;
		$id_usuario= mysqli_insert_id($conexao2);
		
		$result22= mysqli_query($conexao2, "insert into usuarios_setores (id_usuario, id_setor, perfil, data, hora, atual, principal, id_usuario_alterou, id_acesso) values
								('". $id_usuario ."', '1', '2',
								'". date('Y-m-d') ."', '". date('H:i:s') ."', '1', '1', '0', '0') ") or die("2: ".  mysqli_error($conexao2));
		if (!$result22) $var++;
		
		//seta_opcao_vetor("telefone", prepara($usuario_telefone));
		
		finaliza_transacao($var);
		
		$conteudo["cabecalho"]= "Bem-vindo!";
		
		$conteudo["corpo"]= "
		       
		    Olá, <strong>". primeira_palavra($usuario_nome) ."</strong>. Tudo bem? <br><br>
		    
			Aqui estão os seus dados para o primeiro acesso:<br/><br/>
			
			<b>E-mail:</b> ". prepara($usuario_email) ."<br/>
			<b>Senha:</b> 1doc <br/><br/>
			 
			<a style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background: #". $_CLIENTE["cor_tema"] ."; margin: 0; padding: 0; border-color: #". $_CLIENTE["cor_tema"] ."; border-style: solid; border-width: 10px 20px;\" target='_blank' href='". SISTEMA_URL ."b.php?pg=o/login&s=". $slug ."&email=". prepara($usuario_email) ."'>Ir ao sistema &raquo;</a>
			<Br/><br/>
			
			Temos uma equipe online e vamos ajudá-lo nesta fase de testes do ". SISTEMA_NOME ." em <strong>".$organizacao."</strong>. <br>
			";
		
		$conteudo["rodape"]= "";
		
		$para_email= array(prepara($usuario_email));
		
		@envia_email($para_email, $para_nome, "Boas vindas - Acesse agora", $conteudo, "[1Doc] Novo cliente", "");
		
		$cadastro=1;
	}
	else {
		$erro='dados';
	}
}
else {
	$erro='referer';
}

?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	
		<?
		include("__inner/arquivos/header_out.php");
		?>
		
		<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
mixpanel.init("<?=MIXPANEL_TOKEN;?>");</script><!-- end Mixpanel -->

	<style>
		.container, .navbar-static-top .container, .navbar-fixed-top .container, .navbar-fixed-bottom .container {
			width: 100% !important;
		}
	</style>

	</head>
	<body class="pg_o-login">
		
		<div class="container">
			<div class="row-fluid">
				
				<div class="caixa">
					<div class="span12">
						<div class="span6">
							
							<br/><br/><br/><br/><br/>
							
							<h2 class="sis_nome"><?=SISTEMA_NOME;?></h2>
							<p><?=SISTEMA_SLOGAN;?></p>
							
							<div style="margin:8px 0 12px 0;" class="row-fluid">
								<div class="span12">
									
								</div>
							</div>
							
						</div>
						
						<div class="span6">
							<br><br><br>
							<?
							if ($cadastro==1) {
								if ($var==0) {
							?>
								<a style="display:none;" id="Intercom" href="mailto:pzthv8fq@incoming.intercom.io">Support</a>
								
								<!-- Google Code for Criou conta Conversion Page -->
								<? /*<img height="1" width="1" alt="" src="//www.googleadservices.com/pagead/conversion/962338410/imp.gif?label=-6mFCI7H7AgQ6rzwygM&amp;guid=ON&amp;script=0"/>*/ ?>
								
								<script>
								  window.intercomSettings = {
								    
								    "user_id": "<?=$id_cliente;?>_<?=$id_usuario;?>",
								    name: "<?= ($usuario_nome);?>",
								    email: "<?= ($usuario_email);?>",
								    created_at: <?= strtotime(converte_data_completa_utc(date('Y-m-d') .' '. date('H:i:s'))); ?>,
								    
								    "telefone": "<?= "(". $usuario_telefone; ?>",
								    
								    "company": {
									  	"id": "<?=$id_cliente;?>",  
									  	"name": "<?= $organizacao;?>",
									  	"created_at": <?= strtotime(converte_data_completa_utc(date('Y-m-d') .' '. date('H:i:s'))); ?>,
									  	"slug": "<?= $slug; ?>",
									  	"sigla": "<?= strtoupper($slug); ?>",
									  	
									  	"plan": "<?= prepara($plano); ?>",
									  	"Canal": "Online",
									  	"Estagio": "Testando",
									  	<? /*"Tipo cliente": "<?= pega_tipo_cliente(prepara($_POST["tipo_organizacao"])); ?>",*/ ?>
									  	"teste_ate_at": <?= strtotime(soma_data(date('Y-m-d'), 15, 0, 0) .' '. date('H:i:s')); ?>
								    },
								    
								    "Primeiro": "1",
								    
								    "Perfil": "Nível 1 ADM",
									
									"Status": "<?= pega_status_usuario_singular(1);?>",
									"ID Usuario": "<?=$id_usuario;?>",
								    
								    "widget": {
								    	"activator": "#Intercom"
								    },
								    
								    "user_hash": "<?php echo hash_hmac("sha256", $id_cliente."_".$id_usuario, INTERCOM_APP_SM); ?>",
								    
								    app_id: "<?=INTERCOM_APP_ID;?>"
								};
								
								
								
								
								</script>
								<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/t59v2byb';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
								
								<script type="text/javascript">
									
									mixpanel.alias('<?=$id_cliente;?>_<?=$id_usuario;?>');
									
									mixpanel.identify('<?=$id_cliente;?>_<?=$id_usuario;?>');
									
									mixpanel.people.set({
										"$name": "<?= ($usuario_nome);?>",
										"$email": "<?= ($usuario_email);?>",
										"$created": "<?= converte_data_completa_utc(date('Y-m-d') .' '. date('H:i:s'));?>",
										"Nome completo": "<?= ($usuario_nome);?>",
										"Qtde logins": 0,
										"Qtde emissões": 0,
										"telefone": "<?= $usuario_telefone; ?>",
										"ID Usuario": "<?=$id_cliente;?>_<?=$id_usuario;?>",
										"ID Cliente": "<?=$id_cliente;?>",
										"Slug": "<?= $slug; ?>",
										"Sigla Cliente": "<?= strtoupper($slug); ?>",
										"Cliente": "<?= $organizacao;?>",
										"Plano": "<?= prepara($plano); ?>",
										"Canal": "Online",
										"Origem": "Site",
										"Status": "<?= pega_status_usuario_singular(1);?>"
									});
									
									mixpanel.track("Criou conta", {
										"Condicao": true,
										"Erro": false,
										"Testeab": "<?=$_POST['testeab'];?>",
										"Nome completo": "<?= prepara($usuario_nome);?>",
										"Versao do sistema": "<?=SISTEMA_VERSAO;?>",
										"ID Cliente": "<?=$id_cliente;?>",
										"Sigla Cliente": "<?= strtoupper($slug); ?>",
										"Cliente": "<?= $organizacao;?>",
									});
									
									/*setTimeout(function(){
										window.top.location.href='<?=SISTEMA_URL;?>b.php?pg=o/login&s=<?=$slug;?>&erro=w&email=<?= prepara($usuario_email);?>';
									}, 5000);*/
									
								</script>
								
								<br>
								<h3>Sua conta foi criada.</h3>
								<br />
								
								<p>Acesse seu e-mail (<em><?=$usuario_email;?></em>)<br> para confirmar e entrar no 1Doc.</p>
								<br/>
								
								<p><a class="btn btn-primary btn-large" href="<?=SISTEMA_URL;?>b.php?pg=o/login&s=<?=$slug;?>&erro=w&email=<?= prepara($usuario_email);?>">Login &raquo;</a></p>
								
							<?
							}
							else {
							?>
							<br>
							<h3>Problema ao criar sua conta (<?=$var;?>)</h3>
							
							<p>Tente novamente, caso o problema persista, entre em contato: <a href="mailto:contato@intelio.com.br">contato@intelio.com.br</a>.</p>
							
							<script type="text/javascript">
								mixpanel.track("Erro ao criar conta", {
										"Problema": "Erro na rotina de inserção",
										"Ação": "<?=$_POST["nome"];?> | <?=$_POST["email"];?>",
										"Condicao": false,
										"Erro": false,
										"Versao do sistema": "<?=SISTEMA_VERSAO;?>"
									});
							</script>
							<?
							}
							
							
							if ($erro=='dados') {
							?>
						
								<h3>Faltando dados</h3>
								
								<p>Preencha nome, e-mail e nome da organização<br> para criar sua conta.</p>
								<br />
								
								<a class="btn btn-primary" href="javascript:history.back(-1);">&laquo; voltar</a>
								
								<script type="text/javascript">
									mixpanel.track("Erro ao criar conta", {
											"Problema": "Faltando dados",
											"Ação": "<?=$_POST["nome"];?> | <?=$_POST["email"];?>",
											"Condicao": false,
											"Erro": true,
											"Versao do sistema": "<?=SISTEMA_VERSAO;?>"
										});
								</script>
								
							<?
							}
							elseif ($erro=='referer') {
							?>
							
							<h3>Conexão com problema</h3>
							
							<p>Por favor, volte ao site e refaça <br>o pedido de cadastro.</p>
							<br/>
							
							<a class="btn btn-info" href="javascript:history.back(-1);">&laquo; voltar</a>
							
							
							<script type="text/javascript">
								mixpanel.track("Erro ao criar conta", {
										"Problema": "Referrer inválido",
										"Ação": "<?=$_POST["nome"];?> | <?=$_POST["email"];?>",
										"Condicao": false,
										"Erro": false,
										"Versao do sistema": "<?=SISTEMA_VERSAO;?>"
									});
							</script>
							<? } ?>
							<? } /*else { //fim cadastro ?>
							
							<?php
								if( $user_data->photoURL ){
							?>
								<a href="<?php echo $user_data->profileURL; ?>"><img src="<?php echo $user_data->photoURL; ?>" title="<?php echo $user_data->displayName; ?>" border="0" width="100" height="120"></a>
							<?php
								}
								?>
								
							Oi, <?= $user_data->displayName; ?>. <br/>
							<em><?php echo $user_data->email; ?></em>
							
							<? } */ ?>
									
						</div>
					</div>
				</div>
			</div>
	    </div> <!-- /container -->
		
		<? include("__inner/arquivos/footer_in.php"); ?>
		
	</body>
</html>