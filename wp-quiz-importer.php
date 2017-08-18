<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.tdevip.com/
 * @since             1.0.0
 * @package           Wp_Quiz_Importer
 *
 * @wordpress-plugin
 * Plugin Name:       Wp Quiz Importer
 * Plugin URI:        http://www.tdevip.com/wp-quiz-importer
 * Description:       Imports quiz questions from a MS Word template file.
 * Version:           1.1.0
 * Author:            Prasad Tumula
 * Author URI:        http://www.tdevip.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-quiz-importer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Define plugin path
define( 'WPQI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-quiz-importer-activator.php
 */
function activate_wp_quiz_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-quiz-importer-activator.php';
	Wp_Quiz_Importer_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_wp_quiz_importer' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-quiz-importer-deactivator.php
 */
function deactivate_wp_quiz_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-quiz-importer-deactivator.php';
	Wp_Quiz_Importer_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_wp_quiz_importer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-quiz-importer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_quiz_importer() {

	$plugin = new Wp_Quiz_Importer();
	$plugin->run();

}
run_wp_quiz_importer();
