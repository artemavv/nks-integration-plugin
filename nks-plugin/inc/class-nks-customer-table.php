<?php

/**
 * Helper methods to search in the customers table.
 */
class Nks_Customer_Table_Search {
	
	protected $table_name = 'nks_customers';
	protected $db;
	
	
	public function __construct( $dbConnection ) {	
		$this->db = $dbConnection;
	}
	
	public function find_all() {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE 1 "; 
		
		$query_result = $this->db->query( $query );
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_name( $customer_name ) {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE CONCAT(`firstName`, ' ', `lastName`) like ? "; 
		
		$query_result = $this->db->query( $query, '%' . $customer_name . '%' );
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_email( $customer_email ) {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE `emailAddress` like ? "; 
		
		$query_result = $this->db->query( $query, '%' . $customer_email . '%' );
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_name_and_email( $customer_name, $customer_email ) {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE CONCAT(`firstName`, ' ', `lastName`) like ? AND `emailAddress` like ? "; 
		
		$query_result = $this->db->query( $query, '%' . $customer_name . '%' , '%' . $customer_email . '%' );
		
		return $this->process_search_result( $query_result );
	}
	
	public function process_search_result( $query_result ) {
		
		if ( $query_result && $query_result->numRows() > 0 ) {
			$raw_data = $query_result->fetchAll();
			
			$result = array();
			foreach ( $raw_data as $row ) {
				$result[] = new Nks_Customer( $row );
			}
		}
		
		return $result;
	}
	
}