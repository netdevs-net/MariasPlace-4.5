<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( isset( $args['subscription'] ) ) {
	$text_link_color           = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
	$subscript_subscription    = isset( $attrs['titleSubscription'] ) ? $attrs['titleSubscription'] : 'Subscription';
	$subscript_price           = isset( $attrs['titlePrice'] ) ? $attrs['titlePrice'] : 'Price';
	$subscript_last_order_date = isset( $attrs['titleLastOrderDate'] ) ? $attrs['titleLastOrderDate'] : 'Last Order Date';
	$subscript_end_date        = isset( $attrs['titleEndDate'] ) ? $attrs['titleEndDate'] : 'End Date';
	$borderColor               = isset( $attrs['borderColor'] ) && $attrs['borderColor'] ? 'border-color:' . html_entity_decode( $attrs['borderColor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
	$textColor                 = isset( $attrs['textColor'] ) && $attrs['textColor'] ? 'color:' . html_entity_decode( $attrs['textColor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
}
?>

<table
  width="<?php esc_attr_e( $general_attrs['tableWidth'], 'woocommerce' ); ?>"
  cellspacing="0"
  cellpadding="0"
  border="0"
  align="center"
  style="display: table; <?php echo esc_attr( 'background-color: ' . $attrs['backgroundColor'] ); ?>;<?php echo esc_attr( 'min-width: ' . $general_attrs['tableWidth'] . 'px' ); ?>;"
  class="web-main-row"
  id="web<?php echo esc_attr( $id ); ?>"
  >
  <tbody>
	  <tr>
		<td
		  id="web-<?php echo esc_attr( $id ); ?>-order-item"
		  class="web-order-item"
		  align="left"
		  style='font-size: 13px; line-height: 22px; word-break: break-word;
		  <?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;
		  <?php echo esc_attr( 'padding: ' . $attrs['paddingTop'] . 'px ' . $attrs['paddingRight'] . 'px ' . $attrs['paddingBottom'] . 'px ' . $attrs['paddingLeft'] . 'px;' ); ?>
		  '
		>
		  <div
			style="min-height: 10px; <?php echo esc_attr( 'color: ' . $attrs['textColor'] ); ?>;"
		  >
			<h2 class="yaymail_builder_order" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;font-size: 18px; font-weight: 700; <?php echo esc_attr( 'color: ' . $attrs['titleColor'] ); ?>'>
				<?php echo esc_html_e( 'Subscription expired', 'woocommerce-subscriptions' ); ?>
			</h2>
			<!-- Table Subscription Expired -->
			<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;<?php echo esc_attr( $borderColor ); ?>;color: inherit;flex-direction:inherit;" width="100%">
				<thead>
					<tr style="word-break: normal;<?php echo esc_attr( $textColor ); ?>">
						<th class="td yaymail-title-subscription" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php esc_html_e( $subscript_subscription, 'woocommerce-subscriptions' ); ?></th>
						<th class="td yaymail-title-subscription-price" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_price, 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
						<th class="td yaymail-title-subscription-last-order-date" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_last_order_date, 'table heading', 'woocommerce-subscriptions' ); ?></th>
						<th class="td yaymail-title-subscription-enddate" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_end_date, 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
					</tr>
				</thead>

				<tbody style="flex-direction:inherit;">
					<tr class="order_item" style="flex-direction:inherit;<?php echo esc_attr( $textColor ); ?>">
						<td class="td"   style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
							<a class="yaymail-sup-infor" style="color:<?php echo esc_attr( $text_link_color ); ?>" href="<?php echo esc_url( wcs_get_edit_post_link( $subscription->get_id() ) ); ?>">#<?php echo esc_html( $subscription->get_order_number() ); ?></a>
						</td>
						<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
							<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
						</td>
						<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
							<?php
							$last_order_time_created = $subscription->get_time( 'last_order_date_created', 'site' );
							if ( ! empty( $last_order_time_created ) ) {
								echo esc_html( date_i18n( wc_date_format(), $last_order_time_created ) );
							} else {
								esc_html_e( '-', 'yaymail' );
							}
							?>
						</td>
						<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
							<?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- Table Subscription Expired -->
		  </div>
		</td>
	  </tr>
	</tbody>
</table>
