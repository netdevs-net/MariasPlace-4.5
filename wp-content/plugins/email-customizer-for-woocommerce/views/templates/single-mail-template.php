<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YayMail\Page\Source\CustomPostType;
use YayMail\Page\Source\UpdateElement;
$flag_do_action = false;
if ( isset( $args['yith_wc_email'] ) && isset( $args['yith_wc_email']->id ) && ! empty( $args['yith_wc_email']->id ) ) {
	// Get Email ID in yith-woocommerce-multi-vendor-premium
	$template = $args['yith_wc_email']->id;
} else {
	$template = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;
	if ( 'dokan-wholesale/' == $template_path ) {
		$template = 'Dokan_Email_Wholesale_Register';
	}
	if ( class_exists( 'WC_Smart_Coupons' ) ) {
		if ( isset( $args['email'] ) && strpos( $cache_path, plugin_dir_path( WC_SC_PLUGIN_FILE ) ) !== false ) {
			$templateName = str_replace( plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/', '', $cache_path );
			if ( 'email.php' == $templateName ) {
				$template = 'wc_sc_email_coupon';
			}
			if ( 'combined-email.php' == $templateName ) {
				$template = 'wc_sc_combined_email_coupon';
			}
			if ( 'acknowledgement-email.php' == $templateName ) {
				$template = 'wc_sc_acknowledgement_email';
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
$custom_shortcode = new YayMail\MailBuilder\Shortcodes( $template );
$holder_order     = isset( $args['order'] ) ? $args['order'] : null;
if ( CustomPostType::postIDByTemplate( $template, $holder_order ) ) {
	$postID = CustomPostType::postIDByTemplate( $template, $holder_order );
}

switch ( $template ) {
	case 'qwc_req_new_quote':
	case 'qwc_request_sent':
	case 'qwc_send_quote':
		$args['order'] = new WC_Order( $args['order']->get_id() );
		break;
	default:
		break;
}
if ( isset( $args['email'] ) && 'wc_sc_email_coupon' != $template && 'wc_sc_combined_email_coupon' != $template && 'wc_sc_acknowledgement_email' != $template ) {
	$checkIsSumoTemp = strpos( get_class( $args['email'] ), 'SUMOSubscriptions' );
	$checkIsQWCTemp  = strpos( get_class( $args['email'] ), 'QWC' );
} else {
	$checkIsSumoTemp = false;
	$checkIsQWCTemp  = false;
}
if ( ( false === $checkIsSumoTemp ) && ( false === $checkIsQWCTemp ) && isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) && isset( $args['order'] ) && $args['order']->get_id() ) {
	$flag_do_action = true;
	$custom_shortcode->setOrderId( $args['order']->get_id(), $args['sent_to_admin'], $args );
	if ( isset( $args['sent_to_admin'] ) ) {
		if ( 1 === $args['order']->get_id() && false === $args['sent_to_admin'] ) {
			$custom_shortcode->shortCodesOrderSample();
		} else {
			$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args);
		}
	} else {
		$custom_shortcode->shortCodesOrderDefined();
	}
} elseif ( $template ) {
	$flag_do_action = true;
	if ( 'customer_new_account' === $args['email']->id || 'customer_new_account_activation' === $args['email']->id || 'customer_reset_password' === $args['email']->id ) {
		$custom_shortcode->setOrderId( 0, $args['sent_to_admin'], $args );
		$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	} else {
		$arrData = array( $custom_shortcode, $args, $template );
		do_action_ref_array( 'yaymail_addon_defined_shorcode', array( &$arrData ) );
	}
	// if ( 'cancelled_subscription' === $args['email']->id || 'expired_subscription' === $args['email']->id || 'suspended_subscription' === $args['email']->id ) {
	// $custom_shortcode->setOrderId( $args['subscription']->data['parent_id'], $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( 'ywsbs_subscription_admin_mail' === $args['email']->id || 'ywsbs_customer_subscription_cancelled' === $args['email']->id || 'ywsbs_customer_subscription_suspended' === $args['email']->id || 'ywsbs_customer_subscription_expired' === $args['email']->id || 'ywsbs_customer_subscription_before_expired' === $args['email']->id || 'ywsbs_customer_subscription_paused' === $args['email']->id || 'ywsbs_customer_subscription_resumed' === $args['email']->id || 'ywsbs_customer_subscription_request_payment' === $args['email']->id || 'ywsbs_customer_subscription_renew_reminder' === $args['email']->id || 'ywsbs_customer_subscription_payment_done' === $args['email']->id || 'ywsbs_customer_subscription_payment_failed' === $args['email']->id ) {
	// $custom_shortcode->setOrderId( $args['subscription']->order->id, $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateYITHCommissions ) ) {
	// if ( isset( $args['commissions'] ) && isset( $args['commissions']->order_id ) ) {
	// $custom_shortcode->setOrderId( $args['commissions']->order_id, $args['sent_to_admin'], $args );
	// }
	// if ( isset( $args['commission'] ) && isset( $args['commission']->order_id ) ) {
	// $custom_shortcode->setOrderId( $args['commission']->order_id, $args['sent_to_admin'], $args );
	// }
	// if ( isset( $args['order_number'] ) ) {
	// $custom_shortcode->setOrderId( $args['order_number'], $args['sent_to_admin'], $args );
	// }
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateGermanizedForWC ) ) {
	// $custom_shortcode->setOrderId( $args['document']->get_order()->get_order()->id, $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateYITHWishlist ) ) {
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args, 'not_order' );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateWooBookings ) ) {
	// $custom_shortcode->setOrderId( $args['booking']->data['order_id'], false, $args );
	// $custom_shortcode->shortCodesOrderDefined( false, $args );
	// $html = do_shortcode( $html );
	// }
}

if ( $flag_do_action ) {
	$updateElement        = new UpdateElement();
	$yaymail_elements     = get_post_meta( $postID, '_yaymail_elements', true );
	$yaymail_elements     = $updateElement->merge_new_props_to_elements( $yaymail_elements );
	$yaymail_settings     = get_option( 'yaymail_settings' );
	$emailBackgroundColor = get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC';
	$general_attrs        = array( 'tableWidth' => str_replace( 'px', '', $yaymail_settings['container_width'] ) );
	?>
	<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
				<style>
				h1{ font-family:inherit;text-shadow:unset;text-align:inherit;}
				h2,h3{ font-family:inherit;color:inherit;text-align:inherit;}
				</style>
			</head>
			<body style="background: <?php echo esc_attr( $emailBackgroundColor ); ?>">
				<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<?php
				foreach ( $yaymail_elements as $key => $element ) {
					?>
					 <tr><td> 
					 <?php
						$reg_pattern = '/\[([a-z0-9A-Z_]+)\]/';
						if ( isset( $element['settingRow']['content'] ) ) {
							$content      = $element['settingRow']['content'];
							$contentTitle = isset( $element['settingRow']['contentTitle'] ) ? $element['settingRow']['contentTitle'] : '';

							// Add $atts for content if has shortcode
							preg_match_all( $reg_pattern, $content, $result );
							if ( ! empty( $result[0] ) ) {
								foreach ( $result[0] as $key => $shortcode ) {
									$textcolor     = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
									$bordercolor   = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
									$titlecolor    = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
									$fontfamily    = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
									$newshortcode  = substr( $shortcode, 0, -1 );
									$newshortcode .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
									$content       = str_replace( $shortcode, $newshortcode, $content );
								}
								$element['settingRow']['content'] = $content;
							}
							// Add $atts for contentTitle if has shortcode
							if ( $contentTitle ) {
								preg_match_all( $reg_pattern, $contentTitle, $result );
								if ( ! empty( $result[0] ) ) {
									foreach ( $result[0] as $key => $shortcode ) {
										$textcolor     = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
										$bordercolor   = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
										$titlecolor    = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
										$fontfamily    = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
										$newshortcode  = substr( $shortcode, 0, -1 );
										$newshortcode .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
										$contentTitle  = str_replace( $shortcode, $newshortcode, $contentTitle );
									}
									$element['settingRow']['contentTitle'] = $contentTitle;
								}
							}

							// Add $atts for content of shipment tracking if has shortcode
							if ( '[yaymail_order_meta:_wc_shipment_tracking_items]' === $content ) {
								$shortcode                        = $content;
								$textcolor                        = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
								$bordercolor                      = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
								$titlecolor                       = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
								$fontfamily                       = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
								$newshortcode                     = substr( $shortcode, 0, -1 );
								$newshortcode                    .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
								$content                          = str_replace( $shortcode, $newshortcode, $content );
								$element['settingRow']['content'] = $content;
							}
						}
						do_action( 'Yaymail' . $element['type'], $args, $element['settingRow'], $general_attrs, $element['id'], $postID, $isInColumns = false );
						?>
					 </td></tr> 
					 <?php
				}
				?>
				</table>
			</body>
		</html>
<?php } ?>
