<?php


define('DB_HOST', 'localhost');
define('DB_USER', 'user');
define('DB_PASS', 'pass');
define('DB_NAME', 'db');

class RequestManager {
	
	public function authenticate() {

		$validated = false;
		$valid_passwords = array( "nks" => "test_integration_password" );

		$valid_users = array_keys($valid_passwords);

		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			
			$user = $_SERVER['PHP_AUTH_USER'];
			$pass = $_SERVER['PHP_AUTH_PW'];

			$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);
		}
		
		if ( ! $validated ) {
			header('WWW-Authenticate: Basic realm="NKS Integration"');
			header('HTTP/1.0 401 Unauthorized');
			die ("Not authorized");
		}
		
		return true;
	}
	
	public function processCustomerRequest() {
		
		$request_body = file_get_contents('php://input'); // get the plain text data sent inside POST request

		$json_data = json_decode( $request_body, JSON_OBJECT_AS_ARRAY  );
		
		$controller = new CustomerController( DB_HOST, DB_USER, DB_PASS, DB_NAME );

		if ( ! $controller->hasValidConnection() ) {
			$this->abort_on_error( "Cannot establish DB connection", "HTTP/1.0 500 Internal Server Error", $controller->getErrorMessage() );
		}
		
		if ( ! $json_data ) {
			$this->abort_on_error( "Cannot parse JSON data. Error code: " . json_last_error(). ' ; Error message: ' . json_last_error_msg() );
		}

		if ( ! isset( $json_data['customerDetails'] ) ) {
			$this->abort_on_error( "Missing 'customerDetails' in JSON data" );
		}
		
		if ( ! isset( $json_data['addresses']) || ! isset( $json_data['addresses']['address'] ) ) {
			$this->abort_on_error( "Missing 'addresses' key in JSON data or this array is missing 'address' key " );
		}

		$customerData = $json_data['customerDetails'];

		$customerAddresses = array();

		foreach ( $json_data['addresses']['address'] as $addr) {
			$customerAddresses[] = $addr['location'];
		}

	
		
		if ( ! $controller->validateCustomerData( $customerData ) ) {
			$this->abort_on_error( "Invalid customerDetails contents" );
		}
		
		$result = $controller->createCustomer( $customerData['sdbsUserId'], $customerData, $customerAddresses );

		if ( !  $result ) {
			$this->abort_on_error( "Error while creating customer", "HTTP/1.0 500 Internal Server Error", $controller->getErrorMessage() );
		}
		else {
			$this->send_response( $result );
		}
	}
	
	private function abort_on_error( $message, $code = "HTTP/1.0 400 Bad Request", $additional_message = '' ) {
		header( $code );
		die( $message . "\r\n" . $additional_message );
	}

	private function send_response( $result ) {
		header( "HTTP/1.0 200 OK" );
		echo( $result );
	}
}