<!DOCTYPE html>
<html lang="en-us">
	<head>
		<?php wp_head(); ?>
	</head>
	<body ontouchstart <?php body_class(); ?>>
		<?php do_action( 'after_body_open' ); ?>
		<header class="site-header">
			<?php echo colleges_theme_com_child_header_functions::get_header_markup_com();  ?>
		</header>
		<main id="main" class="site-main">
