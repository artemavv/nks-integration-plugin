<?php

class Nks_Address_Table_Search {
	
	protected $table_name;
	protected $db;
	
	public function __construct( $dbConnection ) {	

		global $wpdb;
		$this->db = $dbConnection;
		$this->table_name = $wpdb->prefix . 'nks_addresses';
		
	}
	
	public function find_all() {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE 1 "; 
		
		$query_result = $this->db->query( $query );
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_customer_id( $customer_id ) {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE sdbsUserId = ? "; 
		
		$query_result = $this->db->query( $query, $customer_id );
		
		return $this->process_search_result( $query_result );
	}
	
	public function process_search_result( $query_result ) {
		
		if ( $query_result && $query_result->numRows() > 0 ) {
			$raw_data = $query_result->fetchAll();
			
			$result = array();
			foreach ( $raw_data as $row ) {
				$result[] = new Nks_Address( $row );
			}
		}
		
		return $result;
	}
	
}