<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://www.tdevip.com/
 * @since      1.0.0
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/admin
 * @author     Prasad Tumula <prasad@tdevip.com>
 */

class Wp_Quiz_Importer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The admin notices display system.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $messenger    messaging system.
	 */
	private $messenger;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->messenger = new Settings_Messenger();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Quiz_Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Quiz_Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-quiz-importer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Quiz_Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Quiz_Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-quiz-importer-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add an admin submenu page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Quiz Importer Settings', 'wp-quiz-importer' ),
			__( 'Quiz Importer', 'wp-quiz-importer' ),
			'administrator',
			$this->plugin_name,
			array( $this, 'display_import_page' )
		);

	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_import_page() {

		$display = apply_filters('wpqi_admin_display', 'partials/wp-quiz-importer-admin-display.php');

		// User has permissions to upload the quiz
		if ( current_user_can( 'administrator' ) ) {
			include_once $display;
		} else {
			include_once 'partials/wp-quiz-importer-noadmin-display.php';
		}
	}

	/**
	 * perform checks and import quiz questions
	 *
	 * @since 	1.0.0
	 */
	public function import_quiz_questions() {
		//perform import only if form data is submitted
		if ( isset( $_POST['_wpnonce-wp-quiz-importer-page_import'] ) ) {

			$helper = $helpername = $helperpath = $helperclass = false;

			if( isset( $_POST['wp_quiz_provider'] ) ) {
				//create imprter helper path and class name
				$helpername  = $_POST['wp_quiz_provider'];
				$helperpath  = WPQI_PLUGIN_DIR . '/admin/helpers/class-' . $helpername . '-helper.php';
				$helperclass = 'wpqi_' . str_replace('-', '_', $helpername) . '_helper';

				//apply filters in case file location is different
				//Note: the path must be an absolute path
				$helperpath = apply_filters('wpqi_importer_path', $helperpath, $helpername);
			}

			// User has permissions to upload the quiz
			if ( !current_user_can( 'administrator' ) ) {
				$this->messenger->add_error_message( __('You do not have sufficient permissions.
				You must be an adminstrator to import quiz questions.' , 'wp-quiz-importer') );
			}

			//make sure wpnonce is properly set
			check_admin_referer( 'wp-quiz-importer-page_import', '_wpnonce-wp-quiz-importer-page_import' );

			//make sure an xml file is uploaded and retrieve file name
			if( !$filename = $this->check_file_is_uploaded() ) {
				$this->messenger->add_error_message( __('Error during file upload. Make sure you upload proper XML file.', 'wp-quiz-importer') );
			}

			//make sure uploaded file has correct xml schema
			elseif ( !$this->valid_xml_schema($filename) ){
				$this->messenger->add_error_message( __('Cannot extract data from uploaded file (not a valid file).', 'wp-quiz-importer') );
			}

			// check to see helper class exits
			elseif ( !file_exists( $helperpath ) ) {
				$this->messenger->add_error_message( __('Quiz helper file '.$helperpath.' is not available.', 'wp-quiz-importer') );
			}

			// include helper-class file
			elseif ( !include_once( $helperpath ) ) {
				$this->messenger->add_error_message( __('Unable to include file '.$helperpath, 'wp-quiz-importer') );
			}

			// check helper class exists
			elseif ( !class_exists( $helperclass ) ) {
				$this->messenger->add_error_message( __('Quiz helper class (' . $helperclass . ') does not exist.', 'wp-quiz-importer') );
			}

			// check helper class is created
			elseif ( !$helper = $helperclass::get_instance() ) {
				$this->messenger->add_error_message( __('Quiz helper object (' . $helperclass . ') is NULL.', 'wp-quiz-importer') );
			}

			// import questions
			else {
				$message = $helper->import($filename);
				if( !empty($message['message'] ) ) {
					if ( 'success' === $message['type'] ) {
						$this->messenger->add_success_message($message['message']);
					} else {
						$this->messenger->add_error_message($message['message']);
					}
				}
			}

			//remove file
			if (file_exists($filename)) {
            	@unlink($filename);
        	}

			//redirect page
			$this->redirect_safely();
		}
	}

	/**
	 * send admin notices
	 *
	 * @since 	1.0.0
	 */
	public function send_admin_notices() {

		$this->messenger->display_messages();
	}

	/**
	 * function used to safely redirect the page
	 *
	 * @since 	1.0.0
	 */
	private function redirect_safely() {
		// To make the Coding Standards happy, we have to initialize this.
		if ( ! isset( $_POST['_wp_http_referer'] ) ) { // Input var okay.
			$_POST['_wp_http_referer'] = wp_login_url();
		}

		// Sanitize the value of the $_POST collection for the Coding Standards.
		$url = sanitize_text_field(	wp_unslash( $_POST['_wp_http_referer'] ) );

		$this->messenger->save_messages();

		wp_safe_redirect( urldecode( $url ) );
		exit();
	}

	/**
	 * check if file is uploaded
	 *
	 * @since 	1.0.0
	 */
	private function check_file_is_uploaded() {
		$filename = false;
		if ( is_uploaded_file( $_FILES['wp_quiz_file']['tmp_name'] ) ) {
			$filename = $_FILES['wp_quiz_file']['tmp_name'];
		}

		return $filename;
	}

	/**
	 * check whether uploaded xml file has correct xml schema
	 *
	 * @since 	1.0.0
	 */
	private function valid_xml_schema($filename) {
		libxml_use_internal_errors(true);
		$xml = new DOMDocument();
		$xml->load($filename);

		return apply_filters('wpqi_validate_schema', $xml->schemaValidate(WPQI_PLUGIN_DIR . '/assets/xml_schema.xsd') );
	}
}
