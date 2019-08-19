<?php
/**
 * Template Name: Basic
 * Template Post Type: degree
 */
?>
<?php get_header(); the_post(); ?>

<div class="container mb-5 mt-3 mt-lg-5">

	<article class="<?php echo $post->post_status; ?> post-list-item">
		<?php include('includes/side-nav.php'); the_content(); ?>
	</article>
</div>

<?php if ( is_front_page() ) { ?>

 	<!-- -->

<?php } ?>

<?php get_footer(); ?>
