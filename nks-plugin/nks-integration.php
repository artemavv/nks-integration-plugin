<?php
/**
Plugin Name: NKS Integration
Description: Allows to list & search NKS-imported customers and orders
Version: 0.1
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
	define( 'NKSI_VERSION', '0.1' );
}

require_once 'includes.php';

$nks_admin = new Nks_Admin();
