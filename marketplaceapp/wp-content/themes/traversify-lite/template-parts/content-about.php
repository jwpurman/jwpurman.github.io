<?php
/**
* Files contains the about us section
*/

$traversify_lite_options = traversify_lite_theme_options();
$traversify_about_checkbox = $traversify_lite_options['about_us_checkbox'];
if($traversify_about_checkbox == ''){
?>
<div class="about-sec text-center">
    <div class="container">
        <div class="row">
            <div class="section-title">
                <h2><?php echo esc_html($traversify_lite_options['about_us_title']); ?></h2>
                <p><?php echo esc_html($traversify_lite_options['about_us_description']); ?></p>
            </div>

            <img src="<?php echo wp_kses_post($traversify_lite_options['about_us_image']); ?>">
        </div>
    </div>
</div>
<?php } ?>