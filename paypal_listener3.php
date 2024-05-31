<?php
require_once("includes/conexao.unico.php");
require_once("includes/funcoes.php");

require 'includes/mixpanel-php-master/lib/Mixpanel.php';
$mp = Mixpanel::getInstance("6a789eb890599bd02459fba47a563748");

//$mp->track("[Pagamento] testando", array("label" => "sign-up"));

//die();
// Send an empty HTTP 200 OK response to acknowledge receipt of the notification 
header('HTTP/1.1 200 OK');

$item_name        = $_POST['item_name'];
$item_number      = $_POST['item_number'];
$payment_status   = $_POST['payment_status'];
$payment_amount   = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id           = $_POST['txn_id'];
$receiver_email   = $_POST['receiver_email'];
$payer_email      = $_POST['payer_email'];

$mp->track("[Pagamento] ". $item_name, array("label" => "sign-up"));

// Build the required acknowledgement message out of the notification just received
$req = 'cmd=_notify-validate';               // Add 'cmd=_notify-validate' to beginning of the acknowledgement

foreach ($_POST as $key => $value) {         // Loop through the notification NV pairs
	$value = urlencode(stripslashes($value));  // Encode these values
	$req  .= "&$key=$value";                   // Add the NV pairs to the acknowledgement
}

// Set up the acknowledgement request headers
$header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";                    // HTTP POST request
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

// Open a socket for the acknowledgement request
$fp = fsockopen('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

// Send the HTTP POST request back to PayPal for validation
fputs($fp, $header . $req);

while (!feof($fp)) {                     // While not EOF
	$res = fgets($fp, 1024);               // Get the acknowledgement response
	if (strcmp ($res, "VERIFIED") == 0) {  // Response contains VERIFIED - process notification
		
		$mp->track("[Pagamento] Pagou", array("label" => "sign-up"));
		
	  // Send an email announcing the IPN message is VERIFIED
	  //$mail_From    = "IPN@example.com";
	  //$mail_To      = "Your-eMail-Address";
	  //$mail_Subject = "VERIFIED IPN";
	  //$mail_Body    = $req;
	  //mail($mail_To, $mail_Subject, $mail_Body, $mail_From);
	
	  // Authentication protocol is complete - OK to process notification contents
	
	  // Possible processing steps for a payment include the following:
	
	  // Check that the payment_status is Completed
	  // Check that txn_id has not been previously processed
	  // Check that receiver_email is your Primary PayPal email
	  // Check that payment_amount/payment_currency are correct
	  // Process payment
	
	} 
	else if (strcmp ($res, "INVALID") == 0) { //Response contains INVALID - reject notification
	
	  // Authentication protocol is complete - begin error handling
	
	  // Send an email announcing the IPN message is INVALID
	  //$mail_From    = "IPN@example.com";
	  //$mail_To      = "Your-eMail-Address";
	  //$mail_Subject = "INVALID IPN";
	  //$mail_Body    = $req;
	
	  //mail($mail_To, $mail_Subject, $mail_Body, $mail_From);
	  
	  $mp->track("[Pagamento] Deu errado", array("label" => "sign-up"));
	}
}

fclose($fp);  // Close the file
?>