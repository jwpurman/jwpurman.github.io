<?php 
/**
* Adding The customizer functionalities
*/

/* for default */
$traversify_lite_default = traversify_lite_theme_options();

/**
* Customizer options for about us section
*/

/* About us  title */
	$wp_customize->add_setting('traversify_lite_theme_options[about_us_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['about_us_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[about_us_title]',
	    array(
	        'label' => esc_html__('Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 2,
	        'section' => 'about_section',
	       
	    )
	);
/* About us description */

	$wp_customize->add_setting('traversify_lite_theme_options[about_us_description]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['about_us_description'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[about_us_description]',
	    array(
	        'label' => esc_html__('Description', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'about_section',
	        
	    )
	);


/* About us  Checkbox */ 

	$wp_customize->add_setting('traversify_lite_theme_options[about_us_checkbox]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['about_us_checkbox'],
	        'sanitize_callback' => 'traversify_lite_sanitize_checkbox',
	    )
	);

	$wp_customize->add_control('traversify_lite_theme_options[about_us_checkbox]',
	    array(
	        'label' => esc_html__('Check To Hide', 'traversify-lite'),
	        'description' => esc_html__('Show/Hide About Section','traversify-lite'),
	        'type' => 'Checkbox',
	        'priority' => 1,
	        'section' => 'about_section',
	        
	    )
	);

    /* About us image upload */

	$wp_customize->add_setting('traversify_lite_theme_options[about_us_image]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['about_us_image'],
	        'sanitize_callback' => 'esc_url_raw',
	    )
	);
	$wp_customize->add_control(
	    new WP_Customize_Image_Control(
	        $wp_customize,
	        'traversify_lite_theme_options[about_us_image]',
	        array(
	            'label'           => esc_html__( 'Add Image', 'traversify-lite' ),
	            'section'         => 'about_section',
	            'settings'        => 'traversify_lite_theme_options[about_us_image]',
	        ) )
	);

/**
* Customizer cta sections
**/


/* CTA  title */
	$wp_customize->add_setting('traversify_lite_theme_options[cta_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['cta_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[cta_title]',
	    array(
	        'label' => esc_html__('Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 2,
	        'section' => 'cta_section',
	       
	    )
	);
/* About us description */

	$wp_customize->add_setting('traversify_lite_theme_options[cta_description]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['cta_description'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[cta_description]',
	    array(
	        'label' => esc_html__('Description', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'cta_section',
	        
	    )
	);


/* About us  Checkbox */ 

	$wp_customize->add_setting('traversify_lite_theme_options[cta_checkbox]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['cta_checkbox'],
	        'sanitize_callback' => 'traversify_lite_sanitize_checkbox',
	    )
	);

	$wp_customize->add_control('traversify_lite_theme_options[cta_checkbox]',
	    array(
	        'label' => esc_html__('Check To Hide', 'traversify-lite'),
	        'description' => esc_html__('Show/Hide About Section','traversify-lite'),
	        'type' => 'Checkbox',
	        'priority' => 1,
	        'section' => 'cta_section',
	        
	    )
	);

	/* CTA button title */

	$wp_customize->add_setting('traversify_lite_theme_options[cta_button_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['cta_button_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[cta_button_title]',
	    array(
	        'label' => esc_html__('Button Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'cta_section',
	        
	    )
	);

/* CTA button link */

	$wp_customize->add_setting('traversify_lite_theme_options[cta_button_link]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['cta_button_link'],
	        'sanitize_callback' => 'esc_url_raw',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[cta_button_link]',
	    array(
	        'label' => esc_html__('Button Button Link', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'cta_section',
	        
	    )
	);

    /* About us image upload */

	$wp_customize->add_setting('traversify_lite_theme_options[cta_image]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['cta_image'],
	        'sanitize_callback' => 'esc_url_raw',
	    )
	);
	$wp_customize->add_control(
	    new WP_Customize_Image_Control(
	        $wp_customize,
	        'traversify_lite_theme_options[cta_image]',
	        array(
	            'label'           => esc_html__( 'Add Image', 'traversify-lite' ),
	            'section'         => 'cta_section',
	            'settings'        => 'traversify_lite_theme_options[cta_image]',
	        ) )
	);