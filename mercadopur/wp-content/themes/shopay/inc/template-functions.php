<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Shopay
 */

/*-----------------------------------------------------------------------------------------------------------------------*/
/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function shopay_body_classes( $classes ) {
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }

    $shopay_site_layout = get_theme_mod( 'shopay_site_layout', 'full-width' );
    $classes[] = 'site--'.esc_attr( $shopay_site_layout );

    if ( is_single() ) {
        $shopay_post_sidebar_layout = get_theme_mod( 'shopay_post_sidebar_layout', 'right-sidebar' );
        $classes[] = esc_attr( $shopay_post_sidebar_layout );
    }

    if ( is_page() ) {
        $shopay_page_sidebar_layout = get_theme_mod( 'shopay_page_sidebar_layout', 'right-sidebar' );
        $classes[] = esc_attr( $shopay_page_sidebar_layout );
    }

    if ( is_archive() || is_front_page() ) {
        $shopay_archive_sidebar_layout = get_theme_mod( 'shopay_archive_sidebar_layout', 'right-sidebar' );
        $classes[] = esc_attr( $shopay_archive_sidebar_layout );
    }

    return $classes;
}
add_filter( 'body_class', 'shopay_body_classes' );

/*-----------------------------------------------------------------------------------------------------------------------*/

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function shopay_pingback_header() {
    if ( is_singular() && pings_open() ){
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'shopay_pingback_header' );

/*-----------------------------------------------------------------------------------------------------------------------*/

/**
 * Enqueue admin scripts and styles.
 */
function shopay_admin_scripts(){
    global $shopay_theme_version;

    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/library/font-awesome/css/all.min.css', '', '5.10.2', 'all' );
    wp_enqueue_style( 'shopay-admin-style', get_template_directory_uri() . '/assets/css/mt-admin-style.css', '', esc_attr( $shopay_theme_version ), 'all' );

    wp_enqueue_script( 'shopay-admin-script', get_template_directory_uri() . '/assets/js/mt-admin-scripts.js', array( 'jquery' ), esc_attr( $shopay_theme_version ), true );
}
add_action( 'admin_enqueue_scripts', 'shopay_admin_scripts' );

/*-----------------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'shopay_fonts_url' ) ):

    /**
     * Register Google fonts for Shopay.
     *
     * @return string Google fonts URL for the theme.
     * @since 1.0.0
     */

    function shopay_fonts_url() {

        $fonts_url = '';
        $font_families = array();

        /**
         * Translators: If there are characters in your language that are not supported
         * by Muli font, translate this to 'off'. Do not translate into your own language.
         */
        if ( 'off' !== _x( 'on', 'Muli font: on or off', 'shopay' ) ) {
            $font_families[] = 'Muli:400,500,600,900';
        }

        /**
         * Translators: If there are characters in your language that are not supported
         * by Rubik font, translate this to 'off'. Do not translate into your own language.
         */
        if ( 'off' !== _x( 'on', 'Rubik font: on or off', 'shopay' ) ) {
            $font_families[] = 'Rubik:500,700';
        }

        if ( $font_families ) {
            $query_args = array(
                'family' => urlencode( implode( '|', $font_families ) ),
                'subset' => urlencode( 'latin,latin-ext' ),
            );

            $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
        }

        return $fonts_url;
    }

endif;

/*-----------------------------------------------------------------------------------------------------------------------*/

/**
 * Enqueue scripts and styles.
 */
function shopay_scripts() {
    global $shopay_theme_version;

    wp_enqueue_style( 'shopay-fonts', shopay_fonts_url(), array(), null );
    
    wp_enqueue_style( 'preloader-style', get_template_directory_uri() . '/assets/css/mt-preloader.css', array(), esc_attr( $shopay_theme_version ) );
    
    wp_enqueue_style( 'lightslider-style', get_template_directory_uri() . '/assets/library/lightslider/css/lightslider.min.css', array(), '1.1.3' );

    wp_register_style( 'font-awesome', get_template_directory_uri() . '/assets/library/font-awesome/css/all.min.css', '', '5.10.2', 'all' );

    wp_enqueue_style( 'font-awesome' );
    
    wp_enqueue_style( 'animate', get_template_directory_uri() . '/assets/library/animate/animate.css', '', '3.7.2', 'all' );
    
    wp_enqueue_style( 'shopay-style', get_stylesheet_uri(), '',esc_attr( $shopay_theme_version ) );

    wp_enqueue_style( 'shopay-responsive-style', get_template_directory_uri(). '/assets/css/responsive.css', array(), esc_attr( $shopay_theme_version) );

    wp_enqueue_script( 'shopay-combine', get_template_directory_uri() . '/assets/js/mt-combine-scripts.js', array( 'jquery' ), esc_attr( $shopay_theme_version ), true );
    
    wp_enqueue_script( 'shopay-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), esc_attr( $shopay_theme_version ), true );
    
    wp_enqueue_script( 'shopay-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), esc_attr( $shopay_theme_version ), true );

    $shopay_sticky_menu_option = get_theme_mod( 'shopay_sticky_menu_option', true );
    if ( true === $shopay_sticky_menu_option ) {
        $sticky_value = 'on';
        wp_enqueue_script( 'header-sticky-scripts', get_template_directory_uri() . '/assets/library/sticky/jquery.sticky.min.js', array(), '1.0.4', true );
    } else {
        $sticky_value = 'off';
    }

    $shopay_sticky_sidebar_option = get_theme_mod( 'shopay_sticky_sidebar_option', true );
    if ( true === $shopay_sticky_sidebar_option ) {
        $sidebar_sticky = 'on';
        wp_enqueue_script( 'thia-sticky-sidebar', get_template_directory_uri().'/assets/library/sticky-sidebar/theia-sticky-sidebar.min.js', array(), '1.7.0', true );
    } else {
        $sidebar_sticky = 'off';
    }
    
    $shopay_wow_option = get_theme_mod( 'shopay_wow_option', true );
    if ( true === $shopay_wow_option ) {
        $wow_option = 'on';
        wp_enqueue_script( 'shopay-wow', get_template_directory_uri() . '/assets/library/wow/wow.js', array( 'jquery' ), '1.1.3', true );
    } else {
        $wow_option = 'off';
    }

    wp_enqueue_script( 'shopay-woocommerce', get_template_directory_uri() . '/assets/js/mt-custom-woocommerce.js', array( 'jquery' ), esc_attr( $shopay_theme_version ), true );
    
    wp_enqueue_script( 'shopay-custom', get_template_directory_uri() . '/assets/js/mt-custom-scripts.js', array( 'jquery' ), esc_attr( $shopay_theme_version ), true );
    
    wp_localize_script( 'shopay-custom', 'shopayObject', array(
        'sidebar_sticky'=> $sidebar_sticky,
        'header_sticky' => $sticky_value,
        'wow_option'    => $wow_option
    ) );
    
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'shopay_scripts' );

/*-----------------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'shopay_social_media' ) ):

    /**
     * Social Icons classes from customizer field.
     */
    function shopay_social_media(){
        $get_shopay_social_icons          = get_theme_mod( 'shopay_social_media', '' );
        $get_decode_shopay_social_icons   = json_decode( $get_shopay_social_icons );
        if ( empty( $get_decode_shopay_social_icons ) ){
            return;
        }
        $social_link_target = get_theme_mod( 'shopay_social_link_target', 'new' );
        if ( 'new' == $social_link_target ) {
            $target = '_blank';
        } else {
            $target = '_self';
        }
    ?>
        <div id="shopay-follow-icons-wrapper">
            <?php 
                foreach ( $get_decode_shopay_social_icons as $key => $value ) {
                    $social_icon_class  = $value->mt_item_icon;
                    $social_icon_url    = $value->mt_item_link;
                    echo '<div class="follow-us-icon"><a href="'.esc_url( $social_icon_url ).'" target="'. esc_attr( $target ) .'"><i class="'.esc_attr( $social_icon_class ).'" aria-hidden="true"></i></a></div>';
                }
            ?>
        </div><!-- .shopay-follow-icons-wrapper -->
<?php
    }

endif;

/*-----------------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'shopay_breadcrumbs' ) ) :

    /**
     * Function for shopay breadcrumbs
     */
    function shopay_breadcrumbs() {
        $shopay_breadcrumbs = get_theme_mod( 'shopay_breadcrumbs', true );
        if ( false == $shopay_breadcrumbs || is_front_page() ) {
            return;
        }
        
        if ( shopay_is_active_woocommerce() ) {
            $bread_home_text = get_theme_mod( 'shopay_breadcrumbs_home_lable', __( 'Home', 'shopay' ) );
            $args = array (
                'wrap_before'   => '<div class="woocommerce-breadcrumbs"> <div class="mt-container"> <div class="woocommerce-breadcrumbs-wrapper">',
                'wrap_after'    => '</div> </div> </div>',
                'home'          => $bread_home_text
            );
            woocommerce_breadcrumb( $args );
        } else {
            if ( ! function_exists( 'breadcrumb_trail' ) ) {
                require_once get_template_directory() . '/inc/class-breadcrumbs.php';
            }

            $breadcrumb_args = array (
                'container'   => 'div',
                'before'      => '<div class="mt-container">',
                'after'       => '</div>',
                'show_browse' => false,
            );
            breadcrumb_trail( $breadcrumb_args );
        }
    }

endif;

add_action( 'shopay_after_header', 'shopay_breadcrumbs', 10 );

/*---------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'shopay_breadcrumbs_labels' ) ) :

    /**
     * Custom breadcrumbs labels
     *
     * @since 1.0.0
     */
    function shopay_breadcrumbs_labels( $defaults ) {

        $defaults['home'] = get_theme_mod( 'shopay_breadcrumbs_home_lable', __( 'Home', 'shopay' ) );

        return $defaults;
    }

endif;

add_filter( 'breadcrumb_trail_labels', 'shopay_breadcrumbs_labels' );
/*-----------------------------------------------------------------------------------------------------------------------*/
if ( !function_exists( 'shopay_category_menu_sec' ) ) :

    /**
     * Function for displaying content of category menu. 
     */
    function shopay_category_menu_sec() {

        $cat_menu_args = array(
            'taxonomy'      => 'product_cat',
            'title_li'      => '',
            'hierarchical'  => true,
            'hide_empty'    => '1',
        );
        echo '<ul class="product-categories">';
            wp_list_categories( apply_filters( 'shopay_slider_cat_list', $cat_menu_args ) );
        echo '</ul>';

    }

endif;

/*-----------------------------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'shopay_css_strip_whitespace' ) ) :
    
    /**
     * Get minified css and removed space
     *
     * @since 1.0.0
     */

    function shopay_css_strip_whitespace( $css ) {
        $replace = array(
            "#/\*.*?\*/#s" => "",  // Strip C style comments.
            "#\s\s+#"      => " ", // Strip excess whitespace.
        );
        $search = array_keys( $replace );
        $css = preg_replace( $search, $replace, $css );

        $replace = array(
            ": "  => ":",
            "; "  => ";",
            " {"  => "{",
            " }"  => "}",
            ", "  => ",",
            "{ "  => "{",
            ";}"  => "}", // Strip optional semicolons.
            ",\n" => ",", // Don't wrap multiple selectors.
            "\n}" => "}", // Don't wrap closing braces.
            "} "  => "}\n", // Put each rule on it's own line.
        );

        $search = array_keys( $replace );
        $css = str_replace( $search, $replace, $css );

        return trim( $css );
    }

endif;

/*-----------------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'shopay_font_awesome_icon_array' ) ) :

    /**
     * Define font awesome icons
     *
     * @return array();
     * @since 1.0.0
     */
    function shopay_font_awesome_icon_array() {

        $icon_array = array("fab fa-buromobelexperte","fas fa-burn","fas fa-bullseye","fas fa-bullhorn","fas fa-building","far fa-building","fas fa-bug","fab fa-btc","fas fa-briefcase-medical","fas fa-briefcase","fas fa-braille","fas fa-boxes","fas fa-box-open","fas fa-box","fas fa-bowling-ball","fas fa-bookmark","far fa-bookmark","fas fa-book","fas fa-bomb","fas fa-bolt","fas fa-bold","fab fa-bluetooth-b","fab fa-bluetooth","fab fa-blogger-b","fab fa-blogger","fas fa-blind","fab fa-blackberry","fab fa-black-tie","fab fa-bity","fab fa-bitcoin","fab fa-bitbucket","fas fa-birthday-cake","fas fa-binoculars","fab fa-bimobject","fas fa-bicycle","fas fa-bell-slash","far fa-bell-slash","fas fa-bell","far fa-bell","fab fa-behance-square","fab fa-behance","fas fa-beer","fas fa-bed","fas fa-battery-three-quarters","fas fa-battery-quarter","fas fa-battery-half","fas fa-battery-full","fas fa-battery-empty","fas fa-bath","fas fa-basketball-ball","fas fa-baseball-ball","fas fa-bars","fas fa-barcode","fab fa-bandcamp","fas fa-band-aid","fas fa-ban","fas fa-balance-scale","fas fa-backward","fab fa-aws","fab fa-aviato","fab fa-avianex","fab fa-autoprefixer","fas fa-audio-description","fab fa-audible","fas fa-at","fab fa-asymmetrik","fas fa-asterisk","fas fa-assistive-listening-systems","fas fa-arrows-alt-v","fas fa-arrows-alt-h","fas fa-arrows-alt","fas fa-arrow-up","fas fa-arrow-right","fas fa-arrow-left","fas fa-arrow-down","fas fa-arrow-circle-up","fas fa-arrow-circle-right","fas fa-arrow-circle-left","fas fa-arrow-circle-down","fas fa-arrow-alt-circle-up","far fa-arrow-alt-circle-up","fas fa-arrow-alt-circle-right","far fa-arrow-alt-circle-right","fas fa-arrow-alt-circle-left","far fa-arrow-alt-circle-left","fas fa-arrow-alt-circle-down","far fa-arrow-alt-circle-down","fas fa-archive","fab fa-apple-pay","fab fa-apple","fab fa-apper","fab fa-app-store-ios","fab fa-app-store","fab fa-angular","fab fa-angrycreative","fas fa-angle-up","fas fa-angle-right","fas fa-angle-left","fas fa-angle-down","fas fa-angle-double-up","fas fa-angle-double-right","fas fa-angle-double-left","fas fa-angle-double-down","fab fa-angellist","fab fa-android","fas fa-anchor","fab fa-amilia","fas fa-american-sign-language-interpreting","fas fa-ambulance","fab fa-amazon-pay","fab fa-amazon","fas fa-allergies","fas fa-align-right","fas fa-align-left","fas fa-align-justify","fas fa-align-center","fab fa-algolia","fab fa-affiliatetheme","fab fa-adversal","fab fa-adn","fas fa-adjust","fas fa-address-card","far fa-address-card","fas fa-address-book","far fa-address-book","fab fa-accusoft","fab fa-accessible-icon","fab fa-500px","fab fa-youtube-square","fab fa-youtube","fab fa-yoast","fas fa-yen-sign","fab fa-yelp","fab fa-yandex-international","fab fa-yandex","fab fa-yahoo","fab fa-y-combinator","fab fa-zhihu","fab fa-xing-square","fab fa-xing","fab fa-xbox","fas fa-x-ray","fas fa-wrench","fab fa-wpforms","fab fa-wpexplorer","fab fa-wpbeginner","fab fa-wordpress-simple","fab fa-wordpress","fas fa-won-sign","fab fa-wolf-pack-battalion","fas fa-wine-glass","fab fa-wix","fab fa-wizards-of-the-coast","fab fa-windows","fas fa-window-restore","far fa-window-restore","fas fa-window-minimize","far fa-window-minimize","fas fa-window-maximize","far fa-window-maximize","fas fa-window-close","far fa-window-close","fab fa-wikipedia-w","fas fa-wifi","fab fa-whmcs","fas fa-wheelchair","fab fa-whatsapp-square","fab fa-whatsapp","fab fa-weixin","fas fa-weight","fab fa-weibo","fas fa-warehouse","fab fa-vuejs","fas fa-volume-up","fas fa-volume-off","fas fa-volume-down","fas fa-volleyball-ball","fab fa-vnv","fab fa-vk","fab fa-vine","fab fa-vimeo-v","fab fa-vimeo-square","fab fa-vimeo","fas fa-video-slash","fas fa-video","fab fa-viber","fas fa-vials","fas fa-vial","fab fa-viadeo-square","fab fa-viadeo","fab fa-viacoin","fas fa-venus-mars","fas fa-venus-double","fas fa-venus","fab fa-vaadin","fas fa-utensils","fas fa-utensil-spoon","fab fa-ussunnah","fas fa-users-cog","fas fa-users","fas fa-user-times","fas fa-user-tie","fas fa-user-tag","fas fa-user-slash","fas fa-user-shield","fas fa-user-secret","fas fa-user-plus","fas fa-user-ninja","fas fa-user-minus","fas fa-user-md","fas fa-user-lock","fas fa-user-graduate","fas fa-user-friends","fas fa-user-edit","fas fa-user-cog","fas fa-user-clock","fas fa-user-circle","far fa-user-circle","fas fa-user-check","fas fa-user-astronaut","fas fa-user-alt-slash","fas fa-user-alt","fas fa-user","far fa-user","fab fa-usb","fas fa-upload","fab fa-untappd","fas fa-unlock-alt","fas fa-unlock","fas fa-unlink","fas fa-university","fas fa-universal-access","fab fa-uniregistry","fas fa-undo-alt","fas fa-undo","fas fa-underline","fas fa-umbrella","fab fa-uikit","fab fa-uber","fab fa-typo3","fab fa-twitter-square","fab fa-twitter","fab fa-twitch","fas fa-tv","fab fa-tumblr-square","fab fa-tumblr","fas fa-tty","fas fa-truck-moving","fas fa-truck-loading","fas fa-truck","fas fa-trophy","fab fa-tripadvisor","fab fa-trello","fas fa-tree","fas fa-trash-alt","far fa-trash-alt","fas fa-trash","fas fa-transgender-alt","fas fa-transgender","fas fa-train","fas fa-trademark","fab fa-trade-federation","fas fa-toggle-on","fas fa-toggle-off","fas fa-tint","fas fa-times-circle","far fa-times-circle","fas fa-times","fas fa-ticket-alt","fas fa-thumbtack","fas fa-thumbs-up","far fa-thumbs-up","fas fa-thumbs-down","far fa-thumbs-down","fas fa-thermometer-three-quarters","fas fa-thermometer-quarter","fas fa-thermometer-half","fas fa-thermometer-full","fas fa-thermometer-empty","fas fa-thermometer","fab fa-themeisle","fas fa-th-list","fab fa-the-red-yeti","fas fa-th-large","fas fa-th","fas fa-text-width","fas fa-text-height","fas fa-terminal","fab fa-tencent-weibo","fab fa-telegram-plane","fab fa-telegram","fab fa-teamspeak","fas fa-taxi","fas fa-tasks","fas fa-tape","fas fa-tags","fas fa-tag","fas fa-tachometer-alt","fas fa-tablets","fas fa-tablet-alt","fas fa-tablet","fas fa-table-tennis","fas fa-table","fas fa-syringe","fas fa-sync-alt","fas fa-sync","fas fa-subway","fas fa-suitcase","fas fa-suitcase-rolling","fas fa-sun","far fa-sun","fab fa-superpowers","fas fa-superscript","fab fa-supple","fas fa-surprise","far fa-surprise","fas fa-swatchbook","fas fa-swimmer","fas fa-swimmer-pool","fas fa-synagogue","fab fa-stumbleupon-circle","fab fa-stumbleupon","fab fa-studiovinari","fab fa-stripe-s","fab fa-stripe","fas fa-strikethrough","fas fa-street-view","fab fa-strava","fas fa-stopwatch","fas fa-stop-circle","far fa-stop-circle","fas fa-stop","fas fa-sticky-note","far fa-sticky-note","fab fa-sticker-mule","fas fa-stethoscope","fas fa-step-forward","fas fa-step-backward","fab fa-steam-symbol","fab fa-steam-square","fab fa-steam","fab fa-staylinked","fas fa-star-half","far fa-star-half","fas fa-star","far fa-star","fab fa-stack-overflow","fab fa-stack-exchange","fas fa-square-full","fas fa-square","far fa-square","fab fa-spotify","fas fa-spinner","fab fa-speakap","fas fa-space-shuttle","fab fa-soundcloud","fas fa-sort-up","fas fa-sort-numeric-up","fas fa-sort-numeric-down","fas fa-sort-down","fas fa-sort-amount-up","fas fa-sort-amount-down","fas fa-sort-alpha-up","fas fa-sort-alpha-down","fas fa-sort","fas fa-snowflake","far fa-snowflake","fab fa-snapchat-square","fab fa-snapchat-ghost","fab fa-snapchat","fas fa-smoking","fas fa-smile","far fa-smile","fab fa-slideshare","fas fa-sliders-h","fab fa-slack-hash","fab fa-slack","fab fa-skype","fab fa-skyatlas","fab fa-sith","fas fa-sitemap","fab fa-sistrix","fab fa-simplybuilt","fas fa-signal","fas fa-sign-out-alt","fas fa-sign-language","fas fa-sign-in-alt","fas fa-sign","fas fa-shower","fas fa-shopping-cart","fas fa-shopping-basket","fas fa-shopping-bag","fab fa-shirtsinbulk","fas fa-shipping-fast","fas fa-ship","fas fa-shield-alt","fas fa-shekel-sign","fas fa-share-square","far fa-share-square","fas fa-share-alt-square","fas fa-share-alt","fas fa-share","fab fa-servicestack","fas fa-server","fab fa-sellsy","fab fa-sellcast","fas fa-seedling","fab fa-searchengin","fas fa-search-plus","fas fa-search-minus","fas fa-search","fab fa-scribd","fab fa-schlix","fas fa-save","far fa-save","fab fa-sass","fab fa-safari","fas fa-rupee-sign","fas fa-ruble-sign","fas fa-rss-square","fas fa-rss","fab fa-rockrms","fab fa-rocketchat","fas fa-rocket","fas fa-road","fas fa-ribbon","fas fa-retweet","fab fa-resolving","fab fa-researchgate","fab fa-replyd","fas fa-reply-all","fas fa-reply","fab fa-renren","fab fa-rendact","fas fa-registered","far fa-registered","fas fa-redo-alt","fas fa-redo","fab fa-reddit-square","fab fa-reddit-alien","fab fa-reddit","fab fa-red-river","fas fa-recycle","fab fa-rebel","fab fa-readme","fab fa-react","fab fa-ravelry","fas fa-random","fab fa-r-project","fas fa-quote-right","fas fa-quote-left","fab fa-quora","fab fa-quinscape","fas fa-quidditch","fas fa-question-circle","far fa-question-circle","fas fa-question","fas fa-qrcode","fab fa-qq","fab fa-python","fas fa-puzzle-piece","fab fa-pushed","fab fa-product-hunt","fas fa-procedures","fas fa-print","fas fa-prescription-bottle-alt","fas fa-prescription-bottle","fas fa-power-off","fas fa-pound-sign","fas fa-portrait","fas fa-poo","fas fa-podcast","fas fa-plus-square","far fa-plus-square","fas fa-plus-circle","fas fa-plus","fas fa-plug","fab fa-playstation","fas fa-play-circle","far fa-play-circle","fas fa-play","fas fa-plane","fab fa-pinterest-square","fab fa-pinterest-p","fab fa-pinterest","fas fa-pills","fas fa-piggy-bank","fab fa-pied-piper-pp","fab fa-pied-piper-hat","fab fa-pied-piper-alt","fab fa-pied-piper","fab fa-php","fas fa-phone-volume","fas fa-phone-square","fas fa-phone-slash","fas fa-phone","fab fa-phoenix-squadron","fab fa-phoenix-framework","fab fa-phabricator","fab fa-periscope","fas fa-percent","fas fa-people-carry","fas fa-pencil-alt","fas fa-pen-square","fab fa-paypal","fas fa-paw","fas fa-pause-circle","far fa-pause-circle","fas fa-pause","fab fa-patreon","fas fa-paste","fas fa-paragraph","fas fa-parachute-box","fas fa-paperclip","fas fa-paper-plane","far fa-paper-plane","fas fa-pallet","fab fa-palfed","fas fa-paint-brush","fab fa-pagelines","fab fa-page4","fas fa-outdent","fab fa-osi","fas fa-otter","fab fa-optin-monster","fab fa-opera","fab fa-openid","fab fa-opencart","fab fa-old-republic","fab fa-odnoklassniki-square","fab fa-odnoklassniki","fas fa-object-ungroup","far fa-object-ungroup","fas fa-object-group","far fa-object-group","fab fa-nutritionix","fab fa-ns8","fab fa-npm","fas fa-notes-medical","fab fa-node-js","fab fa-node","fab fa-nintendo-switch","fas fa-newspaper","far fa-newspaper","fas fa-neuter","fab fa-napster","fas fa-music","fas fa-mouse-pointer","fas fa-motorcycle","fas fa-mountain","fas fa-mosque","fas fa-moon","far fa-moon","fas fa-money-bill-alt","far fa-money-bill-alt","fab fa-monero","fab fa-modx","fas fa-mobile-alt","fas fa-mobile","fab fa-mizuni","fab fa-mixcloud","fab fa-mix","fas fa-minus-square","far fa-minus-square","fas fa-minus-circle","fas fa-minus","fab fa-microsoft","fas fa-microphone-slash","fas fa-microphone","fas fa-microchip","fas fa-mercury","fas fa-meh","far fa-meh","fab fa-meetup","fab fa-medrt","fas fa-medkit","fab fa-medium-m","fab fa-medium","fab fa-medapps","fab fa-maxcdn","fab fa-mastodon","fas fa-mars-stroke-v","fas fa-mars-stroke-h","fas fa-mars-stroke","fas fa-mars-double","fas fa-mars","fas fa-map-signs","fas fa-map-pin","fas fa-map-marker-alt","fas fa-map-marker","fas fa-map","far fa-map","fab fa-mandalorian","fas fa-male","fas fa-magnet","fas fa-magic","fab fa-magento","fab fa-lyft","fas fa-low-vision","fas fa-long-arrow-alt-up","fas fa-long-arrow-alt-right","fas fa-long-arrow-alt-left","fas fa-long-arrow-alt-down","fas fa-lock-open","fas fa-lock","fas fa-location-arrow","fas fa-list-ul","fas fa-list-ol","fas fa-list-alt","far fa-list-alt","fas fa-list","fas fa-lira-sign","fab fa-linux","fab fa-linode","fab fa-linkedin-in","fab fa-linkedin","fas fa-link","fab fa-line","fas fa-lightbulb","far fa-lightbulb","fas fa-life-ring","far fa-life-ring","fas fa-level-up-alt","fas fa-level-down-alt","fab fa-less","fas fa-lemon","far fa-lemon","fab fa-leanpub","fas fa-leaf","fab fa-lastfm-square","fab fa-lastfm","fab fa-laravel","fas fa-laptop","fas fa-language","fab fa-korvue","fas fa-kiwi-bird","fab fa-kiss-wink-heart","fas fa-kiss-wink-heart","fab fa-kiss-beam","fas fa-kiss-beam","fab fa-kiss","fas fa-kiss","fab fa-kickstarter-k","fab fa-kickstarter","fab fa-keycdn","fas fa-keyboard","far fa-keyboard","fab fa-keybase","fas fa-key","fab fa-jsfiddle","fab fa-js-square","fab fa-js","fas fa-journal-whills","fab fa-joomla","fab fa-joget","fab fa-jenkins","fab fa-jedi-order","fas fa-jedi","fab fa-java","fab fa-itunes-note","fab fa-itunes","fas fa-italic","fab fa-ioxhost","fab fa-internet-explorer","fab fa-instagram","fas fa-info-circle","fas fa-info","fas fa-industry","fas fa-indent","fas fa-inbox","fab fa-imdb","fas fa-images","far fa-images","fas fa-image","far fa-image","fas fa-id-card-alt","fas fa-id-card","far fa-id-card","fas fa-id-badge","far fa-id-badge","fas fa-i-cursor","fab fa-hubspot","fab fa-html5","fab fa-houzz","fas fa-hourglass-start","fas fa-hourglass-half","fas fa-hourglass-end","fas fa-hourglass","far fa-hourglass","fab fa-hotjar","fas fa-hospital-symbol","fas fa-hospital-alt","fas fa-hospital","far fa-hospital","fab fa-hooli","fas fa-home","fas fa-hockey-puck","fas fa-history","fab fa-hire-a-helper","fab fa-hips","fas fa-heartbeat","fas fa-heart","far fa-heart","fas fa-headphones","fas fa-heading","fas fa-hdd","far fa-hdd","fas fa-hashtag","fas fa-handshake","far fa-handshake","fas fa-hands-helping","fas fa-hands","fas fa-hand-spock","far fa-hand-spock","fas fa-hand-scissors","far fa-hand-scissors","fas fa-hand-rock","far fa-hand-rock","fas fa-hand-pointer","far fa-hand-pointer","fas fa-hand-point-up","far fa-hand-point-up","fas fa-hand-point-right","far fa-hand-point-right","fas fa-hand-point-left","far fa-hand-point-left","fas fa-hand-point-down","far fa-hand-point-down","fas fa-hand-peace","far fa-hand-peace","fas fa-hand-paper","far fa-hand-paper","fas fa-hand-lizard","far fa-hand-lizard","fas fa-hand-holding-usd","fas fa-hand-holding-heart","fas fa-hand-holding","fab fa-hacker-news-square","fab fa-hacker-news","fas fa-h-square","fab fa-gulp","fab fa-grunt","fab fa-gripfire","fab fa-grav","fab fa-gratipay","fas fa-graduation-cap","fab fa-google-wallet","fab fa-google-plus-square","fab fa-google-plus-g","fab fa-google-plus","fab fa-google-play","fab fa-google-drive","fab fa-google","fab fa-goodreads-g","fab fa-goodreads","fas fa-golf-ball","fab fa-gofore","fas fa-globe","fab fa-glide-g","fab fa-glide","fas fa-glass-martini","fab fa-gitter","fab fa-gitlab","fab fa-gitkraken","fab fa-github-square","fab fa-github-alt","fab fa-github","fab fa-git-square","fab fa-git","fas fa-gift","fab fa-gg-circle","fab fa-gg","fab fa-get-pocket","fas fa-genderless","fas fa-gem","far fa-gem","fas fa-gavel","fas fa-gamepad","fab fa-galactic-senate","fab fa-galactic-republic","fas fa-futbol","far fa-futbol","fab fa-fulcrum","fas fa-frown","far fa-frown","fab fa-freebsd","fab fa-free-code-camp","fab fa-foursquare","fas fa-forward","fab fa-forumbee","fab fa-fort-awesome-alt","fab fa-fort-awesome","fas fa-football-ball","fab fa-fonticons-fi","fab fa-fonticons","far fa-font-awesome-logo-full","fas fa-font-awesome-logo-full","fab fa-font-awesome-logo-full","fab fa-font-awesome-flag","fab fa-font-awesome-alt","fab fa-font-awesome","fas fa-font","fas fa-folder-open","far fa-folder-open","fas fa-folder","far fa-folder","fab fa-fly","fab fa-flipboard","fab fa-flickr","fas fa-flask","fas fa-flag-checkered","fas fa-flag","far fa-flag","fab fa-firstdraft","fab fa-first-order-alt","fab fa-first-order","fas fa-first-aid","fab fa-firefox","fas fa-fire-extinguisher","fas fa-fire","fas fa-filter","fas fa-film","fas fa-file-word","far fa-file-word","fas fa-file-video","far fa-file-video","fas fa-file-powerpoint","far fa-file-powerpoint","fas fa-file-pdf","far fa-file-pdf","fas fa-file-medical-alt","fas fa-file-medical","fas fa-file-image","far fa-file-image","fas fa-file-excel","far fa-file-excel","fas fa-file-code","far fa-file-code","fas fa-file-audio","far fa-file-audio","fas fa-file-archive","far fa-file-archive","fas fa-file-alt","far fa-file-alt","fas fa-file","far fa-file","fas fa-fighter-jet","fas fa-female","fas fa-fax","fas fa-fast-forward","fas fa-fast-backward","fab fa-facebook-square","fab fa-facebook-messenger","fab fa-facebook-f","fab fa-facebook","fas fa-eye-slash","far fa-eye-slash","fas fa-eye-dropper","fas fa-eye","far fa-eye","fas fa-external-link-square-alt","fas fa-external-link-alt","fab fa-expeditedssl","fas fa-expand-arrows-alt","fas fa-expand","fas fa-exclamation-triangle","fas fa-exclamation-circle","fas fa-exclamation","fas fa-exchange-alt","fas fa-euro-sign","fab fa-etsy","fab fa-ethereum","fab fa-erlang","fas fa-eraser","fab fa-envira","fas fa-envelope-square","fas fa-envelope-open","far fa-envelope-open","fas fa-envelope","far fa-envelope","fab fa-empire","fab fa-ember","fas fa-ellipsis-v","fas fa-ellipsis-h","fab fa-elementor","fas fa-eject","fas fa-edit","far fa-edit","fab fa-edge","fab fa-ebay","fab fa-earlybirds","fab fa-dyalog","fab fa-drupal","fab fa-dropbox","fab fa-dribbble-square","fab fa-dribbble","fab fa-draft2digital","fas fa-download","fas fa-dove","fas fa-dot-circle","far fa-dot-circle","fas fa-donate","fas fa-dolly-flatbed","fas fa-dolly","fas fa-dollar-sign","fab fa-docker","fab fa-dochub","fas fa-dna","fab fa-discourse","fab fa-discord","fab fa-digital-ocean","fab fa-digg","fas fa-diagnoses","fab fa-deviantart","fas fa-desktop","fab fa-deskpro","fab fa-deploydog","fab fa-delicious","fas fa-deaf","fas fa-database","fab fa-dashcube","fab fa-d-and-d","fab fa-cuttlefish","fas fa-cut","fas fa-cubes","fas fa-cube","fab fa-css3-alt","fab fa-css3","fas fa-crosshairs","fas fa-crop","fas fa-credit-card","far fa-credit-card","fab fa-creative-commons-share","fab fa-creative-commons-sampling-plus","fab fa-creative-commons-sampling","fab fa-creative-commons-sa","fab fa-creative-commons-remix","fab fa-creative-commons-pd-alt","fab fa-creative-commons-pd","fab fa-creative-commons-nd","fab fa-creative-commons-nc-jp","fab fa-creative-commons-nc-eu","fab fa-creative-commons-nc","fab fa-creative-commons-by","fab fa-creative-commons","fab fa-cpanel","fas fa-couch","fas fa-copyright","far fa-copyright","fas fa-copy","far fa-copy","fab fa-contao","fab fa-connectdevelop","fas fa-compress","fas fa-compass","far fa-compass","fas fa-comments","far fa-comments","fas fa-comment-slash","fas fa-comment-dots","far fa-comment-dots","fas fa-comment-alt","far fa-comment-alt","fas fa-comment","far fa-comment","fas fa-columns","fas fa-cogs","fas fa-cog","fas fa-coffee","fab fa-codiepie","fab fa-codepen","fas fa-code-branch","fas fa-code","fab fa-cloudversify","fab fa-cloudsmith","fab fa-cloudscale","fas fa-cloud-upload-alt","fas fa-cloud-download-alt","fas fa-cloud","fas fa-closed-captioning","far fa-closed-captioning","fas fa-clone","far fa-clone","fas fa-clock","far fa-clock","fas fa-clipboard-list","fas fa-clipboard-check","fas fa-clipboard","far fa-clipboard","fas fa-circle-notch","fas fa-circle","far fa-circle","fab fa-chrome","fas fa-child","fas fa-chevron-up","fas fa-chevron-right","fas fa-chevron-left","fas fa-chevron-down","fas fa-chevron-circle-up","fas fa-chevron-circle-right","fas fa-chevron-circle-left","fas fa-chevron-circle-down","fas fa-chess-rook","fas fa-chess-queen","fas fa-chess-pawn","fas fa-chess-knight","fas fa-chess-king","fas fa-chess-board","fas fa-chess-bishop","fas fa-chess","fas fa-check-square","far fa-check-square","fas fa-check-circle","far fa-check-circle","fas fa-check","fas fa-chart-pie","fas fa-chart-line","fas fa-chart-bar","far fa-chart-bar","fas fa-chart-area","fas fa-certificate","fab fa-centercode","fab fa-cc-visa","fab fa-cc-stripe","fab fa-cc-paypal","fab fa-cc-mastercard","fab fa-cc-jcb","fab fa-cc-discover","fab fa-cc-diners-club","fab fa-cc-apple-pay","fab fa-cc-amex","fab fa-cc-amazon-pay","fas fa-cart-plus","fas fa-cart-arrow-down","fas fa-caret-up","fas fa-caret-square-up","far fa-caret-square-up","fas fa-caret-square-right","far fa-caret-square-right","fas fa-caret-square-left","far fa-caret-square-left","fas fa-caret-square-down","far fa-caret-square-down","fas fa-caret-right","fas fa-caret-left","fas fa-caret-down","fas fa-car","fas fa-capsules","fas fa-camera-retro","fas fa-camera","fas fa-calendar-times","far fa-calendar-times","fas fa-calendar-plus","far fa-calendar-plus","fas fa-calendar-minus","far fa-calendar-minus","fas fa-calendar-check","far fa-calendar-check","fas fa-calendar-alt","far fa-calendar-alt","fas fa-calendar","far fa-calendar","fas fa-calculator","fab fa-buysellads","fas fa-bus","fab fa-acquisitions-incorporated","fab fa-alipay","fab fa-creative-commons-zero","fab fa-critical-role","fab fa-d-and-d-beyond","fab fa-dev","fab fa-ello","fab fa-fantasy-flight-games","fab fa-hackerrank","fab fa-hornbill","fab fa-kaggle","fab fa-mailchimp","fab fa-markdown","fab fa-megaport","fab fa-neos","fab fa-nimblr","fab fa-penny-arcade","fab fa-r","fab fa-rev","fab fa-shopware","fab fa-squarespace","fab fa-the-red-yeti","fab fa-themeco","fab fa-think-peaks","fab fa-weebly","fab fa-wix","fab fa-wizards-of-the-coast","fab fa-wpressr","fab fa-zhihu","far fa-angry","far fa-dizzy","far fa-flushed","far fa-frown-open","far fa-grimace","far fa-grin-alt","far fa-grin-beam-sweat","far fa-grin-beam","far fa-grin-hearts","far fa-grin-squint-tears","far fa-grin-squint","far fa-grin-stars","far fa-grin-tears","far fa-grin-tongue-squint","far fa-grin-tongue-wink","far fa-grin-tongue","far fa-grin-wink","far fa-grin","far fa-kiss-beam","far fa-kiss-wink-heart","far fa-kiss","far fa-laugh-beam","far fa-laugh-squint","far fa-laugh-wink","far fa-laugh","far fa-meh-blank","far fa-meh-rolling-eyes","far fa-sad-cry","far fa-sad-tear","far fa-smile-beam","far fa-smile-wink","far fa-surprise","far fa-tired","fas fa-abacus","fas fa-ad","fas fa-air-freshener","fas fa-angry","fas fa-ankh","fas fa-apple-alt","fas fa-archway","fas fa-atlas","fas fa-atom","fas fa-award","fas fa-backspace","fas fa-bezier-curve","fas fa-bible","fas fa-blender-phone","fas fa-blender","fas fa-bone","fas fa-bong","fas fa-book-dead","fas fa-book-open","fas fa-book-reader","fas fa-brain","fas fa-broadcast-tower","fas fa-broom","fas fa-brush","fas fa-bus-alt","fas fa-business-time","fas fa-calculator-alt","fas fa-campground","fas fa-cannabis","fas fa-car-alt","fas fa-car-battery","fas fa-car-crash","fas fa-car-side","fas fa-cat","fas fa-chair","fas fa-chalkboard-teacher","fas fa-chalkboard","fas fa-charging-station","fas fa-check-double","fas fa-church","fas fa-city","fas fa-cloud-moon","fas fa-cloud-sun","fas fa-cocktail","fas fa-coins","fas fa-comment-dollar","fas fa-comments-dollar","fas fa-compact-disc","fas fa-concierge-bell","fas fa-cookie-bite","fas fa-cookie","fas fa-crop-alt","fas fa-cross","fas fa-crow","fas fa-crown","fas fa-dharmachakra","fas fa-dice-d20","fas fa-dice-d6","fas fa-dice-five","fas fa-dice-four","fas fa-dice-one","fas fa-dice-six","fas fa-dice-three","fas fa-dice-two","fas fa-dice","fas fa-digital-tachograph","fas fa-directions","fas fa-divide","fas fa-dizzy","fas fa-dog","fas fa-door-closed","fas fa-door-open","fas fa-drafting-compass","fas fa-dragon","fas fa-draw-polygon","fas fa-drum-steelpan","fas fa-drum","fas fa-drumstick-bite","fas fa-dumbbell","fas fa-dungeon","fas fa-empty-set","fas fa-envelope-open-text","fas fa-equals","fas fa-feather-alt","fas fa-feather","fas fa-file-contract","fas fa-file-csv","fas fa-file-download","fas fa-file-export","fas fa-file-import","fas fa-file-invoice-dollar","fas fa-file-invoice","fas fa-file-prescription","fas fa-file-signature","fas fa-file-upload","fas fa-fill-drip","fas fa-fill","fas fa-fingerprint","fas fa-fish","fas fa-fist-raised","fas fa-flushed","fas fa-folder-minus","fas fa-folder-plus","fas fa-frog","fas fa-frown-open","fas fa-function","fas fa-funnel-dollar","fas fa-gas-pump","fas fa-ghost","fas fa-glass-martini-alt","fas fa-glasses","fas fa-globe-africa","fas fa-globe-americas","fas fa-globe-asia","fas fa-gopuram","fas fa-greater-than-equal","fas fa-greater-than","fas fa-grimace","fas fa-grin-alt","fas fa-grin-beam-sweat","fas fa-grin-beam","fas fa-grin-hearts","fas fa-grin-squint-tears","fas fa-grin-squint","fas fa-grin-stars","fas fa-grin-tears","fas fa-grin-tongue-squint","fas fa-grin-tongue-wink","fas fa-grin-tongue","fas fa-grin-wink","fas fa-grin","fas fa-grip-horizontal","fas fa-grip-vertical","fas fa-hammer","fas fa-hamsa","fas fa-hanukiah","fas fa-hat-wizard","fas fa-haykal","fas fa-headphones-alt","fas fa-headset","fas fa-helicopter","fas fa-highlighter","fas fa-hiking","fas fa-hippo","fas fa-horse","fas fa-hot-tub","fas fa-hotel","fas fa-house-damage","fas fa-hryvnia","fas fa-infinity","fas fa-integral","fas fa-intersection","fas fa-jedi","fas fa-joint","fas fa-journal-whills","fas fa-kaaba","fas fa-khanda","fas fa-kiss-beam","fas fa-kiss-wink-heart","fas fa-kiss","fas fa-kiwi-bird","fas fa-lambda","fas fa-landmark","fas fa-laptop-code","fas fa-laugh-beam","fas fa-laugh-squint","fas fa-laugh-wink","fas fa-laugh","fas fa-layer-group","fas fa-less-than-equal","fas fa-less-than","fas fa-luggage-cart","fas fa-mail-bulk","fas fa-map-marked-alt","fas fa-map-marked","fas fa-marker","fas fa-mask","fas fa-medal","fas fa-meh-blank","fas fa-meh-rolling-eyes","fas fa-memory","fas fa-menorah","fas fa-microphone-alt-slash","fas fa-microphone-alt","fas fa-microscope","fas fa-money-bill-wave-alt","fas fa-money-bill-wave","fas fa-money-bill","fas fa-money-check-alt","fas fa-money-check","fas fa-monument","fas fa-mortar-pestle","fas fa-mosque","fas fa-mountain","fas fa-network-wired","fas fa-not-equal","fas fa-oil-can","fas fa-om","fas fa-omega","fas fa-otter","fas fa-paint-roller","fas fa-palette","fas fa-parking","fas fa-passport","fas fa-pastafarianism","fas fa-peace","fas fa-pen-alt","fas fa-pen-fancy","fas fa-pen-nib","fas fa-pen","fas fa-pencil-ruler","fas fa-percentage","fas fa-pi","fas fa-place-of-worship","fas fa-plane-arrival","fas fa-plane-departure","fas fa-poll-h","fas fa-poll","fas fa-poop","fas fa-pray","fas fa-praying-hands","fas fa-prescription","fas fa-project-diagram","fas fa-quran","fas fa-receipt","fas fa-ring","fas fa-robot","fas fa-route","fas fa-ruler-combined","fas fa-ruler-horizontal","fas fa-ruler-vertical","fas fa-ruler","fas fa-running","fas fa-sad-cry","fas fa-sad-tear","fas fa-school","fas fa-screwdriver","fas fa-scroll","fas fa-search-dollar","fas fa-search-location","fas fa-shapes","fas fa-shoe-prints","fas fa-shuttle-van","fas fa-sigma","fas fa-signal-alt-slash","fas fa-signal-alt","fas fa-signal-slash","fas fa-signature","fas fa-skull-crossbones","fas fa-skull","fas fa-slash","fas fa-smile-beam","fas fa-smile-wink","fas fa-smoking-ban","fas fa-socks","fas fa-solar-panel","fas fa-spa","fas fa-spider","fas fa-splotch","fas fa-spray-can","fas fa-square-root-alt","fas fa-square-root","fas fa-stamp","fas fa-star-and-crescent","fas fa-star-half-alt","fas fa-star-of-david","fas fa-star-of-life","fas fa-store-alt","fas fa-store","fas fa-stream","fas fa-stroopwafel","fas fa-suitcase-rolling","fas fa-surprise","fas fa-swatchbook","fas fa-swimmer","fas fa-swimming-pool","fas fa-synagogue","fas fa-tally","fas fa-teeth-open","fas fa-teeth","fas fa-theater-masks","fas fa-theta","fas fa-tilde","fas fa-tint-slash","fas fa-tired","fas fa-toilet-paper","fas fa-toolbox","fas fa-tooth","fas fa-torah","fas fa-torii-gate","fas fa-tractor","fas fa-traffic-light","fas fa-truck-monster","fas fa-truck-pickup","fas fa-tshirt","fas fa-umbrella-beach","fas fa-union","fas fa-user-injured","fas fa-value-absolute","fas fa-vector-square","fas fa-vihara","fas fa-volume-mute","fas fa-volume-slash","fas fa-volume","fas fa-vr-cardboard","fas fa-walking","fas fa-wallet","fas fa-weight-hanging","fas fa-wifi-slash","fas fa-wind","fas fa-wine-bottle","fas fa-wine-glass-alt","fas fa-yin-yang");

        return $icon_array;
    }

endif;

if ( ! function_exists( 'shopay_font_awesome_social_icon_array' ) ) :

    /**
     * Define font awesome social icons
     *
     * @return array();
     * @since 1.0.0
     */
    function shopay_font_awesome_social_icon_array() {
        return array(
            "fab fa-tumblr-square", "fab fa-tumblr", "fab fa-facebook-square", "fab fa-facebook-messenger", "fab fa-facebook-f", "fab fa-facebook", "fab fa-linkedin-in", "fab fa-linkedin", "fab fa-instagram", "fab fa-pinterest-square", "fab fa-pinterest-p", "fab fa-pinterest",
            "fab fa-whatsapp-square", "fab fa-whatsapp", "fab fa-twitter-square",
            "fab fa-twitter", "fab fa-flickr", "fab fa-snapchat-square", "fab fa-snapchat-ghost", "fab fa-snapchat", "fab fa-viber",
        );
    }
    
endif;

/*----------------------------------------------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'shopay_hover_color' ) ) :

    /**
     * Generate darker color
     * Source: http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
     *
     * @since 1.0.0
     */
    function shopay_hover_color( $hex, $steps ) {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max( -255, min( 255, $steps ) );

        // Normalize into a six character long hex string
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3) {
            $hex = str_repeat( substr( $hex,0,1 ), 2 ).str_repeat( substr( $hex, 1, 1 ), 2 ).str_repeat( substr( $hex,2,1 ), 2 );
        }

        // Split into three parts: R, G and B
        $color_parts = str_split( $hex, 2 );
        $return = '#';

        foreach ( $color_parts as $color ) {
            $color   = hexdec( $color ); // Convert to decimal
            $color   = max( 0, min( 255, $color + $steps ) ); // Adjust color
            $return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
        }

        return $return;
    }

endif;

/**
 * Dynamic style file include
 */
require get_template_directory() . '/inc/dynamic-styles.php';

/**
 * Load Custom Hooks.
 */
require get_template_directory() . '/inc/custom-hooks/header-hooks.php';
require get_template_directory() . '/inc/custom-hooks/custom-hooks.php';
require get_template_directory() . '/inc/custom-hooks/footer-hooks.php';



/**
 * Function for displaying menu item description
 * 
 */
function shopay_nav_description( $item_output, $item, $depth, $args ) {
    if ( ! empty( $item->description ) ) {
        $item_output = str_replace( $args->link_after . '</a>', '<span class="menu-item-description">' . $item->description . '</span>' . $args->link_after . '</a>', $item_output );
    }
    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'shopay_nav_description', 10, 4 );

/**
 * Check if woocommerce is activated.
 */
function shopay_is_active_woocommerce() {
    if ( class_exists( 'WooCommerce' ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if wishlist is activated.
 */
function shopay_is_active_wishlist() {
    if ( function_exists( 'YITH_WCWL' ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if Quick View is activated.
 */
function shopay_is_active_quick_view() {
    if ( class_exists( 'YITH_WCQV_Frontend' ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * function to display search bar type
 */
function shopay_search_bar_type_choices() {
    if ( shopay_is_active_woocommerce() ) {
        $choices = array(
            'default-search'    => __( 'Default Search', 'shopay' ),
            'product-search'    => __( 'Advance Product Search', 'shopay' )
        );
    } else {
        $choices = array(
            'default-search'    => __( 'Default Search', 'shopay' )
        );
    }

    return $choices;
}

/*---------------------------------------------------------------------------------------------------------------*/
/**
 * One click demo import required functions
 */

// Disable PT branding.
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

if ( ! function_exists( 'shopay_ocdi_after_import' ) ) :

    /**
     * OCDI after import.
     */
    function shopay_ocdi_after_import() {

        // Assign front page
        $front_page_id = null;
        $blog_page_id  = null;

        // Assign navigation menu locations.
        $top_menu   = get_term_by( 'name', 'Top Menu', 'nav_menu' );
        $main_menu  = get_term_by( 'name', 'Main Menu', 'nav_menu' );

        set_theme_mod( 'nav_menu_locations', array(
                'menu-primary' => $main_menu->term_id, // replace 'main-menu' here with the menu location identifier from register_nav_menu() function
                'menu-top' => $top_menu->term_id
            )
        );
    }

endif;

add_action( 'pt-ocdi/after_import', 'shopay_ocdi_after_import' );

/*---------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'shopay_register_styles_by_loaded_method' ) ) :

    /**
     * function to register the required style
     * 
     * @since 1.0.8
     */
    function shopay_register_styles_by_loaded_method() {
        // register font awesome icon
        wp_register_style( 'font-awesome', get_template_directory_uri() . '/assets/library/font-awesome/css/all.min.css', '', '5.10.2', 'all' );
    }

    add_action( 'wp_loaded', 'shopay_register_styles_by_loaded_method' );

endif;
