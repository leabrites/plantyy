<?php
/**
 * Typography Custom Control
 *
 * @package   olympus-google-fonts
 * @copyright Copyright (c) 2020, Fonts Plugin
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Typography control class.
 */
class OGF_Customize_Typography_Control extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @var string
	 */
	public $type = 'ogf-typography';

	/**
	 * Array
	 *
	 * @var array
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @param object $manager Customizer manager.
	 * @param string $id      Control ID.
	 * @param array  $args    Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );
		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'family'          => esc_html__( 'Font Family', 'olympus-google-fonts' ),
				'weight'          => esc_html__( 'Font Weight', 'olympus-google-fonts' ),
				'style'           => esc_html__( 'Font Style', 'olympus-google-fonts' ),
				'size'            => esc_html__( 'Font Size (px)', 'olympus-google-fonts' ),
				'line_height'     => esc_html__( 'Line Height', 'olympus-google-fonts' ),
				'color'           => esc_html__( 'Color', 'olympus-google-fonts' ),
				'text_decoration' => esc_html__( 'Text Underline', 'olympus-google-fonts' ),
				'letter_spacing'  => esc_html__( 'Letter Spacing (px)', 'olympus-google-fonts' ),
				'text_transform'  => esc_html__( 'Text Transform', 'olympus-google-fonts' ),
			)
		);

	}

	/**
	 * Enqueue scripts/styles for the color picker.
	 */
	public function enqueue() {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'chosen', esc_url( FPP_DIR_URL . 'assets/js/chosen.min.js' ), array( 'jquery' ), OGF_VERSION, true );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 */
	public function to_json() {
		parent::to_json();
		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {
			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : '',
			);

			if ( 'weight' === $setting_key ) {
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices( $this->value( 'family' ) );
			}
			if ( 'style' === $setting_key ) {
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
			}
			if ( 'text_transform' === $setting_key ) {
				$this->json[ $setting_key ]['choices'] = $this->get_text_transform_choices();
			}
			if ( 'text_decoration' === $setting_key ) {
				$this->json[ $setting_key ]['choices'] = array(
					''          => esc_html__( '- Default -', 'fonts-plugin-pro' ),
					'underline' => esc_html__( 'Underline', 'fonts-plugin-pro' ),
					'none'      => esc_html__( 'None', 'fonts-plugin-pro' ),
				);
			}
		}
	}

	/**
	 * Overwrite this method as we are rendering the template with JS.
	 *
	 * @return void
	 */
	protected function render_content() {}

	/**
	 * Underscore JS template to handle the control's output.
	 */
	public function content_template() {
		$max_font_size = absint( apply_filters( 'fpp_max_font_size', 72 ) );
		?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && typeof ogf_font_array != 'undefined' ) { #>
			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select class="ogf-select" {{{ data.family.link }}}>
					<option value="default">
						<?php esc_html_e( 'Default Font', 'olympus-google-fonts' ); ?>
					</option>
					<# if ( ! _.isEmpty( ogf_custom_fonts ) ) { #>
						<option disabled><?php esc_html_e( '- Custom Fonts -', 'olympus-google-fonts' ); ?></option>
						<# _.each( ogf_custom_fonts, function( font_data, font_id ) { #>
							<option value="cf-{{ font_id }}" <# if ( font_id === data.family.value ) { #> selected="selected" <# } #>>{{ font_data.label }}</option>
						<# } ) #>
					<# } #>
					<# if ( ! _.isEmpty( ogf_typekit_fonts ) ) { #>
						<option disabled><?php esc_html_e( '- Typekit Fonts -', 'olympus-google-fonts' ); ?></option>
						<# _.each( ogf_typekit_fonts, function( font_data, font_id ) { #>
							<option value="{{ font_id }}" <# if ( font_id === data.family.value ) { #> selected="selected" <# } #>>{{ font_data.label }}</option>
						<# } ) #>
					<# } #>
					<option disabled><?php esc_html_e( '- System Fonts -', 'olympus-google-fonts' ); ?></option>
					<# _.each( ogf_system_fonts, function( font_data, font_id ) { #>
						<option value="sf-{{ font_id }}" <# if ( font_id === data.family.value ) { #> selected="selected" <# } #>>{{ font_data.label }}</option>
					<# } ) #>
					<option disabled><?php esc_html_e( '- Google Fonts -', 'olympus-google-fonts' ); ?></option>
					<# _.each( ogf_font_array, function( font_data, font_id ) { #>
						<option value="{{ font_id }}" <# if ( font_id === data.family.value ) { #> selected="selected" <# } #>>{{ font_data.f }}</option>
					<# } ) #>
				</select>
				<button type="button" class="advanced-button">
					<span class="screen-reader-text"><?php esc_html_e( 'Advanced', 'olympus-google-fonts' ); ?></span>
				</button>
			</li>
		<# } #>

		<div class="advanced-settings-wrapper">
			<div class="ogf-device-controls">
				<div class="ogf-device-controls--desktop selected" data-option="desktop">
				<?php esc_html_e( 'Desktop', 'olympus-google-fonts' ); ?></div>

				<div class="ogf-device-controls--tablet"  data-option="tablet"><?php esc_html_e( 'Tablet', 'olympus-google-fonts' ); ?></div>

				<div class="ogf-device-controls--mobile" data-option="mobile"><?php esc_html_e( 'Mobile', 'olympus-google-fonts' ); ?></div>
				</span>
			</div>

			<div class="inner">
			<# if ( data.weight && data.weight.choices ) { #>
				<li class="typography-font-weight">
					<# if ( data.weight.label ) { #>
						<span class="customize-control-title">{{ data.weight.label }}</span>
					<# } #>
					<select {{{ data.weight.link }}}>
						<# _.each( data.weight.choices, function( label, choice ) { #>
							<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>
						<# } ) #>
					</select>
				</li>
			<# } #>

			<# if ( data.style && data.style.choices ) { #>
				<li class="typography-font-style">
					<# if ( data.style.label ) { #>
						<span class="customize-control-title">{{ data.style.label }}</span>
					<# } #>
					<select {{{ data.style.link }}}>
						<# _.each( data.style.choices, function( label, choice ) { #>
							<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>
						<# } ) #>
					</select>
				</li>
			<# } #>

			<# if ( data.color ) { #>
				<li class="typography-font-color">
					<# if ( data.color.label ) { #>
						<span class="customize-control-title">{{ data.color.label }}</span>
					<# } #>
					<input class="color-picker-hex" type="text" maxlength="7" {{{ data.color.link }}} value="{{ data.color.value }}" />
				</li>
			<# } #>

			<# if ( data.size ) { #>
				<li class="typography-font-size typography-font-size--desktop">
					<div class="slider-custom-control">
							<# if ( data.size.label ) { #>
								<span class="customize-control-title">{{ data.size.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.size.value }}"></span>
							<div class="slider" slider-min-value="1" slider-max-value="<?php echo $max_font_size; ?>" slider-step-value="1"></div>
							<input class="customize-control-slider-value" {{{ data.size.link }}} type="number" value="{{ data.size.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.size_tablet ) { #>
				<li class="typography-font-size typography-font-size--tablet">
					<div class="slider-custom-control">
							<# if ( data.size.label ) { #>
								<span class="customize-control-title">{{ data.size.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.size_tablet.value }}"></span>
							<div class="slider" slider-min-value="1" slider-max-value="<?php echo $max_font_size; ?>" slider-step-value="1"></div>
							<input class="customize-control-slider-value" {{{ data.size_tablet.link }}} type="number" value="{{ data.size_tablet.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.size_mobile ) { #>
				<li class="typography-font-size typography-font-size--mobile">
					<div class="slider-custom-control">
							<# if ( data.size.label ) { #>
								<span class="customize-control-title">{{ data.size.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.size_mobile.value }}"></span>
							<div class="slider" slider-min-value="1" slider-max-value="<?php echo $max_font_size; ?>" slider-step-value="1"></div>
							<input class="customize-control-slider-value" {{{ data.size_mobile.link }}} type="number" value="{{ data.size_mobile.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.line_height ) { #>
				<li class="typography-line-height typography-line-height--desktop">
					<div class="slider-custom-control">
							<# if ( data.line_height.label ) { #>
								<span class="customize-control-title">{{ data.line_height.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.line_height.value }}"></span>

							<div class="slider" slider-min-value="0" slider-max-value="3" slider-step-value=".1"></div>
							<input class="customize-control-slider-value" {{{ data.line_height.link }}} type="number" value="{{ data.line_height.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.line_height_tablet ) { #>
				<li class="typography-line-height typography-line-height--tablet">
					<div class="slider-custom-control">
							<# if ( data.line_height.label ) { #>
								<span class="customize-control-title">{{ data.line_height.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.line_height_tablet.value }}"></span>

							<div class="slider" slider-min-value="0" slider-max-value="3" slider-step-value=".1"></div>
							<input class="customize-control-slider-value" {{{ data.line_height_tablet.link }}} type="number" value="{{ data.line_height_tablet.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.line_height_mobile ) { #>
				<li class="typography-line-height typography-line-height--mobile">
					<div class="slider-custom-control">
							<# if ( data.line_height.label ) { #>
								<span class="customize-control-title">{{ data.line_height.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.line_height_mobile.value }}"></span>

							<div class="slider" slider-min-value="0" slider-max-value="3" slider-step-value=".1"></div>
							<input class="customize-control-slider-value" {{{ data.line_height_mobile.link }}} type="number" value="{{ data.line_height_mobile.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.letter_spacing ) { #>
				<li class="typography-letter-spacing">
					<div class="slider-custom-control">
							<# if ( data.letter_spacing.label ) { #>
								<span class="customize-control-title">{{ data.letter_spacing.label }}</span>
							<# } #>
							<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="{{ data.letter_spacing.value }}"></span>
							<div class="slider" slider-min-value="-5" slider-max-value="5" slider-step-value=".1"></div>
							<input class="customize-control-slider-value" {{{ data.letter_spacing.link }}} type="number" value="{{ data.letter_spacing.value }}">
					</div>
				</li>
			<# } #>

			<# if ( data.text_transform && data.text_transform.choices ) { #>
				<li class="typography-text-transform">
					<# if ( data.text_transform.label ) { #>
						<span class="customize-control-title">{{ data.text_transform.label }}</span>
					<# } #>
					<select {{{ data.text_transform.link }}}>
						<# _.each( data.text_transform.choices, function( label, choice ) { #>
							<option value="{{ choice }}" <# if ( choice === data.text_transform.value ) { #> selected="selected" <# } #>>{{ label }}</option>
						<# } ) #>
					</select>
				</li>
			<# } #>

			<# if ( data.text_decoration && data.text_decoration.choices ) { #>
				<li class="typography-text-decoration">
					<# if ( data.text_decoration.label ) { #>
						<span class="customize-control-title">{{ data.text_decoration.label }}</span>
					<# } #>
					<select {{{ data.text_decoration.link }}}>
						<# _.each( data.text_decoration.choices, function( label, choice ) { #>
							<option value="{{ choice }}" <# if ( choice === data.text_decoration.value ) { #> selected="selected" <# } #>>{{ label }}</option>
						<# } ) #>
					</select>
				</li>
			<# } #>

		</div>
		</div>
		</ul>
		<?php
	}

	/**
	 * Returns the available font weights.
	 *
	 * @param string $font User's font choice.
	 * @return array Available font variants.
	 */
	public function get_font_weight_choices( $font ) {

		$variants_to_remove = array(
			'100i' => esc_html__( 'Thin Italic', 'olympus-google-fonts' ),
			'200i' => esc_html__( 'Extra Light Italic', 'olympus-google-fonts' ),
			'300i' => esc_html__( 'Light Italic', 'olympus-google-fonts' ),
			'400i' => esc_html__( 'Normal Italic', 'olympus-google-fonts' ),
			'500i' => esc_html__( 'Medium Italic', 'olympus-google-fonts' ),
			'600i' => esc_html__( 'Semi Bold Italic', 'olympus-google-fonts' ),
			'700i' => esc_html__( 'Bold Italic', 'olympus-google-fonts' ),
			'800i' => esc_html__( 'Extra Bold Italic', 'olympus-google-fonts' ),
			'900i' => esc_html__( 'Ultra Bold Italic', 'olympus-google-fonts' ),
		);

		$all_variants = ogf_font_variants();

		if ( 'default' === $font ) {
			return array_diff( $all_variants, $variants_to_remove );
		}


		if ( ogf_is_google_font( $font ) ) {
			$fonts_array       = ogf_fonts_array();
			$variants          = $fonts_array[ $font ]['v'];
			$new_variants['0'] = esc_html__( '- Default -', 'olympus-google-fonts' );
			$diff = array_diff_key( $variants, $variants_to_remove );

			foreach ( $diff as $key => $value ) {
				$new_variants[ $key ] = $all_variants[ $key ];
			}

			return $new_variants;
		}

		if ( ogf_is_typekit_font( $font ) ) {
			$fonts_array = ogf_typekit_fonts();

			if ( ! array_key_exists( $font, $fonts_array ) ) {
				return array();
			}

			$variants          = $fonts_array[ $font ]['variants'];
			$new_variants['0'] = esc_html__( '- Default -', 'olympus-google-fonts' );

			foreach ( $variants as $variant ) {
				$new_variants[ $variant ] = $all_variants[ $variant ];
			}

			return $new_variants;
		}

		$choices = array(
			'0'   => esc_html__( '- Default -', 'olympus-google-fonts' ),
			'400' => esc_html__( 'Normal', 'olympus-google-fonts' ),
			'700' => esc_html__( 'Bold', 'olympus-google-fonts' ),
		);

		return apply_filters( 'ogf_default_font_weight_choices', $choices );
	}

	/**
	 * Returns the available font styles.
	 *
	 * @return array CSS font-style values.
	 */
	public function get_font_style_choices() {
		$choices = array(
			'default' => esc_html__( '- Default -', 'olympus-google-fonts' ),
			'normal'  => esc_html__( 'Normal', 'olympus-google-fonts' ),
			'italic'  => esc_html__( 'Italic', 'olympus-google-fonts' ),
			'oblique' => esc_html__( 'Oblique', 'olympus-google-fonts' ),
		);

		return apply_filters( 'ogf_default_font_style_choices', $choices );
	}

	/**
	 * Returns the available text-transform values.
	 *
	 * @return array CSS text-transform values.
	 */
	public function get_text_transform_choices() {
		$choices = array(
			''           => esc_html__( '- Default -', 'olympus-google-fonts' ),
			'capitalize' => esc_html__( 'Capitalize', 'olympus-google-fonts' ),
			'uppercase'  => esc_html__( 'Uppercase', 'olympus-google-fonts' ),
			'lowercase'  => esc_html__( 'Lowercase', 'olympus-google-fonts' ),
			'none'       => esc_html__( 'None', 'olympus-google-fonts' ),
		);

		return apply_filters( 'ogf_default_text_transform_choices', $choices );
	}
}
