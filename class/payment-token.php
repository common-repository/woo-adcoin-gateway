<?php
namespace WooGatewayACC;
defined('ABSPATH') or exit;

class PaymentToken {
	/**
	 * Registers a payment token.
	 *
	 * @param int    $order_id   WooCommerce order ID.
	 * @param string $payment_id The payment ID returned by the Wallet API when
	 *                           a new payment was requested.
	 *
	 * @throws PaymentException If the given payment ID is already registered.
	 */
	public static function Register($order_id, $payment_id) {
		// check whether payment ID has yet to be registered
		$token = get_post_meta($order_id, 'woo_acc_gateway_payment_token', true);
		if ('' == $token) {
			// register payment ID
			update_post_meta($order_id, 'woo_acc_gateway_payment_token', $payment_id);
		} else {
			throw new PaymentException('Given payment ID is already registered');
		}
	}
	
	/**
	 * Matches the given payment ID with the one that is registered and
	 * unregisters it afterwards.
	 *
	 * @param int    $order_id   WooCommerce order ID.
	 * @param string $payment_id The payment ID to check against.
	 *
	 * @returns bool true if the given order ID had a payment ID assigned to it
	 *               and the given payment ID matches the saved payment ID.
	 */
	public static function ValidateAndDestroy($order_id, $payment_id) {
		// fetch saved payment ID
		$saved_payment_id = get_post_meta($order_id, 'woo_acc_gateway_payment_token', true);
		
		// test saved payment ID against given payment ID
		if ($saved_payment_id != $payment_id)
			return false; // token mismatch
		
		// destroy saved token
		delete_post_meta($order_id, 'woo_acc_gateway_payment_token');
		return true;
	}
}
?>