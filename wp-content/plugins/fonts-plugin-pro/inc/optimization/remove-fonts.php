<?php

/**
 * Dequeue Google Fonts based on URL.
 */
function fpp_dequeueu_fonts() {

	// Remove fonts added by the Divi Extra theme
	remove_action( 'wp_footer', 'et_builder_print_font' );

	// Dequeue Google Fonts loaded by Revolution Slider.
	remove_action( 'wp_footer', array( 'RevSliderFront', 'load_google_fonts' ) );

	// Dequeue the Jupiter theme font loader.
	wp_dequeue_script( 'mk-webfontloader' );

	// Remove the aThemes resource hints.
	remove_action( 'wp_head', 'sydney_preconnect_google_fonts' );
	remove_action( 'wp_head', 'botiga_preconnect_google_fonts' );

	global $wp_styles;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	$allowed = apply_filters(
		'fpp_exceptions',
		[ 'olympus-google-fonts' ]
	);

	foreach ( $wp_styles->registered as $style ) {
		$handle = $style->handle;
		$src    = $style->src;

		if ( strpos( $src, 'fonts.googleapis' ) !== false ) {
			if ( ! array_key_exists( $handle, array_flip( $allowed ) ) ) {
				wp_dequeue_style( $handle );
			}
		}
	}

	/**
	 * Some themes set the Google Fonts URL as a dependency, so we need to replace
	 * it with a blank value rather than removing it entirely. As that would
	 * remove the stylesheet too.
	 */
	foreach ( $wp_styles->registered as $style ) {
		foreach( $style->deps as $dep ) {
			if ( ( strpos( $dep, 'google-fonts' ) !== false ) || ( strpos( $dep, 'google_fonts' ) !== false ) || ( strpos( $dep, 'googlefonts' ) !== false ) ) {
				$wp_styles->remove( $dep );
				$wp_styles->add( $dep, '' );
			}
		}
	}
	remove_action( 'wp_head', 'hu_print_gfont_head_link', 2 );

	remove_action('wp_head', 'appointment_load_google_font');
}

add_action( 'wp_enqueue_scripts', 'fpp_dequeueu_fonts', PHP_INT_MAX );
add_action( 'wp_print_styles', 'fpp_dequeueu_fonts', PHP_INT_MAX );

/**
 * Dequeue Google Fonts loaded by Elementor.
 */
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded by Beaver Builder.
 */
add_filter(
	'fl_builder_google_fonts_pre_enqueue',
	function( $fonts ) {
		return array();
	}
);

/**
 * Dequeue Google Fonts loaded by JupiterX theme.
 */
add_filter(
	'jupiterx_register_fonts',
	function( $fonts ) {
		return array();
	},
	99999
);

/**
 * Dequeue Google Fonts loaded by the Hustle plugin.
 */
add_filter( 'hustle_load_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded by the Vantage theme.
 */
add_filter( 'vantage_import_google_fonts', '__return_false' );


/**
 * Dequeue Google Fonts loaded by the Hustle plugin.
 */
add_filter( 'mailpoet_display_custom_fonts', '__return_false' );

if ( ! function_exists( 'apollo13framework_get_web_fonts_dynamic' ) ) {
	/**
	 * Dequeue Google Fonts loaded by the Apollo13 Themes Framework.
	 */
	function apollo13framework_get_web_fonts_dynamic() {
		return;
	}
}

if ( ! function_exists( 'apollo13framework_get_web_fonts_static' ) ) {
	/**
	 * Dequeue Google Fonts loaded by the Apollo13 Themes Framework.
	 */
	function apollo13framework_get_web_fonts_static() {
		return;
	}
}

if ( ! function_exists( 'hemingway_get_google_fonts_url' ) ) {
	/**
	 * Dequeue Google Fonts loaded by the Hemingway theme.
	 */
	function hemingway_get_google_fonts_url() {
		return false;
	}
}

/**
 * Dequeue Google Fonts loaded by the Avia framework (Enfold theme).
 */
add_action( 'init', 'fpp_enfold_customization_switch_fonts' );
function fpp_enfold_customization_switch_fonts() {
		if ( class_exists( 'avia_style_generator' ) ) {
	    global $avia;
	    $avia->style->print_extra_output = false;
		}
}

/**
 * Dequeue Google Fonts loaded by Avada theme.
 */
$fusion_options = get_option( 'fusion_options', false );
if (
		$fusion_options
		&& isset( $fusion_options['gfonts_load_method'] )
		&& $fusion_options['gfonts_load_method'] === 'cdn'
	) {
	add_filter(
		'fusion_google_fonts',
		function( $fonts ) {
			return array();
		},
		99999
	);
}

/**
 * Avada caches the CSS output so we need to clear the
 * cache once the fonts have been removed.
 */
function fpp_flush_avada_cache() {
	if ( function_exists( 'fusion_reset_all_caches' ) ) {
		fusion_reset_all_caches();
	}
}
register_activation_hook( __FILE__, 'fpp_flush_avada_cache' );

/**
 * WPBakery enqueues fonts correctly using wp_enqueue_style
 * but does it late so this is required.
 */
function fpp_dequeue_wpbakery_fonts() {
	global $wp_styles;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	$allowed = apply_filters(
		'fpp_exceptions',
		[ 'olympus-google-fonts' ]
	);

	foreach ( $wp_styles->registered as $style ) {
		$handle = $style->handle;
		$src    = $style->src;

		if ( strpos( $src, 'fonts.googleapis' ) !== false ) {
			if ( ! array_key_exists( $handle, array_flip( $allowed ) ) ) {
				wp_dequeue_style( $handle );
			}
		}
	}
}
add_action( 'wp_footer', 'fpp_dequeue_wpbakery_fonts' );

/**
 * Dequeue Google Fonts loaded by Kadence theme.
 */
add_filter( 'kadence_theme_google_fonts_array', '__return_empty_array' );
add_filter( 'kadence_print_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded by X theme.
 */
add_filter( 'cs_load_google_fonts', '__return_false' );
