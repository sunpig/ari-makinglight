<?php
/**
 * Making Light supporting code for the view-all-by page template.
 *
 */

class ML_Commenter_Comments {

	private static $viewAllByUrl;

	public static function getViewAllByUrl() {
		if (!self::$viewAllByUrl) {
			self::$viewAllByUrl = get_permalink( get_page_by_path( 'view-all-by' ) );
		}
		return self::$viewAllByUrl;
	}

	public $origin_comment;
	public $comments;
	public $all_comments_count = 0;
	public $populated_comments_count = 0;

	private $ml_recent_comments;

	public function __construct($ml_recent_comments = null) {
		$this->ml_recent_comments = $ml_recent_comments ?: new ML_Recent_Comments();
	}

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
			"SELECT year(comment_date_gmt) as year, count(*) as comments from $wpdb->comments where comment_author_email=%s and comment_approved = 1 group by year(comment_date_gmt) order by year(comment_date_gmt) asc",
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
		return $this->ml_recent_comments->getRecentCommentsByCommentAuthorEmail($comment_author_email, $n, true, true);
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