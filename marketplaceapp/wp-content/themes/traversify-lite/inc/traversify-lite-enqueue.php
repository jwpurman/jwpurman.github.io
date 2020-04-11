<?php
/**
* This file contains the enqueue files
*/

	wp_enqueue_style('traversify-lite-custom-css', get_template_directory_uri().'/assets/css/custom.css');
	wp_enqueue_style('traversify-lite-travel-css', get_template_directory_uri().'/assets/css/travel.css');
	wp_enqueue_style('traversify-lite-responsive-css', get_template_directory_uri().'/assets/css/responsive.css');
	wp_enqueue_style( 'traversify-lite-style', get_stylesheet_uri() );

	wp_enqueue_script( 'traversify-lite-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	 wp_enqueue_script( 'traversify-lite-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
	wp_enqueue_script('traversify-lite-slick', get_template_directory_uri().'/assets/js/slick.js',array('jquery'), '20151215', true);
	wp_enqueue_script('traversify-lite-bootstrap-min', get_template_directory_uri().'/assets/js/bootstrap.min.js',array('jquery'), '20151215', true);
	wp_enqueue_script('traversify-lite-owl-carousel', get_template_directory_uri().'/assets/js/owl.carousel.js',array('jquery'), '20151215', true);
	wp_enqueue_script('traversify-lite-sticky', get_template_directory_uri().'/assets/js/sticky-header.js',array('jquery'), '20151215', true);
	wp_enqueue_script('traversify-lite-html', get_template_directory_uri().'/assets/js/html5.js',array('jquery'), '20151215', true);
	wp_enqueue_script( 'traversify-lite-app', get_template_directory_uri() . '/assets/js/app.js', array('jquery'), '20151215', true );
	wp_style_add_data('IE-9', 'conditional', 'lt IE 9');
	/********* Adding Multiple Fonts ********************/
		$traversify_lite_googlefont = array();
		array_push( $traversify_lite_googlefont, 'Raleway');
		array_push( $traversify_lite_googlefont, 'Merienda+One');
		array_push( $traversify_lite_googlefont, 'Montserrat:600');
		$traversify_lite_googlefonts = implode("|", $traversify_lite_googlefont);
		wp_register_style( 'traversify_lite_google_fonts', '//fonts.googleapis.com/css?family='.$traversify_lite_googlefonts);
		wp_enqueue_style( 'traversify_lite_google_fonts' );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
