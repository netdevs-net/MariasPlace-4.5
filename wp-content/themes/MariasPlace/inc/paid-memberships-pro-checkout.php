<?php

function pmpro_signup_shortcode_artoon($atts, $content = null, $code = "") {

    global $current_user, $pmpro_level, $username, $email;

    // $atts    ::= array of attributes
    // $content ::= text within enclosing form of shortcode element
    // $code    ::= the shortcode found, when == callback name
    // examples: [pmpro_signup level="3" short="1" intro="0" submit_button="Signup Now"]
    //make sure PMPro is activated
    if (!function_exists('pmpro_getLevel'))
        return "Paid Memberships Pro must be installed to use the pmpro_signup shortcode.";

    //set defaults
    extract(shortcode_atts(array(
        'intro' => "0",
        'hidelabels' => NULL,
        'level' => NULL,
        'login' => true,
        'redirect' => NULL,
        'short' => NULL,
        'submit_button' => __("Sign Up Now", 'pmprosus'),
        'title' => NULL,
        'custom_fields' => true,
                    ), $atts));

    // If there is a current level in global, save it to a backup variable.
    $pmpro_level_backup = $pmpro_level;

    // try to get the Terms of Service page settings
    $tospage = pmpro_getOption('tospage');

    

    // treat this page load as a checkout
    add_filter('pmpro_is_checkout', '__return_true');

    // load recaptcha if needed
    if (!function_exists('pmpro_recaptcha_get_html')) {
        pmpro_init_recaptcha();
    }

    global $current_user, $membership_levels, $pmpro_pages;

    ob_start();
    ?>
    <?php if (!empty($current_user->ID) && pmpro_hasMembershipLevel($level, $current_user->ID)) { ?>
        <?php
        if (current_user_can("manage_options")) {
            ?>
            <div class="pmpro_message pmpro_alert"><?php _e('&#91;pmpro_signup&#93; Admin Only Shortcode Alert: You are logged in as an administrator and already have the membership level specified.', 'pmprosus'); ?></div>
            <?php
        }
        ?>
    <?php } else { ?>
       
        <?php
        $bemail_validation = email_exists( $_POST['bemail'] );
        $username_validation = sanitize_user( $_POST['username'] );
        if ( $bemail_validation ) {
        ?>
            <div id="pmpro_message" class="pmpro_message pmpro_error">
                That email address is already in use. <br>Please <a href="/login/" class="checkout-login1">log in</a>, or use a different email address.
                <br>
                <a href="/login/" class="checkout-login">login</a>
            </div>
        <?php
        }
        else if ( username_exists( $username_validation ) ) {
        ?>
            <div id="pmpro_message" class="pmpro_message pmpro_error">
                That username is already in use. <br>Please <a href="/login/" class="checkout-login1">log in</a>, or use a different username.
                <br>
                <a href="/login/" class="checkout-login">login</a>
            </div>
        <?php
        }
        else if($bemail_validation && username_exists( $username_validation )){
            ?>
            <div id="pmpro_message" class="pmpro_message pmpro_error">
                That email address and username is already in use. <br>Please <a href="/login/" class="checkout-login1">log in</a>, or use a different email address and username.
                <br>
                <a href="/login/" class="checkout-login">login</a>
            </div>
            <?php
        }
        ?>

        <form id="pmpro_form" class="pmpro_form pmpro_signup_form<?php if (!empty($hidelabels)) { ?> pmpro_signup_form-hidelabels<?php } ?>" action="<?php echo pmpro_url("checkout"); ?>" method="post">
        <?php
        if (!empty($title))
            echo '<h2>' . $title . '</h2>';
        ?>
            <?php
            if (!empty($intro))
                echo wpautop($intro);
            ?>
            <div class="pmpro_checkout">
                <div class="pmpro_checkout-fields">
                    <input type="hidden" id="level" name="level" value="<?php echo $level; ?>" />
                    <input type="hidden" id="pmpro_signup_shortcode" name="pmpro_signup_shortcode" value="1" />
        <?php do_action('pmpro_signup_form_before_fields'); ?>

                    <?php if (!empty($current_user->ID)) { ?>
                        <p id="pmpro_account_loggedin">
                        <?php printf(__('You are logged in as <strong>%s</strong>. If you would like to use a different account for this membership, <a href="%s">log out now</a>.', 'pmprosus'), $current_user->user_login, wp_logout_url($_SERVER['REQUEST_URI'])); ?>
                        </p>
                            <?php } else {
                            ?>
                        <div class="pmpro_checkout-field pmpro_checkout-field-bemail">
                        <?php echo do_shortcode('[nextend_social_login provider="facebook" redirect="https://dev.mariasplace.com/welcome-back/"]');?>
                        </div>
                        <div class="pmpro_checkout-field pmpro_checkout-field-bemail">
                            <p class="text-center vs_login_text my-3 my-sm-4">or register below</p>
                        </div>
                        <div class="pmpro_checkout-field pmpro_checkout-field-bemail">
                            <label for="bemail"><?php _e('E-mail Address', 'pmprosus'); ?></label>
                            <input id="bemail" name="bemail" required type="email" class="form-control" size="30" value="<?php echo esc_attr($bemail); ?>" <?php if (!empty($hidelabels)) { ?> placeholder="<?php _e('E-mail Address', 'pmprosus'); ?>"<?php } ?> />
                        </div>

            <?php if (!empty($short)) { ?>
                            <input type="hidden" name="bconfirmemail_copy" value="1" />
                        <?php } else { ?>
                            <div class="pmpro_checkout-field pmpro_checkout-field-bconfirmemail">
                                <label for="bconfirmemail"><?php _e('Confirm E-mail', 'pmprosus'); ?></label>
                                <input id="bconfirmemail" required name="bconfirmemail" type="email" class="form-control" size="30" value="" <?php if (!empty($hidelabels)) { ?>placeholder="<?php _e('Confirm E-mail', 'pmprosus'); ?>"<?php } ?> />
                            </div>
            <?php } ?>

                        <?php if ($short !== 'emailonly') { ?>
                            <div class="pmpro_checkout-field pmpro_checkout-field-username">
                                <label for="username"><?php _e('Username', 'pmprosus'); ?></label>
                                <input id="username" required name="username" type="text" class="form-control" size="30" value="<?php echo esc_attr($username); ?>" <?php if (!empty($hidelabels)) { ?>placeholder="<?php _e('Username', 'pmprosus'); ?>"<?php } ?> />
                            </div>
            <?php } ?>

                        <?php if (!empty($custom_fields)) {
                            do_action("pmpro_checkout_after_username");
                        } ?>

                        <?php if ($short !== 'emailonly') { ?>
                            <div class="pmpro_checkout-field pmpro_checkout-field-password">
                                <label for="password"><?php _e('Password', 'pmprosus'); ?></label>
                                <input id="password" required name="password" type="password" class="form-control" size="30" value="" <?php if (!empty($hidelabels)) { ?> placeholder="<?php _e('Password', 'pmprosus'); ?>"<?php } ?> />
                            </div>
                        <?php } ?>

                        <?php if (!empty($short)) { ?>
                            <input type="hidden" name="password2_copy" value="1" />
                        <?php } else { ?>
                            <div class="pmpro_checkout-field pmpro_checkout-field-password2">
                                <label for="password2"><?php _e('Confirm Password', 'pmprosus'); ?></label>
                                <input id="password2" required name="password2" type="password" class="form-control" size="30" value="" <?php if (!empty($hidelabels)) { ?>placeholder="<?php _e('Confirm Password', 'pmprosus'); ?>"<?php } ?> />
                            </div>
                        <?php } ?>

                        <?php if (!empty($custom_fields)) {
                            do_action("pmpro_checkout_after_password");
                        } ?>

                        <div class="newsletter-block">
<!--                            <div class="vs_custom_checkbox">
                                <input name="_mc4wp_lists[]" required type="checkbox" value="02b7c738de" tabindex="8" id="newsletter1" />
                                <label class="check-boxmain" for="newsletter1">
                                     I would like to receive <b>monthly</b> Inspirational emails.
                                </label>
                            </div>-->
                            <div class="vs_custom_checkbox">
                                <input name="_mc4wp_lists[]" type="checkbox" value="11b1f92675" tabindex="9" id="newsletter2" />
                                <label class="check-boxmain" for="newsletter2">
                                    I would like to receive <b>weekly</b> Activity suggestions email.
                                </label>
                            </div>    
                        </div>

                        <input type="hidden" name="pmprosus_referrer" value="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" />

                        <?php
                        if ($redirect == 'referrer') {
                            $redirect_to = $_SERVER['REQUEST_URI'];
                        } elseif ($redirect == 'account') {
                            $redirect_to = get_permalink($pmpro_pages['account']);
                        } elseif (empty($redirect)) {
                            $redirect_to = '';
                        } else {
                            $redirect_to = $redirect;
                        }
                        ?>

                        <input type="hidden" name="redirect_to" class="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />

            <?php if (!empty($custom_fields)) {
                do_action("pmpro_checkout_after_email");
            } ?>

                        <div class="pmpro_hidden">
                            <label for="fullname"><?php _e('Full Name', 'pmprosus'); ?></label>
                            <input id="fullname" name="fullname" type="text" class="form-control" size="30" value="" /> <strong><?php _e('LEAVE THIS BLANK', 'pmprosus'); ?></strong>
                        </div>

                            <?php
                            global $recaptcha, $recaptcha_publickey;
                            if ($recaptcha == 2 || (!empty($level) && $recaptcha == 1 && pmpro_isLevelFree(pmpro_getLevel($level)) )) {
                                ?>
                            <div class="pmpro_checkout-field pmpro_captcha">
                            <?php echo pmpro_recaptcha_get_html($recaptcha_publickey, NULL, true); ?>
                            </div> <!-- end pmpro_captcha -->
                        <?php } ?>

                    <?php } ?>

                    <?php do_action('pmpro_checkout_after_user_fields'); ?>

                    <?php if ($checkout_boxes && function_exists('pmprorh_pmpro_checkout_boxes')) {
                        pmprorh_pmpro_checkout_boxes();
                    } ?>

                    <?php
                    if (!empty($tospage)) {
                        $tospage = get_post($tospage);
                        ?>
                        <input type="checkbox" name="tos" value="1" id="tos" /> <label class="pmpro_label-inline pmpro_clickable" for="tos"><?php printf(__('I agree to the %s', 'paid-memberships-pro'), $tospage->post_title); ?></label>
            <?php }
        ?>

        <?php if (!empty($custom_fields)) {
            do_action('pmpro_signup_form_before_submit');
        } ?>

                    <div class="pmpro_submit">
                        <span id="pmpro_submit_span">
                            <input type="hidden" name="submit-checkout" value="1" />
                            <input id="pmpro_btn-submit" type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="<?php echo $submit_button; ?>" />
                        </span>
                    </div>
        <?php do_action('pmpro_signup_form_after_submit'); ?>
        <?php if (!empty($login) && empty($current_user->ID)) { ?>
                        <div class="login-link" style="text-align:center;">
                            <h4 class="vs_have_account">Already have account? <a href="#" data-toggle="modal" data-target="#Login"><?php _e('Login here', 'pmprosus'); ?></a></h4>  
                        </div>
        <?php } ?>
                </div> <!-- end pmpro_checkout-fields -->
            </div> <!-- end pmpro_checkout -->
        </form>
        <?php do_action('pmpro_signup_form_after_form'); ?>
    <?php } ?>
    <?php
    $temp_content = ob_get_contents();
    ob_end_clean();

    // Set the global level back to the correct object.
    $pmpro_level = $pmpro_level_backup;

    return $temp_content;
}

add_shortcode('pmpro_signup_artoon', 'pmpro_signup_shortcode_artoon');


function wordpress_custom_registration_popup($atts){
    
    global $current_user, $pmpro_level, $username, $email;

    extract(shortcode_atts(array(
        'intro' => "0",
        'hidelabels' => NULL,
        'level' => NULL,
        'login' => true,
        'redirect' => NULL,
        'short' => NULL,
        'submit_button' => __("Sign Up Now", 'pmprosus'),
        'title' => NULL,
        'custom_fields' => true,
        'gcaptcha' => NULL,
    ), $atts));
    
    add_filter('pmpro_is_checkout', '__return_true');

    if (!function_exists('pmpro_recaptcha_get_html')) {
        pmpro_init_recaptcha();
    }

    ob_start();
    
    ?>
    <form action="#" method="POST" name="register-form" id="pmpro_form" class="register-form pmpro_form pmpro_signup_form">
        <div class="pmpro_checkout">
            <div class="pmpro_checkout-fields">
                <input type="hidden" id="level" name="level" value="<?php echo $level;?>">
                <input type="hidden" id="pmpro_signup_shortcode" name="pmpro_signup_shortcode" value="1" />
                <?php do_action('pmpro_signup_form_before_fields'); ?>
                <div class="common_error"></div>
                
                <div class="pmpro_checkout-field pmpro_checkout-field-bemail">
                    <?php echo do_shortcode('[nextend_social_login provider="facebook" redirect="https://dev.mariasplace.com/welcome-back/"]');?>
                </div>
                <div class="pmpro_checkout-field pmpro_checkout-field-bemail">
                    <p class="text-center vs_login_text my-3 my-sm-4">or register below</p>
                </div>
                <div class="pmpro_checkout-field pmpro_checkout-field-bemail">
                    <label for="bemail">E-mail Address</label>
                    <input id="bemail" name="bemail"  type="text" class="form-control" size="30" value="">
                    <span class="errorbemail"></span>
                </div>
                <div class="row mb-0">
                    <div class="pmpro_checkout-field pmpro_checkout-field-bfirstname col-md-6 pl-md-0 pr-md-3 px-0">
                        <label for="bconfirmemail">First Name</label>
                        <input id="bfirstname"  name="bfirstname" type="text" class="form-control" size="30" value="">
                        <span class="errorbfirstname"></span>
                    </div>
                    <div class="pmpro_checkout-field pmpro_checkout-field-blastname col-md-6 pr-md-0 pl-md-3 px-0 mb-4">
                        <label for="bconfirmemail">Last Name</label>
                        <input id="blastname"  name="blastname" type="text" class="form-control" size="30" value="">
                        <span class="errorblastname"></span>
                    </div>
                </div>
                <div class="pmpro_checkout-field pmpro_checkout-field-username">
                    <label for="username">Username</label>
                    <input id="username"  name="username" type="text" class="form-control" size="30" value="">
                    <span class="errorusername"></span>
                </div>
                <div class="pmpro_checkout-field pmpro_checkout-field-password">
                    <label for="password">Password</label>
                    <input id="password"  name="password" type="password" class="form-control" size="30" value="">
                    <span class="errorpassword"></span>
                </div>
                <div class="pmpro_checkout-field pmpro_checkout-field-password2">
                    <label for="password2">Confirm Password</label>
                    <input id="password2"  name="password2" type="password" class="form-control" size="30" value="">
                    <span class="errorpassword2"></span>
                </div>
                <div class="newsletter-block">          
                    <div class="vs_custom_checkbox">
                        <input name="_mc4wp_lists[]" type="checkbox" value="11b1f92675" tabindex="9" id="newsletter2">
                        <label class="check-boxmain" for="newsletter2">
                            I would like to receive <b>weekly</b> Activity suggestions email.
                        </label>
                    </div>    
                </div>
                <input type="hidden" name="pmprosus_referrer" value="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" />
                <?php
                if ($redirect == 'referrer') {
                    $redirect_to = $_SERVER['REQUEST_URI'];
                } elseif ($redirect == 'account') {
                    $redirect_to = get_permalink($pmpro_pages['account']);
                } elseif (empty($redirect)) {
                    $redirect_to = '';
                } else {
                    $redirect_to = $redirect;
                }
                ?>

                <input type="hidden" name="redirect_to" class="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
               
                <?php do_action('pmpro_checkout_after_user_fields'); ?>
                
                <div id="<?php echo $gcaptcha;?>"></div>

                <div class="pmpro_submit">
                    <span id="pmpro_submit_span">
                        <input type="hidden" name="submit-checkout" value="1"><input type="hidden" name="javascriptok" value="1">
                        <input id="pmpro_btn-submit" type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="Join Us Now">
                    </span>
                    <div class="loader"></div>
                </div>
                <div class="login-link" style="text-align:center;">
                    <h4 class="vs_have_account">Already have account? <a href="#" data-toggle="modal" data-target="#Login">Login here</a></h4>  
                </div>
            </div>
            
        </div>
    </form>

    <?php
    $temp_content = ob_get_contents();
    ob_end_clean();
    return  $temp_content;
}
add_shortcode('custom_registration_artoon','wordpress_custom_registration_popup');

add_action('wp_ajax_register_user_front_end', 'register_user_front_end', 0);
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');
function register_user_front_end() {
      ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
      $new_user_name = stripcslashes($_POST['new_user_name']);
      $new_user_email = stripcslashes($_POST['new_user_email']);
      $new_user_password = $_POST['new_user_password'];
      $user_nice_name = strtolower($_POST['new_user_email']);
      $new_first_name = $_POST['new_first_name'];
      $new_last_name = $_POST['new_last_name'];
      $redirect_to = $_POST['redirect_to'];
      $user_data = array(
          'user_login' => $new_user_name,
          'user_email' => $new_user_email,
          'user_pass' => $new_user_password,
          'user_nicename' => $user_nice_name,
          'first_name' => $new_first_name,
          'last_name' => $new_last_name,
          'display_name' => $new_user_name,
          'role' => 'subscriber'
        );
        $results = array();
        $user_id = wp_insert_user($user_data);

        if (!is_wp_error($user_id)) {
            $results['error'] =  false;
            $results['success'] = $redirect_to;
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            $user = get_user_by('id', $user_id );
            
            if(!empty($_POST['newsletter2'])){
                $subscription = rudr_mailchimp_subscribe_unsubscribe( $user->user_email, 'subscribed', array( 'FNAME' => $user->first_name,'LNAME' => $user->last_name ),$_POST['newsletter2']);  
            }

        } else {
            if (isset($user_id->errors['empty_user_login'])) {
              $notice_key = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    User Name and Email are mandatory
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
                $results['error'] =  true;
                $results['message'] =  $notice_key;
            } elseif (isset($user_id->errors['existing_user_login'])) {
               $results['message'] ='<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Sorry, that username already exists!
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
               $results['error'] =  true;
            }elseif(isset($user_id->errors['existing_user_email'])){
                $results['message'] ='<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Sorry, that email address is already used!
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
                $results['error'] =  true;
            } 
            else {
               $results['message'] =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error Occured please fill up the sign up form carefully.
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
               $results['error'] =  true;
            }
        }
        echo json_encode($results);
    die;
}

function rudr_mailchimp_subscribe_unsubscribe( $email, $status, $merge_fields = array( 'FNAME' => '', 'LNAME' => '' ),$list_id ){
    /* 
     * please provide the values of the following variables 
     * do not know where to get them? read above
     */
    $api_key = '12eec455433a2ca14d39d4bfe63d1e36-us11';
    $list_id = $list_id;
    
    /* MailChimp API URL */
    $url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($email));
    /* MailChimp POST data */
    $data = array(
        'apikey'        => $api_key,
            'email_address' => $email,
        'status'        => $status, // in this post we will use only 'subscribed' and 'unsubscribed'
        'merge_fields'  => $merge_fields // in this post we will use only FNAME and LNAME
    );
    return json_decode( rudr_mailchimp_curl_connect( $url, 'PUT', $api_key, $data ) );
}

function rudr_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
    if( $request_type == 'GET' )
        $url .= '?' . http_build_query($data);
    
    $mch = curl_init();
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Basic '.base64_encode( 'user:'. $api_key )
    );
    curl_setopt($mch, CURLOPT_URL, $url );
    curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
    curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
    curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
    curl_setopt($mch, CURLOPT_TIMEOUT, 10);
    curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
    
    if( $request_type != 'GET' ) {
        curl_setopt($mch, CURLOPT_POST, true);
        curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
    }
 
    return curl_exec($mch);
}
?>