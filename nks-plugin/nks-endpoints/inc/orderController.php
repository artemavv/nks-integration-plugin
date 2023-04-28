<?php

namespace Nks_Integration; 

class OrderController {
	
	private $db;
	private $orderTable = 'nks_orders';
	private $errorHandler = '';
	
	public function __construct( $dbConnection, $errorHandler ) {
		
		$this->errorHandler = $errorHandler;
		$this->db = $dbConnection;
		
		$this->orderTable = $this->db->prefix . $this->orderTable;
	}
	
	public function getErrorMessage() {
		return $this->errorHandler->get_last_error();
	}
	
	public function getOrderById( $orderId ) {
		
		$query = " SELECT * from `" . $this->orderTable . "` WHERE `orderId` = ? LIMIT 1 "; 
		
		$orderResult = $this->db->query( $query, $orderId );
		
		if ( $orderResult && $orderResult->numRows() > 0 ) {
			return $orderResult->fetchArray();
		}
		
		return false;
	}
	
	public function createOrder( $orderData, $customerData ) {
		
		$orderId = $orderData['orderID'];
		
		if ( ! $this->getOrderById( $orderId ) ) {
			return $this->insertOrder( $orderData, $customerData );
		}
		else {
			return $this->updateOrder( $orderId, $orderData, $customerData );
		}
	}
	
	public function insertOrder( $orderData, $customerData ) {
		
		$itemsData = $this->parseItems( $orderData['item'] );
    $itemSkuNumbers = $this->getSkuNumbers( $itemsData );

		$insertQuery = "INSERT INTO `" . $this->orderTable . "` (`orderId`, `customerEmail`, `firstName`, `lastName`, `language`, `customerReference`, `itemsJson`, `itemSkuNumbers` ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )";
		
		$insertResult = $this->db->query( $insertQuery, 
			$orderData['orderID'],
			$customerData['email'],
			$customerData['firstName'],
			$customerData['lastName'],
			$customerData['language'],
      $orderData['customerReference'],
			json_encode( $itemsData ),
			$itemSkuNumbers // string
		);
		
		if ( $this->errorHandler->has_errors() ) {
			return false;
		}
		
		return 'insertOrder ' . $orderData['orderID'];
	}
	
	public function updateOrder( $orderId, $orderData, $customerData ) {

		
		$itemsData = $this->parseItems( $orderData['item'] );
    $itemSkuNumbers = $this->getSkuNumbers( $itemsData );

		$insertQuery = "UPDATE `" . $this->orderTable . "` SET `customerEmail` = ?, `firstName` = ?, `lastName` = ?, `language` = ?, `customerReference` = ?, `itemsJson` = ?, `itemSkuNumbers` = ? WHERE `orderId` = ? ";
		
		$updateResult = $this->db->query( $insertQuery, 
			
			$customerData['email'],
			$customerData['firstName'],
			$customerData['lastName'],
			$customerData['language'],
      $orderData['customerReference'],
			json_encode( $itemsData ),
			$itemSkuNumbers, // string
			$orderData['orderID']
		);
		
		if ( $this->errorHandler->has_errors() ) {
			return false;
		}
		
		return "updateOrder $orderId";
	}

	public function validateOrderData( $orderData, $customerData ) {
		
		if ( isset( $orderData["orderID"] ) ) {
			return true;
		}
    else {
      $error_message = 'Field "orderID" is not set';
      return $error_message;
    }
	}

  private function parseItems( $rawItems ) {
      return $rawItems; // so far, no processing required
  }

  private function getSkuNumbers( $items ) {
      $itemSkuNumbers = '';

      foreach ( $items as $item ) {
        $itemSkuNumbers .= '[[' . $item['skuNumber'] . ']]';
      }

      return $itemSkuNumbers;
  }
	
}