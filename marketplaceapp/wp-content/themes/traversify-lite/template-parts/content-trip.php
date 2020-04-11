<?php 
/**
* Files contains the homeapge trip section
*/

$traversify_lite_options = traversify_lite_theme_options();
$traversify_lite_checkbox = $traversify_lite_options['trip_checkbox'];
if($traversify_lite_checkbox == ''){
?>
<div class="travel-listing-section section">
    <div class="container">
        <div class="section-title">
            <h2><?php echo esc_html($traversify_lite_options['trip_title']); ?></h2>
            <p><?php echo esc_html($traversify_lite_options['trip_description']); ?></p>
            <p>
        </div>
          <?php
          $traversify_lite_posts = esc_html($traversify_lite_options['trip_number']);
          $args = array( 
                'post_type'      => 'trip',
                'post_status'    => 'publish',
                'posts_per_page' => $traversify_lite_posts,
                'orderby'        => 'rand',
                 );
             $loop = 1;
             $traversify_lite_trips = new WP_Query($args);
             $traversify_lite_currency = traversify_lite_currenct_sumbol();
               if ($traversify_lite_trips->have_posts()) :
              while ($traversify_lite_trips->have_posts()) : $traversify_lite_trips->the_post();
                echo(($loop % 3 == 1 || $loop == 1) ? '<div class="row">' : '');
                  $traversify_lite_thumbnail_id = get_post_thumbnail_id();
                    $traversify_lite_trip_thumbnail = wp_get_attachment_image_src($traversify_lite_thumbnail_id, 'full');
                    $traversify_lite_meta = get_post_meta( get_the_ID(), 'wp_travel_engine_setting', true );
                   
                    $person_format = isset($wp_travel_engine_setting_option_setting['person_format']) ? $wp_travel_engine_setting_option_setting['person_format']:'/ person';
               ?>
                <div class="col-md-4 col-sm-12">
                    <div class="travel-listing-content-wrap">
                        <div class="travel-listing-thumb" style="background-image:url(<?php echo esc_url( $traversify_lite_trip_thumbnail[0],'traversify-lite'); ?>)">
                               <div class="listing-thumb-info">
                                <?php if( isset( $traversify_lite_meta['trip_prev_price'] ) && $traversify_lite_meta['trip_prev_price'] ) {
                              echo '<div class="travel-listing-price">'.
                               '<span><h3>'. esc_html( $traversify_lite_currency .' '.$traversify_lite_meta['trip_prev_price'] ).'</h3>'.$person_format.'</span>'.' </div>'; } ?>
                            </div>
                        </div>
                        <div class="travel-listing-desc-wrapper">
                           <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <span><?php
                                      $triptypes = get_terms( array(
                                            'taxonomy' => 'trip_types',
                                            'number'   => 3,
                                            'hide_empty' => false,
                                        ) );
                                         $i = 1;
                                        foreach( $triptypes as $triptype ) {
                                          $test = count($triptypes);
                                           $traversify_lite_triptype = !empty($triptype->name)? $triptype->name:'';
                                            echo '<a href="'.esc_url(get_term_link($triptype->slug, 'trip_types') ).'">';  echo esc_html($traversify_lite_triptype,'traversify-lite'); 
                                            echo '</a>';
                                            echo ($i < count($triptypes))? ", " : "";
                                           $i++;
                                        }
                                         ?></span>
                                          <?php 
                                             if( ( isset( $traversify_lite_meta['trip_duration'] ) &&  $traversify_lite_meta['trip_duration'] != '' )   || ( isset( $traversify_lite_meta['trip_duration_nights'] ) ) &&  $traversify_lite_meta['trip_duration_nights'] != '' ) {
                                               echo '<span class="travel-listing-duration"><i class="ion-ios7-clock-outline"></i>';
                                                    if( $traversify_lite_meta['trip_duration'] ) printf( esc_html__( '%s Days', 'traversify-lite' ), absint( $traversify_lite_meta['trip_duration'] ) );
                                                    if( $traversify_lite_meta['trip_duration_nights'] ) printf( esc_html__( ' - %s Nights', 'traversify-lite' ), absint( $traversify_lite_meta['trip_duration_nights'] ) ); ;
                                                    echo '</span>';
                                                    echo '</span>';
                                                } ?>
                                </div>
                            </div>
                       </div>
                <?php echo(($loop % 3  == 0 && $loop != 1) ? '</div>' : '');
                    $loop++;
                endwhile;
              endif; ?>

      </div>
</div>
<?php } ?>