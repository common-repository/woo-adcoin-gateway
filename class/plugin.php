<?php
namespace WooGatewayACC;
defined('ABSPATH') or exit;

class Plugin {
	
	// Class objects
	public $gateway_loader;
	public $debug_router;
	public $acc_price;
	
	// Singleton instance
	private static $instance;
	
	/**
	 * Singleton instance getter.
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {
		if (!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'register_order_statuses'));
		add_action('plugins_loaded', array($this, 'init'));
		add_action('admin_enqueue_scripts', array($this, 'load_admin_css'));
		
		add_filter('wc_order_statuses', array($this, 'add_order_statuses'));
		add_filter(
			'woocommerce_valid_order_statuses_for_payment_complete',
			array($this, 'add_order_statuses_for_payment_complete')
		);
	}
	
	/**
	 * Initialize plugin.
	 */
	public function init() {
		// Make class objects
		$this->gateway_loader = new GatewayLoader();
		$this->debug_router   = new DebugRouter();
		$this->acc_price      = new ACCPrice();
	}
	
	/**
	 * Register custom order statuses.
	 */
	public function register_order_statuses() {
		$label_count = 'Paid (Unconfirmed) <span class="count">(%s)</span>';
		register_post_status('wc-paid-unconfirmed', array(
			'label'                     => 'Paid (Unconfirmed)',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => false,
			'label_count'               => _n_noop($label_count, $label_count)
		));
	}
	
	/**
	 * Add custom order statuses to array.
	 */
	public function add_order_statuses($order_statuses) {
		$order_statuses['wc-paid-unconfirmed'] = 'Paid (Unconfirmed)';
		return $order_statuses;
	}
	
	/**
	 * Add custom order statuses to the order statuses for payment completion.
	 */
	public function add_order_statuses_for_payment_complete($order_statuses) {
		$order_statuses[] = 'wc-paid-unconfirmed';
		return $order_statuses;
	}
	
	/**
	 * Load custom admin CSS stylesheet.
	 */
	public function load_admin_css() {
		wp_enqueue_style('woocommerce-gateway-adcoin-admin-style', WOO_GATEWAY_ACC_BASE_URL . '/assets/css/style.css');
	}
}
?>