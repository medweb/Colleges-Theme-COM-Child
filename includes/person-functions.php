<?php



add_action( 'pre_get_posts', 'get_person_news_com_news_type' ); // link 'news' to people instead of 'posts'

add_filter( 'ucf_people_post_type_args', 'enable_rest' ); // enables blocks for person cpt
add_filter( 'ucf_people_group_args', 'enable_rest' ); // allows taxonomy to be shown in cpt that is rest-enabled
add_filter( 'pre_get_posts', 'enable_rss_parameter_post_associated_people'); // allows rss feed to be filtered by 'post_associated_people' ACF field
/**
 * Returns news publications related to a person.
 * Overrides parent theme so that we can use 'news' post types instead of the default 'post' type.
 *
 * @author Stephen Schrauger
 **/
function get_person_news_com_news_type( $query ) {

	// only run on code-defined wp_query
	if ( ( ! $query->is_main_query() ) && ( get_post_type() === 'person' ) ) {
		if ( $query->get( 'category_name' ) === 'faculty-news' || $query->get( 'category_name' ) === 'publication' ) { // further check that the query running is for this category
			$meta_query = $query->get( 'meta_query' );
			if ( $meta_query[ 0 ][ 'key' ] === 'post_associated_people' ) { // we only want to modify the one single query for associated news
				$query->set( 'post_type', 'news' ); // override the post type with our own.
				$query->set( 'taxonomy', 'news_category' ); // use our custom taxonomy
				$query->set( 'term', $query->get( 'category_name' ) ); // copy the term - faculty-news and publications will show up under their respective locations
				$query->set( 'category_name', null ); // can't use default category taxonomy, so clear this parameter
			}
		}
	}

}

function disable_taxonomy_person_tags( ) {

}



/**
 * Returns news publications related to a person on the current subsite from COM main site.
 *
 * @param $post object | Person post object
 * @param $limit integer | Number of articles to return (max)
 * @param $start integer | Offset from the newest article
 *
 * @return array | Array of Post objects
 **@author Stephen Schrauger
 */
function get_person_news_com_for_subsite($acf_association_subsite_key_name, $post, $limit = 4, $start = 0) {
	$site_id = 1; // pull news from site id 1 (main com site)
	switch_to_blog( $site_id );
	$network_posts = get_posts(
		array(
			'post_type'      => 'news',
			'posts_per_page' => $limit,
			'offset'         => $start,
			'term'           => 'faculty-news',
			// for our child theme, we don't care about publications. just do this network check for faculty-news
			'taxonomy'       => 'news_category',
			'meta_query'     => array(
				array(
					'key'     => $acf_association_subsite_key_name, // look for news that is linked to the biomed profiles
					'value'   => '"' . $post->ID . '"',
					'compare' => 'LIKE'
				)
			)
		)
	);
	restore_current_blog();

	return $network_posts;
}

/**
 * Displays News and Research/Publications for a person. For use in
 * single-person.php
 *
 * @param $post object | Person post object
 * @param $limit integer | Max number of articles to list
 * @param $start integer | Articles to skip (useful for pagination)
 *
 * @return Mixed | Grid and person's publication list HTML or void
 **@author Jo Dickson, Stephen Schrauger
 * @since 1.0.0
 */
function get_person_news_publications_markup_com( $post, $limit = 4, $start = 0 ) {
	if ( $post->post_type !== 'person' ) {
		return;
	}
	$news_this_site = get_person_news( $post, $limit, $start );
	$news_com_site  = [];

	switch ( get_current_blog_id() ) {
		case 8: // biomed site. pull in articles from com that associate with biomed profiles
			$news_com_site = get_person_news_com_for_subsite( 'post_associated_people_biomed',  $post, $limit, $start ); // get articles from main com site (or whatever network site was specified in this site's acf fields)
            break;
        // case: 123 // if we want to add sites in the future, add the id here, and call the subsite function with the acf key (which has to be created/duplicated)
	}

	// mix articles if needed
	$news = array_merge( $news_this_site, $news_com_site );

	$pubs = get_person_publications( $post, $limit, $start );

	ob_start();

	if ( $news || $pubs ):
		?>
        <div class="row" >
			<?php if ( $news ): ?>
                <div class="col-lg" >
                    <h2 class="person-subheading mt-5" >In The News</h2 >
					<?php
                    // this will print out the news for current and com sites. note: if there is news on both sites, it will output two <ul> elements.
                    if ( $news_this_site ) {
	                    echo get_person_post_list_markup( $news_this_site );
                    }
                    if ( $news_com_site ) {
                        switch_to_blog(1);
	                    echo get_person_post_list_markup( $news_com_site );
                        restore_current_blog();
                    }
                    ?>
                </div >
			<?php endif; ?>

			<?php if ( $pubs ): ?>
                <div class="col-lg" >
                    <h2 class="person-subheading mt-5" >Research and Publications</h2 >
					<?php echo get_person_post_list_markup( $pubs ); ?>
                </div >
			<?php endif; ?>
        </div >
	<?php
	endif;

	return ob_get_clean();
}


// Enabled blocks in person post type. This gives the ability to add blocks to the content,
// plus it enables a far better hierarchical checkbox list.
function enable_rest( $args ) {
	$args[ 'show_in_rest' ] = true;
	return $args;
}


/**
 * Adds a parameter to the rss feeds.
 * Paramater will filter to show only posts with a specific person associated with that post.
 * Person can be specified either by post ID or by post slug
 * @param $query
 */
function enable_rss_parameter_post_associated_people($query){
    if ($query->is_feed() && isset($_GET['post_associated_people'])) {
        $requested_person = sanitize_text_field($_GET['post_associated_people']);

        $person_post = get_post($requested_person);

        if (!$person_post) {
            // check if the parameter passed in is the slug instead of the id. if the following function returns a page, grab the id from that.
            $person_post = get_page_by_path($requested_person, OBJECT, 'person'); // get the id based on the slug
            if ($person_post){
                $requested_person = $person_post->ID;
            }
        }

        // if we found the person, get the id
        if ($person_post){
	        $requested_person_id = $person_post->ID;
        } else {
            // parameter passed in doesn't match an id or a slug. set to 000000 so that the rss feed matches nothing instead of just showing unfiltered articles.
            $requested_person_id = 000000;
        }

	    $query->set(
		    'meta_query',
		    array(
			    array(
			            // example in database:
                        // meta_id,   post_id,  meta_key,                 meta_value
                        // '3449030', '213305', 'post_associated_people', 'a:2:{i:0;s:4:"3561";i:1;s:5:"81774";}'
				    'key' => 'post_associated_people',
				    'value' => '"' . $requested_person_id . '"', // relationship acf fields are stored as serialized data. to match, search for the id surrounded by quotes, in a LIKE comparison
                    'compare' => 'LIKE'
			    )
		    )
	    );
    }
/*    if (!empty($_GET) && isset($_GET['post_associated_people']) && $query->is_main_query()) {
        $query->set(
            'meta_query',
            array(
                array(
                    'key' => 'post_associated_people',
                    'value' => sanitize_text_field($_GET['post_associated_people'])
                )
            )
        );
    }*/
}