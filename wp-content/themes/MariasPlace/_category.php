<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
get_header();
global $wp_query;


$term = get_queried_object();
$children = get_terms( $term->taxonomy, array(
'parent'    => $term->term_id,
'hide_empty' => false
) );
// print_r($children); // uncomment to examine for debugging

//pre($children);
?>
<section id="primary" class="content-area category-page col-sm-12 col-lg-12">
    <div id="main" class="site-main" role="main">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <?php
                the_archive_title('<h1 class="page-title section-main-title">', '</h1>');
                the_archive_description('<div class="archive-description section-sub-title col-12 col-md-10 mx-auto"><div class="more mb-5">', '</div></div>');
                ?>
            </header><!-- .page-header -->
            <div class="col-md-12">
                <div class="row">
                    <div class="grid-container grid-3">
                        <?php
                        if (!empty($children)) {
                            echo category_has_children($term->term_id);
                        } else {
                            /* Start the Loop */
                            while (have_posts()) : the_post();

                                /*
                                 * Include the Post-Format-specific template for the content.
                                 * If you want to override this in a child theme, then include a file
                                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                                 */
//                            get_template_part('template-parts/content', get_post_format());
//        pre(get_post_meta(get_the_id()));
                                get_template_part('template-parts/content', 'loop');
                            endwhile;
                        }
                        ?>
                    </div>
                </div><!--  ROW -->
            </div>

            <?php
            if (empty($children)) {
                if (function_exists("themes_pagination")) {
                    echo themes_pagination($wp_query->max_num_pages);
                }
            } else {
                
            }
        else :
            get_template_part('template-parts/content', 'none');

        endif;
        ?>

    </div><!-- #main -->
</section><!-- #primary -->

<?php
// get_sidebar();
get_footer();
