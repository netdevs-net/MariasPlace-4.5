<?php
/**
 * Plugin Name: YayMail Premium Addon for Woocommerce Subscription
 * Plugin URI: https://yaycommerce.com/yaymail-woocommerce-email-customizer/
 * Description: YayMail Premium Addon for Woocommerce Subscription
 * Version: 1.4
 * Author: YayCommerce
 * Author URI: https://yaycommerce.com
 * Text Domain: yaymail
 * WC requires at least: 3.0.0
 * WC tested up to: 5.5.2
 * Domain Path: /i18n/languages/
 */

namespace YayMailSubscription;

defined( 'ABSPATH' ) || exit;
define( 'YAYMAIL_ADDON_VERSION', '1.4' );
spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__;
		$base_dir = __DIR__ . '/views';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class_name = substr( $class, $len );

		$file = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);


add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'YayMailSubscription\\yaymail_addon_add_action_links' );
function yaymail_addon_add_action_links( $actions ) {

	if ( defined( 'YAYMAIL_PREFIX' ) ) {
		$links   = array(
			'<a href="' . admin_url( 'admin.php?page=yaymail-settings' ) . '" aria-label="' . esc_attr__( 'View WooCommerce Email Builder', 'yaymail' ) . '">' . esc_html__( 'Start Customizing', 'yaymail' ) . '</a>',
		);
		$actions = array_merge( $links, $actions );
	}
	return $actions;
}

add_filter( 'plugin_row_meta', 'YayMailSubscription\\yaymail_addon_custom_plugin_row_meta', 10, 2 );
function yaymail_addon_custom_plugin_row_meta( $plugin_meta, $plugin_file ) {

	if ( strpos( $plugin_file, plugin_basename( __FILE__ ) ) !== false ) {
		$new_links = array(
			'docs'    => '<a href="https://yaycommerce.gitbook.io/yaymail/" aria-label="' . esc_attr__( 'View YayMail documentation', 'yaymail' ) . '">' . esc_html__( 'Docs', 'yaymail' ) . '</a>',
			'support' => '<a href="https://yaycommerce.com/support/" aria-label="' . esc_attr__( 'Visit community forums', 'yaymail' ) . '">' . esc_html__( 'Support', 'yaymail' ) . '</a>',
		);

		$plugin_meta = array_merge( $plugin_meta, $new_links );
	}

	return $plugin_meta;
}

add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'YayMailSubscription\\yaymail_addon_add_notification_after_plugin_row', 10, 2 );
function yaymail_addon_add_notification_after_plugin_row( $plugin_file, $plugin_data ) {

	if ( ! defined( 'YAYMAIL_PREFIX' ) ) {
		$wp_list_table = _get_list_table( 'WP_MS_Themes_List_Table' );
		?>
		<script>
		var plugin_row_element = document.querySelector('tr[data-plugin="<?php echo esc_js( plugin_basename( __FILE__ ) ); ?>"]');
		plugin_row_element.classList.add('update');
		</script>
		<?php
		echo '<tr class="plugin-update-tr' . ( is_plugin_active( $plugin_file ) ? ' active' : '' ) . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange" >';
		echo '<div class="notice inline notice-warning notice-alt"><p>';
		echo esc_html__( 'To use this addon, you need to install and activate YayMail plugin. Get ', 'yaymail' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/yaymail/' ) . '">' . esc_html__( 'YayMail Free', 'yaymail' ) . '</a> or <a href="' . esc_url( 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/' ) . '">' . esc_html__( 'YayMail Pro', 'yaymail' ) . '</a>.
					</p>
				</div>
			</td>
			</tr>';
	}

}

function yaymail_subscription_dependence() {
	if ( class_exists( 'WC_Subscription' ) ) {
		wp_enqueue_script( 'yaymail-subscription', plugin_dir_url( __FILE__ ) . 'assets/dist/js/app.js', array(), '1.2', true );
		wp_enqueue_style( 'yaymail-subscription', plugin_dir_url( __FILE__ ) . 'assets/dist/css/app.css', array(), '1.2' );
	}
}
add_action( 'yaymail_before_enqueue_dependence', 'YayMailSubscription\\yaymail_subscription_dependence' );
add_filter(
	'yaymail_plugins',
	function( $plugins ) {
		if ( class_exists( 'WC_Subscription' ) ) {
			$plugins[] = array(
				'plugin_name'      => 'WC_Subscription',
				'addon_components' => array( 'WooSubscriptionInformation', 'WooSubscriptionSuspended', 'WooSubscriptionExpired', 'WooSubscriptionCancelled' ),
				'template_name'    => array(
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
					'customer_payment_retry',
					'payment_retry',
				),
			);
		}
		return $plugins;
	},
	10,
	1
);

/*
ACtion to defined shortcode

$arrData[0] : $custom_shortcode
$arrData[1] : $args
$arrData[2] : $templateName
*/

add_action(
	'yaymail_addon_defined_shorcode',
	function( $arrData ) {
		if ( class_exists( 'WC_Subscription' ) ) {
			$templateWooSubscription = array(
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
				'customer_payment_retry',
				'payment_retry',
			);
			if ( in_array( $arrData[2], $templateWooSubscription ) ) {
				$arrData[0]->setOrderId( $arrData[1]['subscription']->get_data()['parent_id'], $arrData[1]['sent_to_admin'], $arrData[1] );
				$arrData[0]->shortCodesOrderDefined( $arrData[1]['sent_to_admin'], $arrData[1] );
			}
		}
	}
);

// Filter to defined template
add_filter(
	'yaymail_addon_defined_template',
	function( $result, $template ) {
		if ( class_exists( 'WC_Subscription' ) ) {
			$templateWooSubscription = array(
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
				'customer_payment_retry',
				'payment_retry',
			);
			if ( in_array( $template, $templateWooSubscription ) ) {
				return true;
			}
			return $result;
		}
		return $result;
	},
	10,
	2
);
// Filter to add template to Vuex
add_filter(
	'yaymail_addon_templates',
	function( $addon_templates, $order, $post_id ) {
		if ( class_exists( 'WC_Subscription' ) ) {
			$components = apply_filters( 'yaymail_plugins', array() );
			$position   = '';
			foreach ( $components as $key => $component ) {
				if ( $component['plugin_name'] === 'WC_Subscription' ) {
					$position = $key;
					break;
				}
			}
			foreach ( $components[ $position ]['addon_components'] as $key => $component ) {
				ob_start();
				do_action( 'YaymailAddon' . $component . 'Vue', $order, $post_id );
				$html = ob_get_contents();
				ob_end_clean();
				$addon_templates['wc_subscription'] =
				array_merge(
					isset( $addon_templates['wc_subscription'] ) ? $addon_templates['wc_subscription'] : array(),
					array( $component . 'Vue' => $html )
				);
			}
		}
		return $addon_templates;
	},
	10,
	3
);

// Add new shortcode to shortcodes list
add_filter(
	'yaymail_shortcodes',
	function( $shortcode_list ) {
		if ( class_exists( 'WC_Subscription' ) ) {
			$shortcode_list[] = 'yaymail_addon_subscription_retry_time';
		}
		return $shortcode_list;
	},
	10,
	1
);

// Create shortcode
add_filter(
	'yaymail_do_shortcode',
	function( $shortcode_list, $yaymail_informations, $args = array() ) {
		if ( class_exists( 'WC_Subscription' ) ) {
			$shortcode_list['[yaymail_addon_subscription_retry_time]'] = yaymailAddonSubscriptionRetryTime( $yaymail_informations, $args );
		}
		return $shortcode_list;
	},
	10,
	3
);
function yaymailAddonSubscriptionRetryTime( $yaymail_informations, $args = array() ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		if ( isset( $args['retry'] ) ) {
			$retry = wcs_get_human_time_diff( $args['retry']->get_time() );
			return $retry;
		}
		return '';
	}
}


// Create HTML with Vue syntax to display in Vue
add_action( 'YaymailAddonWooSubscriptionInformationVue', 'YayMailSubscription\\woo_subscription_information_vue', 100, 5 );
function woo_subscription_information_vue( $order, $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		if ( '' === $order ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonWooSubscriptionInformation.php';
			$html = ob_get_contents();
			ob_end_clean();
		} else {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonWooSubscriptionInformation.php';
			$html = ob_get_contents();
			ob_end_clean();
			if ( '' === $html ) {
				$html = '<div></div>';
			}
		}
		echo $html;
	}
}

add_action( 'YaymailAddonWooSubscriptionSuspendedVue', 'YayMailSubscription\\woo_subscription_suspended_vue', 100, 5 );
function woo_subscription_suspended_vue( $order, $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		if ( '' === $order ) {
			$arrSubscription = array( false );
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonWooSubscriptionSuspended.php';
			$html = ob_get_contents();
			ob_end_clean();
		} else {
			$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
			$html            = '';
			if ( $is_parent_order ) {
				$arrSubscription = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
				if ( $arrSubscription ) {
					ob_start();
					include plugin_dir_path( __FILE__ ) . 'views/vue-template/YaymailAddonWooSubscriptionSuspended.php';
					$html = ob_get_contents();
					ob_end_clean();
				}
			} else {
				$arrSubscription = wcs_get_subscription( $order->get_id() );
				if ( false !== $arrSubscription ) {
					ob_start();
					include plugin_dir_path( __FILE__ ) . 'views/vue-template/YaymailAddonWooSubscriptionSuspended.php';
					$html = ob_get_contents();
					ob_end_clean();
				};
			}
			if ( '' === $html ) {
				$html = '<div></div>';
			}
		}
		echo $html;
	}
}

add_action( 'YaymailAddonWooSubscriptionExpiredVue', 'YayMailSubscription\\woo_subscription_expired_vue', 100, 5 );
function woo_subscription_expired_vue( $order, $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		if ( '' === $order ) {
			$arrSubscription = array( false );
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonWooSubscriptionExpired.php';
			$html = ob_get_contents();
			ob_end_clean();
		} else {
			$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
			$html            = '';
			if ( $is_parent_order ) {
				$arrSubscription = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
				if ( $arrSubscription ) {
					ob_start();
					include plugin_dir_path( __FILE__ ) . 'views/vue-template/YaymailAddonWooSubscriptionExpired.php';
					$html = ob_get_contents();
					ob_end_clean();
				}
			} else {
				$arrSubscription = wcs_get_subscription( $order->get_id() );
				ob_start();
				include plugin_dir_path( __FILE__ ) . 'views/vue-template/YaymailAddonWooSubscriptionExpired.php';
				$html = ob_get_contents();
				ob_end_clean();
			}
			if ( '' === $html ) {
				$html = '<div></div>';
			}
		}
		echo $html;
	}
}

add_action( 'YaymailAddonWooSubscriptionCancelledVue', 'YayMailSubscription\\woo_subscription_cancelled_vue', 100, 5 );
function woo_subscription_cancelled_vue( $order, $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		if ( '' === $order ) {
			$arrSubscription = array( false );
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonWooSubscriptionCancelled.php';
			$html = ob_get_contents();
			ob_end_clean();
		} else {
			$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
			$html            = '';
			if ( $is_parent_order ) {
				$arrSubscription = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
				if ( $arrSubscription ) {
					ob_start();
					include plugin_dir_path( __FILE__ ) . 'views/vue-template/YaymailAddonWooSubscriptionCancelled.php';
					$html = ob_get_contents();
					ob_end_clean();
				}
			} else {
				$arrSubscription = wcs_get_subscription( $order->get_id() );
				ob_start();
				include plugin_dir_path( __FILE__ ) . 'views/vue-template/YaymailAddonWooSubscriptionCancelled.php';
				$html = ob_get_contents();
				ob_end_clean();
			}
			if ( '' === $html ) {
				$html = '<div></div>';
			}
		}
		echo $html;
	}
}

// Create HTML to display when send mail
add_action( 'YaymailAddonWooSubscriptionInformation', 'YayMailSubscription\\woo_subscription_information', 100, 5 );
function woo_subscription_information( $args = array(), $attrs = array(), $general_attrs = array(), $id = '', $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		if ( isset( $args['order'] ) ) {
			ob_start();
			$order = $args['order'];
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonOrderSubscription.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} else {
			echo wp_kses_post( '' );
		}
	}
}

add_action( 'YaymailAddonWooSubscriptionSuspended', 'YayMailSubscription\\yaymail_addon_woo_subscription_suspended', 100, 5 );
function yaymail_addon_woo_subscription_suspended( $args = array(), $attrs = array(), $general_attrs = array(), $id = '', $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		$subscription = $args['subscription'];
		if ( isset( $subscription ) ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonSubscriptionSuspended.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} elseif ( isset( $args['order'] ) && 'SampleOrder' === $args['order'] ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonSubscriptionSuspendedDefault.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} else {
			echo wp_kses_post( '' );
		}
	}
}

add_action( 'YaymailAddonWooSubscriptionCancelled', 'YayMailSubscription\\yaymail_addon_woo_subscription_cancelled', 100, 5 );
function yaymail_addon_woo_subscription_cancelled( $args = array(), $attrs = array(), $general_attrs = array(), $id = '', $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		$subscription = $args['subscription'];
		if ( isset( $subscription ) ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonSubscriptionCancelled.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} elseif ( isset( $args['order'] ) && 'SampleOrder' === $args['order'] ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonSubscriptionCancelledDefault.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} else {
			echo wp_kses_post( '' );
		}
	}
}

add_action( 'YaymailAddonWooSubscriptionExpired', 'YayMailSubscription\\yaymail_addon_woo_subscription_expired', 100, 5 );
function yaymail_addon_woo_subscription_expired( $args = array(), $attrs = array(), $general_attrs = array(), $id = '', $postID = '' ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		$subscription = $args['subscription'];
		if ( isset( $subscription ) ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonSubscriptionExpired.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} elseif ( isset( $args['order'] ) && 'SampleOrder' === $args['order'] ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonSubscriptionExpiredDefault.php';
			$html = ob_get_contents();
			ob_end_clean();
			$html = do_shortcode( $html );
			echo wp_kses_post( $html );
		} else {
			echo wp_kses_post( '' );
		}
	}
}

// Action create template default
add_filter( 'YaymailNewTempalteDefault', 'YayMailSubscription\\yaymail_new_template_default', 100, 3 );
function yaymail_new_template_default( $array, $key, $value ) {
	if ( class_exists( 'WC_Subscription' ) ) {
		$getHeading = $value->heading;
		if ( 'WCS_Email_New_Switch_Order' == $key
		|| 'WCS_Email_Completed_Switch_Order' == $key
		|| 'WCS_Email_Cancelled_Subscription' == $key
		|| 'WCS_Email_Expired_Subscription' == $key
		|| 'WCS_Email_On_Hold_Subscription' == $key
		) {
			$defaultSubscription                        = templateDefault\DefaultSubscription::getTemplates( $value->id, $getHeading );
			$defaultSubscription[ $value->id ]['title'] = __( $value->title, 'woocommerce' );
			return $defaultSubscription;
		} elseif ( 'WCS_Email_Completed_Renewal_Order' == $key
		|| 'WCS_Email_Customer_On_Hold_Renewal_Order' == $key
		|| 'WCS_Email_Customer_Renewal_Invoice' == $key
		|| 'WCS_Email_New_Renewal_Order' == $key
		|| 'WCS_Email_Processing_Renewal_Order' == $key
		|| 'WCS_Email_Customer_Payment_Retry' == $key
		|| 'WCS_Email_Payment_Retry' == $key
		) {
			$simpleSubscription                        = templateDefault\SimpleSubscription::getTemplates( $value->id, $getHeading );
			$simpleSubscription[ $value->id ]['title'] = __( $value->title, 'woocommerce' );
			return $simpleSubscription;
		}
		return $array;
	}
}



