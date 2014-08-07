<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Ari
 * @since Ari 1.1.2
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since Ari 1.1.2
 */
function ari_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'ari_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * @since Ari 1.1.2
 */
function ari_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'ari_excerpt_length' );

/*
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Ari 1.1.2
 */
function ari_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'ari' ) . '</a>';
}

/**
 * Replaces "[…]" (appended to automatically generated excerpts) with an ellipsis and ari_continue_reading_link().
 *
 * @since Ari 1.1.2
 */
function ari_auto_excerpt_more( $more ) {
	return ' &hellip;' . ari_continue_reading_link();
}
add_filter( 'excerpt_more', 'ari_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * @since Ari 1.1.2
 */
function ari_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= ari_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'ari_custom_excerpt_more' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Ari 1.1.2
 */
function ari_body_classes( $classes ) {
	$options = ari_get_theme_options();
	$current_color_scheme = $options['color_scheme'];
	// Adds a class of dark when the current color scheme is dark
	// Add current color scheme class.
	switch ( $current_color_scheme ) {
		case 'dark' :
			$classes[] = 'color-dark';
			break;
		default :
			$classes[] = 'color-light';
			break;
	}
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() )
		$classes[] = 'group-blog';
	// Adds a class of sidebar-content when the secondary sidebar is disabled
	if ( ! is_active_sidebar( 'sidebar-2' ) )
		$classes[] = 'sidebar-content';
	return $classes;
}
add_filter( 'body_class', 'ari_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 *
 * @since Ari 1.1.2
 */
function ari_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'ari_enhanced_image_navigation', 10, 2 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since Ari 1.1.2
 */
function ari_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'ari' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'ari_wp_title', 10, 2 );
