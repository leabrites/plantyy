<?php

/**
 * Rewrite URLs to be locally hosted.
 */
function fpp_switch_stylesheet_src( $src, $handle ) {

	if ( $handle === 'olympus-google-fonts' ) {
		return $src;
	}

	$src = preg_replace( '/&ver=([^&]+)/', '', $src );
	if ( false !== strpos( $src, '//fonts.googleapis.com/css' ) ) {
		$font = new FPP_Host_Google_Fonts_Locally( $src );
		$font->set_font_format( fpp_get_font_format() );
		$src = $font->get_url();
	}
	return $src;
}
add_filter( 'style_loader_src', 'fpp_switch_stylesheet_src', 10, 2 );

/**
 * Rewrite RevSlider URLs to be locally hosted.
 */
function fpp_rev_slider_rewrite( $string ) {
 $rev_slider_global = json_decode( get_option('revslider-global-settings') );
 if ( $rev_slider_global->fontdownload == 'off' ) {

	 $url = fpp_extract_url( $string );

	 $local_url = fpp_switch_stylesheet_src( $url );

	 return str_replace( $url, $local_url, $string );
 }

 return $string;
}
add_filter('revslider_printCleanFontImport', 'fpp_rev_slider_rewrite');

/**
 * Extract URL from CSS style tag.
 */
function fpp_extract_url( $string ) {
	preg_match( '/href="(.*?)"/', $string, $matches );
	return $matches[1];
}
