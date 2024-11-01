<?php
namespace WooGatewayACC;
defined('ABSPATH') or exit;

class Gateway extends \WC_Payment_Gateway {
	
	// Extension settings
	private $api_key;
	private $payment_description;
	public  $debug_mode;
	public  $sandbox;
	
	
	
	public function __construct() {
		// Extension properties
		$this->id                 = 'acc_gateway';
		$this->method_title       = 'AdCoin';
		$this->method_description = 'Allow your customers to pay through the AdCoin web wallet.';
		$this->title              = $this->method_title;
		$this->icon               = WOO_GATEWAY_ACC_BASE_URL . 'assets/img/logo.png';
		$this->has_fields         = false;
		$this->description        = 'Pay through your AdCoin web wallet. <a href="http://www.getadcoin.com">More information</a>';
		
		// Initialize extension setting
		$this->init_form_fields();
		$this->init_settings();
		$this->enabled             = $this->get_option('enabled');
		$this->api_key             = trim($this->get_option('api_key'));
		$this->payment_description = $this->get_option('payment_description');
		$this->thankyou_text       = $this->get_option('thankyou_text');
		$this->sandbox             = $this->get_option_checkbox('sandbox');
		$this->debug_mode          = $this->get_option_checkbox('debug_mode');
		
		// Show admin configuration notice if API key has not been set
		if (empty($this->api_key))
			add_action('admin_notices', array($this, 'show_notice_runonce'));
		
		// Register callbacks
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_api_woo_gateway_acc', array($this, 'payment_callback'));
		add_action('woocommerce_thankyou_'.$this->id, array($this, 'thankyou'), 1, 1);
		add_filter('woocommerce_thankyou_order_received_text', array($this, 'thankyou_order_received_text'), 10, 2);
	}
	
	
	
	/**
	 * Fetches the value of a checkbox from the extension's settings.
	 *
	 * @param  string $name Option name.
	 *
	 * @return bool         Whether the checkbox had been checked.
	 */
	private function get_option_checkbox($name) {
		return 'y' == $this->get_option($name)[0] ? true : false;
	}
	
	
	
	/**
	 * Initialize the extension's admin settings' form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => 'Enable',
				'type'    => 'checkbox',
				'label'   => 'Enable WooCommerce AdCoin Gateway',
				'default' => 'no'
			),
			'api_key' => array(
				'title'       => 'API key',
				'type'        => 'text',
				'placeholder' => 'Your AdCoin Wallet API key',
				'description' => 'Step 1) Log in to your <a href="https://wallet.getadcoin.com">AdCoin Wallet</a> or create an account.<br>'.
				                 'Step 2) Go to the <a href="https://wallet.getadcoin.com/settings/api">API Key page</a>.<br>'.
								 'Step 3) Copy the big red code on the API Key page into the field above.',
				'default'     => '',
				'required'    => 'true'
			),
			'payment_description' => array(
				'title'       => 'Payment description',
				'type'        => 'text',
				'description' => 'Your text to be shown on the AdCoin payment page.',
				'default'     => 'Pay with AdCoin.'
			),
			'thankyou_text' => array(
				'title'       => 'Order received message',
				'type'        => 'text',
				'description' => 'Customize the extra message shown on the thank you page when the customer has paid using AdCoin.',
				'default'     => 'Thank you for choosing to pay with AdCoin.<br>'.
				                 'We will send you a notification through email once the payment has been confirmed.<br>'.
				                 'This process can take up to an hour.'
			),
			'sandbox' => array(
				'title'   => 'Sandbox mode',
				'type'    => 'checkbox',
				'label'   => 'WARNING: Don\'t enable this unless you are testing the plugin.',
				'description' => 'Disables payments on the AdCoin payment gateway',
				'default' => 'no'
			),
			'debug_mode' => array(
				'title'   => 'Debug mode',
				'type'    => 'checkbox',
				'label'   => 'WARNING: Developers only.',
				'default' => 'no'
			)
		);
	}
	
	
	
	/**
	 * Callback function for when the payment has been confirmed by the AdCoin
	 * payment gateway.
	 */
	public function payment_callback() {
		
		if ($this->debug_mode)
			DebugRouter::DumpPostData();
		
		$logger  = \wc_get_logger();
		$context = array('source' => $this->id);
		
		try {
			// Check whether the required POST data is present
			if (!isset($_POST['id'],
					   $_POST['created_at'],
					   $_POST['status'],
					   $_POST['metadata'],
					   $_POST['hash'])) {
				throw new BadRequestException('Request lacks required data');
			}
			
			// Check whether the provided hash matches the provided POST data
			$query = http_build_query(array(
				'id'         => $_POST['id'],
				'created_at' => $_POST['created_at'],
				'status'     => $_POST['status'],
				'metadata'   => stripslashes($_POST['metadata'])
			));
			$queryHash = hash_hmac('sha512', $query, $this->api_key);
			if (!$this->sandbox && ($_POST['hash'] != $queryHash))
				throw new BadRequestException('Provided hash does not match POST data');
			
			// Decode metadata and check whether required metadata is present
			$metadata = json_decode(stripslashes($_POST['metadata']), true);
			if (NULL == $metadata)
				throw new BadRequestException('Invalid metadata provided');
			if (!isset($metadata['order_id']))
				throw new BadRequestException('Order ID not given in metadata');
			
			// Check whether the provided order ID is correct and check status
			$order = \wc_get_order($metadata['order_id']);
			if (!$order)
				throw new BadRequestException('Invalid order ID provided');
			
			// Check payment status
			switch ($_POST['status']) {
			case 'paid': break;
			case 'timed_out':
				throw new PaymentTimeOutException('Payment timed out for order ' . $metadata['order_id']);
			default:
				throw new BadRequestException('Given payment status is invalid: "'.$_POST['status'].'"');
			}
			
			// Validate the given payment ID
			if (!$this->sandbox && !PaymentToken::ValidateAndDestroy($metadata['order_id'], $_POST['id'])) {
				throw new BadRequestException('Token validation failed: Invalid order- or payment ID given');
			}
			
			// Complete order
			\wc_reduce_stock_levels($order_id);
			$order->update_status('wc-processing');
			$order->payment_complete();
			
		} catch (ACCException $e) {
			if ($this->sandbox)
				die($e->getMessage());
			$logger->warning($e->getMessage(), $context);
		} finally {
			$logger->info('Payment confirmed for order '.$metadata['order_id'].'.', $context);
		}
	}
	
	
	
	/**
	 * Process payment on checkout. Opens a new payment is on the AdCoin payment
	 * gateway.
	 *
	 * @param int $order_id Order ID as provided by WooCommerce.
	 *
	 * @returns array Result array as expected by WooCommerce.
	 */
	public function process_payment($order_id) {
		try {
			// Calculate order total in ACC
			$order = new \WC_Order($order_id);
			$order_total = $order->get_total();
			$currency    = \get_woocommerce_currency();
			$acc_price   = Plugin::get_instance()->acc_price;
			$acc_price->updatePrice($currency);
			$amount_acc  = $acc_price->priceToAcc($order_total, $currency);
			
			// Open a new payment on the AdCoin payment gateway
			$returnUrl = $this->get_return_url($order);
			$gateway   = new \AdCoin\API\PaymentGateway($this->api_key);
			$payment   = $gateway->openPayment(
				$amount_acc,
				'Total: '.html_entity_decode(\get_woocommerce_currency_symbol($currency)).$order_total.'. '.$this->payment_description,
				$returnUrl,
				trailingslashit(get_bloginfo('wpurl')).'?wc-api=woo_gateway_acc',
				[ 'order_id' => $order_id ]
			);
			
			// Register payment token from returned payment ID
			PaymentToken::Register($order_id, $payment['id']);
			
			// Remove cart
			WC()->cart->empty_cart();
			
			// Return redirection URL
			return array(
				'result'   => 'success',
				'redirect' => $this->sandbox ? $returnUrl : $payment['links']['paymentUrl']
			);
			
		} catch (ACCException $e) {
			error_log('ACCException: ' . $e->getMessage());
			if ($this->debug_mode)
				\wc_add_notice($e->getMessage());
			return array('result' => 'fail');
		}
	}
	
	
	
	/**
	 * Thank you page hook for when the order has been paid with AdCoin.
	 *
	 * @param int $order_id The order ID as provided by WooCommerce.
	 */
	public function thankyou($order_id) {
		$order = \wc_get_order($order_id);
		
		// Check whether the payment had failed
		if ((!isset($_GET['status'])) || ('pending' !== $_GET['status'])) {
			// Cancel order and redirect user
			$order->update_status('wc-cancelled', 'Payment failed');
			wp_redirect($order->get_cancel_order_url());
		}
		
		// Check whether the order is currently awaiting payment
		if ($order->has_status('pending')) {
			// Mark order as paid
			$order->update_status('wc-paid-unconfirmed', 'Paid (Unconfirmed)');
		}
	}
	
	
	
	/**
	 * Custom order received text on the thank you page.
	 */
	public function thankyou_order_received_text($var, $order) {
		return $var . wpautop(wptexturize($this->thankyou_text));
	}
	
	
	
	/**
	 * Show warning admin notice that the administrator should configure the
	 * extension first.
	 */
	public function show_notice_runonce() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong>Thank you for installing the WooCommerce AdCoin Gateway!</strong></p>
			<p>Before you can use the extension. You should provide it with your AdCoin Wallet API key.</p>
			<p><a href="<?php echo admin_url()?>admin.php?page=wc-settings&tab=checkout&section=acc_gateway">Click here to go to the settings page.</a></p>
		</div>
		<?php
	}
}
?>