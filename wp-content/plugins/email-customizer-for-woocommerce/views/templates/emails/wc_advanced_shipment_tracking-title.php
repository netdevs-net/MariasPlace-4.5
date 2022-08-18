<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use YayMail\Page\Source\CustomPostType;

$postID                        = CustomPostType::postIDByTemplate( $this->template );
$emailTextLinkColor            = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
$titleColor                    = isset( $atts['titlecolor'] ) && $atts['titlecolor'] ? 'color:' . html_entity_decode( $atts['titlecolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
$fontFamily                    = isset( $atts['fontfamily'] ) && $atts['fontfamily'] ? 'font-family:' . html_entity_decode( $atts['fontfamily'], ENT_QUOTES, 'UTF-8' ) : 'font-family:inherit';
$settings                      = new wcast_initialise_customizer_settings();
$hide_trackig_header           = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'hide_trackig_header', '' );
$shipment_tracking_header      = $ast->get_option_value_from_array( 'tracking_info_settings', 'header_text_change', 'Tracking Information' );
$shipment_tracking_header_text = $ast->get_option_value_from_array( 'tracking_info_settings', 'additional_header_text', '' );
$provider_header_text          = $ast->get_option_value_from_array( 'tracking_info_settings', 'provider_header_text', $settings->defaults['provider_header_text'] );
$tracking_number_header_text   = $ast->get_option_value_from_array( 'tracking_info_settings', 'tracking_number_header_text', $settings->defaults['tracking_number_header_text'] );
$shipped_date_header_text      = $ast->get_option_value_from_array( 'tracking_info_settings', 'shipped_date_header_text', $settings->defaults['shipped_date_header_text'] );
$track_header_text             = $ast->get_option_value_from_array( 'tracking_info_settings', 'track_header_text', $settings->defaults['track_header_text'] );
?>
<h2 class="shipment_tracking_header_text" style="margin:13px 0;text-align:left;<?php echo esc_attr( $fontFamily ); ?>;<?php echo esc_attr( $titleColor ); ?>;
<?php
if ( $hide_trackig_header ) {
	echo 'display:none;'; }
?>
">
		<?php echo esc_html( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( $shipment_tracking_header, 'woo-advanced-shipment-tracking' ) ) ); ?>
</h2>
<?php if ( '' != $shipment_tracking_header_text ) { ?>
	<p class="addition_header"><?php echo esc_html( $shipment_tracking_header_text ); ?></p>
<?php } ?>
