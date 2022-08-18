<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WP_Bootstrap_Starter
 */
get_header();
?>

<section id="primary" class="content-area col-sm-12 col-lg-12">
    <div id="main" class="site-main" role="main">

        <section class="error-404 not-found text-center mb-4">
            <header class="page-header">
                <h1 class="page-title section-main-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'MariasPlace'); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content section-sub-title col-12 col-md-10 mx-auto">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'MariasPlace'); ?></p>
                <div class="col-12 col-md-10 mx-auto">
                    <?php get_search_form(); ?>
                </div>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->

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
//get_sidebar();
get_footer();
