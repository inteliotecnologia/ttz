
$.fn.modal.Constructor.prototype.enforceFocus = function () {};

var msg_erro_padrao= "<h5>Ops :-(</h5> <br> Tentando reconectar... ";
var msg_erro_padrao_rodape= "<br/><br/><small>Sua ação não foi salva :(</small></small>";

/*
$(document).keypress(function(e) {
  if(e.which == 13) {
    // enter pressed
  }
});
*/

$(function() {

    $('.accordion').on('show', function (e) {
         $(e.target).prev('.accordion-heading').find('.accordion-toggle').parent().parent().parent().addClass('ac_in');
         
         $(e.target).prev('.accordion-heading').find('.icon-caret-right').removeClass('icon-caret-right').addClass('icon-caret-down');
         
         /*setTimeout(function() {
	         $('html, body').animate({
	            scrollTop: $(e.target).prev('.accordion-heading').find('h5.tit_procedimento').offset().top-60
	        }, 1200);
	     }, 400);*/
         
    });

    $('.accordion').on('hide', function (e) {
        $(this).find('.accordion-toggle').not($(e.target)).parent().parent().parent().removeClass('ac_in');
        
        $(this).find('.accordion-toggle').not($(e.target)).find('.icon-caret-down').removeClass('icon-caret-down').addClass('icon-caret-right');
        
    });

});

$(document).ready(function() {
	
	$('#tab_opcoes a, #tab_opcoes1 a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
    })
    
	$('.alert').alert();
	$('.alert.esconde').delay(7000).fadeOut(750);
	
	$('.tt').tooltip({
		delay: { show: 400, hide: 100 }
	});
	
	$('input[type=file]').bootstrapFileInput();
	$('.file-inputs').bootstrapFileInput();

	$('.nav-header').click(function () {
		$(this).parent().parent().children(':not(li:first-child)').toggle(100);
	});
	
	$('form').submit(function () {
        var btn = $(this).find('button:submit');
        btn.button('loading');
        $('.cancelar').hide();
    });
	
	$(document).on('change', '#tipo_convenio', function(event){
		var tipo_convenio= $('#tipo_convenio').val();
		
		$('.tm').hide();
		$('.tm').removeAttr('required');
		
		if (tipo_convenio!='1') $('.recebimento_convenio').show();
		else $('.recebimento_convenio').hide();
	});
	
	$('#atos li a').click(function() {
		var classe= $(this).attr('class');
		var posicao= $(this).attr('data-posicao'); 
		var id_ato= $(this).attr('data-id_ato'); 
		
		if (posicao=='1') posicao='2';
		else posicao='1';
		
		$('.atos_'+posicao+' li').removeClass('active');
		$('#link_ato_'+id_ato+'_'+posicao).parent().addClass('active');
	});
	
	$(document).on('click', '.aplicar_todos', function(event){
		
		var id_convenio= $(this).attr('data-id_convenio');
		var id_ato= $(this).attr('data-id_ato');
		var a= $(this).attr('data-a');
		var campo= $(this).attr('data-rel');
		
		var percentual= parseInt($('#'+campo).val());
		var rel= $(this).attr('rel');
		//alert(1);
		
		bootbox.confirm("Deseja aplicar esta porcentagem aos demais convênios relacionados?", function(resposta) {
			if (resposta) {
				
				$(rel+" input.percentual_campo").each(function() {
					//se o campo estiver vazio, preenche com o percentual do primeiro campo
					$(this).val(percentual);
				});
			}
		}); 
		
		
	});
	
	$(document).on('click', '#nova_clinica', function(event){
		$('#modal_nova_clinica').modal({backdrop:'static'});
	});
	
	/*
	$(document).on('blur', '#anotacao', function(event){
			
		var data= $(this).attr("data-data");
		var anotacao= $("#anotacao").val();
		
		if (anotacao!='') {
			$.get('link.php', {chamada: 'salvaAnotacao', data: data, anotacao: anotacao },
	
			function(retorno) {
				if (retorno[0]=='@') {
					$('#accordion_anotacoes').effect( 'highlight', '', 1400);
				} else {
					bootbox.alert(retorno, function() { });	
				}
				
			});
		}
	});*/
	
	/*
	$(document).on('click', '#accordion_anotacoes .accordion-toggle', function(event){
		
		if (!$('#accordion_anotacoes_anotacao').hasClass('in')) {
			
			var data= $(this).attr("data-data");
			var anotacao= $("#anotacao").val();
			
			if (anotacao!='') {
				$.get('link.php', {chamada: 'salvaAnotacao', data: data, anotacao: anotacao },
		
				function(retorno) {
					if (retorno[0]=='@') {
						$('#accordion_anotacoes').effect( 'highlight', '', 800);
					} else {
						bootbox.alert(retorno, function() { });	
					}
					
				});
			}
		}
	});
	*/
	
	$(document).on('click', '.muda_mes', function(event){
		
		var data_inicio= $(this).attr('rel');
		
		$('#calendario').html('<img src="images/loading2.gif" alt="" />');
		
		if (data_inicio!='') {
			$.get('link.php', {chamada: 'navegaCalendario', data_inicio: data_inicio },
	
			function(retorno) {
				$('#calendario').html(retorno);
			});
		}
		else {
			
		}
		
		return false;
	});
	
	$(document).on('blur', '.lancamento_quantidade', function(event){
		var valor= $(this).val();
		
		if ( (valor!='') && (valor!='0') ) {
			var id_ato= $(this).attr('data-id_ato');
			var i= $(this).attr('data-i');
			var t= $(this).attr('data-t');
			
			$(this).attr('data-valor', valor);
			
			atualiza(id_ato, i, t, 2);
		}
		
	});
	
	var procedimentos = new Bloodhound({
	    datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.procedimento); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: 'link.php?chamada=pesquisaProcedimento&query=',
			
	        replace: function(url, query) {
	            return url + query;
	        },
	        ajax: {
	            type: "GET",
	            async: false,
	            dataType: "json",
	        }
	    }
	});
	
	procedimentos.initialize();
	
	$('#nc_nome_exibicao_procedimento').typeahead({
		hint: true,
		highlight: true,
		minLength: 3,
	},
	{
	    name: 'procedimento_nome',
	    displayKey: 'procedimento',
	    source: procedimentos.ttAdapter(),
	    templates: {
			suggestion: Handlebars.compile([
		      '<div class="convenio_nome">{{procedimento}}</div> <div class="convenio_tipo_convenio">{{codigo_cbhpm}}</div>'
		    ].join(''))
		}
	}).on('typeahead:selected', function(event, datum) {
	    if (datum!=undefined) {
	    	
	    	$('#nc_id_procedimento').val(datum.id_procedimento);
	    	
	    	$('#nc_codigo_cbhpm').val(datum.codigo_cbhpm);
	    	//$('#nc_nome_exibicao_convenio, #modal_convenio_novo form .tt-hint').val(datum.convenio);
	    	
	    }
	});
	
	var convenios = new Bloodhound({
	    datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.convenio); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: 'link.php?chamada=pesquisaConvenio&query=',
			
	        replace: function(url, query) {
	            return url + query;
	        },
	        ajax: {
	            type: "GET",
	            async: false,
	            dataType: "json",
	        }
	    }
	});
	
	convenios.initialize();
	
	$('#nc_nome_exibicao_convenio').typeahead({
		hint: true,
		highlight: true,
		minLength: 3,
	},
	{
	    name: 'convenio_nome',
	    displayKey: 'convenio',
	    source: convenios.ttAdapter(),
	    templates: {
			suggestion: Handlebars.compile([
		      '<div class="convenio_nome">{{convenio}}</div> <div class="convenio_tipo_convenio">{{tipo_convenio}}</div>'
		    ].join(''))
		}
	}).on('typeahead:selected', function(event, datum) {
	    if (datum!=undefined) {
	    	
	    	$('#nc_id_convenio').val(datum.id_convenio);
	    	
	    	$('#nc_nome_exibicao_convenio').attr("disabled", "disabled");
	    	
	    	$('.area_tipo_convenio label, .area_tipo_convenio select').hide();
	    	
	    	//$('#nc_nome_exibicao_convenio, #modal_convenio_novo form .tt-hint').val(datum.convenio);
	    	
	    }
	});
	
	var clinicas = new Bloodhound({
	    datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.clinica); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: 'link.php?chamada=pesquisaClinica&query=',
			
	        replace: function(url, query) {
	            return url + query;
	        },
	        ajax: {
	            type: "GET",
	            async: false,
	            dataType: "json",
	        }
	    }
	});
	
	clinicas.initialize();
	
	$('#nc_clinica').typeahead({
		hint: true,
		highlight: true,
		minLength: 3,
	},
	{
	    name: 'clinica_nome',
	    displayKey: 'clinica',
	    source: clinicas.ttAdapter(),
	    templates: {
			suggestion: Handlebars.compile([
		      '<div class="clinica_nome">{{clinica}}</div>'
		    ].join(''))
		}
	}).on('typeahead:selected', function(event, datum) {
	    if (datum!=undefined) {
	    	
	    	$('#nc_id_clinica').val(datum.id_clinica);
	    	//$('#nc_nome_exibicao_convenio, #modal_convenio_novo form .tt-hint').val(datum.convenio);
	    	
	    }
	});
	
	$(document).on('submit', '#modal_clinica_form', function(event){
		
		$.ajax({ // create an AJAX call...
	        data: $(this).serialize(), // get the form data
	        type: $(this).attr('method'), // GET or POST
	        url: $(this).attr('action'), // the file to call
	        success: function(retorno) { // on success..
	        	
	        	if (retorno=='0') {
	        		
	        		mixpanel.track("Cadastrou clínica");
	        		
	        		setTimeout(function() {
			        	window.top.location.href='./?pagina=lancamento/lancamento&erros=0';
			        }, 1000);
				}
	        }
		});
		
		return(false);
	});
	
	$(document).on('click', '.exclui_horario', function(event){
		
		var id_pcp= $(this).attr("data-id_pcp");
		
		$.ajax({ 
			cache: false,
	        data: {chamada: 'excluiHorarioPlantao', id_pcp: id_pcp },
	        type: 'get',
	        url: 'link.php',
	        timeout: 5000,
	        error: function(x, t, m) {
	        	//$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno=="0") {
	        		
	        		$('#modal_plantao_hora').modal('hide');
	        		
		        	mixpanel.track("Excluiu horário", {
			        	"id_pcp": ""+id_pcp+""
		        	});
		        		
	        		setTimeout(function() {
			        	window.top.location.href='./?pagina=lancamento/lancamento&erros=0';
			        }, 300);
		        
		        }
	        
	        }
	    });
		
		return(false);
	});
	
	$(document).on('click', '#cancelar_conta', function(event){
		
		$('#modal_cancela_conta').modal({backdrop:'static'});
		
		setTimeout(function() {
			$('#senha3').focus();
		}, 600);
	});
	
	$(document).on('click', '#finalizar_lancamento', function(event){
		var terminado= $(this).attr('data-terminado');
		var id_clinica= $(this).attr('data-id_clinica');
		var data= $(this).attr('data-data');
		var data_formatada= $(this).attr('data-data_formatada');
		var identifica_atendimentos= $('#identifica_atendimentos').val();
		//alert(terminado);
		$('#modal_fecha_dia_form #fd_terminado').val(terminado);
		$('#modal_fecha_dia_form #fd_id_clinica').val(id_clinica);
		
		$('#modal_fecha_dia').modal({backdrop:'static'});
		
		if ( (terminado=='0') || (terminado=='') ) {
			$('.lfechar').html('Fechar');
		}
		else {
			$('.lfechar').html('Liberar');
		}
		
		setTimeout(function() {
			$('#senha3').focus();
		}, 600);
	});
	
	$(document).on('submit', '#modal_cancela_conta_form', function(event){
		
		$.ajax({
	        cache: false,
	        data: $(this).serialize(),
	        type: $(this).attr('method'),
	        url: $(this).attr('action'),
	        timeout: 5000,
	        error: function(x, t2, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno=='n') {
		        	//bootbox.alert("Senha incorreta.", function() { });
		        	alert("Senha incorreta.");
		        	$('.cadastrar, .cancelar').button('reset').show();
	        	}
	        	else {
	        	
		        	if (retorno=='0') {
		        		
		        		window.top.location.href='./index2.php?pagina=login&erro=c';	
		        		
					}
				}
	        }
		});
		
		return(false);
	});
	
	$(document).on('submit', '#modal_fecha_dia_form', function(event){
		
		var terminado= $("#fd_terminado").val();
		var data_n= $("#fd_data").val();
		var id_clinica_str= $("#fd_id_clinica").val();
		
		$.ajax({
	        cache: false,
	        data: $(this).serialize(),
	        type: $(this).attr('method'),
	        url: $(this).attr('action'),
	        timeout: 5000,
	        error: function(x, t2, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno=='n') {
		        	alert("Senha incorreta.");
		        	$('.cadastrar, .cancelar').button('reset').show();
	        	}
	        	else {
	        	
		        	if (retorno=='0') {
		        		
		        		
		        		if ( (terminado=='0') || (terminado=='') ) {
	            		
		            		mixpanel.track("Fechou o caixa");
		            		
							$('#finalizar_lancamento').button('reset');	
							$('#finalizar_lancamento').html('<i class=\'icon icon-pencil icon-white\'></i> &nbsp;Liberar para edição');
							$('#finalizar_lancamento').attr('data-terminado', '1');
							
							$('#finalizar_lancamento').removeClass('btn-primary');
							$('#finalizar_lancamento').addClass('btn-danger');
							
							$('.clinica_'+id_clinica_str+'_dia_'+data_n).html('<img src="images/cadeado.png" /> ');
							
							$('.lancamento_quantidade').attr('disabled', 'disabled');
							$('.btn-mais, .btn-zera').hide();
							
							var som= Math.floor(Math.random()*4)+1;
							
							var silenciar_sons= $('#silenciar_sons').is(':checked');
							//if (!silenciar_sons) $('#som_'+som)[0].play();
							
							$("#collapseCalendario").collapse('show');
						}
						else if (terminado=='1') {
							
							mixpanel.track("Liberou para edição");
	            	
							$('#finalizar_lancamento').button('reset');	
							$('#finalizar_lancamento').html('<i class=\'icon icon-lock icon-white\'></i> &nbsp;Fechar o caixa');
							$('#finalizar_lancamento').attr('data-terminado', '0');
							$('#finalizar_lancamento').removeClass('btn-danger');
							$('#finalizar_lancamento').addClass('btn-primary');
							
							$('.clinica_'+id_clinica_str+'_dia_'+data_n).html('');
							
							if (identifica_atendimentos=='1') $('.lancamento_quantidade').removeAttr('disabled');
							
							$('.btn-mais, .btn-zera').show();
							
						}
						
						$('#senha3').val('');
						$('.cadastrar, .cancelar').button('reset').show();
						$('#modal_fecha_dia').modal('hide');
		        		
					}
					else alert('Erro');
				}
	        }
		});
		
		return(false);
	});
	
	$(document).on('submit', '#modal_clinica_opcoes_form', function(event){
		
		$.ajax({ // create an AJAX call...
	        data: $(this).serialize(), // get the form data
	        type: $(this).attr('method'), // GET or POST
	        url: $(this).attr('action'), // the file to call
	        success: function(retorno) { // on success..
	        	
	        	if (retorno=='0') {
		        	window.top.location.href='./?pagina=lancamento/lancamento&erros=0';
				}
	        }
		});
		
		return(false);
	});
	
	$(document).on('submit', '#modal_procedimento_novo_form', function(event){
		
		var data= $('#modal_procedimento_novo_form #nc_data').val();
		
		$.ajax({
	        cache: false,
	        data: $(this).serialize(),
	        type: $(this).attr('method'),
	        url: $(this).attr('action'),
	        timeout: 5000,
	        error: function(x, t2, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	//alert(retorno[0]);
	        	if (retorno[0]=='0') {
	        		
	        		$.ajax({ 
						cache: false,
				        data: {chamada: 'carregaProcedimentos', data: data },
				        type: 'get',
				        url: 'link.php',
				        timeout: 5000,
				        error: function(x, t, m) {
				        	$('.cadastrar, .cancelar').button('reset').show();
				        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
				        	
					    },
				        success: function(retorno) {
				        	mixpanel.track("Associou procedimento");
							
							$('#modal_procedimento_novo').modal('hide');
							
							$("#accordion_procedimentos").html(retorno);
				        }
				    });
				}
	        }
		});
		
		return(false);
	});
	
	$(document).on('submit', '#modal_procedimento_edita_form', function(event){
		
		var data= $('#modal_procedimento_edita_form #nc_data').val();
		
		$.ajax({
	        cache: false,
	        data: $(this).serialize(),
	        type: $(this).attr('method'),
	        url: $(this).attr('action'),
	        timeout: 5000,
	        error: function(x, t2, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	//alert(retorno[0]);
	        	if (retorno[0]=='0') {
	        		
	        		$.ajax({ 
						cache: false,
				        data: {chamada: 'carregaProcedimentos', data: data },
				        type: 'get',
				        url: 'link.php',
				        timeout: 5000,
				        error: function(x, t, m) {
				        	$('.cadastrar, .cancelar').button('reset').show();
				        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
				        	
					    },
				        success: function(retorno) {
				        	
				        	mixpanel.track("Editou procedimento");
							
							$('#modal_procedimento_edita').modal('hide');
							
							$("#accordion_procedimentos").html(retorno);
				        }
				    });
	        		
	        		
			        
				}
	        }
		});
		
		return(false);
	});
	
	$(document).on('submit', '#modal_convenio_novo_form', function(event){
		
		var data= $('#modal_convenio_novo_form #nc_data').val();
		var id_ato= $('#modal_convenio_novo_form #nc_id_ato').val();
		var a= $('#modal_convenio_novo_form #nc_a').val();
		
		$.ajax({
	        cache: false,
	        data: $(this).serialize(),
	        type: $(this).attr('method'),
	        url: $(this).attr('action'),
	        timeout: 5000,
	        error: function(x, t2, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno=='0') {
	        		
	        		$.ajax({ 
						cache: false,
				        data: {chamada: 'carregaAtoTipoConvenio', data: data, id_ato: id_ato, a: a },
				        type: 'get',
				        url: 'link.php',
				        timeout: 5000,
				        error: function(x, t, m) {
				        	$('.cadastrar, .cancelar').button('reset').show();
				        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
				        	
					    },
				        success: function(retorno) {
				        	
				        	mixpanel.track("Cadastrou convênio");
				        	
				        	$('#modal_convenio_novo').modal('hide');
								
				        	$('#collapse_procedimento_'+id_ato+' .accordion-inner').html('<img src="images/loading.gif" alt="" />');
					        $('#collapse_procedimento_'+id_ato+' .accordion-inner').html(retorno);
				        }
				    });
				}
	        }
		});
		
		return(false);
	});
	
	
	$(document).on('click', '.minimiza_video', function(event){
		if ($(this).parent().parent().find('.ato').hasClass('fechado')) {	
			$(this).parent().parent().find('.ato').slideDown();
			$(this).parent().parent().find('.ato').removeClass('fechado');
			
			$(this).find('i').removeClass('icon-chevron-right');
			$(this).find('i').addClass('icon-chevron-down');
			
			$('.lll').html('Esconder');
		}
		else {
			$(this).parent().parent().find('.ato').slideUp();
			$(this).parent().parent().find('.ato').addClass('fechado');
			
			$(this).find('i').removeClass('icon-chevron-down');
			$(this).find('i').addClass('icon-chevron-right');
			
			$('.lll').html('Mostrar');
		}
			
		
	});
	
	
	$(document).on('click', '.minimiza_grupo', function(event){
		if ($(this).parent().find('.ato').hasClass('fechado')) {	
			$(this).parent().find('.ato').slideDown();
			$(this).parent().find('.ato').removeClass('fechado');
			
			$(this).find('i').removeClass('icon-chevron-right');
			$(this).find('i').addClass('icon-chevron-down');
		}
		else {
			$(this).parent().find('.ato').slideUp();
			$(this).parent().find('.ato').addClass('fechado');
			
			$(this).find('i').removeClass('icon-chevron-down');
			$(this).find('i').addClass('icon-chevron-right');
			
		}
			
		
	});
	
	$(document).on('click', '.btn-edita_procedimento', function(event){
		var id_procedimento= $(this).attr('data-id_procedimento');
		var data= $(this).attr('data-data');
		
		$.ajax({ 
			cache: false,
	        data: {chamada: 'editaProcedimento', id_procedimento: id_procedimento, data: data },
	        type: 'get',
	        url: 'link.php',
	        timeout: 5000,
	        error: function(x, t, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
				$('#modal_procedimento_edita_form').html(retorno);
				
				$('#modal_procedimento_edita').modal({backdrop:'static'});
				
				setTimeout(function() { $('#nc_apelido').focus(); }, 500);
	        
	        }
	    });
	});
	
	$(document).on('click', '.btn-exclui_procedimento', function(event){
		var id_procedimento= $(this).attr('data-id_procedimento');
		var data= $(this).attr('data-data');
		
		var resposta= confirm('Remover este procedimento de sua lista?');
		
		if (resposta) {
		
			$.ajax({ 
				cache: false,
		        data: {chamada: 'excluiProcedimentoPessoaClinica', id_procedimento: id_procedimento, data: data },
		        type: 'get',
		        url: 'link.php',
		        timeout: 5000,
		        error: function(x, t, m) {
		        	$('.cadastrar, .cancelar').button('reset').show();
		        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
		        	
			    },
		        success: function(retorno) {
		        	
		        	mixpanel.track("Removeu procedimento", {
			        	"ID Procedimento": ""+id_procedimento+""
		        	});
		        	
		        	if (retorno=='0') {
						$('#procedimento_'+id_procedimento+'').remove();
						
						$('.alterar_procedimento').trigger('click');
					}
					else bootbox.alert(retorno, function() { });
		        
		        }
		    });
		}
	});
	
	$(document).on('click', '.btn-exclui_convenio', function(event){
		var id_ato= $(this).attr('data-id_ato');
		var a= $(this).attr('data-a');
		var t= $(this).attr('data-t');
		var i= $(this).attr('data-i');
		var id_convenio= $(this).attr('data-id_convenio');
		var nome_convenio= $(this).attr('data-nome_convenio');
		
		var resposta= confirm('Remover da sua lista de atendimento?');
		
		if (resposta) {
		
			$.ajax({ 
				cache: false,
		        data: {chamada: 'excluiConvenioPessoaClinica', id_ato: id_ato, id_convenio: id_convenio },
		        type: 'get',
		        url: 'link.php',
		        timeout: 5000,
		        error: function(x, t, m) {
		        	$('.cadastrar, .cancelar').button('reset').show();
		        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
		        	
			    },
		        success: function(retorno) {
		        	
		        	mixpanel.track("Removeu convênio", {
			        	"Convênio": ""+nome_convenio+"",
			        	"ID Convênio": id_convenio,
			        	"ID Ato": id_ato
		        	});
		        	
		        	if (retorno=='0') {
						$('.linha_'+id_ato+'_'+t+'_'+i+'').fadeOut(300);
						$('.linha_'+id_ato+'_'+t+'_'+i+'').remove();
						
						$('.table-lancamento').removeClass('table-striped');
						$('.table-lancamento').addClass('table-striped');
					}
					else bootbox.alert(retorno, function() { });
		        
		        }
		    });
		}
	});
	
	$(document).on('click', '.alterar_procedimento', function(event){
		
		var editando= $(this).attr('editando');
		
		if (editando=='1') {
			$(this).attr('editando', '');
			$('#accordion_procedimentos .btn-exclui_procedimento').hide();
			$('#accordion_procedimentos .btn-edita_procedimento').hide();
			
			$('#accordion_procedimentos h5.tit_procedimento').css('marginLeft', '0px');
			
			$(this).find('.lbl').html('remover');
			$(this).find('i').show();
		}
		else {
			$(this).attr('editando', '1');

			$("#accordion_procedimentos").find('.btn-exclui_procedimento').show();
			$("#accordion_procedimentos").find('.btn-edita_procedimento').show();
			
			$('#accordion_procedimentos h5.tit_procedimento').css('marginLeft', '57px');
			
			$(this).find('.lbl').html('OK');
			$(this).find('i').hide();
		}
	});
	
	$(document).on('click', '.alterar_convenio', function(event){
		
		var id_ato= $(this).attr('data-id_ato');
		var a= $(this).attr('data-a');
		
		//alert(id_ato+' -> '+a+' -> '+t)
		
		var editando= $(this).attr('editando');
		
		if (editando=='1') {
			
			$(this).attr('editando', '');
			$('.well_bloco_'+id_ato+'_'+a+' .btn-exclui_convenio').fadeOut(200);
			$('.well_bloco_'+id_ato+'_'+a+' .vc_link').fadeOut(200);
			$('.well_bloco_'+id_ato+'_'+a+' strong.mr').css('marginLeft', '5px');
			$('.well_bloco_'+id_ato+'_'+a+' strong.mr').css('marginBottom', '0px');
			$('.well_bloco_'+id_ato+'_'+a+' .bloco_botoes').show();
			$('.well_bloco_'+id_ato+'_'+a+' .vc_link_atalho_valor').show();
			
			$(this).find('.lbl').html('remover');
			$(this).find('i').show();
		}
		else {
			$(this).attr('editando', '1');
			
			$('.linha_normal').show();
			$('.linha_extra').show();
			
			$('.well_bloco_'+id_ato+'_'+a+' tbody tr').each(function() {
				var valor= $(this).find('.lancamento_quantidade').attr('data-valor');
				
				if (valor=='') {
					$(this).find('.btn-exclui_convenio').fadeIn(400);
					$(this).find('.vc_link').fadeIn(400);
					
					$('.well_bloco_'+id_ato+'_'+a+' strong.mr').css('marginLeft', '42px');
					$('.well_bloco_'+id_ato+'_'+a+' strong.mr').css('marginBottom', '7px');
					
					$('.well_bloco_'+id_ato+'_'+a+' .bloco_botoes').hide();
					$('.well_bloco_'+id_ato+'_'+a+' .vc_link_atalho_valor').hide();
					
				}
			});
			
			//$('.well_bloco_'+id_ato+'_'+a+'_'+t+' .novo_convenio').fadeIn(400);
			
			$(this).find('.lbl').html('OK');
			$(this).find('i').hide();
		}
	});
	
	$(document).on('click', '.btn-mais', function(event){
		
		var id_ato= $(this).attr('data-id_ato');
		var i= $(this).attr('data-i');
		var t= $(this).attr('data-t');
		
		var identifica_atendimentos= $('#identifica_atendimentos').val();
		
		if (identifica_atendimentos!='1') {
			
			var data= $('#data').val();
			var id_ato= $(this).attr('data-id_ato');
			var nome_ato= $(this).attr('data-nome_ato');
			var id_clinica= $('#id_clinica').val();
			var id_convenio= $('#id_convenio_'+id_ato+'_'+i).val();
			var nome_convenio= $('#nome_convenio_'+id_ato+'_'+i).val();
			var tipo_convenio= $('#tipo_convenio_'+id_ato+'_'+i).val();
			var modo_recebimento_convenios_pagos= $('#modo_recebimento_convenios_pagos').val();
			var recebimento= $('#recebimento_'+id_ato+'_'+i).val();
			var valor= $('#valor_'+id_ato+'_'+i).val();
			var ordem= $('#ordem_'+id_ato+'_'+i).val();
			var percentual_clinica= $('#percentual_clinica_'+id_ato+'_'+i).val();
			var percentual_medico= $('#percentual_medico_'+id_ato+'_'+i).val();
			var valor_formatado= $('.vc_label_'+id_ato+'_'+id_convenio+'_'+ordem+' small').html();
			
			//$('#modal_atendimento .modal-header h4').html(nome_convenio+' &nbsp; <small>'+valor_formatado+'</small>');
			
			//$('.btn-mini-tipo_atendimento').removeClass('active');
			
			//sempre setar consulta como padrão, se for retorno... MUDA!
			//$('#tipo_atendimento').val('1');
			//$('.btn-mini-tipo_atendimento').removeClass('active');
			//$('.btn-mini-tipo_atendimento-1').addClass('active');
				
			if (id_ato=='1') {
				$('.div_tipo_atendimento').show();
				
			}
			else {
				$('.div_tipo_atendimento').hide();
				//quando não é consulta, sempre considerar atendimento pago, pois não existe retorno de procedimento, é sempre retorno de uma consulta
				$('#tipo_atendimento').val('1');
			}
			
			$('.novo_ato').html(nome_ato);
			$('.novo_nome_convenio').html(nome_convenio);
			$('.novo_valor_convenio').html(valor_formatado);
			$('.novo_data').html(data);

			$('#modal_atendimento #paciente_atendimento_id').val('');
			$('#modal_atendimento #edicao').val('');
			$('#modal_atendimento #paciente_data').val(data);
			$('#modal_atendimento #id_convenio').val(id_convenio);
			$('#modal_atendimento #campo_i').val(i);
			$('#modal_atendimento #campo_t').val(t);
			$('#modal_atendimento #paciente_id_clinica').val(id_clinica);
			$('#modal_atendimento #id_ato').val(id_ato);
			$('#modal_atendimento #nome_convenio').val(nome_convenio);
			$('#modal_atendimento #tipo_convenio').val(tipo_convenio);
			$('#modal_atendimento #paciente_modo_recebimento_convenios_pagos').val(modo_recebimento_convenios_pagos);
			$('#modal_atendimento #recebimento').val(recebimento);
			$('#modal_atendimento #valor').val(valor);
			$('#modal_atendimento #ordem').val(ordem);
			$('#modal_atendimento #percentual_clinica').val(percentual_clinica);
			$('#modal_atendimento #percentual_medico').val(percentual_medico);
			
			$('#modal_atendimento').modal({backdrop:'static'});
			
			//qwe
			$('#modal_atendimento_form .cancelar').show();
			$('#modal_atendimento_form button').button('reset');
			
			$('#nome_paciente').val("");
			
			setTimeout( function() { $('#nome_paciente').focus(); }, 600);
		}
		//else if (identifica_atendimentos=='2') {
		
		//}
		// +1 contabilidade
		else {
			atualiza(id_ato, i, t, 1);
		}
		
		return false;
	});
	
	$(document).on('click', '.nova_clinica', function(event){
		
		$('#modal_clinica_opcoes').modal('hide');
		
		setTimeout( function() { $('#nc_clinica').focus(); }, 600);
	});
	
	//Ao clicar no botão mais, quando é um procedimento, automaticamente volta para a aba de Consultas
	/*$(document).on('click', '#ato_pai_2 .btn-mais', function(event){
		var identifica_atendimentos= $('#identifica_atendimentos').val();
		
		if (identifica_atendimentos!='2')
			setTimeout(function(){ $('#link_ato_1_1').trigger( "click" ); }, 2000);
	});*/
	
	$(document).on('click', '.btn-zera', function(event){
		
		var confirma= confirm('Zerar os atendimentos deste convênio?');
		
		if (confirma) {
			var id_ato= $(this).attr('data-id_ato');
			var i= $(this).attr('data-i');
			var t= $(this).attr('data-t');
			var des= $(this).attr('disabled');
			var rel= $(this).attr('rel');
			
			if (des!='disabled') {
				campo= $('.'+rel).val('');
				$(this).attr('disabled', 'disabled');
				atualiza(id_ato, i, t, 0);
			}
		}
		
		return false;
	});
	
	$(document).on('click', '.area .close', function(event){
		var confirmar= confirm('Tem certeza que deseja remover?');
		var id_pc= $(this).attr('rel');
		var data_dismiss= $(this).attr('data-dismiss');
		
		if (confirmar) {
			if (id_pc!='') {
				
				$.ajax({ 
					cache: false,
			        data: {chamada: 'desabilitaPessoaClinica', id_pc: id_pc },
			        type: 'get',
			        url: 'link.php',
			        timeout: 5000,
			        error: function(x, t, m) {
			        	$('.cadastrar, .cancelar').button('reset').show();
			        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
			        	
				    },
			        success: function(retorno) {
			        	if (retorno=='s') {
							$('.area_pc_'+id_pc).fadeOut();
							$('.area_pc_'+id_pc).remove();
						}
						else alert('Não foi possível excluir');
			        }
			    });
			}
			else {
				//alert($(this).parent().parent().attr('class'));
				
				$('.'+data_dismiss).fadeOut();
				$('.'+data_dismiss).remove();
			}
		}
		return false;
	});
	
	$(document).on('change', '#id_procedimento', function(event){
		
		var escolha= $('#id_procedimento option:selected').val();
		
		if (escolha=='-1') alert('Em breve esta funcionalidade para você! :)');
	});
	
	$(document).on('click', '.btn-adicionar_procedimento', function(event){
		var ato= $('#id_procedimento option:selected').html();
		var id_ato= $('#id_procedimento option:selected').val();
		
		//alert(id_ato+ato);
		
		if (id_ato!='') {
			var ultimo_a= $('.ultimo_a').val();
			var ultimo_k= $('.ultimo_k').val();
			
			var id_clinica= $('#id_clinica').val();
			
			//alert(1);
			
			$.ajax({ 
				cache: false,
		        data: {chamada: 'geraSetupAto', id_clinica: id_clinica, id_ato: id_ato, ultimo_a: ultimo_a, ultimo_k: ultimo_k },
		        type: 'get',
		        url: 'link.php',
		        timeout: 5000,
		        error: function(x, t, m) {
		        	$('.cadastrar, .cancelar').button('reset').show();
		        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
		        	
			    },
		        success: function(retorno) {
		        	$('.nenhum_procedimento').remove();
									
					$('#accordion_procedimentos').append('<div class="accordion-group" id="procedimento_'+id_ato+'"><div class="accordion-heading"><h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_procedimentos" href="#collapse_procedimento_'+id_ato+'">'+ato+'</a></h4><a class="pull-right btn btn-mini btn-danger btn-remove_procedimento" data-id_ato="'+id_ato+'" href="javascript:void(0);">remover procedimento</a></div><div id="collapse_procedimento_'+id_ato+'" class="accordion-body collapse in"><div class="accordion-inner">'+retorno+'</div></div></div>');
					
					$('#id_procedimento option:selected').remove();
		        }
		    });
		}
	});
	
	$(document).on('click', '.btn-remove_procedimento', function(event){
		var id_ato= $(this).attr('data-id_ato');
		
		$('#procedimento_'+id_ato).remove();
		
		return false;
	});
	
	$(document).on('click', '.btn-remove-vm', function(event){
		var rel= $(this).attr('rel');
		
		$('.valores_multiplos_'+rel).remove();
		
		return false;
	});
	
	$(document).on('click', '.btn-valores_multiplos', function(event){
		var id_convenio= $(this).attr('data-id_convenio');
		var id_ato= $(this).attr('data-id_ato');
		
		var a= $(this).attr('data-a');
		var n= $(".valores_multiplos_pai_"+id_ato+"_"+id_convenio).length;
		//alert(n);
		
		var n2=n+1;
		var ultimo_k= parseInt($("#ato_"+a+" .ultimo_k").val());
		var ultimo_a= parseInt($(".ultimo_a").val());
		
		//alert(n);
		
		//alert(".valores_multiplos_"+id_ato+"_"+id_convenio+"_"+n2);
		
		$(".valores_multiplos_"+id_ato+"_"+id_convenio+"_"+n).after("<tr class='valores_multiplos_pai_"+id_ato+"_"+id_convenio+" valores_multiplos_"+id_ato+"_"+id_convenio+"_"+n2+" valor_multiplo'><td><input type=\"hidden\" name=\"id_convenio["+a+"]["+ultimo_k+"]\" value=\""+id_convenio+"\" /><input type=\"hidden\" name=\"atendo["+a+"]["+ultimo_k+"]\" value=\"1\" /></td><td>&nbsp;</td><td><div class=\"input-prepend\"><span class=\"add-on\">R$</span><input autocomplete=\"off\" type=\"text\" class=\"input-mini valor_campo\" name=\"valor["+a+"]["+ultimo_k+"]\" value=\"\" /><input type=\"hidden\" name=\"ordem["+a+"]["+ultimo_k+"]\" value=\""+n2+"\" /></div></td><td><div class=\"input-append\"><input autocomplete=\"off\" type=\"text\" class=\"input-mini percentual_campo\" name=\"percentual["+a+"]["+ultimo_k+"]\" value=\"\" /><span class=\"add-on\">%</span></div></td><td><a class='btn btn-mini btn-danger btn-remove-vm' rel='"+id_ato+"_"+id_convenio+"_"+n2+"' href='javascript:void(0);'>remover</a></td></tr>");
		
		ultimo_k++;
		$(".ultimo_k").val(ultimo_k);
		
		$(".valores_multiplos_"+id_ato+"_"+id_convenio+"_"+n2+" .valor_campo").focus();
		
		//ultimo_a++;
		//$(".ultimo_a").val(ultimo_a);
		
		return false;
	});
	
	$(document).on('click', '.btn_plantao', function(event){
		var existe=  $(this).attr('data-existe');
		var vale_dia= $(this).attr('data-vale_dia');
		var tipo_batida= $(this).attr('data-tipo_batida');
		var id_pcp= $(this).attr('data-id_pcp');
		var perfil= $(this).attr('data-perfil');
		 
		var passa= true;
		
		//saída
		if ((tipo_batida=="2") && (existe=="0")) {
			var entrou= $('.btn_entrar').attr('data-existe');
			
			//está tentando bater saída sem ter entrado
			if (entrou=='0') {
				passa= false;
				
				bootbox.alert("Você só pode sair, após ter entrado. <br><br><small>Dica: Se quiser ajustar, é possível editar os horários.</small>", function() { });
			}
		}
		
		if (passa) {
			//Já tem dados, batendo...
			if (existe=="1") {
				//alert("Já bateu!");
				
				
				$.ajax({ 
					cache: false,
			        data: {chamada: 'pegaHorarioPlantao', id_pcp: id_pcp, tipo_batida: tipo_batida },
			        type: 'get',
			        url: 'link.php',
			        timeout: 5000,
			        error: function(x, t, m) {
			        	//$('.cadastrar, .cancelar').button('reset').show();
			        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
			        	
				    },
			        success: function(retorno) {
			        
			        	$('#modal_plantao_hora #modal_plantao_hora_form').html(retorno);
						
						$('#modal_plantao_hora').modal({backdrop:'static'});
			        
			        }
			    });
			}
			//Ainda não tem dados, batendo...
			else {
				$.ajax({ 
					cache: false,
			        data: {chamada: 'entraSaiPlantao', tipo_batida: tipo_batida, vale_dia: vale_dia },
			        type: 'get',
			        url: 'link.php',
			        timeout: 5000,
			        error: function(x, t, m) {
			        	//$('.cadastrar, .cancelar').button('reset').show();
			        	//bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
			        	
				    },
			        success: function(retorno) {
			        	
			        	var parte= retorno.split('@|@');
			        	
			        	if (parte[0]=='0') {
							
							mixpanel.track("Entrou no plantão", {
					        	"Horário": ""+parte[3]+"",
					        	"id_pcp": ""+parte[4]+""
				        	});
							
							//alert(parte[1]);
							
							if (tipo_batida=="1") {
								
								//$(".btn_entrar").effect( 'shake');
								
								$(".btn_entrar span").html("Entrou");
								
								setTimeout(function() {
									$(".btn_entrar span").html(""+parte[2].substr(0, 5));
								}, 750);
								
								$(".btn_entrar").attr('data-existe', 1);
								$(".btn_entrar").attr('data-id_pcp', parte[4]);
								
								$(".btn_entrar").removeClass('btn-success');
								
								//2014-01-01 00:00:00
								var data_completa= parte[1] +' '+parte[2];
								
								var ano= data_completa.substr(0, 4)
								var mes= data_completa.substr(5, 2);
								var dia= data_completa.substr(8, 2);
								var hora= data_completa.substr(11, 2);
								var minuto= data_completa.substr(14, 2);
								var segundo= data_completa.substr(17, 2);
								
								var desde = new Date(ano, mes-1, dia, hora, minuto, segundo);
								
								$('.duracao .duracao_contador').countdown({
									since: desde,
									compact: true,
									format: 'HMS'
								});
								
								$('.duracao').show().css("display", "block");
							}
							else {
								//$(".btn_sair").effect( 'shake' );
								
								
								$(".btn_sair span").html("Saiu");
								
								setTimeout(function() {
									$(".btn_sair span").html(""+parte[2].substr(0, 5));
								}, 750);
								
								$(".btn_sair").attr('data-existe', 1);
								$(".btn_sair").attr('data-id_pcp', parte[4]);
								
								$(".btn_sair").removeClass('btn-success');
								
								var duracao= $('.duracao_contador span').html();
								
								bootbox.alert("<h4>Plantão encerrado.</h4><p><strong>Horas contabilizadas:</strong> "+ duracao +".</p>", function() {
									
									$("#modal_plantonista").modal({backdrop:'static'});
									
								});
								//$('.duracao').remove();
								
								$('.duracao .duracao_contador').countdown('destroy');
								
								$('.duracao_contador').html(duracao);
							}
							
							
							
						}
						else bootbox.alert(retorno, function() { });
			        
			        }
			    });
			 }
		}//fim passa		
	});
	
	$(document).on('submit', '#modal_plantao_hora_form', function(event){
		
		$.ajax({ // create an AJAX call...
	        data: $(this).serialize(), // get the form data
	        type: $(this).attr('method'), // GET or POST
	        url: $(this).attr('action'), // the file to call
	        success: function(retorno) { // on success..
	        	
	        	if (retorno[0]=='0') {
	        		
	        		var parte= retorno.split('@|@');
	        		
	        		//entrada
	        		if (parte[1]=="1")
	        			$('.btn_entrar span').html(parte[2]);
	        		else
	        			$('.btn_sair span').html(parte[2]);
	        		
	        		mixpanel.track("Editou horário");
	        		
	        		$('#modal_plantao_hora').modal('hide');
	        		
	        		setTimeout(function() {
			        	window.top.location.href='./?pagina=lancamento/lancamento&erros=0';
			        }, 300);
	        		
	        		
				}
	        }
		});
		
		return false;
	});
	
	$(document).on('submit', '#modal_convenio_edita_form', function(event){
		var id_ato= $(this).find('#vc_id_ato').val();
		var id_convenio= $(this).find('#vc_id_convenio').val();
		var ordem= $(this).find('#vc_ordem').val();
		var valor= $(this).find('#vc_valor').val();
		var percentual_clinica= $(this).find('#vc_percentual_clinica').val();
		var nome_exibicao_convenio= $(this).find('#vc_nome_exibicao_convenio').val();
		var label_convenio= $(this).find('#vc_label_convenio').val();
		
		$.ajax({
            cache: false,
            data: {chamada: 'alteraValorAtoConvenio', id_ato: id_ato, id_convenio: id_convenio, ordem: ordem, valor: valor, percentual_clinica: percentual_clinica, nome_exibicao_convenio: nome_exibicao_convenio, label_convenio: label_convenio },
            type: 'get',
            url: 'link.php',
            timeout: 5000,
            error: function(x, t, m) {
                 $('.cadastrar, .cancelar').button('reset').show();
                 bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
                
            },
            success: function(retorno) {
            	
            	if (retorno!='') {
					var parte= retorno.split('@|@');
					
					mixpanel.track("Atualiza convênio", {
			        	"Convênio": ""+nome_exibicao_convenio+"",
			        	"ID Convênio": id_convenio,
			        	"ID Ato": id_ato,
			        	"Valor": converteMoedaFloat(valor),
			        	"Percentual Clínica": converteMoedaFloat(percentual_clinica)
			        });
					
					//atualizando o label
					$('.vc_label_'+id_ato+'_'+id_convenio+'_'+ordem+' small').html('R$ '+parte[1]);
					$('.nome_convenio_'+id_ato+'_'+id_convenio+'_'+ordem+'').html(parte[2]);
					
					//atualizando o campo com valor que vale
					$('.vc_valor_2_'+id_ato+'_'+id_convenio+'_'+ordem+'').val(parte[0]);
					
					$('.vc_label_'+id_ato+'_'+id_convenio+'_'+ordem+' .badge-porcentagem').effect( 'highlight', '', 1400);
					
					$('.vc_label_label_'+id_ato+'_'+id_convenio+'_'+ordem+'').html(label_convenio);
					
					$('#modal_convenio_edita form button:submit').button('reset');
					
					$('#modal_convenio_edita').modal('hide');
					
					
					$('.alterar_convenio').attr('editando', '');
					$('.btn-exclui_convenio').fadeOut(200);
					$('.vc_link').fadeOut(200);
					//$('.novo_convenio').hide();
					$('strong.mr').css('marginLeft', '5px');
					
					$('.alterar_convenio').find('.lbl').html('editar');
					$('.alterar_convenio i').show();
					
					location.reload();
					
				}
				else {
					bootbox.alert(retorno, function() { });		
				}
            }
        });
		
		return false;
	});
	
	$(document).on('click', '.vc_link, .vc_link_atalho', function(event){
		var convenio= $(this).attr('data-convenio');
		var id_convenio= $(this).attr('data-id_convenio');
		var id_ato= $(this).attr('data-id_ato');
		var ordem= $(this).attr('data-ordem');
		
		$('#vc_valor').attr('placeholder', 'Carregando...');
		
		$('#vc_nome_exibicao_convenio').removeClass('ta');
		
		$('#vc_id_convenio').val(id_convenio);
		$('#vc_id_ato').val(id_ato);
		$('#vc_ordem').val(ordem);
		//$('#modal_convenio_edita_label').html(convenio);
		
		
		$.ajax({
            cache: false,
            data: {chamada: 'pegaValorAtoConvenio', id_ato: id_ato, id_convenio: id_convenio, ordem: ordem },
            type: 'get',
            url: 'link.php',
            timeout: 5000,
            error: function(x, t, m) {
                 $('.cadastrar, .cancelar').button('reset').show();
                 bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
                
             },
            success: function(retorno) {
            	var parte= retorno.split('@|@');
				
				$('#vc_valor').val(parte[0]);
				$('#vc_percentual_clinica').val(parte[1]);
				
				if (parte[2]!='') {
					$('#vc_nome_exibicao_convenio').val(parte[2]);	
				}
				else {
					$('#vc_nome_exibicao_convenio').val(parte[3]);	
				}
				
				setTimeout(function(){
					$('#vc_valor').focus();
					$('#vc_valor').select();
				}, 500);
            }
        });
		
	});
	
	$(document).on('click', '.novo_procedimento', function(event){
		
		//$('#nc_data').val($('#data').val());
		$('#nc_id_procedimento').val('');
		$('#nc_nome_exibicao_procedimento').val('');
		$('#nc_codigo_cbhpm').val('');
		
		$('#modal_procedimento_novo form .modal-footer button').button('reset').show();
		
		$('#modal_procedimento_novo').modal({backdrop:'static'})
		
		setTimeout(function(){ $('#nc_nome_exibicao_procedimento').focus(); }, 600);
	});
	
	$(document).on('click', '.novo_convenio', function(event){
		
		var id_ato= $(this).attr('data-id_ato');
		var a= $(this).attr('data-a');
		
		$('#nc_data').val($('#data').val());
		$('#nc_id_convenio').val('');
		$('#nc_nome_exibicao_convenio').val('');
		$('#nc_valor').val('');
		$('#nc_percentual_clinica').val('');
		$('#nc_nome_exibicao_convenio').removeAttr("disabled");	
	    $('.area_tipo_convenio label, .area_tipo_convenio select').show();
		
		$('#nc_id_ato').val(id_ato);
		$('#nc_a').val(a);
		
		$('#modal_convenio_novo form .cancelar').show();
		
		$('#modal_convenio_novo form .modal-footer button').button('reset');
		
		$('#modal_convenio_novo').modal({backdrop:'static'})
		
		setTimeout(function(){ $('#nc_nome_exibicao_convenio').focus(); }, 600);
	});
	
	$(document).on('click', '#silenciar_sons', function(event){
		var checado= $(this).is(':checked');
		
		if (checado) checado=1;
		else checado=0;
		
		$.ajax({
            cache: false,
            data: {chamada: 'silenciar', silenciar_sons: checado },
            type: 'get',
            url: 'link.php',
            timeout: 5000,
            error: function(x, t, m) {
                 $('.cadastrar, .cancelar').button('reset').show();
                 bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
                
             },
            success: function(retorno) {
            
            }
        });
		
	});
	
	$(document).on('click', '.atendo', function(event){
		
		var area= $(this).attr('rel');
		var id= $(this).attr('id');
		
		var checado= $('#'+id+'').is(':checked');
		
		$(area+" input.checkbox_atendo").each(function() {
			
			if (checado) $(this).attr('checked', true);
			else $(this).attr('checked', false);
		});
		
	});	
	
	function formata_valor(valor) {
		
		valor= valor.replace('.', '');
		valor= valor.replace(',', '.');
		
		return(valor);
	}
	
	$(".btn-detalhamento").popover({placement:'right'});
	
});

function atualiza_total_linha(linha, data, id_convenio, id_ato, ordem) {
	
	
	$.ajax({
        cache: false,
        data: {chamada: 'pegaTotalLinha', data: data, id_convenio: id_convenio, id_ato: id_ato, ordem: ordem },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             $('.cadastrar, .cancelar').button('reset').show();
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	
        	if (retorno=='0') retorno='';
		
			$("#lancamento_quantidade_"+linha).attr('data-valor', retorno);
			$("#lancamento_quantidade_"+linha).val(retorno);
        	
        }
    });
}

//atualiza resumo lateral
function atualiza_totais_acumulado() {
	
	$('.rr_acumulado').html('<img src="images/loading.gif" alt="" />');
	
	$.ajax({
        cache: false,
        data: {chamada: 'mostraTotalAcumulado' },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             $('.cadastrar, .cancelar').button('reset').show();
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        
        	//não deu erro...
			if (retorno[0]=='@') {
				var parte= retorno.split('@');
				
				//vai receber da unimed
				//$('.vai_receber_valor_pessoa').html(parte[2]);
				
				//vai receber da clinica ref. convenios guias
				//$('.producao_guias').html(parte[3]);
				
				//bruto
				//$('.por_direito_todos').html(parte[5]);
				
				//líquido
				$('.por_direito_valor_pessoa_acumulado').html(parte[4]);

				$('.por_direito_valor_pessoa_acumulado_atendimentos').html(parte[6]);
				
			}
			//deu algum erro
			else {
				bootbox.alert(retorno, function() { });
			}
        
        }
    });	
}

//atualiza resumo lateral
function atualiza_totais(data, id_clinica, edicao) {
	
	$('.rr').html('<img src="images/loading.gif" alt="" />');
	
	$.ajax({
        cache: false,
        data: {chamada: 'mostraDia', data: data, id_clinica: id_clinica, modo: 1 },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             $('.cadastrar, .cancelar').button('reset').show();
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        
        	//não deu erro...
			if (retorno[0]=='@') {
				var parte= retorno.split('@');
				
				//levar hoje
				$('.recebido_valor_pessoa').html(parte[1]);
				
				//vai receber da unimed
				//$('.vai_receber_valor_pessoa').html(parte[2]);
				
				//vai receber da clinica ref. convenios guias
				//$('.producao_guias').html(parte[3]);
				
				//bruto
				$('.por_direito_todos').html(parte[5]);
				
				//líquido
				$('.por_direito_valor_pessoa').html(parte[4]);
				
				if (edicao=='0') $("#resultado_fim div.well").effect( 'highlight', '', 1400);
				
				
			}
			//deu algum erro
			else {
				bootbox.alert(retorno, function() { });
			}
        
        }
    });	
}

//atualiza referente a cada categoria de convênio do dia
function atualiza_brutos(data, id_clinica, id_ato, t, edicao) {
	//alert(t);
	$('.bruto_'+id_ato+'_'+t+', .bruto_dia').html('<img src="images/loading.gif" alt="" />');
	
	$.ajax({
        cache: false,
        data: {chamada: 'mostraDiaConvenio', data: data, id_clinica: id_clinica, tipo_convenio: t, id_ato: id_ato },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             $('.cadastrar, .cancelar').button('reset').show();
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	
        	//não deu erro...
			if (retorno[0]=='@') {
				var parte= retorno.split('@');
				
				$('.bruto_'+id_ato+'_'+t).html(parte[1]);
				//$('.bruto_dia').html(parte[2]);
				$('.bruto_dia_qtde').html(parte[3]);
				$('.bruto_dia_qtde_consultas').html(parte[4]);
				$('.bruto_dia_qtde_retornos').html(parte[5]);
				
				$('.liquido_'+id_ato+'_'+t).html(parte[6]);
			}
			//deu algum erro
			else {
				bootbox.alert(retorno, function() { });
			}
        	
        }
    });
}

function atualiza(id_ato, i, t, modo) {
	//console.log(modo);
	var campo= $('.lancamento_quantidade_'+id_ato+'_'+i);
	var campo_valor= campo.attr('data-valor');
	
	campo.css('background', '#F5F5F5 url(images/loading.gif) no-repeat center');
	
	//faz isso só quando vem pelo botão +
	if (modo=='1') {
		if (campo_valor=='') campo_valor=1;
		else {
			var conta;
			campo_valor= parseInt(campo_valor)+1;
		}
		var campo_valor2= 1;
	}
	//blur no campo
	else if (modo=='2') {
		//modo='1';
		var campo_valor2= parseInt(campo_valor);;
	}
	
	var data= $('#data').val();
	var id_clinica= $('#id_clinica').val();
	var modo_recebimento_convenios_pagos= $('#modo_recebimento_convenios_pagos').val();
	var id_convenio= $('#id_convenio_'+id_ato+'_'+i).val();
	var tipo_convenio= $('#tipo_convenio_'+id_ato+'_'+i).val();
	var nome_convenio= $('#nome_convenio_'+id_ato+'_'+i).val();
	var recebimento= $('#recebimento_'+id_ato+'_'+i).val();
	var valor= $('#valor_'+id_ato+'_'+i).val();
	var ordem= $('#ordem_'+id_ato+'_'+i).val();
	var percentual_clinica= $('#percentual_clinica_'+id_ato+'_'+i).val();
	var percentual_medico= $('#percentual_medico_'+id_ato+'_'+i).val();
	var lancamento_quantidade= campo_valor2;
	var perfil= $('#perfil').val();
	
	$.ajax({
        cache: false,
        data: {chamada: 'atualiza', identifica_atendimentos: '1', modo: modo, data: data, modo_recebimento_convenios_pagos: modo_recebimento_convenios_pagos, id_ato: id_ato, id_convenio: id_convenio, tipo_convenio: tipo_convenio, recebimento: recebimento, valor: valor, ordem: ordem, percentual_clinica: percentual_clinica, percentual_medico: percentual_medico, lancamento_quantidade: lancamento_quantidade },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             
             campo.css('background', '#ffffff');
             
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	
        	campo.val('');
        	campo.attr('data-valor', campo_valor);
        	
        	campo.css('background', '#ffffff');
		
			//não deu erro...
			if (retorno[0]=='@') {
				
				//inserindo
				if ( (modo=='1') || (modo=='2') ) {
					
					var parte= retorno.split('@');
					
					var recebido_valor_pessoa=parte[1];
					var recebido_valor_clinica=parte[2];
					var vai_receber_valor_pessoa=parte[3];
					var vai_receber_valor_clinica=parte[4];
					var pessoa_deve=parte[5];
					var clinica_deve=parte[6];
					var por_direito_valor_pessoa=parte[7];
					var por_direito_valor_clinica=parte[8];
					var ultima_alteracao=parte[9];
					var tipo_atendimento=parte[10];
					
					$(".flexinha").hide();
					
					campo.val(campo_valor);
					//campo.attr("data-valor", campo_valor);
					
					$("#zera_"+id_ato+"_"+i).removeAttr('disabled');
					
					var tocou_som=false;
					
					var silenciar_sons= $('#silenciar_sons').is(':checked');
					
					//alert(silenciar_sons);
					if (!silenciar_sons) {
						
						//setTimeout( function() {
							$('#caixa_registradora')[0].play();
							tocou_som=true;
							mixpanel.track("Caixa registradora");
						//}, 300);
						
					}
					
					$("#linha_"+id_ato+"_"+i+" td").effect( 'highlight', '', 1400);
					$("#linha_"+id_ato+"_"+i+" .flexinha").fadeIn();
					
					//bootbox.alert(recebido_valor_pessoa, function() { });
					
					$("#linha_"+id_ato+"_"+i+" .ultima_alteracao").html(ultima_alteracao);
					$("#linha_"+id_ato+"_"+i+" .ultima_alteracao").fadeIn();
					
					var str=   "<small><strong>Médico</strong> - <em>"+percentual_medico+"%</em><br />"+
								"<strong>Levar em dinheiro:</strong> R$ "+recebido_valor_pessoa+"<br />"+
								"<strong>A receber da Unimed:</strong> R$ "+vai_receber_valor_pessoa+"<br />"+
		    					"<strong>Devendo para a clínica:</strong> R$ "+ pessoa_deve +"<br />"+
		    					"<strong>Líquido:</strong> R$ "+ por_direito_valor_pessoa +"<br /><br />"+
		    					
		    					"<strong>Clínica</strong> - <em>"+percentual_clinica+"%</em><br />"+
		    					"<strong>Recebeu em dinheiro:</strong> R$ "+ recebido_valor_clinica +"<br />"+
		    					"<strong>Receberá de convênio guia:</strong> R$ "+ vai_receber_valor_clinica +"<br />"+
		    					"<strong>Deve para o médico:</strong> R$ "+ clinica_deve +"<br />"+
		    					"<strong>Líquido:</strong> R$ "+ por_direito_valor_clinica +"<br /></small>";
					            					
					$("#detalhamento_"+id_ato+"_"+i).attr("data-content", str);
					
					$("#detalhamento_"+id_ato+"_"+i).popover('destroy');
					//$("#detalhamento_"+id_ato+"_"+i).popover();
					
					$("#detalhamento_"+id_ato+"_"+i).popover({placement:'left',content:str});
					
					if (tipo_atendimento=='1') tipo_atendimento_str='Consulta';
					else tipo_atendimento_str='Retorno';
					
					/*
					var modo_recebimento_convenios_pagos= $('#modo_recebimento_convenios_pagos').val();
					var id_convenio= $('#id_convenio_'+id_ato+'_'+i).val();
					var tipo_convenio= $('#tipo_convenio_'+id_ato+'_'+i).val();
					var recebimento= $('#recebimento_'+id_ato+'_'+i).val();
					var valor= $('#valor_'+id_ato+'_'+i).val();
					var ordem= $('#ordem_'+id_ato+'_'+i).val();
					var percentual_clinica= $('#percentual_clinica_'+id_ato+'_'+i).val();
					var percentual_medico= $('#percentual_medico_'+id_ato+'_'+i).val();
					var lancamento_quantidade= campo_valor;
					*/
					
					mixpanel.track("Novo atendimento", {
						
						"Recebido Valor Pessoa": converteMoedaFloat(recebido_valor_pessoa),
						"Recebido Valor Clínica": converteMoedaFloat(recebido_valor_clinica),
						"Vai Receber Valor Pessoa": converteMoedaFloat(vai_receber_valor_pessoa),
						"Vai Receber Valor Clínica": converteMoedaFloat(vai_receber_valor_clinica),
						"Pessoa Deve": converteMoedaFloat(pessoa_deve),
						"Clínica Deve": converteMoedaFloat(clinica_deve),
						"Líquido Pessoa": converteMoedaFloat(por_direito_valor_pessoa),
						"Líquido Clínica": converteMoedaFloat(por_direito_valor_clinica),
						"Tocou som": tocou_som,
						
						"Data": ""+data+"",
						"ID Clínica": ""+id_clinica+"",
						"Modo Recebimento Convênios Pagos": ""+modo_recebimento_convenios_pagos+"",
						"ID Convênio": id_convenio,
						"Nome convênio": ""+nome_convenio+"",
						"Tipo Convênio": ""+tipo_convenio+"",
						"Recebimento": ""+recebimento+"",
						"Valor": valor,
						"Ordem": ordem,
						"Percentual Clínica": percentual_clinica,
						"Percentual Médico": percentual_medico,
						"Quantidade": lancamento_quantidade,
						
						"Tipo de atendimento": ""+tipo_atendimento_str+""
					});
				}
				//zerando
				else {
					//$('#caixa_registradora')[0].play();
					campo.val('');
					campo.attr("data-valor", '');
					
					//$(".flexinha").hide();
					//$("#linha_"+id_ato+"_"+i+" .flexinha").fadeIn();
					
					$("#linha_"+id_ato+"_"+i+" td").effect( 'highlight', {color: '#FF7373'}, 1400);
					
					var str=   "<small><strong>Médico</strong> - <em>"+percentual_medico+"%</em><br />"+
								"<strong>Levar em dinheiro:</strong> R$ 0,00<br />"+
								"<strong>A receber da Unimed:</strong> R$ 0,00<br />"+
		    					"<strong>Devendo para a clínica:</strong> R$ 0,00<br />"+
		    					"<strong>Líquido:</strong> R$ 0,00<br /><br />"+
		    					
		    					"<strong>Clínica</strong> - <em>"+percentual_clinica+"%</em><br />"+
		    					"<strong>Recebeu em dinheiro:</strong> R$ 0,00<br />"+
		    					"<strong>Receberá de convênio guia:</strong> R$ 0,00<br />"+
		    					"<strong>Deve para o médico:</strong> R$ 0,00<br />"+
		    					"<strong>Líquido:</strong> R$ 0,00<br /></small>";
					/*
					var str=   "<small><strong>Médico levou:</strong> R$ 0,00<br />"+
		    					"<strong>Clínica recebe:</strong> R$ 0,00<br />"+
		    					
		    					"<strong>Médico deve:</strong> R$ 0,00<br />"+
		    					"<strong>Clínica deve:</strong> R$ 0,00<br />"+
		    					
		    					"<strong>Receita do médico:</strong> R$ 0,00<br />"+
		    					"<strong>Receita da clínica:</strong> R$ 0,00<br /></small>";
		    		*/
		    		
		    		
		    		
		    		$("#detalhamento_"+id_ato+"_"+i).attr("data-content", str);
					
					$("#detalhamento_"+id_ato+"_"+i).popover('destroy');
					//$("#detalhamento_"+id_ato+"_"+i).popover();
					
					$("#detalhamento_"+id_ato+"_"+i).popover({content:str});
					
					$("#linha_"+id_ato+"_"+i+" .ultima_alteracao").html('');
					
					mixpanel.track("Zera dia", {
						"Data": ""+data+"",
						"ID Clínica": ""+id_clinica+"",
						"Modo Recebimento Convênios Pagos": ""+modo_recebimento_convenios_pagos+"",
						"ID Convênio": id_convenio,
						"Nome convênio": ""+nome_convenio+"",
						"Tipo Convênio": ""+tipo_convenio+"",
						"Recebimento": ""+recebimento+"",
						"Valor": valor,
						"Ordem": ordem,
						"Percentual Clínica": percentual_clinica,
						"Percentual Médico": percentual_medico,
						"Quantidade": lancamento_quantidade
					});
				}
				
				atualiza_totais(data, id_clinica, '0');
				atualiza_brutos(data, id_clinica, id_ato, t, '0');
				
				//plantonistas em grupo
				if (perfil=='3') {
					atualiza_totais_acumulado();
				}
			}
			//deu algum erro
			else {
				campo.val(campo_valor);
				campo.attr("data-valor", campo_valor);
				
				bootbox.alert(retorno, function() { });
			}
        
        }
    });
}

$.fn.preload = function() {
    this.each(function(){
        $('<img/>')[0].src = this;
    });
}

$(['images/loading.gif', 'images/loading2.gif', 'uploads/caixa_registradora.mp3', 'uploads/caixa_registradora.ogg']).preload();

function addFormField(rotina, local){
	var next= parseInt($('#count').val());
	
	var newIn = '<div class="area area_'+next+' well"></div>';
	var newInput = $(newIn);
	$(local).append(newInput);
	
	carregaConteudo(rotina, '.area_'+next, next);
	//carregaConteudo('setupProcedimentos', 'procedimentos');
	
	next++;
	$("#count").val(next);
}

function carregaConteudo(rotina, id, num, id_pc) {
	$(id).html("<img src='images/loading.gif' alt='' />");
	
	$.ajax({
        cache: false,
        data: {chamada: rotina, id: id, num: num, id_pc: id_pc },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             $(id).html('');
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	$(id).html(retorno);
        }
    });
}

function diaLimpa(chamada, id, tipo) {
	
	$.ajax({
        cache: false,
        data: {chamada: chamada, id: id, tipo: tipo },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        
        	if (retorno=="0") {
				
				$("#linha_"+id).addClass("warning");
				$("#linha_"+id).fadeOut("fast");
				
			}
			else alert("Não foi possível excluir!"+ retorno);
        
        }
    });

}

function apagaLinha(chamada, id, tipo) {
	
	$.ajax({
        cache: false,
        data: {chamada: chamada, id: id, tipo: tipo },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	
        	if (retorno=="0") {
			
				$("#linha_"+id).addClass("warning");
				$("#linha_"+id).fadeOut("fast");
				
			}
			else {
				if (retorno=='r') window.top.location.href='./?pagina=acesso/trabalho_clinicas';
				else alert("Não foi possível excluir!"+ retorno);
			}
        	
        }
    });

}

function apagaLinhaDentro(chamada, id, tipo) {
	
	$.ajax({
        cache: false,
        data: {chamada: chamada, id: id, tipo: tipo },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        
        	if ( (retorno=="0") || (retorno=="r") ) {
				
				window.top.location.href='./?pagina=acesso/trabalho_clinicas&erros=0';
				
			}
			else {
				alert("Não foi possível excluir!"+ retorno);
			}
        }
    });
}

function apagaMinhaFoto() {
	
	$.ajax({
        cache: false,
        data: {chamada: "apagaMinhaFoto" },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	if (retorno!="0") alert("Não foi possível excluir a imagem!");
			else {
				$("#foto_area").html("Foto excluída.");
			}
        }
    });
}


function apagaArquivo(id_pessoa, src) {
	
	$.ajax({
        cache: false,
        data: {chamada: "arquivoExcluir", src: src, id_pessoa: id_pessoa },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	if (retorno!="0") alert("Não foi possível excluir o arquivo!");
			else {
				$("#foto_area").html("Foto excluída.");
			}
        }
    });
}

function situacaoLinha(chamada, id, status, tipo) {
	
	$.ajax({
        cache: false,
        data: {chamada: chamada, id: id, status: status, tipo: tipo },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
             bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
            
         },
        success: function(retorno) {
        	if (retorno=="0") {
				alert("Situação alterada com sucesso!");
				$("#situacao_link_"+id).attr("src", "images/ico_"+inverte_1_0(status)+".png");
			}
			else alert("Não foi possível alterar a situação!");
        }
    });
}

function formata_saida(valor, tamanho_saida) {
	valor+="";
	var tamanho= valor.length;
	var saida="";
	
	for (var i=tamanho; i<tamanho_saida; i++)
		saida+='0';
	
	return(saida+valor);
}

function pegaHorario() {
	
	var id_dia_atual= $("#id_dia_atual").val();
	var tipo_dia_atual= $("#tipo_dia_atual").val();
	var tipo_hora_atual= $("#tipo_hora_atual").val();
	
	$.ajax({
		cache: false,
	    data: {chamada: 'checaHorario' },
	    type: 'get',
	    url: 'link.php',
	    timeout: 20000,
	    error: function(x, t, m) {
	    	
	    },
	    success: function(retorno) {
	    	
	    	var parte= retorno.split('@|@');
	    	
	    	var id_dia= parte[0];
	    	var tipo_dia= parte[1];
	    	var tipo_hora= parte[2];
	    	
	    	$(".id_dia_atual").val(id_dia);
	    	$(".tipo_dia_atual").val(tipo_dia);
	    	$(".tipo_hora_atual").val(tipo_hora);
	    	
	    	if (tipo_hora=="2") {
				$('#topo').removeClass("navbar-inverse");
				$('.brand.interno img').attr("src", "images/cao3_dark.png");
				
				$('.linha_normal').hide();
				$('.linha_extra').show();
			}
			else {
				$('#topo').addClass("navbar-inverse");
				$('.brand.interno img').attr("src", "images/cao3.png");
				
				$('.linha_normal').show();
				$('.linha_extra').hide();
				
				
			}
			
	    }
	});
	
}

function checaRetorno(e) {
    //alert(e.keyCode);
    
    if (e.keyCode == 13) {
        //var tb = document.getElementById("scriptBox");
        //eval(tb.value);
        $('.cadastrar-paciente-vai').trigger('click');
        
        return false;
        
        
    }
}

function converteMoedaFloat(valor){
      
  if (valor==="") {
     valor=  0;
  }
  else{
     valor= valor.replace(".","");
     valor= valor.replace(",",".");
     valor= parseFloat(valor);
  }
  return valor;

}

function pad(number, length) {

    var str = '' + number;
    while (str.length < length) {str = '0' + str;}
    return str;
}

function formatTime(time) {

    time = time / 10;
    var min = parseInt(time / 6000),
        sec = parseInt(time / 100) - (min * 60),
        hundredths = pad(time - (sec * 100) - (min * 6000), 2);
    return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ":" + hundredths;

}