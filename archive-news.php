<?php
get_header();
$current_term_slug = get_query_var('category');
$safe_current_term_slug = esc_attr($current_term_slug);
$current_year = get_query_var('years');
?>

	<section class="main-title">

		<section class="container">

			<h1>News &amp; Information Archive</h1>

		</section>

	</section>


<?php get_template_part( 'includes/breadcrumbs' ); ?>


<section class="main-interior container">


		<nav class="sort-nav">

			<h2>Sort Your News</h2>

			<section class="sort-items">
				<form action="/news/" method="get" id="news-archive-form">
					<select name='category'>
						<option value="" >All</option >
						<?php
						$myterms = get_terms(array('taxonomy' => 'news_category'));
						foreach ( $myterms as $term ) {
							if (!($term->term_id)){
								$term = get_term_by('id', $term, 'news_category' );
							}
							if ( $current_term_slug == $term->slug ) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo "<option value='" . $term->slug . "' $selected>" . $term->name . "</option>";
						}
						?>

					</select >
					<select name="years">
						<option value="" >All</option >
						<?php
						for ($i=2007; $i <= date("Y"); $i++){
							// arbitrarily choosing 2007, since that is the first news article.
							// if we start removing old articles, we may need to dynamically calculate the oldest year with articles.
							if ($current_year == $i){

								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo "<option value='" . $i . "' $selected>" . $i . "</option>";

						}
						?>
					</select>

					<button class="button" value="Find">Find</button>
				</form>
			</section>

		</nav>


		<section class="news-archive">
			<?php echo do_shortcode( "[archive_news archive_news_count='23' category='$safe_current_term_slug']" ); ?>
			<div class="spinner-overlay">
				<div id="loading-img"></div>
			</div>
		</section>


        <?php wp_reset_postdata(); ?>
	

</section>

<?php get_footer(); ?>