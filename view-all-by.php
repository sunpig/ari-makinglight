<?php
/**
 * Template Name: View-all-by
 *
 * Used for displaying all other comments by the author of a given comment. 
 */

get_header(); ?>

		<?php
			$comment_id = get_query_var('comment_id');
			$all = get_query_var('all');
			$max_count = $all ? 1000000 : 20;
			$mcc = new ML_Commenter_Comments();
			$mcc->populate($comment_id, $max_count);
		?>

		<div id="primary" class="site-content">
			<div id="content" role="main">

				<h1 class="entry-title"><?= $mcc->getTitle() ?></h1>

				<?php if ($mcc->comments) { ?>
					<?php if (!$mcc->areAllCommentsLoaded()) { ?>
						<p>
							<a href="<?= $mcc->getViewAllUrl() ?>">Show all <?= $mcc->all_comments_count ?> comments by <?= $mcc->origin_comment->comment_author ?></a>
							<?= $mcc->getLoadTimeWarning() ?>
						</p>
					<?php } ?>
					<div id="comments" class="comments-area">
						<ol class="commentlist">
							<?php
								/* Loop through and list the comments. Tell wp_list_comments()
								 * to use ml_comment() to format the comments.
								 */
								wp_list_comments( array( 'callback' => 'ml_comment_view_all_by' ), $mcc->comments );
							?>
						</ol>
					</div>
				<?php } ?>

			</div><!-- #content -->
		</div><!-- #primary .site-content -->

<?php get_footer(); ?>