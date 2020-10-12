<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2016-03-18
 * Time: 4:00 PM
 */

add_filter( 'post_type_link', 'com_news_permalink', 10, 3 );

global $wp_rewrite;
$news_structure = '/news/%year%/%monthnum%/%news%';
$wp_rewrite->add_rewrite_tag( "%news%", '([^/]+)', "news=" );
$wp_rewrite->add_permastruct( 'news', $news_structure, false );

function com_news_permalink( $permalink, $post_id, $leavename ) {
	$post = get_post( $post_id );
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		$leavename? '' : '%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		$leavename? '' : '%pagename%',
	);

	if ( '' != $permalink && !in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
		$unixtime = strtotime( $post->post_date );

		$category = '';
		if ( false !== strpos( $permalink, '%category%' ) ) {
			$cats = get_the_category( $post->ID );
			if ( $cats ) {
				usort( $cats, '_usort_terms_by_ID' ); // order by ID
				$category = $cats[0]->slug;
				$parent = $cats[0]->parent;
				if ( $parent ) {
					$category = get_category_parents( $parent, false, '/', true ) . $category;
				}
			}
			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty( $category ) ) {
				$default_category = get_category( get_option( 'default_category' ) );
				$category = is_wp_error( $default_category ) ? '' : $default_category->slug;
			}
		}

		$author = '';
		if ( false !== strpos( $permalink, '%author%' ) ) {
			$authordata = get_userdata( $post->post_author );
			$author = $authordata->user_nicename;
		}

		$date = explode( " ",date('Y m d H i s', $unixtime ) );
		$rewritereplace = array(
			$date[0],
			$date[1],
			$date[2],
			$date[3],
			$date[4],
			$date[5],
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		$permalink = str_replace( $rewritecode, $rewritereplace, $permalink );
	} else {
		// if they're not using the fancy permalink option
	}
	return $permalink;
}
