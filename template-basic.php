<?php
/**
 * Template Name: Basic
 * Template Post Type: degree
 */
?>
<?php get_header(); the_post(); ?>

<div class="container mb-5 mt-3 mt-lg-5">

	<article class="<?php echo $post->post_status; ?> post-list-item">

		<!-- TODO Make Dynamic -->

		<ol class="breadcrumb" role="navigation" aria-label="breadcrumb">
		  <li class="breadcrumb-item"><a href="<?php bloginfo( 'url' ); ?>">Home</a></li>
		  

		  <?php if ( get_the_title( $post->post_parent ) != get_the_title() ) { ?> <li class="breadcrumb-item"><a href="#"><?php echo get_the_title( $post->post_parent ); ?></a></li> <?php } ?>

		  <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
		</ol>

		<?php include('includes/side-nav.php'); 

		if ( is_singular( 'news' ) ) { echo '<span class="author-meta">By '.get_the_author().' | '.get_the_date().' '.get_the_time().'</span>'; }

		the_content(); ?>

	</article>
</div>

<?php if ( is_front_page() ) { ?>

 	<!-- -->

<?php } if ( is_singular( 'news' ) ) { ?>

<hr/>

<center><h6>Related Stories</h6></center>

<section class="container related-content">

		<?php

		//TODO find curent sub meta taxonomy and pull only stories from it in this array query

        $args = array(
            'post_type' => 'news',
            'posts_per_page' => '6',
			'tax_query' => array(
				'relation' => 'AND',
				array(
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
				),
			)
		);
        
        $the_query = new WP_Query( $args );
        
        while ( $the_query->have_posts() ) : $the_query->the_post(); 

        $preview = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		$image = $preview[0]; ?>

		<div class="related-content-item">

			<a href="<?php the_permalink(); ?>" style="background: transparent url('<?php echo $image; ?>') no-repeat center center; background-size: cover;" class="related-image"><span><?php the_title(); ?></span></a>
			<h6><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>

		</div>

		<?php endwhile; wp_reset_postdata(); ?>

	</section>

<?php } get_footer(); ?>
