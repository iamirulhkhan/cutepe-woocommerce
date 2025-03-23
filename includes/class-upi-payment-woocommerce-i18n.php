<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://cutepe.com/
 * @since      1.0.0
 *
 * @package    Upi_Payment_Woocommerce
 * @subpackage Upi_Payment_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Upi_Payment_Woocommerce
 * @subpackage Upi_Payment_Woocommerce/includes
 * @author     Abdul Wahab <rockingwahab9@gmail.com>
 */
class Upi_Payment_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'upi-payment-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
