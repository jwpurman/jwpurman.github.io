<?php
/**
 * executive functions and definitions
 *
 * @package executive
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1170;
}

if ( ! function_exists( 'executive_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function executive_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on executive, use a find and replace
	 * to change 'executive' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'executive', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for custom logo.
	 *
	 *  @since executive 1.0.0
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 154,
		'flex-height' => true,
		'flex-width' => true,
	) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'executive-portfolio-img', 1024, 690, true );
	add_image_size( 'executive-portfolio-image', 480, 360, true );
	add_image_size( 'executive-featured-entry-img', 470, 529, true );
	add_image_size( 'executive-single-img', 1920, 600, true );
	add_image_size( 'executive-front-featured', 480, 360, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Main Menu', 'executive' ),
		'social' => __( 'Social Menu', 'executive' ),
		'footer' => __( 'Footer Menu', 'executive' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	// Add excerpts to pages
	add_post_type_support( 'page', 'excerpt' );

	// Add styles to the post editor
	add_editor_style( array( 'editor-style.css', executive_font_url() ) );
}
endif; // executive_setup
add_action( 'after_setup_theme', 'executive_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function executive_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'executive' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'executive' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'executive_widgets_init' );

/**
 * Register Google font.
 */
function executive_font_url() {

	$fonts_url = '';

	/*
	* Translators: If there are characters in your language that are not
	* supported by the following, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$oswald = _x( 'on', 'oswald font: on or off', 'executive' );
	$lato = _x( 'on', 'Open Sans font: on or off', 'executive' );

	if ( 'off' !== $oswald || 'off' !== $lato ) {
		$font_families = array();

		if ( 'off' !== $oswald ) {
			$font_families[] = 'Oswald:400,700,300';
		}

		if ( 'off' !== $lato ) {
			$font_families[] = 'Lato:400,700,300';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}

/**
 * Enqueue scripts and styles.
 */
function executive_scripts() {

	wp_enqueue_style( 'executive-style', get_stylesheet_uri() );

	// Font Awesome
	wp_register_style( 'font-fontawesome', get_stylesheet_directory_uri() . '/assets/fonts/css/font-awesome.css', array(), '20151215' );
	wp_enqueue_style( 'font-fontawesome' );

	// Genericons
	wp_register_style( 'font-genericons', get_stylesheet_directory_uri() . '/assets/fonts/fonts/Genericons-Neue.css', array(), '20151215' );
	wp_enqueue_style( 'font-genericons' );

	wp_enqueue_script( 'executive-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'executive-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20151215', true );

	// Google Fonts
	wp_enqueue_style( 'executive-google-font', executive_font_url(), array(), null );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
add_action( 'wp_enqueue_scripts', 'executive_scripts' );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
