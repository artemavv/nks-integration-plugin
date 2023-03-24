<?php

namespace Nks_Integration; 

class ErrorHandler {
	
	private $last_error = '';
	
	public function handle( $error_message ) {
		$this->last_error = $error_message;
	}
	
	public function get_last_error() {
		return $this->last_error . ' <<<';
	}
	
	public function has_errors() {
		return ( $this->last_error !== '' );
	}
	
}
