<?php

namespace YayMail\Page;

use YayMail\Ajax;
use YayMail\Page\Source\CustomPostType;
use YayMail\Page\Source\DefaultElement;
use YayMail\Templates\Templates;
use YayMail\Page\Source\PolylangHandler;
use YayMail\Page\Source\WPMLHandler;

defined( 'ABSPATH' ) || exit;
/**
 * Settings Page
 */
class Settings {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private $email_customizer_hook_surfix = null;
	private $pageId                       = null;
	private $templateAccount;
	private $emails = null;
	public function doHooks() {
		$this->templateAccount = array( 'customer_new_account', 'customer_new_account_activation', 'customer_reset_password' );

		// Register Custom Post Type use Email Builder
		add_action( 'init', array( $this, 'registerCustomPostType' ) );

		// Register Menu
		add_action( 'admin_menu', array( $this, 'settingsMenu' ) );

		// Register Style & Script use for Menu Backend
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );

		add_filter( 'plugin_action_links_' . YAYMAIL_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );

		// Add Woocommerce email setting columns
		add_filter( 'woocommerce_email_setting_columns', array( $this, 'yaymail_email_setting_columns' ) );
		add_action( 'woocommerce_email_setting_column_template', array( $this, 'column_template' ) );

		// Excute Ajax
		Ajax::getInstance();
	}
	public function __construct() {}

	public function yaymail_email_setting_columns( $array ) {
		if ( isset( $array['actions'] ) ) {
			unset( $array['actions'] );
			return array_merge(
				$array,
				array(
					'template' => '',
					'actions'  => '',
				)
			);
		}
		return $array;
	}
	public function column_template( $email ) {
		echo '<td class="wc-email-settings-table-template">
				<a class="button alignright" target="_blank" href="' . esc_attr( admin_url( 'admin.php?page=yaymail-settings' ) ) . '&template=' . esc_attr( $email->id ) . '">' . esc_html( __( 'Customize with YayMail', 'yaymail' ) ) . '</a></td>';
	}

	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=yaymail-settings' ) . '" aria-label="' . esc_attr__( 'View WooCommerce Email Builder', 'yaymail' ) . '">' . esc_html__( 'Settings', 'yaymail' ) . '</a>',
		);
		return array_merge( $action_links, $links );
	}

	public function registerCustomPostType() {
		$labels       = array(
			'name'               => __( 'Email Template', 'yaymail' ),
			'singular_name'      => __( 'Email Template', 'yaymail' ),
			'add_new'            => __( 'Add New Email Template', 'yaymail' ),
			'add_new_item'       => __( 'Add a new Email Template', 'yaymail' ),
			'edit_item'          => __( 'Edit Email Template', 'yaymail' ),
			'new_item'           => __( 'New Email Template', 'yaymail' ),
			'view_item'          => __( 'View Email Template', 'yaymail' ),
			'search_items'       => __( 'Search Email Template', 'yaymail' ),
			'not_found'          => __( 'No Email Template found', 'yaymail' ),
			'not_found_in_trash' => __( 'No Email Template currently trashed', 'yaymail' ),
			'parent_item_colon'  => '',
		);
		$capabilities = array();
		$args         = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'yaymail_template',
			'capabilities'       => $capabilities,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'author', 'thumbnail' ),
		);
		register_post_type( 'yaymail_template', $args );
	}
	public function settingsMenu() {
		$this->email_customizer_hook_surfix = add_submenu_page( 'woocommerce', __( 'Email Builder Settings', 'yaymail' ), __( 'Email Customizer', 'yaymail' ), 'manage_options', $this->getPageId(), array( $this, 'settingsPage' ) );
	}


	public function nitWebPluginRegisterButtons( $buttons ) {
		$buttons[] = 'table';
		$buttons[] = 'searchreplace';
		$buttons[] = 'visualblocks';
		$buttons[] = 'code';
		$buttons[] = 'insertdatetime';
		$buttons[] = 'autolink';
		$buttons[] = 'contextmenu';
		$buttons[] = 'advlist';
		return $buttons;
	}

	public function njtWebPluginRegisterPlugin( $plugin_array ) {
		$plugin_array['table']          = YAYMAIL_PLUGIN_URL . 'assets/tinymce/table/plugin.min.js';
		$plugin_array['searchreplace']  = YAYMAIL_PLUGIN_URL . 'assets/tinymce/searchreplace/plugin.min.js';
		$plugin_array['visualblocks']   = YAYMAIL_PLUGIN_URL . 'assets/tinymce/visualblocks/plugin.min.js';
		$plugin_array['code']           = YAYMAIL_PLUGIN_URL . 'assets/tinymce/code/plugin.min.js';
		$plugin_array['insertdatetime'] = YAYMAIL_PLUGIN_URL . 'assets/tinymce/insertdatetime/plugin.min.js';
		$plugin_array['autolink']       = YAYMAIL_PLUGIN_URL . 'assets/tinymce/autolink/plugin.min.js';
		$plugin_array['contextmenu']    = YAYMAIL_PLUGIN_URL . 'assets/tinymce/contextmenu/plugin.min.js';
		$plugin_array['advlist']        = YAYMAIL_PLUGIN_URL . 'assets/tinymce/advlist/plugin.min.js';
		return $plugin_array;
	}

	public function settingsPage() {
		// When load this page will not show adminbar
		?>
		<style type="text/css">
			#wpcontent, #footer {opacity: 0}
			#adminmenuback, #adminmenuwrap { display: none !important; }
		</style>
		<script type="text/javascript" id="yaymail-onload">
			jQuery(document).ready( function() {
				jQuery('#adminmenuback, #adminmenuwrap').remove();
			});
		</script>
		<?php
		// add new buttons
		add_filter( 'mce_buttons', array( $this, 'nitWebPluginRegisterButtons' ) );

		// Load the TinyMCE plugin
		add_filter( 'mce_external_plugins', array( $this, 'njtWebPluginRegisterPlugin' ) );
		$viewPath = YAYMAIL_PLUGIN_PATH . 'views/pages/html-settings.php';
		include_once $viewPath;
	}

	public function enqueueAdminScripts( $screenId ) {

		global $wpdb, $sitepress;
		if ( class_exists( 'SitePress' ) ) {
			add_filter(
				'woocommerce_order_get_items',
				function( $items, $order ) {
					$order_id = $order->get_id();
					WPMLHandler::wpml_get_current_language_multilingual( $order_id );
				},
				9,
				2
			);
		}
		if ( class_exists( 'SitePress' ) ) {
			WPMLHandler::wpml_turn_off_dupplicate_post_type();
			WPMLHandler::wpml_turn_on_admin_edit_language();
			global $pagenow;
			if ( 'post.php' === $pagenow ) {
				if ( isset( $_REQUEST['post'] ) && ! empty( $_REQUEST['post'] ) && isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) {
					$post_type = get_post_type( sanitize_text_field( $_REQUEST['post'] ) );
					if ( 'shop_order' === $post_type ) {
						$current_order_language = WPMLHandler::wpml_get_current_language_multilingual( sanitize_text_field( $_REQUEST['post'] ) );
						if ( ! class_exists( 'woocommerce_wpml' ) ) {
							$sitepress->switch_lang( $current_order_language, true );
						}
					}
				}
			}
		}
		if ( class_exists( 'Polylang' ) ) {
			PolylangHandler::polylang_turn_off_dupplicate_post_type();
			global $pagenow;
			if ( 'post.php' === $pagenow ) {
				if ( isset( $_REQUEST['post'] ) && ! empty( $_REQUEST['post'] ) && isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) {
					$post_type = get_post_type( sanitize_text_field( $_REQUEST['post'] ) );
					if ( 'shop_order' === $post_type ) {
						$current_edit_order = new \WC_Order( sanitize_text_field( $_REQUEST['post'] ) );
						$edit_order_items   = $current_edit_order->get_items();
						foreach ( $edit_order_items as $key => $item ) {
							$edit_product_id = $item->get_product_id();
							break;
						}
						$current_order_language = pll_get_post_language( $edit_product_id );
						PolylangHandler::polylang_switch_language_front_end( $current_order_language );
					}
				}
			}
		}
		if ( strpos( $screenId, 'yaymail-settings' ) !== false && class_exists( 'WC_Emails' ) ) {
			// Get list template from Woo
			$wc_emails    = \WC_Emails::instance();
			$this->emails = (array) $wc_emails::instance()->emails;
			if ( class_exists( 'CWG_Trigger_Instock_Mail' ) ) {
				$notifierInstockMail = array(
					'WC_Notifier_Instock_Mail' => array(
						'id'    => 'notifier_instock_mail',
						'title' => 'Notifier Instock Mail',
					),
				);
				$this->emails        = array_merge( $this->emails, $notifierInstockMail );
			}

			if ( class_exists( 'CWG_Trigger_Subscribe_Mail' ) ) {
				$notifierSubscribeMail = array(
					'WC_Notifier_Subscribe_Mail' => array(
						'id'    => 'notifier_subscribe_mail',
						'title' => 'Notifier Subscribe Mail',
					),
				);
				$this->emails          = array_merge( $this->emails, $notifierSubscribeMail );
			}
			$this->emails = apply_filters( 'YaymailCreateGermanMarketTemplates', $this->emails );

			// Check active language
			$listLanguages       = array();
				$active_language = 'en';

			if ( class_exists( 'SitePress' ) ) {
				foreach ( icl_get_languages() as $key => $lang ) {
					$listLanguages[] = array(
						'code'   => $lang['code'],
						'name'   => $lang['native_name'],
						'flag'   => $lang['country_flag_url'],
						'active' => $lang['active'],
					);
				}
				$active_language = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'en';
			} elseif ( class_exists( 'Polylang' ) ) {
				foreach ( icl_get_languages() as $key => $lang ) {
					$listLanguages[] = array(
						'code'   => $lang['language_code'],
						'name'   => $lang['native_name'],
						'flag'   => $lang['country_flag_url'],
						'active' => $lang['active'],
					);
				}
				$active_language = isset( $_COOKIE['pll_language'] ) ? sanitize_text_field( $_COOKIE['pll_language'] ) : 'en';
			}

			update_option( 'yaymail_customizer_page_language', $active_language );

			// Insert database all order template from Woo
			$templateEmail = Templates::getInstance();
			$templates     = $templateEmail::getList();

			foreach ( $templates as $key => $template ) {
				$postIDTemplate = CustomPostType::postIDByTemplate( $key );
				if ( ! $postIDTemplate ) {
					global $wpdb;
					$hold_post_id   = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_yaymail_template' AND meta_value = %s", $key ) );
					$hold_elements  = '';
					$hold_text_link = '';
					if ( count( $hold_post_id ) ) {
						$hold_elements  = get_post_meta( $hold_post_id[0]->post_id, '_yaymail_elements', true );
						$hold_text_link = get_post_meta( $hold_post_id[0]->post_id, '_yaymail_email_textLinkColor_settings', true );
					}
					$arr    = array(
						'mess'                            => '',
						'post_date'                       => current_time( 'Y-m-d H:i:s' ),
						'post_type'                       => 'yaymail_template',
						'post_status'                     => 'publish',
						'_yaymail_template'               => $key,
						'_email_backgroundColor_settings' => 'rgb(236, 236, 236)',
						'_yaymail_elements'               => '' === $hold_elements ? json_decode( $template['elements'], true ) : $hold_elements,
						'_yaymail_email_textLinkColor_settings' => '' === $hold_text_link ? '' : $hold_text_link,
					);
					$insert = CustomPostType::insert( $arr );
				}
			}

			/*
			@@@@ Enable Disable
			@@@@ note: Note the default value section is required when displaying in vue
			 */

			$settingDefaultEnableDisable = array(
				'new_order'                 => 1,
				'cancelled_order'           => 1,
				'failed_order'              => 1,
				'customer_on_hold_order'    => 1,
				'customer_processing_order' => 1,
				'customer_completed_order'  => 1,
				'customer_refunded_order'   => 1,
				'customer_invoice'          => 0,
				'customer_note'             => 0,
				'customer_reset_password'   => 0,
				'customer_new_account'      => 0,
			);

			$settingEnableDisables = ( CustomPostType::templateEnableDisable( false ) ) ? CustomPostType::templateEnableDisable( false ) : $settingDefaultEnableDisable;

			foreach ( $this->emails as $key => $value ) {
				if ( 'ORDDD_Email_Delivery_Reminder' == $key ) {
					$value->id = 'orddd_delivery_reminder_customer';
				}
				if ( isset( $value->id ) ) {
					if ( ! array_key_exists( $value->id, $settingEnableDisables ) ) {
						$settingEnableDisables[ $value->id ] = '0';
					}
				} else {
					if ( ! array_key_exists( $value['id'], $settingEnableDisables ) ) {
						$settingEnableDisables[ $value['id'] ] = '0';
					}
				}
			}

			$this->emails          = apply_filters( 'YaymailCreateFollowUpTemplates', $this->emails );
			$settingEnableDisables = apply_filters( 'YaymailCreateSelectFollowUpTemplates', $settingEnableDisables );

			$settingDefaultGenerals = array(
				'payment'                      => 2,
				'product_image'                => 0,
				'image_size'                   => 'thumbnail',
				'image_width'                  => '30px',
				'image_height'                 => '30px',
				'product_sku'                  => 1,
				'background_color_table_items' => '#e5e5e5',
				'content_items_color'          => '#636363',
				'title_items_color'            => '#96588a',
				'container_width'              => '605px',
				'order_url'                    => '',
				'custom_css'                   => '',
				'enable_css_custom'            => 'no',
				'image_position'               => 'Top',
			);
			$settingGenerals        = get_option( 'yaymail_settings' ) ? get_option( 'yaymail_settings' ) : $settingDefaultGenerals;
			foreach ( $settingDefaultEnableDisable as $keyDefaultEnableDisable => $settingDefaultEnableDisable ) {
				if ( ! array_key_exists( $keyDefaultEnableDisable, $settingEnableDisables ) ) {
					$settingEnableDisables[ $keyDefaultEnableDisable ] = $settingDefaultEnableDisable;
				};
			}
			$settings['enableDisable'] = $settingEnableDisables;

			/*
			@@@@ General
			@@@@ note: Note the default value section is required when displaying in vue
			 */

			$settingGenerals = get_option( 'yaymail_settings' ) ? get_option( 'yaymail_settings' ) : $settingDefaultGenerals;
			foreach ( $settingDefaultGenerals as $keyDefaultGeneral => $settingGeneral ) {
				if ( ! array_key_exists( $keyDefaultGeneral, $settingGenerals ) ) {
					$settingGenerals[ $keyDefaultGeneral ] = $settingDefaultGenerals[ $keyDefaultGeneral ];
				};
			}

			$settingGenerals['direction_rtl'] = get_option( 'yaymail_direction' ) ? get_option( 'yaymail_direction' ) : 'ltr';
			$settings['general']              = $settingGenerals;

			$scriptId = $this->getPageId();
			$order    = CustomPostType::getListOrders();
			wp_enqueue_script( 'vue', YAYMAIL_PLUGIN_URL . ( YAYMAIL_DEBUG ? 'assets/libs/vue.js' : 'assets/libs/vue.min.js' ), '', YAYMAIL_VERSION, true );
			wp_enqueue_script( 'vuex', YAYMAIL_PLUGIN_URL . 'assets/libs/vuex.js', '', YAYMAIL_VERSION, true );

			do_action( 'yaymail_before_enqueue_dependence' );

			wp_enqueue_script( $scriptId, YAYMAIL_PLUGIN_URL . 'assets/dist/js/main.js', array( 'jquery' ), YAYMAIL_VERSION, true );
			wp_enqueue_style( $scriptId, YAYMAIL_PLUGIN_URL . 'assets/dist/css/main.css', array(), YAYMAIL_VERSION );

			// Css of ant
			// wp_enqueue_style($scriptId . 'antd-css', YAYMAIL_PLUGIN_URL . 'assets/admin/css/antd.min.css');
			// Css file app
			// wp_enqueue_style($scriptId, YAYMAIL_PLUGIN_URL . 'assets/dist/css/main.css');

			wp_enqueue_script( $scriptId . '-script', YAYMAIL_PLUGIN_URL . 'assets/admin/js/script.js', '', YAYMAIL_VERSION, true );
			$yaymailSettings = get_option( 'yaymail_settings' );

			// Load ACE Editor -Start
			if ( isset( $yaymailSettings['enable_css_custom'] ) && 'yes' == $yaymailSettings['enable_css_custom'] ) {
				wp_enqueue_script( $scriptId . 'ace-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/ace.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace1-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/ext-language_tools.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace2-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/mode-css.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace3-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/theme-merbivore_soft.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace4-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/worker-css.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace5-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/snippets/css.js ', '', YAYMAIL_VERSION, true );
			} else {
				wp_dequeue_script( $scriptId . 'ace-script' );
				wp_dequeue_script( $scriptId . 'ace1-script' );
				wp_dequeue_script( $scriptId . 'ace2-script' );
				wp_dequeue_script( $scriptId . 'ace3-script' );
				wp_dequeue_script( $scriptId . 'ace4-script' );
				wp_dequeue_script( $scriptId . 'ace5-script' );
			}
			// Load ACE Editor -End
			// Css for page admin of WordPress.
			wp_enqueue_style( $scriptId . '-css', YAYMAIL_PLUGIN_URL . 'assets/admin/css/css.css', array(), YAYMAIL_VERSION );
			$current_user       = wp_get_current_user();
			$default_email_test = false != get_user_meta( get_current_user_id(), 'yaymail_default_email_test', true ) ? get_user_meta( get_current_user_id(), 'yaymail_default_email_test', true ) : $current_user->user_email;
			$element            = new DefaultElement();

			$yaymailSettingsDefaultLogo   = get_option( 'yaymail_settings_default_logo_' . $active_language );
			$setDefaultLogo               = false != $yaymailSettingsDefaultLogo ? $yaymailSettingsDefaultLogo['set_default'] : '0';
			$yaymailSettingsDefaultFooter = get_option( 'yaymail_settings_default_footer_' . $active_language );
			$setDefaultFooter             = false != $yaymailSettingsDefaultFooter ? $yaymailSettingsDefaultFooter['set_default'] : '0';
			if ( isset( $_GET['template'] ) || ! empty( $_GET['template'] ) ) {
				$req_template['id'] = sanitize_text_field( $_GET['template'] );
			} else {
				$req_template['id'] = 'new_order';
			}
			foreach ( $this->emails as $value ) {
				if ( $value->id == $req_template['id'] ) {
					$req_template['title'] = $value->title;
				}
			}

			// List email supported

			$list_email_supported = array(
				'WC_Subscription'                     => array(
					'plugin_name'   => 'WooCommerce Subscriptions',
					'template_name' => array(
						'cancelled_subscription',
						'expired_subscription',
						'suspended_subscription',
						'customer_completed_renewal_order',
						'customer_completed_switch_order',
						'customer_on_hold_renewal_order',
						'customer_renewal_invoice',
						'new_renewal_order',
						'new_switch_order',
						'customer_processing_renewal_order',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-woo-subscriptions',
				),
				'yith_wishlist_constructor'           => array(
					'plugin_name'   => 'YITH WooCommerce Wishlist Premium',
					'template_name' => array(
						'estimate_mail',
						'yith_wcwl_back_in_stock',
						'yith_wcwl_on_sale_item',
						'yith_wcwl_promotion_mail',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-wishlist',
				),
				'SUMO_Subscription'                   => array(
					'plugin_name'   => 'SUMO Subscription',
					'template_name' => array(
						'subscription_new_order',
						'subscription_new_order_old_subscribers',
						'subscription_processing_order',
						'subscription_completed_order',
						'subscription_pause_order',
						'subscription_invoice_order_manual',
						'subscription_expiry_reminder',
						'subscription_automatic_charging_reminder',
						'subscription_renewed_order_automatic',
						'auto_to_manual_subscription_renewal',
						'subscription_overdue_order_automatic',
						'subscription_overdue_order_manual',
						'subscription_suspended_order_automatic',
						'subscription_suspended_order_manual',
						'subscription_preapproval_access_revoked',
						'subscription_turnoff_automatic_payments_success',
						'subscription_pending_authorization',
						'subscription_cancel_order',
						'subscription_cancel_request_submitted',
						'subscription_cancel_request_revoked',
						'subscription_expired_order',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-sumo-subscriptions',
				),

				'YITH_Subscription'                   => array(
					'plugin_name'   => 'YITH WooCommerce Subscription Premium',
					'template_name' => array(
						'ywsbs_subscription_admin_mail',
						'ywsbs_customer_subscription_cancelled',
						'ywsbs_customer_subscription_suspended',
						'ywsbs_customer_subscription_expired',
						'ywsbs_customer_subscription_before_expired',
						'ywsbs_customer_subscription_paused',
						'ywsbs_customer_subscription_resumed',
						'ywsbs_customer_subscription_request_payment',
						'ywsbs_customer_subscription_renew_reminder',
						'ywsbs_customer_subscription_payment_done',
						'ywsbs_customer_subscription_payment_failed',
						'ywsbs_customer_subscription_delivery_schedules',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-subscription',
				),
				'woo-b2b'                             => array(
					'plugin_name'   => 'WooCommerce B2B',
					'template_name' => array(
						'wcb2b_customer_onquote_order',
						'wcb2b_customer_quoted_order',
						'wcb2b_customer_status_notification',
						'wcb2b_new_quote',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-b2b-woocommerce',
				),
				'YITH_Vendors'                        => array(
					'plugin_name'   => 'YITH WooCommerce Multi Vendor Premium',
					'template_name' => array(
						'cancelled_order_to_vendor',
						'commissions_paid',
						'commissions_unpaid',
						'new_order_to_vendor',
						'new_vendor_registration',
						'product_set_in_pending_review',
						'vendor_commissions_bulk_action',
						'vendor_commissions_paid',
						'vendor_new_account',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-multivendor',
				),
				'Germanized_Pro'                      => array(
					'plugin_name'   => 'Germanized for WooCommerce',
					'template_name' => array(
						'sab_cancellation_invoice',
						'sab_document',
						'sab_document_admin',
						'sab_simple_invoice',
						'sab_packing_slip',
						'customer_paid_for_order',
						'customer_cancelled_order',
						'customer_order_confirmation',
						'customer_revocation',
						'customer_new_account_activation',
						'customer_shipment',
						'customer_return_shipment',
						'customer_return_shipment_delivered',
						'new_return_shipment_request',
						'customer_trusted_shops',
						'customer_sepa_direct_debit_mandate',
						'customer_guest_return_shipment_request',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-germanized',
				),
				'WC_Bookings'                         => array(
					'plugin_name'   => 'WooCommerce Bookings',
					'template_name' => array(
						'admin_booking_cancelled',
						'booking_cancelled',
						'booking_confirmed',
						'booking_notification',
						'booking_pending_confirmation',
						'booking_reminder',
						'new_booking',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-woo-bookings',
				),
				'WooCommerce_Waitlist'                => array(
					'plugin_name'   => 'WooCommerce Waitlist',
					'template_name' => array(
						'woocommerce_waitlist_joined_email',
						'woocommerce_waitlist_left_email',
						'woocommerce_waitlist_mailout',
						'woocommerce_waitlist_signup_email',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-woo-waitlist',
				),
				'WooCommerce_Quotes'                  => array(
					'plugin_name'   => 'Quotes for WooCommerce',
					'template_name' => array(
						'qwc_req_new_quote',
						'qwc_request_sent',
						'qwc_send_quote',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-quotes-woocommerce',
				),
				'YITH_Pre_Order'                      => array(
					'plugin_name'   => 'YITH Pre-Order for WooCommerce Premium ',
					'template_name' => array(
						'yith_ywpo_date_end',
						'yith_ywpo_sale_date_changed',
						'yith_ywpo_is_for_sale',
						'yith_ywpo_out_of_stock',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-pre-order',
				),
				'WooCommerce_Appointments'            => array(
					'plugin_name'   => 'WooCommerce Appointments',
					'template_name' => array(
						'admin_appointment_cancelled',
						'admin_new_appointment',
						'appointment_cancelled',
						'appointment_confirmed',
						'appointment_follow_up',
						'appointment_reminder',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-appointments-woocommerce',
				),
				'SG_Order_Approval'                   => array(
					'plugin_name'   => 'Sg Order Approval for Woocommerce',
					'template_name' => array(
						'wc_admin_order_new',
						'wc_customer_order_new',
						'wc_customer_order_approved',
						'wc_customer_order_rejected',
						'sgitsoa_wc_admin_order_new',
						'sgitsoa_wc_customer_order_new',
						'sgitsoa_wc_customer_order_approved',
						'sgitsoa_wc_customer_order_rejected',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-order-approval-woocommerce',
				),
				'YITH_Pre_Order'                      => array(
					'plugin_name'   => 'YITH Pre-Order for WooCommerce Premium ',
					'template_name' => array(
						'yith_ywpo_date_end',
						'yith_ywpo_sale_date_changed',
						'yith_ywpo_is_for_sale',
						'yith_ywpo_out_of_stock',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-pre-order',
				),
				'Follow_Up_Emails'                    => array(
					'plugin_name'   => 'Follow Up Emails for WooCommerce ',
					'template_name' => apply_filters( 'YaymailCreateListFollowUpNames', array() ),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-woo-follow-ups',
				),
				'Order_Delivery_Date'                 => array(
					'plugin_name'   => 'Order Delivery Date Pro for WooCommerce',
					'template_name' => array(
						'orddd_delivery_reminder',
						'orddd_delivery_reminder_customer',
						'orddd_update_date',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-order-delivery-date',
				),
				'WC_Email_Cancelled_Customer_Order'   => array(
					'plugin_name'   => 'Order Cancellation Email to Customer',
					'template_name' => array(
						'wc_customer_cancelled_order',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-order-cancellation-email-customer',
				),
				'WC_Smart_Coupons'                    => array(
					'plugin_name'   => 'WooCommerce Smart Coupons',
					'template_name' => array(
						'wc_sc_combined_email_coupon',
						'wc_sc_acknowledgement_email',
						'wc_sc_email_coupon',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-smart-coupons',
				),
				'Dokan'                               => array(
					'plugin_name'   => 'Dokan',
					'template_name' => array(
						'dokan_new_seller',
						'dokan_email_vendor_disable',
						'dokan_email_vendor_enable',
						'dokan_contact_seller',
						'new_product',
						'new_product_pending',
						'pending_product_published',
						'updated_product_pending',
						'dokan_vendor_new_order',
						'dokan_vendor_completed_order',
						'dokan_vendor_withdraw_request',
						'dokan_vendor_withdraw_cancelled',
						'dokan_vendor_withdraw_approved',
						'dokan_refund_request',
						'dokan_vendor_refund',
						'dokan_announcement',
						'dokan_staff_new_order',
						'Dokan_Email_Wholesale_Register',
						'dokan_email_shipping_status_tracking',
						'dokan_email_subscription_invoice',
						'updates_for_store_followers',
						'vendor_new_store_follower',
						'dokan_product_enquiry_email',
						'dokan_report_abuse_admin_email',
						'Dokan_Send_Coupon_Email',
						'Dokan_Rma_Send_Warranty_Request',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-dokan',
				),
				'Woocommerce_German_Market'           => array(
					'plugin_name'   => 'Woocommerce_German_Market',
					'template_name' => array(
						'wgm_confirm_order_email',
						'wgm_double_opt_in_customer_registration',
						'wgm_sepa',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-german-market',
				),
				'B2B_Wholesale_Suite'                 => array(
					'plugin_name'   => 'B2B & Wholesale Suite',
					'template_name' => array(
						'b2bwhs_new_customer_email',
						'b2bwhs_new_customer_requires_approval_email',
						'b2bwhs_new_message_email',
						'b2bwhs_new_quote_email',
						'b2bwhs_your_account_approved_email',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-b2b-wholesale-suite',
				),
				'WooCommerce_Deposits'                => array(
					'plugin_name'   => 'WooCommerce Deposits',
					'template_name' => array(
						'customer_deposit_partially_paid',
						'customer_partially_paid',
						'customer_second_payment_reminder',
						'full_payment',
						'partial_payment',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-woocommerce-deposits',
				),
				'YITH_Booking'                        => array(
					'plugin_name'   => 'YITH Booking and Appointment for WooCommerce Premium',
					'template_name' => array(
						'yith_wcbk_admin_new_booking',
						'yith_wcbk_booking_status',
						'yith_wcbk_customer_booking_note',
						'yith_wcbk_customer_cancelled_booking',
						'yith_wcbk_customer_completed_booking',
						'yith_wcbk_customer_confirmed_booking',
						'yith_wcbk_customer_new_booking',
						'yith_wcbk_customer_paid_booking',
						'yith_wcbk_customer_unconfirmed_booking',
						'yith_wcbk_booking_status_vendor',
						'yith_wcbk_vendor_new_booking',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-booking',
				),
				'WooCommerce_Points_Rewards'          => array(
					'plugin_name'   => 'Points and Rewards for WooCommerce',
					'template_name' => array(
						'mwb_wpr_email_notification',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-points-and-rewards',
				),
				'PW_WooCommerce_Gift_Cards'           => array(
					'plugin_name'   => 'PW WooCommerce Gift Cards',
					'template_name' => array(
						'pwgc_email',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-pw-woocommerce-gift-cards',
				),
				'YITH_WooCommerce_Gift_Cards_Premium' => array(
					'plugin_name'   => 'YITH WooCommerce Gift Cards Premium',
					'template_name' => array(
						'ywgc-email-delivered-gift-card',
						'ywgc-email-send-gift-card',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-woocommerce-gift-cards-premium',
				),
				'YITH_WooCommerce_Membership_Premium' => array(
					'plugin_name'   => 'YITH WooCommerce Membership Premium',
					'template_name' => array(
						'membership_cancelled',
						'membership_expired',
						'membership_expiring',
						'membership_welcome',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-yith-woocommerce-membership-premium',
				),
				'WooCommerce_Order_Delivery'          => array(
					'plugin_name'   => 'WooCommerce Order Delivery',
					'template_name' => array(
						'subscription_delivery_note',
						'order_delivery_note',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-themesquad-delivery-date',
				),
				'WooCommerce_Simple_Auction'          => array(
					'plugin_name'   => 'WooCommerce Simple Auction',
					'template_name' => array(
						'auction_buy_now',
						'auction_closing_soon',
						'auction_fail',
						'auction_finished',
						'auction_relist',
						'auction_relist_user',
						'auction_win',
						'bid_note',
						'customer_bid_note',
						'outbid_note',
						'remind_to_pay',
						'Reserve_fail',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/#yaymail-addon-simple-auctions',
				),
			);

				$list_plugin_for_pro = array();

			if ( class_exists( 'WC_Shipment_Tracking_Actions' ) || class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
				$list_plugin_for_pro[] = 'WC_Shipment_Tracking';
			}
			if ( class_exists( 'WC_Admin_Custom_Order_Fields' ) ) {
				$list_plugin_for_pro[] = 'WC_Admin_Custom_Order_Fields';
			}
			if ( class_exists( 'EventON' ) ) {
				$list_plugin_for_pro[] = 'EventON';
			}
			if ( function_exists( 'woocontracts_maile_ekle' ) ) {
				$list_plugin_for_pro[] = 'WC_Email_Sozlesmeler';
			}
			if ( class_exists( 'Woocommerce_German_Market' ) ) {
				$list_plugin_for_pro[] = 'Woocommerce_German_Market';
			}
			if ( class_exists( 'YITH_WooCommerce_Order_Tracking_Premium' ) ) {
				$list_plugin_for_pro[] = 'YITH_WooCommerce_Order_Tracking_Premium';
			}
			if ( class_exists( 'SitePress' ) ) {
				$list_plugin_for_pro[] = 'WPML';
			}
			if ( class_exists( 'Polylang' ) ) {
				$list_plugin_for_pro[] = 'Polylang';
			}

				wp_localize_script(
					$scriptId,
					'yaymail_data',
					array(
						'orders'               => $order,
						'imgUrl'               => YAYMAIL_PLUGIN_URL . 'assets/dist/images',
						'nonce'                => wp_create_nonce( 'email-nonce' ),
						'defaultDataElement'   => $element->defaultDataElement,
						'home_url'             => home_url(),
						'settings'             => $settings,
						'admin_url'            => get_admin_url(),
						'yaymail_plugin_url'   => YAYMAIL_PLUGIN_URL,
						'wc_emails'            => $this->emails,
						'default_email_test'   => $default_email_test,
						'template'             => $req_template,
						'set_default_logo'     => $setDefaultLogo,
						'set_default_footer'   => $setDefaultFooter,
						'list_plugin_for_pro'  => $list_plugin_for_pro,
						'plugins'              => apply_filters( 'yaymail_plugins', array() ),
						'list_email_supported' => $list_email_supported,
						'list_languages'       => $listLanguages,
						'active_language'      => $active_language,
					)
				);
		}
	}
	public function getPageId() {
		if ( null == $this->pageId ) {
			$this->pageId = YAYMAIL_PREFIX . '-settings';
		}

		return $this->pageId;
	}
}
