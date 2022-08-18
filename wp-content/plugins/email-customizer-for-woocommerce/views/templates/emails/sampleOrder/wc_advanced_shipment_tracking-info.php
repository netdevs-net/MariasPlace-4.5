<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use YayMail\Page\Source\CustomPostType;

$postID                         = CustomPostType::postIDByTemplate( $this->template );
$emailTextLinkColor             = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
$borderColor                    = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor                      = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
$fontFamily                     = isset( $atts['fontfamily'] ) && $atts['fontfamily'] ? 'font-family:' . html_entity_decode( $atts['fontfamily'], ENT_QUOTES, 'UTF-8' ) : 'font-family:inherit';
$settings                       = new wcast_initialise_customizer_settings();
$select_tracking_template       = $ast->get_option_value_from_array( 'tracking_info_settings', 'select_tracking_template', $settings->defaults['select_tracking_template'] );
$show_provider_th               = 1;
$colspan                        = 1;
$display_thumbnail              = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'display_shipment_provider_image', $settings->defaults['display_shipment_provider_image'] );
$display_shipping_provider_name = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'display_shipment_provider_name', $settings->defaults['display_shipment_provider_name'] );
$tracking_number_link           = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'tracking_number_link', '' );
if ( 1 == $display_shipping_provider_name && 1 == $display_thumbnail ) {
	$show_provider_th = 1;
	$colspan          = 2;
} elseif ( 1 != $display_shipping_provider_name && 1 == $display_thumbnail ) {
	$show_provider_th = 1;
	$colspan          = 1;
} elseif ( 1 == $display_shipping_provider_name && 1 != $display_thumbnail ) {
	$show_provider_th = 1;
	$colspan          = 1;
} elseif ( 1 != $display_shipping_provider_name && 1 != $display_thumbnail ) {
	$show_provider_th = 0;
	$colspan          = 1;
} else {
	$show_provider_th = 0;
}
$show_track_label               = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'show_track_label', $settings->defaults['show_track_label'] );
$hide_trackig_header            = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'hide_trackig_header', '' );
$remove_date_from_tracking_info = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'remove_date_from_tracking', $settings->defaults['remove_date_from_tracking'] );
$shipment_tracking_header       = $ast->get_option_value_from_array( 'tracking_info_settings', 'header_text_change', 'Tracking Information' );
$shipment_tracking_header_text  = $ast->get_option_value_from_array( 'tracking_info_settings', 'additional_header_text', '' );
$provider_header_text           = $ast->get_option_value_from_array( 'tracking_info_settings', 'provider_header_text', $settings->defaults['provider_header_text'] );
$tracking_number_header_text    = $ast->get_option_value_from_array( 'tracking_info_settings', 'tracking_number_header_text', $settings->defaults['tracking_number_header_text'] );
$shipped_date_header_text       = $ast->get_option_value_from_array( 'tracking_info_settings', 'shipped_date_header_text', $settings->defaults['shipped_date_header_text'] );
$track_header_text              = $ast->get_option_value_from_array( 'tracking_info_settings', 'track_header_text', $settings->defaults['track_header_text'] );
?>
	<table class="yaymail_builder_table_items_border yaymail_builder_table_tracking_item" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;<?php echo esc_attr( $borderColor ); ?>" width="100%">

		<thead>
			<tr style="<?php echo esc_attr( $textColor ); ?>">
				<th colspan="<?php echo esc_attr( $colspan ); ?>" class="td" scope="col" style="text-align: left;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>"><?php esc_html_e( $provider_header_text, 'yaymail' ); ?></th>
				<th class="td" scope="col" style="text-align: left;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>"><?php esc_html_e( $tracking_number_header_text, 'yaymail' ); ?></th>
				<th class="td" scope="col" style="text-align: left;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>"><?php esc_html_e( $shipped_date_header_text, 'yaymail' ); ?></th>
				<?php if ( ! $tracking_number_link ) { ?>
				<th class="td" scope="col" style="text-align: left;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>">
																			  <?php
																				if ( 1 == $show_track_label ) {
																					echo esc_html_e( $track_header_text, 'woo-advanced-shipment-tracking' ); }
																				?>
				</th>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
			<tr class="tracking order_item" style="<?php echo esc_attr( $textColor ); ?>">
				
			<?php if ( 1 == $display_thumbnail ) { ?>
					<td class="td" data-title="<?php esc_html_e( 'Provider', 'yaymail' ); ?>" style="width: 50px;text-align: left; padding: 12px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>">
						<img style="width: 50px;vertical-align: middle;max-width:unset;" src="<?php echo esc_url( YAYMAIL_PLUGIN_URL . 'assets/images/icon-default.png' ); ?>">
					</td>
					<?php } ?>

					<?php if ( 1 == $display_shipping_provider_name ) { ?>
					<td style="font-size:13px;text-align: left; padding: 12px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>" class="td tracking-provider" data-title="<?php echo esc_attr__( 'Provider Name', 'woo-advanced-shipment-tracking' ); ?>">
						<?php esc_html_e( 'yaymail', 'yaymail' ); ?>
					</td>
					<?php } ?>
					<td style="font-size:13px;text-align: left; padding: 12px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>" class="td tracking-number" data-title="<?php esc_attr__( 'Tracking Number', 'woo-advanced-shipment-tracking' ); ?>" >
						<?php if ( $tracking_number_link ) { ?>	
								<a class="tracking-number-link" style="<?php echo esc_attr( $textColor ); ?>;text-decoration:none;" href="#" style="text-decoration: none;" target="_blank"><?php esc_html_e( '123', 'yaymail' ); ?></a>
							<?php
						} else {
							echo esc_html_e( '123', 'yaymail' );
						}
						?>
												
					</td>
					<?php
					if ( 1 != $remove_date_from_tracking_info ) {
						;
						?>
					<td class="date-shipped" data-title="<?php echo esc_attr__( 'Date', 'woocommerce' ); ?>" style="text-align:left; white-space:nowrap;">
						<?php echo wp_kses_post( sprintf( '<time datetime="%s">%s</time>', new WC_DateTime(), wc_format_datetime( new WC_DateTime() ) ) ); ?>
					</td>
						<?php
					}
					?>
					<?php if ( ! $tracking_number_link ) { ?>
					<td style="font-size:13px;text-align: left; padding: 12px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>" class="order-actions">
						<a style="color: <?php echo esc_attr( $emailTextLinkColor ); ?>" href="#" target="_blank"><?php echo esc_html_e( 'Track', 'woo-advanced-shipment-tracking' ); ?></a>
					</td>
					<?php } ?>
			</tr>
		</tbody>
	</table>
	<?php

