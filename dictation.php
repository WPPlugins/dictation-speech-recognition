<?php
/*
Plugin Name: Dictation (Speech Recognition)
Plugin URI: http://dictation.gearinvent.com
Author URI: http://gearinvent.com
Description: Dictation (Speech Recognition) is the most popular and easiest way to Write a POST with your voice converting it to text with any language.
Version: 1.0.1
Author: GEAR invent!
*/

/*
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the LICENSE.txt file for details.
*/


class Dictation {
	const VERSION = '1.0.1';

	static
		$baseurl,
		$basename,
		$basedir,
		$debug,
		$js,
		$pages;

	var
		$queue;

	function __construct()  {

		self::$basename = plugin_basename(__FILE__);
		self::$baseurl = plugins_url('', __FILE__);
		self::$basedir = dirname(__FILE__);
		self::$js = self::$baseurl . '/js';

		add_action('init', array(&$this, 'init'));

		add_action('admin_notices', array(&$this, 'admin_notices'));

		// Scripts and stylesheets
		add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

	}

	static function get_version_string() {
		$version = __('Version', 'dictation') . ":" . self::VERSION;
		return $version;
	}

	static function get_support_links() {
		echo self::get_version_string();
		echo " | <a target='_blank' href='http://dictation.gearinvent.com/documentation'>" . __('Documentation', 'dictation') . "</a>";
		echo " | <a target='_blank' href='http://dictation.gearinvent.com/faq'>" . __('Support', 'dictation') . "</a>";
		echo " | <a target='_blank' href='http://gearinvent.com/contact.php'>" . __('Contact', 'dictation') . "</a>";

	}

	static function ajax_response($status, $data=null) {
		$output = trim(ob_get_clean());		// Ignore whitespace, any other output is an error
		header( "Content-Type: application/json" );
		$response = json_encode(array('status' => $status, 'data' => $data, 'output' => $output));
		die ($response);
	}


	/**
	* Scripts & styles for frontend
	* CSS is loaded from: child theme, theme, or plugin directory
	*/
	function wp_enqueue_scripts() {

		// Load the default CSS from the plugin directory
		wp_enqueue_style('dictation', self::$baseurl . '/css/dictation.css', null, self::VERSION);

	}

	// Scripts & styles for admin
	// CSS is always loaded from the plugin directory
	function admin_enqueue_scripts($hook) {

		// Some plugins call this without setting $hook
		if (empty($hook))
			return;

		// Settings page
		if ($hook == self::$pages[0]) {
			wp_enqueue_style('dictation', self::$baseurl . '/css/dictation.css', null, self::VERSION);
		}

		// Post / page edit
		if ($hook == 'edit.php' || $hook == 'post.php' || $hook == 'post-new.php')
			wp_enqueue_style('dictation', self::$baseurl . '/css/dictation.css', null, self::VERSION);
	
	}


	// Sanity checks via notices
	function admin_notices() {
		$error =  "<div id='error' class='error'><p>%s</p></div>";

		if (get_bloginfo('version') < "3.2") {
			echo sprintf($error, __("WARNING: Dictation now requires WordPress 3.2 or higher. Please upgrade before using it.", 'dictation'));
			return;
		}
	}

	function init() {
		// Load text domain
		load_plugin_textdomain('dictation', false, dirname(self::$basename) . '/languages');

		add_action( 'add_meta_boxes', array( $this, 'add_some_box' )  );
	}

	public function add_some_box( $post_type ) {
		$post_types = array('post', 'page');     //limit meta box to certain post types
            	if ( in_array( $post_type, $post_types )) {
			add_meta_box('dictation', 'Dictation', array( $this, 'meta_box' ), $post_type, 'normal', 'high');
		}
	}
	
	public function meta_box( $post ) {
		$this->enqueue_editor();
		require(self::$basedir . '/forms/dictation.php');
	}

	static function string_to_boolean($data) {
		if ($data === 'false')
			return false;

		if ($data === 'true')
			return true;

		if (is_array($data)) {
			foreach($data as &$datum)
				$datum = self::string_to_boolean($datum);
		}

		return $data;
	}

	static function boolean_to_string($data) {
		if ($data === false)
			return "false";
		if ($data === true)
			return "true";

		if (is_array($data)) {
			foreach($data as &$datum)
				$datum = self::boolean_to_string($datum);
		}

		return $data;
	}

	/**
	* Output javascript
	*
	* @param mixed $script
	*/
	static function script($script) {
		return "\r\n<script type='text/javascript'>\r\n/* <![CDATA[ */\r\n$script\r\n/* ]]> */\r\n</script>\r\n";
	}


	function enqueue_editor() {
		$this->load('editor');
		$this->queue['editor'] = true;
	}


	function load($type = '') {
		static $loaded;

		if ($loaded)
			return;
		else
			$loaded = true;

		$version = Dictation::VERSION;

		if ($type == 'editor')
			wp_enqueue_script('dictation', self::$js . "/dictation.min.js", array('jquery'), $version, true);

	}

	/**
	* Get language using settings/WPML/qTrans
	*
	*/
	static function get_language() {
		// For WPML (wpml.org): set the selected language if it wasn't specified in the options screen
		if (defined('ICL_LANGUAGE_CODE'))
			return ICL_LANGUAGE_CODE;

		// For qTranslate, pick up current language from that
		if (function_exists('qtrans_getLanguage'))
			return qtrans_getLanguage();

		return self::$options->language;
	}
}  // Dictation class

$dictation = new Dictation();
?>