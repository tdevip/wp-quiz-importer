<?php

/**
 * A helper class of the plugin.
 *
 * This class can be used to used to import Quiz And Survey Master (Formerly Quiz Master Next) quizzes.
 * An object of the class can be created using get_instance().
 * The entry point to the class is import($filename).
 *
 * @link       http://www.tdevip.com/
 * @since      1.0.3
 *
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/classes
 * @author     Prasad Tumula <prasad@tdevip.com>
 */

if ( !class_exists( 'wpqi_quizmasternext_helper' ) ) {

	class wpqi_quizmasternext_helper {

		/** Refers to a single instance of this class. */
		private static $_instance = null;

		/** Refers to QSM quiz creator object **/
		private $_quizCreator = null;

		/** Refers to QSM quiz creator object **/
		private $_quizId = null;

		/*--------------------------------------------*
	     * Constructor
	     *--------------------------------------------*/
		/**
         * A static class for creating class object if it is not created already.
         * @return $_instance
         */
	  	public static function get_instance() {
	    	if ( ! isset( self::$_instance ) ) {
	      		self::$_instance = new self;
	    	}
	    	return isset( self::$_instance ) ? self::$_instance : false;
	  	}

	  	/**
	     * Initializes the class.
	     */
	  	protected function __construct() {
	  		if(class_exists(QMNQuizCreator)) {
	  			$this->_quizCreator = new QMNQuizCreator();
	  			add_action('qmn_quiz_created', array($this, 'get_quiz_id'), 10, 1);
	  		}
	  	}

	  	/*--------------------------------------------*
	    * Callbacks
	    *---------------------------------------------*/
	  	public function get_quiz_id($quiz_id) {
	  		$this->_quizId = isset($quiz_id) ? $quiz_id : null;
	  	}

	  	/*--------------------------------------------*
	     * Functions
	     *--------------------------------------------*/

	  	/**
         * Retrieve the question type given its code.
         * @param $type 	Coded two letter string
         * @return $result 	question type used in learnpress ('single_choice' is default value)
         */
	  	private function get_question_type($type) {

	  		$types = array(
	  			'MC' => '0',
	  			'MS' => '4',
	  		);

	  		return array_key_exists($type, $types) ? $types[$type] : $types['MC'];
	  	}

	    /**
         * Sanitize the answers of a question.
         * @param $question XML question
         * @return$answers 	reurns an array of sanitized answers
         */
	  	private function sanitize_answers(DOMNode $question) {

	  		//points allocated to corrrect answer
		  	$points = 1;
		  	if(!is_null($child = $question->getElementsByTagName('q_mark'))) {
		  		$points = (isset($child[0]->nodeValue) && intval($child[0]->nodeValue) != 0) ? intval($child[0]->nodeValue) : 1;
		  	}

	  		$answers = array();
	  		$data = $question->getElementsByTagName('q_ans');
	  		foreach($data as $child) {

	  			$value = $child->nodeValue;
				if( !isset($value) ) continue;

	  			//check for correct answers
		  		$isCorrect = 'no';
		  		if($child->hasAttribute('q_ans_correct')) {
		  			$isCorrect = ("yes" === $child->getAttribute("q_ans_correct")) ? 1 : 0;
		  		}

				$answers[] = array(
					htmlspecialchars( stripslashes( $value), ENT_QUOTES ),
					($isCorrect) ? $points : 0,
					$isCorrect
				);
			}

			return $answers;
		}

		/**
         * Sanitize a question.
         * @param $question 	XML question
         * @return$post_fields 	reurns an array of sanitized question fields
         */
	  	private function sanitize_question(DOMNode $question) {

	  		//convert DOM node data into an array
	  		$data = array();
			foreach($question->childNodes as $child) {
				if('answers' !== $child->nodeName)
			    	$data[$child->nodeName] = $child->nodeValue;
			}

	  		//sanitized post fields
	  		$post_fields = array(
	  			'quiz_id' 		=> isset($this->_quizId) ? $this->_quizId : 0,
	  			'question_name'	=> isset($data['q_content']) ? trim( preg_replace( '/\s+/',' ', htmlspecialchars( nl2br( wp_kses_post( stripslashes($data['q_content']) ) ), ENT_QUOTES ) ) ) : 'Enter content here',
	  			'answer_array'	=> serialize($this->sanitize_answers($question)),
	  			'question_answer_info' => 'Enter solution here',
	  			'comments'		=> 3,	//none
	  			'hints'			=> 'Enter hint here',
	  			'question_order'=> 1,   //order not specified
	  			'question_type_new' => isset($data['q_type']) 	? $this->get_question_type($data['q_type']) : $this->get_question_type('MC'),
	  			'question_settings' => serialize(array('required' => 1)),
	  			'category'		=> '',
	  			'deleted'		=> 0
	  		);

			return $post_fields;
		}

		/**
         * Import quiz/questions into quiz and survey master.
         * @param  $filename    name of the file uploaded
         * @return $message 	returns status of import.
         */
	  	public function import( $filename ) {

	  		$message = array('type' => 'error', 'message' => '');

	  		//load data into $xml object
	  		$dom = new DOMDocument();
	  		if( !$dom->load($filename) ) {
	  			$message['message'] = __('Errors while creating XML file to import', 'wp-quiz-importer');
	  			return $message;
	  		}

	  		//insert a new quiz
	  		if(!is_null($this->_quizCreator)) {
	  			$this->_quizCreator->create_quiz('New Quiz');
	  		}

	  		if(!is_null($this->_quizId)) {

	  			$skipped = 0;
			    $imported = 0;

			  	//import each question in $xml and store error ids
			  	$questions = $dom->getElementsByTagName('question');

			  	foreach ($questions as $question) {

			  		//sanitize question
			  		$sanitized_question = $this->sanitize_question($question);

					//insert/update database
			  		if ($this->insert_question($sanitized_question)) {
			            $imported++;
			        } else {
			            $skipped++;
			        }
			  	}

			  	if( 0 === $skipped ) {
			  		$message['type'] = 'success';
		  			$message['message'] = __('Successfully imported quiz questions', 'wp-quiz-importer');
		  		} else {
		  			$message['message'] = __('Unable to import all questions', 'wp-quiz-importer');
		  		}

	  		} else {
	  			$message['message'] = __('Unable to import questions as new quiz is not created.', 'wp-quiz-importer');
	  		}

		  	return $message;
	  	}

	  	/**
         * Insert quiz question into quiz and survey master.
         * @param  $data    sanitized question (post) data
         * @return post id if insert is successful otherwise false.
         */
		private function insert_question($data) {

			global $wpdb;

			// data contains information?
			if ( empty($data) ) return false;

			return $wpdb->insert(
		  		$wpdb->prefix."mlw_questions",
		  		$data,
		  		array('%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%d')
		  	);
		}
	}
}
