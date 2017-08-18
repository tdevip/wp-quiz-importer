<?php

/**
 * A helper class of the plugin.
 *
 * This class can be used to used to import learnpress quizzes.
 * An object of the class can be created using get_instance().
 * The entry point to the class is import($filename).
 *
 * @link       http://www.tdevip.com/
 * @since      1.0.0
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/classes
 * @author     Prasad Tumula <prasad@tdevip.com>
 */

if ( !class_exists( 'wpqi_learnpress_helper' ) ) {

	class wpqi_learnpress_helper {

		/** Refers to a single instance of this class. */
		private static $_instance = null;

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

	  	}

	  	/*--------------------------------------------*
	    * Callbacks
	    *---------------------------------------------*/


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
	  			'MC' => 'single_choice',
	  			'MS' => 'multi_choice',
	  		);

	  		return array_key_exists($type, $types) ? $types[$type] : $types['MC'];
	  	}

	  	/**
         * Retrieve the author_id given anthor name.
         * @param $author 	name or id of author
         * @return 			reurns author id
         */
	  	private function get_author_id($author) {

	        if (is_numeric($author)) {
	            return $author;
	        }

	        //check database for author
	        $author_data = get_user_by('login', $author);

	        //return author ID if exists, otherwise 0
	        return isset($author_data) ? $author_data->ID : 0;
	    }

	    /**
         * Sanitize the answers of a question.
         * @param $id 		id of question (post)
         * @param $question XML question
         * @return$answers 	reurns an array of sanitized answers
         */
	  	private function sanitize_answers(DOMNode $question, $id) {

	  		$answers = array(); $index = 0;
	  		$data = $question->getElementsByTagName('q_ans');
	  		foreach($data as $child) {

	  			$value = $child->nodeValue;
				if( !isset($value) ) continue;

	  			//check for correct answers
		  		$isCorrect = 'no';
		  		if($child->hasAttribute('q_ans_correct')) {
		  			$isCorrect = ("yes" === $child->getAttribute("q_ans_correct")) ? "yes" : "no";
		  		}

				$answers[] = array(
					'answer_data'  => array(
						'text'     => $value,
						'value'    => str_replace( '.', '', microtime( true ) . uniqid() ),
						'is_true'  => $isCorrect
					),
					'answer_order' => $index++,
					'question_id'  => $id
				);
			}

			return $answers;
		}

		/**
         * Sanitize a question.
         * @param $question XML question
         * @param $index    question number
         * @return$answers 	reurns an array of sanitized answers
         */
	  	private function sanitize_question(DOMNode $question, $index) {

	  		//convert DOM node data into an array
	  		$data = array();
			foreach($question->childNodes as $child) {
				if('answers' !== $child->nodeName)
			    	$data[$child->nodeName] = $child->nodeValue;
			}

			//sanitised post meta fields
			$meta_fields = array(
	  			'_lp_type' 		  => isset($data['q_type']) 	? $this->get_question_type($data['q_type']) : $this->get_question_type('MC'),
	  			'_lp_mark' 		  => isset($data['q_mark']) 	? intval($data['q_mark']) : 1,
	  			'_lp_explanation' => 'Enter solution here',
	  			'_lp_hint'		  => 'Enter hint here'
	  		);

	  		//sanitized post fields
	  		$post_fields = array(
	  			'ID' 			=> null,
	  			'post_title' 	=> isset($data['q_name']) 	? convert_chars($data['q_name']) : 'Q' . $index,
	  			'post_content'	=> isset($data['q_content'])? wpautop(convert_chars($data['q_content'])) : 'Enter content here',
	  			'post_author'	=> null,
	  			'post_name' 	=> isset($data['q_name']) 	? sanitize_title($data['q_name']) : null,
	  			'post_status'	=> 'publish',
	  			'post_type'		=> 'lp_question',
	  			'post_parent'	=> 0,
	  			'meta_input'	=> $meta_fields
	  		);

	  		// Collect together just the non-null post fields, those that have a value set,
	        // even if an empty string.
	        $set_post_fields = array();
	        foreach($post_fields as $name => $value) {
	            if ($value !== null) {
	                $set_post_fields[$name] = $value;
	            }
	        }

			return $set_post_fields;
		}

		/**
         * Import quiz/questions into learnpress.
         * @param  $filename    name of the file uploaded
         * @return $result 		returns status of import.
         */
	  	public function import( $filename ) {

	  		$message = array('type' => 'error', 'message' => '');

	  		//load data into $xml object
	  		$dom = new DOMDocument();
	  		if( !$dom->load($filename) ) {
	  			$message['message'] = __('Errors while creating XML file to import', 'wp-quiz-importer');
	  			return $message;
	  		}

	  		$skipped = 0;
		    $imported = 0;

		  	//import each question in $xml and store error ids
		  	$i = 0;
		  	$questions = $dom->getElementsByTagName('question');

		  	foreach ($questions as $question) {

		  		//sanitize question
		  		$sanitized_question = $this->sanitize_question($question, $i++);

				//insert/update database
		  		if ($post_id = $this->insert_question($sanitized_question)) {
		            $imported++;
		            $sanitized_answers = $this->sanitize_answers($question, $post_id);
		            $this->insert_answers($post_id, $sanitized_answers);
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

		  	return $message;
	  	}

	  	/**
         * Insert quiz question into learnpress.
         * @param  $data    sanitized question (post) data
         * @return post id if insert is successful otherwise false.
         */
		private function insert_question($data) {

			// data contains information?
			if ( empty($data) ) return false;

			// is this a valid post type?
        	if( !post_type_exists($data['post_type']) ) return false;

		     return wp_insert_post($data);
		}

		/**
         * Insert answers of a question into learnpress.
         * @param  $id 	ID of a question(post)
         * @param  $answers sanitized answers of a question(post)
         * @return none.
         */
		private function insert_answers($id, $answers) {
			global $wpdb;

			//delete all answers with this $id before iserting new answers
			$query = $wpdb->prepare( "
				DELETE FROM {$wpdb->learnpress_question_answers}
				WHERE question_id = %d
				", $id );
			$wpdb->query( $query );
			//learn_press_reset_auto_increment( 'learnpress_question_answers' );

			//insert answers into database
			foreach ( $answers as $answer ) {
				$answer['answer_data'] = maybe_serialize( $answer['answer_data'] );
				$wpdb->insert(
					$wpdb->learnpress_question_answers,
					$answer,
					array( '%s', '%d', '%d' )
				);
			}
		}
	}
}
