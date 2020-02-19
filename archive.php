<?php
/* 
Template Name: Archives
*/
get_header();
?>
 
<div id="primary" class="site-content archive">

	<div id="content" role="main" class="container">

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