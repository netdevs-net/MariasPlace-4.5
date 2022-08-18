<?php

/*
 * REORDER CHECKOUT FILEDS
 */
add_filter("woocommerce_checkout_fields", "woocommerce_reorder_checkout_fields", 9999);

if (!function_exists('woocommerce_reorder_checkout_fields')) {

    function woocommerce_reorder_checkout_fields($fields) {

        /* To reorder state field you need to add this array. */
        $order = array(
            "billing_first_name",
            "billing_last_name",
            "billing_company",
            "billing_country",
            "billing_state",
            "billing_address_1",
            "billing_address_2",
            "billing_city",
            "billing_postcode",
            "billing_phone",
            "billing_email",
        );

        foreach ($order as $field) {
            $ordered_fields[$field] = $fields["billing"][$field];
        }

        $fields["billing"] = $ordered_fields;

        /* To change email and phone number you have to add only class no need to add priority. */

        $fields['billing']['billing_email']['class'][0] = 'form-row-first';
        $fields['billing']['billing_phone']['class'][0] = 'form-row-last';

        return $fields;
    }

}
/*
  ADD BOOTSTRAP TO CHECKOUT
 */
add_filter('woocommerce_checkout_fields', 'addBootstrapToCheckoutFields');

function addBootstrapToCheckoutFields($fields) {
    foreach ($fields as &$fieldset) {
        foreach ($fieldset as &$field) {
            // if you want to add the form-group class around the label and the input
            $field['class'][] = 'form-group';

            // add form-control to the actual input
            $field['input_class'][] = 'form-control';
        }
    }
    return $fields;
}

function order_details($atts) {
    
    $atts = shortcode_atts(
        array(
            'id' => '',
        ), $atts
    );
    
    $order_id = $atts['id'];
    $order = new WC_Order($order_id);
    $order_data = $order->get_data();
    $order_key = $order_data['order_key'];
    $customer_id = $order_data['customer_id'];
    $billing_details = $order_data['billing'];
    $shipping_details = $order_data['shipping'];
    $payment_method = $order_data['payment_method'];
    $transaction_id = $order_data['transaction_id'];
    $currency = $order_data['currency'];
    $total = $order_data['total'];
    $total_tax = $order_data['total_tax'];
//    pre($order_data);
    // Get and Loop Over Order Items

    $order_summary = '<div class="col-md-8"><table class="table cart"><tbody>';
    foreach ($order->get_items() as $item_id => $item) {
        $total = $item->get_total();
        $tax = $item->get_subtotal_tax();
        $order_summary .= '<tr>';
        $order_summary .= '<td class="col-2 product-thumbnail">' . get_the_post_thumbnail($item->get_product_id(), 'thumbnail') . '</td>';
        $order_summary .= '<td class="col-6 product-name ss"><a href="' . get_the_permalink($item->get_product_id()) . '">' . get_the_title($item->get_variation_id()) . '<a/></td>';
        $order_summary .= '<td class="col-2 product-quantity text-muted"> x' . $item->get_quantity() . '</td>';
        $order_summary .= '<td class="col-2 product-price">' . woocommerce_price(($total + $tax)) . '</td>';
        $order_summary .= '</tr>';
//        echo $product_id = $item->get_product_id();
//        echo $variation_id = $item->get_variation_id();
//      echo $product = $item->get_product();
//        echo$name = $item->get_name();
//        echo$quantity = $item->get_quantity();
//        echo $subtotal = $item->get_subtotal();
//        echo $total = $item->get_total();
//        echo $tax = $item->get_subtotal_tax();
    }
    $order_summary .= '</tbody></table></div>';

    $order_totals = '<div class="col-md-4"><table class="table">';
    $order_totals .= '<tbody class="bg-light text-dark">';
    $order_totals .= '<tr>';
    $order_totals .= '<td>Order Subtotal</td>';
    $order_totals .= '<td>' . woocommerce_price($order->get_subtotal()) . '</td>';
    $order_totals .= '</tr>';
    $order_totals .= '<tr>';
    $order_totals .= '<td>Shipping</td>';
    $order_totals .= '<td>' . woocommerce_price($order->get_shipping_total()) . '</td>';
    $order_totals .= '</tr>';
    $order_totals .= '<tr>';
    $order_totals .= '<td>Tax</td>';
    $order_totals .= '<td>' . woocommerce_price($order->get_total_tax()) . '</td>';
    $order_totals .= '</tr>';
    $order_totals .= '<tr>';
    $order_totals .= '<td>Discount</td>';
    $order_totals .= '<td>' . woocommerce_price($order->get_total_discount()) . '</td>';
    $order_totals .= '</tr>';
    $order_totals .= '<tr class="bg-navyblue text-light font-weight-bold">';
    $order_totals .= '<td>Total</td>';
    $order_totals .= '<td>' . woocommerce_price($order->get_total()) . '</td>';
    $order_totals .= '</tr>';
    $order_totals .= '</tbody>';
    $order_totals .= '</table></div>';
    $order_summary .= $order_totals;
    ?>
    <?php

    $order_html = '';
    $order_html .= '<div class="col-md-12">';
//Biling Info
    $billing_info = '<div class="col-md-6 order-info">';
    $billing_info .= '<h2>Billing Address</h2>';
    $billing_info .= '<div class="customer-name">' . $billing_details['first_name'] . ' ' . $billing_details['last_name'] . '</div>';
    $billing_info .= '<div class="customer-company">' . $billing_details['company'] . '</div>';
    $billing_info .= '<div class="customer-address">' . $billing_details['address_1'] . '</div>';
    $billing_info .= '<div class="customer-address">' . $billing_details['address_2'] . '</div>';
    $billing_info .= '<div class="customer-address">' . $billing_details['city'] . ' ' . $billing_details['state'] . ' ' . $billing_details['country'] . ' ' . $billing_details['postcode'] . '</div>';
    $billing_info .= '<div class="customer-email">' . $billing_details['email'] . '</div>';
    $billing_info .= '<div class="customer-phone">' . $billing_details['phone'] . '</div>';
    $billing_info .= '</div>';
//Billing Info
//Shipping Info
// pre($shipping_details);
    $shipping_info = '<div class="col-md-6 order-info">';
    $shipping_info .= '<h2>Shipping Address</h2>';
    $shipping_info .= '<div class="customer-name">' . $shipping_details['first_name'] . ' ' . $shipping_details['last_name'] . '</div>';
    $shipping_info .= '<div class="customer-company">' . $shipping_details['company'] . '</div>';
    $shipping_info .= '<div class="customer-address">' . $shipping_details['address_1'] . '</div>';
    $shipping_info .= '<div class="customer-address">' . $shipping_details['address_2'] . '</div>';
    $shipping_info .= '<div class="customer-address">' . $shipping_details['city'] . ' ' . $shipping_details['state'] . ' ' . $shipping_details['country'] . ' ' . $shipping_details['postcode'] . '</div>';
    $shipping_info .= '<div class="customer-email">' . $shipping_details['email'] . '</div>';
    $shipping_info .= '<div class="customer-phone">' . $shipping_details['phone'] . '</div>';
    $shipping_info .= '</div>';
//Shipping Info
//    YOUR INFORMATION
    $your_details = '<div class="col-md-6 your-info">';
    $your_details .= '<h2>Your Information</h2>';
    $your_details .= '<div class="customer-name">' . $billing_details['first_name'] . ' ' . $billing_details['last_name'] . '</div>';
    $your_details .= '<div class="customer-company">' . $billing_details['company'] . '</div>';
    $your_details .= '<div class="customer-email">' . $billing_details['email'] . '</div>';
    $your_details .= '<div class="customer-phone">' . $billing_details['phone'] . '</div>';
    $your_details .= '</div>';
    //    YOUR INFORMATION
//    Payment Details
    $payment_details = '<div class="col-md-6 payment-details">';
    $payment_details .= '<h2>Payment Method</h2>';
    $payment_details .= $payment_method;
    $payment_details .= '</div>';
//    Payment Details
    $order_html .= '<div class="row mb-5 pb-5 border-bottom">' . $your_details . $shipping_info . '</div>';
    $order_html .= '<div class="row mb-5 pb-5 border-bottom">' . $payment_details . $billing_info . '</div>';
    $order_html .= '<div id="cart-details" class="row mb-5 pb-5 border-bottom order-summary-wrap">' . $order_summary . '</div>';
    $order_html .= '</div>';



    return $order_html;
}

add_shortcode('order_details', 'order_details');



?>