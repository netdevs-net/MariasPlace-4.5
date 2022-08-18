<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title section-main-title mb-4"><?php esc_html_e('Nothing Found', 'MariasPlace'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content section-sub-title col-12 col-md-10 mx-auto text-center">
        <?php if (is_home() && current_user_can('publish_posts')) : ?>

            <p><?php printf(wp_kses(__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'MariasPlace'), array('a' => array('href' => array()))), esc_url(admin_url('post-new.php'))); ?></p>

        <?php elseif (is_search()) : ?>

            <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'MariasPlace'); ?></p>
            <div class="col-12 col-md-10 mx-auto">
                    <?php get_search_form(); ?>
                </div>
            <?php
        else :
            ?>

            <p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'MariasPlace'); ?></p>
            <div class="col-12 col-md-10 mx-auto">
                    <?php get_search_form(); ?>
                </div>
            <?php
        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->
