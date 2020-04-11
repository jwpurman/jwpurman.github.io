<?php
/**
 * Template for displaying search forms
 * 
 * @package Shopay
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'shopay' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr( 'Search &hellip;', 'shopay' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit"><i class="fas fa-search"></i></button>
</form>