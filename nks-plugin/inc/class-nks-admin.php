<?php

/**
 * This is the admin class of plugin
 *
 * - renders admin page
 * - renders customer list
 * - runs search in customers table
 * 
 */
class Nks_Admin {

	protected $db;
	protected $customer_table;
	protected $address_table;

	/**
	 * There are all hooks and actions that need to be set up for plugin to work.
	 */
	public function __construct() {

		$this->db = new Nks_dbConnection( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
		$this->customer_table = new Nks_Customer_Table_Search( $this->db );
		$this->address_table = new Nks_Address_Table_Search( $this->db );
		
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 10 );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
		}
	}

	/**
	 * Adds a submenu page to the Tools menu.
	 */
	public function add_menu_page() {
		add_management_page(
			__( 'NKS Customers' ),                                  // page title.
			__( 'NKS Customers' ),                                  // menu title.
			'manage_options',
			'nks-customers',                                       // menu slug.
			array( $this, 'render_customers_page' )                // callback.
		);
	}

	/**
	 * Let's add some CSS and JS to our awesome plugin.
	 */
	public function enqueue_styles_scripts() {

		if ( file_exists( NKSI_PATH . 'assets/js/main.js' ) ) {

			wp_enqueue_script( 'nks-int-main', NKSI_URL . 'assets/js/main.js', array( 'jquery' ), NKSI_VERSION, true );
			wp_localize_script( 'nks-int-main', 'ajaxUrl', admin_url( 'admin-ajax.php' ) );
		}

		if ( file_exists( NKSI_PATH . 'assets/css/main.css' ) ) {
			wp_enqueue_style( 'nks-int-main', NKSI_URL . 'assets/css/main.css', false, NKSI_VERSION );
		}

	}

	/**
	 * Displays list of NKS customers on admin page
	 */
	public function render_customers_page() {

		$customer_name = isset( $_GET['nks-int-search-name'] ) ? trim( $_GET['nks-int-search-name'] ) : '';
		$customer_email = isset( $_GET['nks-int-search-email'] ) ? trim( $_GET['nks-int-search-email'] ) : '';
		
		?>
		<form method="GET" target="">
			<div class="wrap">
				<h2><?php esc_html_e( 'NKS Customers', 'nks-int' ); ?></h2>
			</div>

	
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="nks-int-search-name">Search by name</label></th>
						<td>
							<input class="regular-text" id="nks-int-search-name" name="nks-int-search-name" type="text" value="<?php echo $customer_name; ?>" style="width:150px">
						</td>
					</tr>
					<tr>
						<th><label for="nks-int-search-email">Search by email</label></th>
						<td>
							<input class="regular-text" id="nks-int-search-email" name="nks-int-search-email" type="text" value="<?php echo $customer_email; ?>" style="width:150px">
						</td>
					</tr>
					<tr>
						<th><label for="nks-int-show-address">Show customers addresses</label></th>
						<td>
							<input id="nks-int-show-address" name="nks-int-show-address" value="1" type="checkbox">
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<input type="hidden" name="page" value="nks-customers" />
				<input type="submit" id="nks-int-search" class="button button-primary" value="Search" />
			</p>

		</form>
		<?php
		
		$this->render_search_results();
	}
	
	public function render_search_results() {
		
		$customer_name = isset( $_GET['nks-int-search-name'] ) ? trim( $_GET['nks-int-search-name'] ) : '';
		$customer_email = isset( $_GET['nks-int-search-email'] ) ? trim( $_GET['nks-int-search-email'] ) : '';
		
		$show_addresses = isset( $_GET['nks-int-show-address'] ) ? true : false;
		
		if ( $customer_name || $customer_email ) {
			
			if ( $customer_name && ! $customer_email  ) {
				$search_results =	$this->customer_table->find_by_name( $customer_name );
				$header_text = 'Search results for name "' . $customer_name . '"';
				$not_found_text = 'Nothing found for name "' . $customer_name . '"';
			}
			
			if ( ! $customer_name && $customer_email  ) {
				$search_results =	$this->customer_table->find_by_email( $customer_email );
				$header_text = 'Search results for email "' . $customer_email . '"';
				$not_found_text = 'Nothing found for email "' . $customer_email . '"';
			}
			
			if ( $customer_name && $customer_email ) {
				$search_results =	$this->customer_table->find_by_name_and_email( $customer_name, $customer_email );
				$header_text = 'Search results for name "' . $customer_name . '" AND email "' . $customer_email . '"';
				$not_found_text = 'Nothing found for name "' . $customer_name . '" AND email "' . $customer_email . '"';
			}
			
		}
		else {
			$search_results = $this->customer_table->find_all();
			$header_text = 'All customers in DB';
			$not_found_text = 'Nothing found';
		}
		
		if ( is_array( $search_results ) && count( $search_results ) ) {

			if ( $show_addresses ) {
				$addresses = $this->find_addresses_for_customers( $search_results );			
			}
			else {
				$addresses = false;
			}
			echo('<h1>' . $header_text . '</h1>');
			$this->render_customers_table( $search_results, $addresses );
		}
		else {
			echo('<h1>' . $not_found_text. '</h1>');
		}
		

	}
	
	private function render_customers_table( $customers, $addresses = false ) {
		
		?>

			<div class="table-wrapper">
					<table class="fl-table">
							<thead>
							<tr>
								<th>ID</th>
								<th>First name</th>
								<th>Last name</th>
								<th>Email</th>
								<th>Phone</th>
								<th>Language</th>
							</tr>
							</thead>
							<tbody>
								<?php foreach ($customers as $customer ): ?>
									<tr>
										<td><?php echo( $customer->getSdbsUserId() ); ?></td>
										<td><?php echo( $customer->getFirstName() ); ?></td>
										<td><?php echo( $customer->getLastName() ); ?></td>
										<td><?php echo( $customer->getEmailAddress() ); ?></td>
										<td><?php echo( $customer->getPhoneNumber() ); ?></td>
										<td><?php echo( $customer->getLanguage() ); ?></td>
									</tr>
									<?php if ( $addresses ): ?>
										<?php $this->render_address_rows( $customer->getSdbsUserId(), $addresses ); ?>
									<?php endif; ?>
								<?php endforeach; ?>
							<tbody>
					</table>
			</div><?php
	}
	
	private function render_address_rows( $customerId, $addresses ) {
		
		foreach ( $addresses[$customerId] as $address ) {
		?>
			<tr>
				<td colspan="6" class="address-row" >
					<?php echo $address->toSingleLine(); ?> 
				</td>
			</tr>
		<?php
		}
	}
	
	private function find_addresses_for_customers( $customers ) {
		
		$result = array();
		
		foreach ( $customers as $customer ) { 
			$customerId = $customer->getSdbsUserId();
			$result[ $customerId ] = $this->address_table->find_by_customer_id( $customerId );
		}
		
		return $result;
	}
}
