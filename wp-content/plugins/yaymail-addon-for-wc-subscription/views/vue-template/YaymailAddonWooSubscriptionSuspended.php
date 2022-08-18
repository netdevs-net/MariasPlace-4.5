<?php
	$sent_to_admin             = ( isset( $sent_to_admin ) ? true : false );
	$plain_text                = ( isset( $plain_text ) ? $plain_text : '' );
	$email                     = ( isset( $email ) ? $email : '' );
	$text_link_color           = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588A';
	$order_item_title          = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
	$subscript_subscription    = false != $order_item_title ? $order_item_title['subscript_subscription'] : 'Subscription';
	$subscript_price           = false != $order_item_title ? $order_item_title['subscript_price'] : 'Price';
	$subscript_last_order_date = false != $order_item_title ? $order_item_title['subscript_last_order_date'] : 'Last Order Date';
	$subscript_date_suspended  = false != $order_item_title ? $order_item_title['subscript_date_suspended'] : 'Date Suspended';
if ( '' === $order ) {
	$subscript_id_value              = 1;
	$subscript_id_href               = '#';
	$subscript_price_value           = '£2 / month';
	$subscript_last_order_date_value = date( 'm-d-Y' );
	$subscript_date_suspended_value  = date( 'm-d-Y' );
} else {
}
?>
<?php if ( false !== $arrSubscription || '' === $order ) : ?>
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
				<?php echo esc_html_e( 'Subscription suspended', 'woocommerce-subscriptions' ); ?>
			</h2>
			<?php foreach ( $arrSubscription as $key => $subscription ) { ?>
			<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" 
				cellspacing="0" cellpadding="6" border="1" 
				style="width: 100% !important;color: inherit;flex-direction:inherit;" width="100%" :style="{'border-color': emailContent.settingRow.borderColor}">
				<thead>
					<tr style="word-break: normal;" :style="{color: emailContent.settingRow.textColor}">
					<th class="td yaymail-title-subscription" scope="col" style="text-align:left;" :style="{'border-color': emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleSubscription}}</th>
							<th class="td yaymail-title-subscription-price" scope="col" style="text-align:left;" :style="{'border-color': emailContent.settingRow.borderColor}">{{emailContent.settingRow.titlePrice}}</th>
							<th class="td yaymail-title-subscription-last-order-date" scope="col" style="text-align:left;" :style="{'border-color': emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleLastOrderDate}}</th>
							<th class="td yaymail-title-subscription-end-prepaid-term" scope="col" style="text-align:left;" :style="{'border-color': emailContent.settingRow.borderColor}">{{emailContent.settingRow.titleDateSuspended}}</th>
					</tr>
				</thead>
				<tbody style="flex-direction:inherit;">
					<tr class="order_item" style="flex-direction:inherit;" :style="{color: emailContent.settingRow.textColor}">
					<td class="td"   style="text-align:left; vertical-align:middle;" :style="{'border-color': emailContent.settingRow.borderColor}">
							<a :style="{color: emailTextLinkColor}" href="<?php echo esc_url( '' === $order ? $subscript_id_href : wcs_get_edit_post_link( $subscription->get_id() ) ); ?>">#<?php echo esc_html( '' === $order ? $subscript_id_value : $subscription->get_order_number() ); ?></a>
						</td>
						<td class="td" style="text-align:left; vertical-align:middle;" :style="{'border-color': emailContent.settingRow.borderColor}">
							<?php echo wp_kses_post( '' === $order ? $subscript_price_value : $subscription->get_formatted_order_total() ); ?>
						</td>
						<td class="td" style="text-align:left; vertical-align:middle;" :style="{'border-color': emailContent.settingRow.borderColor}">
							<?php
							$last_order_time_created = '' === $order ? $subscript_last_order_date_value : $subscription->get_time( 'last_order_date_created', 'site' );
							if ( ! empty( $last_order_time_created ) ) {
								echo esc_html( date_i18n( wc_date_format(), $last_order_time_created ) );
							} else {
								esc_html_e( '-', 'yaymail' );
							}
							?>
						</td>
						<td class="td" style="text-align:left; vertical-align:middle;" :style="{'border-color': emailContent.settingRow.borderColor}">
							<?php echo esc_html( date_i18n( wc_date_format(), time() ) ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php } ?>
		</div>
	</td>
	</tr>
</tbody>
</table>
<?php endif; ?>
