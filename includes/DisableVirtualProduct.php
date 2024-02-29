<?php

namespace WeLabs\WooPhysicalProductOnly;

class DisableVirtualProduct {
    /**
     * The constructor.
     */
    public function __construct() {
        add_action( 'admin_head', [ $this, 'add_css_to_admin_head' ] );
        add_filter( 'product_type_options', [ $this, 'remove_downloadable_virtual_product_type_options' ] );
        add_filter( 'woocommerce_products_admin_list_table_filters', [ $this, 'remove_downloadable_virtual_dropdown_options' ] );
        add_filter( 'add_meta_boxes', [ $this, 'remove_downloads_meta_box' ], 999 );
    }

    public function add_css_to_admin_head() {
        ?>
        <style>
            #woocommerce-product-data .hndle label:first-child{
                border-right: 0;
            }
        </style>
        <?php
    }

    /**
     * Remove "Virtual" and "Downloadable" checkbox
     *
     * @return void
     */
    public function remove_downloadable_virtual_product_type_options( $options ) {

		if ( isset( $options['virtual'] ) ) {
			unset( $options['virtual'] );
		}

		if ( isset( $options['downloadable'] ) ) {
			unset( $options['downloadable'] );
		}
		return $options;
    }

    /**
     * Remove Downloadable and Virtual Dropdown Options from Product Type Filter
     *
     * @return array
     */
    public function remove_downloadable_virtual_dropdown_options( $filters ) {
        if ( isset( $filters['product_type'] ) ) {
            $filters['product_type'] = [ $this, 'downloadable_virtual_product_type_callback' ];
        }
        return $filters;
    }

    /**
     * Calback function of Remove Downloadable and Virtual Dropdown Options from Product Type Filter
     *
     * @return void
     */
    public function downloadable_virtual_product_type_callback() {
        $current_type = isset( $_REQUEST['product_type'] ) ? wc_clean( wp_unslash( $_REQUEST['product_type'] ) ) : false;
        ?>
            <select name="product_type" id="dropdown_product_type">
                <option value="">Filter by product type</option>
                <?php foreach ( wc_get_product_types() as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $current_type ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php
    }

    /**
     * Remove metabox “Downloadable product permissions” from edit order pages.
     *
     * @return void
     */
    public function remove_downloads_meta_box() {
        remove_meta_box( 'woocommerce-order-downloads', 'shop_order', 'normal' );
    }
}
