<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$watfa_twitter_feeds_options = get_option( 'watfa_twitter_feeds_options' );
?>

<div class="wrap">

	<h1><?php _e( 'Twitter Feeds Api Settings', 'watfa' ); ?></h1>
	<?php settings_errors(); ?>

	<div id="atf-setting">

		<form method="POST" action="options.php" id="atf-setting-form">
			<?php
			if ( function_exists( 'wp_nonce_field' ) ) {
				wp_nonce_field( 'atf_nonce_feeds', 'validate_submit' );
			}
			?>

			<h3 class="hndle">
				<span><?php _e( 'Plugin options', 'watfa' ); ?></span>
			</h3>
			<table class="form-table">
				<tbody>
						<tr>
							<th>
								<label for="atf-widget-flag">
									<?php _e( 'Enable/Disable Widget:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="checkbox" name="watfa_twitter_feeds_options[widget_enable]" id="atf-widget-flag" value="1" <?php checked( $watfa_twitter_feeds_options['widget_enable'], true ); ?> />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-username">
									<?php _e( 'Twitter Username:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[username]" id="atf-username" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['username'] ) ) ? esc_attr( $watfa_twitter_feeds_options['username'] ) : ''; ?>" />
							</td>
						</tr>
						<tr>
							<th>
								<label for="atf-consumer-key">
									<?php _e( 'Consumer Key:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[consumer_key]" id="atf-consumer-key" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['consumer_key'] ) ) ? esc_attr( $watfa_twitter_feeds_options['consumer_key'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-consumer-secret">
									<?php _e( 'Consumer Secret:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[consumer_secret]" id="atf-consumer-secret" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['consumer_secret'] ) ) ? esc_attr( $watfa_twitter_feeds_options['consumer_secret'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-access-token">
									<?php _e( 'Access Token:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[access_token]" id="atf-access-token" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['access_token'] ) ) ? esc_attr( $watfa_twitter_feeds_options['access_token'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
							<label for="atf-access-token-secret">
								<?php _e( 'Access Token Secret:', 'watfa' ); ?>
							</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[access_token_secret]" id="atf-access-token-secret" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['access_token_secret'] ) ) ? esc_attr( $watfa_twitter_feeds_options['access_token_secret'] ) : ''; ?>" />
							</td>
						</tr>
				</tbody>
			</table>

			<h3 class="hndle">
				<span><?php _e( 'Shortcode options', 'watfa' ); ?></span><br/>
				<span><?php _e( 'Use [watfa_twitter_feeds] as shortcodes', 'watfa' ); ?></span>
			</h3>

			<table class="form-table">
				<tbody>
						<tr>
							<th>
								<label for="atf-title-flag">
									<?php _e( 'Show/Hide Title:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="checkbox" name="watfa_twitter_feeds_options[title_enable]" id="atf-title-flag" value="1" <?php checked( $watfa_twitter_feeds_options['title_enable'], true ); ?> />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-tweets-title">
									<?php _e( 'Title:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[tweets_title]" id="atf-tweets-title" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['tweets_title'] ) ) ? esc_attr( $watfa_twitter_feeds_options['tweets_title'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-tweets-count">
									<?php _e( 'Number of Tweets:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="text" name="watfa_twitter_feeds_options[tweets_count]" id="atf-tweets-count" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['tweets_count'] ) ) ? esc_attr( $watfa_twitter_feeds_options['tweets_count'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-background-color">
									<?php _e( 'Background Color:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text color-field" type="text" name="watfa_twitter_feeds_options[background_color]" id="atf-background-color" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['background_color'] ) ) ? esc_attr( $watfa_twitter_feeds_options['background_color'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-text-color">
									<?php _e( 'Text Color:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text color-field" type="text" name="watfa_twitter_feeds_options[text_color]" id="atf-text-color" value="<?php echo ( ! empty( $watfa_twitter_feeds_options['text_color'] ) ) ? esc_attr( $watfa_twitter_feeds_options['text_color'] ) : ''; ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<label for="atf-url-flag">
									<?php _e( 'Show/Hide Url:', 'watfa' ); ?>
								</label>
							</th>
							<td>
								<input class="regular-text" type="checkbox" name="watfa_twitter_feeds_options[url_enable]" id="atf-url-flag" value="1" <?php checked( $watfa_twitter_feeds_options['url_enable'], true ); ?> />
							</td>
						</tr>
					</tbody>
				</table>
			<?php settings_fields( 'watfa_twitter_feeds_options' ); ?>
			<p class="submit">
				<?php submit_button( __( 'Save Changes', 'watfa' ), 'primary', 'submit_store', false ); ?>
			</p>
		</form>
	</div>
</div>
