<?php 

// Custom taxonomies
get_template_part( 'includes/taxonomies' );

// Custom post types
get_template_part( 'includes/post-types' );

// Custom permalinks for news and events
get_template_part( 'includes/permalinks' );

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

?>