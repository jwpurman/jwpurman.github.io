<?php

if (!function_exists('traversify_lite_theme_options')) :
    function traversify_lite_theme_options()
    {
        global $display_name , $user_email;
        $epuser = '';
        if(is_email($user_email) && $user_email){
            $epuser = $user_email;
        }
        $defaults = array(

        	'destination_title' 		=> '',
        	'destination_description'   => '',
        	'destination_checkbox'      => '',
            /*  Trip section default options*/
            'trip_title'                => '',
            'trip_description'          => '',
            'trip_number'               => '',
            'trip_checkbox'             => '',
            /* Banner otions */
            'banner_title'              => '',
            'banner_description'        => '',
            'banner_image'              => '',
            /* Blog Section*/
            'blog_title'                => '',
            'blog_description'          => '',
            'blog_category'             => '',
            'blog_checkbox'              => '',
            /* About Us Section */
            'about_us_title'            => '',
            'about_us_description'      => '',
            'about_us_checkbox'         => '',
            'about_us_image'            => '',
            /* CTA section */
            'cta_title'                 => '',
            'cta_description'           => '',
            'cta_checkbox'              => '',
            'cta_button_title'          => '',
            'cta_button_link'           => '',
            'cta_image'                 => '',


        	);

        $options = get_option('traversify_lite_theme_options', $defaults);

        //Parse defaults again - see comments
        $options = wp_parse_args($options, $defaults);

        return $options;
    }
endif;