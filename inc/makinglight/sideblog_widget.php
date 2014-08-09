<?php
/**
 * Making Light Sideblog widget
 *
 */

class ML_Sideblog_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'ml-sideblog', // Base ID
			__('ML Sideblog', 'ml'), // Name
			array( 'description' => __( 'Display entries from a Making Light sideblog', 'ml' ), ) // Args
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

		$target_blog_id = $instance['blog_id'];
		$this->show_sideblog_entries($target_blog_id);

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
			$title = __( 'Sideblog title', 'ml' );
		}
		if ( isset( $instance[ 'blog_id' ] ) ) {
			$blog_id = absint($instance[ 'blog_id' ]);
		}
		else {
			$blog_id = __( 'Blog ID', 'ml' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'blog_id' ); ?>"><?php _e( 'Blog ID:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'blog_id' ); ?>" name="<?php echo $this->get_field_name( 'blog_id' ); ?>" type="number" min="1" value="<?php echo esc_attr( $blog_id ); ?>">
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
		$instance['blog_id'] = ( ! empty( $new_instance['blog_id'] ) ) ? absint( $new_instance['blog_id'] ) : '';

		return $instance;
	}

	/**
	 * Display the sideblog content
	 */
	private function show_sideblog_entries($targetBlogId) {
		switch_to_blog($targetBlogId);

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 10
		);
		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {
			echo '<ul>';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				echo '<li><a href="' . get_permalink() . '">*</a> '. get_the_content() . '</li>';
			}
			echo '</ul>';
		} else {
			// no posts found
		}

		/* Restore original Blog ID */
		restore_current_blog();

		/* Restore original Post Data */
		wp_reset_postdata();
	}
}

// register ML_Sideblog_Widget widget
function register_ml_sideblog_widget() {
    register_widget( 'ML_Sideblog_Widget' );
}
add_action( 'widgets_init', 'register_ml_sideblog_widget' );
