<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.tdevip.com/
 * @since      1.0.0
 *
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/includes
 * @author     Prasad Tumula <prasad@tdevip.com>
 */
class Wp_Quiz_Importer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-quiz-importer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
