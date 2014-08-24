<?php
/**
 * The template for displaying a comment preview.
 *
 * Shows a preview of the comment submitted via POST,
 * along with a comment form that includes the real
 * "submit" button.
 */

get_header(); ?>

		<div id="primary" class="site-content">
			<div id="content" role="main">

				<h1>Preview your comment</h1>

				<?php
					$mcp = new ML_Comment_Preview();
					$mcp->init_from_form();
					$mcp->show_comment_preview();
					$mcp->show_comment_preview_form();
				?>

			</div><!-- #content -->
		</div><!-- #primary .site-content -->

<?php get_footer(); ?>