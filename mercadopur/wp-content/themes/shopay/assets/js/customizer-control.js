jQuery(document).ready(function($) {

    "use strict";

    // active_callback for enable latest post toggle
	wp.customize( 'show_on_front', function( setting ) {
		wp.customize.control( 'shopay_front_latest_posts_option', function( control ) {
			var visibility = function() {
				if ( 'posts' === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for top header short description
	wp.customize( 'shopay_top_header_option', function( setting ) {
		wp.customize.control( 'shopay_top_header_description', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for top header site location
	wp.customize( 'shopay_top_header_option', function( setting ) {
		wp.customize.control( 'shopay_top_header_location', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for top header site service
	wp.customize( 'shopay_top_header_option', function( setting ) {
		wp.customize.control( 'shopay_top_header_service', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for main header contact info
	wp.customize( 'shopay_site_info_option', function( setting ) {
		wp.customize.control( 'shopay_header_site_contact_info', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for main header email info
	wp.customize( 'shopay_site_info_option', function( setting ) {
		wp.customize.control( 'shopay_header_site_email_info', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for breadcrumb home label
	wp.customize( 'shopay_breadcrumbs', function( setting ) {
		wp.customize.control( 'shopay_breadcrumbs_home_lable', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});

	// active_callback for footer widget area layout
	wp.customize( 'shopay_footer_widget_area_option', function( setting ) {
		wp.customize.control( 'shopay_footer_widget_layout', function( control ) {
			var visibility = function() {
				if ( true === setting.get() ) {
					control.container.slideDown( 180 );
				} else {
					control.container.slideUp( 180 );
				}
			};

			visibility();
			setting.bind( visibility );
		});
	});
    
});