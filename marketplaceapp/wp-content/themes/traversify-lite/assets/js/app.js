


(function ($) {
$('.destination-cat-slider').owlCarousel({
    loop: false,
    margin: 25,
    lazyLoad: true,
    autoplay: true,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 2,
            nav: false
        },
        1000: {
            items: 4,
            nav: false,
            loop: false
        }
    }
})

$('.activities-slider').owlCarousel({
    loop: false,
    margin: 25,
    lazyLoad: true,
    autoplay: true,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 2,
            nav: false
        },
        1000: {
            items: 4,
            nav: false,
            loop: false
        }
    }
})


$('.listing-des-slider').slick({
  infinite: true,
  autoplaySpeed: 7000,
  arrows: false,
  slidesToShow: 3,
  slidesToScroll: 3,
  responsive: [
      {
        breakpoint: 990,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
  ]
});

$('.popular-dest-wrap').owlCarousel({
    loop: false,
    margin: 25,
    lazyLoad: true,
    autoplay: true,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 2,
            nav: false
        },
        1000: {
            items: 4,
            nav: false,
            loop: false
        }
    }
})




$('.explore-des-slider').slick({
    dots: false,
    infinite: true,
    speed: 500,
    asNavFor: '.explore-nav-wrap',
    arrows: false,
});

$('.explore-nav-wrap').slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  asNavFor: '.explore-des-slider',
  dots: false,
  arrows: false,
  centerPadding: '0',
  centerMode: true,
  focusOnSelect: true
});


$('.nav-wrapper').stickMe({
    transitionDuration: 500,
    shadow: true,
    shadowOpacity: 0.6,
});

$('[data-toggle="tooltip"]').tooltip();


})(jQuery);
