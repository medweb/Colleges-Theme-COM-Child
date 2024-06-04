<?php

// Disable WP Users REST API for non-authenticated users (allows anyone to see username list at /wp-json/wp/v2/users)
add_filter( 'rest_authentication_errors', function( $result ) {
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
    }

    if ( ! is_user_logged_in() ) {
        return new WP_Error(
            'rest_not_logged_in',
            __( 'You are not currently logged in.' ),
            array( 'status' => 401 )
        );
    }

    return $result;
});
