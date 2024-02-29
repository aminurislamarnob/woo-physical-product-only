<?php
/**
 * Plugin Name: Woo Physical Product Only
 * Plugin URI:  https://aiarnob.com/
 * Description: Enable this plugin to restrict the sale of virtual and downloadable products within WooCommerce. Ideal for physical product-based stores, it ensures that only tangible goods are available for purchase.
 * Version: 0.0.1
 * Author: Aminur Islam Arnob
 * Author URI: https://aiarnob.com/
 * Text Domain: woo-physical-product-only
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * License: GPL2
 */
use WeLabs\WooPhysicalProductOnly\WooPhysicalProductOnly;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WOO_PHYSICAL_PRODUCT_ONLY_FILE' ) ) {
    define( 'WOO_PHYSICAL_PRODUCT_ONLY_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Woo_Physical_Product_Only Plugin when all plugins loaded
 *
 * @return \WeLabs\WooPhysicalProductOnly\WooPhysicalProductOnly
 */
function welabs_woo_physical_product_only() {
    return WooPhysicalProductOnly::init();
}

// Lets Go....
welabs_woo_physical_product_only();
