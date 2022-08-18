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

    $order_summary = '<div class="col-md-8"><table class="table cart">';
    $order_summary .= '<thead>';
    $order_summary .= '<tr>';
    $order_summary .= '<th><h3 class="font-24">Order Summary</h3></th>';
    $order_summary .= '<th>&nbsp;</th>';
    $order_summary .= '<th><h3 class="font-24">Qty</h3></th>';
    $order_summary .= '<th><h3 class="font-24">Price</h3></th>';

    $order_summary .= '</tr>';
    $order_summary .= '</thead>';
    $order_summary .= '<tbody>';
    foreach ($order->get_items() as $item_id => $item) {
        $total = $item->get_total();
        $tax = $item->get_subtotal_tax();
        $order_summary .= '<tr>';
        $order_summary .= '<td class="col-2 product-thumbnail">' . get_the_post_thumbnail($item->get_product_id(), 'thumbnail') . '</td>';
        $order_summary .= '<td class="col-6 product-name"><a href="' . get_the_permalink($item->get_product_id()) . '">' . $item->get_name() . '<a/></td>';
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
    $billing_info = '<div class="col-md-6 order-info font-24 mb-3">';
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
    $shipping_info = '<div class="col-md-6 order-info font-24 mb-3">';
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
    $your_details = '<div class="col-md-6 your-info font-24 mb-3">';
    $your_details .= '<h2>Your Information</h2>';
    $your_details .= '<div class="customer-name">' . $billing_details['first_name'] . ' ' . $billing_details['last_name'] . '</div>';
    $your_details .= '<div class="customer-company">' . $billing_details['company'] . '</div>';
    $your_details .= '<div class="customer-email">' . $billing_details['email'] . '</div>';
    $your_details .= '<div class="customer-phone">' . $billing_details['phone'] . '</div>';
    $your_details .= '</div>';
    //    YOUR INFORMATION
//    Payment Details
    $payment_details = '<div class="col-md-6 payment-details font-24 mb-3">';
    $payment_details .= '<h2>Payment Method</h2>';
    $payment_details .= $payment_method;
    $payment_details .= '</div>';
//    Payment Details
    $order_html .= '<div class="wc-row-order col-12">';
    $order_html .= '<div class="row no-gutters">' . $your_details . $shipping_info . '</div>';
    $order_html .= '<div class="row no-gutters">' . $payment_details . $billing_info . '</div></div>';
    $order_html .= '<div class="wc-row-order col-12"><div id="cart-details" class="row order-summary-wrap">' . $order_summary . '</div></div>';
    $order_html .= '</div>';



    return $order_html;
}

add_shortcode('order_details', 'order_details');

function order_completed($atts) {

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
    $order_date = $order->get_date_paid()->format('F d, Y');
//    pre($order_data);
    // Get and Loop Over Order Items
    $order_html = '';
    $order_html .= '<div class="col-md-12">';
    $order_html .= '<div class="thankyou-order col-12 mx-auto">';
    $order_heading_icon = '<img width="150" height="150" src="' . get_template_directory_uri() . '/inc/assets/images/order-complete-icon.png" class="img-fluid mb-3" alt="Order Confiramtion Icon"/>';
    $order_heading = '<h2 class="font-36">Thank you! Your order has been successful.</h2>';
    $order_description = '<p class="font-24 mb-0">Please keep the below information for your reference.</p>
<p class="font-24">We will also send a confirmation to the email address you used for this order.</p>';
    $dashboard_btn = '<div class="col-12 mx-auto my-5 text-center"><a href="/my-account" class="btn btn-lg btn-pink d-inline-block font-weight-bold px-5">Back to Dashboard</a></div>';
    $order_header = '<div class="order-header text-center">' . $order_heading_icon . $order_heading . $order_description . '</div>';
    $order_info = '<div class="col-12 mx-auto my-5">
        <h2 class="font-36 text-center">Order Details</h2>
        <table class="table table-thankyou">
  <tbody> 
   <tr>
      <th scope="col">Order Number</th>
          <td>' . $order->get_id() . '</td>
    </tr>
    <tr>
      <th scope="row">Order Date</th>
    <td>' . $order_date . '</td>
    </tr>
    <tr>
      <th scope="row">Total Order</th>
      <td>' . woocommerce_price($order->get_total()) . '</td>
      
    </tr>
    <tr class="d-none">
      <th scope="row">Payment Method</th>
      <td>' . $payment_method . '</td>
    </tr>
    <tr>
      <th scope="row">Name</th>
      <td><div class="customer-name">' . $billing_details['first_name'] . ' ' . $billing_details['last_name'] . '</div></td>
    </tr>
    <tr>
      <th scope="row">Email</th>
      <td><div class="customer-name">' . $billing_details['email'] . '</div>'
            . '<div class="customer-company d-none">' . $shipping_details['company'] . '</div>'
            . '<div class="customer-address d-none">' . $shipping_details['address_1'] . '</div>'
            . '<div class="customer-address d-none">' . $shipping_details['address_2'] . '</div>'
            . '<div class="customer-address d-none">' . $shipping_details['city'] . ' ' . $shipping_details['state'] . ' ' . $shipping_details['country'] . ' ' . $shipping_details['postcode'] . '</div>'
            . '</td>
    </tr> 
  </tbody>
</table></div>';
    $order_html .= $order_header . $order_info . $dashboard_btn . '</div>';



    return $order_html;
}

add_shortcode('order_completed', 'order_completed');

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
    echo '<style>
        table.wp-list-table .column-name {
            width: 10%;
        } 
        table.wp-list-table .column-username{
            width: 15%;
        }
        table.wp-list-table .column-email{
            width: 15%;
        }
        table.wp-list-table .column-role{
            width: 8%;
        }
        table.wp-list-table .column-pmpro_membership_level{
            width: 12%;
        }
        table.wp-list-table .last-login{
            width: 7%;
        }
        table.wp-list-table .column-number_of_logins{
            width: 8%;
        }
    </style>';
}

add_filter('manage_users_columns','remove_users_columns');
function remove_users_columns($column_headers) {
    unset($column_headers['role']);
      unset($column_headers['posts']);
    
 
    return $column_headers;
}

add_action( 'woocommerce_customer_save_address','notify_admin_customer_address_change', 10, 2);
function notify_admin_customer_address_change( $user_id ) {

    // Set the $from_name, $from_email, and $to for your site.
    $from_name = 'Your From Name';
    $from_email = 'wpsuperheroes@gmail.com';
    $to = 'contact@mariasplace.com';
    
    global $woocommerce, $current_user;
    
    if(($current_user->user_email != $_POST['billing_email']) || ($woocommerce->customer->get_billing_email() != $_POST['billing_email'])):

        ob_start();
        wc_get_template( 'emails/processing-order-header.php', array( 'email_heading' => 'User has updated email address.' ) );
        echo '<tr style="display: block;background-color: #DE005D;">
            <td style="padding: 3rem;text-align: center;">
                <img src="'.get_template_directory_uri().'/woocommerce/emails/images/mail_logo.png" style="display: block;padding-bottom: 2.25px;max-width: 100%;margin: 0 auto" alt="logo" class="logo">
                <h2 style="font-weight: 900;margin-top: 30px;text-align: center; font-size: 32px; line-height: 40px; color: #fff;margin-bottom: 0;">User has updated email address</h2> 
            </td>
        </tr>
        <tr>
            <td class="content_td" style="text-align: center;padding: 0;">
                <table style="width: 100%; margin: 0 auto;position: relative;background-color: #fff;border-spacing: 0;padding-bottom: 3rem;padding-top:4rem;">
                    <tbody>
                        <tr>
                            <td style="text-align: center;"><p style=" margin: 0 0 20px;font-size:14px;margin-bottom:25px;line-height: 22px;font-weight: 500;padding-left:30px; padding-right:30px;color: #292929;">';
                                echo "".$current_user->user_login." (".$_POST['billing_email'].") has updated their address. Check for any open subscriptions on this account.";
                            echo "</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>";
        wc_get_template( 'emails/processing-order-footer.php' );
        $message = ob_get_clean();

        $headers = 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n";
        
        wp_mail($to, 'User has updated email address', $message, $headers);

    endif;  

    
}

?>