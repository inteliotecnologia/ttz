<?
require_once("includes/conexao.php");

if (pode("1234", $_COOKIE["perfil"])) {
?>

<div class="container">
	<div class="hero-unit ">
		<div class="row-fluid">
			<div class="span8">
				
				<h3>Bem-vind<? if ($_COOKIE[sexo]=='f') echo 'a'; else echo 'o';?>, <?= primeira_palavra($_COOKIE[nome]);?>.</h3>
				<h5>Veja como utilizar neste vídeo de 1 minuto:</h5>
				<br/>
				
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
				
				
				<?
				/*$result_pc= mysqli_query($conexao1, "select * from pessoas_clinicas
										where id_pessoa = '". $_COOKIE[id_pessoa] ."'
										and   status_pc = '1'
										") or die(mysqli_error());
				$num_pc= mysqli_num_rows($result_pc);
				
				if ($num_pc==0) {
				?>
				<a href="#modal_clinica" data-toggle="modal" data-backdrop="static" class="btn btn-primary btn-large nova_clinica">Iniciar &raquo;</a>
				<? } else { */ ?>
				<a href="./?pagina=lancamento/lancamento" class="btn btn-primary btn-large">Prosseguir &raquo;</a>
				<? //} ?>
			</div>
		</div>
		<br/><br/>
		
	</div>
	
	<script type="text/javascript">
		mixpanel.track("Acessa página de Setup Inicial");
	</script>
	
	<div class="row-fluid">
		<div class="span12 text-center">
		
			<br /><br/>
			
		
		</div>
	</div>
</div>




<? } ?>