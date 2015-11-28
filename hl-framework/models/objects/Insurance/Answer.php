<?php

/**
 * Holds a single underwriting (declaration) question and answer.
 */
class Model_Insurance_Answer extends Model_Abstract {

	const YES = 'yes';
	const NO = 'no';
    const INFO_ONLY = 'infoOnly';
	
	protected $_policyNumber;
	protected $_questionNumber;
	protected $_answer;
	protected $_expectedAnswer;
	protected $_dateAnswered;
	
	
	public function __construct() {
	
		$this->_policyNumber = null;
		$this->_questionNumber = null;
		$this->_answer = null;
		$this->_expectedAnswer = null;
		$this->_dateAnswered = new Zend_Date();
	}
	
	
	/**
	 * Returns the quote/policy number against which the answer has been stored.
	 * 
	 * @return mixed
	 * The full quote/policy number, or null if not set.
	 */
	public function getPolicyNumber() {
		
		return $this->_policyNumber;
	}
	
	
	/**
	 * Returns the underwriting question number.
	 * 
	 * @return mixed
	 * The underwriting question number as an integer, or null
	 * if not set.
	 */
	public function getQuestionNumber() {
		
		return $this->_questionNumber;
	}
	
	
	/**
	 * Returns the answer given to the underwriting question.
	 * 
	 * @return mixed
	 * The answer given to the underwriting question - corresponding to one
	 * of the consts exposed by this class - or null if not set.
	 */
	public function getAnswer() {
		
		return $this->_answer;
	}
	
	
	/**
	 * Returns the expected (correct) answer to the underwriting question.
	 * 
	 * @return mixed
	 * The expected (correct) answer to the underwriting question - corresponding to one
	 * of the consts exposed by this class - or null if not set.
	 */
	public function getExpectedAnswer() {
		
		return $this->_expectedAnswer;
	}
	
	
	/**
	 * Getter for the date answered.
	 *
	 * Returns the date on which this UW question was answered, encapsulated
	 * in a Zend_Date object.
	 *
	 * @return Zend_Date
	 * Always returns a Zend_Date representing the date this UW question
	 * was answered.
	 */
	public function getDateAnswered() {
		
		return $this->_dateAnswered;
	}
	
	
	/**
	 * Sets the quote/policy number.
	 *
	 * @param string $policyNumber
	 * The full, unique quote/policy number.
	 * 
	 * @return void
	 */
	public function setPolicyNumber($policyNumber) {
		
		$this->_policyNumber = $policyNumber;
	}
	
	
	/**
	 * Sets the question number.
	 * 
	 * @param integer $questionNumber
	 * The question number.
	 * 
	 * @return void
	 */
	public function setQuestionNumber($questionNumber) {
		
		$this->_questionNumber = $questionNumber;
	}
	
	
	/**
	 * Records a single underwriting answer.
	 *
	 * Method which records a single underwriting answer, the value of
	 * which should correspond to one of the const values exposed by this class.
	 * 
	 * @param string $answer
	 * The $answer should correspond to one of the consts exposed by this class.
	 *
	 * @throws Exception
	 * Throws an Exception if the $answer does not correspond to one of the
	 * const values exposed by this class.
	 */
	public function setAnswer($answer) {
		
		if(($answer != self::YES) && ($answer != self::NO)) {
			
			throw new Exception("Argument of incorrect type received.");
		}
		
		$this->_answer = $answer;
	}
	
	
	/**
	 * Sets the expected answer for the current underwriting question.
	 * 
	 * Each underwriting question has an expected answer, aka 'correct answer'.
	 * 
	 * @param string $answer
	 * The yes or no answer, which must correspond to one of the consts exposed
	 * by this class.
	 * 
	 * @return void
	 */
	public function setExpectedAnswer($answer) {
		
		if(($answer != self::YES) && ($answer != self::NO) && ($answer != self::INFO_ONLY)) {
			
			throw new Exception("Argument of incorrect type received.");
		}
		
		$this->_expectedAnswer = $answer;
	}
	
	
	/**
	 * Sets the date on which the UW question was answered.
	 *
	 * Method which sets the date on which the UW was answered.
	 * Accepts arguments of type Zend_Date only.
	 *
	 * @param Zend_Date $dateAnswered
	 * A Zend_Date representation of when the UW question was answered.
	 *
	 * @throws Exception
	 * Throws an Exception if the $dateAnswered is not a Zend_Date.
	 */
	public function setDateAnswered($dateAnswered) {
		
		if(!is_a($dateAnswered, 'Zend_Date')) {
			
			throw new Exception("Argument of incorrect type received.");
		}
		$this->_dateAnswered = $dateAnswered;
	}
	
	
	/**
	 * Determines if this object is the same as that passed in.
	 * 
	 * @param Model_Insurance_Answer $otherAnswer
	 * The answer object to compare against.
	 * 
	 * @return boolean
	 * True if the objects are the same, false otherwise.
	 */
	public function equals($otherAnswer) {
		
		$isCopy = false;		
		
		//Begin the comparison process.
        if($this->_policyNumber == $otherAnswer->getPolicyNumber()) {
            
            if($this->_questionNumber == $otherAnswer->getQuestionNumber()) {
                
                if($this->_answer == $otherAnswer->getAnswer()) {
                    
                    if($this->_dateAnswered->compareDate($otherAnswer->getDateAnswered()) == 0) {
                        
						$isCopy = true;
                    }
                }
            }
        }
        
        return $isCopy;
	}
}

?>