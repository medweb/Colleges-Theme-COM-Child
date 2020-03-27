<?php
/**
 * Returns news publications related to a person.
 * Overrides parent theme so that we can use 'news' post types instead of the default 'post' type.
 *
 * @author Stephen Schrauger
 **/
function get_person_news_com_news_type($query) {

    // only run on code-defined wp_query
    if ((!$query->is_main_query()) && (get_post_type() === 'person')){
        if ($query->get('category_name') === 'faculty-news'){ // further check that the query running is for this category
            $meta_query = $query->get('meta_query');
            if ($meta_query[0]['key'] === 'post_associated_people'){ // we only want to modify the one single query for associated news
	            $query->set('post_type', ['news'] ); // override the post type with our own.
            }
        }
    }

}

add_action('pre_get_posts', 'get_person_news_com_news_type');