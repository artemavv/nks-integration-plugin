<?php

require_once( "includes.php");

$req_manager = new Nks_Integration\RequestManager();

if ( $req_manager->authenticate() ) {
	
	// If arrives here, is a valid user.
	$req_manager->processOrderRequestTest();
}

