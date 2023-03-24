<?php

namespace Nks_Integration; 

class RequestManager {
	
	private $db;
	private $errorHandler;
	
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

	public function init() {
		
		$db_params = parse_wordpress_db_params( dirname(__FILE__) . '/../../../../../wp-config.php');
		
		if ( ! is_array( $db_params ) ) {
			$this->abort_on_error( "Failed getting params for DB connection", "HTTP/1.0 500 Internal Server Error" );
		}
		
		$this->errorHandler = new ErrorHandler();
		
		$this->db = new \Nks_dbConnection( 
			$db_params['db_host'], 
			$db_params['db_user'], 
			$db_params['db_password'], 
			$db_params['db_name'], 
			$this->errorHandler,
			$db_params['table_prefix'], 
		);
		
		if ( ! $this->db->has_valid_connection() ) {
			$this->abort_on_error( 
				"Cannot establish DB connection", 
				"HTTP/1.0 500 Internal Server Error"
			);
		}
		
		$request_body = file_get_contents('php://input'); // get the plain text data sent inside POST request
		$json_data = json_decode( $request_body, JSON_OBJECT_AS_ARRAY  );
		
		if ( ! $json_data ) {
			//$this->abort_on_error( "Cannot parse JSON data. Error code: " . json_last_error(). ' ; Error message: ' . json_last_error_msg() );
		}
		
		return $json_data;
	}
	
	public function processOrderRequest() {
		
		$json_data = $this->init();
		
		$controller = new OrderController( $this->db, $this->errorHandler );

		if ( ! $json_data ) {
			$this->abort_on_error( "Cannot parse JSON data. Error code: " . json_last_error(). ' ; Error message: ' . json_last_error_msg() );
		}

		if ( ! isset( $json_data['order'] ) ) {
			$this->abort_on_error( "Missing 'order' key in JSON array" );
		}
		
		if ( ! isset( $json_data['customer'] ) ) {
			$this->abort_on_error( "Missing 'customer' key in JSON array" );
		}

		$orderData = $json_data['order'];
		$customerData = $json_data['customer'];

		if ( ! $controller->validateOrderData( $orderData, $customerData ) ) {
			$this->abort_on_error( "Invalid order/customer contents" );
		}
		
		$result = $controller->createOrder( $orderData, $customerData );

		if ( ! $result ) {
			$this->abort_on_error( "Error while creating order", "HTTP/1.0 500 Internal Server Error", $controller->getErrorMessage() );
		}
		else {
			$this->send_response( $result );
		}
	}

	public function processCustomerRequest() {
		
		$json_data = $this->init();
		
		$controller = new CustomerController( $this->db, $this->errorHandler );

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

	public function processOrderRequestTest() {
		
		$json_data = $this->init();
		
		$request_body = '{
    "customer" : {
    "email" :
    "soa-dev-errors+german_customer1snide@native-instruments.de",
    "firstName" : "German",
    "lastName" : "Customer222",
    "language" : "en"
    },
    "order" : {
    "orderID" : "12591040",
    "customerReference" : "GermanCustDLNoErrors_1594062693374",
    "item" : [ {
    "skuNumber" : "27224",
    "description" : "Spitfire Audio - Hauschka Composer Toolkit NKS Store",
    "serialNumbers" : [ {
            "serialNumber" : "W01K2-35272-8UE25-437B9-D57K6",
            "serialReference" : "W0120356362968"
    } ]
    }, 
    {
        "skuNumber" : "27227",
        "description" : "Spitfire Audio - Olafur Arnalds Chamber Evolutions NKS Store",
        "serialNumbers" : [ {
            "serialNumber" : "P78CB-7998F-C444E-4ED49-3U3D7",
            "serialReference" : "P7820356362975"
        } ]
    } ]
    }
}';
		
		$request_body = '{
    "customer" : {
    "email" :
    "soa-dev-errors+german_customer1snide@native-instruments.de",
    "firstName" : "German",
    "lastName" : "Customer1",
    "language" : "en"
    },
    "order" : {
    "orderID" : "12591045",
    "customerReference" : "GermanCustDLNoErrors_1594114598842",
    "item" : [ {
    "skuNumber" : "27224",
    "description" : "Spitfire Audio - Hauschka Composer Toolkit NKS Store",
    "serialNumbers" : [ {
    "serialNumber" : "W01F2-E434E-823E4-4KD7A-3D954",
    "serialReference" : "W0120356363002"
    }, {
    "serialNumber" : "W01D2-44UEK-CEC9A-44A5B-6C67B",
    "serialReference" : "W0120356363019"
    } ]
    }, {
    "skuNumber" : "27227",
    "description" : "Spitfire Audio - Olafur Arnalds Chamber EvolutionsNKS Store",
    "serialNumbers" : [ {
    "serialNumber" : "P78BF-CAF9K-B5EEU-4U7F8-7FKU2",
    "serialReference" : "P7820356363026"
    }, {
    "serialNumber" : "P78B3-A49AA-DUE63-4958B-825U2",
    "serialReference" : "P7820356363033"
    } ]
    } ]
    }
}';

		$json_data = json_decode( $request_body, JSON_OBJECT_AS_ARRAY  );
		
		$controller = new OrderController( $this->db, $this->errorHandler );

		
		if ( ! $json_data ) {
			$this->abort_on_error( "55Cannot parse JSON data. Error code: " . json_last_error(). ' ; Error message: ' . json_last_error_msg() );
		}

		
		if ( ! isset( $json_data['order'] ) ) {
			$this->abort_on_error( "Missing 'order' key in JSON array" );
		}
		
		if ( ! isset( $json_data['customer'] ) ) {
			$this->abort_on_error( "Missing 'customer' key in JSON array" );
		}

		$orderData = $json_data['order'];
		$customerData = $json_data['customer'];

		if ( ! $controller->validateOrderData( $orderData, $customerData ) ) {
			$this->abort_on_error( "Invalid order/customer contents" );
		}
		
		$result = $controller->createOrder( $orderData, $customerData );

		if ( ! $result ) {
			$this->abort_on_error( "Error while creating order", "HTTP/1.0 500 Internal Server Error", $controller->getErrorMessage() );
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