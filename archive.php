<?php
/* 
Template Name: Archives
*/
get_header(); ?>
 
<div id="primary" class="site-content">

	<div id="content" role="main" class="container">
	 
	<?php while ( have_posts() ) : the_post(); ?>
	                 
		<h1 class="entry-title"><?php the_title(); ?></h1>
		 
		<div class="entry-content">
		 
			<?php the_excerpt(); ?>
		
		</div>
	 
	<?php endwhile; // end of the loop. ?>
	 
	</div>

</div>

<?php get_footer(); ?>