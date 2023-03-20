<?php

class CustomerController {
	
	private $db;
	private $customerTable = 'nks_customers';
	private $addressTable = 'nks_addresses';
	private $address2CustomerTable = 'nks_address2customer';
	private $error_handler = '';
	
	public function __construct( $host, $user, $pass, $name ) {
		
		$this->error_handler = new ErrorHandler();
		$this->db = new dbConnection( $host, $user, $pass, $name, $this->error_handler );
	}
	
	public function hasValidConnection() {
		return $this->db->has_valid_connection();
	}
	
	public function getErrorMessage() {
		return $this->error_handler->get_last_error();
	}
	
	public function createCustomer( $sdbsUserId, $customerData, $customerAddresses ) {
		
		if ( ! $this->getCustomerById( $sdbsUserId ) ) {
			return $this->insertCustomer( $customerData, $customerAddresses );
		}
		else {
			return $this->updateCustomer( $sdbsUserId, $customerData );
		}
	}

	public function validateCustomerData( $customerData ) {
		if ( isset( $customerData['firstName'] ) 
			&& isset( $customerData['lastName'] )
			&& isset( $customerData['emailAddress'] )
			&& isset( $customerData['phoneNumber'] )
			) {
			return true;
		}

		return false;
	}
	
	public function getCustomerById( $sdbsUserId ) {
		
		$query = " SELECT * from `" . $this->customerTable . "` WHERE `sdbsUserId` = ? LIMIT 1 "; 
		
		$customerResult = $this->db->query( $query, $sdbsUserId );
		
		if ( $customerResult && $customerResult->numRows() > 0 ) {
			return $customerResult->fetchArray();
		}
		
		return false;
	}
	
	protected function insertCustomer( $customerData, $customerAddresses ) {
		
		$insertQuery = "INSERT INTO `" . $this->customerTable . "` (`sdbsUserId`, `business`, `firstName`, `lastName`, `emailAddress`, `phoneNumber`, `language`, `vatId`, `category`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		
		$insertResult = $this->db->query( $insertQuery, 
			$customerData['sdbsUserId'],
			intval($customerData['business']),
			$customerData['firstName'],
			$customerData['lastName'],
			$customerData['emailAddress'],
			$customerData['phoneNumber'],
			$customerData['language'],
			$customerData['vatId'],
			$customerData['category'],
		);
		
		if ( $this->error_handler->has_errors() ) {
			return false;
		}
		
		$sdbsUserId = $this->db->lastInsertID();
		
		$addressIds = array();
		
		if ( is_array( $customerAddresses ) && count( $customerAddresses ) ) {
			foreach ( $customerAddresses as $addressData ) {
				$resultId = $this->insertAddress( $addressData );
				if ( $resultId ) {
					$addressIds[] = $resultId;
				}
			}

			if ( count( $addressIds ) ) {
				$this->addAddresses2Customer( $sdbsUserId, $addressIds );
			}
		}

		return 'insertCustomer';
	}
	
	
	protected function updateCustomer( $sdbsUserId, $customerData ) {
		$updateQuery = "UPDATE `" . $this->customerTable . "` SET `business` = ? , `firstName` = ?, `lastName` = ?, `emailAddress` = ?, `phoneNumber` = ?, `language` = ?, `vatId` = ?, `category` = ? WHERE `sdbsUserId` = ? ";
		
		$updateResult = $this->db->query( $updateQuery, 
			intval($customerData['business']),
			$customerData['firstName'],
			$customerData['lastName'],
			$customerData['emailAddress'],
			$customerData['phoneNumber'],
			$customerData['language'],
			$customerData['vatId'],
			$customerData['category'],
			$customerData['sdbsUserId']
		);
		
		if ( $this->error_handler->has_errors() ) {
			return false;
		}
		
		return 'updateCustomer';
	}
	
	protected function addAddresses2Customer( $sdbsUserId, $addressIds ) {
		
		$insertQuery = "INSERT INTO `" . $this->address2CustomerTable . "` ( `sdbsUserId`, `addressId` ) VALUES ( ?, ? )";
		
		foreach ( $addressIds as $addressId ) {
			$this->db->query( $insertQuery, $sdbsUserId, $addressId );
		}
	}
	
	protected function insertAddress( $addressData ) {
		$insertQuery = "INSERT INTO `" . $this->addressTable . "` ( `addressId`, `firstName`, `lastName`, `companyName`, `address1`, `address2`, `address3`, `city`, `state`, `zipCode`, `county`, `province`, `country`, `addressee`, `addressType`) "
			. " VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) ";
		
		$insertResult = $this->db->query( $insertQuery, 
			$addressData['firstName'],
			$addressData['lastName'],
			$addressData['companyName'],
			$addressData['address1'],
			$addressData['address2'],
			$addressData['address3'],
			$addressData['city'],
			$addressData['state'],
			$addressData['zipCode'],
			$addressData['county'],
			$addressData['province'],
			$addressData['country'],
			$addressData['addressee'],
			$addressData['addressType']
		);
	
		$addressId = $this->db->lastInsertID();
		
		return $addressId;
	}	
}