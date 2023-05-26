<?php
/**
 * Build the customizer controls for Optimization options.
 *
 * @package   fonts-plugin-pro
 * @copyright Copyright (c) 2019, Fonts Plugin
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * FPP_Optimization_Controls Class.
 */
class FPP_Optimization_Controls {

	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'customize_register', array( $this, 'register_section' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue' ), 100 );
	}

	/**
 * Register control scripts/styles.
 */
public function customize_controls_enqueue() {
	wp_enqueue_script( 'fpp-customize-controls', esc_url( FPP_DIR_URL . 'assets/js/customize-controls.js' ), array( 'customize-controls' ), FPP_VERSION, true );
}

	/**
	 * Register the Customizer section.
	 *
	 * @param WP_Customize_Manager $wp_customize the Customizer object.
	 */
	public function register_section( $wp_customize ) {
		$wp_customize->add_section(
			'fpp_optimization',
			array(
				'title'       => __( 'Optimization', 'fonts-plugin-pro' ),
				'description' => __( 'Optimize the delivery of font files for improved performance and user-privacy.', 'fonts-plugin-pro' ),
				'panel'       => 'ogf_google_fonts',
			)
		);
	}

	/**
	 * Register the Customizer setting.
	 *
	 * @param WP_Customize_Manager $wp_customize the Customizer object.
	 */
	public function register_settings( $wp_customize ) {

		$site_url = site_url( '', 'https' );
		$url      = preg_replace( '(^https?://)', '', $site_url );

		// Add an option to disable the logo.
		$wp_customize->add_setting(
			'fpp_host_locally',
			array(
				'default'           => false,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'fpp_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'fpp_host_locally',
			array(
				'label'       => esc_html__( 'Host Google Fonts Locally', 'fonts-plugin-pro' ),
				'description' => esc_html__( 'Fonts will be served from ' . $url . ' instead of fonts.googleapis.com', 'fonts-plugin-pro' ),
				'section'     => 'fpp_optimization',
				'type'        => 'checkbox',
				'settings'    => 'fpp_host_locally',
			)
		);

		$wp_customize->add_setting(
			'fpp_use_woff2',
			array(
				'default'           => false,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'fpp_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'fpp_use_woff2',
			array(
				'label'       => esc_html__( 'Use WOFF2 File Format', 'fonts-plugin-pro' ),
				'description' => esc_html__( 'Use the WOFF2 file format instead of WOFF.', 'fonts-plugin-pro' ),
				'section'     => 'fpp_optimization',
				'type'        => 'checkbox',
				'settings'    => 'fpp_use_woff2',
				'active_callback' => 'fpp_local_hosting_is_active',
			)
		);

		$wp_customize->add_setting(
			'fpp_preloading',
			array(
				'default'           => false,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'fpp_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'fpp_preloading',
			array(
				'label'           => esc_html__( 'Enable Preloading', 'fonts-plugin-pro' ),
				'description'     => esc_html__( 'Add preload resource hints.', 'fonts-plugin-pro' ),
				'section'         => 'fpp_optimization',
				'type'            => 'checkbox',
				'settings'        => 'fpp_preloading',
			)
		);

		$wp_customize->add_setting(
			'fpp_removal',
			array(
				'default'           => false,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'fpp_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'fpp_removal',
			array(
				'label'           => esc_html__( 'Remove External Fonts', 'fonts-plugin-pro' ),
				'description'     => esc_html__( 'Remove Google Fonts loaded by other plugins and your theme.', 'fonts-plugin-pro' ),
				'section'         => 'fpp_optimization',
				'type'            => 'checkbox',
				'settings'        => 'fpp_removal',
			)
		);

		$wp_customize->add_setting(
			'fpp_rewrite',
			array(
				'default'           => false,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'fpp_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'fpp_rewrite',
			array(
				'label'           => esc_html__( 'Rewrite External Fonts', 'fonts-plugin-pro' ),
				'description'     => esc_html__( 'Convert fonts added by your theme and plugins to be locally hosted on your domain.', 'fonts-plugin-pro' ),
				'section'         => 'fpp_optimization',
				'type'            => 'checkbox',
				'settings'        => 'fpp_rewrite',
				'active_callback' => 'fpp_removal_is_active',
			)
		);
	}
}

$fpp_optimization_controls = new FPP_Optimization_Controls();

/**
 * Checkbox sanitization callback example.
 *
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 *
 * @param bool $checked Whether the checkbox is checked.
 * @return bool Whether the checkbox is checked.
 */
function fpp_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Check if WOFF or WOFF2 font format is used.
 */
function fpp_local_hosting_is_active() {
	return get_theme_mod( 'fpp_host_locally', false );
}

/**
 * Check if WOFF or WOFF2 font format is used.
 */
function fpp_removal_is_active() {
	return ! get_theme_mod( 'fpp_removal', false );
}
