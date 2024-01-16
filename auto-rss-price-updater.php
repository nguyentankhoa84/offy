<?php
/**
 * Auto RSS price updater
 *
 * @package           auto-rss-price-updater
 * @author            Khoa Nguyen
 * @copyright         2024 Khoa Nguyen
 *
 * @wordpress-plugin
 * Plugin Name:       Auto RSS price updater
 * Description:       Every 10 mins, get and save to DB the latest price for the list of products of 4 metal(Gold, Silver, Platinum, and Palladium) from this RSS source. This plugin is delicated for Woocommcerce plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      5.6 or higher
 * Author:            Khoa Nguyen
 * Text Domain:       arpu
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARPU_VERSION', '1.0.0' );
define( 'ARPU_NAME', 'arpu' );
define( 'ARPU_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARPU_URL', plugin_dir_url( __FILE__ ) );

require 'includes/class-singleton.php';
require 'includes/class-arpu.php';

if ( Arpu\Arpu::is_not_enabled_woocommcere() ) {
	add_action( 'admin_notices', array( Arpu\Arpu::class, 'noitice' ), 10, 1 );
	return;
}

/**
 * The deactivation.
 */
function deactivate_arpu() {
	require_once 'includes/class-deactivator.php';
	Arpu\Deactivator::deactivate();
}

/**
 * The uninstaller.
 */
function uninstall_arpu() {
	require_once 'includes/class-uninstaller.php';
	Arpu\Uninstaller::uninstall();
}

register_deactivation_hook( __FILE__, 'deactivate_arpu' );
register_uninstall_hook( __FILE__, 'uninstall_arpu' );

/**
 * Start this plugin.
 */
function init_arpu() {
	Arpu\Arpu::init();
}

add_action( 'woocommerce_loaded', 'init_arpu', 10, 1 );
