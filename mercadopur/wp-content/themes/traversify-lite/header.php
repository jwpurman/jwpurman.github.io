<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package traversify-lite
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'traversify-lite' ); ?></a>

	<header id="top" class="header hero">


        <div class="nav-wrapper header-default header-mobile-hide">
            <div class="container">
                <div class="row">
                    <nav class="navbar">
                        <div class="navbar-header">
                          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                          </button>
                          <div class="navbar-brand">
                            <div class="site-brandings">
                                <?php
                                the_custom_logo();
                                ?>
                                <h2 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"
                                                          rel="home"><?php echo esc_html(bloginfo('name')); ?></a></h2>
                                <?php
                                $description = get_bloginfo('description', 'display');
                                if ($description || is_customize_preview()) : ?>
                                    <p class="site-description"><?php echo esc_html($description,'traversify-lite'); /* WPCS: xss ok. */ ?></p>
                                    <?php
                                endif; ?>
                            </div><!-- .site-branding -->
                          </div>
                        </div>
                        <div class="collapse navbar-collapse" id="myNavbar">
                            <?php
                            $args = array(
                                'theme_location' => 'primary',
                                'container' => '',
                                'items_wrap' => '<ul class="nav navbar-nav navbar-right">%3$s</ul>',
                                'walker' => new Traversify_Lite_Nav_Walker(),
                                'fallback_cb' => 'Traversify_lite_Nav_Walker::fallback'
                            );
                            wp_nav_menu($args);//extract the content from apperance-> nav menu
                            ?>

                        </div>
                    </nav>
                </div>
            </div>
        </div>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
  <?php 
    /**
     * Content
     * 
     * @hooked traversify_lite_content_wrap_start
    */
    do_action( 'traversify_lite_wrap' ); ?>