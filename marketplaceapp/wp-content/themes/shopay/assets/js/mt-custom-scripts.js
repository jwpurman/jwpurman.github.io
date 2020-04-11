jQuery(document).ready(function($){

    "use-strict";

    var rtlValue = $("body").hasClass("rtl");

    /**
     * Preloader
     */
    if($('#preloader-background').length > 0) {
        setTimeout(function(){$('#preloader-background').hide();}, 600);
    }

    /**
     *  Class for parent category menu. 
     */
    $( '.product-categories li ul' ).parent().addClass( 'has-children' );
    
    /**
     * Settings about sticky header
     */
    var stickyOption = shopayObject.header_sticky;
    if( stickyOption === 'on' ) {
        var windowWidth = $( window ).width();
        if( windowWidth < 500 ) {
            var wpAdminBar = 0;
        } else {
            var wpAdminBar = $('#wpadminbar');
        }
        if ( wpAdminBar.length ) {
          $("#masthead").sticky({topSpacing:wpAdminBar.height()});
        } else {
          $("#masthead").sticky({topSpacing:0});
        }
    }

    /**
     * Sticky sidebar
     */
    var sidebarSticky = shopayObject.sidebar_sticky;
    if( sidebarSticky === 'on' ) {
        $('#primary, #secondary').theiaStickySidebar({
            additionalMarginTop: 40
        });
    }

    /**
     * Highlight Products slide handle
     */
    $('.highlight-products-section-content').marquee({
        delayBeforeStart: 2000,
        pauseOnHover : true,
        startVisible: true,
        duplicated: true,
        duration: 70000,
    });

    /**
     * Wow Animation.
     */
    var wow_option = shopayObject.wow_option;
    if( wow_option === 'on' ) {
        new WOW().init();
    }

    /**
     * Scripts for Header Sticky Sidebar
     */
    $('.sticky-header-sidebar-section .sticky-sidebar-icon').click(function(){
        $(this).parent().find('.sticky-sidebar-content-wrapper').toggleClass('header-sidebar-active');
    });

    $('.sticky-header-sidebar-section .sticky-sidebar-close').click(function(){
        $('.sticky-sidebar-content-wrapper').removeClass('header-sidebar-active');
    });

    /**
     * Category Menu Toggle content in innerpages
     */
    $( 'body' ).on( 'click', '.shopay-cat-menu-wrapper .main-category-list-title > a', function(){
        $(this).parent().next().slideToggle(1000);
    });

    
    /**
     * masthead menu toggle
     */
    $( '#masthead .menu-toggle' ).click(function(){
        $('#masthead .primary-menu-wrap').toggleClass('menu-active'); 
    });
    
    $('.main-menu-close').click(function(){
        $('#masthead .primary-menu-wrap').removeClass('menu-active');
    });

    /**
     * Scripts for front main slider
     * 
     */
    $('.shopay-slider-section').each(function(){
        $(".mainSlider").lightSlider({
            item: 1,
            auto: true,
            pager: true,
            loop: true,
            slideMargin: 0,
            speed: 2000,
            pause: 10000,
            enableTouch: false,
            enableDrag: false,
            rtl: rtlValue,
            prevHtml: '<i class="fa fa-angle-left"></i>',
            nextHtml: '<i class="fa fa-angle-right"></i>',
            onSliderLoad: function() {
                $('.mainSlider').removeClass('cS-hidden');
            }
        });
    });


    /**
     * Scripts for front category collection slider
     * 
     */
    $('.shopay-category-collection-section').each(function() {
        var Id = $(this).parents('.widget').attr('id');
        var NewId = Id;

        NewId = $('#' + Id + " .categorySlider").lightSlider({
            auto: false,
            loop: true,
            pauseOnHover: true,
            pager: false,
            speed: 800,
            pause: 2000,
            controls: false,
            rtl: rtlValue,
            prevHtml: '<i class="fa fa-angle-left"></i>',
            nextHtml: '<i class="fa fa-angle-right"></i>',
            item: 7,
            onSliderLoad: function() {
                $('#' + Id + " .categorySlider").removeClass('cS-hidden');
            },
            responsive: [
                {
                    breakpoint: 980,
                    settings: {
                        item: 5,
                        slideMove: 1,
                        slideMargin: 6,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        item: 4,
                        slideMove: 1,
                        slideMargin: 6,
                    }
                },
                {
                    breakpoint: 360,
                    settings: {
                        item: 2,
                        slideMove: 1,
                    }
                }
            ]
        });

        $('#' + Id + ' .categorySlider-controls__prev').click(function() {
            NewId.goToPrevSlide();
        });
        $('#' + Id + ' .categorySlider-controls__next').click(function() {
            NewId.goToNextSlide();
        });
    });

    /**
     * Height for category menu.
     */
    $(window).on('load', function() {
        if ($(window).width() > 599){
            var container = ".main-slider-section";
            var sliderHeight = $( container ).height();
            $( '.slider-cat-menu .product-categories' ).css('height',sliderHeight);
        }
    });

    /**
     * Isotope for product filterby category widget.
     * 
     */
    $('.pfc-products-wrap').imagesLoaded( function(){
        $('.category-titles-wrap .cat-title:first').addClass("active");
        var initialFilter = $(".pfc-wrap .category-titles-wrap .cat-title").data("filter");
        var $pfcGrid = $('.pfc-products-wrap').isotope({
            filter: initialFilter,
            layoutMode: 'fitRows'
        });
        // filter items click
        $('.category-titles-wrap').on( 'click', 'li', function() {
            $('.category-titles-wrap li').removeClass("active");
            $(this).addClass("active");
            var filterValue = $(this).attr('data-filter');
            $pfcGrid.isotope({ 
                filter: filterValue,
                layoutMode: 'fitRows',
                sortBy : 'random',
                transitionDuration: 600,
            });
        });
    });

    /**
     * widget sub menu toggle
     */
    $('<a class="sub-toggle" href="javascript:void(0);"><i class="fa fa-angle-right"></i></a>').insertAfter('.widget_nav_menu .menu-item-has-children>a, .widget_nav_menu .page_item_has_children>a');

    $('body').on( 'click', '.widget_nav_menu .menu-item-has-children .sub-toggle', function() {
        $(this).parent('.widget_nav_menu .menu-item-has-children').children('ul.sub-menu').first().slideToggle();
        $(this).children('.fa').first().toggleClass('fa-angle-right').toggleClass('fa-angle-down');
    });

    /**
     * category menu toggle
     */
    $('<a class="sub-toggle" href="javascript:void(0);"><i class="fa fa-angle-right"></i></a>').insertAfter('.product-categories li.has-children>a');

    $('body').on( 'click', '.product-categories li.has-children .sub-toggle', function() {
        $(this).parent('.product-categories li.has-children').children('ul.children').first().slideToggle();
        $(this).children('.fa').first().toggleClass('fa-angle-right').toggleClass('fa-angle-down');

    });

    // Primary menu sub-toggle
    $('<a class="sub-toggle" href="javascript:void(0);"><i class="fa fa-angle-down"></i></a>').insertAfter('#site-navigation .menu-item-has-children>a, #site-navigation .page_item_has_children>a');

    $('body').on( 'click', '#site-navigation .sub-toggle', function() {
        $(this).parent('.menu-item-has-children').children('ul.sub-menu').first().slideToggle();
        $(this).parent('.page_item_has_children').children('ul.children').first().slideToggle();
        $(this).children('.fa').first().toggleClass('fa-angle-right').toggleClass('fa-angle-down');
    });
    /**
     * Image height for latest product widget.
     */
    $(window).on('load', function() {
        if ($(window).width() > 767) {
            $(".shopay-latest-product-wrapper").each(function() {
                var imageHeight = $(this).height();
                $(this).find("figure").css('height', imageHeight);
            });
        }
    });

    /**
     * Scroll To Top
     */
    $(window).scroll(function() {
        if ($(this).scrollTop() > 1000){
            $('#shopay-scroll-to-top').fadeIn('slow');
        } else {
            $('#shopay-scroll-to-top').fadeOut('slow');
        }
    });
    $('#shopay-scroll-to-top').click(function(){
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
});