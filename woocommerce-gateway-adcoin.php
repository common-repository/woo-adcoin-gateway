<?php
/*
Plugin Name:       AdCoin Payments for WooCommerce
Plugin URI:        http://www.getadcoin.com/
Description:       Allow your customers to pay with AdCoins.
Version:           0.9.9
Author:            Adcoin Click B.V.
Author URI:        http://www.getadcoin.com
*/
defined('ABSPATH') or exit;
define('WOO_GATEWAY_ACC_BASE_PATH', plugin_dir_path(__FILE__));
define('WOO_GATEWAY_ACC_BASE_URL', plugin_dir_url(__FILE__));

if (!class_exists('AdCoin\\Exception\\ClientException')) {
	require_once WOO_GATEWAY_ACC_BASE_PATH . 'wallet-api-wrapper/Exception/ClientException.php';
	require_once WOO_GATEWAY_ACC_BASE_PATH . 'wallet-api-wrapper/API/PaymentGateway.php';
}
require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/gateway-loader.php';
require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/payment-token.php';
require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/debug-router.php';
require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/exceptions.php';
require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/acc-price.php';
require_once WOO_GATEWAY_ACC_BASE_PATH . 'class/plugin.php';

WooGatewayACC\Plugin::get_instance();
?>