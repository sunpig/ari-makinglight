<?php
/**
 * The Sidebar containing the secondary widget areas.
 *
 * @package Ari
 * @since Ari 1.1.2
 */
?>

<div id="tertiary" class="widget-area" role="complementary">
	<?php do_action( 'before_sidebar' ); ?>
	<?php dynamic_sidebar( 'sidebar-2' ); ?>
</div><!-- #tertiary .widget-area -->
