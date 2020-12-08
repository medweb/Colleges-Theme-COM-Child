<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephen
 * Date: 2020-12-08
 * Time: 12:05 PM
 */


// woff (font) files are references in the parent theme. This code will tell browsers to start fetching/downloading the font files
// during the page load so that it can load faster due to having it downloaded by the time the page needs it for rendering
add_action( 'wp_head', 'colleges_theme_com_child_preload', 5);

function colleges_theme_com_child_preload(){
	$important_fonts = array(
		"https://med.ucf.edu/wp-content/themes/Colleges-Theme/static/fonts/ucf-sans-serif-alt/ucfsansserifalt-black-webfont.woff2",
		"https://med.ucf.edu/wp-content/themes/Colleges-Theme/static/fonts/ucf-sans-serif-alt/ucfsansserifalt-bold-webfont.woff2",
		"https://med.ucf.edu/wp-content/themes/Colleges-Theme/static/fonts/ucf-sans-serif-alt/ucfsansserifalt-medium-webfont.woff2",
		"https://med.ucf.edu/wp-content/themes/Colleges-Theme/static/fonts/ucf-sans-serif-alt/ucfsansserifalt-semibold-webfont.woff2"
	);
	foreach ($important_fonts as $font){
		echo "<link rel='preload' href='${font}' as='font' />";
	}
}