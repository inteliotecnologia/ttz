
<div id="centralizado">
    <h2>Ops! Voc� n�o tem acesso a esta �rea!</h2>
    
    <ul class="recuo1">
	<?
    switch ($erro_a) {
		case 1:
	?>
    <li>Somente no posto de atendimento CENTRAL � poss�vel dar entrada ou fazer movimenta��es.</li>
    <?
    break;
	case 2:
	?>
    <li>Somente em um posto de atendimento � poss�vel realizar consultas.</li>
    <?
	break;
	default:
	?>
    <li>Voc� est� acessando uma �rea restrita.</li>
    <? } ?>
    </ul>
</div>
