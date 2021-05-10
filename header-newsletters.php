<!DOCTYPE html>
<html lang="en-us">
	<head>

		<!-- CSS -->

		<style type="text/css">

			* {
				zoom:1;
			}

			img[data-imagetype="DataUri"], .x_responsiveimgh, .x_lazyload {
				display: none !important;
			}

	     
		  	div, p, a, li, td { 
		  		-webkit-text-size-adjust:none; 
		  	} /* ios likes to = enforce a minimum font size of 13px; kill it with this */

			html,
			body {
				margin: 0;
				padding: 0;
			}

			h2 a {
				color: #000;
				font-size: 22px;
			}

			.index-event {
				width: 27%;
				float: left;
				background: #ececec;
				margin: 2% 2% 2% 0;
				padding: 2%;
			}

			.index-event div {
				margin: 5px 0;
			}

			.event-date {
				font-weight: bold;
			}

			.wp-post-image {
				float: left !important;
				width: 30% !important;
				height: inherit !important;
				clear: none !important;
				margin-right: 25px;
			}

			.health-tip-widget a img {
				width: 50%;
			}

			footer, .site-footer {
				display: none !important;
			}

			@media all and (max-width: 640px) {

		        table {
		          border-collapse: separate !important;
		        }

		        /* The outermost wrapper table */
		        table[class="wrapperOuter"] {
		          width: 100% !important;
		          margin: 0 !important;
		        }

		        /* The firstmost inner tables, which should be padded at mobile sizes */
		        table[class="wrapperInner"] {
		          width: 100% !important;
		          padding-left: 15px;
		          padding-right: 15px;
		          border-left: 0px solid transparent !important;
		          border-right: 0px solid transparent !important;
		          margin: 0 !important;
		        }

		        /* Generic class for a table column that should collapse to 100% width at mobile sizes */
		        td[class="columnCollapse"],
		        th[class="columnCollapse"] {
		          border-left: 0px solid transparent !important;
		          border-right: 0px solid transparent !important;
		          clear: both;
		          display: block !important;
		          float: left;
		          margin-left: 0 !important;
		          margin-right: 0 !important;
		          overflow: hidden;
		          padding-left: 0 !important;
		          padding-right: 0 !important;
		          width: 100% !important;
		        }

		        /* Generic class for a table within a column that should be forced to 100% width at mobile sizes */
		        table[class="tableCollapse"] {
		          border-left: 0px solid transparent !important;
		          border-right: 0px solid transparent !important;
		          margin-left: 0 !important;
		          margin-right: 0 !important;
		          padding-left: 0 !important;
		          padding-right: 0 !important;
		          width: 100% !important;
		        }

		        /* Forces an image to fit 100% width of its parent */
		        img[class="responsiveimg"] {
		          max-width: none !important;
		          width: 100% !important;
		        }

		        img[class="responsiveimgh"] {
		          max-width: none !important;
		          width: 70% !important;
		        }

		        *[class="hidemobile"] {
		          display: none;
		          font-size: 0;
		          line-height: 0;
		          max-height: 0;
		          mso-hide: all; /* hide elements in Outlook 2007-2013 */
		          overflow: hidden;
		          width: 0;
		        }

		        *[class="showmobile"] {
		          display: block !important;
		          font-size: initial;
		          line-height: initial;
		          max-height: none !important;
		          mso-hide: none !important;
		          overflow: visible !important;
		          width: auto !important;
		        }

		        td[class="givebtn"] {
		          font-size: 16px !important;
		          padding: 10px !important;
		        }

		        td[class="givedesc"] {
		          font-size: 16px !important;
		        }


	        }
			
		</style>

		<?php wp_head(); ?>
	</head>
	<body ontouchstart <?php body_class( 'newsletters' ); ?>>

		<?php do_action( 'after_body_open' ); ?>

		<main id="main" class="site-main">
