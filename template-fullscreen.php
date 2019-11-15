<?php
/**
 * Template Name: Full Width
 * Template Post Type: page, degree
 */
?>
<?php get_header(); the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<?php the_content(); ?>
</article>

<?php if ( is_front_page() ) { ?>

 	<!-- Front -->

<?php } ?>

<?php get_footer(); ?>
