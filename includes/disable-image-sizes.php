<?php
/**
 * Remove the Full Size image option for non-admin users.
 *
 * @param array $sizes Available image sizes.
 * @return array
 */
function com_child_theme_restrict_image_sizes( $sizes ) {

    if ( ! current_user_can( 'manage_options' ) ) {
        unset( $sizes['full'] );
    }
    return $sizes;
}

add_filter( 'image_size_names_choose', 'com_child_theme_restrict_image_sizes' );
