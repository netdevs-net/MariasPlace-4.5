<?php

// Register Custom Post Type
function custom_post_type() {

    $labels = array(
        'name' => _x('Testimonials', 'Post Type General Name', 'MariasPlace'),
        'singular_name' => _x('Testimonial', 'Post Type Singular Name', 'MariasPlace'),
        'menu_name' => __('Testimonials', 'MariasPlace'),
        'name_admin_bar' => __('Testimonials', 'MariasPlace'),
        'archives' => __('Item Archives', 'MariasPlace'),
        'attributes' => __('Item Attributes', 'MariasPlace'),
        'parent_item_colon' => __('Parent Testimonial:', 'MariasPlace'),
        'all_items' => __('All Testimonials', 'MariasPlace'),
        'add_new_item' => __('Add New Testimonial', 'MariasPlace'),
        'add_new' => __('Add New Testimonial', 'MariasPlace'),
        'new_item' => __('New Testimonial', 'MariasPlace'),
        'edit_item' => __('Edit Testimonial', 'MariasPlace'),
        'update_item' => __('Update Testimonial', 'MariasPlace'),
        'view_item' => __('View Testimonial', 'MariasPlace'),
        'view_items' => __('View Testimonials', 'MariasPlace'),
        'search_items' => __('Search Testimonial', 'MariasPlace'),
        'not_found' => __('Not found', 'MariasPlace'),
        'not_found_in_trash' => __('Not found in Trash', 'MariasPlace'),
        'featured_image' => __('Author Image', 'MariasPlace'),
        'set_featured_image' => __('Set Author image', 'MariasPlace'),
        'remove_featured_image' => __('Remove Author image', 'MariasPlace'),
        'use_featured_image' => __('Use as Author image', 'MariasPlace'),
        'insert_into_item' => __('Insert into Testimonial', 'MariasPlace'),
        'uploaded_to_this_item' => __('Uploaded to this testimonial', 'MariasPlace'),
        'items_list' => __('Testimonials list', 'MariasPlace'),
        'items_list_navigation' => __('Items list navigation', 'MariasPlace'),
        'filter_items_list' => __('Filter Testimonials list', 'MariasPlace'),
    );
    $args = array(
        'label' => __('Testimonial', 'MariasPlace'),
        'description' => __('Post Type Description', 'MariasPlace'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'taxonomies' => array('testimonials_cat'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-format-quote',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'query_var' => 'testimonial_query',
        'capability_type' => 'page',
    );
    register_post_type('testimonials', $args);
}

add_action('init', 'custom_post_type', 0);

/**
 * Taxonomy: Testimonials Categories.
 */
if (!function_exists('testimonials_category')) {

// Register Custom Taxonomy
    function testimonials_category() {

        $labels = array(
            'name' => _x('Testimonials Categories', 'Taxonomy General Name', 'text_domain'),
            'singular_name' => _x('Testimonials category', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name' => __('Testimonials Category', 'text_domain'),
            'all_items' => __('All Items', 'text_domain'),
            'parent_item' => __('Parent Item', 'text_domain'),
            'parent_item_colon' => __('Parent Item:', 'text_domain'),
            'new_item_name' => __('New Item Name', 'text_domain'),
            'add_new_item' => __('Add New Item', 'text_domain'),
            'edit_item' => __('Edit Item', 'text_domain'),
            'update_item' => __('Update Item', 'text_domain'),
            'view_item' => __('View Item', 'text_domain'),
            'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
            'add_or_remove_items' => __('Add or remove items', 'text_domain'),
            'choose_from_most_used' => __('Choose from the most used', 'text_domain'),
            'popular_items' => __('Popular Items', 'text_domain'),
            'search_items' => __('Search Items', 'text_domain'),
            'not_found' => __('Not Found', 'text_domain'),
            'no_terms' => __('No items', 'text_domain'),
            'items_list' => __('Items list', 'text_domain'),
            'items_list_navigation' => __('Items list navigation', 'text_domain'),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
//            'query_var' => 'Testimonials_Category_Query',
        );
        register_taxonomy('testimonials_cat', array('testimonials'), $args);
    }

    add_action('init', 'testimonials_category', 0);
}

// TESTIMONIAL SLIDER
// Add Shortcode [testimonials_slider category="review" arrow="yes" dots="yes" items="3"]
function testimonials_slider($atts) {
    // Attributes
    $atts = shortcode_atts(
            array(
        'category' => '',
        'quotes' => '',
        'arrow' => '',
        'dots' => '',
        'items' => '3',
            ), $atts
    );
    // WP_Query arguments
    $args = array(
        'post_type' => array('testimonial'),
        'order' => 'DESC',
        'posts_per_page' => $atts['items'],
        'orderby' => 'date',
    );
    if (!empty($atts['quotes'])) {
        $Quotes = ' no-quotes';
    } else {
        $Quotes = '';
    }
    if (!empty($atts['category'])) {
        $tax_query = array(
            array(
                'taxonomy' => 'testimonial-category',
                'field' => 'slug',
                'terms' => $atts['category']
            )
        );
        $args['tax_query'] = $tax_query;
    }
    $testimonial_query = new WP_Query($args);
    $html = '';

    // The Loop
    if ($testimonial_query->have_posts()) {
        $html .= '<div id="testimonialsSlider" class="carousel ss slide' . $Quotes . '" data-ride="carousel" data-interval="false"><div class="carousel-inner">';
        $index = 0;  
        $indicators .= '<ul class="carousel-indicators">';
        for ($i = 0; $i < count($testimonial_query->posts); $i++) {
            if ($i == 0) {
                $current_item = ' active';
            } else {
                $current_item = '';
            }
            $indicators .= '<li data-target="#testimonialsSlider" data-slide-to="' . $i . '" class="' . $current_item . '" aria-current="true" aria-label="Slide ' . $i . ' "></li>';
        }
        $indicators .= '</ul>';

        $arrow = '<div class="carousel-controls"><span class="carousel-control-prev" data-target="#testimonialsSlider" data-slide="prev">
              <i class="fa fa-angle-left"></i>
                </span>
              <span class="carousel-control-next" data-target="#testimonialsSlider" data-slide="next">
                  <i class="fa fa-angle-right"></i>
              </span>
              </div>';

        while ($testimonial_query->have_posts()) {
            $testimonial_query->the_post();
            if ($index == 0) {
                $current_item = ' active';
            } else {
                $current_item = '';
            }
            if (has_post_thumbnail()) {
                $feature_image = get_the_post_thumbnail(get_the_id(), 'full', array('sizes' => '(min-width:320px) 20rem, (min-width:768px) 30rem, (min-width:1024px) 100rem'), array('class' => 'img-fluid'));
            } else {
                $feature_image = '<img width="120" height="120" src="' . get_template_directory_uri() . '/inc/assets/images/review-author.jpg' . '" class="attachment-large size-large wp-post-image" alt="flower arrangement">';
            }
            $Name = get_post_meta(get_the_id(), '_testimonial_client');
            $job = get_post_meta(get_the_id(), '_testimonial_job');
            $company = get_post_meta(get_the_id(), '_testimonial_company');
            pre($company);
            if (!empty($job[0])) {
                $position = $job[0];
            } else {
                $position = '';
            }
            if (!empty($company[0])) {
                $company = $company[0];
            } else {
                $company = '';
            }
            if (!empty($position) && !empty($company)) {
                $sep = ', ';
            } else {
                $sep = '';
            }
            $content = '<div class="carousel-item' . $current_item . '">';
            $content .= '<div id="testimonial-' . get_the_id() . '" class="testimonial-item">';
//            $content .= '<div class="testimonial-body mb-3">' . wp_trim_words(get_the_content(), 80, '') . '</div>';
            $content .= '<div class="testimonial-body mb-3">' . get_the_content() . '</div>';
            $content .= '<div class="testimonial-footer font-weight-bold d-flex align-items-center h3 text-left mb-3"><span class="review-author-img d-inline-block mr-3">' . $feature_image . '</span><span class="d-inline-block review-author-meta"> -' . get_the_title() . '<small class="ml-2 d-block font-weight-normal author-position">' . $position .$sep. $company . '</small></span></div>';
            $content .= '</div>';
            $content .= '</div>';
            $html .= $content;
            $index++;
        }

        $html .= '</div>';
        if ($atts['arrow'] == 'yes' || $atts['arrow'] == 'Yes') {
            $html .= $arrow;
        }
        if ($atts['dots'] == 'yes' || $atts['dots'] == 'Yes') {
            $html .= $indicators;
        }

        $html .= '</div>';
    } else {
        // no posts found
    }

    // Restore original Post Data
    wp_reset_postdata();


    return $html;
}

//add_shortcode('testimonials_slider', 'testimonials_slider');
