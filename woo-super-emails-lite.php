<?php
/**
 * Plugin Name: Super Emails for WooCommerce lite
 * Text Domain: sefw-lite
 * Plugin URI: http://www.woosuperemails.com/
 * Description: Display suggested products on transactional emails sent to customers
 * Author: Boris Colombier
 * Author URI: http://wba.fr
 * Version: 1.1
 * License: 
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	define('sefw_lite_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
	define('sefw_lite_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
	define('sefw_lite_VERSION', '1.0.0');

	require_once( dirname( __FILE__ ) . '/includes/class-sefw-lite.php' );

	add_action( 'plugins_loaded', 'sefw_lite_load', 10 );

	function sefw_lite_load() {
		load_plugin_textdomain( 'sefw-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		$GLOBALS['SEFWLITE'] = new SEFWlite();
	}    
}


