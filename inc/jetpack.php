<?php
/**
 * Infinite Scroll Support
 * See: http://jetpack.me/support/infinite-scroll/
 *
 * Theme Name: Ari
 */

/**
 * Add theme support for infinity scroll
 */
function ari_infinite_scroll_init() {
	add_theme_support( 'infinite-scroll', array(
		'footer'    => 'page',
	) );
}

add_action( 'after_setup_theme', 'ari_infinite_scroll_init' );

/**
 * Do we have footer widgets?
 *
 * @return bool
 */
function further_has_footer_widgets( $has_widgets ) {
	if ( ( Jetpack_User_Agent_Info::is_ipad() && is_active_sidebar( 'sidebar-2' ) ) || ( function_exists( 'jetpack_is_mobile' ) && jetpack_is_mobile() && is_active_sidebar( 'sidebar-2' ) ) )
		return true;

	return $has_widgets;
}
add_filter( 'infinite_scroll_has_footer_widgets', 'further_has_footer_widgets' );