<?php
/**
 */

locate_template( 'includes/simple_html_dom.php', true, true);


$articles_per_row = 2;
?>

<?php get_header( 'newsletters' ); the_post(); ?>

<table width="100%" align="center" style="width: 100% !important; table-layout: fixed; background: #fff;" bgcolor="#fff">
	<tbody>
		<tr>
			<td align="center" style="padding: 0;">

				<table class="wrapperOuter" width="640" align="center" style="width: 640px; border: 1px solid #000; border-spacing: 0; border-collapse: collapse;">
					<tbody>
						<tr>
							<td align="center" style="padding: 0; border-bottom: 1px solid #333;">

								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center"  style="padding: 0; text-align: left; vertical-align: top;">
		
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/communique/communique-logo-18.png" width="400" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" border="0" class="responsiveimgh" />

											</td>

											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">

												<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/ucf-tab.png" width="40" class="ucf-tab-image" />

											</td>
										</tr>
									</tbody>
								</table>

							</td>
						</tr>

						<tr>
							<td align="center" style="padding: 0;">

								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
										<tr>
											<td align="center" style="text-align: left; vertical-align: top;">

												<?php if( get_field( 'newsletter_information' ) ) {
													echo "<div class='newsletter-information' style='padding: 20px; background: #ececec;'>";
															the_field( 'newsletter_information' );
													echo '</div>';
												}; ?>
											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<tr>
							<td align="center" style="padding: 0;">

								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">

												<table class="tableCollapse" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
													<tbody>


														<!-- <tr> above is beginning of news container row -->

														<!-- begin individual news TDs generated from loop below -->

														<?php wp_reset_postdata();

														$news_posts = array();

														$numberArticles = get_field( 'newsletter_number_of_articles' );

														/*
														 * For some reason, get_field is crashing (causing white screen) when it tries to grab
														 * these two fields inside the filter_where function. Instead, we call it outside the
														 * function and share it globally. I don't know how to pass the variables into the
														 * filter function via parameters, so this is the next best option.
														 * Obviously, finding out why it crashes would be even better, but wrapping both the
														 * query and the filter inside try-catch blocks doesn't prevent the white screen
														 * and doesn't let me debug the issue.
														 */
														global $communique_start_date;
														global $communique_end_date;
														$communique_start_date = get_field('news_story_start_date');
														$communique_end_date = get_field('news_story_end_date');
														$args = array(
														   'post_type' => 'news',
														   'posts_per_page' => $numberArticles,
															'tax_query' => array(
																//'relation' => 'AND',
																/*array(
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
																),*/
																array(
																	'taxonomy' => 'news_category',
																	'field'    => 'slug',
																	'terms'    => 'communique',
																	'operator' => 'IN',
																),
															)
														);
														/*
														 * Restricts the query to articles within a specific date range.
														 * Uses global variables because calling get_field with the 'news_story_start_date'
														 * or _end_date fields causes an unexplained white screen.
														 */
														function filter_where($where = '') {
															global $communique_start_date;
															global $communique_end_date;
															$where .= " AND (post_date >= '" . $communique_start_date."' AND post_date <= '".$communique_end_date." 23:59:59')";
															return $where;
														}
														$image_url_array = array ();
														add_filter( 'posts_where', 'filter_where' );
														$the_query = new WP_Query( $args );
														remove_filter( 'posts_where', 'filter_where' );
														
														while ( $the_query->have_posts() ) {

															$the_query->the_post();
															$image_url_array = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
															if ( $image_url_array[ 0 ] == '' ) {
																$thumb_image = get_bloginfo( 'template_url' ) . '/images/default/building.jpg';
															} else {
																$thumb_image = $image_url_array[ 0 ];
															}

															ob_start();

															post_class( 'prev-img' );

															$post_class = ob_get_clean();

															add_filter( 'excerpt_length', 'new_excerpt_length' ); // these functions are defined in the functions.php file.
															add_filter( 'excerpt_more', 'new_excerpt_more' );
															// add all COM articles to array
															array_push( $news_posts, array(
																'image'     => $thumb_image,
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

														// now grab any ucf health articles
														add_filter( 'wp_feed_cache_transient_lifetime', create_function('$a', 'return 600;') ); // refresh every 10 minutes
														$feed = fetch_feed("https://ucfhealth.com/feed/?post_type=news&news_category=crosspost-to-com");
														remove_filter( 'wp_feed_cache_transient_lifetime' , create_function('$a', 'return 600;') );


														if ( ! is_wp_error( $feed)) {
															$maxitems   = $feed->get_item_quantity( $numberArticles );
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
																	$image_url = 'https://ucfhealth.com/wp-content/themes/ucf-health-theme/images/logos/ucf-building.jpg'; // default stock image if image not set
																}

																$UTC = new DateTimeZone("UTC");
																$timezoneEST = new DateTimeZone("America/New_York");
																$datesort = new DateTime($item->get_date('Y-m-d H:i:s' ), $UTC);
																$datesort->setTimezone($timezoneEST);
																$date = new DateTime($item->get_date(), $UTC);
																$date->setTimezone($timezoneEST);

																if (($date > new DateTime($communique_start_date)) && ($date < new DateTime($communique_end_date))) {
																	array_push( $news_posts, array(
																		'image'     => $image_url,
																		'permalink' => $item->get_link(),
																		'title'     => $item->get_title(),
																		'piece'     => $content_minus_image,
																		'datesort'  => $datesort->format( 'Y-m-d H:i:s T' ),
																		'date'      => $date->format( 'F d, Y' ),
																		'class'     => 'class="prev-img"',
																		'target'    => 'target="_blank"'
																	) );
																}
															}
														}

														//add_filter( 'the_excerpt_rss', array($this, 'wcs_post_thumbnails_in_feeds' ));
														//add_filter( 'the_content_feed', array($this, 'wcs_post_thumbnails_in_feeds' ));

														// finally, sort by date, and choose the 7 most recent out of all

														usort($news_posts, function($a,$b){
															return strtotime($a['datesort']) < strtotime($b['datesort']);
														});

														$news_posts = array_slice($news_posts, 0, $numberArticles);

														// finally, loop through the array and print them out in chronological order.
														// as long as there's at least one med OR one ucf health article, print it.

														// Due to mail clients poor css3 implementations, we use tables for compatibility.
														// Print out only 2 articles per row, with the first row colspan=2.
														if (sizeof($news_posts) > 0) {
															$article_count = 0;

															function output_news_post($post, $colspan_tag = null, $img_width=290){
																?>
																<td align="center" style="padding: 10px; text-align: left; vertical-align: top;" class="columnCollapse" <?php echo $colspan_tag ?> >
																	<a target='<?php echo $post['target'] ?>' class="prev-img" href="<?php echo $post['permalink']; ?>" ><img src='<?php echo $post['image']; ?>')'  class="responsiveimg" width="<?php echo $img_width ?>" /></a>

																	<h2><a target='<?php echo $post['target'] ?>' href="<?php echo $post['permalink']; ?>"><?php echo $post['title']; ?></a></h2>

																	<p><?php echo $post['piece']; ?><a href="<?php echo $post['permalink'] ?>" class="more-link" target="<?php echo $post['target']?>">Read More...</a></p>
																</td>
																<?php
															}
															?>


																<?php
															foreach ($news_posts as $post){ 
																$article_count++;
																if ($article_count == 1){
																	echo "<tr>";
																	output_news_post($post, ' colspan="' . $articles_per_row . '" ', 600);
																	// don't print an ending table row. a function later will print it out for the next or last story
																} else {
																
																	if (($article_count - 2) % $articles_per_row == 0){ // subtract 2, since we ignore the first article (-1) , and we need 0-based (-1). this allows for any number of articles per row without changing code.
																		echo "</tr><tr>";
																	}
																	
																	output_news_post($post);
																}
																if ($article_count == sizeof($news_posts)){ // end tag for the last story. keep this outside the if/else loop, in case we only have a single story.
																	echo "</tr>";
																}
															}
															?>
															
														<?php
														} else {
															// only fires if there are 0 com articles and 0 ucf health crosspost articles
															the_content();
														} ?>

														<!-- end individual news TDs -->

														<!-- </tr> below is the end of the news container row -->


												</tbody>
											</table>

											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>


						<tr>
							<td align="center" style="padding: 0;">

								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
										<!-- <tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/communique/communique-header-events.png" width="151" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" />
											</td>
										</tr> -->
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">

												<!-- Events Widget 

												<div class="events-widget">
												
													<?php //locate_template( 'includes/ucf-events-feed.php', true, true); ucf_events_feed::output(3); ?>

												</div> -->

												<!-- -->

											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<tr>
							<td align="center" style="padding: 0;">

								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">

												

											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<tr>
							<td align="center" style="padding: 0; border-top: 1px solid #000;">


								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
										<tr>

											<td align="center" style="padding: 0 0 0 20px; text-align: left;">

												<div class="social-links">
													<a href="http://www.facebook.com/ourMedicalSchool" alt="Facebook" title="Facebook"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-facebook.jpg" width="27" alt="Facebook" title="Facebook" class="comnolazyload"/></a>
											
													<a href="http://www.instagram.com/ourMedSchool" alt="Instagram" title="Instagram"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-instagram.jpg" width="27" alt="Instagram" title="Instagram"  class="comnolazyload"/></a>
											
													<a href="https://twitter.com/ourmedschool" alt="Twitter" title="Twitter"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-twitter.jpg" width="27" alt="Twitter" title="Twitter" class="comnolazyload"/></a>
											
													<a href="http://www.flickr.com/photos/ourmedschool/" alt="Flickr" title="Flickr"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-flickr.jpg" width="27" alt="Flickr" title="Flickr" class="comnolazyload"/></a>

													<a href="http://www.youtube.com/ourmedicalschool" alt="YouTube" title="YouTube"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-youtube.jpg" width="27" alt="YouTube" title="YouTube" class="comnolazyload"/></a>
											
													<a href="https://plus.google.com/102370863086974585070" alt="Google Plus" title="Google Plus"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-google.jpg" width="27" alt="Google Plus" title="Google Plus" class="comnolazyload"/></a>
												</div>

												<a href="<?php bloginfo('url'); ?>" alt="UCF College of Medicine" title="UCF College of Medicine" class="com-link" style="margin: 10px 0; display: block;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/newsletter-com-logo.png" width="233" alt="UCF College of Medicine" title="UCF College of Medicine" class="comnolazyload"/></a>
				

												<!-- Auto Generated For Newsletter Use - CANSPAM Legal Compliance -->

												<div class="unsub-info">

													<div id="autolinks" class="autolinks"><span><unsubscribe>unsubscribe from this email </unsubscribe></span></div>

												</div>

												<!-- -->

											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>

									</tbody>
								</table>


							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php get_footer(); ?>
