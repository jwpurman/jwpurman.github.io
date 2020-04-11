<?php
/**
 * The7 Elementor plugin compatibility class.
 *
 * @since 7.7.0
 *
 * @package The7
 */

use The7\Adapters\Elementor\The7_Elementor_Page_Settings;
use The7\Adapters\Elementor\The7_Elementor_Widgets;
use The7\Adapters\Elementor\The7_Kit_Manager_Control;
use The7\Adapters\Elementor\The7_Schemes_Manager_Control;
use The7\Adapters\Elementor\The7_Elementor_Template_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Elementor_Compatibility
 */
class The7_Elementor_Compatibility {
	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	public $page_settings;
	public $icons_extension;
	public $widgets;
	public $template_manager;
	public $theme_builder_adapter;
	public $kit_manager_control;
	public $scheme_manager_control;
	/**
	 * Bootstrap module.
	 */
	public function bootstrap() {
		require_once __DIR__ . '/elementor-functions.php';
		require_once __DIR__ . '/class-the7-elementor-widgets.php';
		require_once __DIR__ . '/class-the7-elementor-page-settings.php';
		require_once __DIR__ . '/class-the7-elementor-icons-extension.php';
		require_once __DIR__ . '/meta-adapters/class-the7-elementor-color-meta-adapter.php';
		require_once __DIR__ . '/meta-adapters/class-the7-elementor-padding-meta-adapter.php';
		require_once __DIR__ . '/class-the7-elementor-kit-manager-control.php';
		require_once __DIR__ . '/class-the7-elementor-schemes-manager-control.php';
		require_once __DIR__ . '/class-the7-elementor-template-manager.php';

		$this->page_settings = new The7_Elementor_Page_Settings();
		$this->page_settings->bootstrap();

		$this->icons_extension = new The7_Elementor_Icons_Extension();
		$this->icons_extension->bootstrap();

		$this->widgets = new The7_Elementor_Widgets();
		$this->widgets->bootstrap();

		$this->template_manager = new The7_Elementor_Template_Manager();
		$this->template_manager->bootstrap();

		if ( true )//todo add option dependency
		{
			$this->kit_manager_control = new The7_Kit_Manager_Control();
			$this->kit_manager_control->bootstrap();
		}

		if ( true )//todo add option dependency
		{
			$this->scheme_manager_control = new The7_Schemes_Manager_Control();
			$this->scheme_manager_control->bootstrap();
		}

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$this->bootstrap_pro();
		}
	}

	protected function bootstrap_pro() {
		require_once __DIR__ . '/pro/class-the7-elementor-theme-builder-adapter.php';

		$this->theme_builder_adapter = new \The7\Adapters\Elementor\Pro\The7_Elementor_Theme_Builder_Adapter();
		$this->theme_builder_adapter->bootstrap();
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->bootstrap();
		}

		return self::$instance;
	}
}
