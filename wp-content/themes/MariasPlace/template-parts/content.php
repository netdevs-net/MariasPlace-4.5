<?php
global $current_user;

// FREE MEMBERSHIP LEVEL IDs ARRAY
$freememberplanids = array(18, 26, 29, 31, 36);
?>
<?php
// Checking Post Format
$post_format = get_post_format();
// Check if user is Logged In
if (is_user_logged_in()) {
    $um_id = $current_user->membership_level->ID;
} else {
    $um_id = 0;
}
$category_detail = get_the_category(get_the_id()); //$post->ID
foreach ($category_detail as $cd) {
    $free_category = $cd->slug;
}
$free_content = get_post_meta(get_the_ID(), 'free_content', true);

//if ($free_category == 'free' || $free_category == 'time-out-articles' || $free_content == 1) {
//Check Feature Image
if (has_post_thumbnail()) {
//    $feature_image = get_the_post_thumbnail(get_the_id(), 'full', array('sizes' => '(min-width:320px) 20rem, (min-width:768px) 30rem, (min-width:1024px) 100rem'), array('class' => 'img-fluid'));
    $feature_image_url = get_the_post_thumbnail_url(get_the_id(), 'full');
    $feature_image = '<div class="post-thumbnail mb-4"><div class="img-overlay" style="background-image:url(' . $feature_image_url . ');background-size: contain;background-position: center;background-repeat: no-repeat;background-color: rgba(50,40,118, 0.33);"></div></div>';
} else {
    $feature_image_url = get_template_directory_uri() . '/inc/assets/images/default-feature.png';
    $feature_image = '<div class="post-thumbnail mb-4"><div class="img-overlay" style="background-image:url(' . $feature_image_url . ');background-size: contain;background-position: center;min-height:450px;background-repeat: no-repeat;background-color: rgba(50,40,118, 0.33);"></div></div>';
//    $feature_image = '<img width="1200" height="900" src="' . get_template_directory_uri() . '/inc/assets/images/default-feature.png' . '" class="attachment-large size-large wp-post-image" alt="' . get_the_id() . '">';
}

$html_out = '';
if ($post_format == 'video') {

    $video_option = get_field('video_option', get_the_id());
    if ($video_option == 'Youtube' || $video_option == 'youtube') {
        $youtube_id = get_field('youtube_id', get_the_id());
        $VideoIframe = '<iframe class="video-player" src="https://www.youtube.com/embed/' . $youtube_id . '" allowfullscreen></iframe>';
    }
    if ($video_option == 'Vimeo' || $video_option == 'vimeo') {
        $vimeo_id = get_field('vimeo_id', get_the_id());
        $VideoIframe = '<iframe class="video-player" src="https://player.vimeo.com/video/' . $vimeo_id . '" allowfullscreen></iframe>';
    }

    if (!empty($memberPlan)) {
        //echo ' <div class="post-thumbnail h-auto mb-4">'.$memberPlan.'</div>';
    }


    //echo '<div class="video-play-button"><i class="fa fa-play"></i></div>';
} else {
    $VideoIframe = $feature_image;
}
//CHECK IF PDF IS ATTCHED
if (!empty(get_field('pdf_url', get_the_id()))) {
        $PDFbutton = '<div class="w-100 d-block mt-2 mb-5"><a class="btn btn-lg btn-navyblue" href="' . str_replace('/preview', '/edit', get_field('pdf_url', get_the_id())) . '" target="_blank">Click here to Print</a></div>';
        $pdf_Iframe = '<div class="col-12 py-5 mx-auto mb-5 bg-light"><div class="col-12 col-md-8 text-center mx-auto post-pdf-iframe"><iframe id="printf" class="pdf-iframe" src="' . get_field('pdf_url', get_the_id()) . '?usp=drivesdk" title="' . get_field('pdf_url', get_the_id()) . '"></iframe>' . $PDFbutton . '</div></div>';
    } else {
        $pdf_Iframe = '';
    }

if (isset($_GET['social']) || isset($_GET['referrer']) || isset($_GET['fbclid'])) {
    $flag = 1;
}
//custom code to allow specific urls with params
//if gogole bot then allow
if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot")) {
    $flag = 1;
}

//allow user only from these sites
$domains_allowed = array("facebook.com", "google.co.in", "google.com", "pinterest.com", "instagram.com", "linkedin.com", "twitter.com", "t.co", "bing.com", "yahoo.com", "aarp.org", "alz.org");
//$domains_allowed = array("");
$pieces = parse_url($_SERVER['HTTP_REFERER']);
$domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{0,63}\.[a-z\.]{1,6})$/i', $domain, $regs)) {
    if (in_array($regs['domain'], $domains_allowed)) {
        $flag = 1;
    }
}

$post_type = get_post_type(get_the_ID());

//if ($free_content == 1 || $flag == 1 || $post_type != 'post') {
if ($free_content == 1 || $flag == 1 || $post_type != 'post') {
    $html_out .= '<div class="wpb_video_widget wpb_content_element vc_clearfix   vc_video-aspect-ratio-169 vc_video-el-width-100 vc_video-align-left" ><div class="wpb_wrapper"><div class="wpb_video_wrapper">';
    $html_out .= $VideoIframe;
    $html_out .= '</div></div></div>';
    
   
    $html_out .= '<div class="post-item-content">';
    $html_out .= '<div class="post-inner-content col-12 p-0">';
    $html_out .= '<div class="row additional-details">';

    $suppliers_or_short = get_field('suppliers_or_short', get_the_id());
    if ($suppliers_or_short == 'Supplies' && !empty(get_field('supply_list', get_the_id()))) {
        $html_out .= '<div class="col-md-6 supply-list pb-4"> ';
        $html_out .= '<h3 class="additional-details-header">Supply List:</h3>';
        $html_out .= '<div class="supply-list-content">';
        if (have_rows('supply_list')):
            $html_out .= '<ul class="list-styled">';
            while (have_rows('supply_list')) : the_row();
                $sub_value = get_sub_field('list_item');
                $html_out .= '<li>' . $sub_value . '</li>';
            endwhile;
            $html_out .= '</ul>';
        else :
        endif;
        $html_out .= '</div>';
        $html_out .= '</div>';
    }
    elseif ($suppliers_or_short == 'Short Description' && !empty(get_field('short_description', get_the_id()))) {
        $html_out .= '<div class="col-md-6 short-description pb-4"> ';
        $html_out .= '<h3 class="additional-details-header">Short Description:</h3>';
        $html_out .= '<div class="supply-list-content">';
        $html_out .= get_field('short_description', get_the_id());
        $html_out .= '</div>';
        $html_out .= '</div>';
    }
    if (!empty(get_field('1-on-1', get_the_id())) || !empty(get_field('group', get_the_id())) || !empty(get_field('alzheimers_dementia', get_the_id()))) {
        $html_out .= '<div class="col-md-6 pb-4"><h3 class="additional-details-header">Directions for Caregivers:</h3>';
        $html_out .= '<div class="col-md-12 p-0  pb-4">';
        $html_out .= '<div class="accordion" id="directionForCaregivers">';
        $options = array('1-on-1' => '1 on 1', 'group' => 'Group', 'alzheimers_dementia' => "Alzheimer's & Dementia");
        foreach ($options as $key => $value) {
            $option = get_field($key, get_the_id());
            if ($option[0] == 'Yes' || $option[0] == 'yes' || $option[0] == 'YES') {
                $html_out .= '<div class="card">';
                $html_out .= '<div class="card-header bg-white p-3" id="' . $key . '">';
                $html_out .= '<h2 class="mb-0">';
                $html_out .= '<button class="btn btn-link-navyblue btn-block text-left p-0 collapsed" type="button" data-toggle="collapse" data-target="#' . $key . '_description" aria-expanded="true" aria-controls="' . $key . '_description">';
                $html_out .= $value;
                $html_out .= '</button>';
                $html_out .= '</h2>';
                $html_out .= '</div>';
                $html_out .= '<div id="' . $key . '_description" class="collapse" aria-labelledby="' . $key . '" data-parent="#directionForCaregivers">';
                $html_out .= '<div class="card-body">';
                $html_out .= get_field($key . '_description', get_the_id());
                $html_out .= '</div>';
                $html_out .= '</div>';
                $html_out .= '</div>';
            }
        }
        $html_out .= '</div>';
        $html_out .= '</div>';
        $html_out .= '</div>';
    }
    $html_out .= '</div>';
    $html_out .= '</div>';
    $post_content = apply_filters('the_content', get_the_content());
            $html_out .= $pdf_Iframe;
    if (!empty(get_the_content())) {
        $html_out .= '<div class="col-12 p-0 text-center">';
        $html_out .= '<h3 class="additional-details-header">About this Activity</h3>';
        $html_out .= '</div>';
    }

    $html_out .= '<div class="col-12 p-0 post-content-text">';
    if (!empty(get_field('force_pin_image', get_the_id()))) {
        $ptClass= ' with-pinterest-image';
        $html_out .= '<div class="row ww">';
        if (!empty(get_the_content())) {
            $html_out .= '<div class="col-md-12 post-content-text">';
            $html_out .= do_shortcode(wpautop(get_the_content(), $ignore_html = false));
            $html_out .= '</div>';
            $html_out .= '<div class="col-md-4 post-pin-image mx-auto">';
            $html_out .= '<img src="' . get_field('force_pin_image', get_the_id()) . '" alt="' . get_the_title() . '" class="img-fluid" />';
            $html_out .= '</div>';
        } else {
            $html_out .= '<div class="col-md-4 post-pin-image mx-auto">';
            $html_out .= '<img src="' . get_field('force_pin_image', get_the_id()) . '" alt="' . get_the_title() . '" class="img-fluid" />';
            $html_out .= '</div>';
        }
        $html_out .= '</div>';
    } else {
          $ptClass = ' without-pinterest-image';
        $html_out .= '<div class="col-12 col-md-10 mx-auto p-0 post-content-text'.$ptClass.'">';
        $html_out .= do_shortcode(wpautop(get_the_content(), $ignore_html = false));
        $html_out .= '</div>';
    }
    $html_out .= '</div>';
} else {
    if (!$um_id || $um_id == 0) {

        $html_out .= '<div class="post-thumbnail mt-5 locked-content" data-toggle="modal" data-target="#Registeration">';
        $html_out .= '<div class="post-item-header text-center">';
        $html_out .= '<h1 class="section-main-title mb-3">' . get_the_title() . '</h1>';
        if (!empty(get_field('heading_1', get_the_id()))) {
            $html_out .= '<div class="col-12 col-md-10 mx-auto mb-3"><div class="font-36">' . get_field('heading_1', get_the_id()) . '</div></div>';
        }
        if (!empty(get_field('heading_2', get_the_id()))) {
            $html_out .= '<div class="col-12 col-md-10 mx-auto mb-3 d-none"><div class="font-24">' . get_field('heading_2', get_the_id()) . '</div></div>';
        }
        $html_out .= '<i class="fa fa-lock"></i>';
        $html_out .= '</div>';
        $html_out .= '</div>';
        $html_out .= '<div id="lock-msg">';
        $html_out .= '<i class="fa fa-exclamation-circle fa-lg mr-1 mr-md-3"></i> Login or register to view this content and more!';
        $html_out .= '</div>';
        $html_out .= '<div id="locked-content" class="post-item-content locked-content mb-5">';
        $html_out .= '<div class="row no-gutters">';
        $html_out .= '<div id="col-login" class="col-md-6">';
        $html_out .= '<p class="text-center font-36 text-white my-3">Login</p>';
        $html_out .= do_shortcode('[loginform redirect="' . get_permalink(get_the_id()) . '"]');
        $html_out .= '</div>';
        $html_out .= '<div id="col-register" class="col-md-6">';
        $html_out .= '<p class="text-center font-36 text-white my-3">Register</p>';
        $html_out .= '<p class="text-center font-24 text-white mb-4">Unlock the unlimited access of our content, resources, and join the community!</p>';
        $html_out .= '<div class="row d-flex justify-content-center text-center text-white mb-3">';
        $html_out .= '<div class="col-4 px-3">';
        $html_out .= '<strong class="font-36 mb-3">400+</strong>';
        $html_out .= '<p class="font-24 mb-3">Contents</p>';
        $html_out .= '</div>';
        $html_out .= '<div class="col-4 px-3">';
        $html_out .= '<strong class="font-36 mb-3">30+</strong>';
        $html_out .= '<p class="font-24 mb-3">Booklet</p>';
        $html_out .= '</div>';
        $html_out .= '</div>';
        $html_out .= '<div class="row d-flex justify-content-center text-center text-white">';
        $html_out .= '<div class="col-12 px-3">';
        $html_out .= '<a id="locked-btn-register" href="/registration/?level=26">Register for Free Membership</a>';
        $html_out .= ' </div>';
        $html_out .= '</div>';
        $html_out .= '</div>';
        $html_out .= '</div>';
        $html_out .= '</div>';
    } else {
        if (in_array($um_id, $freememberplanids)) {
            $html_out .= '<div class="wpb_video_widget wpb_content_element vc_clearfix   vc_video-aspect-ratio-169 vc_video-el-width-100 vc_video-align-left" ><div class="wpb_wrapper"><div class="wpb_video_wrapper">';
            $html_out .= $VideoIframe;
            $html_out .= '</div></div></div>';
            $html_out .= '<div class="post-item-content 2">';
         
            $html_out .= '<div class="post-inner-content col-12 p-0">';
            $html_out .= '<div class="row additional-details">';

            $suppliers_or_short = get_field('suppliers_or_short', get_the_id());
//            SUPPLY LIST
            $supply_list = '';
            if (!empty(get_field('supply_list', get_the_id()))) {
                $supply_list .= '<div class="col-md-6 supply-list pb-4"> ';
                $supply_list .= '<h3 class="additional-details-header">Supply List:</h3>';
                $supply_list .= '<div class="supply-list-content">';
                if (have_rows('supply_list')):
                    $supply_list .= '<ul class="list-styled">';
                    while (have_rows('supply_list')) : the_row();
                        $sub_value = get_sub_field('list_item');
                        $supply_list .= '<li>' . $sub_value . '</li>';
                    endwhile;
                    $supply_list .= '</ul>';
                else :
                endif;
                $supply_list .= '</div>';
                $supply_list .= '</div>';
            }
//            SUPPLY LIST
            if ($suppliers_or_short == 'Supplies') {
                $html_out .= $supply_list;
            }
//            SHORT DESCRIPTION
            $short_description = '';
            if (!empty(get_field('short_description', get_the_id()))) {
                $short_description .= '<div class="col-md-6 short-description pb-4 pr-md-3"> ';
                $short_description .= '<h3 class="additional-details-header">Short Description:</h3>';
                $short_description .= '<div class="supply-list-content">';
                $short_description .= get_field('short_description', get_the_id());
                $short_description .= '</div>';
                $short_description .= '</div>';
            }
//            SHORT DESCRIPTION
            if ($suppliers_or_short == 'Short Description') {
                $html_out .= $short_description;
            }

            $dfc_out = '';
            if (!empty(get_field('1-on-1', get_the_id())) || !empty(get_field('group', get_the_id())) || !empty(get_field('alzheimers_dementia', get_the_id()))) {
                $dfc_out .= '<div class="col-md-6 pb-4"><h3 class="additional-details-header">Directions for Caregivers:</h3>';
                $dfc_out .= '<div class="col-md-12 p-0  pb-4">';
                $dfc_out .= '<div class="accordion" id="directionForCaregivers">';
                $options = array('1-on-1' => '1 on 1', 'group' => 'Group', 'alzheimers_dementia' => "Alzheimer's & Dementia");
                foreach ($options as $key => $value) {
                    $option = get_field($key, get_the_id());
                    if ($option[0] == 'Yes' || $option[0] == 'yes' || $option[0] == 'YES') {
                        $dfc_out .= '<div class="card">';
                        $dfc_out .= '<div class="card-header bg-white p-3" id="' . $key . '">';
                        $dfc_out .= '<h2 class="mb-0">';
                        $dfc_out .= '<button class="btn btn-link-navyblue btn-block text-left p-0" type="button" data-toggle="collapse" data-target="#' . $key . '_description" aria-expanded="true" aria-controls="' . $key . '_description">';
                        $dfc_out .= $value;
                        $dfc_out .= '</button>';
                        $dfc_out .= '</h2>';
                        $dfc_out .= '</div>';
                        $dfc_out .= '<div id="' . $key . '_description" class="collapse" aria-labelledby="' . $key . '" data-parent="#directionForCaregivers">';
                        $dfc_out .= '<div class="card-body">';
                        $dfc_out .= get_field($key . '_description', get_the_id());
                        $dfc_out .= '</div>';
                        $dfc_out .= '</div>';
                        $dfc_out .= '</div>';
                    }
                }

                $dfc_out .= '</div>';
                $dfc_out .= '</div>';
                $dfc_out .= '</div>';
            }
            $html_out .= $dfc_out;

            $html_out .= '</div>';
            $html_out .= '</div>';
            $html_out .= $pdf_Iframe;
            $post_content = apply_filters('the_content', get_the_content());
            if (!empty(get_the_content())) {
                $html_out .= '<div class="col-12 p-0 text-center">';
                $html_out .= '<h3 class="additional-details-header">About this Activity</h3>';
                $html_out .= '</div>';
            }

            if (!empty(get_field('force_pin_image', get_the_id()))) {
                $html_out .= '<div class="row">';
                if (!empty(get_the_content())) {
                    $html_out .= '<div class="col-md-12 col-lg-12 post-content-text IMHERE">';
                    $html_out .= do_shortcode(wpautop(get_the_content(), $ignore_html = false));
                    $html_out .= '</div>';
                    $html_out .= '<div class="col-md-6 col-lg-4 post-pin-image mt-2 mb-4 mx-auto">';
                    $html_out .= '<img src="' . get_field('force_pin_image', get_the_id()) . '" alt="' . get_the_title() . '" class="img-fluid" />';
                    $html_out .= '</div>';
                } else {
                    $html_out .= '<div class="col-md-6 col-lg-4 post-pin-image mx-auto">';
                    $html_out .= '<img src="' . get_field('force_pin_image', get_the_id()) . '" alt="' . get_the_title() . '" class="img-fluid" />';
                    $html_out .= '</div>';
                }
                $html_out .= '</div>';
            } else {
                $html_out .= '<div class="col-12 col-md-10 mx-auto p-0 post-content-text">';
                $html_out .= do_shortcode(wpautop(get_the_content(), $ignore_html = false));
                $html_out .= '</div>';
            }

            $html_out .= '</div>';
        }
    }
}
?>


<div class="grid-item single-post-item">
    <div class="post-item h-100 <?= $post_format; ?>">
        <?php
        echo '<div class="post-item-header text-center my-4">';
        echo '<h1 class="section-main-title">' . get_the_title() . '</h1>';
        if (!empty(get_field('heading_1', get_the_id()))) {
            echo '<div class="col-12 col-md-10 mx-auto"><div class="font-36">' . get_field('heading_1', get_the_id()) . '</div></div>';
        }
        if (!empty(get_field('heading_2', get_the_id()))) {
            echo '<div class="col-12 col-md-10 mx-auto"><div class="font-24">' . get_field('heading_2', get_the_id()) . '</div></div>';
        }
        echo '</div>';
        //echo sharethis_inline_buttons();
        ?>
        <div class="post-item-body">
            <?php echo $html_out; ?>
        </div>
    </div>
</div>