<?php
/**
 * Maintains the various types of messages for the plugin are maintained by this
 * base class.
 *
 * @package TutsPlus_Custom_Messaging
 */

/**
 * Maintains the various types of messages for the plugin are maintained by this
 * base class. All message types are to be subclassed and identified by a type.
 *
 * @package TutsPlus_Custom_Messaging
 */
class Settings_Message {

	/**
	 * The array of messages used to collect the types of messages managed by this
	 * class. This includes both success, error, and warning messages.
	 *
	 * @access private
	 * @var    array
	 */
	private $messages;

	/**
	 * Instantiates this class by setting up the array of messages to identify
	 * each type of collection of messages.
	 */
	public function __construct() {

		$this->messages = array(
			'success' 	=> array(),
			'error'		=> array(),
			'warning' 	=> array(),
		);
	}

	/**
	 * retrieve notices from database
	 */
	public function read_notices() {
		if(false !== get_option('wpqi_notices') ) {
			$this->messages = get_option('wpqi_notices');
			delete_option('wpqi_notices');
		}
	}

	/**
	 * save notices to database as a setting
	 */
	public function save_notices() {
		// Determine if the user has the ability to save the options.
		//if ( ! current_user_can( 'manage_options' ) ) {
		//	$this->messenger->add_error_message( 'You do not have permission for this operation.' );
		//} else {
			if( false === get_option( 'wpqi_notices' ) ) {
			    add_option( 'wpqi_notices',  $this->messages, '', 'no' );
			} else {
			    update_option( 'wpqi_notices', $this->messages );
			}
		//}
	}

	/**
	 * Add a single message with the specified type to the collection of messages to display.
	 *
	 * @param string $type    The type of message to display.
	 * @param string $message The message to display.
	 */
	public function add_message( $type, $message ) {

		$message = sanitize_text_field( $message );

		if ( in_array( $message, $this->messages[ $type ], true ) ) {
			return;
		}

		array_push( $this->messages[ $type ], $message );
	}

	/**
	 * Retrieves all of the messages that are stored in the message collections. Renders
	 * them to the display.
	 */
	public function get_all_messages() {

		foreach ( $this->messages as $type => $message ) {
			$this->get_messages( $type );
		}
	}

	/**
	 * Retrieves all of the messages of the specified type and renders it ot the display.
	 *
	 * @param string $type The type of message(s) to retrieve from the collection of messages.
	 */
	public function get_messages( $type ) {

		if ( empty( $this->messages[ $type ] ) ) {
			return;
		}

		$html  = "<div class='notice notice-$type is-dismissible'>";
		$html .= '<ul>';
		foreach ( $this->messages[ $type ] as $message ) {
			$html .= "<li>$message</li>";
		}
		$html .= '</ul>';
		$html .= '</div><!-- .notice-$type -->';

		$allowed_html = array(
			'div' => array(
				'class' => array(),
			),
			'ul' => array(),
			'li' => array(),
		);

		echo wp_kses( $html, $allowed_html );
	}
}
