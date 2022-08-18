<?php
/**
 * Growdev_Admin_Meta_Boxes
 *
 * @author      Shop Plugins
 * @category    Admin
 * @version     1.0.0
 * @package     ShopPlugins\WooCommerce_Redirect_Thank_You
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WCRTY_Admin_Meta_Boxes
 */
class WCRTY_Admin_Meta_Boxes {

	/**
	 * WCRTY_Admin_Meta_Boxes constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 30 );
		// Save data.
		add_action( 'woocommerce_process_product_meta', 'WCRTY_Meta_Box_Redirect::save', 10, 2 );

		add_action( 'woocommerce_product_after_variable_attributes', 'WCRTY_Meta_Box_Redirect::variation_output', 20, 3 );
		add_action( 'woocommerce_save_product_variation', 'WCRTY_Meta_Box_Redirect::variation_save', 10, 2 );
	}

	/**
	 * Add Meta Box.
	 */
	public function add_meta_box() {
		add_meta_box( 'growdev-thankyou-redirect', __( 'Custom Thank You Page', 'woocommerce-redirect-thank-you' ), 'WCRTY_Meta_Box_Redirect::output', 'product', 'side', 'default' );
	}
}

new WCRTY_Admin_Meta_Boxes();
