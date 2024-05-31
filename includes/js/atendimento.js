
$.fn.modal.Constructor.prototype.enforceFocus = function () {};

var msg_erro_padrao= "<h5>Ops :-(</h5> <br> Pequeno problema... vamos tentar de novo? <br/><br/>";
var msg_erro_padrao_rodape= "<small><strong>Código de erro:</strong> 387483-24932034-34234-123-431-2-1 <br><br><small>Favor informar este erro ao suporte avançado.</small></small>";

function abreJanelaPaciente(id_paciente, origem) {
	//se estiver vindo do Histórico do paciente
	if (origem=='1')
		$('#modal_pacientes').modal('hide');
    else
    	$('#modal_atendimentos').modal('hide');
    
	$.ajax({ 
		cache: false,
        data: {chamada: 'pegaAtendimentos', id_paciente: id_paciente },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
        	//$('.cadastrar, .cancelar').button('reset').show();
        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
        	
	    },
        success: function(retorno) {
        	
        	$('.edita_paciente').show();
        	
        	$('#historico_conteudo').html(retorno);
			
			$('#modal_historico').modal({backdrop:'static'});
			
			$('#modal_historico .cancelar').show();
        
        }
    });
}

function abreJanelaPaciente_param(id_paciente, origem, id_ato, i, t) {
	//se estiver vindo do Histórico do paciente
	if (origem=='1')
		$('#modal_pacientes').modal('hide');
    else
    	$('#modal_atendimentos').modal('hide');
    
	$.ajax({ 
		cache: false,
        data: {chamada: 'pegaAtendimentos', id_paciente: id_paciente, id_ato: id_ato, i: i, t: t },
        type: 'get',
        url: 'link.php',
        timeout: 5000,
        error: function(x, t, m) {
        	//$('.cadastrar, .cancelar').button('reset').show();
        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
        	
	    },
        success: function(retorno) {
        	
        	$('.edita_paciente').show();
        	
        	$('#historico_conteudo').html(retorno);
			
			$('#modal_historico').modal({backdrop:'static'});
			
			$('#modal_historico .cancelar').show();
        
        }
    });
}

$(document).ready(function() {
	
	var id_modulo= $("#id_modulo").val();
	//alert(id_modulo);
	
	/*$(".modal").each(function(i) {
        $(this).draggable({
            handle: ".modal-header"  
        });
    });*/
	
	$("#paciente_data_nasc, #data_nasc").inputmask("99/99/9999");
	$("#paciente_cpf, #cpf").inputmask("999.999.999-99");
	//$("#paciente_telefone").inputmask("(99) 9999-9999");
	//$("#paciente_telefone2").inputmask("(99) 9999-9999");
	
	var pacientes = new Bloodhound({
	    datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.nome); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: 'link.php?chamada=pesquisaPaciente&query=',
			
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
	
	//contador + nome
	if (id_modulo=="2") {
		classe_pop= 'cadastrar-paciente-vai';
		msg_pop= 'Lançar &raquo;';
	}
	else {
		classe_pop= 'cadastrar-paciente';
		msg_pop= 'Completar dados e atender &raquo;';
	}
	
	pacientes.initialize();
	
	$('#nome_paciente').typeahead({
		hint: true,
		highlight: true,
		minLength: 3,
	},
	{
	    name: 'atendimento_paciente_id',
	    displayKey: 'nome',
	    source: pacientes.ttAdapter(),
	    templates: {
			empty: [
			'<div class="tt-suggestion '+classe_pop+'">',
				''+msg_pop+'',
			'</div>'
			].join('\n'),
			suggestion: Handlebars.compile([
		      '<div class="paciente_nome">{{nome}}</div>',
		      '<div class="paciente_data_nasc">{{data_nasc}} &nbsp; <span class="muted">{{idade}}</span> </div>'
		    ].join(''))
		}
	}).on('typeahead:selected', function(event, datum) {
	    if (datum!=undefined) {
	    	if (id_modulo=="2") {
	    		//alert('opa! :)');
	    		
	    		$('#modal_atendimento #atendimento_paciente_id').val(datum.id);
				$('#modal_atendimento #paciente_atendimento_id').val(datum.id);
				
				$('#modal_atendimento #tipo_atendimento').val('1');
				
				$('#modal_atendimento_form').submit();
	    	}
	    	else {
	    		iniciaAtendimento(datum.id);
	    	}
	    }
	});
	
	var pacientes_pesquisa = new Bloodhound({
	    datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.nome); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: 'link.php?chamada=pesquisaPaciente&query=',
			
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
	
	pacientes_pesquisa.initialize();
	
	$('#nome_paciente_pesquisa').typeahead({
		hint: true,
		highlight: true,
		minLength: 3,
	},
	{
	    name: 'atendimento_paciente_id_pesquisa',
	    displayKey: 'nome',
	    source: pacientes_pesquisa.ttAdapter(),
	    templates: {
			empty: [
			'<div class="tt-suggestion cadastrar-paciente-atalho">',
				'Cadastrar este paciente &raquo;',
			'</div>'
			].join('\n'),
			suggestion: Handlebars.compile([
		      '<div class="paciente_nome">{{nome}}</div>',
		      '<div class="paciente_data_nasc">{{data_nasc}} &nbsp; <span class="muted">{{idade}}</span> </div>'
		    ].join(''))
		}
	}).on('typeahead:selected', function(event, datum) {
	    if (datum!=undefined) {
	    	
	    	abreJanelaPaciente(datum.id, 1);
	    }
	});
	
	$(document).on('click', '.btn_m_pac', function(event){
		setTimeout( function() { $('#modal_pacientes .nome_paciente').blur().focus(); }, 800);
	});
		
	$(document).on('click', '.transfere_paciente', function(event){
		var id_paciente= $(this).attr('data-id_paciente');
		var linha= $(this).attr('data-linha');
		var id= $(this).attr('data-id');
		var id_ato= $(this).attr('data-id_ato');
		var ordem= $(this).attr('data-ordem');
		var i= $(this).attr('data-i');
		var t= $(this).attr('data-t');
		
		abreJanelaPaciente_param(id_paciente, 2, id_ato, i, t);
	});
	
	$(document).on('click', '.link_paciente', function(event){
		
		$('#modal_pacientes .modal-footer button').button('reset').show();
		
		setTimeout( function() { $('#nome_paciente_pesquisa').blur().focus(); }, 600);
	});
	
	
	$(document).on('click', '.mostra_modal_historico', function(event){
		$('#modal_historico').modal({backdrop:'static'});
		
		$(this).removeClass('mostra_modal_historico');
	});
	
	$(document).on('click', '.edita_paciente', function(event){
		
		var id_paciente= $(this).attr('rel');
		
		$.ajax({ 
			cache: false,
	        data: {chamada: 'pegaDadosPaciente', id: id_paciente },
	        type: 'get',
	        url: 'link.php',
	        timeout: 5000,
	        error: function(x, t, m) {
	        	//$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno!='0') {
					
					$('#modal_paciente_form .modal-footer button').button('reset').show();
					
					$('#modal_paciente_form .cancelar, #modal_paciente_form .close').addClass('mostra_modal_historico');
					
					$('#modal_paciente_form #edita_id_paciente').val(id_paciente);
					$('#modal_paciente_form #acao').val('e');
					
					var parte_paciente= retorno.split('@|@');
					
					$('#modal_paciente_form #paciente_nome').val(parte_paciente[1]);
					$('#modal_paciente_form #paciente_data_nasc').val(parte_paciente[2]);
					
					if (parte_paciente[4]=='m') $('#modal_paciente_form #paciente_sexo_m').prop('checked', true);
					else if (parte_paciente[4]=='f') $('#modal_paciente_form #paciente_sexo_f').prop('checked', true);
					
					$('#modal_paciente_form #paciente_cpf').val(parte_paciente[5]);
					$('#modal_paciente_form #paciente_telefone').val(parte_paciente[6]);
					$('#modal_paciente_form #paciente_telefone2').val(parte_paciente[7]);
					$('#modal_paciente_form #paciente_email').val(parte_paciente[8]);
					
					$('#modal_paciente_form .modal-header h5').html('Editar paciente');
					//$('#modal_paciente_form .cadastrar').html('Editar');
					
					//$('.span_typeahead').hide();
					$('#modal_historico').modal('hide');
					$('#modal_paciente').modal({backdrop:'static'});
					$('#modal_atendimento').css('z-index', '1030');
				}
	        	
	        }
	    });
		
	});
	
	$(document).on('click', '.edita_atendimento', function(event){
		
		var id= $(this).attr('data-id');
		var linha= $(this).attr('data-linha');
		var t= $(this).attr('data-t');
		var i= $(this).attr('data-i');
		var id_paciente_botao=  $(this).attr('data-id_paciente');
		
		$.ajax({ 
			cache: false,
	        data: {chamada: 'pegaAtendimento', id: id },
	        type: 'get',
	        url: 'link.php',
	        timeout: 5000,
	        error: function(x, t, m) {
	        	//$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno!='') {
	        		$('#modal_atendimento .span_typeahead').hide();
					$('#atendimento_paciente').show();
					$('#modal_atendimento').modal({backdrop:'static'});
					
					if (id_paciente_botao!='') {
						$('#modal_historico').modal('hide');
					}
					else {
						$('#modal_atendimentos').modal('hide');
					}
					
					var parte= retorno.split('@|@');
					
					var tipo_atendimento= parte[0];
					var id_paciente= parte[1];
					var id_ato= parte[2];
					var id_convenio= parte[3];
					var tipo_convenio= parte[4];
					var ordem= parte[5];
					var recebimento= parte[6];
					var data= parte[7];
					var hora= parte[8];
					var valor_unitario= parte[9];
					var valor_total= parte[10];
					var recebido_valor_pessoa= parte[11];
					var recebido_valor_clinica= parte[12];
					var vai_receber_valor_pessoa= parte[13];
					var vai_receber_valor_clinica= parte[14];
					var pessoa_deve= parte[15];
					var clinica_deve= parte[16];
					var por_direito_valor_pessoa= parte[17];
					var por_direito_valor_clinica= parte[18];
					var percentual_clinica= parte[19];
					var percentual_medico= parte[20];
					var modo_recebimento_convenios_pagos= parte[21];
					var anamnese= parte[22];
					var id_bd= parte[23];
					var nome_ato= parte[24];
					var nome_convenio= parte[25];
					var valor_formatado= parte[26];
					var editado_infos= parte[27];
					
					$.ajax({ 
						cache: false,
				        data: {chamada: 'pegaDadosPaciente', id: id_paciente },
				        type: 'get',
				        url: 'link.php',
				        timeout: 5000,
				        error: function(x, t, m) {
				        	//$('.cadastrar, .cancelar').button('reset').show();
				        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
				        	
					    },
				        success: function(retorno) {
							
							if (retorno!='0') {
								var parte_paciente= retorno.split('@|@');
								
								$('.ver_historico').attr('rel', parte_paciente[0]);
								//$('#atendimento_paciente_id').val(parte_paciente[0]);
								$('#atendimento_paciente_nome').html(parte_paciente[1]);
								
								if (parte_paciente[2]!='00/00/0000') {
									$('#atendimento_paciente_data_nasc').html(parte_paciente[2] +' <br /><em>'+parte_paciente[3]+' anos</em>');
								}
								else
									$('#atendimento_paciente_data_nasc').html('');
								
								$('#modal_atendimento_form .cadastrar, #modal_atendimento_form .cancelar').button('reset').show();
								
								
								
								$('#modal_atendimento #atendimento_paciente_id').val(id_paciente);
								$('#modal_atendimento #paciente_atendimento_id').val(id_bd);
								$('#modal_atendimento #edicao').val('1');
								$('#modal_atendimento #paciente_data').val(data);
								$('#modal_atendimento #paciente_hora').val(hora);
								$('#modal_atendimento #id_convenio').val(id_convenio);
								$('#modal_atendimento #campo_i').val(i);
								$('#modal_atendimento #campo_t').val(t);
								
								//está vindo da tela de listagem de consultas de um paciente específico
								if (id_paciente_botao!='') $('#modal_atendimento #origem').val('1');
								else $('#modal_atendimento #origem').val('2');
								
								$('#modal_atendimento #id_ato').val(id_ato);
								$('#modal_atendimento .novo_ato').html(nome_ato);
								
								$('#modal_atendimento #nome_convenio').val(nome_convenio);
								$('#modal_atendimento .novo_nome_convenio').html(nome_convenio);
								$('#modal_atendimento .novo_valor_convenio').html(valor_formatado);
								
								$('#modal_atendimento #tipo_convenio').val(tipo_convenio);
								$('#modal_atendimento #paciente_modo_recebimento_convenios_pagos').val(modo_recebimento_convenios_pagos);
								$('#modal_atendimento #recebimento').val(recebimento);
								$('#modal_atendimento #valor').val(valor_unitario);
								$('#modal_atendimento #ordem').val(ordem);
								$('#modal_atendimento #percentual_clinica').val(percentual_clinica);
								$('#modal_atendimento #percentual_medico').val(percentual_medico);
								
								$('#modal_atendimento #anamnese').val(anamnese);
								$('#modal_atendimento #editado_infos').val(editado_infos);
										
								$('#modal_atendimento .cadastrar').show();		
								
								//sempre setar consulta como padrão, se for retorno... MUDA!
								$('#modal_atendimento #tipo_atendimento').val(tipo_atendimento);
								$('.btn-mini-tipo_atendimento').removeClass('active');
								$('.btn-mini-tipo_atendimento-'+tipo_atendimento).addClass('active');
								
								if (id_ato=='1') {
									$('.div_tipo_atendimento').show();
								}
								else {
									$('.div_tipo_atendimento').hide();
									//quando não é consulta, sempre considerar atendimento pago, pois não existe retorno de procedimento, é sempre retorno de uma consulta
									//$('#tipo_atendimento').val('1');
								}
								
								setTimeout( function() { $('#anamnese').blur().focus(); }, 600);
								
							} else alert('Não foi possível buscar os dados do paciente.');
							
						}
					});
					
				}
				else bootbox.alert("Impossível carregar, me desculpe!", function() { });
	        }
	        
	    });
		
	});
	$(document).on('click', '.apaga_atendimento', function(event){
		
		var id= $(this).attr('data-id');
		var id_ato= $(this).attr('data-id_ato');
		var id_convenio= $(this).attr('data-id_convenio');
		var ordem= $(this).attr('data-ordem');
		var i= $(this).attr('data-i');
		var t= $(this).attr('data-t');
		var linha= $(this).attr('data-linha');
		var data= $('#data').val();
		var id_clinica= $('#id_clinica').val();
		var perfil= $('#perfil').val();
		
		var resposta= confirm('Excluir este atendimento?');
		//bootbox.confirm('Remover este atendimento?', function(resposta) {
			if (resposta) {
				
				$.ajax({ 
					cache: false,
			        data: {chamada: 'apagaAtendimento', id: id },
			        type: 'get',
			        url: 'link.php',
			        timeout: 5000,
			        error: function(x, t, m) {
			        	//$('.cadastrar, .cancelar').button('reset').show();
			        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
			        	
				    },
			        success: function(retorno) {
			        	
			        	//apagou sem erros
						if (retorno=='0') {
							
							$('#modal_atendimentos, #modal_historico').modal('hide');
							
							$("#linha_"+linha+" td").effect( 'highlight', {color: '#FF7373'}, 600);
							$("#linha_"+linha+" .flexinha").fadeIn();	
							
							atualiza_total_linha(linha, data, id_convenio, id_ato, ordem);
							
							atualiza_totais(data, id_clinica, '0');
							atualiza_brutos(data, id_clinica, id_ato, t, '0');	
							
							//plantonistas em grupo
							if (perfil=='3') {
								atualiza_totais_acumulado();
							}
						}
						else bootbox.alert(retorno, function() { });
			        	
			        }
			    });
				
			} //else $(this).blur();
		//});
	});
	
	$(document).on('submit', '#modal_paciente_form', function(event){
		
		var acao= $('#acao').val();
		var id_paciente= $('#edita_id_paciente').val();
		var cadastro_rapido= $('#cadastro_rapido').val();
		
		var paciente_nome= $('#paciente_nome').val();
		
		$.ajax({ // create an AJAX call...
	        cache: false,
	        data: $(this).serialize(), // get the form data
	        type: $(this).attr('method'), // GET or POST
	        url: $(this).attr('action'), // the file to call
	        timeout: 5000,
	        error: function(x, t, m) {
	        	$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) { // on success..
	        	
	        	if (acao=='i') {
		        	if (retorno[0]=='N') {
			        	alert('Nome obrigatório.');
		        	}
		        	else {
		        		
		        		//var campos_form= $(this).serializeArray();
		        			        		
	        			mixpanel.track("Cadastrou paciente", {
		        			"Paciente nome": ""+paciente_nome+""
						});
		        		
		        		$('#modal_paciente').modal('hide');
		        		$('#modal_paciente_form button').button('reset');
		        		
		        		$('#modal_paciente_form .cadastrar').show();
						$('#modal_paciente_form .cancelar').show();
							
			        	if (cadastro_rapido=='1') {
				        	$('#cadastro_rapido').val('0');
				        	
				        	abreJanelaPaciente(retorno, 1);
			        	}
			        	else {
				        	$('#modal_atendimento').modal({backdrop:'static'});
							
							//aqui o retorno é o id cadastrado
							iniciaAtendimento(retorno);
						}
					}
				}
				else {
					
					if (retorno=='0') {
						$('#modal_paciente').modal('hide');
										
	        			mixpanel.track("Editou paciente", {
		        			"Paciente nome": ""+paciente_nome+"",
		        			"Paciente ID": id_paciente
						});
					
						
						setTimeout(function() {
		        	
				        	$('#modal_paciente_form #paciente_nome').val('');
							$('#modal_paciente_form #paciente_data_nasc').val('');
							
							$('#modal_paciente_form input[type=radio]').prop('checked', false);
							
							$('#modal_paciente_form #paciente_cpf').val('');
							$('#modal_paciente_form #paciente_telefone').val('');
							$('#modal_paciente_form #paciente_telefone2').val('');
							$('#modal_paciente_form #paciente_email').val('');
				        	
			        	} , 1000);
						
						$.ajax({ 
							cache: false,
					        data: {chamada: 'pegaAtendimentos', id_paciente: id_paciente },
					        type: 'get',
					        url: 'link.php',
					        timeout: 5000,
					        error: function(x, t, m) {
					        	$('.cadastrar, .cancelar').button('reset').show();
					        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
					        	
						    },
					        success: function(retorno) {
					        	
					        	$('#modal_paciente_form .cadastrar, #modal_paciente_form .cancelar').button('reset');
					        	
					        	$('#historico_conteudo').html(retorno);
								
								$('#modal_historico').modal({backdrop:'static'});
								
								$('#modal_historico .cancelar').show();
					        }
					    });
						
					}
					else alert('Não foi possível editar!');
					
				}
	        }
		});
		
		return(false);
	});
	
	function iniciaAtendimento(id_pessoa) {
		
		if (id_pessoa!='0') {
			
			$.ajax({ 
				cache: false,
		        data: {chamada: 'pegaDadosPaciente', id: id_pessoa },
		        type: 'get',
		        url: 'link.php',
		        timeout: 5000,
		        error: function(x, t, m) {
		        	//$('.cadastrar, .cancelar').button('reset').show();
		        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
		        	
			    },
		        success: function(retorno) {
		        	
		        	if (retorno!='0') {
		        		
		        		$('#modal_atendimento .span_typeahead').hide();
						$('#atendimento_paciente').fadeIn('fast');
						
						$('#anamnese').val('');
		        		
						var parte= retorno.split('@|@');
						
						$('.ver_historico').show();
						$('.ver_historico').attr('rel', parte[0]);
						$('#atendimento_paciente_id').val(parte[0]);
						$('#atendimento_paciente_nome').html('<strong>'+parte[1]+'</strong>');
						
						if (parte[2]!='00/00/0000')
							$('#atendimento_paciente_data_nasc').html('<em>'+parte[3]+' anos</em>');
						else
							$('#atendimento_paciente_data_nasc').html('');
						
						$('#modal_atendimento_form .modal-footer button').show().button('reset');
						
						//setTimeout(function() { $('#anamnese').focus(); } , 500);
	
					}
		        	
		        }
		    });
		}
		else {
			$('.ver_historico').hide();
			$('#nome_paciente').val('');
			$('#modal_atendimento .span_typeahead').show();
			$('#atendimento_paciente').hide();
			
			$('#modal_atendimento_form .cadastrar').hide();
			$('#modal_atendimento_form .cancelar').show();
			
			$('.ver_historico').attr('rel', '');
			$('#atendimento_paciente_id').val('');
			
			$('#atendimento_paciente_nome').html('');
			$('#atendimento_paciente_data_nasc').html('');
			
			$('#tipo_atendimento').val('');
			$('.btn-mini-tipo_atendimento').removeClass('active');
		}
	}
	
	$(document).on('submit', '#modal_atendimento_form', function(event){
		
		var edicao= $('#modal_atendimento_form #edicao').val();
		var origem= $('#modal_atendimento_form #origem').val();
		var tipo_atendimento_pre= $('#modal_atendimento_form #tipo_atendimento').val();
		var perfil= $('#perfil').val();
		
		if (tipo_atendimento_pre=='') {
			$('.cadastrar, .cancelar').button('reset').show();
			
			bootbox.alert('Informe se é consulta ou retorno.', function() { });
		}
		else {
			$.ajax({
		     	cache: false,
		        data: $(this).serialize(), 
		        type: $(this).attr('method'), 
		        url: $(this).attr('action'), 
		        timeout: 5000,
		        error: function(x, t, m) {
		        	$('.cadastrar, .cancelar').button('reset').show();
		        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
			    },
		        success: function(retorno) {
		            
		            $('.ver_historico').hide();
		            
		            //alert(retorno);
		            var id_paciente= $('#modal_atendimento_form #atendimento_paciente_id').val();
		            var paciente_data= $('#modal_atendimento_form #paciente_data').val();
		            var campo_i= $('#modal_atendimento_form #campo_i').val();
		            var campo_t= $('#modal_atendimento_form #campo_t').val();
					var id_convenio= $('#modal_atendimento_form #id_convenio').val();
					var id_ato= $('#modal_atendimento_form #id_ato').val();
					var ordem= $('#modal_atendimento_form #ordem').val();
					var id_clinica= $('#modal_atendimento_form #paciente_id_clinica').val();
		            var modo= '1';
		            
		            var campo= $('.lancamento_quantidade_'+id_ato+'_'+campo_i);
					var campo_valor= campo.attr('data-valor');
					
					//---
					
					var modo_recebimento_convenios_pagos= $('#modal_atendimento_form #paciente_modo_recebimento_convenios_pagos').val();
					
					var tipo_convenio= $('#modal_atendimento_form #tipo_convenio').val();
					var nome_convenio= $('#modal_atendimento_form #nome_convenio').val();
					var recebimento= $('#modal_atendimento_form #recebimento').val();
					var valor= $('#modal_atendimento_form #valor').val();
					
					var percentual_clinica= $('#modal_atendimento_form percentual_clinica').val();
					var percentual_medico= $('#modal_atendimento_form percentual_medico').val();
					var lancamento_quantidade= '1';
					
					if (tipo_atendimento_pre=='1') tipo_atendimento_str='Consulta';
					else tipo_atendimento_str='Retorno';
					
					//faz isso só quando vem pelo botão + e se for consulta (não retorno)
					if (modo=='1') {
						if (campo_valor=='') campo_valor=1;
						else {
							var conta;
							campo_valor= parseInt(campo_valor)+1;
						}
					}
					else if (modo=='2') modo='1';
		            
		            //não deu erro...
					if (retorno[0]=='@') {
						
						//inserindo
						if (modo=='1') {
							
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
							var tipo_atendimento= parte[10];
							
							$(".flexinha").hide();
							
							$("#zera_"+id_ato+"_"+campo_i).removeAttr('disabled');
							
							//faz isso só se for consulta
							if (tipo_atendimento=='1') {
								
								
								if (edicao!='1') {
									campo.val('');
					
									campo.css('background', '#fff url(images/loading.gif) no-repeat center');
									
									campo.css('background', '#EAEDED');
									
									campo.val(campo_valor);
									campo.attr('data-valor', campo_valor);
									
									var tocou_som=false;
									
									var silenciar_sons= $('#silenciar_sons').is(':checked');
									if (!silenciar_sons) {
										$('#caixa_registradora')[0].play();
										tocou_som=true;
									}
									
									$("#linha_"+id_ato+"_"+campo_i+" td").effect( 'highlight', '', 800);
								}
								valor2= valor;
							}
							else valor2=0;
							
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
									
									"Data": ""+paciente_data+"",
									"ID Clínica": ""+id_clinica+"",
									"Modo Recebimento Convênios Pagos": ""+modo_recebimento_convenios_pagos+"",
									"ID Convênio": id_convenio,
									"Nome Convênio": ""+nome_convenio+"",
									"Tipo Convênio": ""+tipo_convenio+"",
									"Recebimento": ""+recebimento+"",
									"Valor": valor2,
									"Ordem": ordem,
									"Percentual Clínica": percentual_clinica,
									"Percentual Médico": percentual_medico,
									"Quantidade": lancamento_quantidade,
									
									"Tipo de atendimento": ""+tipo_atendimento_str+""
								});
							
							if (edicao=='1') {	
								//$('#modal_atendimentos').modal('hide');
								
								//se editou a consulta, e veio da tela de listagem de consulta de um paciente, volta...
								if (origem=='1') {
								//$('#modal_historico').modal({backdrop:'static'});
								
									$.ajax({ 
										cache: false,
								        data: {chamada: 'pegaAtendimentos', id_paciente: id_paciente },
								        type: 'get',
								        url: 'link.php',
								        timeout: 5000,
								        error: function(x, t, m) {
								        	//$('.cadastrar, .cancelar').button('reset').show();
								        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
								        	
									    },
								        success: function(retorno) {
								        	
								        	$('#historico_conteudo').html(retorno);
											
											$('#modal_historico').modal({backdrop:'static'});
											
											$('#modal_historico .cancelar').show();
								        	
								        }
								    });
								}
								//se veio da tela de listagem de atendimentos por dia/convenio e tals... volta para lá.
								else {
									
									$.ajax({ 
										cache: false,
								        data: {chamada: 'pegaAtendimentos', data: paciente_data, id_ato: id_ato, id_convenio: id_convenio, ordem: ordem, t: campo_t, i: campo_i },
								        type: 'get',
								        url: 'link.php',
								        timeout: 5000,
								        error: function(x, t, m) {
								        	//$('.cadastrar, .cancelar').button('reset').show();
								        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
								        	
									    },
								        success: function(retorno) {
								        
								        	$('#modal_atendimentos #atendimentos').html(retorno);
											
											$('#modal_atendimentos').modal({backdrop:'static'});
											
											$('#modal_atendimentos .cancelar').show();
								        
								        }
								    });
								
								}
							}
							
							$("#linha_"+id_ato+"_"+campo_i+" .flexinha").fadeIn();
							
							//bootbox.alert(recebido_valor_pessoa, function() { });
							
							$("#linha_"+id_ato+"_"+campo_i+" .ultima_alteracao").html(ultima_alteracao);
							$("#linha_"+id_ato+"_"+campo_i+" .ultima_alteracao").fadeIn();
							
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
							            					
							$("#detalhamento_"+id_ato+"_"+campo_i).attr("data-content", str);
							
							$("#detalhamento_"+id_ato+"_"+campo_i).popover('destroy');
							//$("#detalhamento_"+id_ato+"_"+i).popover();
							
							$("#detalhamento_"+id_ato+"_"+campo_i).popover({placement:'left',content:str});
							
							$('#modal_historico').modal('hide');
							$('#modal_atendimento').modal('hide');
							
							setTimeout(function(){ $('#link_ato_1_1').trigger( "click" ); }, 1600);
							
							iniciaAtendimento('0');
							
						}
						//zerando
						/*else {
							//$('#caixa_registradora')[0].play();
							campo.val('');
							campo.attr("data-valor", '');
							
							//$(".flexinha").hide();
							//$("#linha_"+id_ato+"_"+i+" .flexinha").fadeIn();
							
							$("#linha_"+id_ato+"_"+i+" td").effect( 'highlight', {color: '#FF7373'}, 600);
							
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
				    		
				    		$("#detalhamento_"+id_ato+"_"+i).attr("data-content", str);
							
							$("#detalhamento_"+id_ato+"_"+i).popover('destroy');
							//$("#detalhamento_"+id_ato+"_"+i).popover();
							
							$("#detalhamento_"+id_ato+"_"+i).popover({content:str});
							
							$("#linha_"+id_ato+"_"+i+" .ultima_alteracao").html('');
						}*/
						
						atualiza_totais(paciente_data, id_clinica, edicao);
						atualiza_brutos(paciente_data, id_clinica, id_ato, campo_t, edicao);
						
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
	    return false; // cancel original event to prevent form submitting
		
	});
	
	$(document).on('click', '.ver_historico', function(event){
		
		var rel= $(this).attr('rel');
		
		$.ajax({ 
			cache: false,
	        data: {chamada: 'pegaAtendimentos', id_paciente: rel },
	        type: 'get',
	        url: 'link.php',
	        timeout: 5000,
	        error: function(x, t, m) {
	        	//$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	$('#historico_conteudo').html(retorno);
				
				$('#modal_historico .cancelar').show();
				
				
				$('#modal_atendimento').css('left', '30%');
				
				$('#modal_historico').addClass('posso_voltar');
				
				$('#modal_historico').modal({backdrop:'static'});
	        	
	        	//$('.edita_paciente').hide();
			}
		});
	});
	
	$(document).on('click', '.cadastrar-paciente-atalho', function(event){
		
		var digitado= $("#modal_pacientes .span_typeahead pre").html();
		
		$('#modal_pacientes').modal('hide');
		
		//seta cadastro_rapido 1 para depois de submeter o cadastro, dar um aviso
		$('#cadastro_rapido').val('1');
		
		$('#edita_id_paciente').val('');
		$('#modal_paciente #acao').val('i');
		$('#paciente_nome').val('');
		$('#paciente_data_nasc').val('');
		$('#paciente_cpf').val('');
		$('#paciente_sexo_m').prop('checked', true);
		$('#paciente_sexo_f').prop('checked', false);
		$('#paciente_telefone').val('');
		$('#paciente_telefone2').val('');
		$('#paciente_email').val('');
		
		$('#modal_paciente_form .modal-header h5').html('Cadastrar paciente');
		
	    $('#modal_paciente').modal({backdrop:'static'});
	    $('#modal_paciente_form .cadastrar, #modal_paciente_form .cancelar').button('reset').show();
	    
	    $('#modal_paciente_form .cadastrar').html('Cadastrar');
	    
	    //setTimeout(function() { $('#paciente_nome').val(digitado); } , 500);
	    setTimeout(function() { $('#paciente_nome').blur().focus().val(digitado);  } , 600);
	});
	
	$(document).on('click', '.cadastrar-paciente-vai', function(event){
		
		//var digitado= $("#modal_atendimento .span_typeahead pre").html();
		var digitado= $("#modal_atendimento_form .span_typeahead pre").html();
		
		//alert(digitado);
		
		$.ajax({ 
			cache: false,
	        data: {formCadastroPaciente: '1', nome: digitado, acao: 'i' },
	        type: 'post',
	        url: 'form.php',
	        timeout: 5000,
	        error: function(x, t, m) {
	        	//$('.cadastrar, .cancelar').button('reset').show();
	        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
	        	
		    },
	        success: function(retorno) {
	        	
	        	if (retorno!='0') {
					
					$('#modal_atendimento #atendimento_paciente_id').val(retorno);
					$('#modal_atendimento #paciente_atendimento_id').val(retorno);
					
					$('#modal_atendimento #tipo_atendimento').val('1');
					
					$('#modal_atendimento_form').submit();
				}
	        	
	        }
	    });
	    
	    //setTimeout(function() { $('#paciente_nome').val(digitado); } , 500);
	    //setTimeout(function() { $('#paciente_nome').blur().focus().val(digitado);  } , 600);
	});
	
	$(document).on('click', '.cadastrar-paciente', function(event){
		
		//var digitado= $("#modal_atendimento .span_typeahead pre").html();
		var digitado= $("#modal_atendimento_form .span_typeahead pre").html();
		
		$('#modal_atendimento').modal('hide');
		
		$('#edita_id_paciente').val('');
		$('#modal_paciente #acao').val('i');
		$('#paciente_nome').val('');
		$('#paciente_data_nasc').val('');
		$('#paciente_cpf').val('');
		$('#paciente_sexo_m').prop('checked', true);
		$('#paciente_sexo_f').prop('checked', false);
		$('#paciente_telefone').val('');
		$('#paciente_telefone2').val('');
		$('#paciente_email').val('');
		
		$('#modal_paciente_form .modal-header h5').html('Cadastrar paciente');
		
	    $('#modal_paciente').modal({backdrop:'static'});
	    $('#modal_paciente_form .cadastrar, #modal_paciente_form .cancelar').button('reset').show();
	    
	    $('#modal_paciente_form .cadastrar').html('Cadastrar');
	    
	    //setTimeout(function() { $('#paciente_nome').val(digitado); } , 500);
	    setTimeout(function() { $('#paciente_nome').blur().focus().val(digitado);  } , 600);
	});
	
	
	$('#modal_paciente').on('hidden', function () {
    	$('#modal_atendimento').css('z-index', '1050');
    });
	
	$('#modal_atendimento').on('hidden', function () {
    	//iniciaAtendimento('0');
    });
    
    
    $('#modal_historico').on('hidden', function () {
    	if ($(this).hasClass('posso_voltar')) {
	    	$(this).removeClass('posso_voltar');
	    	$('#modal_atendimento').css('left', '50%');
    	}
    	//$('#modal_atendimento').modal({backdrop:'static'});
    });
	
	$(document).on('click', '#modal_atendimento .cancelar', function(event){
		iniciaAtendimento('0');
	});
	
	
	//Ao clicar no botão mais, quando é um procedimento, automaticamente volta para a aba de Consultas
	$(document).on('click', '#ato_pai_2 .btn-mais', function(event){
		var identifica_atendimentos= $('#identifica_atendimentos').val();
		
		if (identifica_atendimentos=='1')
			setTimeout(function(){ $('#link_ato_1_1').trigger( "click" ); }, 2000);
	});
	
	$(document).on('click', '.btn-lista', function(event){
		//alert(' aqui vai listar todos os atendimentos do dia deste convênio/valor :-) ');
		
		var desabilitado = $(this).attr("disabled");
        //alert(desabilitado);
        
		if (desabilitado!='disabled') { 
		
			var t= $(this).attr('data-t');
			var i= $(this).attr('data-i');
			var id_ato= $(this).attr('data-id_ato');
			var id_convenio= $(this).attr('data-id_convenio');
			var data= $('#data').val();
			var ordem= $(this).attr('data-ordem');
			
			$.ajax({ 
				cache: false,
		        data: {chamada: 'pegaAtendimentos', data: data, id_ato: id_ato, id_convenio: id_convenio, ordem: ordem, t: t, i: i },
		        type: 'get',
		        url: 'link.php',
		        timeout: 5000,
		        error: function(x, t, m) {
		        	//$('.cadastrar, .cancelar').button('reset').show();
		        	bootbox.alert(msg_erro_padrao+" "+t+msg_erro_padrao_rodape, function() { });
		        	
			    },
		        success: function(retorno) {
		        
		        	$('#modal_atendimentos #atendimentos').html(retorno);
					
					$('#modal_atendimentos').modal({backdrop:'static'});
					
					$('#modal_atendimentos .cancelar').show();
		        
		        }
		    });
		}
	});
	
	$(document).on('click', '.btn-tipo_atendimento button', function(event){
		var valor= $(this).attr('data-value');
		$('#tipo_atendimento').val(valor);
	});
	
});