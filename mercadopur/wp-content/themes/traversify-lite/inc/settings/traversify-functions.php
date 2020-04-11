<?php
/**
* Cusom Functions 
*/

if( ! function_exists( 'traversify_lite_currenct_sumbol' ) ) :
function traversify_lite_currenct_sumbol(){
    $currency = '';
    if( class_exists('Wp_Travel_Engine_Functions') ){
        $traversify_lite_setting = get_option( 'wp_travel_engine_settings', true ); 
        $symbol = 'USD';
        if( isset( $traversify_lite_setting['currency_code'] ) && $traversify_lite_setting['currency_code']!= '' ){
            $symbol = $traversify_lite_setting['currency_code'];
        } 
        $obj = new Wp_Travel_Engine_Functions();
        $currency = $obj->wp_travel_engine_currencies_symbol( $symbol );
    }
    return $currency;
}
endif;

/**
* For Post Format
*/

if (! function_exists('traversify_lite_blog_post_format')) {
    function traversify_lite_blog_post_format($post_format, $post_id) {

        if (is_single()){
            $single_post_format_class = 'single-post-format';
        } else {
            $single_post_format_class = '';
        }

        if($post_format == 'video'){ ?>
            <div class="post-video <?php echo esc_attr($single_post_format_class);?>">
                <div class="post-video-holder">
                    <?php
                        $content = trim(  get_post_field('post_content', $post_id) );
                        $new_content =  preg_match_all("/\[[^\]]*\]/", $content, $matches);
                        if( $new_content){
                            echo do_shortcode( $matches[0][0] );
                        }
                        else{
                            echo esc_html( traversify_lite_the_featured_video( $content ) );
                        }
                    ?>
                </div>
            </div>
        <?php
        }
        elseif($post_format == 'audio'){
            ?>
                <div class="post-audio <?php echo esc_attr($single_post_format_class);?>">
                    <div class="post-audio-holder">
                        <?php
                            $content = trim(  get_post_field('post_content', $post_id) );
                              $ori_url = explode( "\n", esc_html( $content ) );
                            $new_content =  preg_match_all("/\[[^\]]*\]/", $content, $matches);
                            if( $new_content){
                                echo do_shortcode( $matches[0][0] );
                            }
                            elseif (preg_match ( '#^<(script|iframe|embed|object)#i', $content )) {
                                $regex = '/https?\:\/\/[^\" ]+/i';
                                preg_match_all($regex, $ori_url[0], $matches);
                                $urls = ($matches[0]);
                                 print_r('<iframe src="'.$urls[0].'" width="100%" height="240" frameborder="no" scrolling="no"></iframe>');
                            } elseif (0 === strpos( $content, 'https://' )) {
                                $embed_url = wp_oembed_get( esc_url( $ori_url[0] ) );
                                print_r($embed_url);
                            }
                        ?>
                    </div>
                </div>
            <?php
        }
        elseif ($post_format == 'gallery') {
            //Get the alt and title of the image
                $post_thumbnail_id = get_post_thumbnail_id( $post_id );
                $attachment =  get_post($post_thumbnail_id);
                $gallery = get_post_gallery( $post_id, false );
                 $ids = explode( ",", $gallery['ids'] );
                        if( $ids ) {
                            ?>
                        <div class="post-gallery">
                            <?php foreach ( $ids as $key => $images ) {
                            $link   = wp_get_attachment_url( $images ); ?>
                                <div class="post-gallery-item">
                                    <div class="post-gallery-item-holder" style="background-image: url('<?php echo esc_url( $link); ?>');" alt= "<?php echo esc_attr( $attachment->post_excerpt );?>">
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    <?php

            }
        }
        else
        {
                    if( has_post_thumbnail()) { ?>
                        <div class="post-image-content">
                            <figure class="post-featured-image">
                                <a href="<?php the_permalink();?>" title="<?php echo the_title_attribute('echo=0'); ?>">
                                <?php the_post_thumbnail('blog-image'); ?>
                                </a>
                            </figure><!-- end.post-featured-image  -->
                        </div> <!-- end.post-image-content -->
        <?php
                    }

        }
}
}

/**
* for featured videos
*/

if ( ! function_exists( 'traversify_lite_the_featured_video' ) ) {
    function traversify_lite_the_featured_video( $content ) {

        $ori_url = explode( "\n", esc_html( $content ) );
        $url = esc_url( $ori_url[0] );

        $w = get_option( 'embed_size_w' );
        if ( !is_single() )
            $url = str_replace( '448', $w, $url );

        if ( 0 === strpos( $url, 'https://' ) ) {
            $embed_url = wp_oembed_get( esc_url( $url ) );
            print_r($embed_url);
            $content = trim( str_replace( $url, '', esc_html( $content ) ) );
        }
        elseif ( preg_match ( '#^<(script|iframe|embed|object)#i', $url ) ) {
            $h = get_option( 'embed_size_h' );
            echo esc_url( $url );
            if ( !empty( $h ) ) {

                if ( $w === $h ) $h = ceil( $w * 0.75 );
                $url = preg_replace(
                    array( '#height="[0-9]+?"#i', '#height=[0-9]+?#i' ),
                    array( sprintf( 'height="%d"', $h ), sprintf( 'height=%d', $h ) ),
                    $url
                    );
                echo esc_url( $url );
            }

            $content = trim( str_replace( $url, '', $content ) );
        }
    }
}

/**
* For ecxerpt length
*/

if (!function_exists('traversify_lite_get_excerpt')) :
    function traversify_lite_get_excerpt($post_id, $count)
    {
        $title = get_the_title($post_id);
        $content_post = get_post($post_id);
        $excerpt = $content_post->post_content;

        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);


        $excerpt = preg_replace('/\s\s+/', ' ', $excerpt);
        $excerpt = preg_replace('#\[[^\]]+\]#', ' ', $excerpt);
        $strip = explode(' ', $excerpt);
        foreach ($strip as $key => $single) {
            if (!filter_var($single, FILTER_VALIDATE_URL) === false) {
                unset($strip[$key]);
            }
        }
        $excerpt = implode(' ', $strip);

        $excerpt = substr($excerpt, 0, $count);
        if (strlen($excerpt) >= $count) {
            $excerpt = substr($excerpt, 0, strripos($excerpt, ' '));
            $excerpt = $excerpt . '...';
        }
        if($title)
            return $excerpt;
        else
            return '<a href="'.esc_url(get_the_permalink($post_id)).'">'.$excerpt.'</a>';

    }
endif;


if( ! function_exists( 'traversify_lite_content_wrap' ) ) :
/**
 * Content wrap Start
*/
function traversify_lite_content_wrap(){
    
  
    
    if( !( is_front_page() && ! is_home()) ){
    ?>
        <div class="container">
            <div class="row">
            
    <?php
    }
}
endif;
add_action( 'traversify_lite_wrap', 'traversify_lite_content_wrap' );

if( ! function_exists( 'traversify_lite_content_wrap_footer' ) ) :
/**
 * Content wrap end 
*/
function traversify_lite_content_wrap_footer(){
    
  
    
    if( !( is_front_page() && ! is_home()) ){
    ?>
        </div>
    </div>
            
    <?php
    }
}
    endif;
add_action( 'traversify_lite_wrap_footer', 'traversify_lite_content_wrap_footer' );

add_image_size('traversify-lite-destination-archive-img', 600,800,true);
add_image_size('traversify-lite-activites-archive-img', 600,800,true);
add_image_size('traversify-lite-triptype-archive-img', 600,800,true);

/**
 * destination archive image
*/
if( ! function_exists( 'traversify_lite_archive_image' ) ) :
function traversify_lite_archive_image(){
    return 'traversify-lite-destination-archive-img';
}
endif;
add_action('wp_travel_engine_destination_img_size','traversify_lite_archive_image');

/**
 * activites archive image
*/
if( ! function_exists( 'traversify_lite_archive_activity_image' ) ) :
function traversify_lite_archive_activity_image(){
    return 'traversify-lite-activites-archive-img';
}
endif;
add_action('wp_travel_engine_activities_img_size','traversify_lite_archive_activity_image');
/**
 * triptype archive image
*/
if( ! function_exists( 'traversify_lite_archive_triptype_image' ) ) :
function traversify_lite_archive_triptype_image(){
    return 'traversify-lite-triptype-archive-img';
}
endif;
add_action('wp_travel_engine_archive_trip_feat_img_size','traversify_lite_archive_triptype_image');
/*
* for wp page menue
*/
if (! function_exists('traversify_lite_wp_page_menu')) {
    add_filter('wp_page_menu', 'traversify_lite_wp_page_menu');
    function traversify_lite_wp_page_menu($page_markup) {
        preg_match('/^<div class=\"([a-z0-9-_]+)\">/i', $page_markup, $matches);
        $divclass   = $matches[1];
        $replace    = array('<div class="'.$divclass.'">', '</div>');
        $new_markup = str_replace($replace, '', $page_markup);
        $new_markup = preg_replace('/^<ul>/i', '<ul class="'.$divclass.'">', $new_markup);
        return $new_markup;
    }
}


/********************* Used wp_page_menu filter hook *********************************************/
if (! function_exists('traversify_lite_wp_page_menu_filter')) {
    function traversify_lite_wp_page_menu_filter($text) {
        $replace = array(
            'current_page_item' => 'current-menu-item',
        );
        $text = str_replace(array_keys($replace), $replace, $text);
        return $text;
    }
    add_filter('wp_page_menu', 'traversify_lite_wp_page_menu_filter');
}
