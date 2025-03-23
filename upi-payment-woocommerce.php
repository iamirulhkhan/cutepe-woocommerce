<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cutepe.com/
 * @since             1.0.0
 * @package           Upi_Payment_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       CutePe Gateway Woocommerce
 * Plugin URI:        https://cutepe.com/dashboard/plugins
 * Description:       Get Payment on your own UPI ID without any Transaction charges, with simple subscription.
 * Version:           1.0.0
 * Author:            CutePe
 * Author URI:        https://cutepe.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cutepe-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'UPI_PAYMENT_WOOCOMMERCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-upi-payment-woocommerce-activator.php
 */
function activate_upi_payment_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upi-payment-woocommerce-activator.php';
	Upi_Payment_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-upi-payment-woocommerce-deactivator.php
 */
function deactivate_upi_payment_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upi-payment-woocommerce-deactivator.php';
	Upi_Payment_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_upi_payment_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_upi_payment_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-upi-payment-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_upi_payment_woocommerce() {

	$plugin = new Upi_Payment_Woocommerce();
	$plugin->run();

}
run_upi_payment_woocommerce();
