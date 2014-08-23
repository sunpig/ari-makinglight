<?php

/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function ml_comment($comment, $args, $depth) {
	// Note the two element IDs with comment IDs in them for backwards compatibility
	// with old Making Light comment permalinks */
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
			?>
			<li class="post pingback">
				<p><?php _e( 'Pingback:', 'ml' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'ml' ), ' ' ); ?></p>
			<?php
			break;
		default :
			?>
			<li <?php comment_class(); ?> id="li-comment-<?= comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment">
					<span id="<?php comment_ID(); ?>"></span>
					<footer>
						<div class="comment-author vcard">
							<?php echo get_avatar( $comment, 50 ); ?>
						</div><!-- .comment-author .vcard -->
					</footer>

					<div class="comment-content">
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'ml' ); ?></em>
							<br />
						<?php endif; ?>
						<div class="comment-meta commentmetadata">
							:::
							<?php printf('<cite class="fn">%s</cite>', get_comment_author_link()); ?>
							:::
							<a href="<?= ML_Commenter_Comments::getViewAllByUrl() ?>?comment_id=<?= comment_ID() ?>">(view all by)</a>
							:::
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
							<?php
								/* translators: 1: date, 2: time */
								printf( __( '%1$s at %2$s', 'ml' ), get_comment_date(), get_comment_time() ); ?>
							</time></a>
							<?php edit_comment_link( __( ' (Edit &rarr;)', 'ml' ), ' ' ); ?>
							:
						</div><!-- .comment-meta .commentmetadata -->
						<?php comment_text(); ?>
						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</div><!-- .reply -->
					</div>
				</article><!-- #comment-## -->

			<?php
			break;
	endswitch;
}

/**
 * Comment template variant for view-all-by.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function ml_comment_view_all_by($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	$post = get_post($comment->comment_post_ID);
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div><!-- .comment-author .vcard -->
			</footer>

			<div class="comment-content">
				<div class="comment-meta commentmetadata">
					Posted on entry <a href="<?= esc_url(get_permalink($post)) ?>"><?= get_the_title($post) ?></a>
					:::
					<a href="<?= esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'ml' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( ' (Edit &rarr;)', 'ml' ), ' ' ); ?>
					:
				</div><!-- .comment-meta .commentmetadata -->
				<?php comment_text(); ?>
			</div>
		</article><!-- #comment-## -->
	<?php
}

/**
 * Comment template variant for recent comments page.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function ml_comment_recent_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	$post = get_post($comment->comment_post_ID);
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div><!-- .comment-author .vcard -->
			</footer>

			<div class="comment-content">
				<div class="comment-meta commentmetadata">
					:::
					<?php printf('<cite class="fn">%s</cite>', get_comment_author_link()); ?>
					:::
					<a href="<?= ML_Commenter_Comments::getViewAllByUrl() ?>?comment_id=<?= comment_ID() ?>">(view all by)</a>
					:::
					on entry <a href="<?= esc_url(get_permalink($post)) ?>"><?= get_the_title($post) ?></a>
					:::
					<a href="<?= esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'ml' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( ' (Edit &rarr;)', 'ml' ), ' ' ); ?>
					:
				</div><!-- .comment-meta .commentmetadata -->
				<?php comment_text(); ?>
			</div>
		</article><!-- #comment-## -->
	<?php
}

/**
 * Comment template variant for recent comment links page.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function ml_comment_recent_comment_links($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	$post = get_post($comment->comment_post_ID);
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div><!-- .comment-author .vcard -->
			</footer>

			<div class="comment-content">
				<div class="comment-meta commentmetadata">
					:::
					<?php printf('<cite class="fn">%s</cite>', get_comment_author_link()); ?>
					:::
					<a href="<?= ML_Commenter_Comments::getViewAllByUrl() ?>?comment_id=<?= comment_ID() ?>">(view all by)</a>
					:::
					on entry <a href="<?= esc_url(get_permalink($post)) ?>"><?= get_the_title($post) ?></a>
					:::
					<a href="<?= esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'ml' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( ' (Edit &rarr;)', 'ml' ), ' ' ); ?>
					:
				</div><!-- .comment-meta .commentmetadata -->
			</div>
		</article><!-- #comment-## -->
	<?php
}

/**
 * Comment template variant for recent comments sidebar
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function ml_comment_recent_comment_sidebar($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	$post = get_post($comment->comment_post_ID);
	?>
		<li class="recentcomments">
			<a href="<?= esc_url( get_comment_link($comment->comment_ID) ) ?>">
				<?= get_comment_author($comment->comment_ID) ?> on <?= get_the_title($comment->comment_post_ID) ?>
			</a>
		</li>
	<?php
}
