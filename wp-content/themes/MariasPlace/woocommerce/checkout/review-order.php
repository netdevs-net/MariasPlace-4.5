<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourdiveme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (dive diveme developer) will need to copy dive new files to your diveme to
 * maintain compatibility. We try to do divis as little as possible, but it does
 * happen. When divis occurs dive version of dive template file will be bumped and
 * dive readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */
defined('ABSPATH') || exit;
?>
<div class="shop_table woocommerce-checkout-review-order-table">

    <div class="cart-subtotal d-flex justify-content-between align-items-center">
        <div class="text-left col-md-6 p-md-2"><?php esc_html_e('Subtotal', 'woocommerce'); ?></div>
        <div class="text-right col-md-6 p-md-2"><?php wc_cart_totals_subtotal_html(); ?></div>
    </div>

    <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
        <div class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?> d-flex justify-content-between align-items-center">
            <div class="text-left col-md-6 p-md-2"><?php wc_cart_totals_coupon_label($coupon); ?></div>
            <div class="text-right col-md-6 p-md-2"><?php wc_cart_totals_coupon_html($coupon); ?></div>
        </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

        <?php do_action('woocommerce_review_order_before_shipping'); ?>
        <!--PER PRODUCT SHIPPING-->
        <div class="get_custom_shiping-total d-flex justify-content-between align-items-center">
            <div class="text-left col-md-6 p-md-2">Product Shipping</div>
            <div class="text-right col-md-6 p-md-2"><span class="woocommerce-Price-amount amount"><?php echo WC()->cart->get_cart_shipping_total() ?></span></div>
        </div>
        <?php //wc_cart_totals_shipping_html(); ?>
        <!--PER PRODUCT SHIPPING-->
        <?php do_action('woocommerce_review_order_after_shipping'); ?>

    <?php endif; ?>

    <?php foreach (WC()->cart->get_fees() as $fee) : ?>
        <div class="fee d-flex justify-content-between align-items-center">
            <div class="text-left col-md-6 p-md-2"><?php echo esc_html($fee->name); ?></div>
            <div class="text-right col-md-6 p-md-2"><?php wc_cart_totals_fee_html($fee); ?></div>
        </div>
    <?php endforeach; ?>

    <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
        <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
            <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                <div class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?> d-flex justify-content-between align-items-center">
                    <div class="text-left col-md-6 p-md-2"><?php echo esc_html($tax->label); ?></div>
                    <div class="text-right col-md-6 p-md-2"><?php echo wp_kses_post($tax->formatted_amount); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="tax-total d-flex justify-content-between align-items-center">
                <div class="text-left col-md-6 p-md-2"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></div>
                <div class="text-right col-md-6 p-md-2"><?php wc_cart_totals_taxes_total_html(); ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <div class="order-total d-flex justify-content-between align-items-center">
        <div class="text-left col-md-6 p-md-2"><?php esc_html_e('Total', 'woocommerce'); ?></div>
        <div class="text-right col-md-6 p-md-2"><?php wc_cart_totals_order_total_html(); ?></div>
    </div>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>


</div>
