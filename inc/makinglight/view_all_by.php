<?php
/**
 * Making Light supporting code for the view-all-by page template.
 *
 */

class ML_View_All_By {

	/* 
	 * In order to consume custom querystring variables, they must be added
	 * to the list of public querystring variables available to WP_Query.
	 * See http://codex.wordpress.org/Function_Reference/get_query_var
	 */
	function add_query_vars_filter( $vars ){
		$vars[] = "comment_id";
		$vars[] = "all";
		return $vars;
	}

	/*
	 * The view-all-by page should not appear in a list of content pages 
	 */
	function exclude_vab_page($pages) {
		$count = count($pages);
		for ($i = 0; $i < $count; $i++) {
			$page = $pages[$i];
			if ($page->post_name == 'view-all-by') {
				unset($pages[$i]);
			}
		}
		// reindex array
		$pages = array_values($pages);

		return $pages;
	}

}

$vab = new ML_View_All_By();
add_filter( 'query_vars', array($vab, 'add_query_vars_filter') );
// add_filter( 'template_include', array($vab, 'set_page_template') );
add_filter( 'get_pages' , array($vab, 'exclude_vab_page') );


class ML_Commenter_Comments {

	public $origin_comment;
	public $comments;
	public $all_comments_count = 0;
	public $populated_comments_count = 0;

	public function populate($origin_comment_id, $max_count) {
		if (!$this->origin_comment = get_comment($origin_comment_id)) {
			return;
		}

		$this->all_comments_count = $this->getCountAllCommentsByCommentAuthorEmail($this->origin_comment->comment_author_email);
		if (isset($max_count) && ($max_count < $this->all_comments_count)) {
			$n = $max_count;
		} else {
			$n = $this->all_comments_count;
		}
		$this->comments = $this->getCommentsByCommentAuthorEmail($this->origin_comment->comment_author_email, $n);
		$this->populated_comments_count = count($this->comments);
	}

	public function getTitle() {
		if (!$this->origin_comment) {
			return 'No comments found';
		}

		$all_count = $this->all_comments_count;
		$populated_count = $this->populated_comments_count;
		if ($populated_count == 1) {
			$title = "Only comment by ";
		} else if ($all_count == $populated_count) {
			$title = "All $all_count comments by ";
		} else {
			$title = "Last $populated_count comments by ";
		}
		$title .= $this->origin_comment->comment_author;

		return $title;
	}

	public function areAllCommentsLoaded() {
		return $this->all_comments_count == $this->populated_comments_count;
	}

	public function getLoadTimeWarning() {
		$load_time_warning = "";
		$all_comments_count = $this->all_comments_count;
		if ($all_comments_count > 200) {
			$timeToLoad = "some time";
			if ($all_comments_count < 1000) {
				$timeToLoad = "some time";
			} else if ($all_comments_count < 2000) {
				$timeToLoad = "quite a while";
			} else if ($all_comments_count < 4000) {
				$timeToLoad = "a frightfully long time";
			} else {
				$timeToLoad = "\$WHY_ARE_YOU_DOING_THIS_TO_ME days";
			}
			$load_time_warning = "(May take $timeToLoad to load.)";
		}
		return $load_time_warning;
	}

	public function getViewAllUrl() {
		return get_permalink() . "?comment_id=" . $this->origin_comment->comment_ID . "&all=true";
	}

	public function getYearlyCommentCountsByCommentAuthorEmail() {
		if (!$this->origin_comment) {
			return false;
		}

		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT year(comment_date) as year, count(*) as comments from $wpdb->comments where comment_author_email=%s group by year(comment_date) order by year(comment_date) asc",
			$this->origin_comment->comment_author_email
		);
		$results = $wpdb->get_results($query);
		return $results;
	}

	public function getOriginCommenterName() {
		if (!$this->origin_comment) {
			return '';
		}
		return $this->origin_comment->comment_author;
	}

	private function getCommentsByCommentAuthorEmail($comment_author_email, $n) {
		$args = array(
			'author_email' => $comment_author_email,
			'status' => 'approve',
			'number' => $n
		);
		return get_comments($args);
	}

	private function getCountAllCommentsByCommentAuthorEmail($comment_author_email) {
		$args = array(
			'author_email' => $comment_author_email,
			'status' => 'approve',
			'count' => true
		);
		return get_comments($args);
	}
}