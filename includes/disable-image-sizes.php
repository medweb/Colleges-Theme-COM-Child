<?php

/**
 * Restrict the Full Size image option to only admins or those
 * specifically granted the capability with publishpress.
 *
 * @param array $sizes Available image sizes.
 * @return array
 */
function com_child_theme_restrict_image_sizes( $sizes ) {

	if (
		current_user_can( 'manage_options' ) ||
		current_user_can( 'use_full_image_size' )
	) {
		return $sizes;
	}

	unset( $sizes['full'] );

	return $sizes;
}

add_filter( 'image_size_names_choose', 'com_child_theme_restrict_image_sizes' );

/**
 * Register our custom capabilities with PublishPress Capabilities.
 *
 * @param array $plugin_caps Existing plugin capabilities.
 * @return array
 */
function com_child_theme_restrict_image_sizes_register_capabilities( $plugin_caps ) {

	$plugin_caps['Colleges Theme COM Child'] = array(
		'use_full_image_size',
	);

	return $plugin_caps;
}

add_filter( 'cme_plugin_capabilities', 'com_child_theme_restrict_image_sizes_register_capabilities' );