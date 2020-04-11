<?php
/**
*
*/
$traversify_lite_options = traversify_lite_theme_options();
$traversify_blog_checkbox = $traversify_lite_options['blog_checkbox'];
if($traversify_blog_checkbox == ''){
?>
<div class="blog-section section">
    <div class="container">
        <div class="row">
            <div class="section-title">
                <h2><?php echo esc_html($traversify_lite_options['blog_title']); ?></h2>
                <p><?php echo esc_html($traversify_lite_options['blog_description']); ?></p>
             </div>
                    <?php
                    $traversify_blog_category = $traversify_lite_options['blog_category'];
                    $blog_category_obj = get_category_by_slug($traversify_blog_category);
                    $blog_post_count = 3;
                    $blog_category_id = '';
                    $sticky = get_option( 'sticky_posts' );
                      if(!empty($sticky)){
                          $sticky_count = count($sticky);
                          if(!empty($sticky_count) && $sticky_count > $blog_post_count):
                              $blog_post_count = 0;
                          elseif (!empty($sticky_count) && $blog_post_count >$sticky_count):
                              $blog_post_count = $blog_post_count - $sticky_count;
                          endif;
                      }
                     if($blog_category_obj->term_id){
                        $blog_category_id = $blog_category_obj->term_id;
                        $args = array(
                            'post_type' => 'post',
                            'posts_per_page' => $blog_post_count,
                            'post_status' => 'publish',
                            'order' => 'DESC',
                            'orderby' => 'DATE',
                            'post__not_in' => array($blog_category_id),
                            'tax_query' =>
                                array(
                                    array(
                                        'taxonomy' => 'category',
                                        'field' => 'id',
                                        'terms' => $blog_category_id,
                                    ),
                                ),
                        );
                    }else{
                        $args = array(
                            'post_type' => 'post',
                            'posts_per_page' => $blog_post_count,
                            'post_status' => 'publish',
                            'order' => 'desc',
                            'orderby' => 'menu_order date',
                        );
                     }

                    $traversify_lite_query = new WP_Query($args);

                     if ($traversify_lite_query->have_posts()):
                        while ($traversify_lite_query->have_posts()) : $traversify_lite_query->the_post();
                        global $post;
                        $traversify_lite_post_format = get_post_format($post->ID);
                        $traversify_lite_thumbnail = wp_get_attachment_image(get_post_thumbnail_id($post->ID), 'full');
                        $archive_year  = get_the_time('Y');
                        $archive_month = get_the_time('m'); ?>
                        <div class="col-md-4 col-sm-12">
                            <div class="post-wrap">
                                <?php traversify_lite_blog_post_format($traversify_lite_post_format, $post->ID);?>
                                <div class="post-review">
                                    <h3 class="post-title"><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a></h3>
                                    <div class="post-info">
                                        <ul>
                                           <?php echo '<li>By <a href="'. esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) .'">'.esc_html(get_the_author()).'</a></li>'; ?>
                                          <?php echo '<li><span class="post-date"><a href="'. esc_url(get_month_link($archive_year,$archive_month)).'"><i class="fa fa-calendar-o"></i>' .esc_html(get_the_date()) .' </a></span></li>'; ?>
                                        </ul>
                                    </div>
                                    <p class="post-description"><?php echo wp_kses_post(traversify_lite_get_excerpt(get_the_ID(), 125)); ?></p>
                                    <a href="<?php echo esc_url(get_the_permalink()); ?>" class="btn btn-default"><?php echo esc_html__('Read more', 'traversify-lite'); ?> </a>
                                </div>
                            </div>
                        </div>



                    <?php
                    endwhile;
                endif;
              wp_reset_postdata(); ?>
         </div>
     </div>
</div>
<?php } ?>
