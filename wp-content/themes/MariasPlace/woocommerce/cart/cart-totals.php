<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */
defined('ABSPATH') || exit;
?>
<?php if (wc_coupons_enabled()) { ?>
    <div class="col-md-12 p-0">
        <div class="coupon form-inline row justify-content-center align-items-center no-gutters mb-3">
            <div class="form-group col-9">
                <input type="text" name="coupon_code" class="form-control form-control-lg w-100 mr-2" id="coupon_code" value="" placeholder="<?php esc_attr_e('Enter Promo Code', 'MariasPlace'); ?>" />
            </div>
            <div class="form-group col-3">
                <button type="submit" class="w-100 px-0 btn-pink apply-coupon" name="apply_coupon" value="<?php esc_attr_e('Apply', 'MariasPlace'); ?>"><?php esc_attr_e('Apply', 'MariasPlace'); ?></button>
            </div>
            <?php do_action('woocommerce_cart_coupon'); ?>
        </div>
    </div>
<?php } ?>

<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">
    <?php do_action('woocommerce_before_cart_totals'); ?>
    <h4 class="heading text-light">Order Summary</h4>
    <div class="shop_table text-white table shop_table_responsive">       
        <div class="cart-subtotal d-flex justify-content-between align-items-center ">
            <div scope="col" class="text-left col-md-6 p-md-2"><?php esc_html_e('Subtotal', 'woocommerce'); ?></div>
            <div scope="col" class="text-right col-md-6 p-md-2" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"><?php wc_cart_totals_subtotal_html(); ?></div>
        </div>
        <!--Discount Row-->
        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?> d-flex justify-content-between align-items-center ">
                <div scope="col" class="text-left col-md-6 p-md-2">Discount <small><?php echo esc_attr(sanitize_title($code)); ?> used</small></div>
                <div scope="col" class="text-right col-md-6 p-md-2" data-title="<?php echo esc_attr(wc_cart_totals_coupon_label($coupon, false)); ?>"><?php wc_cart_totals_coupon_html($coupon); ?></div>
            </div>
        <?php endforeach; ?>
        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="fee d-flex justify-content-between align-items-center ">
                <div scope="col" class="text-left col-md-6 p-md-2"><?php echo esc_html($fee->name); ?></div>
                <div scope="col" class="text-right col-md-6 p-md-2" data-title="<?php echo esc_attr($fee->name); ?>"><?php wc_cart_totals_fee_html($fee); ?></div>
            </div>
        <?php endforeach; ?>
        <?php
        if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text = '';

            if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
                /* translators: %s location. */
                $estimated_text = sprintf(' <small>' . esc_html__('(estimated for %s)', 'woocommerce') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]);
            }

            if ('itemized' === get_option('woocommerce_tax_total_display')) {
                foreach (WC()->cart->get_tax_totals() as $code => $tax) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    ?>
                    <div class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?> d-flex justify-content-between align-items-center ">
                        <div scope="col" class="text-left col-md-6 p-md-2"><?php echo esc_html($tax->label) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                    ?></div>
                        <div scope="col" class="text-right col-md-6 p-md-2" data-title="<?php echo esc_attr($tax->label); ?>"><?php echo wp_kses_post($tax->formatted_amount); ?></div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="tax-total d-flex justify-content-between align-items-center ">
                    <div scope="col" class="text-left col-md-6 p-md-2"><?php
                        echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text;
                        ?></div>
                    <div scope="col" class="text-right col-md-6 p-md-2" data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>">calculated in checkout</div>
                </div>
                <?php
            }
        }
        ?>
        <?php do_action('woocommerce_cart_totals_before_order_total'); ?>
        <div class="order-total d-flex justify-content-between align-items-center ">
            <div scope="col" class="text-left col-md-6 p-md-2"><?php esc_html_e('Total', 'woocommerce'); ?></div>
            <div scope="col" class="text-right col-md-6 p-md-2" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>"><?php wc_cart_totals_order_total_html(); ?></div>
        </div>
        <?php do_action('woocommerce_cart_totals_after_order_total'); ?>
    </ul>
   

    <div class="col-md-12 proceed-checkout">
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" title="Checkout" class="w-100 py-3 btn heading btn-pink btn-checkout">Proceed to Checkout</a>
    </div>
    <?php do_action('woocommerce_after_cart_totals'); ?>
</div>