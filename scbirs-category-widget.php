<?php
/*
Plugin Name: ScBirs - Category Widget Plugin With Thumbnail
Description: Displays the thumbnail of the most recent post and a list of recent posts in a specified category.
*/

if ( !defined('ABSPATH') ) {
	die('-1');
}

class scbirs_thumb_category_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
      'scbirs_thumb_category_widget',
		  'ScBirs Thumb Category Widget',
			array( 'description' => __( 'ScBirs - A widget to display the most recent posts and a header thumbnail from a single category' ) )
		);
	}

  public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Recent Posts in Category' );
		$cat = isset( $instance[ 'cat' ] ) ? intval( $instance[ 'cat' ] ) : 0;
		$qty = isset( $instance[ 'qty' ] ) ? intval( $instance[ 'qty' ] ) : 5;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Category:' ); ?></label>
			<?php wp_dropdown_categories( Array(
						'orderby'            => 'ID',
						'order'              => 'ASC',
						'show_count'         => 1,
						'hide_empty'         => 0,
						'hide_if_empty'      => false,
						'echo'               => 1,
						'selected'           => $cat,
						'hierarchical'       => 1,
						'name'               => $this->get_field_name( 'cat' ),
						'id'                 => $this->get_field_id( 'cat' ),
						'class'              => 'widefat',
						'taxonomy'           => 'category',
					) ); ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'qty' ); ?>"><?php _e( 'Quantity:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'qty' ); ?>" name="<?php echo $this->get_field_name( 'qty' ); ?>" type="number" min="1" step="1" value="<?php echo $qty; ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['cat'] = intval( $new_instance['cat'] );
		$instance['qty'] = intval( $new_instance['qty'] );
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$cat = $instance['cat'];
		$qty = (int) $instance['qty'];

		echo $before_widget;
    ?>
    <style type="text/css">
      .scbirs_thumb_category_widget-thumb > img {
        width: 100%;
        height: auto;
		padding: 1px;
		border: 1 solid lightgray;
		
	  }
    </style>
    <?php
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
    echo self::get_header_image($cat);
    echo self::get_cat_posts( $cat, $qty );
		echo $after_widget;
	}

  public function get_header_image($cat) {
    $posts = get_posts( Array(
			'cat'			=>	$cat,
			'orderby'		=>	'date',
			'order'			=>	'DESC',
			'numberposts'	=>	1
		));

    $post = $posts[0];

		$returnThis = '';
		if( count( $posts ) && has_post_thumbnail( $post->ID )) {

			$returnThis .= '<a class="'.__CLASS__.'-thumb" href="'.get_permalink( $post->ID ).'">';
      $returnThis .= get_the_post_thumbnail( $post->ID );
      $returnThis .= '</a>'."\r\n";
    }
    return $returnThis;
  }

	public function get_cat_posts( $cat, $qty ) {
		$posts = get_posts( Array(
			'cat'			=>	$cat,
			'orderby'		=>	'date',
			'order'			=>	'DESC',
			'numberposts'	=>	$qty
		));

		$returnThis = '';
		if( count( $posts ) )
			$returnThis .= '<ul class="'.__CLASS__.'">'."\r\n";
		foreach( $posts as $post )
			$returnThis .= "\t".'<li><a href="'.get_permalink( $post->ID ).'">'.$post->post_title.'</a></li>'."\r\n";
		if( count( $posts ) )
			$returnThis .= '</ul>'."\r\n";
		return $returnThis;
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget( "scbirs_thumb_category_widget" );' ) );
?>
