<?php

include_once 'includes/header-functions.php';
include_once 'includes/wp-bs-navwalker.php';


// Custom taxonomies
get_template_part( 'includes/taxonomies' );

// Custom post types
get_template_part( 'includes/post-types' );

// Custom permalinks for news and events
get_template_part( 'includes/permalinks' );

// Custom shortcodes
get_template_part( 'includes/functions' );

if (!class_exists('ucf_com_shortcodes_settings')){
	get_template_part('includes/shortcodes_settings');
}

/**
 * Enqueues scripts and styles (javascript and css) used by the theme on every page.
 */
add_action( 'wp_enqueue_scripts', 'com_child_theme_scripts');

get_template_part('acf-fields'); //add all theme ACF settings (side & top nav)



function com_child_theme_scripts() {
    // Theme engine
    wp_enqueue_script(
        'com_child_theme_engine',
        get_stylesheet_directory_uri() . '/js/engine.js',
        array('jquery'),
        filemtime( get_stylesheet_directory() . '/js/engine.js' ), // force cache invalidate if md5 changes
        true // load in footer
    );

    // Library js
    wp_enqueue_script(
        'library_js',
        get_stylesheet_directory_uri() . '/js/library.js',
        array('jquery'),
        filemtime( get_stylesheet_directory() . '/js/library.js' ), // force cache invalidate if md5 changes
        true // load in footer
    );

    // masonry javascript for grid layouts
    wp_enqueue_script(
        'masonry',
        'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js',
        array(),
        null,
        true
    );

    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' ); // using get_TEMPLATE_directory_uri to force loading parent theme styles
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        filemtime(get_stylesheet_directory() . '/style.css' )
    );

    wp_register_style(
        'jquery-ui-style',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css',
        array( 'jquery' ),
        null
    );
    wp_enqueue_style( 'jquery-ui-style' );

    wp_enqueue_script(
        'jquery-ui-script',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
        array( 'jquery' ),
        null,
        false
    );
    
    // register, but don't enqueue this script. it will be enqueued if a page content has the shortcode.
    wp_register_script(
        'view-all-events-script',
        get_stylesheet_directory_uri() . '/js/view-all-events.js',
        array('jquery'),
        filemtime( get_stylesheet_directory() . '/js/view-all-events.js' ), // force cache invalidate if md5 changes
        true // load in footer
    );

}

// Custom login screen
add_action( 'login_head', 'custom_login_style' );
function custom_login_style() {
?>
    <style type="text/css">
    html, body {
        background: #222 !important;
    }
    h1 a {
        display: none !important;
    }
    a:hover {
        color: #fff !important;
    }
    input[type=text]:focus,
    input[type=password]:focus,
    input[type=checkbox]:focus {
        border-color: #666 !important;
        box-shadow: 0 0 2px #ffae00 !important;
    }
    .button-primary {
        background: #ffcc00 !important;
        box-shadow: 0 1px 0 #ffae00 !important;
        border-color: #ffae00 !important;
        text-shadow: 0 -1px 1px #ffae00, 1px 0 1px #ffae00, 0 1px 1px #ffae00, -1px 0 1px #ffae00 !important;
    }
    .message {
        border-left-color: #ffcc00 !important;
    }
    </style>
<?php
}

// Custom excerpt length for copy
add_filter( 'excerpt_length', 'new_excerpt_length' );
function new_excerpt_length( ) {
    return 25;
}

// Custom excerpt ellipses
add_filter( 'excerpt_more', 'new_excerpt_more' );
function new_excerpt_more( ) {
    return '...';
}

// Custom templates for custom post types that have taxonomies - they now work as single-*VARIABLE*.php
function get_custom_single_template($single_template) {
    global $post;

    if ($post->post_type == 'newsletters') {
        $terms = get_the_terms($post->ID, 'newsletter_category');
        if($terms && !is_wp_error( $terms )) {
            //Make a foreach because $terms is an array but it supposed to be only one term
            foreach($terms as $term){
                $single_template = dirname( __FILE__ ) . '/single-'.$term->slug.'.php';
            }
        }
     }
     return $single_template;
}

add_action( 'init', 'register_my_menus' );

function register_my_menus() {

	global $blog_id;

	if ( $blog_id == 1 ) {

		register_nav_menus(
			array(
				'menu-1' => __( 'Main Header - About' ),
				'menu-2' => __( 'Main Header - Admissions' ),
				'menu-3' => __( 'Main Header - Academics' ),
				'menu-4' => __( 'Main Header - Research' ),
				'menu-5' => __( 'Main Header - Patient Care' ),
				'menu-6' => __( 'Main Header - Giving' ),
				'menu-7' => __( 'Footer - Visitor Menu' ),
				'menu-8' => __( 'Footer - Facilities Menu' ),
				'menu-9' => __( 'Homepage - Find People' ),
				'menu-10' => __( 'Homepage - Personell Resources' ),
				'menu-11' => __( 'Homepage - Prospective Students' ),
				'menu-12' => __( 'Homepage - Current Students' )
			)
		);

	} elseif ( $blog_id == 8 ) {

		register_nav_menus(
			array(
				'menu-2' => __( 'Main Header - Research' )
			)
		);

	} else {

		register_nav_menus(
			array(
				'header-menu' => __( 'Main Header' )
			)
		);

	}

}

add_filter( "single_template", "get_custom_single_template" ) ;

?>