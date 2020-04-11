<?php
/**
 * Template part for displaying page content in default posts widget.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shopay
 */
$author_id  = get_the_author_meta( 'ID' );
$author_avatar = get_avatar( $author_id, 'thumbnail' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'wow fadeInUp' ); ?>>
    <figure class="post-thumb shopay-bg-image medium-image" style="background-image:url( <?php echo esc_url( get_the_post_thumbnail_url() ); ?> )">
    </figure>

    <div class="post-content-wrap clearfix">
        <div class="post-author">
            <?php
                echo $author_avatar;
                shopay_posted_by();
            ?>
        </div><!-- .post-author -->
        <div class="post-elements">
            <div class="post-meta">
                <?php
                    shopay_widget_posted_on();
                    shopay_widget_comment_meta();
                ?>
            </div>
            <?php the_title( '<h2 class="post-title large-font"><a href="'.esc_url( get_the_permalink() ).'">', '</a></h2>' ); ?>
            <div class="entry-content">
                <?php the_excerpt(); ?>
            </div><!-- .entry-content -->
        </div><!-- .post-elements -->
    </div><!-- .post-content-wrap -->
</article><!-- #post-<?php the_ID(); ?> -->