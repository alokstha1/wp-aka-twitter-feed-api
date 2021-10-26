<?php
/**
* Twitter api widget settings page
**/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wp_Aka_Twitter_Feeds_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'Wp_Aka_Twitter_Feeds_Widget', // Base ID
			__( 'WATFA Twitter Feeds', 'watfa' ), // Name
			array(
				'description' => __( 'Display the number of Tweets with Twitter Api.', 'watfa' ),
			) // Widget Options
		);

		// enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'watfa_admin_widget_enqueues' ) );
	}

	public function watfa_admin_widget_enqueues() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'atf-widget-script', ATF_PLUGIN_URL . 'assets/js/atf-widget-script.js', array(), false, true );
	}

	/**
	* Echoes the widget content.
	**/
	public function widget( $args, $instance ) {

		$watfa_twitter_feeds_options = get_option( 'watfa_twitter_feeds_options' );
		
		if ( $watfa_twitter_feeds_options['widget_enable'] ) {
			if ( ! empty( $instance['title'] ) && 1 == $instance['show_title'] ) {
				echo $args['before_title'] . $instance['title'] . $args['after_title'];

			}
			require_once( dirname( ATF_PLUGIN_FILE ) . '/twitteroauth/twitteroauth.php' );

			$consumer_key          = trim( $watfa_twitter_feeds_options['consumer_key'] );
			$consumer_secret       = trim( $watfa_twitter_feeds_options['consumer_secret'] );
			$access_token          = trim( $watfa_twitter_feeds_options['access_token'] );
			$access_token_secret   = trim( $watfa_twitter_feeds_options['access_token_secret'] );
			$tweets_count = ( isset( $instance['tweets_count'] ) && ! empty( $instance['tweets_count'] ) ) ? intval( $instance['tweets_count'] ) : 5;
			$username              = $watfa_twitter_feeds_options['username'];
			$background_color      = esc_attr( $instance['widget_background_color'] );
			$text_color            = esc_attr( $instance['widget_text_color'] );

			$api_call       = new viwptf_TwitterOAuth(
				$consumer_key,
				$consumer_secret,
				$access_token,
				$access_token_secret
			);
			$fetched_tweets = $api_call->get(
				'statuses/user_timeline',
				array(
					'screen_name'     => $username,
					'count'           => $tweets_count,
					'exclude_replies' => true,
				)
			);
			
			echo $args['before_widget'];
			echo '<ul class="atf-tweets">';
			if ( 200 != $api_call->http_code ) {

				?>
					<li class="tweet-item">
						<?php
							$profile_link = 'https://twitter.com/' . $username;
							echo sprintf( __( 'Follow us <a href="%1$s" target="_blank"> @%2$s</a>', 'watfa' ), $profile_link, $username );
						?>
					</li>
				<?php

			} else {
				$count = 1;
				foreach ( $fetched_tweets as $key => $fetched_value ) {
					if ( $count <= $tweets_count ) {
						$screen_name = $fetched_value->user->screen_name;
						$full_name = $fetched_value->user->name;
						$tweet_id    = $fetched_value->id_str;
						$permalink   = 'https://twitter.com/' . $username . '/status/' . $tweet_id;
						$text        = $this->atf_widget_sanitize_links( $fetched_value );
						?>
							<li class="tweet-item">
								<div class="twitter-avatar">
									<?php

										$image_url = $fetched_value->user->profile_image_url;
									?>
										<img src="<?php echo str_replace( 'http://', '//', $image_url ); ?>" width="45px" height="45px" alt="Tweet Avatar">
								</div>

								<div class="twitter-username">
									<span class="screen-name"><?php echo $full_name; ?> </span>
									<a href="https://twitter.com/<?php echo $screen_name; ?>\" target="_blank" dir="ltr">@<?php echo $screen_name; ?></a>
								</div>

								<div class="tweet-data">
									<?php echo $text; ?>
								</div>
							</li>
						<?php
						++$count;
					}
				}
				if ( ! empty( $instance['show_page_link'] ) && 1 == $instance['show_page_link'] ) {
					?>
						<li class="tweet-item">
							<?php
								$profile_link = 'https://twitter.com/' . $username;
								echo sprintf( __( 'Follow us <a href="%1$s" target="_blank"> @%2$s</a>', 'watfa' ), $profile_link, $username );
							?>
						</li>
					<?php
				}
			}

			echo '</ul>';
			?>
				<style type="text/css">

					section.widget_aka_twitter_feeds_widget ul.atf-tweets li.tweet-item {
						background: <?php echo esc_attr( $background_color ); ?>;
						color: <?php echo esc_attr( $text_color ); ?> !important;
					}

				</style>
			<?php
			echo $args['after_widget'];
		}
	}

	/**
	* Sanitize tweets contents
	**/
	public function atf_widget_sanitize_links( $tweet ) {
		if ( isset( $tweet->retweeted_status ) ) {
			$rt_section = current( explode( ':', $tweet->text ) );
			$text       = $rt_section . ': ';
			$text      .= $tweet->retweeted_status->text;
		} else {
			$text = $tweet->text;
		}
		$text = preg_replace( '/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text );
		$text = preg_replace( '/[@]+([A-Za-z0-9-_]+)/', '<a href="https://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text );
		$text = preg_replace( '/[#]+([A-Za-z0-9-_]+)/', '<a href="https://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text );
		return $text;

	}


	/**
	* Outputs the settings update form.
	**/
	public function form( $instance ) {

		$tweets_count = ( isset( $instance['tweets_count'] ) && ! empty( $instance['tweets_count'] ) ) ? $instance['tweets_count'] : '';

		$widget_title     = ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		$tweets_count     = ( isset( $instance['tweets_count'] ) && ! empty( $instance['tweets_count'] ) ) ? esc_attr( $instance['tweets_count'] ) : '';
		$background_color = ( isset( $instance['widget_background_color'] ) && ! empty( $instance['widget_background_color'] ) ) ? esc_attr( $instance['widget_background_color'] ) : '';
		$text_color       = ( isset( $instance['widget_text_color'] ) && ! empty( $instance['widget_text_color'] ) ) ? esc_attr( $instance['widget_text_color'] ) : '';
		$show_page_link   = ( isset( $instance['show_page_link'] ) && ! empty( $instance['show_page_link'] ) ) ? $instance['show_page_link'] : '';
		$show_title       = ( isset( $instance['show_title'] ) && ! empty( $instance['show_title'] ) ) ? $instance['show_title'] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title: ', 'watfa' ); ?><input class="atf-fields" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>" /></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" value="true" <?php checked( ( isset( $instance['show_title'] ) && ( 'true' == $instance['show_title'] ) ), true ); ?> id="<?php echo $this->get_field_id( 'show_title' ); ?>" name="<?php echo $this->get_field_name( 'show_title' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Show Title', 'watfa' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'tweets_count' ); ?>"><?php _e( 'Tweets Count: ', 'watfa' ); ?><input class="atf-fields" id="<?php echo $this->get_field_id( 'tweets_count' ); ?>" name="<?php echo $this->get_field_name( 'tweets_count' ); ?>" type="text" value="<?php echo esc_attr( $tweets_count ); ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'widget_background_color' ); ?>"><?php _e( 'Background Color', 'watfa' ); ?></label><input class="background-color color-field atf-fields" id="<?php echo $this->get_field_id( 'widget_background_color' ); ?>" name="<?php echo $this->get_field_name( 'widget_background_color' ); ?>" type="text" value="<?php echo esc_attr( $background_color ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'widget_text_color' ); ?>"><?php _e( 'Text Color', 'watfa' ); ?></label><input class="text-color color-field atf-fields" id="<?php echo $this->get_field_id( 'widget_text_color' ); ?>" name="<?php echo $this->get_field_name( 'widget_text_color' ); ?>" type="text" value="<?php echo esc_attr( $text_color ); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" value="true" <?php checked( ( isset( $instance['show_page_link'] ) && ( 'true' == $instance['show_page_link'] ) ), true ); ?> id="<?php echo $this->get_field_id( 'show_page_link' ); ?>" name="<?php echo $this->get_field_name( 'show_page_link' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_page_link' ); ?>"><?php _e( 'Show Twitter Page Link', 'watfa' ); ?></label>
		</p>
		<?php
	}

	/**
	* Updates a particular instance of a widget.
	**/
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']                   = strip_tags( $new_instance['title'] );
		$instance['tweets_count']            = intval( $new_instance['tweets_count'] );
		$instance['widget_background_color'] = sanitize_hex_color( $new_instance['widget_background_color'] );
		$instance['widget_text_color']       = sanitize_hex_color( $new_instance['widget_text_color'] );
		$instance['show_page_link']          = ( isset( $new_instance['show_page_link'] ) && ! empty( $new_instance['show_page_link'] ) ) ? true : false;
		$instance['show_title']              = ( isset( $new_instance['show_title'] ) && ! empty( $new_instance['show_title'] ) ) ? true : false;
		return $instance;
	}
}
