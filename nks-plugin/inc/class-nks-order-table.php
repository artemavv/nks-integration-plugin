<?php

class Nks_Order_Table_Search {
	
	protected $table_name;
	protected $db;
	
	public function __construct( $dbConnection ) {	

		global $wpdb;
		$this->db = $dbConnection;
		$this->table_name = $wpdb->prefix . 'nks_orders';
	}
	
	public function find_all() {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE 1 "; 
		
		$query_result = $this->db->query( $query );
		
		return $this->process_search_result( $query_result );
	}
	
	
	public function find_by_name( $name, $sku = '' ) {
		
		if ( $sku ) {
			$query = " SELECT * from `" . $this->table_name . "` WHERE CONCAT(`firstName`, ' ', `lastName`) like ? AND `itemSkuNumbers` LIKE ? "; 
			$query_result = $this->db->query( $query,  '%' . $name . '%',  '%[[' . $sku . ']]%' );
		}
		else {
			$query = " SELECT * from `" . $this->table_name . "` WHERE CONCAT(`firstName`, ' ', `lastName`) like ? "; 
			$query_result = $this->db->query( $query,  '%' . $name . '%' );
		}
	
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_email( $email, $sku = '' ) {
		
		if ( $sku ) {
			$query = " SELECT * from `" . $this->table_name . "` WHERE `customerEmail` like ? AND `itemSkuNumbers` LIKE ? "; 
			$query_result = $this->db->query( $query,  '%' . $email . '%',  '%[[' . $sku . ']]%' );
		}
		else {
			$query = " SELECT * from `" . $this->table_name . "` WHERE `customerEmail` like ? "; 
			$query_result = $this->db->query( $query,  '%' . $email . '%' );
		}
	
		return $this->process_search_result( $query_result );
	}
	
	
	public function find_by_name_and_email( $customer_name, $customer_email ) {
		
		if ( $sku ) {
			$query = " SELECT * from `" . $this->table_name . "` WHERE CONCAT(`firstName`, ' ', `lastName`) like ? AND `customerEmail` like ? AND `itemSkuNumbers` LIKE ? "; 
			$query_result = $this->db->query( $query, '%' . $customer_name . '%' , '%' . $customer_email . '%',  '%[[' . $sku . ']]%' );
		}
		else {
			$query = " SELECT * from `" . $this->table_name . "` WHERE CONCAT(`firstName`, ' ', `lastName`) like ? AND `customerEmail` like ? "; 
			$query_result = $this->db->query( $query, '%' . $customer_name . '%' , '%' . $customer_email . '%' );
		}
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_sku( $sku ) {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE `itemSkuNumbers` LIKE ? "; 
		
		$query_result = $this->db->query( $query,  '%[[' . $sku . ']]%' );
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_order_id( $customer_id ) {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE orderId = ? "; 
		
		$query_result = $this->db->query( $query, $customer_id );
		
		return $this->process_search_result( $query_result );
	}
	
	public function process_search_result( $query_result ) {
		
		if ( $query_result && $query_result->numRows() > 0 ) {
			$raw_data = $query_result->fetchAll();
			
			$result = array();
			foreach ( $raw_data as $row ) {
				$items = Nks_Order_Item::createItemsFromRawData( $row['itemsJson'] );
				$result[] = new Nks_Order( $row, $items );
			}
		}
		
		return $result;
	}
	
}