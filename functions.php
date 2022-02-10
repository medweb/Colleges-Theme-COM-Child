<?php

get_template_part('includes/header-functions');

// associate news with people
get_template_part('includes/person-functions');

// modify menus to allow multi column
get_template_part('includes/wp-bs-navwalker');

// Custom taxonomies
get_template_part( 'includes/taxonomies' );

// Custom post types
get_template_part( 'includes/post-types' );

// Custom permalinks for news and events
get_template_part( 'includes/permalinks' );

// Preload often-used font files on every page
get_template_part('includes/preload');

// Email log on page changes
get_template_part('includes/email-notifications');

// Custom shortcodes
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

	// Library js - only register the script (the shortcode with enqueue it if shortcode exists on a page)
	wp_register_script(
		'library_js',
		get_stylesheet_directory_uri() . '/js/library.js',
		array('jquery'),
		filemtime( get_stylesheet_directory() . '/js/library.js' ), // force cache invalidate if md5 changes
		true // load in footer
	);

	// pagination for library e-resources
	wp_register_script(
		'twbs-pagination',
		get_stylesheet_directory_uri() . '/js/jquery.twbsPagination.min.js',
		array( 'jquery' ),
		filemtime( get_stylesheet_directory() . '/js/jquery.twbsPagination.min.js' ),
		true
	);
	//}

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

	// Google analytics
	wp_enqueue_script(
		'ucf_com_google_analytics',
		get_stylesheet_directory_uri() . '/js/google-analytics.js',
		array( ),
		filemtime( get_stylesheet_directory() . '/js/google-analytics.js' ), // force cache invalidate if md5 changes
		true // load in footer
	);

	if ( is_page( 'library' ) ) {
		// Google analytics
		wp_enqueue_script(
			'ucf_com_library_google_analytics',
			get_stylesheet_directory_uri() . '/js/google-analytics-library-page-click.js',
			array( ),
			filemtime( get_stylesheet_directory() . '/js/google-analytics-library-page-click.js' ), // force cache invalidate if md5 changes
			false //
		);
	}

	// register, but don't enqueue this script. it will be enqueued if a page content has the shortcode.
	/*wp_register_script(
		'view-all-events-script',
		get_stylesheet_directory_uri() . '/js/view-all-events.js',
		array('jquery'),
		filemtime( get_stylesheet_directory() . '/js/view-all-events.js' ), // force cache invalidate if md5 changes
		true // load in footer
	);*/

}

// Custom body class for page-name and site-name
add_filter( 'body_class', 'body_class_for_pages' );

function body_class_for_pages( $classes ) {

	global $post;

	$sitename = get_bloginfo('name');
	// strip out all whitespace
	$sitename = preg_replace('/\s*/', '', $sitename);
	// convert the string to all lowercase
	$sitename_clean = strtolower($sitename);

	$classes[] = 'page-' . $post->post_name;
	$classes[] = 'site-' . $sitename_clean;

	return $classes;

}

// Custom editor stylesheet
add_action( 'after_setup_theme', 'com_gutenberg_css' );

function com_gutenberg_css(){

	add_theme_support( 'editor-styles' ); // if you don't add this line, your stylesheet won't be added
	add_editor_style( 'editor-style.css' ); // tries to include style-editor.css directly from your theme folder

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



// Force login for dev
function rl_redirect(){
	if (!is_user_logged_in()) {
		auth_redirect();
	}
}

// Custom sitemap shortcode
add_shortcode('sitemap', 'wp_sitemap_page');

function wp_sitemap_page(){
	return "<ul class='sitemap comsitemap'>".wp_list_pages('title_li=&echo=0')."</ul>";
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

// Allow editors to see access the Menus page under Appearance but hide other options
// Note that users who know the correct path to the hidden options can still access them

if ( get_current_blog_id() != '1' ) {

	function hide_menu() {
		$user = wp_get_current_user();

		// Check if the current user is an Editor
		if ( in_array( 'editor', (array) $user->roles ) ) {

			// They're an editor, so grant the edit_theme_options capability if they don't have it
			if ( !current_user_can( 'edit_theme_options' ) ) {
				$role_object = get_role( 'editor' );
				$role_object->add_cap( 'edit_theme_options' );
			}

			// Hide the Themes page
			remove_submenu_page( 'themes.php', 'themes.php' );

			// Hide the Widgets page
			remove_submenu_page( 'themes.php', 'widgets.php' );

			// Hide the Customize page
			remove_submenu_page( 'themes.php', 'customize.php' );

			// Remove Customize from the Appearance submenu
			global $submenu;
			unset($submenu['themes.php'][6]);
		}
	}

	add_action('admin_menu', 'hide_menu', 10);

}

// suppress site-health.php warning for disabled automatic updates.
// we disable them on purpose and update wordpress core and plugins manually. no need to have it complain about it.
function prefix_remove_background_updates_test( $tests ) {
	unset( $tests['async']['background_updates'] );
	return $tests;
}
add_filter( 'site_status_tests', 'prefix_remove_background_updates_test' );


// Run environment options and functions
switch ( ENVIRONMENT ):
	case 'local':
	case 'dev':
		add_action('get_header', 'rl_redirect'); // force login for dev. using add_action prevents infinite loops
		break;
	case 'staging':
		add_action('get_header', 'rl_redirect'); // force login for dev. using add_action prevents infinite loops
		break;
	case 'production':
		break;
	default:
		break;
endswitch;

add_filter( "single_template", "get_custom_single_template" ) ;

// TERTIARY server detection
if (defined('TERTIARY_SERVER')){
	if (TERTIARY_SERVER === true){
		// disable the social board plugin, and any other we need to.
		add_action( 'init', 'disable_bad_plugins' );

	}
}

/**
 * Only runs if the server is currently hosted on the tertiary server. Due to firewall rules and
 * odd php versions, some plugins can cause pages to crash and should be disabled during the
 * period that the tertiary server is serving the failover site.
 */
function disable_bad_plugins(){

	$array_bad_plugins = array(
		'ax-social-stream/ax-social-stream.php',
	);
	$array_bad_shortcodes = array(
		'social_board',
	);

	deactivate_plugins( $array_bad_plugins );

	foreach ($array_bad_shortcodes as $bad_shortcode){
		remove_shortcode($bad_shortcode);
	}
}

?>