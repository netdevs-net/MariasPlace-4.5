<?php

namespace YayMailSubscription\templateDefault;

defined( 'ABSPATH' ) || exit;

class DefaultSubscription {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function getTemplates( $customOrder, $emailHeading ) {
		/*
		@@@ Html default send email.
		@@@ Note: Add characters '\' before special characters in a string.
		@@@ Example: font-family: \'Helvetica Neue\'...
		*/

		$orderSubscription = self::orderSubscription( $customOrder );
		$emailTitle        = __( $emailHeading, 'woocommerce' );
		$customText        = '';
		if ( 'expired_subscription' == $customOrder ) {
			$customText = esc_html__( ' has expired.', 'woocommerce' );
		}
		if ( 'suspended_subscription' == $customOrder ) {
			$customText = esc_html__( ' has been suspended by the user.', 'woocommerce' );
		}
		if ( 'cancelled_subscription' == $customOrder ) {
			$customText = esc_html__( ' has been cancelled.', 'woocommerce' );
		}
		$emailtext         = esc_html__( 'A subscription belonging to ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_billing_first_name] [yaymail_billing_last_name]' ) ) . $customText . esc_html__( ' Their subscription\'s details are as follows:', 'woocommerce' );
		$additionalContent = __( 'Thanks for reading.', 'woocommerce' );
		if ( 'new_switch_order' == $customOrder ) {
			$emailtext         = esc_html__( 'Customer ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_billing_first_name] [yaymail_billing_last_name]' ) ) . esc_html__( ' has switched 0 of their subscriptions. The details of their new subscriptions are as follows:', 'woocommerce' );
			$additionalContent = __( 'Congratulations on the sale.', 'woocommerce' );
		}
		if ( 'customer_completed_switch_order' == $customOrder ) {
			$emailtext         = esc_html__( 'Hi ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_billing_first_name]' ) . ', <br /><br/>' ) . esc_html__( 'You have successfully changed your subscription items. Your new order and subscription details are shown below for your reference:', 'woocommerce' );
			$additionalContent = __( 'Thanks for shopping with us.', 'woocommerce' );
		}
		$textShippingAddress = __( 'Shipping Address', 'woocommerce' );
		$textBillingAddress  = __( 'Billing Address', 'woocommerce' );

		/*
		@@@ Elements default when reset template.
		@@@ Note 1: Add characters '\' before special characters in a string.
		@@@ example 1: "family": "\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif",

		@@@ Note 2: Add characters '\' before special characters in a string.
		@@@ example 2: "<h1 style=\"font-family: \'Helvetica Neue\',...."
		*/

		// Elements
		$elements =
		'[{
			"id": "8ffa62b5-7258-42cc-ba53-7ae69638c1fe",
			"type": "Logo",
			"nameElement": "Logo",
			"settingRow": {
				"backgroundColor": "#ECECEC",
				"align": "center",
				"pathImg": "",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50",
				"width": "172",
				"url": "#"
			}
		}, {
			"id": "802bfe24-7af8-48af-ac5e-6560a81345b3",
			"type": "ElementText",
			"nameElement": "Email Heading",
			"settingRow": {
				"content": "<h1 style=\"font-size: 30px; font-weight: 300; line-height: normal; margin: 0; color: inherit;\">' . $emailTitle . '</h1>",
				"backgroundColor": "#96588A",
				"textColor": "#ffffff",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "36",
				"paddingRight": "48",
				"paddingBottom": "36",
				"paddingLeft": "48"
			}
		}, {
			"id": "b035d1f1-0cfe-41c5-b79c-0478f144ef5f",
			"type": "ElementText",
			"nameElement": "Text",
			"settingRow": {
				"content": "<p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailtext . '</span></p>",
				"backgroundColor": "#fff",
				"textColor": "#636363",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "47",
				"paddingRight": "50",
				"paddingBottom": "0",
				"paddingLeft": "50"
			}
		},
		' . $orderSubscription . '
		{
			"id": "ad422370-f762-4a26-92de-c4cf3878h0oi",
			"type": "OrderItem",
			"nameElement": "Order Item",
			"settingRow": {
				"contentBefore": "[yaymail_items_border_before]",
				"contentAfter": "[yaymail_items_border_after]",
				"contentTitle": "[yaymail_items_border_title]",
				"content": "[yaymail_items_border_content]",
				"backgroundColor": "#fff",
				"titleColor" : "#96588a",
				"textColor": "#636363",
				"borderColor": "#e5e5e5",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50"
			}
		},
		{
			"id": "de242956-a617-4213-9107-138842oi4tch",
			"type": "BillingAddress",
			"nameElement": "Billing Shipping Address",
			"settingRow": {
				"nameColumn": "BillingShippingAddress",
				"contentTitle": "[yaymail_billing_shipping_address_title]",
				"checkBillingShipping": "[yaymail_billing_shipping_address_title]",
				"titleBilling": "' . $textBillingAddress . '",
				"titleShipping": "' . $textShippingAddress . '",
				"content": "[yaymail_billing_shipping_address_content]",
				"titleColor" : "#96588a",
				"backgroundColor": "#fff",
				"borderColor": "#e5e5e5",
				"textColor": "#636363",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50"
			}
		},
		{
			"id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8d",
			"type": "ElementText",
			"nameElement": "Text",
			"settingRow": {
				"content": "<p><span style=\"font-size: 14px;\">' . $additionalContent . '</span></p>",
				"backgroundColor": "#fff",
				"textColor": "#636363",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "0",
				"paddingRight": "50",
				"paddingBottom": "38",
				"paddingLeft": "50"
			}
		},
		{
			"id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8ds",
			"type": "ElementText",
			"nameElement": "Footer",
			"settingRow": {
				"content": "<p style=\"font-size: 14px;margin: 0px 0px 16px; text-align: center;\">[yaymail_site_name]&nbsp;- Built with <a style=\"color: #96588a; font-weight: normal; text-decoration: underline;\" href=\"https://woocommerce.com\" target=\"_blank\" rel=\"noopener\">WooCommerce</a></p>",
				"backgroundColor": "#ececec",
				"textColor": "#8a8a8a",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50"
			}
		}]';

		// Templates Subscription
		$templates = array(
			$customOrder => array(),
		);

		$templates[ $customOrder ]['elements'] = $elements;
		return $templates;
	}

	public static function orderSubscription( $customOrder ) {

		$isDisplay    = false;
		$content      = '';
		$type         = '';
		$nameElement  = '';
		$keyEndDate   = '';
		$valueEndDate = '';
		if ( 'suspended_subscription' == $customOrder ) {
			$type         = 'AddonWooSubscriptionSuspended';
			$nameElement  = 'Woo Subscription Suspended';
			$keyEndDate   = 'titleDateSuspended';
			$valueEndDate = 'Date Suspended';
			$isDisplay    = true;
		}
		if ( 'expired_subscription' == $customOrder ) {
			$type         = 'AddonWooSubscriptionExpired';
			$nameElement  = 'Woo Subscription Expired';
			$keyEndDate   = 'titleEndDate';
			$valueEndDate = 'End Date';
			$isDisplay    = true;
		}
		if ( 'cancelled_subscription' == $customOrder ) {
			$type         = 'AddonWooSubscriptionCancelled';
			$nameElement  = 'Woo Subscription Cancelled';
			$keyEndDate   = 'titleEndOfPrepaidTerm';
			$valueEndDate = 'End of Prepaid Term';
			$isDisplay    = true;
		}
		$element = '{
			"id": "ad422370-f762-4a26-92de-c4cf38",
			"type": "' . $type . '",
			"nameElement": "' . $nameElement . '",
			"settingRow": {
				"content" :"This is content",
				"backgroundColor": "#fff",
				"titleColor" : "#96588a",
				"textColor": "#636363",
				"borderColor": "#e5e5e5",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50",
				"titleSubscription" : "Subscription",
				"titlePrice" : "Price",
				"titleLastOrderDate" : "Last Order Date",
				"' . $keyEndDate . '": "' . $valueEndDate . '"
			}
		},';

		if ( $isDisplay ) {
			return $element;
		}
		return '';
	}


}
