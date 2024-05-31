<?
if (pode("1234", $_COOKIE["perfil"])) {
	//require_once("includes/conexao.php");
	
	$result= mysqli_query($conexao1, "select * from  usuarios, pessoas
							where usuarios.id_usuario = '". $_COOKIE["id_usuario"] ."'
							and   pessoas.id_pessoa = usuarios.id_pessoa
							") or die(mysqli_error());
	$rs= mysqli_fetch_object($result);	
?>
			
	<? if ($_COOKIE[id_clinica]!='') include('dados_menu.php'); ?>
		
	<!--<div class="page-header">
		<h2>Clínicas onde trabalho</h2>
	</div>-->
	
	<?
	$result_pc= mysqli_query($conexao1, "select * from clinicas, pessoas_clinicas
												where  pessoas_clinicas.id_pessoa = '". $_COOKIE[id_pessoa] ."'
												and   pessoas_clinicas.id_clinica = clinicas.id_clinica
												and   pessoas_clinicas.status_pc = '1' 
												") or die(mysqli_error());
	
	$num_pc= mysqli_num_rows($result_pc);
	
	if ($num_pc>1) $str_s='is';
	else $str_s='l';
	
	if ($num_pc==0) {
		//echo '<p>Cadastre um local de trabalho para começar a registrar os atendimentos.</p>';
	?>
	
	<div class="container">
		<div class="hero-unit ">
			<div class="row-fluid">
				<div class="span8">
					
					<h3>Boas vindas, <?= primeira_palavra($_COOKIE[nome]);?>.</h3>
					<small>Assista o vídeo abaixo para entender como utilizar:</small>
					<br /><br/>
					
					<div id="video2" style="width:100%;background:#ccc;height:405px;"></div>
				
				<script>
			      // 2. This code loads the IFrame Player API code asynchronously.
			      var tag = document.createElement('script');
			
			      tag.src = "https://www.youtube.com/iframe_api";
			      var firstScriptTag = document.getElementsByTagName('script')[0];
			      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
			      // 3. This function creates an <iframe> (and YouTube player)
			      //    after the API code downloads.
			      var player;
			      function onYouTubeIframeAPIReady() {
			        player = new YT.Player('video', {
			          height: '400',
			          width: '100%',
			          videoId: 'dt3HfZJoGbU',
			          events: {
			            'onReady': onPlayerReady,
			            'onStateChange': onPlayerStateChange
			          }
			        });
			      }
			
			      // 4. The API will call this function when the video player is ready.
			      function onPlayerReady(event) {
			        //event.target.playVideo();
			      }
			
			      // 5. The API calls this function when the player's state changes.
			      //    The function indicates that when playing a video (state=1),
			      //    the player should play for six seconds and then stop.
			      var done = false;
			      function onPlayerStateChange(event) {
				    //alert(event.data);
				    
				    if (event.data == YT.PlayerState.ENDED) {
					    alert('terminou de assistir.');
				    }
				    
				    if (event.data == YT.PlayerState.PAUSED) {
				    	alert('pausou o vídeo em '+player.getCurrentTime());
				    }
				    
			        if (event.data == YT.PlayerState.PLAYING && !done) {
			          
			          done = true;
			          
			          alert('começou a assistir.');
			        }
			      }
			      function stopVideo() {
			        player.stopVideo();
			      }
			    </script>
				</div>
				<div class="span3 offset1" style="padding-top:120px;">
					<br/>	
					<a href="#modal_clinica" data-toggle="modal" data-backdrop="static" class="btn btn-primary btn-large nova_clinica">Iniciar &raquo;</a>
				</div>
			</div>
		</div>
	</div>
			
	
	<script>
		//window.top.location.href='./?pagina=lancamento/instrucao';
	</script>
	
	<?
	}
	else {
	?>
	
	<script type="text/javascript">
		mixpanel.track("Acessa Clínicas");
	</script>
	
	<br />
	
	<table cellspacing="0" width="100%" class="table table-striped table-hover">
		<thead>
	        <tr>
	            <!--<th width="5%">#</th>-->
	            <th width="75%" align="left">Local</th>
	            <? /*<th width="75%" align="left">Endereço</th>*/ ?>
	            <th width="25%">Ações</th>
	        </tr>
	    </thead>
	    <tbody>
			<?
	        $i=0;
			while ($rs_pc= mysqli_fetch_object($result_pc)) {
	        ?>
	        <tr id="linha_<?=$rs_pc->id_pc;?>">
	            <!--<td><?= $rs_pc->id_pc; ?></td>-->
	            <td><?= $rs_pc->clinica; ?></td>
	            <? /*<td class="menor"><?= $rs_pc->endereco .'. '. pega_cidade($rs_pc->id_cidade); ?></td>*/ ?>
	            <td align="center">
	                <? /*
	                <a class="btn btn-mini btn-success" href="./?pagina=acesso/trabalho_clinica&amp;id_pc=<?= $rs_pc->id_pc; ?>&acao=e">
	                	<i class="icon-white icon-pencil"></i> Detalhes
	                </a>
	                <a class="btn btn-mini btn-danger" href="javascript:apagaLinha('desabilitaPessoaClinica', <?=$rs_pc->id_pc;?>);" onclick="return confirm('Tem certeza que deseja remover esta clínica? Não se preocupe, os dados não serão apagados. Você somente não verá mais esta clínica na listagem para realizar os lançamentos diários.');">
	                    <i class="icon-white icon-trash"></i> Não trabalho mais aqui
	                </a>
	                */ ?>
	                
	                <a class="btn btn-mini btn-primary" href="./link.php?chamada=trocaClinica&amp;id_clinica=<?=$rs_pc->id_clinica;?>">
	                	<i class="icon-user"></i>&nbsp; Lançar
	                </a>
	                
	            </td>
	        </tr>
	        <? $i++; } ?>
	    </tbody>
	</table>
	
	<? } ?>
<? } ?>
