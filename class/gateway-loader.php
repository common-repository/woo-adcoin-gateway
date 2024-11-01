<?php
namespace WooGatewayACC;
defined('ABSPATH') or exit;

class GatewayLoader {
	
	public function __construct() {
		require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/gateway.php';
		add_filter('woocommerce_payment_gateways', array($this, 'payment_gateways'));
	}
	
	/**
	 * Register the ACC payment method.
	 *
	 * @param array $methods Payment methods.
	 * @return array Payment methods
	 */
	public function payment_gateways($methods) {
		$methods[] = 'WooGatewayACC\Gateway';
		return $methods;
	}
}
?>