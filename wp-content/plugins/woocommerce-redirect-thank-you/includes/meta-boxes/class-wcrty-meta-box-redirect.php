<?php
/**
 * Custom Thank You page redirect
 *
 * Displays the product data box, tabbed, with several panels covering price, stock etc.
 *
 * @author      Shop Plugins
 * @category    Admin
 * @version     1.0.0
 * @package     ShopPlugins\WooCommerce_Redirect_Thank_You
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WCRTY_Meta_Box_Redirect
 */
class WCRTY_Meta_Box_Redirect {

	/**
	 * Pages.
	 *
	 * @var null
	 */
	public static $pages = null;

	/**
	 * Get cached pages so we don't have to create DB requests all the time.
	 *
	 * @return array|false|null
	 */
	public static function get_pages() {
		if ( null === self::$pages ) {
			self::$pages = get_pages();
		}

		return self::$pages;
	}

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post Post Object.
	 */
	public static function output( $post ) {

		$_redirect_page_id = get_post_meta( $post->ID, '_redirect_page_id', true );
		$_redirect_url     = get_post_meta( $post->ID, '_redirect_url', true );
		$_redirect_type    = get_post_meta( $post->ID, '_redirect_type', true );
		$custom_link       = '';

		if ( ! $_redirect_type ) {
			$_redirect_type = 'none';
		}

		if ( 'custom_link' === $_redirect_type ) {
			$custom_link = $_redirect_url;
		}
		?>
		<script>
			document.addEventListener('DOMContentLoaded',function() {
				var types = document.getElementsByClassName('wc-redirect-type');
				function redirectChangeType(event) {

					if ( event.target.checked ) {
						for (var i = 0; i < types.length; i++) {
							if ( types[i].parentNode.parentNode.nextElementSibling ) {
								types[i].parentNode.parentNode.nextElementSibling.classList.add('hidden');
							}
						}
						if ( event.target.parentNode.parentNode.nextElementSibling ) {
							event.target.parentNode.parentNode.nextElementSibling.classList.remove('hidden');
						}
					}
				}
				if (types) {
					for (var i = 0; i < types.length; i++) {
						types[i].addEventListener('change', redirectChangeType);
						if ( types[i].checked ) {
							if ( types[i].parentNode.parentNode.nextElementSibling ) {
								types[i].parentNode.parentNode.nextElementSibling.classList.remove('hidden');
							}
						}
					}
				}
			});
		</script>
		<ul class="thank_you_page_redirect submitbox">
			<li>
				<p>
					<label>
						<input type="radio" <?php checked( $_redirect_type, 'none', true ); ?> class="wc-redirect-type" name="_redirect_type" value="none" /> <?php esc_html_e( 'None', 'woocommerce-redirect-thank-you' ); ?>
					</label>
				</p>
			</li>
			<?php
				do_action( 'wc_thank_you_page_redirect_before_custom_link', $post, $_redirect_type, $_redirect_url );
			?>
			<li>
				<p>
					<label>
						<input type="radio" <?php checked( $_redirect_type, 'custom_link', true ); ?> class="wc-redirect-type" name="_redirect_type" value="custom_link" /> <?php esc_html_e( 'Custom URL', 'woocommerce-redirect-thank-you' ); ?>
					</label>
				</p>
				<div class="redirect-type-input hidden">
					<input type="url" class="widefat" name="_redirect_custom_url" value="<?php echo esc_attr( $custom_link ); ?>" placeholder="<?php esc_attr_e( 'Enter a Custom URL', 'woocommerce-redirect-thank-you' ); ?>">
				</div>
			</li>
			<?php
				do_action( 'wc_thank_you_page_redirect_after_custom_link', $post, $_redirect_type, $_redirect_url );
			?>
			<li class="wide" id="actions">
				<p>
					<label>
						<input type="radio" <?php checked( $_redirect_type, 'page', true ); ?> class="wc-redirect-type" name="_redirect_type" value="page" /> <?php esc_html_e( 'Page', 'woocommerce-redirect-thank-you' ); ?>
					</label>
				</p>
				<div class="redirect-type-input hidden">
				<?php
				$args = array(
					'name'             => '_redirect_page_id',
					'id'               => '_redirect_page_id',
					'sort_column'      => 'menu_order',
					'sort_order'       => 'ASC',
					'show_option_none' => ' ',
					'class'            => 'chosen_select_nostd',
					'echo'             => false,
					'selected'         => absint( $_redirect_page_id ),
				);
				echo str_replace( ' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'woocommerce-redirect-thank-you' ) . "' style='width:90%;' class='chosen_select_nostd' id=", wp_dropdown_pages( $args ) );

				?>
				</div>
			</li>
			<?php
				do_action( 'wc_thank_you_page_redirect_after_pages', $post, $_redirect_type, $_redirect_url );
			?>
		</ul>
		<p><?php _e( 'Customers who buy this product will be redirected to this page.', 'growdev' ); ?></p>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 */
	public static function save( $post_id, $post ) {

		if ( ! isset( $_POST['_redirect_type'] ) || 'none' === $_POST['_redirect_type'] ) {
			// Let's delete the redirect url if exists.
			delete_post_meta( $post_id, '_redirect_type' );
			delete_post_meta( $post_id, '_redirect_url' );
			delete_post_meta( $post_id, '_redirect_page_id' );
			do_action( 'wc_thank_you_page_remove_meta', $post_id );
			return;
		}

		update_post_meta( $post_id, '_redirect_type', sanitize_text_field( $_POST['_redirect_type'] ) );

		switch ( $_POST['_redirect_type'] ) {
			case 'custom_link':
				$redirect_input = '_redirect_custom_url';
				break;
			default:
				$redirect_input = '_redirect_page_id';
				break;
		}

		$redirect_input = apply_filters( 'wc_thank_you_page_redirect_input_on_save', $redirect_input, $_POST['_redirect_type'], $post );

		$url = self::get_posted_url( $redirect_input, $_POST['_redirect_type'] );

		if ( $url ) {
			update_post_meta( $post_id, '_redirect_url', untrailingslashit( $url ) );
			if ( '_redirect_page_id' === $redirect_input ) {
				update_post_meta( $post_id, '_redirect_page_id', stripslashes( $_POST['_redirect_page_id'] ) );
			}
			do_action( 'wc_thank_you_page_save_meta', $post_id );
		} else {
			delete_post_meta( $post_id, '_redirect_type' );
			delete_post_meta( $post_id, '_redirect_url' );
			delete_post_meta( $post_id, '_redirect_page_id' );
			do_action( 'wc_thank_you_page_remove_meta', $post_id );
		}

	}

	/**
	 * Output Variation Redirect URLs.
	 *
	 * @param integer $loop The index of the current variation.
	 * @param array   $variation_data Array of data from postmeta.
	 * @param WP_Post $variation Post object.
	 */
	public static function variation_output( $loop, $variation_data, $variation ) {

		$name              = 'variation_redirect_url[' . $loop . ']';
		$_redirect_page_id = get_post_meta( $variation->ID, '_redirect_page_id', true );
		$_redirect_url     = get_post_meta( $variation->ID, '_redirect_url', true );
		$_redirect_type    = get_post_meta( $variation->ID, '_redirect_type', true );
		$custom_link       = '';

		if ( ! $_redirect_type ) {
			$_redirect_type = 'none';
		}

		if ( 'custom_link' === $_redirect_type ) {
			$custom_link = $_redirect_url;
		}

		?>
		<ul class="wcrty-page-options">
			<li>
				<p>
					<label>
						<input type="radio" <?php checked( $_redirect_type, 'none', true ); ?>
						name="<?php echo esc_attr( $name ); ?>[type]"
						value="none"/> <?php esc_html_e( 'None', 'woocommerce-redirect-thank-you' ); ?>
					</label>
				</p>
			</li>
			<li>
				<p>
					<label>
						<input type="radio" <?php checked( $_redirect_type, 'custom_link', true ); ?>
						name="<?php echo esc_attr( $name ); ?>[type]"
						value="custom_link"/> <?php esc_html_e( 'Custom URL', 'woocommerce-redirect-thank-you' ); ?>
					</label>
				</p>
				<div class="wcrty-page-option <?php echo 'custom_link' !== $_redirect_type ? 'hidden' : ''; ?>">
					<input type="url" class="widefat"
					name="<?php echo esc_attr( $name ); ?>[url]"
					value="<?php echo esc_attr( $custom_link ); ?>"
					placeholder="<?php esc_attr_e( 'Enter a Custom URL', 'woocommerce-redirect-thank-you' ); ?>">
				</div>
			</li>
			<li class="wide" id="actions">
				<p>
					<label>
						<input type="radio" <?php checked( $_redirect_type, 'page', true ); ?>
						name="<?php echo esc_attr( $name ); ?>[type]"
						value="page"/> <?php esc_html_e( 'Page', 'woocommerce-redirect-thank-you' ); ?>
					</label>
				</p>
				<div class="wcrty-page-option <?php echo 'page' !== $_redirect_type ? 'hidden' : ''; ?>">

					<select name="<?php echo esc_attr( $name ); ?>[page_id]">
						<?php
						foreach ( self::get_pages() as $page ) {
							if ( ! $page->post_title ) {
								continue;
							}
							?>
							<option <?php selected( $_redirect_page_id, $page->ID, true ); ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo $page->post_title; ?>
								(<?php echo $page->ID; ?>)
							</option>
							<?php
						}
						?>
					</select>
				</div>

			</li>
		</ul>
		<?php
	}

	/**
	 * Saving the Variation data.
	 *
	 * @param integer $variation_id Variation ID.
	 * @param integer $i Loop/Index to use in $_POST to check for the correct data.
	 */
	public static function variation_save( $variation_id, $i ) {
		$redirect_url_options = isset( $_POST['variation_redirect_url'] ) && isset( $_POST['variation_redirect_url'][ $i ] ) ? $_POST['variation_redirect_url'][ $i ] : false;

		if ( false === $redirect_url_options ) {
			return;
		}

		if ( ! isset( $redirect_url_options['type'] ) || 'none' === $redirect_url_options['type'] ) {
			// Let's delete the redirect url if exists.
			delete_post_meta( $variation_id, '_redirect_type' );
			delete_post_meta( $variation_id, '_redirect_url' );
			delete_post_meta( $variation_id, '_redirect_page_id' );
			do_action( 'wc_thank_you_page_remove_variation_meta', $variation_id );
			return;
		}

		update_post_meta( $variation_id, '_redirect_type', sanitize_text_field( $redirect_url_options['type'] ) );

		switch ( $redirect_url_options['type'] ) {
			case 'custom_link':
				$redirect_input = 'url';
				break;
			default:
				$redirect_input = 'page_id';
				break;
		}

		$redirect_input = apply_filters( 'wc_thank_you_page_redirect_input_on_variation_save', $redirect_input, $redirect_url_options['type'], $variation_id );

		$url = '';
		switch ( $redirect_url_options['type'] ) {
			case 'custom_link':
				$url = sanitize_text_field( $redirect_url_options['url'] );
				if ( $url ) {
					// if not https or http set, let's add it.
					if ( false === strpos( $url, 'http' ) ) {
						$url = 'http://' . $url;
					}
				}
				break;
			default:
				$id = absint( $redirect_url_options['page_id'] );
				if ( $id ) {
					$url = get_permalink( $id );
					if ( ! $url ) {
						$url = ''; }
				}

				break;
		}

		if ( $url ) {
			update_post_meta( $variation_id, '_redirect_url', untrailingslashit( $url ) );
			if ( 'page_id' === $redirect_input ) {
				update_post_meta( $variation_id, '_redirect_page_id', stripslashes( $redirect_url_options['page_id'] ) );
			}
			do_action( 'wc_thank_you_page_save_variation_meta', $variation_id );
		} else {
			delete_post_meta( $variation_id, '_redirect_type' );
			delete_post_meta( $variation_id, '_redirect_url' );
			delete_post_meta( $variation_id, '_redirect_page_id' );
			do_action( 'wc_thank_you_page_remove_variation_meta', $variation_id );
		}
	}

	/**
	 * Get the URL we want.
	 *
	 * @param string $redirect_input The input name attribute.
	 * @param string $redirect_type  The type we use so we can validate the URL.
	 *
	 * @return false|string
	 */
	public static function get_posted_url( $redirect_input, $redirect_type ) {
		if ( ! isset( $_POST[ $redirect_input ] ) ) {
			return '';
		}

		$ret = '';

		switch ( $redirect_type ) {
			case 'custom_link':
				$ret = sanitize_text_field( $_POST[ $redirect_input ] );
				if ( $ret ) {
					// if not https or http set, let's add it.
					if ( false === strpos( $ret, 'http' ) ) {
						$ret = 'http://' . $ret;
					}
				}
				break;
			default:
				$id = absint( $_POST[ $redirect_input ] );
				if ( $id ) {
					$ret = get_permalink( absint( $_POST[ $redirect_input ] ) );
					if ( ! $ret ) {
						$ret = ''; }
				}

				break;
		}

		return apply_filters( 'wc_thank_you_page_redirect_get_posted_url', $ret, $redirect_type );
	}
}
