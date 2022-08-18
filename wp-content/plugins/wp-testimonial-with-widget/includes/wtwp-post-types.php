<?php
/**
 * Function to create custom post type
 * 
 * @package WP Testimonials with rotator widget Pro
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to register post types
 * 
 * @since 1.0
 */
function wtwp_register_post_types() {

	$labels = array(
		'name' 					=> _x( 'SP Testimonials', 'post type general name', 'wp-testimonial-with-widget' ),
		'singular_name' 		=> _x( 'Testimonial', 'post type singular name', 'wp-testimonial-with-widget' ),
		'add_new' 				=> _x( 'Add New', 'testimonial', 'wp-testimonial-with-widget' ),
		'add_new_item' 			=> sprintf( __( 'Add New %s', 'wp-testimonial-with-widget' ), __( 'Testimonial', 'wp-testimonial-with-widget' ) ),
		'edit_item' 			=> sprintf( __( 'Edit %s', 'wp-testimonial-with-widget' ), __( 'Testimonial', 'wp-testimonial-with-widget' ) ),
		'new_item' 				=> sprintf( __( 'New %s', 'wp-testimonial-with-widget' ), __( 'Testimonial', 'wp-testimonial-with-widget' ) ),
		'all_items' 			=> sprintf( __( 'All %s', 'wp-testimonial-with-widget' ), __( 'Testimonials', 'wp-testimonial-with-widget' ) ),
		'view_item' 			=> sprintf( __( 'View %s', 'wp-testimonial-with-widget' ), __( 'Testimonial', 'wp-testimonial-with-widget' ) ),
		'search_items' 			=> sprintf( __( 'Search %s', 'wp-testimonial-with-widget' ), __( 'Testimonials', 'wp-testimonial-with-widget' ) ),
		'not_found' 			=> sprintf( __( 'No %s Found', 'wp-testimonial-with-widget' ), __( 'Testimonials', 'wp-testimonial-with-widget' ) ),
		'not_found_in_trash'	=> sprintf( __( 'No %s Found In Trash', 'wp-testimonial-with-widget' ), __( 'Testimonials', 'wp-testimonial-with-widget' ) ),
		'parent_item_colon' 	=> '',
		'menu_name'				=> __( 'WP Testimonials', 'wp-testimonial-with-widget' )
	);

	$single_slug	= apply_filters( 'sp_testimonials_single_slug', _x( 'testimonial', 'single post url slug', 'wp-testimonial-with-widget' ) );
	$archive_slug	= apply_filters( 'sp_testimonials_archive_slug', _x( 'wp_testimonial', 'post archive url slug', 'wp-testimonial-with-widget' ) );

	$args = array(
		'labels'				 => $labels,
		'public'		 		 => true,
		'publicly_queryable'	 => true,
		'show_ui' 				 => true,
		'show_in_menu'			 => true,
		'query_var' 			 => true,
		'hierarchical'			 => false,
		'capability_type'		 => 'post',
		'has_archive'			 => $archive_slug,
		'rewrite' 				 => array( 'slug' => $single_slug, 'with_front' => false ),
		'supports' 				 => array( 'title', 'author' ,'editor', 'thumbnail', 'page-attributes', 'publicize', 'wpcom-markdown' ),			
		'menu_icon' 			 => 'dashicons-format-quote'
	);

	register_post_type( 'testimonial', apply_filters( 'sp_testimonials_post_type_args', $args ) );
}
add_action( 'init', 'wtwp_register_post_types' );

/**
 * Function to register taxonomy
 * 
 * @since 1.0
 */
function wtwp_register_taxonomies() {

	$labels = array(
		'name'              => _x( 'Category', 'wp-testimonial-with-widget' ),
		'singular_name'     => _x( 'Category', 'wp-testimonial-with-widget' ),
		'search_items'      => __( 'Search Category', 'wp-testimonial-with-widget' ),
		'all_items'         => __( 'All Category', 'wp-testimonial-with-widget' ),
		'parent_item'       => __( 'Parent Category', 'wp-testimonial-with-widget' ),
		'parent_item_colon' => __( 'Parent Category', 'wp-testimonial-with-widget' ),
		'edit_item'         => __( 'Edit Category', 'wp-testimonial-with-widget' ),
		'update_item'       => __( 'Update Category', 'wp-testimonial-with-widget' ),
		'add_new_item'      => __( 'Add New Category', 'wp-testimonial-with-widget' ),
		'new_item_name'     => __( 'New Category Name', 'wp-testimonial-with-widget' ),
		'menu_name'         => __( 'Category', 'wp-testimonial-with-widget' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'testimonial-category' ),
	);

	register_taxonomy( 'testimonial-category', array( 'testimonial' ), $args );
}
add_action( 'init', 'wtwp_register_taxonomies' );