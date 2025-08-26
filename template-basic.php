<?php
/**
 * Template Name: Basic
 * Template Post Type: degree
 */
global $post, $post_id;
$parentId = $post->post_parent;
$linkToParent = get_permalink($parentId);

?>
<?php get_header(); the_post(); ?>

<div class="container mb-5 mt-3 mt-lg-5">

	<article class="<?php echo $post->post_status; ?> post-list-item">

		<?php if ( !is_singular( 'news' ) ) { ?>

			<ol class="breadcrumb" role="navigation" aria-label="breadcrumb">
			  <li class="breadcrumb-item"><a href="<?php bloginfo( 'url' ); ?>">Home</a></li>
			  

			  <?php if ( get_the_title( $post->post_parent ) != get_the_title() ) { ?> <li class="breadcrumb-item"><a href="<?php echo get_permalink( $parentId ); ?>"><?php echo get_the_title( $parentId ); ?></a></li> <?php } ?>

			  <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
			</ol>

		<?php } ?>

		<?php

        if (get_post_type() == 'page') {
	        include( 'includes/side-nav.php' );
        }

		if ( is_singular( 'news' ) ) { ?>

		<?php
            $author = '';
			if (get_field('author_override')){
				$author = get_field('author_override_text');
			}
			if (!$author){
			    // author override wasn't enabled, or it was but the text was left blank. use the user's name.
			    $author = get_the_author();
            }
            echo '<span class="author-meta">By ' . $author . ' | '.get_the_date().' '.get_the_time().'</span>';
            ?>


		<?php } the_content(); if ( is_singular( 'news' ) ) { 

			if ( get_field( 'affiliate_url' ) ) { ?>

				<a href="<?php echo get_field( 'affiliate_url' ); ?>" class="btn btn-primary" style="margin: 0 0 30px 0;">View Article On Affiliate Site</a>

			<?php } ?>

		<!-- <h6>Post Tags</h6> -->

		<?php /*echo custom_taxonomies_terms_links('post_tag');*/ } ?>

		<?php echo custom_taxonomies_terms_links('news_category'); ?>


	</article>
</div>

<?php if ( is_front_page() ) { ?>

 	<!-- -->

<?php } if ( is_singular( 'news' ) ) { echo do_shortcode( '[ucf-spotlight slug="giving"]' ); ?>

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

<?php } get_footer();

function custom_taxonomies_terms_links($taxonomy = null) {
    global $post;

	$out = "<ul class='post-info {$taxonomy}'>";
		$out .= "<li>";
		// get the terms related to post
		$terms = get_the_terms( $post->ID, $taxonomy );

		if ( !empty( $terms ) ) {
			foreach ( $terms as $term )
				$out .= '<a href="'.get_term_link($term->slug, $taxonomy).'" class="btn btn-sm btn-news-cats">'.$term->name.'</a> ';
		}
		$out .= "</li>";
	$out .= "</ul>";

	return $out;
}

?>
