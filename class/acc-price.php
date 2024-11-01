<?php

namespace WooGatewayACC;
defined('ABSPATH') or exit;

class ACCPrice {

	// CoinMarketCap supported conversion currencies
	private static $currencies = array(
		'USD', 'AUD', 'BRL', 'CAD', 'CHF', 'CLP', 'CNY', 'CZK',
		'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'IDR', 'ILS', 'INR',
		'JPY', 'KRW', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PKR',
		'PLN', 'RUB', 'SEK', 'SGD', 'THB', 'TRY', 'TWD', 'ZAR'
	);

	// ACC price table
	private $prices;



	/**
	 * Constructor. Loads the stored latest AdCoin price.
	 */
	public function __construct() {
		add_option('woo_acc_gateway_acc_prices');
		$this->prices = get_option('woo_acc_gateway_acc_prices');
		if (!$this->prices || (isset($this->prices) && empty($this->prices))) {
			$this->updatePrice(\get_woocommerce_currency());
		}
	}

	/**
	 * Fetches the latest AdCoin price from the CoinMarketCap API and stores it
	 * for later use.
	 *
	 * @param string $currency The currency to update.
	 *
	 * @throws BadCurrencyException      If the provided currency was invalid.
	 * @throws CoinMarketCapAPIException If the API request failed.
	 */
	public function updatePrice($currency) {
		// Check given currency code
		if (!in_array($currency, self::$currencies))
			throw new BadCurrencyException('Invalid currency code '.$currency.' provided');

		// Send CoinMarketCap API request
		global $wp_version;
		$response = wp_remote_get(
			'https://api.coinmarketcap.com/v1/ticker/adcoin/?convert='.$currency,
			array(
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => true,
				'headers'     => array(),
				'cookies'     => array(),
				'body'        => null,
				'compress'    => false,
				'decompress'  => true,
				'sslverify'   => true,
				'stream'      => false,
				'filename'    => null
			)
		);
		$body = wp_remote_retrieve_body($response);

		// Check HTTP status
		$status = wp_remote_retrieve_response_code($response);
		if ($status < 200 || $status >= 300)
			throw new CoinMarketCapAPIException($body, $status);

		// Decode API response
		$responseDecoded = json_decode($body, true);

		// Store fetched prices
		$this->prices['USD']     = (float)$responseDecoded[0]['price_usd'];
		$this->prices[$currency] = (float)$responseDecoded[0]['price_'.strtolower($currency)];
		update_option('woo_acc_gateway_acc_prices', $this->prices);
	}

	/**
	 * Converts a price to ACC.
	 *
	 * @param float  $price    Price to convert.
	 * @param string $currency The currency to convert from. Must be a supported
	 *                         currency code.
	 * @throws BadCurrencyException When the given currency code had no price
	 *                              assigned to it.
	 *
	 * @return float Converted price in ACC.
	 */
	public function priceToAcc($price, $currency) {
		if (!array_key_exists($currency, $this->prices))
			throw new BadCurrencyException('AdCoin price for '.$currency.' not found');
		return $price / $this->prices[$currency];
	}
}
?>