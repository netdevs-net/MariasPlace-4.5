<?php
/**
 * The header for our theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google-site-verification" content="ljrg0Hs8YWjQ8gWHlSBmtnhp2VZbVnNvWk-O7-vXJcU" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
		<script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=6263054c76e56100197a0b91&product=sticky-share-buttons' async='async'></script>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-98786755-2"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-98786755-2');
        </script>
        <!-- Facebook Pixel Code -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq)
                    return;
                n = f.fbq = function () {
                    n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq)
                    f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
<?= "fbq('init', '443317507087468');"; ?>
        </script>

        <!-- End Facebook Pixel Code -->
		 <!-- LuckyOrange Code -->
<script type='text/javascript'>
window.__lo_site_id = 82955;

	(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
		wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>
		<!-- LuckyOrange Code ENDS -->
		    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-98786755-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-98786755-2');
    </script>
		<!-- Global site tag (gtag.js) - Google Analytics ENDS -->
        <?php
// PIXEL STATNDARD EVENTS
        global $woocommerce, $order, $wp, $post;
        $PixelID = '443317507087468';
        $item_ID = $post->ID;
        $track_title = 'PageView';
        $content_name = get_the_title($item_ID);
        $content_type = 'product';
        $content_ids = array();
        $contents = array();
        $content_category = '';
        $currency = 'USD';
        $content_value = 0.25;
        $num_items = '';
        if (is_product()) {
            $_product = wc_get_product($item_ID);
            $content_value = $_product->get_price();
        } elseif (is_shop()) {
            $track_title = 'ViewContent';
            $content_name = 'Shop';
            $content_category = 'shop';
        } elseif (is_cart()) {
            $track_title = 'AddToCart';
            $content_category = 'cart';
            $content_value = $woocommerce->cart->total;
            $num_items = $woocommerce->cart->cart_contents_count;
        } elseif (is_wc_endpoint_url('order-received')) {
            $track_title = 'Purchase';
            $content_name = 'Order Confirmed';
            $content_category = 'Order';
            $order_id = intval(str_replace('checkout/order-received/', '', $wp->request));
            $order = new WC_Order($order_id);
            $content_value = $order->get_total();
            $num_items = $order->get_item_count();
        } elseif (is_checkout()) {
            $track_title = 'AddPaymentInfo';
            $content_category = 'checkout';
            $content_value = $woocommerce->cart->total;
            $num_items = $woocommerce->cart->cart_contents_count;
        } elseif (is_account_page()) {
            $content_category = 'account';
        } else {
            
        }
        if (!empty($num_items)) {
            $numitems = "num_items: '" . $num_items . "',";
        } else {
            $numitems = "";
        }
        if (!empty($content_category)) {
            $contentcategory = "content_category: '" . $content_category . "',";
        } else {
            $contentcategory = "";
        }
        if ($track_title == 'PageView') {
            echo "<script>fbq('track', '" . $track_title . "');</script>" . "\n";
        } else {
            echo "<script>fbq('track', '" . $track_title . "', {content_id:'" . $item_ID . "', content_name: '" . $content_name . "', content_type: '" . $content_type . "', " . $contentcategory . $numitems . "currency: '" . $currency . "', value: '" . $content_value . "' });</script>" . "\n";
        }
        ?>
    </head>
    <body <?php body_class(); ?>>
        <?php
        // WordPress 5.2 wp_body_open implementation
        if (function_exists('wp_body_open')) {
            wp_body_open();
        } else {
            do_action('wp_body_open');
        }
        ?>
        <div id="page" class="site">
            <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'MariasPlace'); ?></a>
            <?php if (!is_page_template('blank-page.php') && !is_page_template('blank-page-with-container.php')) : ?>
                <header id="masthead" class="site-header navbar-static-top <?php echo wp_bootstrap_starter_bg_class(); ?>">
                    <div class="container-fluid">
                        <nav class="navbar navbar-expand-lg p-0">
                            <div class="site-logo col-lg-3 col-md-4 col-sm-6 p-0 px-md-3">
                                <div class="navbar-brand m-0">
                                    <?php if (get_theme_mod('wp_bootstrap_starter_logo')) : ?>
                                        <a href="<?php echo esc_url(home_url('/')); ?>">
                                            <img width="238" height="72" src="<?php echo esc_url(get_theme_mod('wp_bootstrap_starter_logo')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                                        </a>
                                    <?php else : ?>
                                        <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_url(bloginfo('name')); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- LOGO -->
                            <div class="site-navigation col-lg-6">
                                <?php
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'container' => 'div',
                                    'container_id' => 'main-nav',
                                    'container_class' => 'collapse navbar-collapse justify-content-md-center',
                                    'menu_id' => false,
                                    'menu_class' => 'navbar-nav align-items-center',
                                    'depth' => 3,
//                                    'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
//                                    'walker' => new wp_bootstrap_navwalker()
                                ));
                                ?>
                            </div>
                            <!-- Navigation -->
                            <div id="mobile-widget" class="col-lg-3 col-md-8 col-sm-6 pl-0 text-right">
                                <button class="navbar-toggler pull-right" type="button" data-toggle="collapse" data-target="#mobile-navigation" aria-expanded="false" aria-label="Toggle navigation">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="navbar-nav pull-right">
                                    <?php
                                    $items = "";
                                    if (is_user_logged_in()) {
                                        $items .= '<li id="myaccount-menu" class="nav-item user-menu"><a class="nav-link" href="' . site_url() . '/my-account/    ">My Account</a></li>';
                                        $items .= '<li id="logout-menu" class="nav-item user-menu"><a class="nav-link" href="' . wp_logout_url(home_url()) . '">Logout</a></li>';
                                    } else {
                                        $items .= '<li id="login-menu" class="nav-item user-menu"><a _data-toggle="modal" _data-target="#Login"  class="nav-link" href="/login">Login</a></li>';
                                        $items .= '<li id="signup-menu" class="nav-item user-menu"><a _data-toggled="modal" _data-targetd="#Registeration" class="nav-link" href="/registration/">Sign up</a></li>';
                                    }
                                    if ($woocommerce) {
                                        $shopping_icon_url = get_template_directory_uri() . '/inc/assets/images/shopping-icon.png';
                                        $items .= '<li id="shopping-cart" class="nav-item user-menu"><a class="nav-link" id="mobile-cart-link" href="' . $woocommerce->cart->get_cart_url() . '"><img width="20" height="20" src="' . $shopping_icon_url . '" alt="Shopping Icon"/>';

                                        if ($woocommerce->cart->cart_contents_count > 0) {
                                            $items .= '<span class="cart-total-items">' . $woocommerce->cart->cart_contents_count . '</span>';
                                        }
                                        $items .= '</a></li>';
                                    }
                                    $search_icon_url = get_template_directory_uri() . '/inc/assets/images/search-icon.png';
                                    $items .= '<li id="mobile-search-item" class="nav-item user-menu"><img width="20" height="20" src="' . $search_icon_url . '" alt="Search Icon"/></li>';
                                    echo $items
                                    ?>
                                </ul>
                                <div class="search_form_div"><aside id="searchformWrap" class="d-none"><form action="<?php echo esc_url(home_url('/')); ?>" id="searchform" method="get"><input type="text" name="s" id="s" placeholder="Start Typing..."><em class="d-block">Press enter to begin your search</em></form></aside></div>
                            </div>

                        </nav>
                    </div>
                    <div id="mobile-navigation">
                        <i id="close-btn" class="fa fa-close"></i>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container' => 'div',
                            'container_id' => 'mobile-nav',
                            'container_class' => 'mobile-nav-bar',
                            'menu_id' => false,
                            'menu_class' => 'navbar-nav align-items-center',
                            'depth' => 0,
//                                    'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
//                                    'walker' => new wp_bootstrap_navwalker()
                        ));
                        ?> 
                    </div>
                </header><!-- #masthead -->
                <div id="content" class="site-content">
                    <?php
                    if (!is_attachment() && (!is_front_page() && !is_home())) {
                        echo custom_breadcrumbs();
                    }
                    ?>
                    <?php
                    if (get_page_template_slug(get_queried_object_id()) == 'fullwidth.php' || get_post_type(get_queried_object_id()) == 'post') {
                        $container = 'container-fluid';
                    } else {
                        $container = 'container';
                    }
                    if (!is_product()) {
                        ?>
                        <div class="<?= $container; ?>">
                            <div class="row">
                                <?php
                            }
                            ?>
                        <?php endif; ?>