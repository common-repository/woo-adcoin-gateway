== AdCoin Payments for WooCommerce ==
Contributors: appels, adcoin
Tags: adcoin, acc, cryptocurrency, payment, gateway, checkout, ecommerce, e-commerce, woocommerce, payments, blockchain, cryptocurrency
Requires at least: 4.7
Requires PHP: 5.3
Tested up to: 4.9.4
Stable tag: 0.9.9
License: GPLv2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

== Description ==

Quickly integrate and enable AdCoin Payments in WooCommerce, wherever you need them. Simply drop them ready-made into your WooCommerce webshop with this powerful plugin by AdCoin Click BV. AdCoin is dedicated to making payments better for WooCommerce.

> Cryptocurrency payments, for WooCommerce

No need to spend weeks on paperwork or security compliance procedures. No more lost conversions because you don't support a shopper's local payment method, you need to ask for address data or because they don't feel safe. We made payments intuitive and safe for merchants and their customers.

= GETTING STARTED =

Please go to the [AdCoin Wallet](https://wallet.getadcoin.com/register) to create a new AdCoin Wallet and hit 'Generate API key' under your personal settings tab. Contact support@getadcoin.com if you have any questions or comments about this plugin.

> No startup fees, no monthly fees, and no gateway fees. Receive and send payments instantly and for free. 

= FEATURES =

* Accept payments directly into your personal AdCoin wallet.
* Accept payment in AdCoin for physical, digital downloadable products and/or services.
* Add AdCoin payments option to your existing online store with alternative main currency.
* Automatic conversion to AdCoin via realtime exchange rate feed and calculations.
* Zero fees and no commissions for AdCiub payments processing from any third party.
* [Powerful dashboard](https://wallet.getadcoin.com) on wallet.getadcoin.com to easily keep track of your payments.
* Fast in-house support. You will always be helped by someone who knows our products intimately.

== Frequently Asked Questions ==

= I can't install the plugin, the plugin is displayed incorrectly =

Please temporarily enable the [WordPress Debug Mode](https://codex.wordpress.org/Debugging_in_WordPress). Edit your `wp-config.php` and set the constants `WP_DEBUG` and `WP_DEBUG_LOG` to `true` and try
it again. When the plugin triggers an error, WordPress will log the error to the log file `/wp-content/debug.log`. Please check this file for errors. When done, don't forget to turn off
the WordPress debug mode by setting the two constants `WP_DEBUG` and `WP_DEBUG_LOG` back to `false`.

= I get a white screen when opening ... =

Most of the time a white screen means a PHP error. Because PHP won't show error messages on default for security reasons, the page is white. Please turn on the WordPress Debug Mode to turn on PHP error messages (see previous answer).

= The AdCoin payment gateway isn't displayed in my checkout = 

* Please go to WooCommerce -> Settings -> Checkout in your WordPress admin and click on the AdCoin tab.
* Check 'Enable WooCommerce AdCoin Gateway'
* Scroll down to the footer and hit 'Save changes'

= After receiving a payment, the status is 'Paid (Unconfirmed)' =

The payment is for 99.9% ensured. However, the transaction is not yet confirmed by the AdCoin Blockchain. This will normally take up to 3 hours. 

= Can I carry out my order when the status is unconfirmed? =

Yes, you can. However, there is a change that the blockchain is hacked and the transaction is fake.

== Screenshots ==

1. Please insert your AdCoin API key to start and check 'enable'.
2. The available payment gateways in the checkout.
3. When your customers chooses AdCoin as payment method, the shop will calculate the realtime amount of AdCoin and redirects him to the AdCoin payment gateway.
4. When the customer completes the payment, they will be redirected back to the shop.
5. Order received page.

== Installation ==

= Minimum Requirements =

* PHP version 5.3 or greater
* PHP extensions enabled: cURL
* WordPress 3.8 or greater
* WooCommerce 2.2.0 or greater

= Automatic installation =

1. Install the plugin via Plugins -> New plugin. Search for 'AdCoin Payments for WooCommerce'.
2. Activate the 'AdCoin Payments for WooCommerce' plugin through the 'Plugins' menu in WordPress
3. Set your AdCoin API key at WooCommerce -> Settings -> Checkout -> AdCoin
4. You're done, your customers can now pay with AdCoin in their WooCommerce webshop.

= Manual installation =

1. Unpack the download package
2. Upload the directory 'woocommerce-gateway-adcoin' to the `/wp-content/plugins/` directory
3. Activate the 'AdCoin Payments for WooCommerce' plugin through the 'Plugins' menu in WordPress
4. Set your AdCoin API key at WooCommerce -> Settings -> Checkout -> AdCoin
5. You're done, the active payment methods should be visible in the checkout of your webshop.

Please contact support@getadcoin.com if you need help installing the AdCoin WooCommerce plugin. Please provide your AdCoin Wallet ID and website URL.

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Changelog ==

= 0.9.1 - 02/03/2018 =

* Add - Description and graphic material

= 0.9.0 - 01/03/2018 =

* Initial release
