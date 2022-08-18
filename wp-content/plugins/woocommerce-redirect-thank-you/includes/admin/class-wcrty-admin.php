<?php
/**
 * Admin part of the plugin.
 *
 * @package ShopPlugins\WooCommerce_Redirect_Thank_You
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WCRTY_Admin.
 *
 * Admin settings class.
 *
 * @class       WCRTY_Admin
 * @version     1.0.0
 * @author      Shop Plugins
 */
class WCRTY_Admin {

	/**
	 * __construct function.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Class hooks.
	 *
	 * All initial hooks used in this class.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		// Plugins page links.
		add_filter( 'plugin_action_links_' . WC_REDIRECT_THANK_YOU_FILE, array( $this, 'plugin_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );

		// Add WC settings tab.
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'woocommerce_settings_tab' ), 40 );

		// Settings page contents.
		add_action( 'woocommerce_settings_tabs_redirect_thank_you', array( $this, 'woocommerce_settings_page' ) );

		// Save settings page.
		add_action( 'woocommerce_update_options_redirect_thank_you', array( $this, 'woocommerce_update_options' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'admin_init', array( $this, 'auto_updater' ) );

		// Delete status on option change.
		add_action( 'pre_update_option_woocommerce_redirect_thank_you_sl_key', array( $this, 'update_license_status_on_key_change' ), 10, 2 );

		add_action( 'woocommerce_admin_field_wcrty_global_page', array( $this, 'generate_wcrty_global_page_html' ) );

		add_action( 'woocommerce_admin_field_wcrty_code', array( $this, 'generate_wcrty_code_html' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_redirect_thank_you_scripts', array( $this, 'sanitize_wcrty_code' ), 20, 3 );

		/** Shortcode Documenation Field */
		add_action( 'woocommerce_admin_field_wcrty_shortcodes', array( $this, 'generate_wcrty_shortcode_html' ) );

		/** Email add text*/
		add_action(
			'woocommerce_admin_field_wcrty_completed_email',
			array(
				$this,
				'generate_wcrty_completed_email_html',
			)
		);

		/** Gateway specific redirect */
		add_action(
			'woocommerce_admin_field_wcrty_payment_gateways',
			array(
				$this,
				'generate_wcrty_payment_gateways_html',
			)
		);

	}

	/**
	 * Enqueueing the scripts for admin side.
	 *
	 * @param string $hook Hook name.
	 */
	public function enqueue( $hook = '' ) {
		global $post;

		$enqueue = false;

		if ( $post && 'product' === get_post_type( $post ) ) {
			$enqueue = true;
		}

		if ( 'woocommerce_page_wc-settings' === $hook
			&& isset( $_GET['tab'] )
			&& 'redirect_thank_you' === $_GET['tab'] ) {
			$enqueue = true;
		}

		if ( ! $enqueue ) {
			return;
		}

		if ( function_exists( 'wp_enqueue_code_editor' ) ) {
			wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		}

		wp_enqueue_script(
			'wcrty-js',
			plugin_dir_url( WC_REDIRECT_THANK_YOU_FILE ) . '/assets/admin.js',
			array(
				'jquery',
				'wp-util',
			),
			'',
			true
		);
		wp_localize_script(
			'wcrty-js',
			'wcrty',
			array(
				'placeholders' => array(
					'products'   => esc_attr__( 'Search for a product', 'woocommerce' ),
					'categories' => esc_attr__( 'Search for a category', 'woocommerce' ),
				),
			)
		);
		wp_enqueue_style( 'wcrty-css', plugin_dir_url( WC_REDIRECT_THANK_YOU_FILE ) . '/assets/admin.css' );
	}

	/**
	 * Settings tab.
	 *
	 * Add a WooCommerce settings tab for the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array All WC settings tabs including newly added.
	 */
	public function woocommerce_settings_tab( $tabs ) {

		$tabs['redirect_thank_you'] = __( 'Thank You', 'woocommerce-redirect-thank-you' );

		return $tabs;

	}


	/**
	 * Settings page array.
	 *
	 * Get settings page fields array.
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_get_settings() {

		$settings = apply_filters(
			'woocommerce_redirect_thank_you_data_settings',
			array(

				array(
					'title' => __( 'Redirect Thank You', 'woocommerce-redirect-thank-you' ),
					'type'  => 'title',
					'id'    => 'wcrty_general',
				),

				array(
					'title'    => __( 'Global Thank You Page', 'woocommerce-redirect-thank-you' ),
					'desc'     => __( 'Use this setting to override the WooCommerce order-received endpoint.', 'woocommerce-redirect-thank-you' ),
					'id'       => 'woocommerce_redirect_thank_you_global',
					'type'     => 'wcrty_global_page',
					'default'  => '',
					'class'    => 'wc-enhanced-select-nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Scripts', 'woocommerce-redirect-thank-you' ),
					'desc'     => __( 'Add scripts to thank you pages.', 'woocommerce-redirect-thank-you' ),
					'id'       => 'woocommerce_redirect_thank_you_scripts',
					'type'     => 'wcrty_code',
					'default'  => '',
					'class'    => 'widefat',
					'css'      => 'min-width:300px;min-height:200px;',
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'License key', 'woocommerce-redirect-thank-you' ),
					'desc'     => '',
					'id'       => 'woocommerce_redirect_thank_you_sl_key',
					'default'  => '',
					'type'     => 'wcrty_license',
					'autoload' => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wcrty_general',
				),

				array(
					'title' => __( 'Payment Gateways', 'woocommerce-redirect-thank-you' ),
					'type'  => 'title',
					'desc'  => __( 'Select different redirect URLs based on Gateways. This overwrites the global thank you page, but not the one of a product.', 'woocommerce-redirect-thank-you' ),
					'id'    => 'wcrty_gateways',
				),

				array(
					'title'    => '',
					'desc'     => __( 'Use this setting to override the WooCommerce order-received endpoint.', 'woocommerce-redirect-thank-you' ),
					'id'       => 'wcrty_payment_gateways',
					'type'     => 'wcrty_payment_gateways',
					'default'  => '',
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wcrty_gateways',
				),

				array(
					'title' => __( 'Completed Order Email', 'woocommerce-redirect-thank-you' ),
					'type'  => 'title',
					'desc'  => __( 'Show additional text in the email based on Products or Categories', 'woocommerce-redirect-thank-you' ),
					'id'    => 'wcrty_emails',
				),

				array(
					'title' => __( 'Completed Order Email', 'woocommerce-redirect-thank-you' ),
					'type'  => 'wcrty_completed_email',
					'desc'  => '',
					'value' => '',
					'id'    => 'wcrty_completed_email_texts',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wcrty_emails',
				),

				array(
					'title' => __( 'Shortcodes', 'woocommerce-redirect-thank-you' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'wcrty_shortcodes',
				),

				array(
					'title'    => __( 'Shortcodes', 'woocommerce-redirect-thank-you' ),
					'desc'     => '',
					'id'       => '',
					'default'  => '',
					'type'     => 'wcrty_shortcodes',
					'autoload' => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wcrty_shortcodes',
				),
			)
		);

		return $settings;

	}

	/**
	 * License field.
	 *
	 * Print the HTML formatted license field.
	 *
	 * @since 1.0.0
	 */
	public function generate_wcrty_license_html() {

		$license = get_option( 'woocommerce_redirect_thank_you_sl_key' );
		$status  = get_option( 'woocommerce_redirect_thank_you_sl_status' );
		?>
		<tr valign='top'>
			<th scope='row' class='titledesc'>
				<label for='woocommerce_redirect_thank_you_sl_key'><?php esc_html_e( 'License key', 'woocommerce-redirect-thank-you' ); ?></label>
			</th>
			<td class='forminp forminp-text'>
				<input name='woocommerce_redirect_thank_you_sl_key' id='woocommerce_redirect_thank_you_sl_key'
				type='text' style='' value='<?php echo esc_attr( $license ); ?>' class=''>
				<span class='description'>
				<?php
				_e( 'Enter the license key, found in your <a target="_blank" href="https://shopplugins.com/account/">Shop Plugins dashboard</a>.' ); // WPCS: XSS ok.
				?>
				</span>
			</td>
		</tr>
		<?php
		if ( false !== $license ) :

			wp_nonce_field( 'wcrty_nonce_action', 'wcrty_nonce' );
			?>
			<tr valign='top'>

				<th scope='row' valign='top'>
					<?php
					esc_html_e( 'License status', 'woocommerce-redirect-thank-you' );
					?>
				</th>
				<td>
					<?php
					if ( false !== $status && 'valid' === $status ) :
						/*
						-- Deactivate button
						<input type='submit' class='button-secondary' name='wcrty_license_deactivate' style='vertical-align:middle; margin-right: 10px;'
						value='<?php _e( 'Deactivate License', 'woocommerce-redirect-thank-you' ); ?>'/>
						*/
						?>
						<span style='color:green;'><?php esc_html_e( 'Active', 'woocommerce-redirect-thank-you' ); ?></span>
						<?php
					else :
						?>
						<input type='submit' class='button-secondary' name='wcrty_license_activate'
						style='vertical-align:middle; margin-right: 10px;'
						value='<?php esc_attr_e( 'Activate License', 'woocommerce-redirect-thank-you' ); ?>'/>
						<span style='color:#A00;'><?php esc_html_e( 'License not yet activated', 'woocommerce-redirect-thank-you' ); ?></span>
						<?php
					endif;
					?>
				</td>
			</tr>
			<?php

		endif;

	}

	/**
	 * Shortcodes Documnetation field.
	 *
	 * Print the HTML formatted license field.
	 *
	 * @since 1.0.0
	 */
	public function generate_wcrty_shortcode_html() {
		?>
		<tr valign='top'>
			<th>
				[growdev_order_details]
			</th>
			<td>
				<?php
				_e( 'Use this shortcode to display order details on a thank you page if redirected from the checkout.', 'woocommerce-redirect-thank-you' );
				?>
			</td>
		</tr>
		<tr valign='top'>
			<th>
				[redirect_thank_you_text]<br/><span style="font-weight:400">Your Content goes here</span><br/>[/redirect_thank_you_text]
			</th>
			<td>
				<?php _e( 'Show content based on attributes.', 'woocommerce-redirect-thank-you' ); ?>
				<table class="form-table">
					<tr>
						<td style="width: 200px">
							<code>on_order=true|false</code>
							<?php echo '<p>' . esc_html__( 'Default: true', 'woocommerce-redirect-thank-you' ) . '</p>'; ?>
						</td>
						<td>
							<?php
							echo '<p>' . esc_html__( 'If set to false, it will show the content only if this page was visited directly and not redirected from the checkout page.', 'woocommerce-redirect-thank-you' ) . '</p>';
							?>
						</td>
					</tr>
					<tr>
						<td style="width: 200px">
							<code>product_id=PRODUCT_ID</code>
							<?php echo '<p>' . esc_html__( 'Default: 0', 'woocommerce-redirect-thank-you' ) . '</p>'; ?>
						</td>
						<td>
							<?php
							echo '<p>' . esc_html__( 'If a product ID is set, it will check if that product was purchased. Ignored if on_order is false.', 'woocommerce-redirect-thank-you' ) . '</p>';
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Text Based on Category or Products in Completed Email
	 */
	public function generate_wcrty_completed_email_html() {
		$emails = get_option( 'wcrty_completed_email_texts', array() );
		?>
		<tr>
			<td colspan="2">
				<div id="wcrty_completed_email_texts">
					<?php

					if ( $emails ) {
						foreach ( $emails['type'] as $index => $type ) {
							$condition = isset( $emails['condition'][ $index ] ) ? $emails['condition'][ $index ] : 0;
							$text      = isset( $emails['text'][ $index ] ) ? $emails['text'][ $index ] : '';
							?>
							<div class="wcrty-email-text">
								<label for="wcrty_completed_email_texts_type_<?php echo esc_attr( $index ); ?>">
									<strong>
										<?php esc_html_e( 'Condition Type', 'woocommerce-redirect-thank-you' ); ?>
									</strong>
									<br/>
									<select class="widefat wcrty-completed-email-type"
											name="wcrty_completed_email_texts[type][]">
										<option <?php selected( $type, 'product', true ); ?>
												value="product"><?php esc_html_e( 'Product', 'woocommerce-redirect-thank-you' ); ?></option>
										<option <?php selected( $type, 'category', true ); ?>
												value="category"><?php esc_html_e( 'Category', 'woocommerce-redirect-thank-you' ); ?></option>
									</select>
								</label>
								<br/>
								<label>
									<strong>
										<?php esc_html_e( 'Condition', 'woocommerce-redirect-thank-you' ); ?>
									</strong>
									<br/>
									<?php
									$data_placeholder = __( 'Search for a product', 'woocommerce-redirect-thank-you' );
									$class            = 'wc-product-search';

									if ( 'category' === $type ) {
										$data_placeholder = __( 'Search for a category', 'woocommerce-redirect-thank-you' );
										$class            = 'wcrty-category-search';
									}
									?>
									<select id="wcrty_completed_email_texts_condition_<?php echo esc_attr( $index ); ?>"
											class="<?php echo esc_attr( $class ); ?>" style="width: 50%;"
											name="wcrty_completed_email_texts[condition][]"
											data-placeholder="<?php echo esc_attr( $data_placeholder ); ?>">
										<?php

										if ( 'product' === $type ) {
											$product = wc_get_product( $condition );
											if ( is_object( $product ) ) {
												echo '<option value="' . esc_attr( $condition ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
											}
										} else {
											$category = get_term_by( 'id', $condition, 'product_cat' );

											if ( is_object( $category ) && ! is_wp_error( $category ) ) {
												echo '<option value="' . esc_attr( $condition ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $category->name ) . '</option>';
											}
										}
										?>
									</select>
								</label>
								<br/>
								<label for="wcrty_completed_email_texts_<?php echo esc_attr( $index ); ?>">
									<strong>
										<?php esc_html_e( 'Text', 'woocommerce-redirect-thank-you' ); ?>
									</strong>
								</label>
								<br/>
								<textarea id="wcrty_completed_email_texts_<?php echo esc_attr( $index ); ?>"
								name="wcrty_completed_email_texts[text][]"><?php echo $text; ?></textarea>
								<br/><br/>
								<button type="button"
										class="button button-secondary button-small wcrty-delete-email-text"><?php esc_html_e( 'Remove Text', 'woocommerce-redirect-thank-you' ); ?></button>
							</div>
							<?php
						}
					}
					?>

				</div>
				<button type="button" class="button button-secondary" id="wcrtyAddCompletedEmailText">
					<?php esc_html_e( 'Add Text', 'woocommerce-redirect-thank-you' ); ?>
				</button>
				<script type="text/template" id="tmpl-wcrty-completed-email">
					<div class="wcrty-email-text">
						<label for="wcrty_completed_email_texts_type_{{ data.index }}">
							<strong>
								<?php esc_html_e( 'Condition Type', 'woocommerce-redirect-thank-you' ); ?>
							</strong>
							<br/>
							<select class="widefat wcrty-completed-email-type"
									name="wcrty_completed_email_texts[type][]">
								<option value="product"><?php esc_html_e( 'Product', 'woocommerce-redirect-thank-you' ); ?></option>
								<option value="category"><?php esc_html_e( 'Category', 'woocommerce-redirect-thank-you' ); ?></option>
							</select>
						</label>
						<br/>
						<label>
							<strong>
								<?php esc_html_e( 'Condition', 'woocommerce-redirect-thank-you' ); ?>
							</strong>
							<br/>
							<select id="wcrty_completed_email_texts_condition_{{ data.index }}"
									class="wc-product-search" style="width: 50%;"
									name="wcrty_completed_email_texts[condition][]"
									data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>"
									data-action="woocommerce_json_search_products_and_variations">
								<?php
								$product_ids = array();

								foreach ( $product_ids as $product_id ) {
									$product = wc_get_product( $product_id );
									if ( is_object( $product ) ) {
										echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
									}
								}
								?>
							</select>
						</label>
						<br/>
						<label for="wcrty_completed_email_texts_{{ data.index }}">
							<strong>
								<?php esc_html_e( 'Text', 'woocommerce-redirect-thank-you' ); ?>
							</strong>
						</label>
						<br/>
						<textarea id="wcrty_completed_email_texts_{{ data.index }}"
						name="wcrty_completed_email_texts[text][]"></textarea>
						<br/><br/>
						<button type="button"
								class="button button-secondary button-small wcrty-delete-email-text"><?php esc_html_e( 'Remove Text', 'woocommerce-redirect-thank-you' ); ?></button>
					</div>
				</script>
			</td>
		</tr>
		<?php
	}

	/**
	 * Generate the Global Page.
	 *
	 * @param array $field Field configuration.
	 */
	public function generate_wcrty_global_page_html( $field ) {

		$url     = get_option( 'woocommerce_redirect_thank_you_global', '' );
		$page_id = get_option( 'woocommerce_redirect_thank_you_global_page', 0 );
		$type    = get_option( 'woocommerce_redirect_thank_you_global_type', 'none' );
		$link    = '';

		// It's a page.
		if ( is_numeric( $url ) ) {
			$page_id = $url;
		}

		if ( 'custom_link' === $type ) {
			$link = $url;
		}
		?>
		<tr valign='top'>
			<th>
				<?php echo esc_html( $field['title'] ); ?>
			</th>
			<td>
				<ul class="thank_you_page_redirect submitbox">
					<li>
						<p>
							<label>
								<input type="radio" <?php checked( $type, 'none', true ); ?>
								class="wc-redirect-type" name="_global_redirect_type"
								value="none"/> <?php esc_html_e( 'None', 'woocommerce-redirect-thank-you' ); ?>
							</label>
						</p>
					</li>
					<?php
					do_action( 'wc_thank_you_page_global_redirect_before_custom_link', $type, $url );
					?>
					<li>
						<p>
							<label>
								<input type="radio" <?php checked( $type, 'custom_link', true ); ?>
								class="wc-redirect-type" name="_global_redirect_type"
								value="custom_link"/> <?php esc_html_e( 'Custom URL', 'woocommerce-redirect-thank-you' ); ?>
							</label>
						</p>
						<div class="redirect-type-input hidden">
							<input type="url" class="widefat" name="_global_redirect_custom_url"
							value="<?php echo esc_attr( $link ); ?>"
							placeholder="<?php esc_attr_e( 'Enter a Custom URL', 'woocommerce-redirect-thank-you' ); ?>">
						</div>
					</li>
					<?php
					do_action( 'wc_thank_you_page_global_redirect_after_custom_link', $type, $url );
					?>
					<li class="wide" id="actions">
						<p>
							<label>
								<input type="radio" <?php checked( $type, 'page', true ); ?> class="wc-redirect-type"
								name="_global_redirect_type"
								value="page"/> <?php esc_html_e( 'Page', 'woocommerce-redirect-thank-you' ); ?>
							</label>
						</p>
						<div class="redirect-type-input hidden">
							<?php
							$args = array(
								'name'             => '_global_redirect_page_id',
								'id'               => '_global_redirect_page_id',
								'sort_column'      => 'menu_order',
								'sort_order'       => 'ASC',
								'show_option_none' => ' ',
								'class'            => 'chosen_select_nostd',
								'echo'             => false,
								'selected'         => absint( $page_id ),
							);
							echo str_replace( ' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'woocommerce-redirect-thank-you' ) . "' style='width:90%;' class='chosen_select_nostd' id=", wp_dropdown_pages( $args ) );

							?>
						</div>
					</li>
					<?php
					do_action( 'wc_thank_you_page_global_redirect_after_pages', $type, $url );
					?>
				</ul>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get only enabled gateways.
     *
     * @return array
	 */
	public function get_enabled_gateways() {
		$registered_gateways = WC()->payment_gateways()->payment_gateways();
        $enabled_gateways    = array();

        foreach ( $registered_gateways as $gateway_id => $gateway ) {
            if ( 'yes' === $gateway->enabled ) {
                $enabled_gateways[ $gateway_id ] = $gateway;
            }
        }

        return $enabled_gateways;
    }

	/**
	 * Generate the Payment Gateways Field.
	 *
	 * @param array $field Field configuration.
	 */
	public function generate_wcrty_payment_gateways_html( $field ) {
		$gateways            = get_option( $field['id'], array() );
		$registered_gateways = $this->get_enabled_gateways();
		$pages               = get_pages();
		?>
		<tr>
			<td colspan="2">
				<input type="hidden" name="<?php echo esc_attr( $field['id'] ); ?>" value="-1" />
				<div id="wcrty_gateways_urls">
					<?php

					if ( $gateways && is_array( $gateways ) ) {
						foreach ( $gateways as $index => $gateway_options ) {
							$name       = $field['id'] . '[' . $index . ']';
							$gateway_id = $gateway_options['gateway'];
							$link       = isset( $gateway_options['url'] ) ? $gateway_options['url'] : '';
							$page_id    = isset( $gateway_options['page_id'] ) ? absint( $gateway_options['page_id'] ) : 0;
							$type       = isset( $gateway_options['type'] ) ? $gateway_options['type'] : 'custom_link';
							?>
							<div class="wcrty-gateway-url">
								<label for="<?php echo esc_attr( $name ); ?>_gateway"><?php esc_html_e( 'Gateway', 'woocommerce-redirect-thank-you' ); ?></label>
								<br/>
								<select id="<?php echo esc_attr( $name ); ?>_gateway" name="<?php echo esc_attr( $name ); ?>[gateway]">
									<?php
									foreach ( $registered_gateways as $reg_gateway_id => $gateway_object ) {
										?>
										<option <?php selected( $reg_gateway_id, $gateway_id, true ); ?>
												value="<?php echo esc_attr( $reg_gateway_id ); ?>"><?php echo $gateway_object->get_title(); ?></option>
										<?php
									}
									?>
								</select>
								<ul class="wcrty-page-options">
									<li>
										<p>
											<label>
												<input type="radio" <?php checked( $type, 'custom_link', true ); ?>
												name="<?php echo esc_attr( $name ); ?>[type]"
												value="custom_link"/> <?php esc_html_e( 'Custom URL', 'woocommerce-redirect-thank-you' ); ?>
											</label>
										</p>
										<div class="wcrty-page-option <?php echo 'custom_link' !== $type ? 'hidden' : ''; ?>">
											<input type="url" class="widefat"
											name="<?php echo esc_attr( $name ); ?>[url]"
											value="<?php echo esc_attr( $link ); ?>"
											placeholder="<?php esc_attr_e( 'Enter a Custom URL', 'woocommerce-redirect-thank-you' ); ?>">
										</div>
									</li>
									<li class="wide" id="actions">
										<p>
											<label>
												<input type="radio" <?php checked( $type, 'page', true ); ?>
												name="<?php echo esc_attr( $name ); ?>[type]"
												value="page"/> <?php esc_html_e( 'Page', 'woocommerce-redirect-thank-you' ); ?>
											</label>
										</p>
										<div class="wcrty-page-option <?php echo 'page' !== $type ? 'hidden' : ''; ?>">

											<select name="<?php echo esc_attr( $name ); ?>[page_id]">
												<?php
												foreach ( $pages as $page ) {
													if ( ! $page->post_title ) {
														continue;
													}
													?>
													<option <?php selected( $page_id, $page->ID, true ); ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo $page->post_title; ?>
														(<?php echo $page->ID; ?>)
													</option>
													<?php
												}
												?>
											</select>
										</div>

									</li>
								</ul>

								<button type="button"
										class="button button-secondary button-small wcrty-delete-gateway"><?php esc_html_e( 'Remove Gateway', 'woocommerce-redirect-thank-you' ); ?></button>

							</div>
							<?php
						}
					}
					?>
				</div>
				<button type="button" class="button button-secondary" id="wcrtyAddGatewayURL">
					<?php esc_html_e( 'Add Gateway Redirect', 'woocommerce-redirect-thank-you' ); ?>
				</button>
				<script type="text/template" id="tmpl-wcrty-gateway-url">
					<?php
					$name = $field['id'] . '[{{ data.index }}]';
					?>
					<div class="wcrty-gateway-url">
						<label for="<?php echo esc_attr( $name ); ?>_gateway"><?php esc_html_e( 'Gateway', 'woocommerce-redirect-thank-you' ); ?></label>
						<br/>
						<select id="<?php echo esc_attr( $name ); ?>_gateway" name="<?php echo esc_attr( $name ); ?>[gateway]">
							<?php
							foreach ( $registered_gateways as $reg_gateway_id => $gateway_object ) {
								?>
								<option value="<?php echo esc_attr( $reg_gateway_id ); ?>"><?php echo $gateway_object->get_title(); ?></option>
								<?php
							}
							?>
						</select>
						<ul class="wcrty-page-options">
							<li>
								<p>
									<label>
										<input type="radio" checked="checked"
										name="<?php echo esc_attr( $name ); ?>[type]"
										value="custom_link"/> <?php esc_html_e( 'Custom URL', 'woocommerce-redirect-thank-you' ); ?>
									</label>
								</p>
								<div class="wcrty-page-option">
									<input type="url" class="widefat"
									name="<?php echo esc_attr( $name ); ?>[url]"
									value=""
									placeholder="<?php esc_attr_e( 'Enter a Custom URL', 'woocommerce-redirect-thank-you' ); ?>">
								</div>
							</li>
							<li class="wide" id="actions">
								<p>
									<label>
										<input type="radio"
										name="<?php echo esc_attr( $name ); ?>[type]"
										value="page"/> <?php esc_html_e( 'Page', 'woocommerce-redirect-thank-you' ); ?>
									</label>
								</p>
								<div class="wcrty-page-option hidden">

									<select name="<?php echo esc_attr( $name ); ?>[page_id]">
										<?php
										foreach ( $pages as $page ) {
											if ( ! $page->post_title ) {
												continue;
											}
											?>
											<option value="<?php echo esc_attr( $page->ID ); ?>"><?php echo $page->post_title; ?>
												(<?php echo $page->ID; ?>)
											</option>
											<?php
										}
										?>
									</select>
								</div>

							</li>
						</ul>
						<button type="button"
								class="button button-secondary button-small wcrty-delete-gateway"><?php esc_html_e( 'Remove Gateway', 'woocommerce-redirect-thank-you' ); ?></button>

					</div>
				</script>
			</td>
		</tr>
		<?php
	}

	/**
	 * Generate the Code Editor
	 *
	 * @param array $field Field configuration.
	 */
	public function generate_wcrty_code_html( $field ) {
		$value = WC_Admin_Settings::get_option( $field['id'], $field['default'] );
		?>
		<tr valign='top'>
			<th>
				<?php echo esc_html( $field['title'] ); ?>
			</th>
			<td>
				<textarea
						id="<?php echo esc_attr( $field['id'] ); ?>"
						name="<?php echo esc_attr( $field['id'] ); ?>"
						style="<?php if ( isset( $value['css'] ) ) { echo esc_attr( $value['css'] ); } ?>"
						class="<?php if ( isset( $value['class'] ) ) { echo esc_attr( $value['class'] ); } ?> wcrty-code-editor"
				><?php echo $value; ?></textarea>
			</td>
		</tr>
		<?php
	}

	/**
	 * Sanitize the code editor.
	 *
	 * @param string $value Value after default WC sanitization.
	 * @param string $option Option which we go over.
	 * @param string $raw Unsanitized value.
	 * @return array|string
	 */
	public function sanitize_wcrty_code( $value, $option, $raw ) {
		if ( ! $value && $raw ) {
			$value = esc_sql( $raw );
		}
		return $value;
	}

	/**
	 * Delete status.
	 *
	 * Delete the license status when the license key changes. This
	 * forces the user to re-activate the license.
	 *
	 * @since 1.0.0
	 *
	 * @param   mixed $new_value New value to be saved.
	 * @param   mixed $old_value Current value, about to be overwritten.
	 *
	 * @return  mixed               The new value.
	 */
	public function update_license_status_on_key_change( $new_value, $old_value ) {

		if ( $old_value && $old_value !== $new_value ) :
			delete_option( 'woocommerce_redirect_thank_you_sl_status' );
		endif;

		return $new_value;

	}

	/**
	 * Activate/Deactivate license.
	 *
	 * Send a API request to activate/deactivate the current site.
	 *
	 * @since 1.0.0
	 */
	public function activate_deactivate_license() {

		// Bail if not activating license.
		if ( ! isset( $_POST['wcrty_license_activate'] ) && ! isset( $_POST['wcrty_license_deactivate'] ) ) :
			return;
		endif;

		// Verify nonce.
		if ( ! isset( $_POST['wcrty_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wcrty_nonce'] ), 'wcrty_nonce_action' ) ) : // WPCS: sanitization ok.
			return;
		endif;

		// data to send in our API request.
		$api_params = array(
			'edd_action' => isset( $_POST['wcrty_license_activate'] ) ? 'activate_license' : 'deactivate_license',
			'license'    => trim( get_option( 'woocommerce_redirect_thank_you_sl_key', '' ) ),
			'item_name'  => rawurlencode( 'WooCommerce Redirect Thank You' ),
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, WC_REDIRECT_THANK_YOU_SHOP_PLUGINS_URL ) );

		// make sure the response came back okay.
		if ( is_wp_error( $response ) ) :
			return false;
		endif;

		// decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'woocommerce_redirect_thank_you_sl_status', $license_data->license );

	}

	/**
	 * Settings page content.
	 *
	 * Output settings page content via WooCommerce output_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_settings_page() {
		WC_Admin_Settings::output_fields( $this->woocommerce_get_settings() );
	}

	/**
	 * Save settings.
	 *
	 * Save settings based on WooCommerce save_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_update_options() {
		if ( isset( $_POST['_global_redirect_type'] ) ) {
			update_option( 'woocommerce_redirect_thank_you_global_type', $_POST['_global_redirect_type'], false );
		}

		switch ( $_POST['_global_redirect_type'] ) {
			case 'custom_link':
				$redirect_input = '_global_redirect_custom_url';
				break;
			default:
				$redirect_input = '_global_redirect_page_id';
				break;
		}

		$url = WCRTY_Meta_Box_Redirect::get_posted_url( $redirect_input, $_POST['_global_redirect_type'] );

		if ( $url ) {
			update_option( 'woocommerce_redirect_thank_you_global', untrailingslashit( $url ), false );
			if ( '_global_redirect_page_id' === $redirect_input ) {
				update_option( 'woocommerce_redirect_thank_you_global_page', stripslashes( $_POST['_global_redirect_page_id'] ), false );
			}
			do_action( 'wc_thank_you_page_save_global' );
		} else {
			delete_option( 'woocommerce_redirect_thank_you_global_type' );
			delete_option( 'woocommerce_redirect_thank_you_global' );
			delete_option( 'woocommerce_redirect_thank_you_global_page' );
			do_action( 'wc_thank_you_page_remove_global' );
		}

		WC_Admin_Settings::save_fields( $this->woocommerce_get_settings() );

	}

	/**
	 * Plugin page links
	 *
	 * @param array $links Array of links.
	 *
	 * @return array
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=redirect_thank_you' ) . '">' . __( 'Settings', 'woocommerce-redirect-thank-you' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Plugin meta info links.
	 *
	 * @param array  $links Array of links.
	 * @param string $file  File.
	 *
	 * @return array
	 */
	public function add_plugin_row_meta( $links, $file ) {

		if ( WC_REDIRECT_THANK_YOU_FILE === $file ) {
			$links[] = '<a href="https://shopplugins.com/support">' . __( 'Support', 'woocommerce-redirect-thank-you' ) . '</a>';
			$links[] = '<a href="http://docs.shopplugins.com/article/15-woocommerce-redirect-thank-you" target="_blank">' . __( 'Docs', 'woocommerce-redirect-thank-you' ) . '</a>';
			$links[] = '<a href="https://shopplugins.com/plugins/category/woocommerce/" target="_blank">' . __( 'WooCommerce Plugins', 'woocommerce-redirect-thank-you' ) . '</a>';
		}

		return $links;

	}

	/**
	 * Updater.
	 *
	 * Function to get automatic updates.
	 *
	 * @since 2.0.0
	 */
	public function auto_updater() {

		if ( ! class_exists( '\ShopPlugins\Updater\WP_Updater' ) ) {
			require WC_REDIRECT_THANK_YOU_PATH . '/vendor/shopplugins/wp-updater/class-wp-updater.php';
		}
		new \ShopPlugins\Updater\WP_Updater(
			array(
				'file'    => WC_REDIRECT_THANK_YOU_FILE,
				'name'    => 'WooCommerce Redirect Thank You',
				'version' => WC_REDIRECT_THANK_YOU_VERSION,
				'api_url' => 'https://shopplugins.com',
			)
		);
	}

}
