<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}?>
<!-- Processing Order Header -->
<?php wc_get_template( 'emails/processing-order-header.php'); ?>
       <tr style="display: block;background-color: #DE005D;">
            <td style="padding: 3rem;text-align: center;">
                <img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/mail_logo.png" style="display: block;padding-bottom: 2.25px;max-width: 100%;margin: 0 auto" alt="logo" class="logo">
                <h2 style="font-weight: 900;margin-top: 30px;text-align: center; font-size: 48px; line-height: 65px; color: #fff;margin-bottom: 0;"><?php echo $email_heading; ?></h2> 
            </td>
        </tr>
        <tr style="background-color: #DE005D;">
            <td style="padding: 0;">
                <table style="width: 100%; max-width: 560px;margin: 0 auto ;border-spacing: 0;box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.16);">
                    <tbody>
                        <tr>
                            <td style="text-align: center;padding: 0;">
                                <img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/triangle_img.png" style="display: block;max-width: 100%;margin: 0 auto;" alt="tringle_img" class="tringle_img">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0; text-align: center;background-color: #fff;">
                                <img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/checked.png" style="display: block;padding:3rem 0 2.25rem;max-width: 100%;margin: 0 auto;" alt="checked" class="checked">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
         <tr>
            <td class="content_td" style="text-align: center;padding: 0;">
                <table style="width: 100%; max-width: 560px;margin: 0 auto;position: relative;background-color: #fff;border-spacing: 0;box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.16);padding-bottom: 3rem;">
                    <tbody>
                        <tr>
                            <td style="text-align: center;">
                                  <?php if ( $additional_content ) { ?>
                                         <span style="margin: 0 0 16px;font-size: 14px;font-weight:400;color: #292929;padding: 0 2.25rem;text-align:center">   
                                       <?php   echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) ); ?>
                                          </span>
                                       <?php  } ?>
                                <p style=" margin: 0 0 20px;font-size:14px;margin-bottom:25px;line-height: 22px;font-weight: 500;max-width: 523px;color: #292929;">In the meantime, see your order detail below: </p>
                                <h4 style="margin: 0 0 30px;font-size:20px;line-height: 27px; color:#292929;font-weight: 700;"> Order Details </h4>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 700;width: 50%;display: inline-block;float: left;text-align: right; padding-right: 15px;box-sizing: border-box;">Order Number</p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 400;width: 50%;display: inline-block;padding-left: 15px;text-align: left;box-sizing: border-box;"><?php echo $order->get_id(); ?></p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 700;width: 50%;display: inline-block;float: left;text-align: right;padding-right: 15px;box-sizing: border-box;">Order Date</p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 400;width: 50%;display: inline-block;padding-left: 15px;box-sizing: border-box;text-align: left;"><?php $order_date= $order->get_date_created(); echo $orderdate = date("F , d Y", strtotime($order_date)); ?></p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 700;width: 50%;display: inline-block;float: left;text-align: right; padding-right: 15px;box-sizing: border-box;">Total Order</p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 400;width: 50%;display: inline-block;padding-left: 15px;text-align: left;box-sizing: border-box;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?> <?php echo $order->get_total(); ?></p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 700;width: 50%;display: inline-block;float: left;text-align: right;padding-right: 15px;box-sizing: border-box;">Payment Method</p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 400;width: 50%;display: inline-block;padding-left: 15px;text-align: left;box-sizing: border-box;"><?php echo $order->get_payment_method(); ?></p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 700;width: 50%;display: inline-block;float: left;text-align: right; padding-right: 15px;box-sizing: border-box;">Cardholder Name</p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 400;width: 50%;display: inline-block;padding-left: 15px;text-align: left;box-sizing: border-box;"><?php echo $order->get_billing_first_name(); echo "&nbsp;"; echo $order->get_billing_last_name();?></p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 700;width: 50%;display: inline-block;float: left;text-align: right;padding-right: 15px;box-sizing: border-box;">Shipping Address</p>
                                <p style="font-size:14px;line-height: 19px; color:#292929;margin: 0 0 16px;font-weight: 400;width: 50%;display: inline-block;padding-left: 15px;text-align: left;box-sizing: border-box;"><span style="margin: 0 0 8px;display: block;"><?php echo $order->get_formatted_shipping_address();?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px dashed rgba(0,0,0,0.2);padding-bottom: 36px;">
                            </td>
                        </tr>
                         <?php foreach ( $order->get_items() as $item_id => $item ) {
                         
                              $product_id = $item->get_product_id();
                              $_product = wc_get_product( $product_id );
                              $thumbnail = $_product->get_image();
                         ?>
                        <tr>
                            <td style="padding: 0 2.25rem 1rem;">
                              <?php   echo $image = '<div class="ts-product-image" style="width: 36px; height: 36px;float:left;">'
                                           . $thumbnail .
                                            '</div>'; ?>
                                <p style="float: left;margin:0;padding-left: 16px;"><span style="font-weight: 900; font-size: 14px; line-height: 19px;color: #292929;display: block;box-sizing: border-box;"><?php echo $name = $item->get_name(); ?></span><!-- <span style="font-weight: normal; font-size: 14px; line-height: 19px;color: #292929;">(Booklet + Supplies)</span> --></p>
                                <p style="float: right;margin:9px 0 0;"><span style="font-weight: normal; font-size: 14px; line-height: 19px;color: rgba(0,0,0,0.5);box-sizing: border-box;padding-right: 16px;">x<?php echo $quantity = $item->get_quantity(); ?></span><span style="font-weight: normal; font-size: 14px; line-height: 19px;color: #292929;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?> <?php echo $total = $item->get_total(); ?></span></p>
                            </td>
                        </tr>
                    <?php } ?>
                        <tr>
                            <td>
                                <p style="max-width: 230px; float: right; width: 100%; display: block;font-weight: normal; font-size: 14px; line-height: 19px;color: #292929;padding: 0 36px;margin: 0 0 16px;">
                                    <span style="width: 50%;box-sizing: border-box;text-align: left; float: left;">Order Subtotal</span>
                                    <span style="float:left;box-sizing: border-box;width: 50%; text-align: right;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?><?php echo $subtotal = $item->get_subtotal(); ?></span>
                                </p>
                                <p style="max-width: 230px; float: right; width: 100%; display: block;font-weight: normal; font-size: 14px; line-height: 19px;color: #292929;padding: 0 36px;margin: 0 0 16px;">
                                    <span style="width: 50%;box-sizing: border-box;text-align: left; float: left;">Shipping</span>
                                    <span style="float:left;box-sizing: border-box;width: 50%; text-align: right;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?><?php echo $order->get_shipping_total(); ?></span>
                                </p>
                                <p style="max-width: 230px; float: right; width: 100%; display: block;font-weight: normal; font-size: 14px; line-height: 19px;color: #292929;padding: 0 36px;margin: 0 0 16px;">
                                    <span style="width: 50%;box-sizing: border-box;text-align: left; float: left;">Tax</span>
                                    <span style="float:left;box-sizing: border-box;width: 50%; text-align: right;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?><?php echo $order->get_total_tax(); ?></span>
                                </p>
                                <p style="max-width: 230px; float: right; width: 100%; display: block;font-weight: normal; font-size: 14px; line-height: 19px;color: #292929;padding: 0 36px;margin: 0 0 16px;">
                                    <span style="width: 50%;box-sizing: border-box;text-align: left; float: left;">Discount</span>
                                    <span style="float:left;box-sizing: border-box;width: 50%; text-align: right;color: #DE005D;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?><?php echo $discount = $order->get_discount_total(); ?></span>
                                </p>
                                <p style="max-width: 230px; float: right; width: 100%; display: block;text-align:right;font-weight: normal;font-size: 12px; line-height: 16px;color: rgba(0,0,0,0.5);padding: 0 36px;margin: 0 0 16px;">
                                    <!-- SPRSL10 code used -->
                                    <?php 
                                     foreach ( $order->get_items( 'coupon' ) as $item ) {
                                        // Get the WC_Coupon Object
                                        $coupon = new WC_Coupon($item->get_code());
                                        echo $coupon->code; echo " code used"; 
                                    }
                                    ?>
                                </p>
                                <p style="max-width: 230px; float: right; width: 100%; border-top: 1px solid #000000;display: block;font-weight: 900; font-size: 14px; line-height: 19px;color: #292929;margin: 0 36px 16px;padding-top: 5px;">
                                    <span style="width: 50%;box-sizing: border-box;text-align: left; float: left;">Total</span>
                                    <span style="float:left;box-sizing: border-box;width: 50%; text-align: right;"><?php $currency=$order->get_currency(); if($currency=='USD') { echo '$'; } else{ echo $currency; } ?> <?php echo $total = $order->get_total();?> </span>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 0;">
                <table style="width: 100%; max-width: 560px;margin: 0 auto 64px;border-spacing: 0;">
                    <tbody>
                        <tr>
                            <td style="padding: 0; text-align: center;background-color: #fff;">
                                <img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/tr_bottom.png" style="display: block;max-width: 100%;margin: 0 auto;" alt="tr_bottom">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom: 3rem;">
                  <?php 
                          if($order->get_customer_note()!=''){ ?>
                        <p style="font-weight: normal; font-size: 14px; line-height: 24px;color: #292929;max-width: 560px;margin: 0 auto 16px;">
                              <?php echo $order->get_customer_note(); ?>
                        </p>   
                      <?php } ?>
                   <a href="<?php echo $order->get_checkout_order_received_url(); ?>" style="background: #DE005D; border-radius: 6px;display: inline-block;margin: 0 auto;color: #fff;font-weight: bold; font-size: 20px; line-height: 27px;width: 240px;text-align: center;padding: 10px 10px 11px;text-decoration: none;box-shadow: 0px 8px 16px rgba(222, 0, 93, 0.24);">View Order</a>           
            </td>
        </tr>

<!-- Processing Order Footer -->
<?php wc_get_template( 'emails/processing-order-footer.php'); ?>