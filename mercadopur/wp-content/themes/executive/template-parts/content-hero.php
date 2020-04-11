<?php
/**
 * @package executive
 */

$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );
$thumb_url = $thumb_url_array[0];
?>

<div class="header-image" style="background-image: url('<?php  if ( '' != get_the_post_thumbnail() ) : echo $thumb_url; else : header_image(); endif; ?>');">
	<div class="hero-title">
		<h2>
			<?php the_title(); ?>
			<?php if ( 'post' == get_post_type() ) : ?>
				<span><?php executive_posted_on(); ?></span>
			<?php endif; ?>
		</h2>
	</div>
</div>
