<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2016-03-18
 * Time: 4:00 PM
 */

add_action( 'init', 'create_post_type' );

function create_post_type() {
	register_main_site_post_types(is_main_site());
	// if main site, show the post types.
	// otherwise, hide them (but still register them so it can query other sites)

}
function register_main_site_post_types($is_visible = true) {

	if ($is_visible){
		// hide from subsites, but still register the post type so plugins can query it.
		// main `init` hook doesn't fire on switch_to_blog, so if we only register these
		// post types on the main blog, they cease to exist for subsites even when they
		// try to switch_to_blog(1)
		$show_ui = true;
		$show_in_menu = true;
		$show_in_nav_menus = true;
		$show_in_admin_bar = true;
	} else {
		$show_ui = false;
		$show_in_menu = false;
		$show_in_nav_menus = false;
		$show_in_admin_bar = false;
	}

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
            'show_ui'     => $show_ui,
            'show_in_menu' => $show_in_menu,
            'show_in_nav_menus' => $show_in_nav_menus,
            'show_in_admin_bar' => $show_in_admin_bar,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite'     => array(
                'slug'       => 'news',
                'with_front' => false
            ),
            'menu_icon'   => get_stylesheet_directory_uri() . '/images/admin/icon-news.png',
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
            'show_ui'     => $show_ui,
            'show_in_menu' => $show_in_menu,
            'show_in_nav_menus' => $show_in_nav_menus,
            'show_in_admin_bar' => $show_in_admin_bar,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite'     => array(
                'slug'       => 'newsletters',
                'with_front' => false
            ),
            'menu_icon'   => get_stylesheet_directory_uri() . '/images/admin/icon-news.png'
        )
	);
}
