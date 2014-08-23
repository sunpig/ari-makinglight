<?php
/**
 * Template Name: Recent Comments
 *
 * Used for displaying the most recent N comments. 
 */

get_header(); ?>

		<?php
			$n = filter_input(INPUT_GET, "n", FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 200)));
			if (!$n) {
				$n = 100;
			}
			$mrc = new ML_Recent_Comments();
			$comments = $mrc->getRecentComments($n, true, true);
			$recent_comments_url = get_permalink( get_page_by_path( 'recent-comments' ) );
			$recent_comment_links_url = get_permalink( get_page_by_path( 'recent-comment-links' ) );
		?>

		<div id="primary" class="site-content">
			<div id="content" role="main">

				<h1 class="entry-title">Most recent <?= $n ?> comments</h1>
				<p>
					<a href="<?= $recent_comments_url ?>?n=100">Last 100 comments</a> |
					<a href="<?= $recent_comment_links_url ?>?n=1000">Links to last 1000</a> | 
					<a href="<?= $recent_comment_links_url ?>?n=2000">Links to last 2000</a> | 
					<a href="<?= $recent_comment_links_url ?>?n=4000">Links to last 4000</a>
				</p>

				<div id="comments" class="comments-area">
					<ol class="commentlist">
						<?php
							/* Loop through and list the comments. Tell wp_list_comments()
							 * to use ml_comment() to format the comments.
							 */
							wp_list_comments( array( 'callback' => 'ml_comment_recent_comments' ), $comments );
						?>
					</ol>
				</div>

			</div><!-- #content -->
		</div><!-- #primary .site-content -->

<?php get_footer(); ?>