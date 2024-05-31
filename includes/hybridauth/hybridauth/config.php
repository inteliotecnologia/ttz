<?php
/**
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return 
	array(
		"base_url" => "https://app.ttz.med.br/includes/hybridauth/hybridauth/", 

		"providers" => array ( 
			// openid providers
			"OpenID" => array (
				"enabled" => false
			),

			"Yahoo" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ),
			),

			"AOL"  => array ( 
				"enabled" => false 
			),

			"Google" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "430878363647-9v4lsse0tltlc5cdrbq1n74id30u8dfh.apps.googleusercontent.com", "secret" => "NuzTZ4LR-Wk5EI1dz6lAzEWa" ), 
				"scope"           => "https://www.googleapis.com/auth/userinfo.profile ". // optional
		                               "https://www.googleapis.com/auth/userinfo.email" // optional
			),

			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "1486339101636517", "secret" => "326ff43c31560b9a4a689d3f7bc743ea" ),
				"scope" => "email, user_about_me, user_birthday, user_hometown, user_website, read_stream, read_friendlists",
				"trustForwarded" => false
			),

			"Twitter" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			// windows live
			"Live" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ) 
			),

			"LinkedIn" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			"Foursquare" => array (
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ) 
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);