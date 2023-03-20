<?php

class Nks_Address_Table {
	
	protected $table_name = 'nks_addresses';
	protected $join_table_name = 'nks_address2customer';
	protected $db;
	
	
	public function __construct( $dbConnection ) {	
		$this->db = $dbConnection;
	}
	
	public function find_all() {
		
		$query = " SELECT * from `" . $this->table_name . "` WHERE 1 "; 
		
		$query_result = $this->db->query( $query );
		
		return $this->process_search_result( $query_result );
	}
	
	public function find_by_customer_id( $customer_id ) {
		
		$query = " SELECT a.* from `" . $this->table_name . "` AS a LEFT JOIN `" .  $this->join_table_name  . "` as a2c ON (a.addressId =  a2c.addressId  ) WHERE a2c.sdbsUserId = ? "; 
		
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