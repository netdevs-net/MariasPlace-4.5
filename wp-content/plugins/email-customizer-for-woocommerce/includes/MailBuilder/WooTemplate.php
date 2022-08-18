<?php

namespace YayMail\MailBuilder;

use YayMail\Page\Source\CustomPostType;
use YayMail\Ajax;

defined( 'ABSPATH' ) || exit;
/**
 * Settings Page
 */
class WooTemplate {


	protected static $instance = null;
	private $templateAccount;
	private $templateSubscription;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function doHooks() {
		$this->templateAccount         = array( 'customer_new_account', 'customer_new_account_activation', 'customer_reset_password' );
		$this->templateGermanizedForWC = array( 'sab_simple_invoice', 'sab_cancellation_invoice' );
		add_filter( 'storeabill_get_template', array( $this, 'storeabill_get_template' ), 100, 5 );
		add_filter( 'wc_get_template', array( $this, 'getTemplateMail' ), 100, 5 );
		add_filter( 'fue_before_sending_email', array( $this, 'getFollowUpTemplates' ), 100, 3 );
		$postID_notifier_instock_mail = CustomPostType::postIDByTemplate( 'notifier_instock_mail' );
		if ( get_post_meta( $postID_notifier_instock_mail, '_yaymail_status', true ) ) {
			add_filter( 'cwginstock_message', array( $this, 'cwginstock_message' ), 100, 2 );
			add_action( 'admin_action_cwginstock-sendmail', array( $this, 'action_remove_header_footer' ), 9 );
			add_action( 'cwginstock_notify_process', array( $this, 'action_remove_header_footer' ), 9 );
			add_action( 'cwginstocknotifier_handle_action_send_mail', array( $this, 'action_remove_header_footer' ), 9 );
		}

		$postID_notifier_subscribe_mail = CustomPostType::postIDByTemplate( 'notifier_subscribe_mail' );
		if ( get_post_meta( $postID_notifier_subscribe_mail, '_yaymail_status', true ) ) {
			add_filter( 'cwgsubscribe_message', array( $this, 'cwgsubscribe_message' ), 100, 2 );
			add_action( 'cwginstock_after_insert_subscriber', array( $this, 'action_remove_header_footer' ), 9 );
		}
		// change german market template dir
		$this->yaymail_get_german_market_templates();
	}

	public function yaymail_get_german_market_templates() {
		if ( class_exists( 'WGM_Email_Confirm_Order' ) || class_exists( 'WGM_Email_Double_Opt_In_Customer_Registration' ) ) {
			add_filter(
				'wgm_locate_template',
				function( $template, $template_name, $template_path ) {
					if ( 'emails/customer-confirm-order.php' === $template_name ) {
						$postID          = CustomPostType::postIDByTemplate( 'wgm_confirm_order_email' );
						$template_status = get_post_meta( $postID, '_yaymail_status', true );
						if ( $template_status ) {
							return YAYMAIL_PLUGIN_PATH . 'views/templates/emails/customer-confirm-order.php';
						}
					} elseif ( 'double-opt-in-customer-registration.php' === $template_name ) {
						$postID          = CustomPostType::postIDByTemplate( 'wgm_double_opt_in_customer_registration' );
						$template_status = get_post_meta( $postID, '_yaymail_status', true );
						if ( $template_status ) {
							return YAYMAIL_PLUGIN_PATH . 'views/templates/emails/double-opt-in-customer-registration.php';
						}
					} elseif ( 'emails/sepa-mandate.php' === $template_name ) {
						$postID          = CustomPostType::postIDByTemplate( 'wgm_sepa' );
						$template_status = get_post_meta( $postID, '_yaymail_status', true );
						if ( $template_status ) {
							return YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sepa.php';
						}
					}
					return $template;
				},
				100,
				3
			);
		}
	}

	public function cwgsubscribe_message( $message, $subscriber_id ) {
		$custom_shortcode = new Shortcodes( 'notifier_subscribe_mail' );
		$custom_shortcode->setOrderId( 0, true );
		$custom_shortcode->shortCodesOrderDefined( false, array( 'subscriber_id' => $subscriber_id ) );
		$Ajax   = Ajax::getInstance();
		$postID = CustomPostType::postIDByTemplate( 'notifier_subscribe_mail' );
		$html   = $Ajax->getHtmlByElements( $postID, array( 'order' => 'SampleOrder' ) );
		// Replace shortcode cannot do_shortcode
		$reg  = '/\[yaymail.*?\]/m';
		$html = preg_replace( $reg, '', $html );
		$html = html_entity_decode( $html, ENT_QUOTES, 'UTF-8' );
		return $html;
	}

	public function cwginstock_message( $message, $subscriber_id ) {
		$custom_shortcode = new Shortcodes( 'notifier_instock_mail' );
		$custom_shortcode->setOrderId( 0, true );
		$custom_shortcode->shortCodesOrderDefined( false, array( 'subscriber_id' => $subscriber_id ) );
		$Ajax   = Ajax::getInstance();
		$postID = CustomPostType::postIDByTemplate( 'notifier_instock_mail' );
		$html   = $Ajax->getHtmlByElements( $postID, array( 'order' => 'SampleOrder' ) );
		// Replace shortcode cannot do_shortcode
		$reg  = '/\[yaymail.*?\]/m';
		$html = preg_replace( $reg, '', $html );
		$html = html_entity_decode( $html, ENT_QUOTES, 'UTF-8' );
		return $html;
	}
	public function action_remove_header_footer() {
		$emails = \WC_Emails::instance();
		remove_action( 'woocommerce_email_header', array( $emails, 'email_header' ) );
		remove_action( 'woocommerce_email_footer', array( $emails, 'email_footer' ) );
	}

	public function storeabill_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		$this_template  = false;
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
		$template       = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;

		if ( $template ) {
			if ( CustomPostType::postIDByTemplate( $template ) ) {
				$postID = CustomPostType::postIDByTemplate( $template );
				if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_elements', true ) ) ) {
					if ( in_array( $template, $this->templateGermanizedForWC ) ) { // template mail with account
						$this_template = $templateActive;
					}
				}
			}
		}
		$this_template = $this_template ? $this_template : $located;
		return $this_template;
	}

	private function __construct() {}
	// define the woocommerce_new_order callback
	public function getTemplateMail( $located, $template_name, $args, $template_path, $default_path ) {
		$this_template  = false;
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
		if ( isset( $args['yith_wc_email'] ) && isset( $args['yith_wc_email']->id ) && ! empty( $args['yith_wc_email']->id ) ) {
			// Get Email ID in yith-woocommerce-multi-vendor-premium
			$template = $args['yith_wc_email']->id;
		} else {
			$template = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;
			if ( 'dokan-wholesale/' == $template_path ) {
				$template = 'Dokan_Email_Wholesale_Register';
			}
			if ( class_exists( 'WC_Smart_Coupons' ) ) {
				if ( isset( $args['email'] ) && strpos( $located, plugin_dir_path( WC_SC_PLUGIN_FILE ) ) !== false ) {
					$templateName = str_replace( plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/', '', $located );
					if ( 'email.php' == $templateName ) {
						$template   = 'wc_sc_email_coupon';
						$args['id'] = 'wc_sc_email_coupon';
					}
					if ( 'combined-email.php' == $templateName ) {
						$template   = 'wc_sc_combined_email_coupon';
						$args['id'] = 'wc_sc_combined_email_coupon';
					}
					if ( 'acknowledgement-email.php' == $templateName ) {
						$template   = 'wc_sc_acknowledgement_email';
						$args['id'] = 'wc_sc_acknowledgement_email';
					}
				}
			}
			if ( 'emails/waitlist-mailout.php' == $template_name ) {
				$template = 'woocommerce_waitlist_mailout';
			}
			if ( 'emails/waitlist-left.php' == $template_name ) {
				$template = 'woocommerce_waitlist_left_email';
			}
			if ( 'emails/waitlist-joined.php' == $template_name ) {
				$template = 'woocommerce_waitlist_joined_email';
			}
			if ( 'emails/waitlist-new-signup.php' == $template_name ) {
				$template = 'woocommerce_waitlist_signup_email';
			}
		}

		if ( isset( $args['email'] ) && isset( $args['email']->id ) && false !== strpos( get_class( $args['email'] ), 'ORDDD_Email_Delivery_Reminder' ) ) {
			$template .= '_customer';
		}

		if ( $template ) {
			$holder_order = isset( $args['order'] ) ? $args['order'] : null;
			if ( CustomPostType::postIDByTemplate( $template, $holder_order ) ) {
				$postID = CustomPostType::postIDByTemplate( $template, $holder_order );
				if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_elements', true ) ) ) {
					if ( isset( $args['order'] ) || in_array( $template, $this->templateAccount ) ) { // template mail with order
						$this_template = $templateActive;
					} else {
						$checkHasTempalte = apply_filters( 'yaymail_addon_defined_template', false, $template );
						if ( $checkHasTempalte ) { // template mail with account
							$this_template = $templateActive;
						}
					}
				}
			}
		}
		$this_template = $this_template ? $this_template : $located;
		return $this_template;
	}

	public function getFollowUpTemplates( $email_data, $email, $queue_item ) {
		if ( has_filter( 'yaymail_follow_up_shortcode' ) ) {
			$templateActive  = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;
			$template        = 'follow_up_email_' . $email->id;
			$postID          = CustomPostType::postIDByTemplate( $template );
			$template_status = get_post_meta( $postID, '_yaymail_status', true );
			$args            = array(
				'email_data' => $email_data,
				'email'      => $email,
				'queue_item' => $queue_item,
			);
			if ( $template_status ) {
				ob_start();
				include $templateActive;
				$template_body = ob_get_contents();
				ob_end_clean();
				$email_data['message'] = $template_body;
			}
		}
		return $email_data;
	}
}
