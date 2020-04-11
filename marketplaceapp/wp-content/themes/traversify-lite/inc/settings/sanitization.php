<?php
/**
*Contains major functions needed for customizer
*/

/**
*for blog category
*/
if ( ! class_exists( 'WP_Customize_Control' ) ) {
        return null; }

class Traversify_lite_Category_dropdown_control extends WP_Customize_Control {

        public function render_content() {
            $cat_args = array(
                    'taxonomy' => 'category',
                    'orderby' => 'name',
                );
            $categories = get_categories( $cat_args ); ?>
             <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
             <span><?php echo esc_html( $this->description ); ?></span><br>
                <select data-customize-setting-link="<?php echo esc_attr($this->id); ?>">
                    <option value="none" <?php selected( get_theme_mod($this->id), 'none' ); ?>><?php esc_html_e( 'None','traversify-lite' ); ?></option>
                    <?php foreach ( $categories as $post ) { ?>
                            <option value="<?php echo esc_attr($post->slug); ?>" <?php selected( $post->slug); ?>><?php echo esc_html($post->name); ?></option>
                    <?php } ?>
                </select> <br /><br />
            <?php
        }
    }



 class Pro extends WP_Customize_Section {
  /**
   * The type of customize section being rendered.
   *
   * @since  1.0.0
   * @access public
   * @var    string
   */
  public $type = 'traversify-customize-pro';
  /**
   * Custom button text to output.
   *
   * @since  1.0.0
   * @access public
   * @var    string
   */
  public $button_text = '';
  /**
   * Custom pro button URL.
   *
   * @since  1.0.0
   * @access public
   * @var    string
   */
  public $button_url = '';
  /**
   * Default priority of the section.
   *
   * @since  1.0.0
   * @access public
   * @var    string
   */
  public $priority = 999;
  /**
   * Add custom parameters to pass to the JS via JSON.
   *
   * @since  1.0.0
   * @access public
   * @return void
   */
  public function json() {
    $json = parent::json();
    $theme = wp_get_theme();
    $json['button_text'] = esc_html(
      $this->button_text
      ? $this->button_text
      : $theme->get( 'Name' )
    );
    $json['button_url']  = esc_url(
      $this->button_url
      ? $this->button_url
      : $theme->get( 'ThemeURI' )
    );
    return $json;
  }
  /**
   * Outputs the Underscore.js template.
   *
   * @since  1.0.0
   * @access public
   * @return void
   */
  protected function render_template() { ?>

    <li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand">

      <h3 class="accordion-section-title">
        {{ data.title }}

        <# if ( data.button_text && data.button_url ) { #>
          <a href="{{ data.button_url }}" class="button button-secondary alignright" target="_blank">{{ data.button_text }}</a>
        <# } #>
      </h3>
    </li>
  <?php }
}


/**
* for blog text sanitization
*/

if(!function_exists('traversify_lite_sanitize_checkbox')):
    function traversify_lite_sanitize_checkbox( $input ) {
        return ( ( isset( $input ) && true == $input ) ? true : false );
    }
endif;

if(!function_exists('traversify_lite_sanitize_select')):
function traversify_lite_sanitize_select( $input, $setting ) {

    // Ensure input is a slug
    $input = sanitize_key( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}
endif;