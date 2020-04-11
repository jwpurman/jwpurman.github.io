<?php
/**
 * @package The7
 */

namespace The7\Adapters\Elementor\Page_Settings;

use Elementor\Controls_Manager;
use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

$template_option_name = The7_Elementor_Compatibility::instance()->page_settings->template_option_name;

return [
	'args'     => [
		'label' => __( 'Sidebar settings', 'the7mk2' ),
		'tab'   => Controls_Manager::TAB_SETTINGS,
		'condition' => [
			$template_option_name .'!' => [PageTemplatesModule::TEMPLATE_CANVAS, PageTemplatesModule::TEMPLATE_HEADER_FOOTER ],
		],
	],
	'controls' => [
		'the7_document_sidebar_position' => [
			'meta' => '_dt_sidebar_position',
			'args' => [
				'label'     => __( 'Sidebar position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'     => __( 'Left', 'the7mk2' ),
					'right'    => __( 'Right', 'the7mk2' ),
					'disabled' => __( 'Disabled', 'the7mk2' ),
				],
				'separator' => 'none',
			],
		],
		'the7_document_sidebar_id'       => [
			'meta' => '_dt_sidebar_widgetarea_id',
			'args' => [
				'label'     => __( 'Sidebar', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'sidebar_1',
				'options'   => 'presscore_get_widgetareas_options',
				'separator' => 'none',
				'condition' => [
					'the7_document_sidebar_position' => [ 'left', 'right' ],
				],
			],
		],
	],
];
