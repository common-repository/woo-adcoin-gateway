<?php
namespace WooGatewayACC;
defined('ABSPATH') or exit;

// General plugin exception
class ACCException extends \Exception {}

// Checkout
class CheckoutException extends ACCException {}

// Payment callback
class BadRequestException extends ACCException {}
class PaymentTimeOutException extends ACCException {}

// ACCPrice
class BadCurrencyException extends ACCException {}
class CoinMarketCapAPIException extends ACCException {}

?>