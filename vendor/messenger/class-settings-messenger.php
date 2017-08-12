<?php
/**
 * Represents the mechanism responsible for displaying the settings messages.
 *
 * @package TutsPlus_Custom_Messaging
 */

/**
 * Represents the mechanism responsible for displaying the settings messages. This
 * includes all success, warning, and error message types.
 *
 * @package TutsPlus_Custom_Messaging
 */
class Settings_Messenger {

	/**
	 * Refers to an instance of a settings message as defined in this class.
	 *
	 * @access private
	 * @var Settings_Message $message An instance of the settings message.
	 */
	private $message;

	/**
	 * Initializes this class by creating an instance of a Settings Message.
	 */
	public function __construct() {
		$this->message = new Settings_Message();
	}

	/**
	 * Initializes the class by associated the `get_all_messages` hook with the custom
	 * hook defined elsewhere in the codebase.
	 */
	public function init() {
		add_action( 'wpqi_settings_messages', array( $this, 'display_messages' ) );
	}

	/**
	 * Adds the specified message with a success attribute to the collection of messages.
	 *
	 * @param string $message The message to add to the collection of messages.
	 */
	public function add_success_message( $message ) {
		$this->add_message( 'success', $message );
	}

	/**
	 * Adds the specified message with a warning attribute to the collection of messages.
	 *
	 * @param string $message The message to add to the collection of messages.
	 */
	public function add_warning_message( $message ) {
		$this->add_message( 'warning', $message );
	}

	/**
	 * Adds the specified message with a error attribute to the collection of messages.
	 *
	 * @param string $message The message to add to the collection of messages.
	 */
	public function add_error_message( $message ) {
		$this->add_message( 'error', $message );
	}

	/**
	 * Retrieves all of the success messages and displays them on the front-end.
	 */
	public function get_success_messages() {
		echo esc_html( $this->get_messages( 'success' ) );
	}

	/**
	 * Retrieves all of the warning messages and displays them on the front-end.
	 */
	public function get_warning_messages() {
		echo esc_html( $this->get_messages( 'warning' ) );
	}

	/**
	 * Retrieves all of the error messages and displays them on the front-end.
	 */
	public function get_error_messages() {
		echo esc_html( $this->get_messages( 'error' ) );
	}

	/**
	 * A method for rendering all of the messages on front end.
	 */
	public function display_messages() {
		//read messages from database
		$this->message->read_notices();

		//display messages
		$this->message->get_all_messages();
	}

	/**
	 * A method to save all messages to database (as settings option)
	 */
	public function save_messages() {
		$this->message->save_notices();
	}

	/**
	 * Adds the message with the specified type to the collection of messages.
	 *
	 * @access private
	 *
	 * @param string $type    The type of message to add (either success, warning, or error).
	 * @param string $message The message to add to the collection of messages.
	 */
	private function add_message( $type, $message ) {
		$this->message->add_message( $type, $message );
	}

	/**
	 * Retrieves all of the messages with the specified type.
	 *
	 * @access private
	 *
	 * @param string $type    The type of message to retrieve (either success, warning, or error).
	 */
	private function get_messages( $type ) {
		return $this->message->get_messages( $type );
	}
}
