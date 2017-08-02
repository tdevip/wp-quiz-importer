<?php
/**
 * Represents a single Setitngs Message to display in the WordPress admin.
 *
 * @package TutsPlus_Custom_Messaging
 */

/**
 * Represents a single Setitngs Message to display in the WordPress admin.
 * This class subclasses the Settings Message class to make it easier to create its
 * own message (such as a success message, a warning message, or an error message).
 *
 * @extends Settings_Message
 * @package TutsPlus_Custom_Messaging
 */
class Message extends Settings_Message {

	/**
	 * The type of message represented by this class.
	 *
	 * @access private
	 * @var string
	 */
	private $type;

	/**
	 * Instianties this class by setting the specified type.
	 *
	 * @param string $type The type of message this class represents.
	 */
	public function __construct( $type ) {
		$this->type = $type;
	}

	/**
	 * Adds a message to the collection of messages maintained by this class.
	 *
	 * @param string $message The message to add to this collection of message types.
	 */
	public function add( $message ) {

		parent::add(
			$this->type,
			sanitize_text_field( $message )
		);
	}

	/**
	 * Retrieves all messages represetenced by this message type.
	 */
	public function get_messages() {
		parent::get_messages( $this->type );
	}
}
