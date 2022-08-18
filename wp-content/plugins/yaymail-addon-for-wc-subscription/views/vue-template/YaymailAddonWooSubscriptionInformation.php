<?php

	$has_automatic_renewal = false;
	$is_admin_email        = false;
	$text_link_color       = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
	$order_item_title      = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
if ( '' === $order ) {
	$subscript_id_value              = 1;
	$subscript_id_href               = '#';
	$subscript_start_date_value      = gmdate( 'm-d-Y' );
	$subscript_end_date_value        = 'When cancelled';
	$subscript_recurring_total_value = '£2 / month';
	$subscriptions                   = array( false );
	$has_automatic_renewal           = false;
	$is_parent_order                 = false;
} else {
	$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
	$subscriptions   = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
}
?>
<?php if ( count( $subscriptions ) || '' === $order ) : ?>
<table
	:width="tableWidth"
	cellspacing="0"
	cellpadding="0"
	border="0"
	align="center"
	style="display: table;width: 100%;"
	:style="{
	  backgroundColor: emailContent.settingRow.backgroundColor,
	  width: tableWidth
	}"
	class="web-main-row"
	:id="'web' + emailContent.id"
  >
	  <tbody>
	  <tr>
		<td
		  :id="'web-' + emailContent.id + '-order-item'"
		  class="web-order-item"
		  align="left"
		  style="font-size: 13px; line-height: 22px; word-break: break-word;"
		  :style="{
			fontFamily: emailContent.settingRow.family,
			paddingTop: emailContent.settingRow.paddingTop + 'px',
			paddingBottom: emailContent.settingRow.paddingBottom + 'px',
			paddingRight: emailContent.settingRow.paddingRight + 'px',
			paddingLeft: emailContent.settingRow.paddingLeft + 'px'
		  }"
		>
		  <div
		  class="yaymail-items-order-border"
			style="min-height: 10px"
			:style="{
			  color: emailContent.settingRow.textColor,
			  borderColor: emailContent.settingRow.borderColor,
			}"
		  >
			  <h2 class="yaymail_builder_order" style="font-size: 18px; font-weight: 700;" :style="{color: emailContent.settingRow.titleColor}">
				<?php echo esc_html_e( 'Subscription information', 'woocommerce-subscriptions' ); ?>
			</h2>
			<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" 
				  cellspacing="0" cellpadding="6" 
				  border="1" 
				  style="width: 100% !important;color: inherit;flex-direction:inherit;" 
				  width="100%" 
				  :style="{'border-color' : emailContent.settingRow.borderColor}">
			  <thead>
				<tr style="word-break: normal;" :style="{color: emailContent.settingRow.textColor}">
				  <th class="td yaymail-title-subscription-id" scope="col" style="text-align:left;" :style="{'border-color' : emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleID}}</th>
				  <th class="td yaymail-title-subscription-startdate" scope="col" style="text-align:left;" :style="{'border-color' : emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleStartDate}}</th>
				  <th class="td yaymail-title-subscription-enddate" scope="col" style="text-align:left;" :style="{'border-color' : emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleEndDate}}</th>
				  <th class="td yaymail-title-subscription-recurring-total" scope="col" style="text-align:left;" :style="{'border-color' : emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleRecurringTotal}}</th>
				</tr>
			  </thead>

			  <tbody style="flex-direction:inherit;">
				<?php foreach ( $subscriptions as $subscription ) : ?>
					<?php
					if ( '' !== $order ) {
						$has_automatic_renewal = $has_automatic_renewal || ! $subscription->is_manual();}
					?>
				  <tr class="order_item" style="flex-direction:inherit;" :style="{'color' : emailContent.settingRow.textColor}">
					<td class="td"   style="text-align:left; vertical-align:middle;" :style="{'border-color' : emailContent.settingRow.borderColor}">
					  <a :style="{color: emailTextLinkColor}" href="<?php echo esc_url( '' !== $order ? ( $is_admin_email ? wcs_get_edit_post_link( $subscription->get_id() ) : $subscription->get_view_order_url() ) : '' ); ?>"><?php echo sprintf( esc_html_x( '#%s', 'subscription number in email table. (eg: #106)', 'woocommerce-subscriptions' ), esc_html( '' !== $order ? $subscription->get_order_number() : $subscript_id_value ) ); ?></a>
					</td>
					<td class="td" style="text-align:left; vertical-align:middle;" :style="{'border-color' : emailContent.settingRow.borderColor}">
					<?php echo esc_html( date_i18n( wc_date_format(), '' === $order ? $subscript_start_date_value : $subscription->get_time( 'start_date', 'site' ) ) ); ?>
					</td>
					<td class="td" style="text-align:left; vertical-align:middle;" :style="{'border-color' : emailContent.settingRow.borderColor}">
					<?php echo esc_html( '' !== $order ? ( 0 < $subscription->get_time( 'end' ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : _x( 'When cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-subscriptions' ) ) : $subscript_end_date_value ); ?>
					</td>
					<td class="td" style="text-align:left; vertical-align:middle;" :style="{'border-color' : emailContent.settingRow.borderColor}">
					<?php echo wp_kses_post( '' !== $order ? $subscription->get_formatted_order_total() : $subscript_recurring_total_value ); ?>
					<?php if ( $is_parent_order && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
						<br>
						<small><?php printf( esc_html__( 'Next payment: ', 'woocommerce-subscriptions' ) . esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'next_payment', 'site' ) ) ) ); ?></small>
				  <?php endif; ?>
					</td>
				  </tr>
				<?php endforeach; ?>
			  </tbody>
			</table>
			<?php
			if ( $has_automatic_renewal && ! $is_admin_email && $subscription->get_time( 'next_payment' ) > 0 ) {
				if ( count( $subscriptions ) === 1 ) {
					$subscription   = reset( $subscriptions );
					$my_account_url = $subscription->get_view_order_url();
				} else {
					$my_account_url = wc_get_endpoint_url( 'subscriptions', '', wc_get_page_permalink( 'myaccount' ) );
				}

				printf(
					'<small>%s</small>',
					wp_kses_post(
						sprintf(
							/*
							translators: %1$s: search term */
							/* translators: %2$s: search term */
							_n(
							// Translators: Placeholders are opening and closing My Account link tags.
								'This subscription is set to renew automatically using your payment method on file. You can manage or cancel this subscription from your %1$smy account page%2$s.',
								'These subscriptions are set to renew automatically using your payment method on file. You can manage or cancel your subscriptions from your %1$smy account page%2$s.',
								count( $subscriptions ),
								'woocommerce-subscriptions'
							),
							'<a href="' . $my_account_url . '">',
							'</a>'
						)
					)
				);
			}
			?>
		  </div>
		</td>
	  </tr>
	</tbody>
  </table>
<?php endif; ?>
