<?php
/**
 * Template Name: Basic
 * Template Post Type: degree
 */
?>
<?php get_header(); the_post(); ?>

<div class="container mb-5 mt-3 mt-lg-5">
	<article class="<?php echo $post->post_status; ?> post-list-item">
		<?php the_content(); ?>
	</article>
</div>

<?php if ( is_front_page() ) { ?>

	<div class="container">

		Get Information for (audience groups)

	</div>

	<div class="container">

		News/Events

	</div>

	<div class="container">

		Add'l Info and Search Bar

	</div>

	<div class="container">

		Rich media area here for YT widget

	</div>

<?php } ?>

<?php get_footer(); ?>
