

<?php

locate_template( 'includes/simple_html_dom.php', true, true);

class news_module {
	function wcs_post_thumbnails_in_feeds( $content ) {
		global $post;
		if ( has_post_thumbnail( $post->ID ) ) {
			$content = '<p>' . get_the_post_thumbnail( $post->ID ) . '</p>' . $content;
		}

		return $content;
	}
	function wpdocs_excerpt_more( $more ) {
		return "<span class='readmore'> [...] Read More</span>";
	}
	function wpdocs_custom_excerpt_length( $length ) {
		// restrict summary to 30 words. change this in functions.php as well
		return 30;
	}

	function __construct() {
		$news_posts = array();

		$max_news_articles = 5; // show max of 5 articles.

		if ( get_current_blog_id() == 8 ) {

			switch_to_blog( 1 );

			$args = array(
				'post_type' => 'news',
				'posts_per_page' => $max_news_articles,
				'tax_query' => array(
					array(
						'taxonomy' => 'news_category',
						'field' => 'slug',
						'terms' => 'burnett-school',
						'operator' => 'IN'
					)
				)
			);

		} else {

			$args = array(
				'post_type' => 'news',
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'news_category',
						'field'    => 'slug',
						'terms'    => 'external-news',
						'operator' => 'NOT IN',
					),
					array(
						'taxonomy' => 'news_category',
						'field'    => 'slug',
						'terms'    => 'health-sciences-campus-news',
						'operator' => 'NOT IN',
					),
				),
				'posts_per_page' => $max_news_articles
			);

		}

		/*$args = array(
			'post_type' => 'news',
			'posts_per_page' => $max_news_articles
		);*/

		$the_query = new WP_Query( $args );


		while ( $the_query->have_posts() ) {

			$the_query->the_post();

			$news_image_array = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
			$news_image       = $news_image_array[ 0 ];

			ob_start();

			post_class( 'news-preview-image' );
			$post_class = ob_get_clean();

			add_filter( 'excerpt_length', 'new_excerpt_length' ); // these functions are defined in the functions.php file.
			add_filter( 'excerpt_more', 'new_excerpt_more' );

			// add all (5) COM articles to array
			array_push( $news_posts, array(
				'image'     => $news_image,
				'permalink' => get_the_permalink(),
				'title'     => get_the_title(),
				'piece'     => get_the_excerpt(),
				'datesort'  => get_the_date( 'Y-m-d H:i:s T' ),
				'date'      => get_the_date(),
				'class'     => $post_class,
				'target'    => ''
			) );

		}

		wp_reset_postdata();

		// next, get all UCF Health articles that have the crosspost-to-com category, and add them to the array.
		add_filter( 'wp_feed_cache_transient_lifetime', create_function('$a', 'return 600;') ); // refresh every 10 minutes
		$feed = fetch_feed("https://ucfhealth.com/feed/?post_type=news&news_category=crosspost-to-com");
		remove_filter( 'wp_feed_cache_transient_lifetime' , create_function('$a', 'return 600;') );


		if ( ! is_wp_error( $feed)) {
			$maxitems   = $feed->get_item_quantity( $max_news_articles );
			$feed_items = $feed->get_items( 0, $maxitems );


			foreach ( $feed_items as $item ) {
				/* @var SimplePie_Item $item */

				/* get thumbnail */
				$htmlDOM = new simple_html_dom();
				$htmlDOM->load( $item->get_content() );
				$image     = $htmlDOM->find( 'img', 0 );
				$image_url = $image->src;

				// remove images for description
				$image->outertext = '';
				$htmlDOM->save();

				$content_minus_image = wp_trim_words( $htmlDOM, new_excerpt_length(), new_excerpt_more() ); // these functions are defined in functions.php

				if ( ! isset( $image_url ) ) // if exists
				{
					$image_url = '/wp-content/themes/ucf-health-theme/images/logos/ucf-building.jpg'; // default stock image if image not set
				}

				$UTC = new DateTimeZone("UTC");
				$timezoneEST = new DateTimeZone("America/New_York");
				$datesort = new DateTime($item->get_date('Y-m-d H:i:s' ), $UTC);
				$datesort->setTimezone($timezoneEST);
				$date = new DateTime($item->get_date(), $UTC);
				$date->setTimezone($timezoneEST);

				array_push( $news_posts, array(
					'image'     => $image_url,
					'permalink' => $item->get_link(),
					'title'     => $item->get_title(),
					'piece'     => $content_minus_image,
					'datesort'  => $datesort->format('Y-m-d H:i:s T'),
					'date'      => $date->format('F d, Y'),
					'class'     => 'class="news-preview-image"',
					'target'    => 'target="_blank"'
				) );
			}
		}

		add_filter( 'the_excerpt_rss', array($this, 'wcs_post_thumbnails_in_feeds' ));
		add_filter( 'the_content_feed', array($this, 'wcs_post_thumbnails_in_feeds' ));

		// finally, sort by date, and choose the 7 most recent out of all

		usort($news_posts, function($a,$b){
			return strtotime($a['datesort']) < strtotime($b['datesort']);
		});

		$news_posts = array_slice($news_posts, 0, $max_news_articles);

		foreach ($news_posts as $post){
			?>
			<article>
				<a target='<?php echo $post['target'] ?>'  class="photo-prev" href="<?php echo $post['permalink']; ?>" style="background: url('<?php echo $post['image']; ?>') no-repeat center center; background-size: cover;"><?php echo $post['title']; ?></a>

				<?php if ($post['youtube_video_id'] ) { ?><aside class="go-to"><a href="<?php echo $post['permalink']; ?>">Go to Article</a></aside><?php } ?>

				<a href="<?php echo $post['permalink']; ?>" alt="<?php echo $post['title']; ?>" title="<?php echo $post['title']; ?>"><?php echo $post['title']; ?> <small><?php if ( $post['notification_type']) { ?><span class="notification"><?php $post['notification_type']; ?></span><?php } echo $post['date']; ?></small></a>

				<p><?php echo $post['piece']; ?></p>
			</article>

			<?php
			// the a.button should be outside the loop, but we only show 1 article. must fix later if we want to show more (requires css changes)
		}

		restore_current_blog();

		wp_reset_postdata();
	}
}

new news_module();
?> 

