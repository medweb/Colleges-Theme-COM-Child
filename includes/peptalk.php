<table width="100%" align="center" style="width: 100% !important; table-lay=
out: fixed;">
	<tbody>
		<tr>
			<td align="center" style="padding: 0;">

				<table class="wrapperOuter" width="640" align="center" style="width: 640px; border: 1px solid #000; border-spacing: 0; border-collapse: collapse;">
					<tbody>
						<tr>
							<td align="center" style="padding: 0; border-bottom: 1px solid #333;">



								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center"  style="padding: 0; text-align: left; vertical-align: top;">
												<p>&nbsp;</p>
												<img src="<?php bloginfo('template_url'); ?>/images/newsletter/peptalk/peptalk-logo-18.png?v=2.0.0" width="400" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" border="0" class="responsiveimgh" />

											</td>

											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">

												<img src="<?php bloginfo('template_url'); ?>/images/newsletter/ucf-tab.png" width="40" class="ucf-tab-image" />

											</td>
										</tr>
									</tbody>
								</table>

							</td>
						</tr>


						<tr>
							<td align="center" style="padding: 0;">

								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">

												<?php if ( have_posts() ) :
														while ( have_posts() ) : the_post();
															the_content();
											        	endwhile;
													endif;
													wp_reset_postdata();
												?>
											</td>

											<td align="center" width="40" style="padding: 0; text-align: left; vertical-align: top;">&nbsp;</td>

											<td align="center" style="padding: 0; text-align: left;">
												<img src="<?php bloginfo('template_url'); ?>/images/newsletter/peptalk/peptalk-header-gallery.png" width="100" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" />
												<p>&nbsp;</p>
												<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_1') ); ?>" />
												<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_2') ); ?>" />
												<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_3') ); ?>" />
												<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_4') ); ?>" />
												<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_5') ); ?>" />
												<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_6') ); ?>" />

												<?php if ( get_field( 'peptalk_image_7' ) != NULL ) { ?>
													<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_7') ); ?>" />	
												<?php } if ( get_field( 'peptalk_image_8' ) != NULL ) { ?>
													<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_8') ); ?>" />
												<?php } if ( get_field( 'peptalk_image_9' ) != NULL ) { ?>
													<img src="<?php echo wp_get_attachment_thumb_url( get_field('peptalk_image_9') ); ?>" />
												<?php } ?>

											</td>
										</tr>
									</tbody>
								</table>

							</td>
						</tr>
			
						<tr>
							<td align="center" style="padding: 0; border-top: 1px solid #000;">


								<table class="wrapperInner" width="600" align="center" style="border-spacing: 0; border-collapse: collapse;">
									<tbody>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>
										<tr>

											<td align="center" style="padding: 0 0 0 20px; text-align: left;">

												<div class="social-links">
													<a href="https://www.facebook.com/ourMedicalSchool" alt="Facebook" title="Facebook"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-facebook.jpg" width="27" alt="Facebook" title="Facebook" /></a>
											
													<a href="https://www.instagram.com/ourMedSchool" alt="Instagram" title="Instagram"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-instagram.jpg" width="27" alt="Instagram" title="Instagram" /></a>
											
													<a href="https://twitter.com/ourmedschool" alt="Twitter" title="Twitter"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-twitter.jpg" width="27" alt="Twitter" title="Twitter" /></a>
											
													<a href="https://www.flickr.com/photos/ourmedschool/" alt="Flickr" title="Flickr"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-flickr.jpg" width="27" alt="Flickr" title="Flickr" /></a>

													<a href="https://www.youtube.com/ourmedicalschool" alt="YouTube" title="YouTube"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-youtube.jpg" width="27" alt="YouTube" title="YouTube" /></a>
											
													<a href="https://plus.google.com/102370863086974585070" alt="Google Plus" title="Google Plus"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-google.jpg" width="27" alt="Google Plus" title="Google Plus" /></a>
												</div>

												<a href="<?php bloginfo('url'); ?>" alt="UCF College of Medicine" title="UCF College of Medicine" class="com-link" style="margin: 10px 0; display: block;"><img src="<?php bloginfo('template_url'); ?>/images/newsletter/newsletter-com-logo.png" width="233" alt="UCF College of Medicine" title="UCF College of Medicine" /></a>
				

												<!-- Auto Generated For Newsletter Use - CANSPAM Legal Compliance -->

												<div class="unsub-info">

													<div id="autolinks" class="autolinks"><span><unsubscribe>unsubscribe from this email </unsubscribe></span></div>

												</div>

												<!-- -->

											</td>
										</tr>
										<tr>
											<td align="center" style="padding: 0; text-align: left; vertical-align: top;">
												&nbsp;
											</td>
										</tr>

									</tbody>
								</table>


							</td>
						</tr>

					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>			