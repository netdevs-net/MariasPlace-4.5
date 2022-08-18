<?php
/**
 * Plugin Name:     WooCommerce Redirect Thank You
 * Plugin URI:      https://shopplugins.com/plugins/woocommerce-redirect-thank-you/
 * Description:     This plugin allows the WooCommerce store owner to redirect the customer to a different Thank You page based on products purchased.
 * Author:          Shop Plugins
 * Author URI:      https://shopplugins.com
 * Version:         2.0.3
 * Text Domain:     woocommerce-redirect-thank-you
 * Domain Path:     /languages/
 * WC requires at least: 2.6.12
 * WC tested up to:      3.7.0
 *
 * @package ShopPlugins\WooCommerce_Redirect_Thank_you
 */

/**
 * Copyright 2015-2019  Daniel Espinoza  (email: daniel@shopplugins.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define( 'WC_REDIRECT_THANK_YOU_VERSION', '2.0.3' );
define( 'WC_REDIRECT_THANK_YOU_FILE', plugin_basename( __FILE__ ) );
define( 'WC_REDIRECT_THANK_YOU_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_REDIRECT_THANK_YOU_SHOP_PLUGINS_URL', 'https://shopplugins.com' );

// Check items in order to see.
add_filter( 'woocommerce_get_checkout_order_received_url', 'growdev_get_checkout_order_received_url', 10, 2 );
/**
 * Filter the redirect URL and return the custom thank you page URL if a product has it set.
 *
 * @param string   $order_received_url URL for Order.
 * @param WC_Order $order Order Object.
 * @return string
 */
function growdev_get_checkout_order_received_url( $order_received_url, $order ) {

	$items            = $order->get_items();
	$redirect_page_id = 0;
	$redirect_url     = '';

	if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
		$order_id  = $order->get_id();
		$order_key = $order->get_order_key();
	} else {
		$order_id  = $order->id;
		$order_key = $order->order_key;
	}

	$product_redirect_url = '';
	foreach ( $items as $item ) {
		$item_id              = absint( $item['variation_id'] ) ? absint( $item['variation_id'] ) : absint( $item['product_id'] );
		$product_redirect_url = get_post_meta( $item_id, '_redirect_url', true );
		// We have a Variation, but not a redirect URL. Let's check the by Product ID.
		if ( ! $product_redirect_url && absint( $item['variation_id'] ) ) {
			$item_id      = absint( $item['product_id'] );
			$redirect_url = get_post_meta( $item_id, '_redirect_url', true );
		}
		if ( ! $product_redirect_url ) {
			$_id = absint( get_post_meta( $item_id, '_redirect_page_id', true ) );
			if ( 0 < $_id ) {
				$redirect_page_id = $_id;
			}
		} else {
			// We have an URL.
			$redirect_url = $product_redirect_url;
		}
	}

	if ( $redirect_url ) {
		$order_received_url = add_query_arg(
			array(
				'order' => $order_id,
				'key'   => $order_key,
			),
			$redirect_url
		);
	} elseif ( 0 < $redirect_page_id ) {
		$order_received_url = add_query_arg(
			array(
				'order' => $order_id,
				'key'   => $order_key,
			),
			get_permalink( $redirect_page_id )
		);
	} else {

		// Let's first check for the gateway redirects.
		$gateway_redirects    = get_option( 'wcrty_payment_gateways', array() );
		$has_gateway_redirect = false;
		if ( $gateway_redirects && is_array( $gateway_redirects ) ) {
			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$payment_method = $order->get_payment_method();
			} else {
				$payment_method = $order->payment_method;
			}

			foreach ( $gateway_redirects as $gateway_options ) {
				if ( $payment_method === $gateway_options['gateway'] ) {
					$link    = isset( $gateway_options['url'] ) ? $gateway_options['url'] : '';
					$page_id = isset( $gateway_options['page_id'] ) ? absint( $gateway_options['page_id'] ) : 0;
					$type    = isset( $gateway_options['type'] ) ? $gateway_options['type'] : 'custom_link';
					if ( 'custom_link' === $type && $link ) {
						$order_received_url   = add_query_arg(
							array(
								'order' => $order_id,
								'key'   => $order_key,
							),
							$link
						);
						$has_gateway_redirect = true;
					} elseif ( $page_id ) {
						$order_received_url   = add_query_arg(
							array(
								'order' => $order_id,
								'key'   => $order_key,
							),
							get_permalink( $page_id )
						);
						$has_gateway_redirect = true;
					}
					break;
				}
			}
		}

		if ( ! $has_gateway_redirect ) {
			$global_thank_you_page = get_option( 'woocommerce_redirect_thank_you_global', false );
			if ( $global_thank_you_page ) {
				if ( is_numeric( $global_thank_you_page ) ) {
					// Previous versions used Page ID. So let's get back to a full URL.
					$global_thank_you_page = get_permalink( $global_thank_you_page );
				}
				$order_received_url = add_query_arg(
					array(
						'order' => $order_id,
						'key'   => $order_key,
					),
					$global_thank_you_page
				);
			}
		}
	}

	return $order_received_url;
}


// Add meta box to the product page for the Custom Thank You page.
add_action( 'admin_init', 'growdev_include_post_type_handlers' );
/**
 * Include the meta boxes if in the WordPress admin
 *
 * @return void
 */
function growdev_include_post_type_handlers() {
	include 'includes/class-wcrty-admin-meta-boxes.php';
}

/**
 * Shortcode definition to output the order details on the custom thank you pages.
 *
 * @param array $atts Shortcode Attributes.
 * @return string
 */
function growdev_shortcode_order_details( $atts ) {

	if ( ! is_admin() ) {
		ob_start();

		if ( function_exists( 'wc_print_notices' ) ) {
			wc_print_notices();
		}

		$order     = false;
		$order_id  = '';
		$order_key = '';

		if ( isset( $_GET['order'] ) && ! empty( $_GET['order'] ) ) {
			$order_id = wc_clean( absint( $_GET['order'] ) ); // WPCS: CSRF ok, sanitization ok..
		}

		if ( isset( $_GET['key'] ) && ! empty( $_GET['key'] ) ) {
			$order_key = wc_clean( $_GET['key'] ); // WPCS: CSRF ok, sanitization ok.
		}

		// Get the order.
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', $order_key ); // WPCS: CSRF ok, sanitization ok.

		if ( $order_id > 0 ) {
			$order = new WC_Order( $order_id );
			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				if ( $order->get_order_key() !== $order_key ) {
					unset( $order );
				}
			} else {
				if ( $order->order_key !== $order_key ) {
					unset( $order );
				}
			}
		}

		// Empty awaiting payment session.
		unset( WC()->session->order_awaiting_payment );

		// Empty cart
		// this is normally called in wc-cart-functions.php, but we are bypassing the
		// 'order-received' endpoint so need to do this ourselves.
		if ( null !== WC()->cart ) {
			WC()->cart->empty_cart();
		}

		// Payment gateways are not auto loaded yet so need to do that now.
		WC()->payment_gateways();


		if ( isset( $order ) ) {
			wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
		}

		return ob_get_clean();
	}

	return '';
}

add_filter( 'woocommerce_is_order_received_page', 'growdev_is_order_received_page' );

/**
 * Checks if the current page is the order received page.
 *
 * @param boolean $is_page Check if it's an order received page.
 *
 * @return bool
 */
function growdev_is_order_received_page( $is_page ) {
	global $wpdb, $wp;

	if ( ! $is_page && isset( $_GET['order'] ) && isset( $_GET['key'] ) ) {
		// Maybe, it's one of the thank you pages?
		$page_id = get_option( 'woocommerce_redirect_thank_you_global_page', 0 );
		if ( $page_id && is_page( $page_id ) ) {
			$is_page = true;
		} else {
			// Maybe it's not the global one but one of the products?
			$object = get_queried_object();
			if ( is_a( $object, 'WP_Post' ) ) {
				$sql    = "SELECT count(post_id) FROM $wpdb->postmeta WHERE meta_key='_redirect_page_id' AND meta_value=%d";
				$result = $wpdb->get_var( $wpdb->prepare( $sql, $object->ID ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				if ( $result && absint( $result ) ) {
					$is_page = true;
				}
			}

			if ( ! $is_page ) {
				$current_url = home_url( $wp->request );
				if ( $current_url ) {
					$sql    = "SELECT count(post_id) FROM $wpdb->postmeta WHERE meta_key='_redirect_url' AND meta_value=%s";
					$result = $wpdb->get_var( $wpdb->prepare( $sql, untrailingslashit( $current_url ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					if ( $result && absint( $result ) ) {
						$is_page = true;
					}
				}
			}

			if ( ! $is_page ) {
				// Still not a page? Maybe it's from a gateway
				$gateway_redirects = get_option( 'wcrty_payment_gateways', array() );
				if ( $gateway_redirects && is_array( $gateway_redirects ) ) {
					$current_url = home_url( $wp->request );
					$order       = wc_get_order( $_GET['order'] );

					if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
						$payment_method = $order->get_payment_method();
					} else {
						$payment_method = $order->payment_method;
					}
					foreach ( $gateway_redirects as $gateway_options ) {
						if ( $payment_method === $gateway_options['gateway'] ) {
							$link    = isset( $gateway_options['url'] ) ? $gateway_options['url'] : '';
							$page_id = isset( $gateway_options['page_id'] ) ? absint( $gateway_options['page_id'] ) : 0;
							$type    = isset( $gateway_options['type'] ) ? $gateway_options['type'] : 'custom_link';
							if ( 'custom_link' === $type && $link ) {
								if ( $current_url === $link ) {
									$is_page = true;
									break;
								}
							} elseif ( $page_id ) {
								if ( $page_id && is_page( $page_id ) ) {
									$is_page = true;
									break;
								}
							}
						}
					}
				}
			}
		}

		if ( $is_page ) {
			// Adding the query var order-received used by WC and WC extensions.
			$wp->query_vars['order-received'] = absint( $_GET['order'] );
			// Filtering the is_checkout page so it returns true because we are actually on the thankyou page.
			add_filter( 'woocommerce_is_checkout', '__return_true' );
		}
	}

	return $is_page;
}

add_action( 'init', 'growdev_register_shortcodes' );

/**
 * Registering Shortcodes.
 */
function growdev_register_shortcodes() {
	if ( is_admin() ) {
		return;
	}

	// Define shortcode to put on pages to display the order details.
	add_shortcode( 'growdev_order_details', 'growdev_shortcode_order_details' );

	add_shortcode( 'redirect_thank_you_text', 'growdev_shortcode_redirect_thank_you_text' );
}

/**
 * Conditionally showing text.
 *
 * @param array  $atts Attributes.
 * @param string $content Content.
 * @return string
 */
function growdev_shortcode_redirect_thank_you_text( $atts, $content = '' ) {
	$atts = shortcode_atts(
		array(
			'product_id'  => '0',
			'category_id' => '0',
			'on_order'    => 'true',
		),
		$atts,
		'redirect_thank_you_text'
	);

	$is_order_page = filter_var( $atts['on_order'], FILTER_VALIDATE_BOOLEAN );
	$show_text     = false;

	if ( ! $is_order_page && ! is_order_received_page() ) {
		$show_text = true;
	} elseif ( $is_order_page && is_order_received_page() ) {
		$product_id  = absint( $atts['product_id'] );
		$category_id = absint( $atts['category_id'] );

		// There is no specific product or category.
		if ( ! $product_id && ! $category_id ) {
			$show_text = true;
		} else {
			// We search for a specific product or category.
			$order = isset( $_GET['order'] ) ? wc_get_order( absint( $_GET['order'] ) ) : false;
			if ( $order ) {
				foreach ( $order->get_items() as $item ) {

					if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
						// We search for a specific product.
						if ( $product_id ) {
							if ( $product_id === $item->get_product_id() ) {
								$show_text = true;
								break;
							}
						} else {
							// We check the category.
							$product      = $item->get_product();
							$category_ids = $product->get_category_ids();
							if ( in_array( $category_id, $category_ids ) ) {
								$show_text = true;
								break;
							}
						}
					}
				}
			}
		}
	}

	if ( $show_text ) {
		return $content;
	}

	return '';
}

add_action( 'woocommerce_email_order_details', 'wcrty_email_order_details', 20, 4 );

/**
 * Adding text based on the order.
 *
 * @param WC_Order $order Order object.
 * @param boolean  $admin If is sent to admin or not.
 * @param boolean  $plain If is sent as plain or HTML.
 * @param WC_Email $email Email Object.
 */
function wcrty_email_order_details( $order, $admin, $plain, $email ) {
	if ( 'customer_completed_order' !== $email->id ) {
		return;
	}

	$email_text = get_option( 'wcrty_completed_email_texts', array() );

	if ( ! $email_text ) {
		return;
	}

	$product_ids  = array();
	$category_ids = array();

	$items = $order->get_items( 'line_item' );

	foreach ( $items as $item ) {
		$product       = $item->get_product();
		$product_ids[] = $product->get_id();
		$category_ids  = array_merge( $category_ids, $product->get_category_ids() );
	}

	$product_ids  = array_unique( array_filter( $product_ids ) );
	$category_ids = array_unique( array_filter( $category_ids ) );

	$texts = array();

	foreach ( $email_text['type'] as $index => $type ) {
		$condition = isset( $email_text['condition'][ $index ] ) ? $email_text['condition'][ $index ] : '';
		$text      = isset( $email_text['text'][ $index ] ) ? $email_text['text'][ $index ] : '';
		if ( 'product' === $type ) {
			if ( in_array( $condition, $product_ids ) ) {
				$texts[ 'p_' . $condition ] = $text;
			}
		} else {
			if ( in_array( $condition, $category_ids ) ) {
				$texts[ 'c_' . $condition ] = $text;
			}
		}
	}

	if ( $texts ) {
		foreach ( $texts as $text ) {
			if ( ! $plain ) {
				echo '<p>';
			}

			echo $text;

			if ( ! $plain ) {
				echo '</p>';
			} else {
				echo  "\n\n";
			}
		}
	}
}

add_action( 'wp_footer', 'wcrty_footer_scripts' );

/**
 * Adding Thank You Scripts if on a Thank You Page.
 */
function wcrty_footer_scripts() {
	if ( is_order_received_page() ) {
		$scripts = get_option( 'woocommerce_redirect_thank_you_scripts', '' );
		if ( $scripts ) {
			echo wp_unslash( $scripts );
		}
	}
}

if ( is_admin() ) {

	/**
	 * Admin settings
	 */
	include_once 'includes/meta-boxes/class-wcrty-meta-box-redirect.php';
	require_once 'includes/admin/class-wcrty-admin.php';
	$admin = new WCRTY_Admin();

}

