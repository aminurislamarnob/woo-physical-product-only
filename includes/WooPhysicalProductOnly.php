<?php

namespace WeLabs\WooPhysicalProductOnly;

/**
 * WooPhysicalProductOnly class
 *
 * @class WooPhysicalProductOnly The class that holds the entire WooPhysicalProductOnly plugin
 */
final class WooPhysicalProductOnly {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.0.1';

    /**
     * Instance of self
     *
     * @var WooPhysicalProductOnly
     */
    private static $instance = null;

    /**
     * Holds various class instances
     *
     * @since 2.6.10
     *
     * @var array
     */
    private $container = [];

    /**
     * Plugin dependencies
     *
     * @since 2.6.10
     *
     * @var array
     */
    private const WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES = [
        'plugins' => [
            'woocommerce/woocommerce.php',
            // 'dokan-lite/dokan.php',
            // 'dokan-pro/dokan-pro.php'
        ],
        'classes' => [
            'Woocommerce',
            // 'WeDevs_Dokan',
            // 'Dokan_Pro'
        ],
        'functions' => [
            // 'dokan_admin_menu_position'
        ],
    ];

    /**
     * Constructor for the WooPhysicalProductOnly class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( WOO_PHYSICAL_PRODUCT_ONLY_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( WOO_PHYSICAL_PRODUCT_ONLY_FILE, [ $this, 'deactivate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        add_action( 'woocommerce_flush_rewrite_rules', [ $this, 'flush_rewrite_rules' ] );
    }

    /**
     * Initializes the WooPhysicalProductOnly() class
     *
     * Checks for an existing WooPhysicalProductOnly instance
     * and if it doesn't find one then create a new one.
     *
     * @return WooPhysicalProductOnly
     */
    public static function init() {
        if ( self::$instance === null ) {
			self::$instance = new self();
		}

        return self::$instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 2.6.10
     *
     * @param string $prop
     *
     * @return Class Instance
     */
    public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
		}
    }

    /**
     * Placeholder for activation function
     *
     * Nothing is being called here yet.
     */
    public function activate() {
        // Check woo_physical_product_only dependency plugins
        if ( ! $this->check_dependencies() ) {
            wp_die( $this->get_dependency_message() );
        }

        // Rewrite rules during woo_physical_product_only activation
        if ( $this->has_woocommerce() ) {
            $this->flush_rewrite_rules();
        }

        // Update woocommerce_myaccount_downloads_endpoint to disable downloads page from myaccount
        update_option( 'woocommerce_myaccount_downloads_endpoint', '' );
    }

    /**
     * Flush rewrite rules after woo_physical_product_only is activated or woocommerce is activated
     *
     * @since 3.2.8
     */
    public function flush_rewrite_rules() {
        // fix rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {
        // Update woocommerce_myaccount_downloads_endpoint to enable downloads page on myaccount
        update_option( 'woocommerce_myaccount_downloads_endpoint', 'downloads' );
    }

    /**
     * Define all constants
     *
     * @return void
     */
    public function define_constants() {
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_PLUGIN_VERSION', $this->version );
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_DIR', dirname( WOO_PHYSICAL_PRODUCT_ONLY_FILE ) );
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_INC_DIR', WOO_PHYSICAL_PRODUCT_ONLY_DIR . '/includes' );
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_TEMPLATE_DIR', WOO_PHYSICAL_PRODUCT_ONLY_DIR . '/templates' );
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_PLUGIN_ASSET', plugins_url( 'assets', WOO_PHYSICAL_PRODUCT_ONLY_FILE ) );

        // give a way to turn off loading styles and scripts from parent theme
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_LOAD_STYLE', true );
        $this->define( 'WOO_PHYSICAL_PRODUCT_ONLY_LOAD_SCRIPTS', true );
    }

    /**
     * Define constant if not already defined
     *
     * @param string      $name
     * @param string|bool $value
     *
     * @return void
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
		}
    }

    /**
     * Load the plugin after WP User Frontend is loaded
     *
     * @return void
     */
    public function init_plugin() {
        // Check woo_physical_product_only dependency plugins
        if ( ! $this->check_dependencies() ) {
            add_action( 'admin_notices', [ $this, 'admin_error_notice_for_dependency_missing' ] );
            return;
        }

        $this->includes();
        $this->init_hooks();

        do_action( 'woo_physical_product_only_loaded' );
    }

    /**
     * Initialize the actions
     *
     * @return void
     */
    public function init_hooks() {
        // initialize the classes
        add_action( 'init', [ $this, 'init_classes' ], 4 );
        add_action( 'plugins_loaded', [ $this, 'after_plugins_loaded' ] );
    }

    /**
     * Include all the required files
     *
     * @return void
     */
    public function includes() {
        // include_once STUB_PLUGIN_DIR . '/functions.php';
    }

    /**
     * Init all the classes
     *
     * @return void
     */
    public function init_classes() {
        $this->container['scripts'] = new Assets();
        $this->container['disable_virtual_product_feature'] = new DisableVirtualProduct();
    }

    /**
     * Executed after all plugins are loaded
     *
     * At this point woo_physical_product_only Pro is loaded
     *
     * @since 2.8.7
     *
     * @return void
     */
    public function after_plugins_loaded() {
        // Initiate background processes and other tasks
    }

    /**
     * Check whether woocommerce is installed and active
     *
     * @since 2.9.16
     *
     * @return bool
     */
    public function has_woocommerce() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Check whether woocommerce is installed
     *
     * @since 3.2.8
     *
     * @return bool
     */
    public function is_woocommerce_installed() {
        return in_array( 'woocommerce/woocommerce.php', array_keys( get_plugins() ), true );
    }

    /**
     * Check plugin dependencies
     *
     * @return boolean
     */
    public function check_dependencies() {
        if ( array_key_exists( 'plugins', self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES ) && ! empty( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['plugins'] ) ) {
            for ( $plugin_counter = 0; $plugin_counter < count( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['plugins'] ); $plugin_counter++ ) {
                if ( ! is_plugin_active( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['plugins'][ $plugin_counter ] ) ) {
                    return false;
                }
            }
        } elseif ( array_key_exists( 'classes', self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES ) && ! empty( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['classes'] ) ) {
            for ( $class_counter = 0; $class_counter < count( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['classes'] ); $class_counter++ ) {
                if ( ! class_exists( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['classes'][ $class_counter ] ) ) {
                    return false;
                }
            }
        } elseif ( array_key_exists( 'functions', self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES ) && ! empty( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['functions'] ) ) {
            for ( $func_counter = 0; $func_counter < count( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['functions'] ); $func_counter++ ) {
                if ( ! function_exists( self::WOO_PHYSICAL_PRODUCT_ONLY_DEPENEDENCIES['functions'][ $func_counter ] ) ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Dependency error message
     *
     * @return void
     */
    protected function get_dependency_message() {
        return __( 'Woo Physical Product Only plugin is enabled but not effective. It requires dependency plugins to work.', 'woo-physical-product-only' );
    }

    /**
     * Admin error notice for missing dependency plugins
     *
     * @return void
     */
    public function admin_error_notice_for_dependency_missing() {
        $class = 'notice notice-error';
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $this->get_dependency_message() ) );
    }

    /**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WOO_PHYSICAL_PRODUCT_ONLY_FILE ) );
	}

    /**
     * Get the template file path to require or include.
     *
     * @param string $name
     * @return string
     */
    public function get_template( $name ) {
        $template = untrailingslashit( WOO_PHYSICAL_PRODUCT_ONLY_TEMPLATE_DIR ) . '/' . untrailingslashit( $name );

        return apply_filters( 'woo-physical-product-only_template', $template, $name );
    }
}
