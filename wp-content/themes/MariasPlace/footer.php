<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */
?>

<?php if (!is_page_template('blank-page.php') && !is_page_template('blank-page-with-container.php')) : ?>
    <?php if(!is_product()) :?>
    </div><!-- .row -->
    </div><!-- .container -->
    <?php endif;?>
    </div><!-- #content -->
    <?php get_template_part('footer-widget'); ?>
    <footer id="colophon" class="site-footer bg-light">
        <div class="container pt-3 pb-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="site-info text-left">
                        &copy; <?php echo date('Y'); ?> <?php echo '<a href="' . home_url() . '">' . get_bloginfo('name') . '</a>'; ?>
                        <small class="sep"> | </small> 
                        <a class="credits" href="/terms-and-conditions/" target="_blank" title="Terms & conditions"><small>Terms & Conditions</small></a> 

                    </div><!-- close .site-info -->
                </div>
                <div class="col-md-6"></div>
            </div>

        </div>
    </footer><!-- #colophon -->
<?php endif; ?>
</div><!-- #page -->

<div id="Registration" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <h5 class="modal-title">Register</h5>
                <div class="wpb_text_column wpb_content_element ">
                    <div class="wpb_wrapper">
                        <p style="text-align: center;" class="mt-4">Registration is 100% Free.<br>
                        We believe everyone deserves access<br>
                        to activities and resources.</p>
                    </div>
                </div>
                <?php //echo do_shortcode('[pmpro_signup_artoon level="26" redirect="referrer"  submit_button="Join Us Now"]'); ?>
                <?php echo do_shortcode('[custom_registration_artoon level="26" redirect="referrer"  submit_button="Join Us Now" gcaptcha="signupcaptchadiv"]'); ?>
            </div>
        </div>
    </div>
</div>
<div id="Login" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <h3 class="modal-title">Login</h3>
                <?php echo do_shortcode('[loginform_single_page redirect="/welcome-back/"]'); ?>
            </div>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
<link rel="stylesheet" href="https://res.cloudinary.com/veseylab/raw/upload/v1613706377/magicmouse/magic-mouse-1.1.css" />
<script type="text/javascript" src="https://res.cloudinary.com/veseylab/raw/upload/v1613706377/magicmouse/magic_mouse-1.1.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#teamMemberBio").click(function () {
            jQuery('#teamMemberBio').modal('hide');
            jQuery('#magicMouseCursor').remove();
            jQuery('#magicPointer').remove();
            jQuery('body').attr("style", "cursor: auto");
        });
        jQuery(document).on('click', 'div[id^=team-]', function () {
            jQuery('#teamMemberBio .team_member_details .bio-inner .team-desc').text('');
            options = {
                "cursorOuter": "circle-basic",
                "hoverEffect": "circle-move",
                "hoverItemMove": false,
                "defaultCursor": false,
                "outerWidth": 75,
                "outerHeight": 75
            };
            magicMouse(options);
            jQuery('#magicMouseCursor').addClass('fa fa-times');
            var curentimg = jQuery(this).find("div").children("img");
            var curentposition = jQuery(this).find("span").text();
            var curentname = jQuery(this).find("h3").html();
            var curentid = jQuery(this).attr("id");
            var tid = curentid.split("-")[1];
            var templatedir = jQuery(this).find("div").text();
            var curentimg = jQuery(this).find("div.pic").html();
            var content = jQuery(this).find('div.templatedir').html();
            //console.log(content);
            jQuery('#teamMemberBio .team_member_details .bio-inner h1').text(curentname);
            jQuery('#teamMemberBio .team_member_details .bio-inner .title').text(curentposition);
            jQuery('#teamMemberBio div.team_member_picture div.team_member_image').empty();
            jQuery('#teamMemberBio div.team_member_picture div.team_member_image').append(curentimg);
            jQuery('#teamMemberBio .team_member_details .bio-inner .team-desc').html(content);
            /* jQuery.ajax({
             type: 'POST',
             url: '/wp-admin/admin-ajax.php',
             data: {
             'post_id': tid,
             'action': 'getPostcontentteam' //this is the name of the AJAX method called in WordPress
             }, success: function (data) {
             jQuery('#teamMemberBio .team_member_details .bio-inner .team-desc').text(data.replace(/^0+|0+$/g, ""));
             },
             error: function (e) {
             alert(e);
             }
             });*/
        });
    });
</script>
<script>
    jQuery(document).ready(function ($) {
        $(document).on('click', '.plus', function (e) {
            $input = $(this).prev('input.qty');
            var val = parseInt($input.val());
            var step = $input.attr('step');
            step = 'undefined' !== typeof (step) ? parseInt(step) : 1;
            $input.val(val + step).change();
        });

        $(document).on('click', '.minus', function (e) {
            $input = $(this).next('input.qty');
            var val = parseInt($input.val());
            var step = $input.attr('step');
            step = 'undefined' !== typeof (step) ? parseInt(step) : 1;
            if (val > 0) {
                $input.val(val - step).change();
            }
        });
        $(document).on('click', '.paid_feature_img,.paid_title,.paid_video,.paid', function (e) {
            $('form .redirect_to').val($(this).attr('data-url'));
        });
    });
</script>
<script type="text/javascript">
var CaptchaCallback = function() {
        grecaptcha.render('signupcaptchadiv', {'sitekey' : '6LdKTk8UAAAAAHl0AwIDcWAS9ZJNR0WBYYXX_TlI'});
        <?php  if(is_page('registration')) :?>
        grecaptcha.render('signupgcaptchareg', {'sitekey' : '6LdKTk8UAAAAAHl0AwIDcWAS9ZJNR0WBYYXX_TlI'});
        <?php endif; ?>
    };
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit" async defer></script>

<script type="text/javascript">
        
        jQuery(".register-form input[type='text'],.register-form  input[type='password'],.register-form  input[type='email']").on("focus", function () {
            jQuery('.register-form').find('input[type=submit]').removeAttr('disabled');
        });
        
        jQuery('.register-form').submit(function(e){
            //console.log(this);
            var __this = jQuery(this);
            e.preventDefault();
            var newUserName = jQuery(this).find('#username').val();
            var newUserEmail = jQuery(this).find('#bemail').val();
            var newUserFirstName = jQuery(this).find('#bfirstname').val();
            var newUserLastName = jQuery(this).find('#blastname').val();
            var newUserPassword = jQuery(this).find('#password').val();
            var newUserpassword2 = jQuery(this).find('#password2').val();
            if (jQuery('#newsletter2').is(':checked')) {
                var newsletter2 = jQuery(this).find('#newsletter2').val();
            }
            else
            {
                var newsletter2 = '';
            }
            var redirect_to = jQuery(this).find('.redirect_to').val();
            var flag = 0;
            // email validation 
            if(newUserEmail != ''){
                if(isEmail(newUserEmail)){
                    jQuery(this).find('.errorbemail').html('');
                    flag = 0;
                } else {
                    jQuery(this).find('.errorbemail').html('Please enter your valid email address.');
                    flag = 1;
                }
            } else {
                jQuery(this).find('.errorbemail').html('Please enter your email address.');
                flag = 1;
            }
            // Firstname Validation
            if(newUserFirstName != ''){
                jQuery(this).find('.errorbfirstname').html('');
                flag = 0;
            } else {
                jQuery(this).find('.errorbfirstname').html('Please enter your firstname.');
                flag = 1;
            }
            // Lastname Validation
            if(newUserLastName != ''){
                jQuery(this).find('.errorblastname').html('');
                flag = 0;
            } else {
                jQuery(this).find('.errorblastname').html('Please enter your lastname.');
                flag = 1;
            }
            // username validation
            if(newUserName != ''){
                jQuery(this).find('.errorusername').html('');
                flag = 0;
            } else {
                jQuery(this).find('.errorusername').html('Please enter your username.');
                flag = 1;
            }
            // password validation 
            if(newUserPassword != ''){
                jQuery(this).find('.errorpassword').html('');
                flag = 0;
            } else {
                jQuery(this).find('.errorpassword').html('Please enter your password');
                flag = 1;
            }
            if(newUserpassword2 != ''){
                jQuery(this).find('.errorpassword2').html('');
                flag = 0;
            } else {
                jQuery(this).find('.errorpassword2').html('Please enter your confirm password.');
                flag = 1;
            }
            if(newUserPassword != '' && newUserpassword2 != ''){
                if(newUserPassword == newUserpassword2){
                    jQuery(this).find('.errorpassword2').html('');
                    flag = 0;
                } else {
                    jQuery(this).find('.errorpassword2').html(‘Password and Confirm Password are not same');
                    flag = 1;
                } 
            }
            
            if(flag==0){

                if(jQuery('#Registration').hasClass('show')){
                    if (grecaptcha.getResponse(0) == ""){
                        flag = 1;
                        jQuery(this).find('.common_error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"> Try the CAPTCHA again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        jQuery('#Registration').animate({scrollTop:0}, '300');
                    } else {
                        flag = 0;
                    }
                } else{
                    if (grecaptcha.getResponse(1) == ""){
                        flag = 1;
                        jQuery(this).find('.common_error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"> Try the CAPTCHA again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        jQuery('#Registration').animate({scrollTop:0}, '300');
                    } else {
                        flag = 0;
                    }
                }
            }
            
            if(flag==0){
                jQuery(this).find('.loader').addClass('show');
                jQuery(this).find('input[type=submit]').attr('disabled', 'disabled');
                jQuery.ajax({
                  type:"POST",
                  dataType : "json",
                  url:"<?php echo admin_url('admin-ajax.php'); ?>",
                  data: {
                    action: "register_user_front_end",
                    new_user_name : newUserName,
                    new_user_email : newUserEmail,
                    new_first_name : newUserFirstName,
                    new_last_name : newUserLastName,
                    new_user_password : newUserPassword,
                    newsletter2 : newsletter2,
                    redirect_to : redirect_to,
                  },
                  success: function(results){
                    if(results.error){
                        // jQuery(this).find('.common_error').closest('.alert-danger').addClass('show');
                        __this.find('.common_error').html(results.message);
                        jQuery('#Registeration').animate({scrollTop:0}, '300');
                        __this.find('.loader').removeClass('show');
                        __this.find('input[type=submit]').removeAttr('disabled');
                    }
                    else
                    {
                        location.href = results.success;
                    }
                    
                  },
                  error: function(results) {

                  }
                });
            }
        });
        function isEmail(email) {
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
          return regex.test(email);
        }
    </script>
<span class="back-top"><i class="fa fa-angle-up"></i></span>
</body>
</html>