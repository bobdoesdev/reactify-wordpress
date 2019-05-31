<?php
/**
 * Reactify
 *
 */

namespace Movies_Post_Type;

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


add_theme_support( 'post-thumbnails' ); 

//register post type: codex.wordpress.org/Function_Reference/register_post_type
add_action('init', __NAMESPACE__.'\create_post_type');
function create_post_type() {
	$labels = array(
		'name'               => _x( 'Movies', 'post type general name', 'dei' ),
		'singular_name'      => _x( 'Movie', 'post type singular name', 'dei' ),
		'menu_name'          => _x( 'Movies', 'admin menu', 'dei' ),
		'name_admin_bar'     => _x( 'Movie', 'add new on admin bar', 'dei' ),
		'add_new'            => _x( 'Add New', 'Movie', 'dei' ),
		'add_new_item'       => __( 'Add New Movie', 'dei' ),
		'new_item'           => __( 'New Movie', 'dei' ),
		'edit_item'          => __( 'Edit Movie', 'dei' ),
		'view_item'          => __( 'View Movie', 'dei' ),
		'all_items'          => __( 'All Movies', 'dei' ),
		'search_items'       => __( 'Search Movies', 'dei' ),
		'parent_item_colon'  => __( 'Parent Movies:', 'dei' ),
		'not_found'          => __( 'No Movies found.', 'dei' ),
		'not_found_in_trash' => __( 'No Movies found in Trash.', 'dei' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => false,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'movie' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 12,
		'menu_icon'			 => 'dashicons-images-alt2',
		'show_in_rest'       => true,
		'rest_base'          => 'movies',
		// 'rest_controller_class' => 'Slick_Movies_Post_REST_Controller',
		'supports'           => array( 'title', 'page-attributes', 'editor', 'thumbnail'),
	);

	register_post_type( 'movie', $args );
}


// // Add new taxonomy, make it hierarchical (like categories)
// add_action('init', __NAMESPACE__.'\create_post_taxonomies');
// function create_post_taxonomies() {
// 	$labels = array(
// 		'name'              => _x( 'Movie Category', 'taxonomy general name' ),
// 		'singular_name'     => _x( 'Movie Category', 'taxonomy singular name' ),
// 		'search_items'      => __( 'Search Movie Categories' ),
// 		'all_items'         => __( 'All Movie Categories' ),
// 		'parent_item'       => __( 'Parent Movie Category' ),
// 		'parent_item_colon' => __( 'Parent Movie Category:' ),
// 		'edit_item'         => __( 'Edit Movie Category' ),
// 		'update_item'       => __( 'Update Movie Category' ),
// 		'add_new_item'      => __( 'Add New Movie Category' ),
// 		'new_item_name'     => __( 'New Movie Category' ),
// 		'menu_name'         => __( 'Movie Categories' ),
// 	);

// 	$args = array(
// 		'hierarchical'      => true,
// 		'labels'            => $labels,
// 		'show_ui'           => true,
// 		'show_admin_column' => true,
// 		'show_in_nav_menus' => false,
// 		'query_var'         => true,
// 		'rewrite'           => array( 'slug' => 'Movie-category' ),
// 		'show_in_rest'       => true,
// 	  	'rest_base'          => 'Movie-category',
// 	  	'rest_controller_class' => 'WP_REST_Terms_Controller',
// 	);

// 	register_taxonomy( 'movie-category', array( 'movie' ), $args );
// }


//custom fields
add_action( 'add_meta_boxes_movie', __NAMESPACE__.'\genre_meta_box' );
function genre_meta_box() {  
	add_meta_box(    
		'genre_meta_box',   
		 __( 'Genre' ),    
		 __NAMESPACE__.'\genre_meta_box_callback',    
		 'movie',    
		 'side',    
		 'low'  
	);

}

function genre_meta_box_callback(){
	global $post;  
	$custom = get_post_custom($post->ID);  
	$genre = $custom["genre"][0];  
	echo '<input style="width:100%" name="genre" value="'.$genre.'" />';  
}

add_action( 'save_post', __NAMESPACE__.'\save_genre' );
function save_genre(){
	global $post;
	update_post_meta($post->ID, "genre", 
	$_POST["genre"]);
}



//register as rest field
add_action( 'rest_api_init', __NAMESPACE__.'\register_genre_as_rest_field' );
function register_genre_as_rest_field(){
	register_rest_field(
		'movie',
		'genre',
		array(
		  'get_callback' => __NAMESPACE__.'\get_genre_meta_field',
		  'update_callback' => null,
		  'schema' => null,
		)
	);
}

function get_genre_meta_field( $object, $field_name, $value ) {
	return get_post_meta($object['id'])[$field_name][0];
  };
