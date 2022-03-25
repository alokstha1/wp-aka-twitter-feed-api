<?php
/**
 * Twitter api setup
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Initialize Wp_Aka_Twitter_Feeds class
*/
class Wp_Aka_Twitter_Feeds {

	/**
	* Class constructor
	*/
	function __construct() {

		//add menu options
		add_action( 'admin_menu', array( $this, 'watfa_register_menu_page' ) );

		// Register a setting and its data.
		add_action( 'admin_init', array( $this, 'watfa_register_settings' ) );

		// enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'watfa_admin_enqueues' ) );

		// enqueue frontend scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'watfa_frontend_enqueues' ) );

		// wp_head hook to add styles
		add_action( 'wp_head', array( $this, 'watfa_wp_head_hook' ) );

		// tweets feeds shortcode
		add_shortcode( 'watfa_twitter_feeds', array( $this, 'watfa_twitter_feeds_shortcode' ) );

		//register widget action hook
		add_action( 'widgets_init', array( $this, 'watfa_widgets_init' ) );

	}

	/**
	* Add menu page to the dashboard menu.
	*/
	public function watfa_register_menu_page() {
		add_menu_page( __( 'Twitter Feeds', 'watfa' ), __( 'Twitter Feeds', 'watfa' ), 'manage_options', 'twitter-feeds.php', array( $this, 'atf_add_setting_page' ), '', 20 );

	}

	/**
	* Callback function of add_menu_page. Displays the page's content.
	*/
	public function atf_add_setting_page() {

		//included the plugin option settings html
		include_once ATF_PLUGIN_DIR . '/includes/twitter-feeds-settings.php';

	}

	/**
	* Registers a text field setting save to options table.
	*/
	public function watfa_register_settings() {
		register_setting( 'watfa_twitter_feeds_options', 'watfa_twitter_feeds_options', array( $this, 'atf_sanitize_settings' ) );
	}

	/**
	* Save admin form settings value to watfa_twitter_feeds_options option.
	*/
	public function atf_sanitize_settings() {

		if ( ! isset( $_POST['validate_submit'] ) || ! wp_verify_nonce( $_POST['validate_submit'], 'atf_nonce_feeds' ) ) {
			return false;
		}

		$input_options = array();

		//Enable/Disable widgets
		$input_options['widget_enable'] = isset( $_POST['watfa_twitter_feeds_options']['widget_enable'] ) ? 1 : 0;
		//Username
		$username                  = sanitize_text_field( $_POST['watfa_twitter_feeds_options']['username'] );
		$input_options['username'] = strip_tags( $username );

		// Consumer Key
		$input_options['consumer_key'] = sanitize_text_field( $_POST['watfa_twitter_feeds_options']['consumer_key'] );
		// Consumer Secret Key
		$input_options['consumer_secret'] = sanitize_text_field( $_POST['watfa_twitter_feeds_options']['consumer_secret'] );
		// Access Token
		$input_options['access_token'] = sanitize_text_field( $_POST['watfa_twitter_feeds_options']['access_token'] );
		// Access Token Secret
		$input_options['access_token_secret'] = sanitize_text_field( $_POST['watfa_twitter_feeds_options']['access_token_secret'] );

		//Show/Hide Title
		$input_options['title_enable'] = isset( $_POST['watfa_twitter_feeds_options']['title_enable'] ) ? 1 : 0;

		//Number of tweets
		$input_options['tweets_count'] = intval( $_POST['watfa_twitter_feeds_options']['tweets_count'] );

		// tweets title
		$input_options['tweets_title'] = sanitize_text_field( $_POST['watfa_twitter_feeds_options']['tweets_title'] );

		// Background Color
		$input_options['background_color'] = ( ! empty( $_POST['watfa_twitter_feeds_options']['background_color'] ) ) ? sanitize_hex_color( $_POST['watfa_twitter_feeds_options']['background_color'] ) : '';

		// Text Color
		$input_options['text_color'] = ( ! empty( $_POST['watfa_twitter_feeds_options']['text_color'] ) ) ? sanitize_hex_color( $_POST['watfa_twitter_feeds_options']['text_color'] ) : '';

		//Show/Hide Url
		$input_options['url_enable'] = isset( $_POST['watfa_twitter_feeds_options']['url_enable'] ) ? 1 : 0;

		return $input_options;
	}

	/**
	* Admin enqueue scripts
	**/
	public function watfa_admin_enqueues() {
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script( 'atf-admin-script', ATF_PLUGIN_URL . 'assets/js/atf-admin-script.js', array( 'wp-color-picker' ), false, true );
	}

	/**
	* Frontend enqueue scripts
	**/
	public function watfa_frontend_enqueues() {
		wp_enqueue_style( 'atf-style', ATF_PLUGIN_URL . 'assets/css/atf-style.css' );
	}

	/**
	* `watfa_twitter_feeds` shortcode call back function
	**/
	public function watfa_twitter_feeds_shortcode( $atts ) {

		require_once( ATF_PLUGIN_DIR . '/twitteroauth/twitteroauth.php' );

		$watfa_twitter_feeds_options = get_option( 'watfa_twitter_feeds_options' );

		$consumer_key        = trim( $watfa_twitter_feeds_options['consumer_key'] );
		$consumer_secret     = trim( $watfa_twitter_feeds_options['consumer_secret'] );
		$access_token        = trim( $watfa_twitter_feeds_options['access_token'] );
		$access_token_secret = trim( $watfa_twitter_feeds_options['access_token_secret'] );

		$tweets_count = ( isset( $watfa_twitter_feeds_options['tweets_count'] ) && ! empty( $watfa_twitter_feeds_options['tweets_count'] ) ) ? intval( $watfa_twitter_feeds_options['tweets_count'] ) : 5;
		$username     = $watfa_twitter_feeds_options['username'];
		$title        = esc_attr( $watfa_twitter_feeds_options['tweets_title'] );
		$title_flag   = $watfa_twitter_feeds_options['title_enable'];
		$url_enable   = $watfa_twitter_feeds_options['url_enable'];

		$api_call = new viwptf_TwitterOAuth(
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
		ob_start();

		if ( $title_flag ) {

			echo sprintf( '%1s%2s%3s', __( '<h2 class="widget-title">', 'watfa' ), $title, __( '</h2>', 'watfa' ) );
		}

		echo '<ul class="atf-tweets atf-shortcode">';
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
					$full_name   = $fetched_value->user->name;
					$tweet_id    = $fetched_value->id_str;
					$permalink   = 'https://twitter.com/' . $username . '/status/' . $tweet_id;
					$text        = $this->atf_sanitize_links( $fetched_value );
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

			if ( ! empty( $url_enable ) && 1 == $url_enable ) {
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
		return ob_get_clean();

	}

	/**
	* Sanitize tweets contents
	**/
	public function atf_sanitize_links( $tweet ) {
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
	* Hook to add style to header
	**/
	public function watfa_wp_head_hook() {

		$watfa_twitter_feeds_options = get_option( 'watfa_twitter_feeds_options' );
		?>
			<style type="text/css">

				ul.atf-tweets.atf-shortcode li.tweet-item {
					background: <?php echo esc_attr( $watfa_twitter_feeds_options['background_color'] ); ?>;
					color: <?php echo esc_attr( $watfa_twitter_feeds_options['text_color'] ); ?> !important;
				}
			</style>
		<?php
	}

	/**
	*
	**/
	public function watfa_widgets_init() {
		register_widget( 'Wp_Aka_Twitter_Feeds_Widget' );
	}
}

$aka_twitter_feeds = new Wp_Aka_Twitter_Feeds();
