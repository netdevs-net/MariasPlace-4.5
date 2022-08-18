<?php
/**
 * Processing Order Footer
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-footer.php.
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

defined( 'ABSPATH' ) || exit;
?>
    
<tr>
            <td style="text-align: center;background-color: #DE005D;" align="center">
                <h5 style="font-weight: 900; font-size: 20px; line-height: 27px;color: #fff;">Find Us on</h5>
                <p style="margin: 40px 0;">
                    <a href="https://twitter.com/mariasplacecom" style="display: inline-block;margin: 0 14px;"><img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/twitter.png" style="display: inline-block;padding-top: 2px;" alt="facebook"></a>
                    <a href="https://www.facebook.com/mariasplacecom/" style="display: inline-block;margin: 0 14px;"><img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/facebook.png" style="display: inline-block;padding-top: 2px;" alt="twitter"></a>
                    <a href="https://www.pinterest.com/seniorsactivities/_saved/" style="display: inline-block;margin: 0 13px;"><img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/pinterest.png" style="display: inline-block;padding-top: 2px;" alt="instagram"></a>
                    <a href="https://www.linkedin.com/company/maria%27s-place" style="display: inline-block;margin: 0 13px;"><img src="<?php echo get_template_directory_uri(); ?>/woocommerce/emails/images/linkedin.png" style="display: inline-block;padding-top: 2px;" alt="instagram"></a>
                </p>
                
                <table style="width: 100%;">
                    <tr>
                        <td align="center" style="color: #fff;padding-bottom: 22px !important;">
                           <?php echo wp_kses_post( wpautop( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ); ?>
                        </td>
                    </tr>
                </table>
            </td>   
        </tr>
    </table>
</body>
</html>