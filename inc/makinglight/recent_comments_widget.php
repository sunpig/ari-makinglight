<?php
/**
 * Making Light Recent Comments widget
 *
 */

class ML_Recent_Comments_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'ml-recent-comments', // Base ID
			__('ML Recent Comments', 'ml'), // Name
			array( 'description' => __( 'Display recent comments from Making Light', 'ml' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$this->show_recent_comments();

		echo $args['after_widget'];
	}


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = esc_attr($instance[ 'title' ]);
		}
		else {
			$title = __( 'Title', 'ml' );
		}
		if ( isset( $instance[ 'number' ] ) ) {
			$number = absint($instance[ 'number' ]);
		}
		else {
			$number = __( 'Number of comments to show', 'ml' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? absint( $new_instance['number'] ) : '';

		return $instance;
	}

	/**
	 * Display the recent comments content
	 */
	private function show_recent_comments() {
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => 10,
			'status'      => 'approve',
			'post_status' => 'publish'
		) ) );

		$output = '<ul id="recentcomments">';
		if ( $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment) {
				$output .=  '<li class="recentcomments"><a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_comment_author($comment->comment_ID) . ' on ' . get_the_title($comment->comment_post_ID) . '</a></li>';
			}
		}
		$output .= '</ul>';
		$output .= '<p><a href="recent-comments?n=1000">See last 1000 comments...</a></p>';
		$output .= '<p><a href="recent-comments?n=2000">See last 2000 comments...</a></p>';
		$output .= '<p><a href="recent-comments?n=4000">See last 4000 comments...</a></p>';
		echo $output;
	}
}

// register ML_Recent_Comments_Widget widget
function register_ml_recent_comments_widget() {
    register_widget( 'ML_Recent_Comments_Widget' );
}
add_action( 'widgets_init', 'register_ml_recent_comments_widget' );
