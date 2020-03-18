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

		<?php include('includes/side-nav.php'); 

		if ( is_singular( 'news' ) ) { ?>

			<?php function custom_taxonomies_terms_links() {
			    // get post by post id
			    $post = &get_post($post->ID);
			    // get post type by post
			    $post_type = $post->post_type;
			    // get post type taxonomies
			    $taxonomies = get_object_taxonomies($post_type);

			    $out = "<ul class='post-info'>";
			    foreach ($taxonomies as $taxonomy) {        
			        $out .= "<li>".$taxonomy.": ";
			        // get the terms related to post
			        $terms = get_the_terms( $post->ID, $taxonomy );
			        if ( !empty( $terms ) ) {
			            foreach ( $terms as $term )
			                $out .= '<span class="btn btn-info btn-sm">'.$term->name.'</span> ';
			        }
			        $out .= "</li>";
			    }
			    $out .= "</ul>";

			    return $out;
			} ?>

			<?php echo custom_taxonomies_terms_links(); ?>

		<?php echo '<span class="author-meta">By '.get_the_author().' | '.get_the_date().' '.get_the_time().'</span>'; ?> 


		<?php }

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
