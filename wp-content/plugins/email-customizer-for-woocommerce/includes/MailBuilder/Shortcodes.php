<?php

namespace YayMail\MailBuilder;

use YayMail\Helper\Helper;
use YayMail\Page\Source\CustomPostType;
use YayMail\Page\Source\UpdateElement;
use YayMail\Templates\Templates;

defined( 'ABSPATH' ) || exit;
global $woocommerce, $wpdb, $current_user, $order;

class Shortcodes {

	protected static $instance = null;
	public $order_id           = false;
	public $args_email         = false;
	public $order;
	public $sent_to_admin = false;
	public $order_data;
	public $template          = false;
	public $customer_note     = false;
	public $yaymail_states    = null;
	public $yaymail_countries = null;
	public $shipping_address  = null;
	public $billing_address   = null;
	// public $array_content_template = false;
	public $shortcodes_lists;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct( $template = false, $checkOrder = '' ) {
		$this->yaymail_states    = include plugin_dir_path( __FILE__ ) . '/languages/states.php';
		$this->yaymail_countries = include plugin_dir_path( __FILE__ ) . '/languages/countries.php';
		if ( $template ) {
			$this->template = $template;
			if ( 'sampleOrder' === $checkOrder ) {
				$this->shortCodesOrderSample();
			} else {
				$this->shortCodesOrderDefined();
			}
			// style css
			add_filter( 'woocommerce_email_styles', array( $this, 'customCss' ) );

			// Order Details
			$order_details_list = array(
				'items_downloadable_title',
				'items_downloadable_product',
				'items_border',
				'items',
				'order_date',
				'order_fee',
				'order_id',
				'order_link',
				'order_number',
				'order_refund',
				'order_sub_total',
				'order_total',
				'order_tn',
				'items_border_before',
				'items_border_after',
				'items_border_title',
				'items_border_content',
				'items_downloadable_product',
				'items_downloadable_title',
				'shipment_tracking_title',
				'woocommerce_email_before_order_table',
				'woocommerce_email_after_order_table',
				'woocommerce_email_sozlesmeler',
			);

			// Payments
			$payments_list = array(
				'order_payment_method',
				'order_payment_url',
				'order_payment_url_string',
				'payment_method',
				'transaction_id',
			);

			// Shippings
			$shippings_list = array(
				'order_shipping',
				'shipping_address',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_company',
				'shipping_country',
				'shipping_first_name',
				'shipping_last_name',
				'shipping_method',
				'shipping_postcode',
				'shipping_state',
			);

			// Billings
			$billings_list = array(
				'billing_address',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_company',
				'billing_country',
				'billing_email',
				'billing_first_name',
				'billing_last_name',
				'billing_phone',
				'billing_postcode',
				'billing_state',
			);

			// Reset Password
			$reset_password_list = array( 'password_reset_url', 'password_reset_url_string' );

			// New Users
			$new_users_list = array( 'user_new_password', 'user_activation_link' );

			// General
			$general_list = array(
				'customer_note',
				'customer_notes',
				'customer_provided_note',
				'site_name',
				'site_url',
				'site_url_string',
				'user_email',
				'user_id',
				'user_name',
				'customer_username',
				'customer_name',
				'view_order_url',
				'view_order_url_string',
				'billing_shipping_address',
				'domain',
				'user_account_url',
				'user_account_url_string',
				// new
				'billing_shipping_address_title',
				'billing_shipping_address_content',
				'check_billing_shipping_address',
				'order_status',
				'notifier_product_name',
				'notifier_product_id',
				'notifier_product_link',
				'notifier_shopname',
				'notifier_email_id',
				'notifier_subscriber_email',
				'notifier_subscriber_name',
				'notifier_cart_link',
				'notifier_only_product_name',
				'notifier_only_product_sku',
				'notifier_only_product_image',
			);

			if ( class_exists( 'WC_Shipment_Tracking_Actions' ) || class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
				$shipment_tracking_list = array( 'order_meta:_wc_shipment_tracking_items' );
				$order_details_list     = array_merge( $order_details_list, $shipment_tracking_list );
			}

			if ( class_exists( 'WC_Admin_Custom_Order_Fields' ) ) {
				$admin_custom_order_fields = array( 'order_meta:_wc_additional_order_details' );
				$order_details_list        = array_merge( $order_details_list, $admin_custom_order_fields );
			}

			if ( class_exists( 'EventON' ) ) {
				$event_on           = array( 'order_meta:_event_on_list' );
				$order_details_list = array_merge( $order_details_list, $event_on );
			}

			if ( class_exists( 'YITH_WooCommerce_Order_Tracking_Premium' ) ) {
				$tracking           = array( 'order_carrier_name', 'order_pickup_date', 'order_track_code', 'order_tracking_link' );
				$order_details_list = array_merge( $order_details_list, $tracking );
			}

			// Additional Order Meta.
			$order = CustomPostType::getListOrders();

			/* Define Shortcodes */
			$shortcodes_lists = array();
			$shortcodes_lists = array_merge( $shortcodes_lists, apply_filters( 'yaymail_shortcodes', $shortcodes_lists ) );
			$shortcodes_lists = array_merge( $shortcodes_lists, $order_details_list );
			// $shortcodes_lists       = array_merge( $shortcodes_lists, $order_subscription_list );
			$shortcodes_lists       = array_merge( $shortcodes_lists, $payments_list );
			$shortcodes_lists       = array_merge( $shortcodes_lists, $shippings_list );
			$shortcodes_lists       = array_merge( $shortcodes_lists, $billings_list );
			$shortcodes_lists       = array_merge( $shortcodes_lists, $reset_password_list );
			$shortcodes_lists       = array_merge( $shortcodes_lists, $new_users_list );
			$shortcodes_lists       = array_merge( $shortcodes_lists, $general_list );
			$this->shortcodes_lists = $shortcodes_lists;
			foreach ( $this->shortcodes_lists as $key => $shortcode_name ) {
				if ( 'woocommerce_email_before_order_table' == $shortcode_name || 'woocommerce_email_after_order_table' == $shortcode_name ) {
					add_shortcode( $shortcode_name, array( $this, 'shortcodeCallBack' ) );
				} else {
					$function_name = $this->parseShortCodeToFunctionName( 'yaymail_' . $shortcode_name );
					if ( method_exists( $this, $function_name ) ) {
						add_shortcode(
							'yaymail_' . $shortcode_name,
							function ( $atts, $content, $tag ) {
								$function_name = $this->parseShortCodeToFunctionName( $tag );
								return $this->$function_name( $atts, $this->order, $this->sent_to_admin, $this->args_email );
							}
						);
					} elseif ( strpos( $shortcode_name, 'addon' ) !== false ) {
						add_shortcode(
							$shortcode_name,
							function ( $atts, $content, $tag ) {
								return $this->order_data[ '[' . $tag . ']' ];
							}
						);
					} else {
						add_shortcode( 'yaymail_' . $shortcode_name, array( $this, 'shortcodeCallBack' ) );
					}
				}
			}
			// add_shortcode( 'yaymail_billing_shipping_address_content', array( $this, 'billing_shipping_address_content' ) );
			// add_shortcode( 'yaymail_items_border_content', array( $this, 'items_border_content' ) );
			// add_shortcode( 'yaymail_items_border_title', array( $this, 'items_border_title' ) );
		}
	}

	public function parseShortCodeToFunctionName( $shortcode_name ) {
		$function_name = substr( $shortcode_name, 8 );
		$offset        = 0;
		while ( false !== strpos( $function_name, '_', $offset ) ) {
			$position                       = strpos( $function_name, '_', $offset );
			$function_name[ $position + 1 ] = strtoupper( $function_name[ $position + 1 ] );
			$offset                         = $position + 1;
		}
		$function_name = str_replace( '_', '', $function_name );
		if ( 'yaymail_order_meta:_wc_shipment_tracking_items' == $shortcode_name
			|| 'yaymail_order_meta:_wc_additional_order_details' == $shortcode_name
			|| 'yaymail_order_meta:_event_on_list' === $shortcode_name
		) {
			$function_name = str_replace( ':', '', $function_name );
		}
		return $function_name;
	}

	public function applyCSSFormat( $defaultsCss = '' ) {
		$templateEmail = \YayMail\Templates\Templates::getInstance();
		$css           = $templateEmail::getCssFortmat();
		$cssDirection  = '';
		$cssDirection .= 'td{direction: rtl}';
		$cssDirection .= 'td, th, td{text-align:right;}';

		$css .= get_option( 'yaymail_direction' ) && get_option( 'yaymail_direction' ) === 'rtl' ? $cssDirection : '';
		$css .= $defaultsCss;
		$css .= '.td { color: inherit; }';
		return $css;
	}
	public function customCss( $css = '' ) {
		return $this->applyCSSFormat( $css );
	}
	public function setOrderId( $order_id = '', $sent_to_admin = '', $args = '' ) {
		$this->order_id      = $order_id;
		$this->args_email    = $args;
		$this->sent_to_admin = $sent_to_admin;
		// Additional Order Meta.
		$order_meta_list = array();
		if ( ! empty( $this->order_id ) ) {
			$order_metaArr = get_post_meta( $this->order_id );
			if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
				foreach ( $order_metaArr as $k => $v ) {
					$nameField         = str_replace( ' ', '_', trim( $k ) );
					$order_meta_list[] = 'order_meta:' . $nameField;
				}
			}
		}
		if ( 0 == count( $order_meta_list ) ) {
			$order_meta_list[] = 'order_meta:_wc_shipment_tracking_items';
			$order_meta_list[] = 'order_meta:_wc_additional_order_details';
			$order_meta_list[] = 'order_meta:_event_on_list';
		}
		$shortcodes_lists = array();
		$shortcodes_lists = array_merge( $shortcodes_lists, $order_meta_list );
		foreach ( $shortcodes_lists as $key => $shortcode_name ) {
			if ( 'order_meta:_wc_shipment_tracking_items' == $shortcode_name ) {
				add_shortcode(
					'yaymail_' . $shortcode_name,
					function ( $atts, $content, $tag ) {
						return $this->orderMetaWcShipmentTrackingItems( $atts, $this->order, $this->sent_to_admin, $this->args_email );
					}
				);
			} elseif ( 'order_meta:_wc_additional_order_details' == $shortcode_name ) {
				add_shortcode(
					'yaymail_' . $shortcode_name,
					function ( $atts, $content, $tag ) {
						return $this->orderMetaWcAdditionalOrderDetails( $atts, $this->order, $this->sent_to_admin, $this->args_email );
					}
				);
			} elseif ( 'order_meta:_event_on_list' === $shortcode_name ) {
				add_shortcode(
					'yaymail_' . $shortcode_name,
					function ( $atts, $content, $tag ) {
						return $this->eventOnList( $atts, $this->order, $this->sent_to_admin, $this->args_email );
					}
				);
			} elseif ( 'order_meta:ywot_tracking_code' === $shortcode_name ) {
				$abc;
			} else {
				add_shortcode( 'yaymail_' . $shortcode_name, array( $this, 'shortcodeCallBack' ) );
			}
		}
	}

	protected function _shortcode_atts( $defaults = array(), $atts = array() ) {
		if ( isset( $atts['class'] ) ) {
			$atts['classname'] = $atts['class'];
		}

		return \shortcode_atts( $defaults, $atts );
	}

	// short Codes Order when select SampleOrder
	public function shortCodesOrderSample( $sent_to_admin = '' ) {
		$user  = wp_get_current_user();
		$useId = get_current_user_id();
		$this->defaultSampleOrderData( $sent_to_admin );
	}

	public function shortCodesOrderDefined( $sent_to_admin = '', $args = array() ) {
		// if ( false === $this->order_id && class_exists( 'WC_Order' ) ) {
		// $this->shortCodesOrderSample( $sent_to_admin );
		// }
		if ( false !== $this->order_id && ! empty( $this->order_id ) && class_exists( 'WC_Order' ) ) {
			$this->order = new \WC_Order( $this->order_id );
			$this->collectOrderData( $sent_to_admin, $args );
		}
		if ( ! function_exists( 'get_user_by' ) ) {
			return false;
		}
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : '';
		if ( empty( $this->order_id ) || ! $this->order_id ) {
			$shortcode = $this->order_data;
			if ( isset( $_REQUEST['billing_email'] ) ) {
				$shortcode['[yaymail_user_email]'] = sanitize_email( $_REQUEST['billing_email'] );
				$user                              = get_user_by( 'email', sanitize_email( $_REQUEST['billing_email'] ) );
				if ( ! empty( $user ) ) {
					$shortcode['[yaymail_customer_username]'] = $user->user_login;
					$shortcode['[yaymail_customer_name]']     = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					$shortcode['[yaymail_user_id]']           = $user->ID;
				}
			}
			if ( empty( $shortcode['[yaymail_customer_username]'] ) ) {
				if ( isset( $_REQUEST['user_email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['user_email'] ) );
					if ( isset( $user->user_login ) ) {
						$shortcode['[yaymail_customer_username]'] = $user->user_login;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				} elseif ( isset( $_REQUEST['email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['email'] ) );
					if ( isset( $user->user_login ) ) {
						$shortcode['[yaymail_customer_username]'] = $user->user_login;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				}
			}
			if ( empty( $shortcode['[yaymail_user_email]'] ) ) {
				if ( isset( $_REQUEST['user_email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['user_email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_user_email]'] = $user->user_email;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				} elseif ( isset( $_REQUEST['email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_user_email]'] = $user->user_email;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				}
			}
			if ( empty( $shortcode['[yaymail_customer_name]'] ) ) {
				if ( isset( $_REQUEST['user_email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['user_email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_customer_name]'] = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				} elseif ( isset( $_REQUEST['email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_customer_name]'] = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				}
			}
			if ( ! empty( $args ) ) {
				if ( isset( $args['email'] ) || isset( $args['admin_email'] ) || isset( $args['user'] ) ) {
					$postID               = CustomPostType::postIDByTemplate( $this->template );
					$text_link_color      = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
					$yaymail_settings     = get_option( 'yaymail_settings' );
					$yaymail_informations = array(
						'post_id'          => $postID,
						'template'         => $this->template,
						'yaymail_elements' => get_post_meta( $postID, '_yaymail_elements', true ),
						'general_settings' => array(
							'tableWidth'           => $yaymail_settings['container_width'],
							'emailBackgroundColor' => get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC',
							'textLinkColor'        => get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A',
						),
					);
					if ( isset( $args['email']->id ) && 'customer_reset_password' == $args['email']->id ) {
						$shortcode['[yaymail_customer_username]']  = $args['email']->user_login;
						$user                                      = new \WP_User( intval( $args['email']->user_id ) );
						$shortcode['[yaymail_customer_name]']      = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
						$shortcode['[yaymail_user_email]']         = $args['email']->user_email;
						$shortcode['[yaymail_user_id]']            = $user->ID;
						$reset_key                                 = get_password_reset_key( $user );
						$resetURL                                  = esc_url(
							add_query_arg(
								array(
									'key'   => $reset_key,
									'login' => rawurlencode( $args['email']->user_login ),
								),
								wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
							)
						);
						$shortcode['[yaymail_password_reset_url]'] = '<a style="color: ' . $text_link_color . ';" href="' . esc_url( $resetURL ) . '">' . esc_html__( 'Click here to reset your password', 'woocommerce' ) . '</a>';
						$shortcode['[yaymail_password_reset_url_string]'] = esc_url( $resetURL );
						$shortcode['[yaymail_site_name]']                 = get_bloginfo( 'name' );
					}

						$shortcode['[yaymail_site_name]']               = get_bloginfo( 'name' );
						$shortcode['[yaymail_site_url]']                = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
						$shortcode['[yaymail_site_url_string]']         = get_site_url();
						$shortcode['[yaymail_user_account_url]']        = '<a style="color:' . $text_link_color . '; font-weight: normal; text-decoration: underline;" href="' . wc_get_page_permalink( 'myaccount' ) . '">' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '</a>';
						$shortcode['[yaymail_user_account_url_string]'] = wc_get_page_permalink( 'myaccount' );
					if ( isset( $args['email']->user_pass ) && ! empty( $args['email']->user_pass ) ) {
						$shortcode['[yaymail_user_new_password]'] = $args['email']->user_pass;
					} else {
						if ( isset( $_REQUEST['pass1-text'] ) && '' != $_REQUEST['pass1-text'] ) {
							$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
						} elseif ( isset( $_REQUEST['pass1'] ) && '' != $_REQUEST['pass1'] ) {
							$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
						} else {
							$shortcode['[yaymail_user_new_password]'] = '';
						}
					}
					if ( isset( $args['email']->user_login ) && ! empty( $args['email']->user_login ) ) {
						$shortcode['[yaymail_customer_username]'] = $args['email']->user_login;
						$user                                     = get_user_by( 'email', $args['email']->user_email );
						$shortcode['[yaymail_customer_name]']     = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					}
					if ( isset( $args['email']->user_email ) && ! empty( $args['email']->user_email ) ) {
						$shortcode['[yaymail_user_email]'] = $args['email']->user_email;
					}
					if ( isset( $args['email']->id ) && ( 'customer_new_account' == $args['email']->id || 'customer_new_account_activation' == $args['email']->id ) ) {
						if ( 'customer_new_account_activation' == $args['email']->id ) {
							if ( isset( $args['email']->user_activation_url ) && ! empty( $args['email']->user_activation_url ) ) {
								$shortcode['[yaymail_user_activation_link]'] = $args['email']->user_activation_url;
							}
						} else {
							if ( isset( $args['email']->user_login ) && ! empty( $args['email']->user_login ) ) {
								global $wpdb, $wp_hasher;
								$newHash = $wp_hasher;
								// Generate something random for a password reset key.
								$key = wp_generate_password( 20, false );

								/**
								 *
								 * This action is documented in wp-login.php
								 */
								do_action( 'retrieve_password_key', $args['email']->user_login, $key );

								// Now insert the key, hashed, into the DB.
								if ( empty( $wp_hasher ) ) {
									if ( ! class_exists( 'PasswordHash' ) ) {
										include_once ABSPATH . 'wp-includes/class-phpass.php';
									}
									$newHash = new \PasswordHash( 8, true );
								}
								$hashed = time() . ':' . $newHash->HashPassword( $key );
								$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $args['email']->user_login ) );
								$activation_url                              = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $args['email']->user_login ), 'login' );
								$shortcode['[yaymail_user_activation_link]'] = $activation_url;
							}
						}
					}

					// Define shortcode from plugin addon
					$shortcode = apply_filters( 'yaymail_do_shortcode', $shortcode, $yaymail_informations, $args );
				}
			}
			// support plugin Support Back In Stock Notifier for WooCommerce
			if ( class_exists( 'CWG_Instock_API' ) && isset( $args['subscriber_id'] ) ) {
				$obj               = new \CWG_Instock_API();
				$product_name      = $obj->display_product_name( $args['subscriber_id'] );
				$only_product_name = $obj->display_only_product_name( $args['subscriber_id'] );
				$product_link      = $obj->display_product_link( $args['subscriber_id'] );
				$only_product_sku  = $obj->get_product_sku( $args['subscriber_id'] );
				$product_image     = $obj->get_product_image( $args['subscriber_id'] );
				$subscriber_name   = $obj->get_subscriber_name( $args['subscriber_id'] );
				$pid               = get_post_meta( $args['subscriber_id'], 'cwginstock_pid', true );
				$cart_url          = esc_url_raw( add_query_arg( 'add-to-cart', $pid, get_permalink( wc_get_page_id( 'cart' ) ) ) );
				$blogname          = get_bloginfo( 'name' );

				$shortcode['[yaymail_notifier_product_name]']       = $product_name;
				$shortcode['[yaymail_notifier_product_id]']         = $pid;
				$shortcode['[yaymail_notifier_product_link]']       = $product_link;
				$shortcode['[yaymail_notifier_shopname]']           = $blogname;
				$shortcode['[yaymail_notifier_email_id]']           = get_post_meta( $args['subscriber_id'], 'cwginstock_subscriber_email', true );
				$shortcode['[yaymail_notifier_subscriber_email]']   = get_post_meta( $args['subscriber_id'], 'cwginstock_subscriber_email', true );
				$shortcode['[yaymail_notifier_subscriber_name]']    = $subscriber_name;
				$shortcode['[yaymail_notifier_cart_link]']          = '<a href="' . $cart_url . '"> ' . $cart_url . ' </a>';
				$shortcode['[yaymail_notifier_only_product_name]']  = $only_product_name;
				$shortcode['[yaymail_notifier_only_product_sku]']   = $only_product_sku;
				$shortcode['[yaymail_notifier_only_product_image]'] = $product_image;
				$shortcode['[yaymail_site_name]']                   = get_bloginfo( 'name' );
			}
			$this->order_data = $shortcode;
		}
	}
	public function shortcodeCallBack( $atts, $content, $tag ) {

		return isset( $this->order_data[ '[' . $tag . ']' ] ) ? $this->order_data[ '[' . $tag . ']' ] : false;

	}

	public function templateParser() {
		// Helper::checkNonce();
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
			wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
		} else {
			$request        = $_POST;
			$this->order_id = false;
			if ( isset( $request['order_id'] ) ) {
				$order_id = sanitize_text_field( $request['order_id'] );
				if ( 'sampleOrder' !== $order_id ) {
					$order_id = intval( $order_id );
				}
				if ( ! $order_id ) {
					$order_id = '';
				}

				$this->template = isset( $request['template'] ) ? $request['template'] : false;
				$this->order_id = $order_id;
			}

			if ( ! $this->order_id || ! $this->template ) {
				return false;
			}

			if ( 'sampleOrder' !== $order_id ) {
				$this->order = new \WC_Order( $this->order_id );
			}

			if ( 'sampleOrder' !== $order_id && ( is_null( $this->order ) || empty( $this->order ) || ! isset( $this->order ) ) ) {
				return false;
			}

			if ( 'sampleOrder' !== $order_id ) {
				$this->collectOrderData();
			} else {
				$this->defaultSampleOrderData();
			}

			$result             = (object) array();
			$result->order_id   = $this->order_id;
			$result->order_data = $this->order_data;

			$shortcode_order_meta        = array();
			$shortcode_order_custom_meta = array();
			if ( 'sampleOrder' !== $order_id ) {
				$result->order        = $this->order;
				$result->order_items  = $result->order->get_items();
				$result->user_details = $result->order->get_user();

				/*
				@@@@ Get name field in custom field of order woocommerce.
				 */
				$order_metaArr = get_post_meta( $order_id );
				if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
					$pattern = '/^_.*/i';
					$n       = 0;
					foreach ( $order_metaArr as $k => $v ) {
						// @@@ starts with the "_" character of the woo field.
						if ( ! preg_match( $pattern, $k ) ) {
							$nameField              = str_replace( ' ', '_', trim( $k ) );
							$nameShorcode           = '[yaymail_post_meta:' . $nameField . ']';
							$key_order_meta         = 'post_meta:' . $nameField . '_' . $n;
							$shortcode_order_meta[] = array(
								'key'         => $key_order_meta,
								$nameShorcode => 'Loads value of order meta key - ' . $nameField,
							);
							$n++;
						}
					}
				}
				if ( ! empty( $result->order ) ) {
					foreach ( $result->order->get_meta_data() as $meta ) {
						$nameField                         = str_replace( ' ', '_', trim( $meta->get_data()['key'] ) );
						$nameShorcode                      = '[yaymail_order_meta:' . $nameField . ']';
						$key_order_custom_meta             = 'order_meta:' . $nameField;
							$shortcode_order_custom_meta[] = array(
								'key'         => $key_order_custom_meta,
								$nameShorcode => 'Loads value of order custom meta key - ' . $nameField,
							);
					}
				}
			} else {
				$result->order        = '';
				$result->order_items  = '';
				$result->user_details = '';
			}
			$real_postID = '';
			if ( isset( $request['template'] ) ) {
				if ( CustomPostType::postIDByTemplate( $this->template ) ) {
					$postID           = CustomPostType::postIDByTemplate( $this->template );
					$real_postID      = $postID;
					$emailTemplate    = get_post( $postID );
					$updateElement    = new UpdateElement();
					$yaymail_elements = get_post_meta( $postID, '_yaymail_elements', true );
					$result->elements = Helper::unsanitize_array( $updateElement->merge_new_props_to_elements( $yaymail_elements ) );
					// $result->html                        = html_entity_decode( get_post_meta( $postID, '_yaymail_html', true ), ENT_QUOTES, 'UTF-8' );
					$result->emailBackgroundColor        = get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : 'rgb(236, 236, 236)';
					$result->emailTextLinkColor          = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
					$result->titleShipping               = get_post_meta( $postID, '_email_title_shipping', true ) ? get_post_meta( $postID, '_email_title_shipping', true ) : 'Shipping Address';
					$result->titleBilling                = get_post_meta( $postID, '_email_title_billing', true ) ? get_post_meta( $postID, '_email_title_billing', true ) : 'Billing Address';
					$result->orderTitle                  = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
					$result->customCSS                   = $this->applyCSSFormat();
					$result->shortcode_order_meta        = $shortcode_order_meta;
					$result->shortcode_order_custom_meta = $shortcode_order_custom_meta;
					$result->follow_up_shortcodes        = array();
				}
			}
			if ( class_exists( 'Follow_Up_Emails' ) ) {
				$result->follow_up_shortcodes = apply_filters( 'yaymail_follow_up_shortcode', array() );
			}
			$result->yaymailAddonTemps = apply_filters( 'yaymail_addon_templates', array(), $result->order, $real_postID );

			echo json_encode( $result );
			die();
		}
	}

	public function collectOrderData( $sent_to_admin = '', $args = array() ) {
		$order = $this->order;
		if ( empty( $this->order_id ) || empty( $order ) ) {
			return false;
		}

		// Getting Fee & Refunds:
		$fee    = 0;
		$refund = 0;
		$totals = $order->get_order_item_totals();
		foreach ( $totals as $index => $value ) {
			if ( strpos( $index, 'fee' ) !== false ) {
				$fees = $order->get_fees();
				foreach ( $fees as $feeVal ) {
					if ( method_exists( $feeVal, 'get_amount' ) ) {
						$fee += $feeVal->get_amount();
					}
				}
			}
			if ( strpos( $index, 'refund' ) !== false ) {
				$refund = $order->get_total_refunded();
			}
		}
		// User Info
		$user_data        = $order->get_user();
		$created_date     = $order->get_date_created();
		$items            = $order->get_items();
		$yaymail_settings = get_option( 'yaymail_settings' );
		$order_url        = $order->get_edit_order_url();
		add_filter(
			'woocommerce_formatted_address_replacements',
			function( $info, $args ) {
				$file = __FILE__;
				if ( 'Shortcodes.php' === basename( $file ) ) {
					$info['{state}']         = $this->yaymail_states[ $args['country'] ][ $args['state'] ];
					$info['{state_upper}']   = wc_strtoupper( $info['{state}'] );
					$info['{country}']       = $this->yaymail_countries[ $args['country'] ];
					$info['{country_upper}'] = wc_strtoupper( $info['{country}'] );
				}
				return $info;
			},
			100,
			2
		);
		if ( class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ) {
			add_action(
				'woocommerce_email_customer_details',
				function( $order ) {
					if ( 'Shortcodes.php' === basename( __FILE__ ) ) {
						$this->shipping_address = $order->get_formatted_shipping_address();
						$this->billing_address  = $order->get_formatted_billing_address();
					}
				},
				100,
				1
			);
			ob_start();
			do_action( 'woocommerce_email_customer_details', $order );
			$delete_write = ob_get_contents();
			ob_end_clean();
		}
		$shipping_address     = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->shipping_address : $order->get_formatted_shipping_address();
		$billing_address      = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->billing_address : $order->get_formatted_billing_address();
		$postID               = CustomPostType::postIDByTemplate( $this->template );
		$yaymail_informations = array(
			'post_id'          => $postID,
			'template'         => $this->template,
			'order'            => $order,
			'yaymail_elements' => get_post_meta( $postID, '_yaymail_elements', true ),
			'general_settings' => array(
				'tableWidth'           => $yaymail_settings['container_width'],
				'emailBackgroundColor' => get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC',
				'textLinkColor'        => get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A',
			),
		);
		$text_link_color      = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
		if ( $order->get_billing_phone() ) {
			$billing_address .= "<br/> <a href='tel:" . esc_html( $order->get_billing_phone() ) . "' style='color:" . $text_link_color . "; font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_phone() ) . '</a>';
		}
		if ( $order->get_billing_email() ) {
			$billing_address .= "<br/><a href='mailto:" . esc_html( $order->get_billing_email() ) . "' style='color:" . $text_link_color . ";font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_email() ) . '</a>';
		}
		$customerNotes        = $order->get_customer_order_notes();
		$customerNoteHtmlList = '';
		$customerNoteHtml     = $customerNoteHtmlList;
		if ( ! empty( $customerNotes ) && count( $customerNotes ) ) {
			$customerNoteHtmlList  = $this->getOrderCustomerNotes( $customerNotes );
			$customerNote_single[] = $customerNotes[0];
			$customerNoteHtml      = $this->getOrderCustomerNotes( $customerNote_single );
		}

		$resetURL = '';
		if ( isset( $args['email']->reset_key ) && ! empty( $args['email']->reset_key )
			&& isset( $args['email']->user_login ) && ! empty( $args['email']->user_login )
		) {
			$user      = new \WP_User( intval( $args['email']->user_id ) );
			$reset_key = get_password_reset_key( $user );
			$resetURL  = esc_url(
				add_query_arg(
					array(
						'key'   => $reset_key,
						'login' => rawurlencode( $args['email']->user_login ),
					),
					wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
				)
			);
		}

		// Link Downloadable Product
		$shortcode['[yaymail_items_downloadable_title]']   = $this->itemsDownloadableTitle( '', $this->order, $sent_to_admin, '' ); // done
		$shortcode['[yaymail_items_downloadable_product]'] = $this->itemsDownloadableProduct( '', $this->order, $sent_to_admin, '' ); // done

		// ORDER DETAILS
		$shortcode['[yaymail_items_border]']         = $this->itemsBorder( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_before]']  = $this->itemsBorderBefore( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_after]']   = $this->itemsBorderAfter( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_title]']   = $this->itemsBorderTitle( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_content]'] = $this->itemsBorderContent( '', $this->order, $sent_to_admin ); // done

		// WC HOOK
		$shortcode['[woocommerce_email_before_order_table]']  = $this->orderWoocommerceBeforeHook( $args, $sent_to_admin ); // not Changed
		$shortcode['[woocommerce_email_after_order_table]']   = $this->orderWoocommerceAfterHook( $args, $sent_to_admin ); // not Changed
		$shortcode['[yaymail_woocommerce_email_sozlesmeler]'] = $this->woocommerceEmailSozlesmeler( '', $this->order, $sent_to_admin ); // not Changed

		// support plugin Support Back In Stock Notifier for WooCommerce
		if ( class_exists( 'CWG_Trigger_Instock_Mail' ) || class_exists( 'CWG_Trigger_Subscribe_Mail' ) ) {
			$shortcode['[yaymail_notifier_product_name]']       = 'YayMail';
			$shortcode['[yaymail_notifier_product_id]']         = '1';
			$shortcode['[yaymail_notifier_product_link]']       = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
			$shortcode['[yaymail_notifier_shopname]']           = get_bloginfo( 'name' );
			$shortcode['[yaymail_notifier_email_id]']           = 'yaymail@gmail.com';
			$shortcode['[yaymail_notifier_subscriber_email]']   = 'yaymail@gmail.com';
			$shortcode['[yaymail_notifier_subscriber_name]']    = 'YayMail';
			$shortcode['[yaymail_notifier_cart_link]']          = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
			$shortcode['[yaymail_notifier_only_product_name]']  = 'YayMail';
			$shortcode['[yaymail_notifier_only_product_sku]']   = '1';
			$shortcode['[yaymail_notifier_only_product_image]'] = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
		}
		// Define shortcode from plugin addon
		$shortcode = apply_filters( 'yaymail_do_shortcode', $shortcode, $yaymail_informations, $this->args_email );

		$shortcode['[yaymail_items]'] = $this->orderItems( $items, $sent_to_admin );
		if ( null != $created_date ) {
			$shortcode['[yaymail_order_date]'] = $order->get_date_created()->date_i18n( wc_date_format() );
		} else {
			$shortcode['[yaymail_order_date]'] = '';
		}
		$shortcode['[yaymail_order_fee]'] = $fee;
		if ( ! empty( $order->get_id() ) ) {
			$shortcode['[yaymail_order_id]'] = $order->get_id();
		} else {
			$shortcode['[yaymail_order_id]'] = '';
		}
		$shortcode['[yaymail_order_link]'] = '<a href="' . $order_url . '" style="color:' . $text_link_color . ';">' . esc_html__( 'Order', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_link]'] = str_replace( '[yaymail_order_id]', $order->get_id(), $shortcode['[yaymail_order_link]'] );
		if ( ! empty( $order->get_order_number() ) ) {
			$shortcode['[yaymail_order_number]'] = $order->get_order_number();
		} else {
			$shortcode['[yaymail_order_number]'] = '';
		}
		$shortcode['[yaymail_order_refund]'] = $refund;
		if ( isset( $totals['cart_subtotal']['value'] ) ) {
			$shortcode['[yaymail_order_sub_total]'] = $totals['cart_subtotal']['value'];
		} else {
			$shortcode['[yaymail_order_sub_total]'] = '';
		}
		$shortcode['[yaymail_order_total]'] = wc_price( $order->get_total() );

		// PAYMENTS
		if ( isset( $totals['payment_method']['value'] ) ) {
			$shortcode['[yaymail_order_payment_method]'] = $totals['payment_method']['value'];
		} else {
			$shortcode['[yaymail_order_payment_method]'] = '';
		}
		$shortcode['[yaymail_order_payment_url]']        = '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'Payment page', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_payment_url_string]'] = esc_url( $order->get_checkout_payment_url() );
		if ( ! empty( $order->get_payment_method_title() ) ) {
			$shortcode['[yaymail_payment_method]'] = $order->get_payment_method_title();
		} else {
			$shortcode['[yaymail_payment_method]'] = '';
		}
		if ( ! empty( $order->get_transaction_id() ) ) {
			$shortcode['[yaymail_transaction_id]'] = $order->get_transaction_id();
		} else {
			$shortcode['[yaymail_transaction_id]'] = '';
		}

		// SHIPPINGS
		if ( ! empty( $order->calculate_shipping() ) ) {
			$shortcode['[yaymail_order_shipping]'] = $order->calculate_shipping();
		} else {
			$shortcode['[yaymail_order_shipping]'] = 0;
		}
		$shortcode['[yaymail_shipping_address]'] = $shipping_address;
		if ( ! empty( $order->get_shipping_address_1() ) ) {
			$shortcode['[yaymail_shipping_address_1]'] = $order->get_shipping_address_1();
		} else {
			$shortcode['[yaymail_shipping_address_1]'] = '';
		}
		if ( ! empty( $order->get_shipping_address_2() ) ) {
			$shortcode['[yaymail_shipping_address_2]'] = $order->get_shipping_address_2();
		} else {
			$shortcode['[yaymail_shipping_address_2]'] = '';
		}
		if ( ! empty( $order->get_shipping_city() ) ) {
			$shortcode['[yaymail_shipping_city]'] = $order->get_shipping_city();
		} else {
			$shortcode['[yaymail_shipping_city]'] = '';
		}
		if ( ! empty( $order->get_shipping_company() ) ) {
			$shortcode['[yaymail_shipping_company]'] = $order->get_shipping_company();
		} else {
			$shortcode['[yaymail_shipping_company]'] = '';
		}
		if ( ! empty( $order->get_shipping_country() ) ) {
			$shortcode['[yaymail_shipping_country]'] = $order->get_shipping_country();
		} else {
			$shortcode['[yaymail_shipping_country]'] = '';
		}
		if ( ! empty( $order->get_shipping_first_name() ) ) {
			$shortcode['[yaymail_shipping_first_name]'] = $order->get_shipping_first_name();
		} else {
			$shortcode['[yaymail_shipping_first_name]'] = '';

		}
		if ( ! empty( $order->get_shipping_last_name() ) ) {
			$shortcode['[yaymail_shipping_last_name]'] = $order->get_shipping_last_name();
		} else {
			$shortcode['[yaymail_shipping_last_name]'] = '';

		}
		if ( ! empty( $order->get_shipping_method() ) ) {
			$shortcode['[yaymail_shipping_method]'] = $order->get_shipping_method();
		} else {
			$shortcode['[yaymail_shipping_method]'] = '';
		}
		if ( ! empty( $order->get_shipping_postcode() ) ) {
			$shortcode['[yaymail_shipping_postcode]'] = $order->get_shipping_postcode();
		} else {
			$shortcode['[yaymail_shipping_postcode]'] = '';
		}
		if ( ! empty( $order->get_shipping_state() ) ) {
			$shortcode['[yaymail_shipping_state]'] = $order->get_shipping_state();
		} else {
			$shortcode['[yaymail_shipping_state]'] = '';
		}

		// BILLINGS
		$shortcode['[yaymail_billing_address]'] = $billing_address;
		if ( ! empty( $order->get_billing_address_1() ) ) {
			$shortcode['[yaymail_billing_address_1]'] = $order->get_billing_address_1();
		} else {
			$shortcode['[yaymail_billing_address_1]'] = '';
		}
		if ( ! empty( $order->get_billing_address_2() ) ) {
			$shortcode['[yaymail_billing_address_2]'] = $order->get_billing_address_2();
		} else {
			$shortcode['[yaymail_billing_address_2]'] = '';
		}
		if ( ! empty( $order->get_billing_city() ) ) {
			$shortcode['[yaymail_billing_city]'] = $order->get_billing_city();
		} else {
			$shortcode['[yaymail_billing_city]'] = $order->get_billing_city();
		}
		if ( ! empty( $order->get_billing_company() ) ) {
			$shortcode['[yaymail_billing_company]'] = $order->get_billing_company();
		} else {
			$shortcode['[yaymail_billing_company]'] = '';
		}
		if ( ! empty( $order->get_billing_country() ) ) {
			$shortcode['[yaymail_billing_country]'] = $order->get_billing_country();
		} else {
			$shortcode['[yaymail_billing_country]'] = '';
		}
		if ( ! empty( $order->get_billing_email() ) ) {
			$shortcode['[yaymail_billing_email]'] = '<a style="color: inherit" href="mailto:' . $order->get_billing_email() . '">' . $order->get_billing_email() . '</a>';
		} else {
			$shortcode['[yaymail_billing_email]'] = '';
		}
		if ( ! empty( $order->get_billing_first_name() ) ) {
			$shortcode['[yaymail_billing_first_name]'] = $order->get_billing_first_name();
		} else {
			$shortcode['[yaymail_billing_first_name]'] = '';
		}
		if ( ! empty( $order->get_billing_last_name() ) ) {
			$shortcode['[yaymail_billing_last_name]'] = $order->get_billing_last_name();
		} else {
			$shortcode['[yaymail_billing_last_name]'] = '';
		}
		if ( ! empty( $order->get_billing_phone() ) ) {
			$shortcode['[yaymail_billing_phone]'] = $order->get_billing_phone();
		} else {
			$shortcode['[yaymail_billing_phone]'] = '';
		}
		if ( ! empty( $order->get_billing_postcode() ) ) {
			$shortcode['[yaymail_billing_postcode]'] = $order->get_billing_postcode();
		} else {
			$shortcode['[yaymail_billing_postcode]'] = '';
		}
		if ( ! empty( $order->get_billing_state() ) ) {
			$shortcode['[yaymail_billing_state]'] = $order->get_billing_state();
		} else {
			$shortcode['[yaymail_billing_state]'] = '';
		}

		// Reset Passwords
		$shortcode['[yaymail_password_reset_url]']        = '<a style="color: ' . $text_link_color . ';" href="' . esc_url( $resetURL ) . '">' . esc_html__( 'Click here to reset your password', 'woocommerce' ) . '</a>';
		$shortcode['[yaymail_password_reset_url_string]'] = esc_url( $resetURL );

		// New Users
		if ( isset( $args['email']->user_pass ) && ! empty( $args['email']->user_pass ) ) {
			$shortcode['[yaymail_user_new_password]'] = $args['email']->user_pass;
		} else {
			if ( isset( $_REQUEST['pass1-text'] ) && '' != $_REQUEST['pass1-text'] ) {
				$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
			} elseif ( isset( $_REQUEST['pass1'] ) && '' != $_REQUEST['pass1'] ) {
				$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
			} else {
				$shortcode['[yaymail_user_new_password]'] = '';
			}
		}
		// Review this code ??
		if ( isset( $args['email']->user_activation_url ) && ! empty( $args['email']->user_activation_url ) ) {
			$shortcode['[yaymail_user_activation_link]'] = $args['email']->user_activation_url;
		} else {
			$shortcode['[yaymail_user_activation_link]'] = '';
		}

		// GENERALS
		$shortcode['[yaymail_customer_note]']  = $customerNoteHtml;
		$shortcode['[yaymail_customer_notes]'] = $customerNoteHtmlList;
		if ( ! empty( $order->get_customer_note() ) ) {
			$shortcode['[yaymail_customer_provided_note]'] = $order->get_customer_note();
		} else {
			$shortcode['[yaymail_customer_provided_note]'] = '';
		}
		$shortcode['[yaymail_site_name]']       = get_bloginfo( 'name' );
		$shortcode['[yaymail_site_url]']        = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
		$shortcode['[yaymail_site_url_string]'] = get_site_url();
		if ( isset( $user_data->user_email ) ) {
			$shortcode['[yaymail_user_email]'] = $user_data->user_email;
		} else {
			$shortcode['[yaymail_user_email]'] = $order->get_billing_email();
		}
		if ( isset( $shortcode['[yaymail_user_email]'] ) && '' != $shortcode['[yaymail_user_email]'] ) {
			$user                           = get_user_by( 'email', $shortcode['[yaymail_user_email]'] );
			$shortcode['[yaymail_user_id]'] = ( isset( $user->ID ) ) ? $user->ID : '';
		}
		if ( isset( $user_data->user_login ) && ! empty( $user_data->user_login ) ) {
			$shortcode['[yaymail_customer_username]'] = $user_data->user_login;
		} elseif ( isset( $user_data->user_nicename ) ) {
			$shortcode['[yaymail_customer_username]'] = $user_data->user_nicename;
		} else {
			$shortcode['[yaymail_customer_username]'] = $order->get_billing_first_name();
		}
		if ( isset( $user->ID ) && ! empty( $user->ID ) ) {
			$shortcode['[yaymail_customer_name]'] = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
		} elseif ( isset( $user_data->user_nicename ) ) {
			$shortcode['[yaymail_customer_name]'] = $user_data->user_nicename;
		} else {
			$shortcode['[yaymail_customer_name]'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		}
		if ( ! empty( $order->get_view_order_url() ) ) {
			$text_your_order                              = esc_html__( 'Your Order', 'yaymail' );
			$shortcode['[yaymail_view_order_url]']        = '<a href="' . $order->get_view_order_url() . '" style="color:' . $text_link_color . ';">' . $text_your_order . '</a>';
			$shortcode['[yaymail_view_order_url_string]'] = $order->get_view_order_url();
		} else {
			$shortcode['[yaymail_view_order_url]'] = '';
		}
		$shortcode['[yaymail_billing_shipping_address]'] = $this->billingShippingAddress( '', $this->order ); // done

		$shortcode['[yaymail_billing_shipping_address_title]']   = $this->billingShippingAddressTitle( '', $this->order ); // done
		$shortcode['[yaymail_billing_shipping_address_content]'] = $this->billingShippingAddressContent( '', $this->order ); // done
		$shortcode['[yaymail_check_billing_shipping_address]']   = $this->checkBillingShippingAddress( '', $this->order );
		$shortcode['[yaymail_order_status]']                     = strtolower( wc_get_order_status_name( $this->order->get_status() ) );
		$shortcode['[yaymail_shipment_tracking_title]']          = $this->shipmentTrackingTitle( '', $this->order );

		if ( class_exists( 'WC_Admin_Custom_Order_Fields' ) ) {
			$shortcode['[yaymail_order_meta:_wc_additional_order_details]'] = $this->orderMetaWcAdditionalOrderDetails( '', $this->order );
		}
		if ( class_exists( 'EventON' ) ) {
			$shortcode['[yaymail_order_meta:_event_on_list]'] = $this->eventOnList( '', $this->order );
		}
		if ( class_exists( 'YITH_WooCommerce_Order_Tracking_Premium' ) ) {
			$shortcode['[yaymail_order_carrier_name]']  = $this->orderCarrierName( '', $this->order );
			$shortcode['[yaymail_order_pickup_date]']   = $this->orderPickupDate( '', $this->order );
			$shortcode['[yaymail_order_track_code]']    = $this->orderTrackCode( '', $this->order );
			$shortcode['[yaymail_order_tracking_link]'] = $this->orderTrackingLink( '', $this->order );
		}

		if ( ! empty( parse_url( get_site_url() )['host'] ) ) {
			$shortcode['[yaymail_domain]'] = parse_url( get_site_url() )['host'];
		} else {
			$shortcode['[yaymail_domain]'] = '';
		}

		$shortcode['[yaymail_user_account_url]']        = '<a style="color:' . $text_link_color . '; font-weight: normal; text-decoration: underline;" href="' . wc_get_page_permalink( 'myaccount' ) . '">' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '</a>';
		$shortcode['[yaymail_user_account_url_string]'] = wc_get_page_permalink( 'myaccount' );

		// ADDITIONAL ORDER META:
		$order_metaArr = get_post_meta( $this->order_id );
		if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
			foreach ( $order_metaArr as $k => $v ) {
				$nameField    = str_replace( ' ', '_', trim( $k ) );
				$nameShorcode = '[yaymail_post_meta:' . $nameField . ']';

				// when array $v has tow value ???
				if ( is_array( $v ) && count( $v ) > 0 ) {
					$shortcode[ $nameShorcode ] = trim( $v[0] );
				} else {
					$shortcode[ $nameShorcode ] = trim( $v );
				}
			}
		}

		/*
		To get custom fields support Checkout Field Editor for WooCommerce */
		// funtion wc_get_custom_checkout_fields of Plugin Checkout Field Editor ????
		// if (!empty($order)) {
		// if (function_exists('wc_get_custom_checkout_fields')) {
		// $custom_fields = wc_get_custom_checkout_fields($order);
		// if (!empty($custom_fields)) {
		// foreach ($custom_fields as $key => $custom_field) {
		// $shortcode['[yaymail_' . $key . ']'] = get_post_meta($order->get_id(), $key, true);
		// }
		// }
		// }
		// }
		if ( ! empty( $order ) ) {
			foreach ( $order->get_meta_data() as $meta ) {
				$nameField    = str_replace( ' ', '_', trim( $meta->get_data()['key'] ) );
				$nameShorcode = '[yaymail_order_meta:' . $nameField . ']';
				if ( '_wc_shipment_tracking_items' == $nameField ) {
					$shortcode[ $nameShorcode ] = $this->orderMetaWcShipmentTrackingItems( '', $this->order );
				} else {
					if ( is_array( $meta->get_data()['value'] ) ) {
						$checkNestedArray = false;
						foreach ( $meta->get_data()['value'] as $value ) {
							if ( is_object( $value ) || is_array( $value ) ) {
								$checkNestedArray = true;
								break;
							}
						}
						if ( false == $checkNestedArray ) {
							$shortcode[ $nameShorcode ] = implode( ', ', $meta->get_data()['value'] );
						} else {
							$shortcode[ $nameShorcode ] = '';
						}
					} else {
						$shortcode[ $nameShorcode ] = $meta->get_data()['value'];
					}
				}
			}
		}

		$this->order_data = $shortcode;
	}

	public function defaultSampleOrderData( $sent_to_admin = '' ) {
		$current_user         = wp_get_current_user();
		$postID               = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color      = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
		$billing_address      = "John Doe<br/>YayCommerce<br/>7400 Edwards Rd<br/>Edwards Rd<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		$shipping_address     = "John Doe<br/>YayCommerce<br/>755 E North Grove Rd<br/>Mayville, Michigan<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		$user_id              = get_current_user_id();
		$yaymail_settings     = get_option( 'yaymail_settings' );
		$yaymail_informations = array(
			'post_id'          => $postID,
			'yaymail_elements' => get_post_meta( $postID, '_yaymail_elements', true ),
			'template'         => $this->template,
			'general_settings' => array(
				'tableWidth'           => $yaymail_settings['container_width'],
				'emailBackgroundColor' => get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC',
				'textLinkColor'        => get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A',
			),
		);

		// Link Downloadable Product
		$shortcode['[yaymail_items_downloadable_title]']   = $this->itemsDownloadableTitle( '', $this->order, $sent_to_admin, '' ); // done
		$shortcode['[yaymail_items_downloadable_product]'] = $this->itemsDownloadableProduct( '', $this->order, $sent_to_admin, '' ); // done

		// ORDER DETAILS
		$shortcode['[yaymail_items_border]'] = $this->itemsBorder( '', $this->order, $sent_to_admin ); // done

		$shortcode['[yaymail_items_border_before]']  = $this->itemsBorderBefore( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_after]']   = $this->itemsBorderAfter( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_title]']   = $this->itemsBorderTitle( '', $this->order, $sent_to_admin ); // done
		$shortcode['[yaymail_items_border_content]'] = $this->itemsBorderContent( '', $this->order, $sent_to_admin ); // done

		// WC HOOK
		$shortcode['[woocommerce_email_before_order_table]']  = $this->orderWoocommerceBeforeHook( array(), $sent_to_admin, 'sampleOrder' ); // not changed
		$shortcode['[woocommerce_email_after_order_table]']   = $this->orderWoocommerceAfterHook( array(), $sent_to_admin, 'sampleOrder' ); // not changed
		$shortcode['[yaymail_woocommerce_email_sozlesmeler]'] = $this->woocommerceEmailSozlesmeler( '', $this->order, $sent_to_admin ); // not Changed

		// support plugin Support Back In Stock Notifier for WooCommerce
		if ( class_exists( 'CWG_Trigger_Instock_Mail' ) || class_exists( 'CWG_Trigger_Subscribe_Mail' ) ) {
			$shortcode['[yaymail_notifier_product_name]']       = 'YayMail';
			$shortcode['[yaymail_notifier_product_id]']         = '1';
			$shortcode['[yaymail_notifier_product_link]']       = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
			$shortcode['[yaymail_notifier_shopname]']           = get_bloginfo( 'name' );
			$shortcode['[yaymail_notifier_email_id]']           = 'yaymail@gmail.com';
			$shortcode['[yaymail_notifier_subscriber_email]']   = 'yaymail@gmail.com';
			$shortcode['[yaymail_notifier_subscriber_name]']    = 'YayMail';
			$shortcode['[yaymail_notifier_cart_link]']          = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
			$shortcode['[yaymail_notifier_only_product_name]']  = 'YayMail';
			$shortcode['[yaymail_notifier_only_product_sku]']   = '1';
			$shortcode['[yaymail_notifier_only_product_image]'] = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
		}
		// Yith-woocommerce-wishlist-premium
		// if ( function_exists( 'yith_wishlist_constructor' ) ) {
		// $shortcode['[yaymail_yith_wishlist_email_admin]']      = $this->yithWishlistEmailAdmin( '', $this->order, $sent_to_admin );
		// $shortcode['[yaymail_yith_wishlist_item_title]']       = $this->yithWishlistItemTitle( '', $this->order, $sent_to_admin );
		// $shortcode['[yaymail_yith_wishlist_item]']             = $this->yithWishlistItem( '', $this->order, $sent_to_admin );
		// $shortcode['[yaymail_yith_wishlist_additional_info]']  = $this->yithWishlistAdditionalInfo( '', $this->order, $sent_to_admin );
		// $shortcode['[yaymail_yith_wishlist_additional_data]']  = $this->yithWishlistAdditionalData( '', $this->order, $sent_to_admin );
		// $shortcode['[yaymail_yith_wishlist_customer_details]'] = $this->yithWishlistCustomerDetails( '', $this->order, $sent_to_admin );
		// }

		// Define shortcode from plugin addon
		$shortcode = apply_filters( 'yaymail_do_shortcode', $shortcode, $yaymail_informations, '' );

		$shortcode['[yaymail_items]']           = $this->orderItems( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_order_date]']      = gmdate( 'd-m-Y' );
		$shortcode['[yaymail_order_fee]']       = 0;
		$shortcode['[yaymail_order_id]']        = 1;
		$shortcode['[yaymail_order_link]']      = '<a href="" style="color:' . $text_link_color . ';">' . esc_html__( 'Order', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_number]']    = '1';
		$shortcode['[yaymail_order_refund]']    = 0;
		$shortcode['[yaymail_order_sub_total]'] = '£18.00';
		$shortcode['[yaymail_order_total]']     = '£18.00';

		// PAYMENTS
		$shortcode['[yaymail_order_payment_method]']     = 'Direct bank transfer';
		$shortcode['[yaymail_order_payment_url]']        = '<a href="">' . esc_html__( 'Payment page', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_payment_url_string]'] = '';
		$shortcode['[yaymail_payment_method]']           = 'Check payments';
		$shortcode['[yaymail_transaction_id]']           = 1;

		// SHIPPINGS
		$shortcode['[yaymail_order_shipping]']      = '333';
		$shortcode['[yaymail_shipping_address]']    = $shipping_address;
		$shortcode['[yaymail_shipping_address_1]']  = '755 E North Grove Rd';
		$shortcode['[yaymail_shipping_address_2]']  = '755 E North Grove Rd';
		$shortcode['[yaymail_shipping_city]']       = 'Mayville, Michigan';
		$shortcode['[yaymail_shipping_company]']    = 'YayCommerce';
		$shortcode['[yaymail_shipping_country]']    = '';
		$shortcode['[yaymail_shipping_first_name]'] = 'John';
		$shortcode['[yaymail_shipping_last_name]']  = 'Doe';
		$shortcode['[yaymail_shipping_method]']     = '';
		$shortcode['[yaymail_shipping_postcode]']   = '48744';
		$shortcode['[yaymail_shipping_state]']      = 'Random';

		// BILLING
		$shortcode['[yaymail_billing_address]']    = $billing_address;
		$shortcode['[yaymail_billing_address_1]']  = '7400 Edwards Rd';
		$shortcode['[yaymail_billing_address_2]']  = '7400 Edwards Rd';
		$shortcode['[yaymail_billing_city]']       = 'Edwards Rd';
		$shortcode['[yaymail_billing_company]']    = 'YayCommerce';
		$shortcode['[yaymail_billing_country]']    = '';
		$shortcode['[yaymail_billing_email]']      = 'johndoe@gmail.com';
		$shortcode['[yaymail_billing_first_name]'] = 'John';
		$shortcode['[yaymail_billing_last_name]']  = 'Doe';
		$shortcode['[yaymail_billing_phone]']      = '(910) 529-1147';
		$shortcode['[yaymail_billing_postcode]']   = '48744';
		$shortcode['[yaymail_billing_state]']      = 'Random';

		// RESET PASSWORD:
		$shortcode['[yaymail_password_reset_url]']        = '<a style="color:' . $text_link_color . ';" href="">' . esc_html__( 'Click here to reset your password', 'woocommerce' ) . '</a>';
		$shortcode['[yaymail_password_reset_url_string]'] = get_site_url() . '/my-account/lost-password/?login';

		// NEW USERS:
		$shortcode['[yaymail_user_new_password]']    = 'G(UAM1(eIX#G';
		$shortcode['[yaymail_user_activation_link]'] = '';

		// GENERALS
		$shortcode['[yaymail_customer_note]']            = 'note';
		$shortcode['[yaymail_customer_notes]']           = 'notes';
		$shortcode['[yaymail_customer_provided_note]']   = 'provided note';
		$shortcode['[yaymail_site_name]']                = get_bloginfo( 'name' );
		$shortcode['[yaymail_site_url]']                 = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
		$shortcode['[yaymail_site_url_string]']          = get_site_url();
		$shortcode['[yaymail_user_email]']               = $current_user->data->user_email;
		$shortcode['[yaymail_user_id]']                  = $user_id;
		$shortcode['[yaymail_customer_username]']        = $current_user->data->user_login;
		$shortcode['[yaymail_customer_name]']            = get_user_meta( $current_user->data->ID, 'first_name', true ) . ' ' . get_user_meta( $current_user->data->ID, 'last_name', true );
		$shortcode['[yaymail_view_order_url]']           = '';
		$shortcode['[yaymail_view_order_url_string]']    = '';
		$shortcode['[yaymail_billing_shipping_address]'] = $this->billingShippingAddress( '', $this->order ); // done

		$shortcode['[yaymail_billing_shipping_address_title]']   = $this->billingShippingAddressTitle( '', $this->order ); // done
		$shortcode['[yaymail_billing_shipping_address_content]'] = $this->billingShippingAddressContent( '', $this->order ); // done
		$shortcode['[yaymail_check_billing_shipping_address]']   = $this->checkBillingShippingAddress( '', $this->order ); // done
		$shortcode['[yaymail_order_status]']                     = 'sample status'; // done

		$shortcode['[yaymail_domain]']                  = parse_url( get_site_url() )['host'];
		$shortcode['[yaymail_user_account_url]']        = '<a style="color:' . $text_link_color . '; font-weight: normal; text-decoration: underline;" href="' . wc_get_page_permalink( 'myaccount' ) . '">' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '</a>';
		$shortcode['[yaymail_user_account_url_string]'] = wc_get_page_permalink( 'myaccount' );

		$shortcode['[yaymail_order_meta:_wc_shipment_tracking_items]'] = $this->orderMetaWcShipmentTrackingItems( '', $this->order );
		$shortcode['[yaymail_shipment_tracking_title]']                = $this->shipmentTrackingTitle( '', $this->order );

		if ( class_exists( 'WC_Admin_Custom_Order_Fields' ) ) {
			$shortcode['[yaymail_order_meta:_wc_additional_order_details]'] = $this->orderMetaWcAdditionalOrderDetails( '', $this->order );
		}
		if ( class_exists( 'EventON' ) ) {
			$shortcode['[yaymail_order_meta:_event_on_list]'] = $this->eventOnList( '', $this->order );
		}
		if ( class_exists( 'YITH_WooCommerce_Order_Tracking_Premium' ) ) {
			$shortcode['[yaymail_order_carrier_name]']  = $this->orderCarrierName( '', $this->order );
			$shortcode['[yaymail_order_pickup_date]']   = $this->orderPickupDate( '', $this->order );
			$shortcode['[yaymail_order_track_code]']    = $this->orderTrackCode( '', $this->order );
			$shortcode['[yaymail_order_tracking_link]'] = $this->orderTrackingLink( '', $this->order );
		}

		// ADDITIONAL ORDER META:
		$order         = CustomPostType::getListOrders();
		$order_metaArr = get_post_meta( isset( $order[0]['id'] ) ? $order[0]['id'] : '' );
		if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
			foreach ( $order_metaArr as $k => $v ) {
				$nameField    = str_replace( ' ', '_', trim( $k ) );
				$nameShorcode = '[yaymail_post_meta:' . $nameField . ']';

				// when array $v has tow value ???
				if ( is_array( $v ) && count( $v ) > 0 ) {
					$shortcode[ $nameShorcode ] = trim( $v[0] );
				} else {
					$shortcode[ $nameShorcode ] = trim( $v );
				}
			}
		}

		$this->order_data = $shortcode;
	}

	public function ordetItemTables( $order, $default_args ) {
		$items            = $order->get_items();
		$path             = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-items.php';
		$yaymail_settings = get_option( 'yaymail_settings' );

		$show_product_image            = isset( $yaymail_settings['product_image'] ) ? $yaymail_settings['product_image'] : 0;
		$show_product_sku              = isset( $yaymail_settings['product_sku'] ) ? $yaymail_settings['product_sku'] : 0;
		$default_args['image_size'][0] = isset( $yaymail_settings['image_width'] ) ? $yaymail_settings['image_width'] : 32;
		$default_args['image_size'][1] = isset( $yaymail_settings['image_height'] ) ? $yaymail_settings['image_height'] : 32;
		$default_args['image_size'][2] = isset( $yaymail_settings['image_size'] ) ? $yaymail_settings['image_size'] : 'thumbnail';

		$args = array(
			'order'                         => $order,
			'items'                         => $order->get_items(),
			'show_download_links'           => $order->is_download_permitted() && ! $default_args['sent_to_admin'],
			'show_sku'                      => $show_product_sku,
			'show_purchase_note'            => $order->is_paid() && ! $default_args['sent_to_admin'],
			'show_image'                    => $show_product_image,
			'image_size'                    => $default_args['image_size'],
			'plain_text'                    => $default_args['plain_text'],
			'sent_to_admin'                 => $default_args['sent_to_admin'],
			'order_item_table_border_color' => isset( $yaymail_settings['background_color_table_items'] ) ? $yaymail_settings['background_color_table_items'] : '#dddddd',
		);
		include $path;
	}
	public function itemsBorder( $atts, $order, $sent_to_admin = '' ) {
		if ( null === $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

	}

	/* Link Downloadable Product - start */
	public function itemsDownloadableTitle( $atts, $order, $sent_to_admin = '', $items = array() ) {
		if ( null !== $order ) {
			$items     = $order->get_items();
			$downloads = $order->get_downloadable_items();
		}
		ob_start();
		$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-item-download-title.php';
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	public function itemsDownloadableProduct( $atts, $order, $sent_to_admin = '', $items = array() ) {
		if ( null === $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-item-download.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			$items = $order->get_items();
			ob_start();
			$downloads = $order->get_downloadable_items();
			$path      = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-item-download.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	/* Order items border - start */
	public function itemsBorderBefore( $atts, $order, $sent_to_admin = '' ) {
		if ( null === $order ) {
			return '';
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-before.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function itemsBorderAfter( $atts, $order, $sent_to_admin = '' ) {
		if ( null === $order ) {
			return '';
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-after.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function itemsBorderTitle( $atts, $order, $sent_to_admin = '' ) {
		if ( null === $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border-title.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-title.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function itemsBorderContent( $atts, $order, $sent_to_admin = '' ) {
		if ( null === $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border-content.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-content.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	/* Order items border - end */

	public function billingShippingAddress( $atts, $order ) {
		$postID          = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
		if ( null !== $order ) {
			$shipping_address = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->shipping_address : $order->get_formatted_shipping_address();
			$billing_address  = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->billing_address : $order->get_formatted_billing_address();
			if ( $order->get_billing_phone() ) {
				$billing_address .= "<br/> <a href='tel:" . esc_html( $order->get_billing_phone() ) . "' style='color:" . $text_link_color . "; font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_phone() ) . '</a>';
			}
			if ( $order->get_billing_email() ) {
				$billing_address .= "<br/><a href='mailto:" . esc_html( $order->get_billing_email() ) . "' style='color:" . $text_link_color . ";font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_email() ) . '</a>';
			}
		} else {
			$billing_address  = "John Doe<br/>YayCommerce<br/>7400 Edwards Rd<br/>Edwards Rd<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
			$shipping_address = "John Doe<br/>YayCommerce<br/>755 E North Grove Rd<br/>Mayville, Michigan<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		}
		ob_start();
		$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address.php';
		$order = $this->order;
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;

	}

	/* Billing Shipping Address - start */
	public function billingShippingAddressTitle( $atts, $order ) {
		$postID          = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
		if ( null !== $order ) {
			$shipping_address = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->shipping_address : $order->get_formatted_shipping_address();
			$billing_address  = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->billing_address : $order->get_formatted_billing_address();
		} else {
			$billing_address  = "John Doe<br/>YayCommerce<br/>7400 Edwards Rd<br/>Edwards Rd<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
			$shipping_address = "John Doe<br/>YayCommerce<br/>755 E North Grove Rd<br/>Mayville, Michigan<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		}
		ob_start();
		$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address-title.php';
		$order = $this->order;
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function checkBillingShippingAddress( $atts, $order_id ) {
		$isShippingAddress = false;
		$isBillingAddress  = false;

		if ( ! empty( $billing_address ) ) {
			$isBillingAddress = true;
		}
		if ( ! empty( $shipping_address ) ) {
			$isShippingAddress = true;
		}

		$args = array(
			'isShippingAddress' => $isShippingAddress,
			'isBillingAddress'  => $isBillingAddress,
		);

		return 'Checking_here';
	}

	public function billingShippingAddressContent( $atts, $order ) {
		$postID          = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
		if ( null !== $order ) {
			$shipping_address = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->shipping_address : $order->get_formatted_shipping_address();
			$billing_address  = class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) ? $this->billing_address : $order->get_formatted_billing_address();
			if ( $order->get_billing_phone() ) {
				$billing_address .= "<br/> <a href='tel:" . esc_html( $order->get_billing_phone() ) . "' style='color:" . $text_link_color . "; font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_phone() ) . '</a>';
			}
			if ( $order->get_billing_email() ) {
				$billing_address .= "<br/><a href='mailto:" . esc_html( $order->get_billing_email() ) . "' style='color:" . $text_link_color . ";font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_email() ) . '</a>';
			}
		} else {
			$billing_address  = "John Doe<br/>YayCommerce<br/>7400 Edwards Rd<br/>Edwards Rd<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
			$shipping_address = "John Doe<br/>YayCommerce<br/>755 E North Grove Rd<br/>Mayville, Michigan<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		}
		ob_start();
		$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address-content.php';
		$order = $this->order;
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	/* Billing Shipping Address - end */

	public function orderItems( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;

		} else {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

	}
	public function getOrderCustomerNotes( $customerNotes ) {
		ob_start();
		foreach ( $customerNotes as $customerNote ) {
			?>
				<?php echo wp_kses_post( $customerNote->comment_content ); ?>
			<?php
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/** Admin custom fields */
	public function orderMetaWcAdditionalOrderDetails( $atts, $order, $sent_to_admin = '' ) {
		if ( ! $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/additional-order-details.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
		} else {
			$order_id            = $order->get_id();
			$order_fields        = array();
			$order               = $order_id ? wc_get_order( $order_id ) : null;
			$custom_order_fields = get_option( 'wc_admin_custom_order_fields' );
			if ( ! is_array( $custom_order_fields ) ) {
				$custom_order_fields = array();
			}
			foreach ( $custom_order_fields as $key => $field ) {
				$order_field = new \WC_Custom_Order_Field( $key, $field );
				$have_value  = false;
				if ( $order instanceof \WC_Order ) {
					$set_value = false;
					$value     = '';
					if ( metadata_exists( 'post', $order_id, $order_field->get_meta_key() ) ) {
						$set_value = true;
						$value     = $order->get_meta( $order_field->get_meta_key() );
					}
					if ( $set_value ) {
						$order_field->set_value( $value );
						$have_value = true;
					}
				}
				if ( $return_all || $have_value ) {
					$order_fields[ $key ] = $order_field;
				}
			}
			if ( ! empty( $order_fields ) ) {
				ob_start();
				$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/additional-order-details.php';
				include $path;
				$html = ob_get_contents();
				ob_end_clean();
			} else {
				$html = '';
			}
		}
		return $html;
	}
	/** Event On */
	public function eventOnList( $atts, $order, $sent_to_admin = '' ) {
		ob_start();
		$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/event-on-list.php';
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/* Woo Shipment Tracking - Start */
	public function orderMetaWcShipmentTrackingItems( $atts, $order, $sent_to_admin = '' ) {
		ob_start();
		$order = $this->order;
		if ( ( ! class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) ) {
			ob_end_clean();
			return null;
		}
		$setClassAvtive = null;

		if ( ! $order ) {
			if ( class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
				$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/wc_shipment_tracking-info.php';
			}
			if ( class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
				$ast  = \WC_Advanced_Shipment_Tracking_Actions::get_instance();
				$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/wc_advanced_shipment_tracking-info.php';
			}
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

		$order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		if ( class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
			$sta            = \WC_Shipment_Tracking_Actions::get_instance();
			$tracking_items = $sta->get_tracking_items( $order_id, true );
			if ( $tracking_items ) {
				$setClassAvtive = 'WC_Shipment_Tracking_Actions';
				$path           = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc_shipment_tracking-info.php';
				include $path;
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
			} else {
				ob_end_clean();
				return null;
			}
		}
		if ( class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
			$ast            = \WC_Advanced_Shipment_Tracking_Actions::get_instance();
			$order_id       = $ast->get_formated_order_id( $order_id );
			$tracking_items = $ast->get_tracking_items( $order_id, true );
			if ( $tracking_items ) {
				$setClassAvtive = 'WC_Advanced_Shipment_Tracking_Actions';
				$path           = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc_advanced_shipment_tracking-info.php';
				include $path;
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
			} else {
				ob_end_clean();
				return null;
			}
		}
	}

	public function shipmentTrackingTitle( $atts, $order, $sent_to_admin = '' ) {
		ob_start();
		$order = $this->order;
		if ( ( ! class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) ) {
			ob_end_clean();
			return null;
		}
		if ( class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc_shipment_tracking-title.php';
		}
		if ( class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
			$ast  = \WC_Advanced_Shipment_Tracking_Actions::get_instance();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc_advanced_shipment_tracking-title.php';
		}
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	/*  Woo Shipment Tracking - End */

	/*  Woocommerce Hook - End */
	public function orderWoocommerceBeforeHook( $args, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			return '[woocommerce_email_before_order_table]';
		} else {
			$order = $this->order;
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc-email-before.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	public function orderWoocommerceAfterHook( $args, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			return '[woocommerce_email_after_order_table]';
		} else {
			$order = $this->order;
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc-email-after.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	public function woocommerceEmailSozlesmeler( $atts, $order, $sent_to_admin = '' ) {
		if ( function_exists( 'woocontracts_maile_ekle' ) ) {
			$order = $this->order;
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc-email-sozlesmeler.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			return '[yaymail_woocommerce_email_sozlesmeler]';
		}

	}

	/** Yith tracking order */
	public function orderCarrierName( $atts, $order ) {
		if ( null !== $order ) {
			$data         = \YITH_Tracking_Data::get( $order );
			$carriers     = \Carriers::get_instance()->get_carrier_list();
			$carrier_id   = $data->get_carrier_id();
			$carrier_name = isset( $carriers[ $carrier_id ] ) ? $carriers[ $carrier_id ]['name'] : '';
			$html         = $carrier_name;
		} else {
			$html = get_option( 'ywot_carrier_default_name' );
		}
		return $html;
	}
	public function orderPickupDate( $atts, $order ) {
		if ( null !== $order ) {
			$data = \YITH_Tracking_Data::get( $order );
			$html = date_i18n( get_option( 'date_format' ), strtotime( $data->get_pickup_date() ) );
		} else {
			$html = date_i18n( get_option( 'date_format' ), strtotime( gmdate( 'm-d-y' ) ) );
		}
		return $html;
	}
	public function orderTrackCode( $atts, $order ) {
		if ( null !== $order ) {
			$data          = \YITH_Tracking_Data::get( $order );
			$tracking_code = $data->get_tracking_code();
			if ( strpos( $tracking_code, '{' ) !== false ) {
				preg_match_all( '/{(.*?)}/', $tracking_code, $words );
				$tracking_code = implode( ' ', $words[1] );
			}
			$html = $tracking_code;
		} else {
			$html = 'SAMPLE_TRACKING_CODE';
		}
		return $html;
	}
	public function orderTrackingLink( $atts, $order ) {
		$postID          = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
		if ( null !== $order ) {
			$html = '';
			$data = \YITH_Tracking_Data::get( $order );
			if ( $data->is_pickedup() ) {
				$order_tracking_code     = $data->get_tracking_code();
				$order_tracking_postcode = $data->get_tracking_postcode();
				$order_carrier_id        = $data->get_carrier_id();
				$carriers                = \Carriers::get_instance()->get_carrier_list();
				if ( ! isset( $carriers[ $order_carrier_id ] ) ) {
					return '';
				}

				$carrier_object = $carriers[ $order_carrier_id ];

				// Check if tracking code is single or multiple
				if ( strpos( $order_tracking_code, '{' ) !== false ) {
					$order_track_url = $carrier_object['track_url'];

					preg_match_all( '/{(.*?)}/', $order_tracking_code, $words );
					$length_word = count( $words[1] );
					for ( $i = 0; $i < $length_word; $i++ ) {
						$order_track_url = str_replace( '[TRACK_CODE][' . $i . ']', $words[1][ $i ], $order_track_url );
					}
				} else {

					$text            = array( '[TRACK_CODE]', '[TRACK_POSTCODE]' );
					$codes           = array( $order_tracking_code, $order_tracking_postcode );
					$order_track_url = str_replace( $text, $codes, $carrier_object['track_url'] );
				}

				if ( strpos( $order_track_url, '[TRACK_YEAR]' ) !== false || strpos( $order_track_url, '[TRACK_MONTH]' ) !== false || strpos( $order_track_url, '[TRACK_DAY]' ) !== false ) {
					$date            = $data->get_pickup_date();
					$array_date      = explode( '-', $date );
					$order_track_url = str_replace( '[TRACK_YEAR]', $array_date[0], $order_track_url );
					$order_track_url = str_replace( '[TRACK_MONTH]', $array_date[1], $order_track_url );
					$order_track_url = str_replace( '[TRACK_DAY]', $array_date[2], $order_track_url );
				}
				$html = "<a style='color: " . esc_attr( $text_link_color ) . "' href='" . esc_url( $order_track_url ) . "'>" . __( 'Live track your order', 'yith-woocommerce-order-tracking' ) . '</a>';
			}
		} else {
			$html = "<a style='color: " . esc_attr( $text_link_color ) . "' href='#'>" . __( 'Live track your order', 'yith-woocommerce-order-tracking' ) . '</a>';
		}
		return $html;
	}
}
