<?

$result= mysqli_query($conexao1, "select * from usuarios
						where perfil = '2'						
						");
$num= mysqli_num_rows($result);
$insercoes=0;

while ($rs= mysqli_fetch_object($result)) {
	
	$result_pagamento= mysqli_query($conexao1, "insert into pagamentos
									(id_usuario, id_pessoa, data_pagamento,
									data_de, data_ate, tipo_pagamento,
									hora_pagamento, valor, referencia)
									values
									('". $rs->id_usuario ."', '". $rs->id_pessoa ."', '". $rs->data_cadastro ."',
									'". $rs->data_cadastro ."', '". soma_data($rs->data_cadastro, 0, 1, 0) ."', '0',
									'". $rs->hora_cadastro ."', '0.00', ''
									)
									");
	
	
}

echo "<br />". $num ." inserções.";

?>