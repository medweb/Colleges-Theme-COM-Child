<?php
/* 
Template Name: Archives
*/
get_header();
?>
 
<div id="primary" class="site-content archive mt-4">

	<div id="content" role="main" class="container p-0 mb-4">

	<section class="archive-content">
	 
		<?php while ( have_posts() ) : the_post(); ?>

			<article class="">
		                 
				<h2 class="h5 entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				 
				<div class="entry-content">
				 
					<?php the_excerpt(); ?>
				
				</div>

			</article>
		 
		<?php endwhile; // end of the loop. ?>

	</section>
	 
	</div>

</div>

<?php get_footer(); ?>