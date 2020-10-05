<?php

/* Template Name: Library Shell */ 

get_header(); 

get_template_part( 'includes/main-video' ); 

get_template_part( 'includes/breadcrumbs' ); ?>

<section class="main-interior container">

	<section class="main-content">

	<?php
	if ( get_field( 'right_side_custom' ) ) {
		echo "<aside>";
			the_field( 'right_side_custom' );
		echo "</aside>";
	}

    if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
    }
	$libraryResource = get_the_title();

	$frameSize = '1000px';
    $frameSource = '';
	if ($libraryResource == 'E-Books') {
		$frameSource = 'eBooks';
		$frameSize = '1050px';
	}elseif($libraryResource == 'E-Journals'){
		$frameSource = 'eJournals';
		$frameSize = '750px';
	}elseif($libraryResource == 'Online Databases'){
		$frameSource = 'eDatabases';
		$frameSize = '1600px';
	}elseif($libraryResource == 'Student Textbooks'){
		$frameSource = 'eTextBooks';
		$frameSize = '4500px';
	}
    ?>
	<iframe id="library-iframe" src="//library.med.ucf.edu/<?php echo $frameSource; ?>.aspx" width="1000" border="0" height="<?php echo $frameSize; ?>" style="border: none; height: <?php echo $frameSize; ?>;" scrolling="yes" marginheight="0" marginwidth="0" frameborder="0">
		<p>Yikes! Your browser isn't displaying iFrames at this time.</p>
	</iframe>

	</section>

</section>



<?php get_footer(); ?>