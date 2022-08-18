<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( isset( $args['order'] ) ) {
	if ( 'SampleOrder' === $args['order'] ) {
		$subscript_id_value              = 1;
		$subscript_id_href               = '#';
		$subscript_start_date_value      = gmdate( 'm-d-Y' );
		$subscript_end_date_value        = 'When cancelled';
		$subscript_recurring_total_value = '£2 / month';
		$subscriptions                   = array( false );
		$has_automatic_renewal           = false;
		$is_parent_order                 = false;
	} else {
		$is_parent_order = wcs_order_contains_subscription( $args['order'], 'parent' );
		$subscriptions   = wcs_get_subscriptions_for_order( $args['order']->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
	}
	$has_automatic_renewal     = false;
	$is_admin_email            = false;
	$text_link_color           = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
	$subscript_id              = isset( $attrs['titleID'] ) ? $attrs['titleID'] : 'ID';
	$subscript_start_date      = isset( $attrs['titleStartDate'] ) ? $attrs['titleStartDate'] : 'Start date';
	$subscript_end_date        = isset( $attrs['titleEndDate'] ) ? $attrs['titleEndDate'] : 'End date';
	$subscript_recurring_total = isset( $attrs['titleRecurringTotal'] ) ? $attrs['titleRecurringTotal'] : 'Recurring total';
	$borderColor               = isset( $attrs['borderColor'] ) && $attrs['borderColor'] ? 'border-color:' . html_entity_decode( $attrs['borderColor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
	$textColor                 = isset( $attrs['textColor'] ) && $attrs['textColor'] ? 'color:' . html_entity_decode( $attrs['textColor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
}
?>
<?php if ( ! empty( $subscriptions ) ) { ?>
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
				<?php echo esc_html_e( 'Subscription information', 'woocommerce-subscriptions' ); ?>
			</h2>
			<!-- Table Subscription information -->
			<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;<?php echo esc_attr( $borderColor ); ?>;color: inherit;flex-direction:inherit;" width="100%">
				<thead>
					<tr style="word-break: normal;<?php echo esc_attr( $textColor ); ?>">
						<th class="td yaymail-title-subscription-id" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_id, 'subscription ID table heading', 'woocommerce-subscriptions' ); ?></th>
						<th class="td yaymail-title-subscription-startdate" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_start_date, 'table heading', 'woocommerce-subscriptions' ); ?></th>
						<th class="td yaymail-title-subscription-enddate" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_end_date, 'table heading', 'woocommerce-subscriptions' ); ?></th>
						<th class="td yaymail-title-subscription-recurring-total" scope="col" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left;<?php echo esc_attr( $borderColor ); ?>;'><?php echo esc_html_x( $subscript_recurring_total, 'table heading', 'woocommerce-subscriptions' ); ?></th>
					</tr>
				</thead>

				<tbody style="flex-direction:inherit;">
					<?php foreach ( $subscriptions as $subscription ) : ?>
						<?php
						if ( 'SampleOrder' !== $args['order'] ) {
							$has_automatic_renewal = $has_automatic_renewal || ! $subscription->is_manual();
						}
						?>
						<tr class="order_item" style="flex-direction:inherit;<?php echo esc_attr( $textColor ); ?>">
							<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
								<?php /* translators: #%s: search term */ ?>
								<a class="yaymail-sup-infor" style="color:<?php echo esc_attr( $text_link_color ); ?>" href="<?php echo esc_url( 'SampleOrder' !== $args['order'] ? ( $is_admin_email ? wcs_get_edit_post_link( $subscription->get_id() ) : $subscription->get_view_order_url() ) : '' ); ?>"><?php echo sprintf( esc_html_x( '#%s', 'subscription number in email table. (eg: #106)', 'woocommerce-subscriptions' ), esc_html( 'SampleOrder' !== $args['order'] ? $subscription->get_order_number() : $subscript_id_value ) ); ?></a>
							</td>
							<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
								<?php echo esc_html( date_i18n( wc_date_format(), 'SampleOrder' === $args['order'] ? $subscript_start_date_value : $subscription->get_time( 'start_date', 'site' ) ) ); ?>
							</td>
							<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
								<?php echo esc_html( 'SampleOrder' !== $args['order'] ? ( 0 < $subscription->get_time( 'end' ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : _x( 'When cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-subscriptions' ) ) : $subscript_end_date_value ); ?>
							</td>
							<td class="td" style='<?php echo 'font-family: ' . wp_kses_post( $attrs['family'] ); ?>;text-align:left; vertical-align:middle;<?php echo esc_attr( $borderColor ); ?>;'>
							<?php echo wp_kses_post( 'SampleOrder' !== $args['order'] ? $subscription->get_formatted_order_total() : $subscript_recurring_total_value ); ?>
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
<?php } ?>
