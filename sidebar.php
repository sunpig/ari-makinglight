<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Ari
 * @since Ari 1.1.2
 */
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php do_action( 'before_sidebar' ); ?>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary .widget-area -->
