<?php
/**
 * Main Fonts_Plugin_Pro Class
 *
 * @package   fonts-plugin-pro
 * @copyright Copyright (c) 2019, Fonts Plugin
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Main Fonts_Plugin_Pro Class
 */
class Fonts_Plugin_Pro {

	/**
	 * Initialize plugin.
	 */
	public function __construct() {

		if ( ! defined( 'OGF_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'base_plugin_notice' ) );
			return;
		}

		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( 'invalid' === get_option( 'fpp_license_status', 'invalid' ) ) {
			add_action( 'admin_notices', array( $this, 'license_key_notice' ) );
		}

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue' ) );

		if ( version_compare( OGF_VERSION, '3.0.12', '>' ) ) {
			add_action( 'customize_register', array( $this, 'fpp_overwrite_register_typography_control' ), 5 );
		}

		require FPP_DIR_PATH . '/inc/local/class-fpp-host-google-fonts-locally.php';
		require FPP_DIR_PATH . '/inc/local/class-fpp-preload-fonts.php';
		require FPP_DIR_PATH . '/inc/optimization/class-fpp-optimization-controls.php';
		require FPP_DIR_PATH . '/inc/filters.php';

		$fpp_removal = (bool) get_theme_mod( 'fpp_removal', false );

		if ( $fpp_removal ) {
			add_filter( 'wp_resource_hints', array( $this, 'remove_resource_hints' ), PHP_INT_MAX, 2 );
			require FPP_DIR_PATH . '/inc/optimization/remove-fonts.php';
			return;
		}

		$fpp_rewrite = (bool) get_theme_mod( 'fpp_rewrite', false );

		if ( $fpp_rewrite ) {
			add_filter( 'wp_resource_hints', array( $this, 'remove_resource_hints' ), PHP_INT_MAX, 2 );
			require FPP_DIR_PATH . '/inc/optimization/rewrite-fonts.php';
		}
	}

		/**
		 * Load plugin textdomain.
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'fonts-plugin-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

	/**
	 * Missing base plugin notice.
	 */
	public function base_plugin_notice() {
		$class     = 'notice notice-error';
		$admin_url = admin_url( 'plugin-install.php?s=googlefonts&tab=search&type=author' );
		/* translators: 1. Admin URL, 2. Admin URL */
		$message = sprintf( __( '<a href="%1$s">Google Fonts for WordPress</a> must be active for the <strong>Pro</strong> plugin to function. <a href="%2$s">Install now</a>.', 'fonts-plugin-pro' ), $admin_url, $admin_url );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
	}

	/**
	 * Missing license key notice.
	 */
	public function license_key_notice() {
		$class     = 'notice notice-error';
		$admin_url = admin_url( 'admin.php?page=fpp_license_page' );
		/* translators: 1. Admin URL, 2. Admin URL */
		$message = sprintf( __( 'Your <strong>Fonts Plugin Pro</strong> license key must be active to receive updates, security fixes and new features. <a href="%2$s">Activate now</a>.', 'fonts-plugin-pro' ), $admin_url, $admin_url );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
	}

	/**
	 * Register control scripts/styles.
	 */
	public function customize_controls_enqueue() {
		wp_enqueue_style( 'fpp-customize-controls', esc_url( FPP_DIR_URL . 'assets/css/customize-controls.css' ), array( 'ogf-customize-controls' ), FPP_VERSION );
	}

	public function fpp_overwrite_register_typography_control( $wp_customize ) {
		require_once FPP_DIR_PATH . 'inc/controls/class-ogf-customize-typography-control.php';
		$wp_customize->register_control_type( 'OGF_Customize_Typography_Control' );
	}

	/**
	 * Remove DNS prefetch, preconnect and preload headers.
	 */
	function remove_resource_hints( $urls, $relation_type ) {
	 if ( 'dns-prefetch' === $relation_type ) {
		 $urls = array_diff( $urls, array( 'fonts.googleapis.com' ) );
	 } elseif ( 'preconnect' === $relation_type || 'preload' === $relation_type ) {
		 foreach ( $urls as $key => $url ) {
			 if ( ! isset( $url['href'] ) ) {
				 continue;
			 }
			 if ( preg_match( '/\/\/fonts\.(gstatic|googleapis)\.com/', $url['href'] ) ) {
				 unset( $urls[ $key ] );
			 }
		 }
	 }

	 return $urls;
	}

}
