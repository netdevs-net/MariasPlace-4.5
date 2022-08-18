<?php
/**
 * Recurring totals
 *
 * @author  Prospress
 * @package WooCommerce Subscriptions/Templates
 * @version 2.6.0
 */
if (!defined('ABSPATH')) {
    exit;
}

$display_th = true;
?>
<div class="seprator-line">
    <hr>
</div>
<div class="recurring">
    <h4 class="heading text-light"><?php esc_html_e('Recurring charges', 'woocommerce-subscriptions'); ?></h4>
    <?php foreach ($recurring_carts as $recurring_cart_key => $recurring_cart) : ?>
        <?php
        $count_p = sizeof($recurring_cart->cart_contents);
        echo '<h6 class="product_title imhere">';
        $prefix = '';
        $count = 1;
        foreach ($recurring_cart->cart_contents as $key => $products) {
            if ($count_p > $count) {
                $prefix = '&';
                echo '<span>' . get_the_title($products['product_id']) . ' </span>' . $prefix . ' ';
            } else {
                echo '<span>' . get_the_title($products['product_id']) . ' </span>';
            }
            $count++;
        }
        echo '</h6>';
        ?>

        <?php if (0 == $recurring_cart->next_payment_date) : ?>
            <?php continue; ?>
        <?php endif; ?>
        <div class="cart-subtotal recurring-total d-flex justify-content-between align-items-center">
            <div class="text-left col-md-6 p-md-2" rowspan="<?php echo esc_attr($carts_with_multiple_payments); ?>"><?php esc_html_e('Subtotal', 'woocommerce-subscriptions'); ?></div>
            <div class="text-right col-md-6 p-md-2" data-title="<?php esc_attr_e('Subtotal', 'woocommerce-subscriptions'); ?>"><?php wcs_cart_totals_subtotal_html($recurring_cart); ?></div>
        </div>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <?php if (0 == $recurring_cart->next_payment_date) : ?>
                <?php continue; ?>
            <?php endif; ?>

            <?php
            foreach ($recurring_cart->get_coupons() as $recurring_code => $recurring_coupon) :
                if ($recurring_code !== $code) {
                    continue;
                }
                ?>
                <div class="cart-discount coupon-<?php echo esc_attr($code); ?> recurring-total d-flex justify-content-between align-items-center ">
                    <div class="text-left col-md-6 p-md-2" rowspan="<?php echo esc_attr($carts_with_multiple_payments); ?>"><?php wc_cart_totals_coupon_label($coupon); ?></div>
                    <div class="text-right col-md-6 p-md-2" data-title="<?php wc_cart_totals_coupon_label($coupon); ?>"><?php wcs_cart_totals_coupon_html($recurring_coupon, $recurring_cart); ?>
                        <?php
                        echo ' ';
                        wcs_cart_coupon_remove_link_html($recurring_coupon);
                        ?></div>
                </div>
                <?php
            endforeach;
        endforeach;
        ?>
        <?php
//        PER PRODUCT SHIPPING
        if (WC()->cart->needs_shipping() || WC()->cart->show_shipping()) :
            $count = 1;
            foreach (WC()->cart->get_cart() as $cart_item) {
                if ($count_p > $count) {
                    $current_shipping_cost = WC()->cart->get_cart_shipping_total();
                    echo '<div class="get_custom_shiping-total d-flex justify-content-between align-items-center">
            <div class="text-left col-md-6 p-md-2" rowspan="2">Product Shipping</div>
            <div class="text-right col-md-6 p-md-2"><span class="woocommerce-Price-amount amount">' . $current_shipping_cost . '</span> / month</div>
        </div>';
                }
                $count++;
            }
            //        PER PRODUCT SHIPPING
            ?>


            <?php //echo wcs_cart_totals_shipping_html() ;  ?>
        <?php endif; ?>



        <?php if (0 == $recurring_cart->next_payment_date) : ?>
            <?php continue; ?>
        <?php endif; ?>

        <?php foreach ($recurring_cart->get_fees() as $recurring_fee) : ?>
            <div class="fee recurring-total d-flex justify-content-between align-items-center">
                <div class="text-left col-md-6 p-md-2"><?php echo esc_html($recurring_fee->name); ?></div>
                <div  class="text-right col-md-6 p-md-2"><?php wc_cart_totals_fee_html($recurring_fee); ?></div>
            </div>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && WC()->cart->tax_display_cart === 'excl') : ?>
            <?php if (get_option('woocommerce_tax_total_display') === 'itemized') : ?>

                <?php foreach (WC()->cart->get_taxes() as $tax_id => $tax_total) : ?>

                    <?php if (0 == $recurring_cart->next_payment_date) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php foreach ($recurring_cart->get_tax_totals() as $recurring_code => $recurring_tax) : ?>
                        <?php
                        if (!isset($recurring_tax->tax_rate_id) || $recurring_tax->tax_rate_id !== $tax_id) {
                            continue;
                        }
                        ?>
                        <div class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($recurring_code)); ?> recurring-total d-flex justify-content-between align-items-center">
                            <div class="text-left col-md-6 p-md-2"><?php echo esc_html($recurring_tax->label); ?></div>
                            <div class="text-right col-md-6 p-md-2" data-title="<?php echo esc_attr($recurring_tax->label); ?>"><?php echo wp_kses_post(wcs_cart_price_string($recurring_tax->formatted_amount, $recurring_cart)); ?></div>
                        </div>
                    <?php endforeach; ?>

                <?php endforeach; ?>

            <?php else : ?>
                <?php if (0 == $recurring_cart->next_payment_date) : ?>
                    <?php continue; ?>
                <?php endif; ?>
                <div class="tax-total recurring-total d-flex justify-content-between align-items-center ">
                    <?php if (is_cart()) { ?>
                        <div class="text-left col-md-6 p-md-2"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></div>
                        <div class="text-right col-md-6 p-md-2"><span> calculated in checkout </span></div>
                    <?php } else { ?>
                        <div class="text-left col-md-6 p-md-2"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></div>
                        <div class="text-right col-md-6 p-md-2"><?php echo wp_kses_post(wcs_cart_price_string($recurring_cart->get_taxes_total(), $recurring_cart)); ?></div>
                    <?php } ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (0 == $recurring_cart->next_payment_date) : ?>
            <?php continue; ?>
        <?php endif; ?>
        <div class="order-total recurring-total d-flex justify-content-between align-items-center ">
            <div class="text-left col-md-6 p-md-2" rowspan="<?php echo esc_attr($carts_with_multiple_payments); ?>"><?php esc_html_e('Recurring total', 'woocommerce-subscriptions'); ?></div>
            <div class="text-right col-md-6 p-md-2" data-title="<?php esc_attr_e('Recurring total', 'woocommerce-subscriptions'); ?>"><?php wcs_cart_totals_order_total_html($recurring_cart); ?></div>
        </div>
    <?php endforeach; ?>

</div>