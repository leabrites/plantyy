<?php
/**
 * Handles the local font preloading.
 *
 * @package   fonts-plugin-pro
 * @copyright Copyright (c) 2019, Fonts Plugin
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Add preload resource hints to wp_head().
 */
class FPP_Preload_Fonts {

	/**
	 * The local fonts loader object.
	 *
	 * @var FPP_Host_Google_Fonts_Locally
	 */
	public $loader = null;

	/**
	 * The constructor.
	 *
	 * @param FPP_Host_Google_Fonts_Locally $loader The local fonts loader object.
	 */
	public function __construct( $loader ) {
		$this->loader = $loader;
		add_action( 'wp_head', array( $this, 'init' ), 1 );
	}

	/**
	 * Preload the fonts.
	 */
	public function init() {

		// Early return if local hosting is disabled.
		if ( ! get_theme_mod( 'fpp_host_locally', false ) ) {
			return;
		}

		// Early return if preloading is disabled.
		if ( ! get_theme_mod( 'fpp_preloading', false ) ) {
			return;
		}

		$this->loader->set_font_format( fpp_get_font_format() );

		// Get an array of locally-hosted files.
		$files = $this->get_remote_files_from_css( $this->loader->get_styles() );

		$fonts[] = $this->get_subsets_from_css( $this->loader->get_styles() );
		$disabled = get_theme_mod( 'fpp_disable_subsets', [] );

		foreach ( $files as $font ) {
			foreach ( $font as $google_url ) {

					// If we can't find a subset match then preload by default.
					$key = array_search( $google_url, array_column( $fonts[0], 'file' ) );
					if ( $key === false ) {
						echo '<link rel="preload" as="font" href="' . esc_url( $google_url ) . '" crossorigin>' . PHP_EOL;
						continue;
					}

					$is_disabled = in_array( $fonts[0][ $key ]['subset'], $disabled );
					if ( ! $is_disabled ) {
						echo '<link rel="preload" as="font" href="' . esc_url( $google_url ) . '" crossorigin>' . PHP_EOL;
					}

			}
		}
	}

	/**
	 * Retrieve an array of supported subsets.
	 *
	 * @param array $styles Styles pulled from the remote CSS file.
	 */
	public function get_subsets_from_css( $styles ) {
		$font_faces = explode( '/* ', $styles );
		$font_faces_array = [];

		foreach( $font_faces as $ff ){
			$font_faces_array[] = explode( ' */', $ff );
		}

		// The first item is always blank.
		unset( $font_faces_array[0] );

		$matches = [];

		foreach( $font_faces_array as $font ) {
			preg_match( '/url\(.*?\)/i', $font[1], $url );
			$matches[] = [
				'file' => rtrim( ltrim( $url[0], 'url(' ), ')' ),
				'subset' => $font[0],
			];
		}

		return $matches;
	}

	/**
	 * Retrieve an array of remote font files.
	 *
	 * @param array $styles Styles pulled from the remote CSS file.
	 */
	public function get_remote_files_from_css( $styles ) {

		$font_faces = explode( '@font-face', $styles );

		$result = array();

		// Loop all our font-face declarations.
		foreach ( $font_faces as $font_face ) {

			// Make sure we only process styles inside this declaration.
			$style = explode( '}', $font_face )[0];

			// Sanity check.
			if ( false === strpos( $style, 'font-family' ) ) {
				continue;
			}

			// Get an array of our font-families.
			preg_match_all( '/font-family.*?\;/', $style, $matched_font_families );

			// Get an array of our font-files.
			preg_match_all( '/url\(.*?\)/i', $style, $matched_font_files );

			// Get the font-family name.
			$font_family = 'unknown';
			if ( isset( $matched_font_families[0] ) && isset( $matched_font_families[0][0] ) ) {
				$font_family = rtrim( ltrim( $matched_font_families[0][0], 'font-family:' ), ';' );
				$font_family = trim( str_replace( array( "'", ';' ), '', $font_family ) );
				$font_family = sanitize_key( strtolower( str_replace( ' ', '-', $font_family ) ) );
			}

			// Make sure the font-family is set in our array.
			if ( ! isset( $result[ $font_family ] ) ) {
				$result[ $font_family ] = array();
			}

			// Get files for this font-family and add them to the array.
			foreach ( $matched_font_files as $match ) {

				// Sanity check.
				if ( ! isset( $match[0] ) ) {
					continue;
				}

				// Add the file URL.
				$result[ $font_family ][] = rtrim( ltrim( $match[0], 'url(' ), ')' );
			}

			// Make sure we have unique items.
			// We're using array_flip here instead of array_unique for improved performance.
			$result[ $font_family ] = array_flip( array_flip( $result[ $font_family ] ) );
		}
		return $result;
	}

}

$fonts = new OGF_Fonts();

if ( $fonts->has_google_fonts() ) {
	$url     = $fonts->build_url();
	$loader  = new FPP_Host_Google_Fonts_Locally( $url );
	$preload = new FPP_Preload_Fonts( $loader );
}

/**
 * Add preloading for custom uploaded fonts.
 */
function fpp_preload_custom_fonts() {

	// Early return if preloading is disabled.
	if ( ! get_theme_mod( 'fpp_preloading', false ) ) {
		return;
	}

	foreach( ogf_custom_fonts() as $font ) {
		if ( ! empty( $font['files']['woff'] ) ) {
			echo '<link rel="preload" as="font" type="font/woff" href="' . esc_url( $font['files']['woff'] ) . '" crossorigin>' . PHP_EOL;
		}
		if ( ! empty( $font['files']['woff2'] ) ) {
			echo '<link rel="preload" as="font" type="font/woff2" href="' . esc_url( $font['files']['woff2'] ) . '" crossorigin>' . PHP_EOL;
		}
	}
}
add_action( 'wp_head', 'fpp_preload_custom_fonts', 0 );
