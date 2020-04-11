<?php

namespace The7\Adapters\Elementor\Pro\ThemeSupport;

use Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\Documents\Footer;
use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class The7_Theme_Support {

	/**
	 * @param Locations_Manager $manager
	 */
	public function register_locations( $manager ) {
		$manager->register_core_location( 'header' );
		$manager->register_core_location( 'footer' );
	}

	public function overwrite_config_base_init() {
		if ( self::get_document_id_for_location( 'header' ) ) {
			add_filter( 'presscore_show_header', [ $this, 'do_header' ], 0 );
		}

		$main_post_id  = get_the_ID();
		$footer_source = 'the7';
		if ( self::get_document_id_for_location( 'footer' ) ) {
			$footer_source = 'elementor';
			if ( ! is_single() && ! is_page() ) {
				$main_post_id = self::get_document_id_for_location( 'archive', $main_post_id );
			}
		}

		if ( metadata_exists( 'post', $main_post_id, '_dt_footer_elementor_source' ) ) {
			$footer_source = get_post_meta( $main_post_id, '_dt_footer_elementor_source', true );
		}

		$show_footer = true;
		if ( metadata_exists( 'post', $main_post_id, '_dt_footer_show' ) ) {
			$show_footer = get_post_meta( $main_post_id, '_dt_footer_show', true );
		}

		if ( $show_footer && $footer_source === 'elementor' ) { //use elementor footer
			presscore_config()->set( 'template.bottom_bar.enabled', false );
			add_filter( 'presscore_replace_footer', '__return_true' );
			add_action( 'presscore_before_footer_widgets', [ $this, 'do_footer' ], 0 );
			add_action(
				'presscore_footer_html_class',
				static function ( $output ) {
					$output[] = 'elementor-footer';

					return $output;
				}
			);
		}
	}

	public function do_header() {
		elementor_theme_do_location( 'header' );

		return false;
	}

	public function do_footer() {
		elementor_theme_do_location( 'footer' );
	}

	/**
	 * Alter current page value with archive template id in the theme config.
	 *
	 * @param int|null $page_id
	 *
	 * @return int|null|false
	 */
	public static function config_page_id_filter( $page_id = null ) {
		if ( is_single() || is_page() ) {
			return get_the_ID();
		}

		return self::get_document_id_for_location( 'archive' );
	}

	public function __construct() {
		add_action( 'elementor/theme/register_locations', [ $this, 'register_locations' ] );
		add_action( 'presscore_config_base_init', [ $this, 'overwrite_config_base_init' ] );
		add_filter( 'presscore_config_post_id_filter', [ $this, 'config_page_id_filter' ], 20 );
	}

	/**
	 * @param string $location
	 * @param null   $page_id
	 *
	 * @return int|null
	 */
	public static function get_document_id_for_location( $location, $page_id = null ) {
		$documents = Module::instance()->get_conditions_manager()->get_documents_for_location( $location );

		foreach ( $documents as $document ) {
			return $document->get_post()->ID;
		}

		return $page_id;
	}
}
