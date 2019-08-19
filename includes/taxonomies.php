<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2016-03-18
 * Time: 4:00 PM
 */

add_action( 'init', 'create_taxonomy' );

function create_taxonomy() {

	// Default labels used in our custom taxonomies. No need to personalize the labels for each type..
	$labels = array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'all_items' => __( 'All Categories' ),
		'edit_item' => __( 'Edit Category' ),
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'menu_name' => __( 'Categories' )
	);

	// Taxonomy for post type news
	register_taxonomy( 'news_category', 'news', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_in_rest' => true,
	));

	// Taxonomy for post type newsletters
	register_taxonomy( 'newsletter_category', 'newsletters', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_in_rest' => true,
	));

	// Taxonomy for post type profiles
	register_taxonomy( 'profiles_category', 'profiles', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_in_rest' => true,
	));

	register_taxonomy( 'page_shortcode_taxonomy', 'page', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => _x( 'Shortcode Taxonomy', 'taxonomy general name'),
			'menu_name' => __('Shortcode Taxonomy')
		),
		'show_in_rest' => true,
	));


}

//------------------------------------------------------------Show custom taxonomies in edit window
add_filter( 'manage_posts_columns', 'govid_columns' );
function govid_columns( $defaults ) {
	$defaults['news_category'] = __( 'Category' );
	return $defaults;
}

add_action( 'manage_posts_custom_column', 'govid_custom_column', 10, 2 );
function govid_custom_column( $column_name, $post_id ) {
	global $wpdb;
	if( 'news_category' == $column_name ) {
		$tags = get_the_terms( $post->ID, 'news_category' );

		if ( !empty( $tags ) ) {
			$out = array();

			foreach ( $tags as $c ) {
				$out[] = "<a href='edit.php?cat=$c->slug'> ".esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'news_category', 'display' ) )."</a>";
				echo join( ', ', $out );
			}
		} else {
			_e( 'Not Categorized' );
		}
	} else {
		echo '<i>'.__( 'None' ).'</i>';
	}
}
