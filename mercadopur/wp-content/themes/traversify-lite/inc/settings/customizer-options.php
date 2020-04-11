<?php 
/**
* Adding The customizer functionalities
*/

/* for default */
$traversify_lite_default = traversify_lite_theme_options();

//Support section
    $wp_customize->register_section_type( Pro::class );

     $wp_customize->add_section(new Pro($wp_customize,'support_links',array(
            'priority' => 1,
            'title'       => __( 'Traversify-Pro', 'traversify-lite' ),
            'button_text' => __( 'Go Pro',        'traversify-lite' ),
            'button_url'  => 'https://codethemes.co/product/traversify/'
            
            )
        )
    );

/* destination title */
	$wp_customize->add_setting('traversify_lite_theme_options[destination_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['destination_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[destination_title]',
	    array(
	        'label' => esc_html__('Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 2,
	        'section' => 'destination_section',
	       
	    )
	);
/* destination description */

	$wp_customize->add_setting('traversify_lite_theme_options[destination_description]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['destination_description'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[destination_description]',
	    array(
	        'label' => esc_html__('Description', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'destination_section',
	        
	    )
	);



/* Destination Checkbox */ 

	$wp_customize->add_setting('traversify_lite_theme_options[destination_checkbox]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['destination_checkbox'],
	        'sanitize_callback' => 'traversify_lite_sanitize_checkbox',
	    )
	);

	$wp_customize->add_control('traversify_lite_theme_options[destination_checkbox]',
	    array(
	        'label' => esc_html__('Check To Hide', 'traversify-lite'),
	        'description' => esc_html__('Show/Hide Destination Section','traversify-lite'),
	        'type' => 'Checkbox',
	        'priority' => 1,
	        'section' => 'destination_section',
	        
	    )
	);

/**
* Customizer options for trip section
*/

/* Trip title */
	$wp_customize->add_setting('traversify_lite_theme_options[trip_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['trip_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[trip_title]',
	    array(
	        'label' => esc_html__('Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 2,
	        'section' => 'trip_section',
	       
	    )
	);
/* Trip description */

	$wp_customize->add_setting('traversify_lite_theme_options[trip_description]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['trip_description'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[trip_description]',
	    array(
	        'label' => esc_html__('Description', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'trip_section',
	        
	    )
	);

/* Trip Number */ 

	$wp_customize->add_setting('traversify_lite_theme_options[trip_number]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['trip_number'],
	        'sanitize_callback' => 'absint',
	    )
	);

	$wp_customize->add_control('traversify_lite_theme_options[trip_number]',
	    array(
	        'label' => esc_html__('No Of Trip', 'traversify-lite'),
	        'description' => esc_html__('Total no of Trip to show','traversify-lite'),
	        'type' => 'number',
	        'priority' => 4,
	        'section' => 'trip_section',
	    )
	);

/* Trip Checkbox */ 

	$wp_customize->add_setting('traversify_lite_theme_options[trip_checkbox]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['trip_checkbox'],
	        'sanitize_callback' => 'traversify_lite_sanitize_checkbox',
	    )
	);

	$wp_customize->add_control('traversify_lite_theme_options[trip_checkbox]',
	    array(
	        'label' => esc_html__('Check To Hide', 'traversify-lite'),
	        'description' => esc_html__('Show/Hide Trip Section','traversify-lite'),
	        'type' => 'Checkbox',
	        'priority' => 1,
	        'section' => 'trip_section',
	        
	    )
	);

/**
*  Customizer Banner sections
**/

/* Banner title */
	$wp_customize->add_setting('traversify_lite_theme_options[banner_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['banner_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[banner_title]',
	    array(
	        'label' => esc_html__('Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 2,
	        'section' => 'banner_section',
	       
	    )
	);
/* Banner description */

	$wp_customize->add_setting('traversify_lite_theme_options[banner_description]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['banner_description'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[banner_description]',
	    array(
	        'label' => esc_html__('Description', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 3,
	        'section' => 'banner_section',
	        
	    )
	);
/* Banner image upload */

	$wp_customize->add_setting('traversify_lite_theme_options[banner_image]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['banner_image'],
	        'sanitize_callback' => 'esc_url_raw',
	    )
	);
	$wp_customize->add_control(
	    new WP_Customize_Image_Control(
	        $wp_customize,
	        'traversify_lite_theme_options[banner_image]',
	        array(
	            'label'           => esc_html__( 'Add Banner Image', 'traversify-lite' ),
	            'section'         => 'banner_section',
	            'settings'        => 'traversify_lite_theme_options[banner_image]',
	        ) )
	);

/**
** Blog options
*/
 			/* Blog title */
	$wp_customize->add_setting('traversify_lite_theme_options[blog_title]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['blog_title'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[blog_title]',
	    array(
	        'label' => esc_html__('Title', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 1.1,
	        'section' => 'blog_section',
	       
	    )
	);
/* blog description */

	$wp_customize->add_setting('traversify_lite_theme_options[blog_description]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['blog_description'],
	        'sanitize_callback' => 'sanitize_text_field',
	    )
	);
	$wp_customize->add_control('traversify_lite_theme_options[blog_description]',
	    array(
	        'label' => esc_html__('Description', 'traversify-lite'),
	        'type' => 'text',
	        'priority' => 2,
	        'section' => 'blog_section',
	        
	    )
	);
	/* checkbox */
	$wp_customize->add_setting('traversify_lite_theme_options[blog_checkbox]',
	    array(
	        'type' => 'option',
	        'default' => $traversify_lite_default['blog_checkbox'],
	        'sanitize_callback' => 'traversify_lite_sanitize_checkbox',
	    )
	);

	$wp_customize->add_control('traversify_lite_theme_options[blog_checkbox]',
	    array(
	        'label' => esc_html__('Check To Hide', 'traversify-lite'),
	        'description' => esc_html__('Show/Hide Blog Section','traversify-lite'),
	        'type' => 'Checkbox',
	        'priority' => 1,
	        'section' => 'blog_section',
	        
	    )
	);

	/* blog category*/

	$wp_customize->add_setting( 'traversify_lite_theme_options[blog_category]', array(
	            'default'           => $traversify_lite_default['blog_category'],
	            'type' 				=> 'option',
	            'sanitize_callback' => 'traversify_lite_sanitize_select',
	        ) );

	$wp_customize->add_control( new Traversify_lite_Category_dropdown_control( $wp_customize, 'traversify_lite_theme_options[blog_category]', array(
	        'label'       => __( 'Choose category','traversify-lite' ),
	        'section'     => 'blog_section',
	        'priority'    => 3,

	    ) ) );

