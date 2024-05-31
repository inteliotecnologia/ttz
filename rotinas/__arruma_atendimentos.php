<?

$result= mysqli_query($conexao1, "select * from atendimentos
						order by id asc
						
						");
$num= mysqli_num_rows($result);
$insercoes=0;

while ($rs= mysqli_fetch_object($result)) {
	
	
	
	for ($i=1; $i<=$rs->qtde; $i++) {
		echo "Inserindo lançamento ID ". $rs->id ." / ". $i ." <br />";
		
		$valor_total= ($rs->valor_total/$rs->qtde);
		
		$recebido_valor_pessoa= ($rs->recebido_valor_pessoa/$rs->qtde);
		$recebido_valor_clinica= ($rs->recebido_valor_clinica/$rs->qtde);
		$vai_receber_valor_pessoa= ($rs->vai_receber_valor_pessoa/$rs->qtde);
		$vai_receber_valor_clinica= ($rs->vai_receber_valor_clinica/$rs->qtde);
		
		$pessoa_deve= ($rs->pessoa_deve/$rs->qtde);
		$clinica_deve= ($rs->clinica_deve/$rs->qtde);
		
		$por_direito_valor_pessoa= ($rs->por_direito_valor_pessoa/$rs->qtde);
		$por_direito_valor_clinica= ($rs->por_direito_valor_clinica/$rs->qtde);
		
		$result1= mysqli_query($conexao1, "insert into atendimentos_uni
								(original_id, id_medico, id_paciente, id_clinica, id_ato,
								id_convenio, tipo_convenio, recebimento,
								data, ordem,
								valor_unitario, recebido_valor_pessoa, recebido_valor_clinica,
								vai_receber_valor_pessoa, vai_receber_valor_clinica,
								pessoa_deve, clinica_deve,
								por_direito_valor_pessoa, por_direito_valor_clinica,
								valor_total, percentual_clinica, percentual_medico,
								modo_recebimento_convenios_pagos,
								id_acesso) values
							('". $rs->id ."', '". $rs->id_pessoa ."', '0', '". $rs->id_clinica ."', '". $rs->id_ato ."',
							'". $rs->id_convenio ."', '". $rs->tipo_convenio ."', '". $rs->recebimento ."',
							'". $rs->data ."', '". $rs->ordem ."',
							'". $rs->valor_unitario ."', '". $recebido_valor_pessoa ."', '". $recebido_valor_clinica ."',
							'". $vai_receber_valor_pessoa ."', '". $vai_receber_valor_clinica ."',
							'". $pessoa_deve ."', '". $clinica_deve ."',
							'". $por_direito_valor_pessoa ."', '". $por_direito_valor_clinica ."',
							'". $valor_total ."', '". $rs->percentual_clinica ."', '". $rs->percentual_medico ."',
							'". $rs->modo_recebimento_convenios_pagos ."',
							'". $rs->id_acesso ."' ) ") or die("3.1: ". mysqli_error());
							
		$insercoes++;
	}
}

echo "<br />". $num ." linhas originalmente. ". $insercoes. " inserções na nova.";

?>