<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */
if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>
<div class="col-12">
    <form name="checkout" method="post" class="checkout row woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
        <?php if ($checkout->get_checkout_fields()) : ?>

            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

            <div class="col-lg-7 col-md-12" id="customer_details">
                <div class="col-12">
                    <?php do_action('woocommerce_checkout_billing'); ?>
                </div>

                <div class="col-12">
                    <?php do_action('woocommerce_checkout_shipping'); ?>
                </div>
            </div>

            <?php do_action('woocommerce_checkout_after_customer_details'); ?>

        <?php endif; ?>

        <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                <!-- <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3> -->

        <?php do_action('woocommerce_checkout_before_order_review'); ?>

        <div class="woocommerce-checkout-review-order col-lg-5 col-md-12" id="order_review">
            
            <div class="bg-navyblue text-light p-4 rounded">
                <h4 class="heading text-light">Order Summary</h4>
                <?php
                do_action('woocommerce_review_order_before_cart_contents');

                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
    //                    pre($cart_item);
                        ?>
                        <div class="cart_item row rounded d-flex align-items-center bg-light text-dark no-gutters p-2 mb-3">
                            <div class="product-image col-2"> <?= $cart_item['data']->get_image(); //get_the_post_thumbnail($cart_item['product_id'], 'thumbnail', array('class' => 'img-fluid'));     ?></div>
                            <div class="product-name col-7 px-3">
                                <?php echo apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
                                <?php //echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php //echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                            <div class="product-total col-3 text-center">
                                <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity d-block">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php echo get_woocommerce_currency_symbol() . ' ' . $cart_item['line_total']; ?>
                                <?php //echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                do_action('woocommerce_review_order_after_cart_contents');
                ?>
            
                <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>
        </div>

        <?php do_action('woocommerce_checkout_after_order_review'); ?>

    </form>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
