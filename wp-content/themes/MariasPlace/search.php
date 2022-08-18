<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WP_Bootstrap_Starter
 */
get_header();
?>
    <section id="primary" class="content-area container archive">
        <div id="main" class="site-main" role="main">
            <?php if (have_posts()) : ?>

                <header class="page-header">
                    <h1 class="section-main-title mb-4"><?php printf(esc_html__('Search Results for: %s ', 'MariasPlace'), '<span>' . get_search_query() . '</span>'); ?></h1>
                </header><!-- .page-header -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="grid-container grid-3">
                            <?php
                            /* Start the Loop */
                            while (have_posts()) : the_post();

                                /*
                                 * Include the Post-Format-specific template for the content.
                                 * If you want to override this in a child theme, then include a file
                                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                                 */
//                            get_template_part('template-parts/content', get_post_format());
                                get_template_part('template-parts/content', 'loop');
                            endwhile;
                            ?>
                        </div>
                    </div><!--  ROW -->
                </div>
                <?php
                if (function_exists("themes_pagination")) {
                    echo themes_pagination($wp_query->max_num_pages);
                } else {
                    
                }
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </div><!-- #main -->
    </section><!-- #primary -->   
    <div class="col-12 col-md-12 py-5 mx-auto">
        <h1 class="page-title section-main-title text-center my-3"><?php esc_html_e('Free Activities', 'MariasPlace'); ?></h1>
        <?php echo do_shortcode('[recent_posts type="free" items="3" pagination="no"]'); ?>
    </div>
    <div class="col-12 col-md-12 py-5 mx-auto">
        <h1 class="page-title section-main-title text-center my-3"><?php esc_html_e('Latest Activities', 'MariasPlace'); ?></h1>
        <?php echo do_shortcode('[recent_posts type="paid" items="3" pagination="no"]'); ?>
    </div>
<?php
// get_sidebar();
get_footer();
