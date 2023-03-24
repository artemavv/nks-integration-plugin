<?php

function parse_wordpress_db_params( $filename ) {
	
	$params = array();
	$matches = array();
	
	$text = file_get_contents( $filename );
	
	//preg_match_all( "/'DB_NAME',\s*'([^']+)\'([\S\s]*)'DB_USER',\s*'([^']+)\'([\S\s]*)'DB_PASSWORD',\s*'([^']+)\'([\S\s]*)'DB_HOST',\s*'([^']+)\'([\S\s]*)/m", $text, $matches );
	
	preg_match_all( 
		"/'DB_NAME',\s*'([^']+)\'([\S\s]*)" . 
		"'DB_USER',\s*'([^']+)\'([\S\s]*)" . 
		"'DB_PASSWORD',\s*'([^']+)\'([\S\s]*)" .
		"'DB_HOST',\s*'([^']+)\'([\S\s]*)" . 
		"\\\$table_prefix\s*=\s*'([^']+)'/m",
		$text, 
		$matches 
	);
	
	
	
	$params['db_name']			= isset( $matches[1] ) ? $matches[1][0] : false;
	$params['db_user']			= isset( $matches[3] ) ? $matches[3][0] : false;
	$params['db_password']	= isset( $matches[5] ) ? $matches[5][0] : false;
	$params['db_host']			= isset( $matches[7] ) ? $matches[7][0] : false;
	$params['table_prefix']	= isset( $matches[9] ) ? $matches[9][0] : false;
	
	if ( array_filter($params) == $params ) { // if array has no false values
		return $params; // everything is OK, can return data
	}
	else {
		return false;
	}
	
}