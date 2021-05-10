<?php get_header(); the_post(); ?>

<!-- Add Search Bar here to be floated right of page title -->

<?php
do_action( 'single_person_before_article'); // allows plugins (ie the directory) to add data (like the search bar)
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container my-5">
		<div class="row">
			<div class="col-md-4 mb-5">

				<aside class="person-contact-container">

					<a href="javascript:history.back()" class="return-tp"><i class="fa fa-chevron-circle-left icongrey"></i> Return to Directory</a>

					<div class="mb-4">
						<?php echo get_person_thumbnail( $post, 'rounded-circle' ); ?>
					</div>

					<h1 class="h5 person-title text-center mb-2">
						<?php echo get_person_name( $post ); ?>
					</h1>

					<?php if ( $job_title = get_field( 'person_jobtitle' ) ): ?>
					<div class="person-job-title text-center mb-4"><?php echo $job_title; ?></div>
					<?php endif; ?>

					<?php if ( $cv_url = get_field( 'person_cv' ) ): ?>
					<p>
						<a class="btn btn-secondary mt-3" href="<?php echo $cv_url; ?>">Download CV</a>
					</p>
					<?php endif; ?>

					<?php echo get_person_contact_btns_markup( $post ); ?>

					<?php echo get_person_dept_markup( $post ); ?>
					<?php echo get_person_office_markup( $post ); ?>
					<?php echo get_person_email_markup( $post ); ?>
					<?php echo get_person_phones_markup( $post ); ?>

					<!--<a class="btn btn-secondary" href="javascript:history.back()">Return to Directory</a> -->

				</aside>

			</div>

			<div class="col-md-8 col-lg-7 pl-md-5">

				<?php if( get_field('ucf_tf_person') ) { ?> <div class="alert alert-warning" role="alert">IF YOU ARE A UCF HEALTH PATIENT OF THIS PHYSICIAN AND WISH TO COMMUNICATE WITH THEM, PLEASE USE THE <a href="https://ucfhealth.com/patient-portal/">PATIENT PORTAL</a>.</div> <?php } ?>

				<section class="person-content">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
					  <li class="nav-item">
					    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Biography & Research</a>
					  </li>
					  <li class="nav-item">
					    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Education & Specialties</a>
					  </li>
					  <li class="nav-item">
					    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">News & Media</a>
					  </li>
					</ul>

					<div class="tab-content" id="myTabContent">
					  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
					  	<?php echo get_person_desc_heading( $post ); ?>
					  	<?php
						if ( $post->post_content ) {
							the_content();
						} else {
							echo '<p>No description available.</p>';
						}
						?>
					  </div>
					  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
					  	<div class="person-addl-info">

							<?php if ( get_field( 'person_educationspecialties' ) ) { ?>

								<h3><strong>Education</strong></h3>

								<?php the_field( 'person_educationspecialties' ); ?>

							<?php } ?>

						</div>
					  </div>
					  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
					  	<?php echo get_person_news_publications_markup_com( $post ); ?>

						<?php echo get_person_videos_markup( $post ); ?>
					  </div>
					</div>
					
				</section>

			</div>
		</div>

		
	</div>
</article>

<?php get_footer(); ?>
