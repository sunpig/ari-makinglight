<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Ari
 * @since Ari 1.1.2
 */
?>

<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
	<div id="secondary" class="widget-area" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- #secondary .widget-area -->
<?php endif; // end sidebar widget area ?>