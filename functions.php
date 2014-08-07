<?php
/**
 * Ari functions and definitions
 *
 * @package Ari
 * @since Ari 1.1.2
 */

/**
 * Set up the content width value based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 450; /* Default content width */

/**
 * Adjust the content width based on the presence of active widgets, page templates, and attachment pages.
 *
 * @since Ari 1.1.2
 */
function ari_set_content_width() {
	global $content_width;

	if ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'full-width-page.php' ) || is_attachment() )
		$content_width = 660;
}
add_action( 'template_redirect', 'ari_set_content_width' );

if ( ! function_exists( 'ari_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since Ari 1.1.2
 */
function ari_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Ari, use a find and replace
	 * to change 'ari' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'ari', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ari' ),
	) );

	/**
	 * This theme allows users to set a custom background.
	 */
	add_theme_support( 'custom-background' );

	add_theme_support( 'print-style' );
}
endif; // ari_setup
add_action( 'after_setup_theme', 'ari_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since Ari 1.1.2
 */
function ari_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Primary Sidebar', 'ari' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Secondary Sidebar', 'ari' ),
		'id' => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'ari_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function ari_scripts() {
	global $post;

	wp_enqueue_style( 'style', get_stylesheet_uri() );

	wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.js', array( 'jquery' ), '20120206', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image( $post->ID ) ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'ari_scripts' );

/**
 * Enqueue font styles.
 */
function ari_fonts() {
	$protocol = is_ssl() ? 'https' : 'http';
	wp_enqueue_style( 'ari-droid-sans', "$protocol://fonts.googleapis.com/css?family=Droid+Sans:400,700" );
	wp_enqueue_style( 'ari-droid-serif', "$protocol://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic" );
}
add_action( 'wp_enqueue_scripts', 'ari_fonts' );

/**
 * Enqueue font style for the custom header admin page.
 */
function ari_admin_fonts( $hook_suffix ) {
	if ( 'appearance_page_custom-header' != $hook_suffix )
		return;

	$protocol = is_ssl() ? 'https' : 'http';
	wp_enqueue_style( 'ari-droid-sans', "$protocol://fonts.googleapis.com/css?family=Droid+Sans:400,700" );
	wp_enqueue_style( 'ari-droid-serif', "$protocol://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic" );
}
add_action( 'admin_enqueue_scripts', 'ari_admin_fonts' );

/**
 * Background wrapper style for front-end when a custom background image is set
 */
function ari_custom_background() {
	$options = ari_get_theme_options();
	$current_color_scheme = $options['color_scheme'];

	// Don't do anything if there is nothing for background image and background color.
	if ( '' == get_background_image() && '' == get_background_color() ) :
		return;

	// Background image is set but no background color, then use default background color for each color scheme
	elseif ( '' != get_background_image() && '' == get_background_color() ) :
		switch ( $current_color_scheme ) {
			case 'dark' :
				$background_color = '1b1c1b';
				break;
			default:
				$background_color = 'ffffff';
				break;
		}
	// If both background image and background color are set let's use the background color
	else :
		$background_color = get_background_color();
	endif;
?>
	<style type="text/css">
		#page,
		.main-navigation ul ul {
			background-color: #<?php echo $background_color; ?>
		}
	</style>
<?php
}
add_action( 'wp_head', 'ari_custom_background' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/tweaks.php';

/**
 * Custom Theme Options.
 */
require get_template_directory() . '/inc/theme-options/theme-options.php';

/*
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


// updater for WordPress.com themes
if ( is_admin() )
	include dirname( __FILE__ ) . '/inc/updater.php';
