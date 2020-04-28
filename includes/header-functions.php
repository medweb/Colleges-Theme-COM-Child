<?php
/**
 * Header Related Functions
 **/

class colleges_theme_com_child_header_functions {

	const header_prefix = 'header-menu';
	const max_menu_slots_blog_1 = 7;


    static function register_my_menus() {

	    if ( get_current_blog_id() === 1 ) {
	        // main site uses a bunch of menus with submenu structures

            // make 7 generic header menu slots, then register them
	        $main_menus = [];
	        for ($i = 1; $i <= self::max_menu_slots_blog_1; $i++){
	            $main_menus[self::header_prefix . '-' . $i] = __( "Main Header - Slot {$i}" );
            }
		    register_nav_menus($main_menus);

	    } elseif ( get_current_blog_id() === 8 ) {
	        // for some reason, nursing (blog 8) uses slot 2

		    register_nav_menus(
			    array(
				    self::header_prefix . '-' . '2' => __( 'Main Header - Slot 2' )
			    )
		    );

	    } else {
	        // for all other subsites, just use a single menu

		    register_nav_menus(
			    array(
				    self::header_prefix => __( 'Main Header' )
			    )
		    );

	    }

    }

	/**
	 * Get the markup for the primary site navbar.
	 **/
	function get_nav_markup_com() {
		ob_start();
		?>
		<?php if ( get_current_blog_id() === 1 ) { ?>
            <nav class="navbar navbar-toggleable-md navbar-inverse site-navbar" role="navigation" >
                <div class="container" >
					<?php if ( is_front_page() ): ?>
                        <a href="<?php echo bloginfo( 'url' ); ?>" class="text-decoration-none" >
                            <h1 class="navbar-brand mb-0" >
								<?php echo get_sitename_formatted(); ?>
                            </h1 >
                        </a >
					<?php else: ?>
                        <a href="<?php echo bloginfo( 'url' ); ?>" class="navbar-brand" >
							<?php echo get_sitename_formatted(); ?>
                        </a >
					<?php endif; ?>
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#header-menu"
                            aria-controls="header-menu" aria-expanded="false" aria-label="Toggle navigation" >
                        <span class="navbar-toggler-icon" ></span >
                    </button >

                    <div id="header-menu" class="navbar-collapse collapse" >

						<?php
						//    				$main_menu_array = array(
						//    					array( 'url' => '/about/', 'text' => 'About', 'acf_prefix' => 'about' ),
						//    					array( 'url' => '/admissions/', 'text' => 'Admissions', 'acf_prefix' => 'admissions' ),
						//    					array( 'url' => '/academics/', 'text' => 'Education', 'acf_prefix' => 'academics' ),
						//    					array( 'url' => '/research/', 'text' => 'Research', 'acf_prefix' => 'research' ),
						//    					array( 'url' => '/patient-care/', 'text' => 'Patient Care', 'acf_prefix' => 'patient_care' ),
						//					    array( 'url' => '/giving/', 'text' => 'Giving', 'acf_prefix' => 'giving' ),
						//    					array( 'url' => '/menu-7/', 'text' => 'Giving', 'acf_prefix' => 'giving' )
						//    				);

						$menus = get_registered_nav_menus();
						foreach ( $menus as $location => $description) {
							if (strpos(strtolower($location), self::header_prefix . '-') !== false) {
								// location is a header menu (slug starts with 'header-menu-' (note the last dash; we don't want a menu called exactly 'header-menu')
								$nav_menu = array(

									'theme_location'  => $location,
									'depth'           => 3,
									'container'       => 'div',
									'container_class' => 'com-submenu-l1',
									'container_id'    => $location,
									'menu_class'      => 'nav navbar-nav ml-md-auto',
									'fallback_cb'     => 'bs4Navwalker::fallback',
									'walker'          => new bs4Navwalker_com()

								);
								wp_nav_menu( $nav_menu );
							}


						}
						//    				foreach ( $main_menu_array as $menu_item ) {
						//    					//$image_url = wp_get_attachment_image_src( get_field( $menu_item['acf_prefix'] . '_image', 'options' ), 'large' );
						//    					 $array = array(
						//
						//    						'theme_location'  => 'menu-' . $count,
						//    						'depth'           => 3,
						//    						'container'       => 'div',
						//    						'container_class' => 'com-submenu-l1',
						//    						'container_id'    => 'header-menu-' . $count,
						//    						'menu_class'      => 'nav navbar-nav ml-md-auto',
						//    						'fallback_cb'     => 'bs4Navwalker::fallback',
						//    						'walker'          => new bs4Navwalker_com()
						//
						//    					);
						//
						//    					wp_nav_menu( $array );
						//    					$count ++;
						//    				}
						?>

                    </div>

                </div >
            </nav >

			<?php
		} else {
			?>
            <nav class="navbar navbar-toggleable-md navbar-inverse site-navbar" role="navigation">
                <div class="container">
					<?php if ( is_front_page() ): ?>
                        <a href="<?php echo bloginfo( 'url' ); ?>" class="text-decoration-none">
                            <h1 class="navbar-brand mb-0">
								<?php echo get_sitename_formatted(); ?>
                            </h1>
                        </a>
					<?php else: ?>
                        <a href="<?php echo bloginfo( 'url' ); ?>" class="navbar-brand">
							<?php echo get_sitename_formatted(); ?>
                        </a>
					<?php endif; ?>
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#header-menu" aria-controls="header-menu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
					<?php
					wp_nav_menu( array(
						             'theme_location'  => 'header-menu',
						             'depth'           => 2,
						             'container'       => 'div',
						             'container_class' => 'collapse navbar-collapse',
						             'container_id'    => 'header-menu',
						             'menu_class'      => 'nav navbar-nav ml-md-auto',
						             'fallback_cb'     => 'bs4Navwalker::fallback',
						             'walker'          => new bs4Navwalker()
					             ) );
					?>
                </div>
            </nav>
			<?php
		}
		return ob_get_clean();
	}

    static function get_header_markup_com() {
	    $videos = $images = null;
	    $obj    = get_queried_object();

	    if ( is_single() || is_page() ) {
		    $videos = get_header_videos( $obj );
		    $images = get_header_images( $obj );
	    }

	    echo self::get_nav_markup_com();

	    if ( $videos || $images ) {
		    echo get_header_media_markup( $obj, $videos, $images );
	    } else {
		    echo get_header_default_markup( $obj );
	    }
    }
}
add_action( 'init', ['colleges_theme_com_child_header_functions','register_my_menus' ] );







