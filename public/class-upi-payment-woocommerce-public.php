<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cutepe.com/
 * @since      1.0.0
 *
 * @package    Upi_Payment_Woocommerce
 * @subpackage Upi_Payment_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Upi_Payment_Woocommerce
 * @subpackage Upi_Payment_Woocommerce/public
 * @author     Abdul Wahab <rockingwahab9@gmail.com>
 */
class Upi_Payment_Woocommerce_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Upi_Payment_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Upi_Payment_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/upi-payment-woocommerce-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Upi_Payment_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Upi_Payment_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/upi-payment-woocommerce-public.js', array('jquery'), $this->version, false);
	}


	public function woocommerce_payment_gateways($methods)
	{
		$methods[] = 'UPI_Payment_Gateway';
		return $methods;
	}

	public function template_redirect()
	{
		if (isset($_GET['txn_id']) && isset($_GET['order_id'])) {
			$id = $_GET['order_id'];
			$order = wc_get_order($id);
			$order_key = $order->get_order_key();
			$url = home_url("checkout/order-received/$id/?key=$order_key");

			wp_redirect($url);
			//var_dump($_GET);
			die;
		}
	}
}

add_action('plugins_loaded', function () {

	class UPI_Payment_Gateway extends WC_Payment_Gateway
	{

		public function __construct()
		{

			$this->id = 'upi-payment'; // payment gateway ID
			$this->icon = ''; // payment gateway icon
			$this->has_fields = false; // for custom credit card form
			$this->title = __('CutePe', 'text-domain'); // vertical tab title
			$this->method_title = __('CutePe', 'text-domain'); // payment method name
			$this->method_description = __('Custom UPI Payment', 'text-domain'); // payment method description


			// load backend options fields
			$this->init_form_fields();

			// load the settings.
			$this->init_settings();
			$this->title = $this->get_option('title');
			$this->description = $this->get_option('description');
			$this->enabled = $this->get_option('enabled');
			//$this->test_mode = 'yes' === $this->get_option( 'test_mode' );
			//$this->private_key = $this->test_mode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
			$this->publish_key =  $this->get_option('publish_key');
			$this->default_email =  $this->get_option('default_email');

			add_action('woocommerce_api_' . $this->id, array($this, 'check_h_payment_response'));

			// Action hook to saves the settings
			if (is_admin()) {
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			}
		}

		public function check_h_payment_response()
		{
			update_option('aw_upi', $_REQUEST);
			if (isset($_REQUEST['status'])) {


				$id = $_REQUEST['udf1'];
				$order = wc_get_order($id);
				if ($_REQUEST['status'] == 'success') {
					$order->payment_complete();
					$order->reduce_order_stock();

					$order->add_order_note('UPI Payment completed!', true);
				} else {
					$order->update_status('failed');
					$order->add_order_note('UPI Payment Failed!', true);
				}
			}
		}

		public function init_form_fields()
		{
			$this->form_fields = array(
				'enabled' => array(
					'title'       => __('Enable/Disable', 'text-domain'),
					'label'       => __('Enable CutePe', 'text-domain'),
					'type'        => 'checkbox',
					'description' => __('This enable the CutePe which allow to accept payment through UPI.', 'text-domain'),
					'default'     => 'no',
					'desc_tip'    => true
				),
				'title' => array(
					'title'       => __('Title', 'text-domain'),
					'type'        => 'text',
					'description' => __('This controls the title which the user sees during checkout.', 'text-domain'),
					'default'     => __('UPI Payment', 'text-domain'),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __('Description', 'text-domain'),
					'type'        => 'textarea',
					'description' => __('This controls the description which the user sees during checkout.', 'text-domain'),
					'default'     => __('UPI.', 'text-domain'),
				),

				'publish_key' => array(
					'title'       => __('API Key', 'text-domain'),
					'type'        => 'text'
				),

				'default_email' => array(
					'title'       => __('Default Email', 'text-domain'),
					'type'        => 'text',
					'description' => __('Default email is used when user is not logged in and making payment.', 'text-domain'),
				),

				'ipn' => array(
					'title' => 'Webhook URL',
					'type' => 'hidden',
					'description' => 'Go to <a href="https://cutepe.com/dashboard/api-keys" target="_blank">CutePe  > Webhooks</a> and click on "API & Docs" > "API Credentials" and enter the following URL in webhooks: ' . site_url('wc-api/upi-payment')
				),
				'account_details' => array(
					'type' => 'hidden',
					'description' => '<img src="' . plugin_dir_url(__FILE__) . 'upi-image.jpg"/>',
				),

			);
		}

		public function genrate_aw_image_html()
		{
			echo 123;
		}


		public function process_payment($order_id)
		{

			global $woocommerce;

			// get order detailes
			$order = wc_get_order($order_id);
			$total = $order->get_total();
			$key = $this->publish_key;
			$default_email = $this->default_email;

			$p_name = '';
			foreach ($order->get_items() as $item_id => $item) {

				$product_name = $item->get_name();

				$p_name .= $product_name . ',';
			}
			$url = $order->get_checkout_order_received_url();
			$order_key = $order->get_order_key();

			$url = home_url("checkout/order-received/$order_id/?key=$order_key&order_id=$order_id&test");
			$billing_emails = $order->get_billing_email();
			if (!$billing_emails || $billing_emails == '' || $billing_emails == null) {
				$billing_emails = $default_email;
			}
			
			$phone = substr($order->get_billing_phone(), -10);
			if(strlen($phone)!=10){
			    $phone = strval(mt_rand(1000000000, 9999999999));;
			}else{
			    $phone = substr($order->get_billing_phone(), -10);
			}

			$data = [
				"txn_id" => time(),
				"amount" => (float) $total,
				"p_info" => substr($p_name, 0, 95),
				"customer_name" => substr($order->get_formatted_billing_full_name(), 0, 94),
				"customer_email" => substr($billing_emails, 0, 94),
				"customer_mobile" =>  $phone,
				"redirect_url" => $url,
				"udf1" => $order_id,
				"udf2" => $order_key,
				"udf3" => substr(home_url(), 0, 94),
				// "merchant_key" => "paytm",
			];

			$response = wp_remote_post("https://merchants.cutepe.com/api/orders/create-order", array(
				'headers'     => array(
				                        // 'Content-Type' => 'application/json; charset=utf-8',
				                        'Authorization'=> "Bearer $key" ),
				'body'        => $data,
				'method'      => 'POST',
			));

			if (!is_wp_error($response)) {

				$body = json_decode($response['body'], true);

				// it could be different depending on your payment processor
				if ($body['status'] == "success") {


					$id = $body['order_id'];
					update_post_meta($order_id, 'upi_order_id', $id);
					$payment_url = $body['payment_url'];


					$order->add_order_note('Payment throught UPI pending', false);

					// empty cart
					$woocommerce->cart->empty_cart();

					// redirect to the thank you page
					return array(
						'result' => 'success',
						'redirect' => $payment_url
					);
				} else {
					// 			$h = '';
					// 			ob_start();
					// 			var_dump($total);
					// 			var_dump($data);
					// 			var_dump($response);
					// 			$h = ob_get_contents();
					// 			ob_clean();
					//  wc_add_notice(  $h, 'error' );
					if ($body) {
						wc_add_notice($body['message'], 'error');
					} else {
						wc_add_notice('Please try again.', 'error');
					}
					return;
				}
			} else {
				wc_add_notice('Connection error.', 'error');
				return;
			}
		}
	}
});
