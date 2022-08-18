<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php
    // You can start editing here -- including this comment!
    if (have_comments()) :
        ?>

        <h2 class="comments-title">
            <?php
			wp_reset_query();
			$comments_number = wp_count_comments(get_the_ID())->approved;
			//print_r($comments_number);
			//$comments_number = get_comments_number(get_the_ID());
            if ('1' === $comments_number) {
                 echo $comments_number.' thought on &ldquo;'.esc_html(get_the_title()).'&rdquo;';
            } else {
                echo $comments_number.' thoughts on &ldquo;'.esc_html(get_the_title()).'&rdquo;';
            }
            ?>
        </h2><!-- .comments-title -->


        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through?  ?>
                <nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
                    <h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'MariasPlace'); ?></h2>
                    <div class="nav-links">
                        <div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'MariasPlace')); ?></div>
                        <div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'MariasPlace')); ?></div>

                    </div><!-- .nav-links -->
                </nav><!-- #comment-nav-above -->
        <?php endif; // Check for comment navigation.  ?>

            <ul class="comment-list">
            <?php
            wp_list_comments(array('callback' => 'wp_bootstrap_starter_comment', 'avatar_size' => 50));
            ?>
            </ul><!-- .comment-list -->

                <?php if ($comments_number  > 1 && get_option('page_comments')) : // Are there comments to navigate through? ?>
                <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
                    <h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'MariasPlace'); ?></h2>
                    <div class="nav-links">

                        <div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'MariasPlace')); ?></div>
                        <div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'MariasPlace')); ?></div>

                    </div><!-- .nav-links -->
                </nav><!-- #comment-nav-below -->
            <?php
        endif; // Check for comment navigation.

    endif; // Check for have_comments().
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( ! comments_open() ) : 
    ?>

        <p class="no-comments"><?php esc_html_e('Comments are closed.', 'MariasPlace'); ?></p>
   	<?php endif; ?>

    <?php
	$args = array(
        'id_form' => 'commentform', // that's the wordpress default value! delete it or edit it ;)
        'id_submit' => 'commentsubmit',
        'title_reply' => __('Leave a Comment', 'MariasPlace'), // that's the wordpress default value! delete it or edit it ;)
        /* translators: 1: Reply Specific User */
        'title_reply_to' => __('Leave a Comment to %s', 'MariasPlace'), // that's the wordpress default value! delete it or edit it ;)
        'cancel_reply_link' => __('Cancel Reply', 'MariasPlace'), // that's the wordpress default value! delete it or edit it ;)
        'label_submit' => __('Post Comment', 'MariasPlace'), // that's the wordpress default value! delete it or edit it ;)
        'comment_field' => '<p><textarea placeholder="Start typing..." id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
        'comment_notes_after' => '<p class="form-allowed-tags d-none">' .
        __('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:', 'MariasPlace') .
        '</p>'

            // So, that was the needed stuff to have bootstrap basic styles for the form elements and buttons
            // Basically you can edit everything here!
            // Checkout the docs for more: http://codex.wordpress.org/Function_Reference/comment_form
            // Another note: some classes are added in the bootstrap-wp.js - ckeck from line 1
    );
	 comment_form($args);
    ?>

</div><!-- #comments -->
