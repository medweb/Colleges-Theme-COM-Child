<?php
require_once( 'com_shortcode.php' );

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 12/09/15
 * Time: 3:00 PM
 */
class archive_newsletters_shortcode extends com_shortcode {

	const name          = 'archive_newsletters'; // the text entered by the user (inside square brackets)
	const section_name  = 'archive_newsletters_settings'; //unique section id that organizes each setting
	const section_title = 'Archive newsletters Listing [archive_newsletters]'; // Section header for this shortcode's settings

	const taxonomy_categories = 'newsletter_category'; // the slug for the 'categories' taxonomy, used in the 'archive_newsletters' custom post type.

	const tinymce_archive_newsletters_category   = 'category'; // if unset, show all archive newsletters. otherwise, limit to profiles in this category name or slug.
	const tinymce_archive_newsletters_year   = 'years'; // if unset, show all archive newsletters. otherwise, limit to profiles in this year.
	const tinymce_archive_newsletters_per_page = 'archive_newsletters_count'; // number of archive newsletters to return. if more archive newsletters than this exist, it will paginate.


	public function __construct( ) {
		add_action('wp_loaded', array($this, 'init_hooks'));
	}

	public function init_hooks(){
		add_filter('query_vars', array($this, 'parameter_queryvars'));
	}

	/**
	 * Tells wordpress to listen for the 'category' and 'year' parameter sin the url. Used to filter down to specific newsletters.
	 * @param $query_vars
	 *
	 * @return array
	 */
	public function parameter_queryvars($query_vars){
		$query_vars[] = 'category';
		$query_vars[] = 'years';
		return $query_vars;
	}

	public function get_name() {
		return self::name;
	}

	public function get_css() {
		return '';
	}

	public function get_section_name() {
		return self::section_name;
	}

	public function get_section_title() {
		return self::section_title;
	}

	public function add_settings() {
		$this->add_setting_custom_fields_group();
	}

	public function replacement( $attrs = null ) {
		global $wpdb; // direct sql call
		global $post; // manually set global post when looping through results
		$attrs = shortcode_atts(
			array(
				self::tinymce_archive_newsletters_category => '', //default to show all newsletters, regardless of category
				self::tinymce_archive_newsletters_year => '', //default to show all newsletters, regardless of year
				self::tinymce_archive_newsletters_per_page => '-1', //default to no max count
			), $attrs, self::name
		);

		if ($attrs[ self::tinymce_archive_newsletters_category ]) {
			// shortcode category is defined. show only that category.
			$newsletters_archive_category = $attrs[ self::tinymce_archive_newsletters_category ];
		} else {
			// category undefined. allow the client browser to narrow the category
			// by passed in parameters.
			$newsletters_archive_category = get_query_var('category');
			if (is_array($newsletters_archive_category)){
				$newsletters_archive_category = $newsletters_archive_category[0]; //with arrays, just take the first one. can't filter multiple categories.
			}
		}
		if ($attrs[ self::tinymce_archive_newsletters_year ]) {
			// shortcode category is defined. show only that category.
			$archive_newsletters_year = $attrs[ self::tinymce_archive_newsletters_year ];
		} else {
			// category undefined. allow the client browser to narrow the category
			// by passed in parameters.
			$archive_newsletters_year = get_query_var('years');
			if (is_array($archive_newsletters_year)){
				$archive_newsletters_year = $archive_newsletters_year[0]; //with arrays, just take the first one. can't filter multiple categories.
			}
		}
		if (($attrs[ self::tinymce_archive_newsletters_per_page]) && (is_numeric( $attrs[ self::tinymce_archive_newsletters_per_page])) && ($attrs[ self::tinymce_archive_newsletters_per_page] > 0)){
			$newsletters_archive_count = $attrs[ self::tinymce_archive_newsletters_per_page];
		} else {
			$newsletters_archive_count = -1;
		}
		$newsletters_archive_count = $attrs[ self::tinymce_archive_newsletters_per_page];
		$profile_page = (get_query_var('paged')) ? get_query_var('paged') : 1; // if not defined, default to page 1
		$profile_page_post_start = ($newsletters_archive_count * ($profile_page - 1));

		$page_class = 'content';

		if ($newsletters_archive_category) {
			// get the category.

			// remove all non alpha-numerics characters from the user input.
			$newsletters_archive_category = strtolower( preg_replace( "/[^A-Za-z0-9]/", '', $newsletters_archive_category ) );
			// get all categories.
			$all_newsletters_archive_categories = get_terms( array( 'taxonomy' => self::taxonomy_categories ) );
			// loop through each category and check for match
			foreach ( $all_newsletters_archive_categories as $category ) {
				//  remove all non-alpha-numeric characters from slug and name.
				$category_normalized_slug = strtolower( preg_replace( "/[^A-Za-z0-9]/", '', $category->slug ) );
				$category_normalized_name = strtolower( preg_replace( "/[^A-Za-z0-9]/", '', $category->name ) );

				// match on either slug or name. NOTE: this does allow for the possibility of overlap, but the ability to not worry
				// about capitalization or other symbols outweighs the possibility of duplicate names. If this becomes a problem in the
				// future, just remove the normalization code and check explicit characters for a match.
				if ( ( $newsletters_archive_category == $category_normalized_name ) || ( $newsletters_archive_category == $category_normalized_slug ) ) {
					// if match found, set the staff_category to the slug of the matching category. use this for the sql query.
					$newsletters_archive_category = $category->slug;

				}
			}
			if ( ! ( $newsletters_archive_category ) ) {
				// if no match found, set the staff_category to a slug that doesn't exist.
				// with 0 profiles returned this way, the user knows for sure they entered a wrong
				// category, rather than having all profiles returned.

				$newsletters_archive_category = '!no_category'; // cause the query to return no results (there shouldn't exist a slug with exclamation points)
			}


		}
		if ($newsletters_archive_category){
			$tax_query = array(
				array(
					'taxonomy' => self::taxonomy_categories,
					'field'    => 'slug',
					'terms'    => $newsletters_archive_category,
				),
			);
		} else {
			$tax_query = array();
		}
		$args = array(
			'post_type' => 'newsletters',
			'paged' => get_query_var('paged'),
			'tax_query' => $tax_query
		);
        if ($archive_newsletters_year) {
            $args['date_query'] = array(
                array(
                    'year'  => $archive_newsletters_year,
                )
            );
        }

		$wp_query = new WP_Query( $args );
		$return = '';

		while ( $wp_query->have_posts() ) {

			$wp_query->the_post();

			$return .= '<article class="news-feed single-article">';
			$return .= '<h2><a href="'.get_permalink().'">'.get_the_title().'</a></h2><small class="badge badge-default">'.get_the_time('m/d/Y').'</small>';
			/*if ( has_post_thumbnail() ){
				echo '<a class="news-image" href="'.get_permalink().'">';
				the_post_thumbnail( array(100), array( 'style' => 'float:left; margin-right:10px;' ) );
				echo '</a>';
			}*/
			$return .= '<p>'.get_the_excerpt().'&nbsp;<a href="'.get_permalink().'" class="btn btn-info">Read More...</a></p>';
			$return .= '</article>';
		};

		$return .= '</section><div class="pagination">';

		$big = 999999999; // need an unlikely integer
		$return .= paginate_links(array(
			'add_args' => false,
            'base' => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $wp_query->max_num_pages,
        ) );
		$return .= '</div>';

		wp_reset_postdata();

		return $return;

	}
	
}
