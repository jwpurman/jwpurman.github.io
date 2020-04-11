<?php
/**
* Files contains the cta options
*/
$traversify_lite_options = traversify_lite_theme_options();
$traversify_about_checkbox = $traversify_lite_options['cta_checkbox'];
if($traversify_about_checkbox == ''){
	if($traversify_lite_options['cta_title'] !=''||$traversify_lite_options['cta_description'] !=''||$traversify_lite_options['cta_image'] !=''){
?>
<section class="section cta-sec" style="background-image:url(<?php echo wp_kses_post($traversify_lite_options['cta_image']); ?>)">
    <div class="container">
        <div class="row">
            <div class="cta-content">
                <h2 class="cta-title"><?php echo esc_html($traversify_lite_options['cta_title']); ?></h2>
                    <p><?php echo esc_html($traversify_lite_options['cta_description']); ?></p>
                    <?php if(!empty($traversify_lite_options['cta_button_title'])){ ?>
                    <div class="cta-action">
                        <a href="<?php echo esc_url($traversify_lite_options['cta_button_link']); ?>" class="btn btn-default"><?php echo esc_html($traversify_lite_options['cta_button_title']); ?></a>
                    </div>
                    <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php } }?>