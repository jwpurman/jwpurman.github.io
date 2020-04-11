<?php
/**
 * Template for displaying search forms in executive
 *
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php _e( 'Search archives', 'executive' ); ?></span>
		<span class="search-icon">
		<a class="search-toggle"><span class="screen-reader-text"><?php _e( 'Search', 'executive' ); ?></span></a>
		</span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search...', 'executive' ); ?>" value="" name="s" title="<?php esc_attr_e( 'Search for:', 'executive' ); ?>" />
	</label>
	<input type="submit" class="search-submit" value="<?php esc_attr_e( 'Search', 'executive' ); ?>" />
</form>