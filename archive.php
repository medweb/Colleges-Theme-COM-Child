<?php
/* 
Template Name: Archives
*/
get_header(); ?>
 
<div id="primary" class="site-content archive">

	<div id="content" role="main" class="container">

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

	<section class="archive-content">
	 
		<?php while ( have_posts() ) : the_post(); ?>

			<article>
		                 
				<h2 class="entry-title"><?php the_title(); ?></h2>
				 
				<div class="entry-content">
				 
					<?php the_excerpt(); ?>
				
				</div>

			</article>
		 
		<?php endwhile; // end of the loop. ?>

	</section>
	 
	</div>

</div>

<?php get_footer(); ?>