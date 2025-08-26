<!DOCTYPE html>
<html lang="en-us">
	<head>
		<?php wp_head(); ?>

		<!-- TEMP ADDITION FOR ANALYTICS 4 TESTING -->
		
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-EF3R36YKVE"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-EF3R36YKVE');
		</script>

	</head>
	<body ontouchstart <?php body_class(); ?>>
		<?php do_action( 'after_body_open' ); ?>
		<header class="site-header">
			<?php echo colleges_theme_com_child_header_functions::get_header_markup_com();  

			if ( is_singular( 'news' ) && get_field( 'news_subhead' ) ) { ?>

				<div class="news-subhead container"><p class="lead"><?php the_field( 'news_subhead'); ?></p></div>

			<?php } ?>

		</header>
		<main id="main" class="site-main">
