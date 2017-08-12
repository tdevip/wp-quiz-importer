<?php

/**
 * The helper classes of the plugin.
 *
 * @link       http://www.tdevip.com/
 * @since      1.0.0
 *
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/classes
 */

/**
 * The helper classes of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/classes
 * @author     Prasad Tumula <prasad@tdevip.com>
 */

if ( !class_exists( 'wpqi_wpproquiz_helper' ) ) {

	class wpqi_wpproquiz_helper {

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
	    } // end get_instance;

	    /**
	     * Initializes the class.
	     */
	    protected function __construct() {

	    } // end constructor

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
	  			'SC' => 'single',
	  			'MC' => 'multiple',
	  		);

	  		return array_key_exists($type, $types) ? $types[$type] : $types['SC'];
	  	}

	  	/**
         * Sanitize the answers of a question.
         * @param $question XML question
         * @return$answers 	reurns an array of sanitized answers
         */
	  	private function sanitize_answers(DOMNode $question) {

	  		$answers = array();
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
					'q_ans'  	=> $value,
					'q_ans_correct' => $isCorrect,
					'q_ans_sort' 	=> '',
				);
			}

			return $answers;
		}

	  	/**
         * Sanitize a question.
         * @param $question XML question
         * @param $index 	question number
         * @return$answers 	reurns an array of sanitized answers
         */
	  	private function sanitize_question(DOMNode $question, $index) {

	  		//convert DOM node data into an array
	  		$data = array();
			foreach($question->childNodes as $child) {
				if('answers' !== $child->nodeName)
			    	$data[$child->nodeName] = $child->nodeValue;
			}

			//sanitized question fields
			$question_fields = array(
				'q_name' 	=> isset($data['q_name']) 	  ? convert_chars($data['q_name']) : 'Q' . $index,
				'q_content' => isset($data['q_content'])  ? wpautop(convert_chars($data['q_content'])) : 'Enter content here',
				'q_type' 	=> isset($data['q_type']) 	  ? $this->get_question_type($data['q_type']) : $this->get_question_type('SC'),
				'q_mark' 	=> intval($data['q_mark']) 	  ? intval($data['q_mark']) : 1,
			);

			return $question_fields;
		}

		/**
         * Import quiz/questions into WpProQuiz.
         * @param  $filename 	name of the file uploaded
         * @return $result 		returns status of import
         */
	  	public function import( $filename ) {

			$importer = null;
	  		$message = array('type' => 'error', 'message' => '');

	  		//load data into $xml object
	  		if( !$xml = $this->create_XML_from_array($filename) ) {
	  			$message['message'] = __('Errors while creating XML file to import', 'wp-quiz-importer');
	  		} elseif ( !class_exists( 'WpProQuiz_Controller_Admin') ) {
				$message['message'] = __('Wp-Pro-Quiz is not installed or activated', 'wp-quiz-importer');
			} elseif( is_null( $importer = new WpProQuiz_Helper_ImportXml() ) ) {
				$message['message'] = __('Errors while creating WpProQuiz importer object', 'wp-quiz-importer');
			} else {
				$importer->setImportString(base64_encode(gzcompress($xml)));
				if( $importer->saveImport(false) ) {
		  			$message['type'] = 'success';
		  			$message['message'] = __('Successfully imported quiz questions', 'wp-quiz-importer');
		  		} else {
		  			$message['message'] = __('Unable to import all questions', 'wp-quiz-importer');
		  		}
			}

	  		return $message;
	  	}

	  	/**
         * create XML file in the WpProQuiz import format.
         * @param  $filename 	name of the file uploaded
         * @return $result 		returns 'false' on errors otherwise XML string.
         */
	  	private function create_XML_from_array($filename) {

	  		$dom = new DOMDocument();
	  		if( !$dom->load($filename) ) return false;

			$Qdata = array(); $i = 0;
	  		$questions = $dom->getElementsByTagName('question');

			foreach ($questions as $question) {
				$sanitized_question = $this->sanitize_question($question, $i++);
				$sanitized_answers = $this->sanitize_answers($question);

				$Adata = array();
				foreach ($sanitized_answers as $sanitized_answer) {
					$Adata[] = array( '@attributes' => array( 'points' => 1,
						                                      'correct' => ('yes' === $sanitized_answer['q_ans_correct']) ? true : false,
						                                    ),
									  'answerText'  => array( '@attributes' => array( 'html' => 'true' ), '@value' => $sanitized_answer['q_ans'] ),
									  'stortText'   => array( '@attributes' => array( 'html' => 'true' ), '@value' => $sanitized_answer['q_ans_sort'] )
									);
				}

				$Qdata[] = array( '@attributes'		=> array( 'answerType' => $sanitized_question['q_type']),
								  'title'			=> array( '@value' => $sanitized_question['q_name'] ),
								  'points'			=> array( '@value' => $sanitized_question['q_mark'] ),
								  'questionText'	=> array( '@value' => $sanitized_question['q_content'] ),
								  'correctMsg'		=> array( '@value' => 'Enter correct message here' ),
								  'incorrectMsg'	=> array( '@value' => 'Enter incorrect message here' ),
								  'tipMsg'			=> array( '@attributes' => array( 'enabled' => 'false' ) ),
								  'category'		=> array(),
								  'correctSameText'	=> array( '@value' => 'true' ),
								  'showPointsInBox'	=> array( '@value' => 'true' ),
								  'answerPointsActivated' => array( '@value' => 'false' ),
								  'answerPointsDiffModusActivated' => array( '@value' => 'false' ),
								  'disableCorrect'	=> array( '@value' => 'false' ),
								  'answers'			=> array( 'answer' => $Adata )
								);
			}

	  		$data = array(
	  			'header' =>  array( '@attributes' => array( 'version' => 0.37, 'exportVersion' => 1 ) ),
	  			'data' => array( 'quiz' => array (
	  				'title' 				=> array( '@attributes' => array( 'titleHidden' => 'false'), '@value' => 'New Quiz' ),
	  				'text'					=> array( '@value' => 'Please enter your quiz description here.' ),
	  				'category'				=> array(),
	  				'resultText'			=> array( '@attributes' => array( 'gradeEnabled' => 'true'), '@value' => 'false' ),
	  				'btnRestartQuizHidden'	=> array( '@value' => 'false' ),
	  				'btnViewQuestionHidden'	=> array( '@value' => 'false' ),
	  				'questionRandom'		=> array( '@value' => 'false' ),
	  				'answerRandom'			=> array( '@value' => 'false' ),
	  				'timeLimit'				=> array( '@value' => 0 ),
	  				'showPoints'			=> array( '@value' => 'false' ),
	  				'statistic' 			=> array( '@attributes' => array( 'activated' => 'true', 'ipLock' => 140 ) ),
	  				'quizRunOnce' 			=> array( '@attributes' => array( 'type' => 1, 'cookie' => 'false', 'time' => 10 ), '@value' => 'false' ),
	  				'numberedAnswer'		=> array( '@value' => 'false' ),
	  				'hideAnswerMessageBox'	=> array( '@value' => 'false' ),
	  				'disabledAnswerMark'	=> array( '@value' => 'false' ),
	  				'showMaxQuestion' 		=> array( '@attributes' => array( 'showMaxQuestionValue' => 1, 'showMaxQuestionPercent' => 'false' ),
	  												  '@value' => 'false' ),
	  				'toplist'				=> array( '@attributes' 				=> array( 'activated' => 'false'),
	  												  'toplistDataAddPermissions' 	=> array( '@value' => 1 ),
	  												  'toplistDataSort' 			=> array( '@value' => 1 ),
	  												  'toplistDataAddMultiple' 		=> array( '@value' => 'false' ),
	  												  'toplistDataAddBlock' 		=> array( '@value' => 1 ),
	  												  'toplistDataShowLimit' 		=> array( '@value' => 1 ),
	  												  'toplistDataShowIn' 			=> array( '@value' => 1 ),
	  												  'toplistDataCaptcha' 			=> array( '@value' => 'false' ),
	  												  'toplistDataAddAutomatic' 	=> array( '@value' => 'false' )
	  												),
	  				'showAverageResult'		=> array( '@value' => 'false' ),
	  				'prerequisite'			=> array( '@value' => 'false' ),
	  				'showReviewQuestion'	=> array( '@value' => 'false' ),
	  				'quizSummaryHide'		=> array( '@value' => 'false' ),
	  				'skipQuestionDisabled'	=> array( '@value' => 'false' ),
	  				'emailNotification'		=> array( '@value' => -2 ),
	  				'userEmailNotification'	=> array(),
	  				'showCategoryScore'		=> array( '@value' => 'false' ),
	  				'hideResultCorrectQuestion'	=> array( '@value' => 'false' ),
	  				'hideResultQuizTime'	=> array( '@value' => 'false' ),
	  				'hideResultPoints'		=> array( '@value' => 'false' ),
	  				'autostart'				=> array( '@value' => 'false' ),
	  				'forcingQuestionSolve'	=> array( '@value' => 'false' ),
	  				'hideQuestionPositionOverview' => array( '@value' => 'false' ),
	  				'hideQuestionNumbering'	=> array( '@value' => 'false' ),
	  				'sortCategories'		=> array( '@value' => 'false' ),
	  				'showCategory'			=> array( '@value' => 'false' ),
	  				'quizModus'				=> array( '@attributes' => array( 'questionsPerPage' => 0 ),
	  												  '@value' => 0
	  												),
	  				'startOnlyRegisteredUser'=>array( '@value' => 'false' ),
	  				'adminEmail'			=> array( 'to' 	   => array( '@value' => 'Deactivate'),
	  												  'from'   => array(),
	  												  'subject'=> array(),
	  												  'html'   => array(),
	  												  'message'=> array( '@value' => 'false' )
	  												),
	  				'userEmail'				=> array( 'to' 	   => array( '@value' => 'false' ),
	  												  'toUser' => array( '@value' => 'false' ),
	  												  'toForm' => array(),
	  												  'form'   => array(),
	  												  'subject'=> array(),
	  												  'html'   => array(),
	  												  'message'=> array( '@value' => 'false' )
	  												),
	  				'forms'					=> array( '@attributes' => array( 'activated' => 'false', 'position' => -1 ) ),
	  				'questions'				=> array( 'question' => $Qdata )
	  				)
	  			)
	  		);

			require_once(WPQI_PLUGIN_DIR . 'vendor/class-array-2-xml.php');
			$dom = Array2XML::createXML('wpProQuiz', $data);
			$xml = $dom->saveXML();

			return $xml;
	  	}
	}
}
