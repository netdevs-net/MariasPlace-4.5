<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */
get_header();
?>

<section id="primary" class="content-area col-sm-12 col-md-12 col-lg-12">
    <div id="main" class="site-main" role="main">

        <?php
//        pre($post);
        $type = $post->post_mime_type;
        $url = $post->guid;
        if (($type == 'image/jpeg' || $type == 'image/png') || ($type == 'image/jpg' || $type == 'image/gif')) {
            $content = wp_get_attachment_image($post->ID, 'full');
        }else{
            $content = '<div class="wpb_video_widget wpb_content_element vc_clearfix   vc_video-aspect-ratio-169 vc_video-el-width-100 vc_video-align-left" ><div class="wpb_wrapper"><div class="wpb_video_wrapper"><iframe src="'.$url.'"></iframe></div></div></div>';
        }
        echo '<div class="post-item-header text-center my-4"><h1 class="section-main-title">' . $post->post_title . '</h1></div>';
        echo '<div class="col-12 text-center">'.$content.'</div>';
        ?>
    </div><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
