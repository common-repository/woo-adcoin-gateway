<?php
namespace WooGatewayACC;
defined('ABSPATH') or exit;

class DebugRouter {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		/*
		// Register debug routes
		add_action('rest_api_init', array($this, 'init_routes'));
		*/
	}
	
	/**
	 * Register debug routes.
	 */
	public function init_routes() {
		/*
		// /wp-json/woo_gateway_acc/pay/<int Order ID>
		register_rest_route(
			'woo_gateway_acc', '/pay/(?P<order_id>\d+)', array(
				'methods' => 'GET',
				'callback' => array($this, 'route_pay')
			)
		);
		*/
	}
	
	/**
	 * Spoofs a payment request to the payment webhook.
	 */
	public function route_pay(\WP_REST_Request $request) {
		/*
		// Create POST fields string
		$metadata = array('order_id' => $request['order_id']);
		$fields = array(
			'id'         => '143bcb7c-1bc1-11e8-a6b0-5254004def45',
			'created_at' => urlencode('2018-02-21T16:29:09+01:00'),
			'status'     => 'paid',
			'metadata'   => json_encode($metadata),
			'hash'       => '13994e9dd972876e4fa426d37dbda9577fa87ebd6f2c278261685d8d0e085c35dbf48401d253c92d43940862b013ef49821147bd38b8c4db3a206bf1b7ae0e44'
		);
		
		// Prepare request
		$url = trailingslashit(get_bloginfo('wpurl')).'?wc-api=woo_gateway_acc';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields));
		
		// Execute and close request
		$result = curl_exec($curl);
		curl_close($curl);
		*/
	}
	
	/**
	 * Dumps all elements in $_POST to a text file.
	 */
	public static function DumpPostData() {
		// Open a new file for writing
		$filename = (string)time() . '.txt'; // {Unix timestamp}.txt
		$file = fopen(WOO_GATEWAY_ACC_BASE_PATH . $filename, 'w');
		if (!$file) {
			error_log('An error occurred while opening file of name "' . $filename . '".');
			return;
		}

		// Write POST fields to file and close handle
		fwrite($file, print_r($_POST, true));
		fclose($file);
	}
}
?>