<?php
/**
* This file contains the destination
*/
$traversify_lite_options = traversify_lite_theme_options();
$traversify_trip_checkbox = $traversify_lite_options['destination_checkbox'];
if($traversify_trip_checkbox == ''){
?>
<div class="section destination-cat-sec">
    <div class="container">
        <div class="row">
            <div class="section-title">
                <h2><?php echo esc_html($traversify_lite_options['destination_title']); ?></h2>
                <p><?php echo esc_html($traversify_lite_options['destination_description']); ?></p>
                <p>
             </div>
             <div class="destination-cat-slider owl-carousel owl-theme">
                <?php


                    $destinations = get_terms('destination',array(
                    'hide_empty' => true,
                      ));
                    foreach( $destinations as $destination ) {
                      $category = get_term_by('id', $destination->term_id, 'destination');
                      $destination_meta = get_option( "taxonomy_$destination->term_id" );
                      // $destination_meta = get_option( "taxonomy_$destination" );
                      $featured_image_id    = get_term_meta($destination->term_id, 'category-image-id', true);
                      $featured_image_url = wp_get_attachment_image_src ( $featured_image_id,'traversify-lite-destination-archive-img');
                      
                      ?>
                  <div class="destination-cat-wrap" style="background-image: url(<?php echo esc_url($featured_image_url[0]); ?>);">
                        <div class="destination-cat-desc">
                            <h3><a href="<?php echo esc_url(get_term_link($destination->slug, 'destination') ); ?>"> <?php echo esc_html($category->name);?> </a></h3>
                        </div>
                  </div> <?php  }  ?>
              </div>
         </div>
     </div>
  </div>
</div>
<?php }   ?>
