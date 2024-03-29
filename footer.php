		</main>
		<footer class="site-footer bg-inverse">
			<div class="container">
				<div class="row">
					<div class="col-lg-4">
						<section class="primary-footer-section-left">
							<h2 class="h5 text-primary mb-2 text-transform-none"><?php echo get_sitename_formatted(); ?></h2>
							<?php echo get_contact_address_markup(); ?>

							<?php if (get_current_blog_id() != 1) { echo 'This is a UCF College of Medicine website.'; } ?>

							<?php
								if ( shortcode_exists( 'ucf-social-icons' ) ) {
									echo do_shortcode( '[ucf-social-icons color="grey"]' );
								}
							?>
						</section>
					</div>
					<div class="col-lg-4">
						<section class="primary-footer-section-center">

							<?php if ( !dynamic_sidebar( 'footer-col-1' ) ) { ?>

							  	<h2 class="h6 heading-underline letter-spacing-3">Access Modules</h2>		

								<ul>
						            <li><a href="https://portal.med.ucf.edu/" target="_blank" rel="noopener noreferrer">Extranet</a></li>
									<li><a href="https://share.med.ucf.edu/sites/com" target="_blank" rel="noopener noreferrer">Intranet</a></li>
									<li><a href="https://my.ucf.edu/" target="blank" rel="noopener noreferrer">myUCF Login</a></li>
									<li><a href="https://oasis.med.ucf.edu/" target="_blank" rel="noopener noreferrer">OASIS</a></li>
									<li><a href="https://ucf.qualtrics.com">Student Professionalism Reporting</a></li>
									<li><a href="https://helpdesk.med.ucf.edu/calltickets/submit.aspx" target="_blank" rel="noopener noreferrer">Submit a Support Ticket</a></li>
									<li><a href="https://webcourses.ucf.edu/" target="_blank" rel="noopener noreferrer">Webcourses</a></li>
									<li><a href="https://med.ucf.edu/login" target="_blank" rel="noopener noreferrer">Website Login</a></li>
						        </ul>

							<?php } ?>
								
							</section>
					</div>
					<div class="col-lg-4">
						<section class="primary-footer-section-right">

							<?php if ( !dynamic_sidebar( 'footer-col-2' ) ) { ?>

							  	<h2 class="h6 heading-underline letter-spacing-3">Social Media</h2>		
			
								<ul class="social-icons">
								    <li><a href="https://www.facebook.com/ourMedicalSchool" class="facebook" alt="See us on Facebook" title="See us on Facebook" target="_blank">Facebook</a></li>
								    <li><a href="https://www.instagram.com/ourMedSchool" class="instagram" alt="See us on Instagram" title="See us on Instagram" target="_blank">Instagram</a></li>
								    <li><a href="https://twitter.com/ourmedschool" class="twitter" alt="Follow us on Twitter" title="Follow us on Twitter" target="_blank">Twitter</a></li>
								    <li><a href="https://www.flickr.com/photos/ourmedschool/albums" class="flickr" alt="See us on Flickr" title="See us on Flickr" target="_blank">Flickr</a></li>
								    <li><a href="https://www.youtube.com/ourmedicalschool" class="youtube" alt="See us on YouTube" title="See us on YouTube" target="_blank">YouTube</a></li>
									<li><a href="https://www.linkedin.com/school/university-of-central-florida-college-of-medicine/" class="linkedin" alt="See us on LinkedIn" title="See us on LinkedIn" target="_blank">LinkedIn</a></li>
								</ul>

							<?php } ?>
							
						</section>
					</div>
				</div>
			</div>
		</footer>
		<?php wp_footer(); ?>
        <!-- Server hostname: <?php echo gethostname(); ?> -->
    </body>
</html>
