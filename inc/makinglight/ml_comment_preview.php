<?php
/**
 * Making Light supporting code for comment previews.
 *
 */

class ML_Comment_Preview {

	const NONCE_FIELD_NAME = '_ml_nonce';
	const COMMENT_PREVIEW_ACTION = 'ml_comment_preview';
	const REAL_POST_SUBMIT_VALUE = 'POST';

	private $comment_author;
	private $comment_author_email;
	private $comment_author_url;
	private $comment_raw;
	private $comment_sanitized;
	private $post_id;

	function init_from_form() {
		$this->comment_author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
		$this->comment_author_email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
		$this->comment_author_url = filter_input(INPUT_POST, "url", FILTER_SANITIZE_URL);
		$this->comment_raw = $_POST['comment'];
		$this->comment_sanitized = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);
		$this->post_id = filter_input(INPUT_POST, "comment_post_ID", FILTER_VALIDATE_INT);
	}

	/*
	 * Handler for the pre_comment_on_post action.
	 * If the submit button pressed was "preview",
	 * just preview the comment instead of posting it.
	 */
	function pre_comment_check() {
		$preview_nonce = isset($_POST[self::NONCE_FIELD_NAME]) ? $_POST[self::NONCE_FIELD_NAME] : '';
		$is_valid_nonce = !!wp_verify_nonce($preview_nonce, self::COMMENT_PREVIEW_ACTION);
		$is_real_submit = strtoupper(trim($_POST['submit'])) === self::REAL_POST_SUBMIT_VALUE;
		if ($is_real_submit && $is_valid_nonce) {
			// Carry on with posting the comment
		} else {
			// Show the comment preview page
			get_template_part('comment-preview');
			exit;
		}
	}

	function show_actual_submit_button() {
		$real_submit_html = '<p class="form-submit">'
			. '<input name="submit" type="submit" id="submit" value="POST">'
			. wp_nonce_field(self::COMMENT_PREVIEW_ACTION, self::NONCE_FIELD_NAME, true, false)
			. '</p>';
		echo $real_submit_html;
	}

	function get_current_commenter_array() {
		return array(
			'comment_author' => $this->comment_author,
			'comment_author_email' => $this->comment_author_email,
			'comment_author_url' => $this->comment_author_url
		);
	}

	function show_comment_preview_form() {
		add_filter('wp_get_current_commenter', array($this, 'get_current_commenter_array'), 10, 0);
		add_action('comment_form', array($this, 'show_actual_submit_button'));

		$args = array(
			'label_submit' => 'PREVIEW AGAIN',
			'comment_field' => '<p class="comment-form-comment"><label for="comment">'
				. _x( 'Comment', 'noun' )
				. '</label> <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">'
				. $this->comment_sanitized
				. '</textarea></p>'
		);
		comment_form($args, $this->post_id);
	}

	function show_comment_preview() {
		ml_comment_preview(
			$this->comment_author,
			$this->comment_author_email,
			$this->comment_author_url,
			$this->comment_raw,
			$this->post_id
		);
	}

}

// register pre-comment-on-post handler
function register_ml_comment_preview() {
	$mcp = new ML_Comment_Preview();
	add_action('pre_comment_on_post', array($mcp, 'pre_comment_check'));
}
register_ml_comment_preview();
