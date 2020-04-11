<?php
/**
* This file contains the banner section
*/

$traversify_lite_options = traversify_lite_theme_options();
?>
 <div class="banner-wrapper" style="background-image:url(<?php echo wp_kses_post($traversify_lite_options['banner_image']); ?>)">
            <div class="container">
                <div class="row">
                    <div class="banner-text-wrap">
                        <h2><?php echo esc_html($traversify_lite_options['banner_title']); ?></h2>
                        <p><?php echo esc_html($traversify_lite_options['banner_description']); ?></p>
                        <div class="banner-search-input">
                           <?php echo get_search_form(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>