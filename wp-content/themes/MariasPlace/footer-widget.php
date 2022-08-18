<?php
wp_reset_query();
if (is_single() || is_product() || is_404() || is_search()) {
   do_shortcode('[get_social]') ; // Output Content
}
?>
<?php if (is_active_sidebar('footer-promotion')) : ?>
    <div id="footer-promotion" class="py-5 bg-light">
        <div class="container">
            <!--<div class="grid-container grid-3">-->
            <div class="col-md-9 mx-auto text-center">
                <?php dynamic_sidebar('footer-promotion'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) { ?>
    <div id="footer-widget" class="row m-0 py-0 <?php
    if (!is_theme_preset_active()) {
        echo 'bg-light';
    }
    ?>">
        <div class="container">
            <div class="row">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <div class="col-md-4 col-sm-6 col-12"><?php dynamic_sidebar('footer-1'); ?></div>
                <?php endif; ?>
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <div class="col-md-4 col-sm-6 col-12"><?php dynamic_sidebar('footer-2'); ?></div>
                <?php endif; ?>
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <div class="col-md-4 col-12"><?php dynamic_sidebar('footer-3'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
}
