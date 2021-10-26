<?php
/**
 * Plugin Name: WP AKA Twitter Feeds API
 * Description: This plugin displays twitter feeds through the widget and shortcode using the latest twitter api version.
 * Author: Alok Shrestha
 * Version: 1.0.0
 * Author Email: alokstha1@gmail.com
 * Author URI: http://alokshrestha.com.np
 * Text Domain: watfa
 **/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define ATF_PLUGIN_FILE.
if ( ! defined( 'ATF_PLUGIN_FILE' ) ) {
	define( 'ATF_PLUGIN_FILE', __FILE__ );
}

// Define ATF_PLUGIN_URL.
if ( ! defined( 'ATF_PLUGIN_URL' ) ) {
	define( 'ATF_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
}

// Include the main Aka_Twitter_Feeds class page.
if ( ! class_exists( 'Aka_Twitter_Feeds' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-aka-twitter-feeds.php';
}

// Include the main Aka_Twitter_Feeds_Widget widget class page.
if ( ! class_exists( 'Aka_Twitter_Feeds_Widget' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-aka-twitter-feeds-widget.php';
}
