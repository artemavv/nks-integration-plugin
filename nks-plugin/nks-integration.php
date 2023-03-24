<?php
/**
Plugin Name: NKS Integration
Description: Allows to list & search NKS-imported customers and orders
Version: 0.2
Author: Artem Avvakumov
Author URI: https://artemavv.dev
Text Domain: nks-int
License: GPLv2 or later

*/

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! defined( 'NKSI_URL' ) ) {
	define( 'NKSI_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'NKSI_PATH' ) ) {
	define( 'NKSI_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'NKSI_PLUGIN_FILE' ) ) {
	define( 'NKSI_PLUGIN_FILE', basename( __FILE__ ) );
}

if ( ! defined( 'NKSI_VERSION' ) ) {
	define( 'NKSI_VERSION', '0.2' );
}


// Act on plugin activation
register_activation_hook( __FILE__, "activate_nks_plugin" );

// Act on plugin de-activation
register_deactivation_hook( __FILE__, "deactivate_nks_plugin" );

function activate_nks_plugin() {
 
	nks_init_db(); // Insert DB Tables

}

function deactivate_nks_plugin() {
	// maybe put something there later.
}

// Create DB Tables
function nks_init_db() {

	global $wpdb;

	// Include upgrade script
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

	// 1) Set up order table
	$orderTable = $wpdb->prefix . 'nks_orders';

	// Create table if not exist
	if( $wpdb->get_var( "show tables like '$orderTable'" ) != $orderTable ) {

		$orderSql = "CREATE TABLE `$orderTable` (
			`orderId` int NOT NULL PRIMARY KEY,
			`customerEmail` varchar(255) NOT NULL,
			`firstName` varchar(255) NOT NULL,
			`lastName` varchar(255) NOT NULL,
			`language` varchar(255) NOT NULL,
			`customerReference` varchar(255) NOT NULL,
			`itemsJson` text NOT NULL,
			`itemSkuNumbers` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		// Create orders table
		dbDelta( $orderSql );
	}

	// 2) Set up customer table
	$customerTable = $wpdb->prefix . 'nks_customers';

	// Create table if not exist
	if( $wpdb->get_var( "show tables like '$customerTable'" ) != $customerTable ) {

		$customerSql = "CREATE TABLE `$customerTable` (
			`sdbsUserId` int NOT NULL PRIMARY KEY,
			`business` int DEFAULT NULL,
			`firstName` varchar(255) NOT NULL,
			`lastName` varchar(255) NOT NULL,
			`emailAddress` varchar(255) NOT NULL,
			`phoneNumber` varchar(255) NOT NULL,
			`language` varchar(255) NOT NULL,
			`vatId` varchar(255) DEFAULT NULL,
			`category` varchar(255) DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		// Create customers table
		dbDelta( $customerSql );
	}


	// 3) Set up address table
	$addressTable = $wpdb->prefix . 'nks_addresses';

	// Create table if not exist
	if( $wpdb->get_var( "show tables like '$addressTable'" ) != $addressTable ) {

		$addressSql = "CREATE TABLE `$addressTable` (
			`addressId` int NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`sdbsUserId` int NOT NULL,
			`firstName` varchar(255) NOT NULL,
			`lastName` varchar(255) NOT NULL,
			`companyName` varchar(255)  DEFAULT NULL,
			`address1` varchar(255)  DEFAULT NULL,
			`address2` varchar(255)  DEFAULT NULL,
			`address3` varchar(255)  DEFAULT NULL,
			`city` varchar(255)  DEFAULT NULL,
			`state` varchar(255)  DEFAULT NULL,
			`zipCode` varchar(255)  DEFAULT NULL,
			`county` varchar(255)  DEFAULT NULL,
			`province` varchar(255)  DEFAULT NULL,
			`country` varchar(255)  DEFAULT NULL,
			`addressee` varchar(255) DEFAULT NULL,
			`addressType` varchar(255) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		// Create customers table
		dbDelta( $addressSql );
	}

}

require_once 'includes.php';

$nks_admin = new Nks_Admin();
