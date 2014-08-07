<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package Ari
 * @since Ari 1.1.2
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @uses ari_header_style()
 * @uses ari_admin_header_style()
 * @uses ari_admin_header_image()
 *
 * @package Ari
 * @since Ari 1.1.2
 */
function ari_custom_header_setup() {
	$options = ari_get_theme_options();

	$current_color_scheme = $options['color_scheme'];
	switch ( $current_color_scheme ) {
		case 'dark' :
			$header_color = '8a8a8a';
			break;
		default:
			$header_color = '88c34b';
			break;
	}

	$args = array(
		'default-image'          => '',
		'default-text-color'     => $header_color,
		'width'                  => 240,
		'height'                 => 75,
		'flex-height'            => true,
		'wp-head-callback'       => 'ari_header_style',
		'admin-head-callback'    => 'ari_admin_header_style',
		'admin-preview-callback' => 'ari_admin_header_image',
	);

	$args = apply_filters( 'ari_custom_header_args', $args );

	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'ari_custom_header_setup' );

/**
 * Shiv for get_custom_header().
 *
 * get_custom_header() was introduced to WordPress
 * in version 3.4. To provide backward compatibility
 * with previous versions, we will define our own version
 * of this function.
 *
 * @return stdClass All properties represent attributes of the curent header image.
 *
 * @package Ari
 * @since Ari 1.1.2
 */

if ( ! function_exists( 'get_custom_header' ) ) {
	function get_custom_header() {
		return (object) array(
			'url'           => get_header_image(),
			'thumbnail_url' => get_header_image(),
			'width'         => HEADER_IMAGE_WIDTH,
			'height'        => HEADER_IMAGE_HEIGHT,
		);
	}
}

if ( ! function_exists( 'ari_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @since Ari 1.1.2
 */
function ari_header_style() {

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		.site-title,
		.site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		.site-title a {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // ari_header_style

if ( ! function_exists( 'ari_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 *
 * @since Ari 1.1.2
 */
function ari_admin_header_style() {
	$options = ari_get_theme_options();
	$current_color_scheme = $options['color_scheme'];
	$text_color = $options['text_color'];

	if ( '' != get_background_color() && '' == get_background_image() ) :
		$background_color = get_background_color();
	else :
		switch ( $current_color_scheme ) {
			case 'dark' :
				$background_color = '1b1c1b';
				break;
			default:
				$background_color = 'ffffff';
				break;
		}
	endif;
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		background-color: #<?php echo $background_color; ?>;
		border: none;
		max-width: 240px;
		padding: 10px 10px 6px 10px;
	}
	#headimg h1 {
		font-family: 'Droid Sans', Arial, sans-serif;
		font-size: 30px;
		line-height: 35px;
		margin: 0 0 5px 0;
	}
	#headimg h1 a {
		color: #<?php echo get_header_textcolor(); ?>;
		text-decoration: none;
	}
	#desc {
		color: <?php echo $text_color; ?> !important;
		font-family: 'Droid Serif', Times, serif;
		font-size: 13px;
		font-style: italic;
		line-height: 18px;
		margin: 0 0 10px 0;
	}
	</style>
<?php
}
endif; // ari_admin_header_style

if ( ! function_exists( 'ari_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 *
 * @since Ari 1.1.2
 */
function ari_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // ari_admin_header_image