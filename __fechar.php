<? if ($_COOKIE["id_usuario"]!="") { ?>
<h2 class="titulos">Fechar produção</h2>

<?
$data= date("d/m/Y");
$vale_dia= date("Y-m-d");

$ontem= soma_data($data, -1, 0, 0);
$amanha= soma_data($data, 1, 0, 0);
				
switch ($_COOKIE["id_turno_sessao"]) {
	case 1:
		$str_soma= " and   op_limpa_pesagem.data_pesagem = '". $data ."' ";
	break;
	
	case 2:
		$str_soma= " and   op_limpa_pesagem.data_pesagem = '". $data ."' ";
	break;
	
	case 3:
		$str_soma= " and   op_limpa_pesagem.data_pesagem = '". $data ."' ";
	break;
	
	case 4:
		$str_soma= " and   op_limpa_pesagem.data_pesagem = '". $amanha ."' ";
		
	break;
}

//$str= " and   op_limpa_pesagem.id_turno = '". $h ."' ";


$result_soma= mysql_query("select sum(peso) as soma from op_limpa_pesagem, rh_turnos
							where op_limpa_pesagem.id_empresa = '". $_COOKIE["id_empresa"] ."' 
							and   op_limpa_pesagem.id_turno = rh_turnos.id_turno
							and   rh_turnos.id_turno_index = '". $_COOKIE["id_turno_sessao"] ."'
							and   op_limpa_pesagem.extra = '0'
							$str_soma
							") or die(mysql_error());
$rs_soma= mysql_fetch_object($result_soma);

$hoje_mk= mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$id_dia= date("w", $hoje_mk);

$result_dia= mysql_query("select rh_turnos_horarios.* from rh_turnos, rh_turnos_horarios
							where rh_turnos.id_turno = rh_turnos_horarios.id_turno
							and   rh_turnos.id_turno_index = '". $_COOKIE["id_turno_sessao"] ."'
							and   rh_turnos_horarios.id_dia = '$id_dia'
							");
$rs_dia= mysql_fetch_object($result_dia);

$funcionarios_neste_turno_neste_dia= pega_funcionarios_trabalhando_retroativo(1, $_COOKIE["id_turno_sessao"], $data, $data ." ". $rs_dia->entrada);

if ($funcionarios_neste_turno_neste_dia>0) $media= $rs_soma->soma/$funcionarios_neste_turno_neste_dia;
else $media= 0;
?>

<table cellspacing="0">
	<tr>
    	<th align="left">Data</th>
    	<th align="left">Turno</th>
        <th>Peso turno</th>
        <th>Funcionários</th>
        <th>Média individual</th>
    </tr>
    <tr>
    	<td><?= $data; ?></td>
        <td><?= pega_turno_padrao($_COOKIE["id_turno_sessao"]); ?></td>
        <td align="center"><?= fnumf($rs_soma->soma) ." kg"; ?></td>
        <td align="center"><?= $funcionarios_neste_turno_neste_dia; ?></td>
        <td align="center"><?= fnumf($media) ." kg/funcionário"; ?></td>
    </tr>
</table>

<br /><br /><br /><br /><br /><br /><br /><br /><br />

<center>
	<button id="sair" onclick="window.top.location.href='index2.php?pagina=logout';">sair do sistema</button>
</center>


<script language="javascript">
	daFoco("sair");
</script>
<?
}
?>