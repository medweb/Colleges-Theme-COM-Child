<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2016-03-18
 * Time: 4:00 PM
 */

add_action( 'init', 'create_post_type' );

function create_post_type() {
	if ( get_current_blog_id() == 1 ) {
		register_main_site_post_types();
	}
}
function register_main_site_post_types() {
	
	// News articles
	register_post_type( 'news',
        array(
            'labels'      => array(
                'name'          => __( 'News' ),
                'singular_name' => __( 'News Article' ),
                'add_new'       => __( 'Add News Article' ),
                'add_new_item'  => __( 'Add News Article' ),
                'edit_item'     => __( 'Edit Article' )
            ),
            'supports'    => array( 'title', 'editor', 'thumbnail', 'revisions' ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array(
                'slug'       => 'news',
                'with_front' => false
            ),
            'menu_icon'   => get_bloginfo( 'template_directory' ) . '/images/admin/icon-news.png',
            'taxonomies' => array('post_tag')
        )
	);

	// Newsletter (email) - not listed on website @TODO: right?
	register_post_type( 'newsletters',
        array(
            'labels'      => array(
                'name'          => __( 'Newsletters' ),
                'singular_name' => __( 'Newsletter' ),
                'add_new'       => __( 'Add Newsletter' ),
                'add_new_item'  => __( 'Add Newsletter' ),
                'edit_item'     => __( 'Edit Newsletter' )
            ),
            'supports'    => array( 'title', 'editor', 'revisions' ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array(
                'slug'       => 'newsletters',
                'with_front' => false
            ),
            'menu_icon'   => get_bloginfo( 'template_directory' ) . '/images/admin/icon-news.png'
        )
	);

	// profile pages for people: staff, faculty, residents, etc
	register_post_type( 'profiles',
        array(
            'labels'      => array(
                'name'          => __( 'Profiles' ),
                'singular_name' => __( 'Profile' ),
                'add_new'       => __( 'Add Profile' ),
                'add_new_item'  => __( 'Add Profile' ),
                'edit_item'     => __( 'Edit Profile' )
            ),
            'supports'    => array( 'title', 'editor', 'revisions' ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array(
                'slug'       => 'directory',
                'with_front' => false
            ),
            'menu_icon'   => get_bloginfo( 'template_directory' ) . '/images/admin/icon-profiles.png'
        )
	);
}
