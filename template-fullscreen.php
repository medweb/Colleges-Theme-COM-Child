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

 	<!-- -->

 	<section class="container grid">

	 	<?php get_template_part( 'includes/print-social-posts' ); ?>


	 </section>

	 <section class="news">

		<section class="container">

			<section class="site-news">
				
				<?php get_template_part( 'includes/news-module' ); ?>

			</section>


			<section class="in-news">

				<!-- Switch to blog *before* printing the url, since we want in-the-news based on blog 1. -->
				<h4><a href="<?php bloginfo( 'url' ); ?>/news-and-communications/in-the-news/">In The News</a></h4>

				<?php
				$args = array(
					'post_type' => 'news',
					'posts_per_page' => 6,
					'tax_query' => array(
						array(
							'taxonomy' => 'news_category',
							'field'    => 'slug',
							'terms'    => 'external-news',
							'operator' => 'IN',
						),
					)
				);

				$the_query = new WP_Query( $args ); if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post(); ?>

						<article <?php post_class('innewsart'); ?>>

							<a href="<?php the_field( 'affiliate_url' ); ?>" target="_blank" title="<?php the_title(); ?>" ><?php the_title(); ?>
								<small ><?php the_date(); ?></small >
							</a>

						</article>

						<?php
					}
				}
				?>

			</section>

			<a href="<?php bloginfo( 'url' ); ?>/news/" class="more">More News</a>

		</section>

	</section>

<?php } ?>

<?php get_footer(); ?>
