<?php
/**
 * Template for footer section
 * 
 * @package Shopay
 */
?>

<div class="footer-content-wrap">
    <div class="mt-container">
        <nav id="footer-site-navigation" class="footer-main-navigation">
            <?php
                wp_nav_menu( array(
                    'theme_location' => 'menu-footer',
                    'menu_id'        => 'footer-menu',
                    'fallback_cb'    => false,
                    'depth'          => 1
                ) );
            ?>
        </nav><!-- #site-navigation -->
        <?php
            $shopay_footer_description = get_theme_mod( 'shopay_footer_description', __( 'Write a short description about your site here.', 'shopay' ) );
            if ( ! empty( $shopay_footer_description ) ) {
        ?>
                <div class="footer-description">
                    <?php echo wp_kses_post( $shopay_footer_description ); ?>
                </div><!-- .footer-description -->
        <?php
            }
        ?>
    </div><!-- mt-container -->
</div><!-- .footer-content-wrap -->

<?php
    $footer_social_media_option = get_theme_mod( 'footer_social_media_option', false );
    if ( true == $footer_social_media_option ) {
?>
        <div class="footer-social-media-section">
            <div class="mt-container">
                <?php shopay_social_media(); ?>
            </div><!-- mt-container -->
        </div><!-- .footer-social-media-section -->
<?php
    }
?>

<div class="site-bottom-footer">
    <div class="mt-container">
        <div class="site-info">
            <?php
                $shopay_copyright_text = get_theme_mod( 'shopay_copyright_text', __( 'Shopay Store', 'shopay' ) ); 
                echo esc_html( $shopay_copyright_text ); ?>
            <span class="sep"> | </span>
                <?php
                    $author_url = 'https://mysterythemes.com';
                    /* translators: 1: Theme name, 2: Theme author. */
                    printf( esc_html__( 'Theme: %1$s by %2$s.', 'shopay' ), 'Shopay', '<a href="'. esc_url( $author_url ) .'">Mystery Themes</a>' );
                ?>
        </div><!-- .site-info -->
        <?php
            $shopay_payment_image = get_theme_mod( 'shopay_payment_image', '' ); 
            if ( ! empty( $shopay_payment_image ) ) {
        ?>
            <div class="payment-image">
                <img src="<?php echo esc_url( $shopay_payment_image ); ?>" />
            </div>
        <?php } ?>
    </div><!-- mt-container -->
</div><!-- .site-bottom-footer -->