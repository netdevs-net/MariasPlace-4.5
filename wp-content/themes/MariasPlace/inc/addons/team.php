<?php
function custom_team_type() {

    $labels = array(
        'name' => _x('Team', 'Post Type General Name', 'MariasPlace'),
        'singular_name' => _x('team', 'Post Type Singular Name', 'MariasPlace'),
        'menu_name' => __('Team', 'MariasPlace'),
        'name_admin_bar' => __('Team', 'MariasPlace'),
        'archives' => __('Item Archives', 'MariasPlace'),
        'attributes' => __('Item Attributes', 'MariasPlace'),
        'parent_item_colon' => __('Parent Team:', 'MariasPlace'),
        'all_items' => __('All Team', 'MariasPlace'),
        'add_new_item' => __('Add New Team member', 'MariasPlace'),
        'add_new' => __('Add New Team member', 'MariasPlace'),
        'new_item' => __('New Team member', 'MariasPlace'),
        'edit_item' => __('Edit Team member', 'MariasPlace'),
        'update_item' => __('Update Team member', 'MariasPlace'),
        'view_item' => __('View Team member', 'MariasPlace'),
        'view_items' => __('View Team members', 'MariasPlace'),
        'search_items' => __('Search Team member', 'MariasPlace'),
        'not_found' => __('Not found', 'MariasPlace'),
        'not_found_in_trash' => __('Not found in Trash', 'MariasPlace'),
        'featured_image' => __('Team member Image', 'MariasPlace'),
        'set_featured_image' => __('Set Team member image', 'MariasPlace'),
        'remove_featured_image' => __('Remove Team image', 'MariasPlace'),
        'use_featured_image' => __('Use as Team image', 'MariasPlace'),
        'insert_into_item' => __('Insert into Team', 'MariasPlace'),
        'uploaded_to_this_item' => __('Uploaded to this Team', 'MariasPlace'),
        'items_list' => __('OurTeam list', 'MariasPlace'),
        'items_list_navigation' => __('Items list navigation', 'MariasPlace'),
        'filter_items_list' => __('Filter Team list', 'MariasPlace'),
    );
    $args = array(
        'label' => __('Team', 'MariasPlace'),
        'description' => __('Team Description', 'MariasPlace'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-image-filter',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'query_var' => 'team_query',
        'capability_type' => 'page',
    );
    register_post_type('team', $args);
}

add_action('init', 'custom_team_type', 0);

// TEAM GRID
// Add Shortcode [team_grid column="4" items="4"]
function team_grid($atts) {

    // Attributes
    $atts = shortcode_atts(
            array(      
        'column' => 4,
        'items' => 4,
            ), $atts
    );
    // WP_Query arguments
    $args = array(
        'post_type' => array('team'),
        'order' => 'ASC',
        'posts_per_page' => $atts['items'],
         'orderby' => 'date',
    );
    if($atts['column'] != 5 && $atts['column'] <= 6 ){
        $colclass = 12 / $atts['column'];
    }else{
        $colclass = 3;
    }
    // The Query
    $team_query = new WP_Query($args);

    $html = '';

    // The Loop
    if ($team_query->have_posts()) {

        $html .= '<div id="teamGrid" class="col-md-12"><div class="row">';
        $index = 0;
        while ($team_query->have_posts()) {
            $team_query->the_post();
            if ($index == 0) {
                $current_item = ' active';
            } else {
                $current_item = '';
            }
            if (!empty(get_field('position', get_the_id()))) {
                $position = get_field('position', get_the_id());
            }
            if ( has_post_thumbnail() ) {
                $feature_image = get_the_post_thumbnail(get_the_id(), 'full',array('sizes' => '(min-width:320px) 320px, (min-width:768px) 300px, (min-width:1024px) 350px'), array( 'class' => 'img-fluid' ) );
            }
            else {
                $feature_image = '<img width="1200" height="1500" src="' . get_template_directory_uri() . '/inc/assets/images/default-team.png' . '" class="attachment-large size-large wp-post-image" alt="flower arrangement">';    
            }
            $content ='<div class="col-md-'.$colclass.' col-sm-6">';
            $content .='<div data-toggle="modal" data-target="#teamMemberBio"><div id="team-'.get_the_id().'" class="our-team mb-5">';
            $content .='<div class="pic">'.$feature_image.'</div>';
            $content .='<div class="team-content">';
            $content .='<h3 class="title">' . get_the_title() . '</h3>';
            $content .='<span class="post">' . $position . '</span>';
            $content .='<div class="templatedir d-none">' . get_the_content() . '</div>';
            $content .='</div></div>';
            $content .='</div>'; // Our-Team Ends
            $content .='</div>';
            $html .= $content;
            $index++;
        }
       
        $html .= '</div>';
         
        $html .= '</div>';
    } else {
        // no posts found
    }

    // Restore original Post Data
    wp_reset_postdata();


    return $html.'<!-- Modal -->
<div class="modal fade right p-0 m-0" id="teamMemberBio" tabindex="-1" role="dialog" aria-labelledby="teamMemberBioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered momodel modal-fluid" role="document">
        <div class="modal-content ">
            <div class="modal-body p-0 m-0">
                <div class="container-fluid">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="team_member_details">
                           <div class="bio-inner">
                              <span class="mobile-close"></span>
                              <h1>Nichole Bontrager</h1>
                              <div class="title mt-2">Chief Executive Officer</div>
                              <div class="team-desc mt-4">
                                <p> Nichole Bontrager is here to serve and lead a life and company full of purpose. She strives to make a difference in the world and has a passion for the older generation.
                                 </p>
                                 <p>She is deep rooted in business and leadership and believes that combining business, purpose and impact makes for a better world.</p>
                                 <p>Nichole brings team members together who are passionate and are leaders in their fields. Together, they create a powerful force for good.</p>
                                 <p>Nichole lives in the mountains of Colorado with her two children, husband and lots of animals.</p>
                                 <div class="bottom_meta"></div>
                              </div>
                           </div>
                        </div>
                      </div>
                      <div class="col-md-6 bg-navyblue text-center">
                        <div class="team_member_picture">
                            <div class="team_member_image_bg_cover"></div>
                            <div class="team_member_picture_wrap">
                              <div class="team_member_image">
                                <img width="" height="" src="https://mpnewstaging.wpengine.com/wp-content/uploads/2021/04/Destia-Taylor.jpg" class="attachment-full size-full wp-post-image" alt="" loading="lazy" sizes="(min-width:320px) 320px, (min-width:768px) 300px, (min-width:1024px) 350px" srcset="https://mpnewstaging.wpengine.com/wp-content/uploads/2021/04/Destia-Taylor.jpg 1200w, https://mpnewstaging.wpengine.com/wp-content/uploads/2021/04/Destia-Taylor-600x750.jpg 600w, https://mpnewstaging.wpengine.com/wp-content/uploads/2021/04/Destia-Taylor-240x300.jpg 240w, https://mpnewstaging.wpengine.com/wp-content/uploads/2021/04/Destia-Taylor-819x1024.jpg 819w, https://mpnewstaging.wpengine.com/wp-content/uploads/2021/04/Destia-Taylor-768x960.jpg 768w">
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
}

add_shortcode('team_grid', 'team_grid');
