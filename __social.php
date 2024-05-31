<?
require_once("includes/_core/protecao.php");

if (!isset($_GET["pagina"])) $pg= "home";
else $pg= $_GET["pagina"];

session_start();
$retornou=0;

//Retorno social
if ( (isset($_GET["entrada"])) && ($_GET["entrada"]) ) {
	$config = 'includes/hybridauth/hybridauth/config.php';
	require_once( "includes/hybridauth/hybridauth/Hybrid/Auth.php" );
	
	$user_data = NULL;
	
	// try to get the user profile from an authenticated provider
	try{
		$hybridauth = new Hybrid_Auth( $config );

		// selected provider name 
		$provider = @ trim( strip_tags( $_GET["provider"] ) );

		// check if the user is currently connected to the selected provider
		if( !  $hybridauth->isConnectedWith( $provider ) ){ 
			// redirect him back to login page
			header( "location: login.php?error=Your are not connected to $provider or your session has expired" );
		}

		// call back the requested provider adapter instance (no need to use authenticate() as we already did on login page)
		$adapter = $hybridauth->getAdapter( $provider );
		
		// grab the user profile
		$user_data = $adapter->getUserProfile();
		
		$retornou=1;
		
		//echo "E-mail: ". $user_data->email;
		
		//echo $informacoes;
		
		$cadastroRedeSocial= true;
		
		require_once("form.php");
		
		/*
		$result_consulta= mysqli_query($conexao2, "select * from usuarios_clientes
										where email = '". $user_data->email ."'
										") or die( mysqli_error($conexao2));
		$num_consulta= mysqli_num_rows($result_consulta);
		if ($num_consulta>0) {
			$rs_consulta= mysqli_fetch_object($result_consulta);
			
			if ($num_consulta==1)
				header("location: ./b.php?pg=o/login&s=". $rs_consulta->slug ."&email=". $rs_consulta->email ."&site=1");
			else
				header("location: ./b.php?pg=o/login&pedido=1&email=". $rs_consulta->email ."");
		}*/
		
    }
	catch( Exception $e ){  
		// In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to 
		// let hybridauth forget all about the user so we can try to authenticate again.

		// Display the received error,
		// to know more please refer to Exceptions handling section on the userguide
		switch( $e->getCode() ){ 
			case 0 : echo "Unspecified error."; break;
			case 1 : echo "Hybriauth configuration error."; break;
			case 2 : echo "Provider not properly configured."; break;
			case 3 : echo "Unknown or disabled provider."; break;
			case 4 : echo "Missing provider application credentials."; break;
			case 5 : echo "Authentication failed. " 
					  . "The user has canceled the authentication or the provider refused the connection."; 
			case 6 : echo "User profile request failed. Most likely the user is not connected "
					  . "to the provider and he should to authenticate again."; 
				   $adapter->logout(); 
				   break;
			case 7 : echo "User not connected to the provider."; 
				   $adapter->logout(); 
				   break;
		} 

		echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();

		echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>";  
	}
}


//Chamada social, retornar novamente
elseif ( (isset($_GET["provider"])) && ($_GET["provider"]) ) {
	
	$config = 'includes/hybridauth/hybridauth/config.php';
	require_once( "includes/hybridauth/hybridauth/Hybrid/Auth.php" );

	// check for erros and whatnot
	$error = "";
	
	try {
		// create an instance for Hybridauth with the configuration file path as parameter
		$hybridauth = new Hybrid_Auth( $config );

		// set selected provider name 
		$provider = @ trim( strip_tags( $_GET["provider"] ) );

		// try to authenticate the selected $provider
		$adapter = $hybridauth->authenticate( $provider );
		
		$tokens= $adapter->getAccessToken();
		
		// if okey, we will redirect to user profile page 
		$hybridauth->redirect( "index2.php?pagina=social&entrada=1&provider=$provider" );
	}
	catch( Exception $e ){
		// In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to 
		// let hybridauth forget all about the user so we can try to authenticate again.

		// Display the received error,
		// to know more please refer to Exceptions handling section on the userguide
		switch( $e->getCode() ){ 
			case 0 : $error = "Unspecified error."; break;
			case 1 : $error = "Hybriauth configuration error."; break;
			case 2 : $error = "Provider not properly configured."; break;
			case 3 : $error = "Unknown or disabled provider."; break;
			case 4 : $error = "Missing provider application credentials."; break;
			case 5 : $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection."; break;
			case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
				     $adapter->logout(); 
				     break;
			case 7 : $error = "User not connected to the provider."; 
				     $adapter->logout(); 
				     break;
		} 

		// well, basically your should not display this to the end user, just give him a hint and move on..
		$error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage(); 
		$error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";
	}
	
}
?>