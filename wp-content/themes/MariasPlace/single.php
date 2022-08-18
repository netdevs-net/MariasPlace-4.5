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

<section id="primary" class="content-area container">
    <div id="main" class="site-main" role="main">

        <?php
        while (have_posts()) : the_post();
            get_template_part('template-parts/content', get_post_format());
        ?>
        <?php
            the_post_navigation();
            // If comments are open or we have at least one comment, load up the comment template.
            //if (comments_open() || get_comments_number()) :
            //comments_template();
            //endif;
        endwhile; // End of the loop.
        ?>
        <?php //echo do_shortcode('[site_reviews assigned_posts="'.get_the_id().'"]');
        ?>
        <?php //echo do_shortcode('[site_reviews_form assigned_posts="'.get_the_id().'" class="my-reviews-form full-width"]');
        ?>
    </div><!-- #main -->
</section><!-- #primary -->
<!-- REVIW SECTION -->
<section class="review-single col-12 px-0 py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
			<?php 
			if (comments_open() || get_comments_number()) :
					comments_template();
			endif;
			?>
			</div>
			<?php /*
            $tab_menu_items = '';
            $tab_content = '';
            $tab_menu = array('recent-reviews' => 'Recent Reviews', 'submit-review' => 'Submit Reviews', 'comments' => 'Thoughts');
            $i = 0;
            foreach ($tab_menu as $key => $value) {
                if ($i == 0) {
                    $class = ' class="nav-link active show"';
                } else {
                    $class = ' class="nav-link"';
                }
                $tab_menu_items .= '<li class="nav-item"><a' . $class . ' href="#' . $key . '" data-toggle="tab">' . $value . '</a></li>';
                // $tab_menu_items .= '<li class="nav-item"><button' . $class . ' id="' . $key . '-tab" data-bs-toggle="tab" data-bs-target="#' . $key . '" type="button" role="tab" aria-controls="' . $key . '">' . $value . '</button></li>';
                $i++;
            }
            ?>
            <div class="col-12">
                <ul class="nav nav-tabs" id="ReviewTab" role="tablist">
                    <?php echo $tab_menu_items; ?>
                </ul>
            </div>
            <div class="col-12 p-3">
                <div class="tab-content" id="ReviewTabContent">
                    <?php
                    $j = 0;
                    foreach ($tab_menu as $key => $value) {
                        if ($j == 0) {
                            $class = ' class="tab-pane fade show active"';
							wp_reset_query();
                            $review_list = do_shortcode('[site_reviews title="" assigned_posts="' . get_the_ID() . '" id="kqm39i7e" _hide="assigned_links,avatar,author,response"]');
                        } elseif ($j == 1) {
                            $class = ' class="tab-pane fade"';
							wp_reset_query();
                            $review_list = do_shortcode('[site_reviews_form title="" assigned_posts="' . get_the_ID() . '" class="submit_review_form" id="kpz86r8i" hide="name,terms"]');
                        } else {
                            $class = ' class="tab-pane fade"';
                            if (comments_open() || get_comments_number()) :
                                // comments_template();
                                ob_start();
                                comments_template();
                                $review_list = ob_get_contents();
								ob_end_clean();
                            // $review_list = '';
                            else :
                                $review_list = '';
                            endif;
                        }

                        //  $tab_content .= '<li' . $class . '><a href="#' . $key . '" data-toggle="tab">' . $value . '</a></li>';
                        $tab_content .= '<div' . $class . ' id="' . $key . '" role="tabpanel" aria-labelledby="' . $key . '-tab">' . $review_list . '</div>';

                        $j++;
                    }
                    ?>

                    <?php echo $tab_content; */ ?>


                </div>
            </div>

        </div>
    </div>
</section>
<!-- REVIW SECTION -->
<!-- <section class="review-single col-12 px-0 py-5">
    <div class="container">
        <div class="review-single-form py-5">
            <?php //echo do_shortcode('[site_reviews_form title="Submit a Review" assigned_posts="' . get_the_ID() . '" class="submit_review_form" id="kpz86r8i" hide="name,terms"]'); 
            ?>
        </div>
        <div class="review-single-list">
            <?php //echo do_shortcode('[site_reviews title="Recent Reviews" assigned_posts="' . get_the_ID() . '" id="kpz8itu6" hide="assigned_links,avatar,author,response"]'); 
            ?>
        </div>
    </div>
</section> -->

<?php
wp_reset_query();
echo do_shortcode('[other_craft post_id="' . get_the_ID() . '"]');
?>
<?php
wp_reset_query();
echo do_shortcode('[related_post post_id="' . get_the_ID() . '"]');
?>
<?php
// get_sidebar();
get_footer();
