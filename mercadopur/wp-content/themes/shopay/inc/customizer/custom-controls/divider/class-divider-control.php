<?php
/**
 * Customizer Repeater Control.
 * 
 * @package Shopay
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Shopay_Control_Divider' ) ) {

	/**
	 * A text control with validation for CSS units.
	 */
	class Shopay_Control_Divider extends WP_Customize_Control {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'mt-divider';

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $caption = '';

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $separator = true;

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();
			$this->json['label']       = esc_html( $this->label );
			$this->json['caption']     = $this->caption;
			$this->json['description'] = $this->description;
			$this->json['separator']   = $this->separator;
		}

		
		public function enqueue() {            
            wp_enqueue_style( 'shopay-divider-style', get_template_directory_uri() . '/inc/customizer/custom-controls/divider/divider.css', null );
        }

        /**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {
	?>

			<# if ( data.caption ) { #>
				<span class="customize-control-caption">{{{ data.caption }}}</span>
			<# } #>

			<# if ( data.separator ) { #>
				<hr />
			<# } #>

			<label class="customizer-text">
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
			</label>
	<?php
		}
	}

}