<?php
global $current_user;
global $play_video;

// FREE MEMBERSHIP LEVEL IDs ARRAY
$freememberplanids = array(18, 26, 29, 31, 36);
?>
<?php
if (has_post_thumbnail()) {
    $feaure_img_url = get_the_post_thumbnail_url();
} else {
    $feaure_img_url = get_template_directory_uri() . '/inc/assets/images/default-feature.png';
}
$bg_img = 'style="background:url(' . $feaure_img_url . ');" ';
// Checking Post Format
$post_format = get_post_format();
//Check Feature Image
if (has_post_thumbnail()) {
//    $feature_image = get_the_post_thumbnail(get_the_id(), 'full', array('sizes' => '(min-width:320px) 20rem, (min-width:768px) 30rem, (min-width:1024px) 100rem'), array('class' => 'img-fluid d-none'));
    $feature_image_url = get_the_post_thumbnail_url(get_the_id(), 'full');
  
} else {
    $feature_image = '<img width="600" height="435" src="' . get_template_directory_uri() . '/inc/assets/images/default-feature.png' . '" class="d-none attachment-large size-large wp-post-image" alt="'.get_the_id().'">';
    $feature_image_url =  get_template_directory_uri() . '/inc/assets/images/default-feature.png';
}
 $featureImageOverlay =' <span class="img-overlay" style="background-image:url('.$feature_image_url.');background-size: cover;background-position: center;"></span>';
// Check if user is Logged In
if (is_user_logged_in()) {
    $um_id = $current_user->membership_level->ID;
} else {
    $um_id = 0;
}
$free_content = get_post_meta( get_the_ID(), 'free_content', true );
$post_type = get_post_type( get_the_ID() );

if($free_content == 1 || $post_type != 'post'){
    $memberPlan = '<span class="membership-status free d-none"><span class="back"></span></span>';
    $post_title = '<h4><a class="post-item-title" href="' . get_permalink(get_the_id()) . '" rel="bookmark">' . get_the_title() . '</a></h4>';
    $featureImage = '<a href="' . get_permalink(get_the_id()) . '" rel="bookmark">' . $featureImageOverlay . '</a>';
    $play_video = '<div class="video-play-button"><a href="' . get_the_permalink(get_the_id()) . '" class="fa fa-play"></a></div>';
} 
else {
    if (!$um_id || $um_id == 0) {
        $memberPlan = '<span class="membership-status paid" data-url="'.get_the_permalink().'"><i class="fa fa-lock" data-toggle="modal" data-target="#Registeration"></i><span class="back"></span></span>';
        $post_title = '<h4 class="post-item-title paid_title" data-url="'.get_the_permalink().'" data-toggle="modal" data-target="#Registeration">' . get_the_title() . '</h4>';
        $featureImage = '<span class="paid_feature_img" data-url="'.get_the_permalink().'" data-toggle="modal" data-target="#Registeration">' . $featureImageOverlay . '</span>';
        $play_video = '<div class="video-play-button paid_video" data-url="'.get_the_permalink().'"><i class="fa fa-play" data-toggle="modal" data-target="#Registeration"></i></div>';
    } else {
        if (in_array($um_id, $freememberplanids)) {
//            $memberPlan = '<span class="membership-status alreadypaid"><i class="fa fa-unlock" data-toggle="modal" data-target="#Registeration"></i><span class="back"></span></span>';
            $post_title = '<h4><a class="post-item-title" href="' . get_permalink(get_the_id()) . '" rel="bookmark">' . get_the_title() . '</a></h4>';
            $featureImage = '<a href="' . get_permalink(get_the_id()) . '" rel="bookmark">' . $featureImageOverlay . '</a>';
        }
    }
}
?>

<div class="grid-item loop-items">
    <div class="post-item h-100 <?= $post_format; ?>">
        <div class="post-item-body">
            <div class="post-thumbnail mb-4">
                <?php
                if ($post_format == 'video') {
                    echo $play_video;
                }
                if (!empty($memberPlan)) {
                    echo $memberPlan;
                }
                ?>
                <?php
                echo $featureImage;
                ?>
            </div>
            <div class="post-item-content mb-4">
                <?php
                echo $post_title;
                if (!empty(get_field('blurb', get_the_id()))) {
                    $post_content = get_field('blurb', get_the_id());
                    echo '<p class="post-item-text mb-0">' . wp_trim_words($post_content, 15, '') . '</p>'; 
                }
                ?>
            </div>
        </div>
    </div>
</div>