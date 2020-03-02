<?php
/**
 * Header Related Functions
 **/


/**
 * Get the markup for the primary site navbar.
 **/
function get_nav_markup_com() {
    ob_start();
    ?>
    <nav class="navbar navbar-toggleable-md navbar-inverse site-navbar" role="navigation" >
        <div class="container" >
            <?php if ( is_front_page() ): ?>
                <a href="<?php echo bloginfo( 'url' ); ?>" class="text-decoration-none" >
                    <h1 class="navbar-brand mb-0" >
                        <?php echo get_sitename_formatted(); ?>
                    </h1 >
                </a >
            <?php else: ?>
                <a href="<?php echo bloginfo( 'url' ); ?>" class="navbar-brand" >
                    <?php echo get_sitename_formatted(); ?>
                </a >
            <?php endif; ?>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#header-menu"
                    aria-controls="header-menu" aria-expanded="false" aria-label="Toggle navigation" >
                <span class="navbar-toggler-icon" ></span >
            </button >
            <?php
            wp_nav_menu( array(
                             'theme_location'  => 'header-menu',
                             'depth'           => 3,
                             'container'       => 'div',
                             'container_class' => 'collapse navbar-collapse',
                             'container_id'    => 'header-menu',
                             'menu_class'      => 'nav navbar-nav ml-md-auto',
                             'fallback_cb'     => 'bs4Navwalker::fallback',
                             'walker'          => new bs4Navwalker_com()
                         ) );
            ?>
        </div >
    </nav >
    <?php
    return ob_get_clean();
}

function get_header_markup_com() {
    $videos = $images = null;
    $obj    = get_queried_object();

    if ( is_single() || is_page() ) {
        $videos = get_header_videos( $obj );
        $images = get_header_images( $obj );
    }

    echo get_nav_markup_com();

    if ( $videos || $images ) {
        echo get_header_media_markup( $obj, $videos, $images );
    } else {
        echo get_header_default_markup( $obj );
    }
}

