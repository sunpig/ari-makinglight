<?php
/**
 * Making Light supporting code for the recent-comments page template.
 *
 */

class ML_Recent_Comments {

	/*
	 * Get a set of recent comments
	 */
	public function getRecentComments($n, $prime_posts_cache=false, $filter_unbpublished_posts=false) {
		// Performance note: adding a 'post_status' => 'publish' argument to the comments query
		// forces a join between the wp_*_comments and wp_*_posts table, which appears to be
		// super-expensive in a db the scale of Making Light. It's faster to programmatically filter
		// out comments on unpublished posts.
		$args = array(
			'status' => 'approve',
			'number' => $n
		);
		return $this->getComments($args, $prime_posts_cache, $filter_unbpublished_posts);
	}

	/*
	 * Get a set of recent comments for a particular comment author
	 */
	public function getRecentCommentsByCommentAuthorEmail($comment_author_email, $n, $prime_posts_cache=false, $filter_unbpublished_posts=false) {
		$args = array(
			'author_email' => $comment_author_email,
			'status' => 'approve',
			'number' => $n
		);
		return $this->getComments($args, $prime_posts_cache, $filter_unbpublished_posts);
	}

	/*
	 * Get a set of recent comments
	 */
	private function getComments($args, $prime_posts_cache, $filter_unbpublished_posts) {
		$comments = get_comments($args);
		if ($prime_posts_cache || $filter_unbpublished_posts) {
			$this->prime_post_cache_from_comments($comments);
		}
		if ($filter_unbpublished_posts) {
			$this->filter_comments_for_unpublished_posts($comments);
		}
		return $comments;
	}

	/*
	 * Eager load into cache all the associated posts for a list of comments
	 */
	private function prime_post_cache_from_comments($comments) {
		if (!empty($comments)) {
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			$non_cached_ids = $this->_get_non_cached_ids($post_ids, 'posts');
			if (!empty($non_cached_ids)) {
				global $wpdb;
				$fresh_posts = $wpdb->get_results( sprintf( "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE ID IN (%s)", join( ",", $non_cached_ids ) ) );
				update_post_cache($fresh_posts);
			}
		}
	}

	/*
	 * From an array of object ids, return a list of those not already being cached.
	 * This is from wordpress's own functions.php - copying here to avoid referencing a private API.
	 */
	private function _get_non_cached_ids( $object_ids, $cache_key ) {
		$clean = array();
		foreach ( $object_ids as $id ) {
			$id = (int) $id;
			if ( !wp_cache_get( $id, $cache_key ) ) {
				$clean[] = $id;
			}
		}

		return $clean;
	}

	/*
	 * Programmatically remove comments from an array of comments
	 * when the associated post is not published.
	 */
	private function filter_comments_for_unpublished_posts(&$comments) {
		foreach($comments as $comment) {
			if (!get_post_status($comment->comment_post_ID)=='publish') {
				unset($comment);
			}
		}
		$comments = array_values($comments);
	}

}
