<?php

/**
 * Business rules class which provides underwriting answser services.
 */
class Manager_Insurance_Answers {

    protected $_underwritingAnswersModel;
	

    /**
	 * Convenience method to write an array of underwriting answers to the dbase.
	 *
	 * This method writes to the data storage the answers given by the customer to
	 * the underwriting (declaration) questions. It expects an array of
	 * Model_Insurance_Answer objects, each encapsulating a single
	 * answer.
	 *
	 * @param array $answers
	 * An array of Model_Insurance_Answer objects encapsulating the
	 * answers given by the customer in response to the UW questions.
	 */
    public function insertUnderwritingAnswers($answers) {
        
        foreach($answers as $currentAnswer) {
            
            $this->insertUnderwritingAnswer($currentAnswer);
        }
    }
	
	
	/**
	 * Convenience method to write a single UW answer to the data storage.
	 *
	 *
	 * This method writes to the data storage a single answers given by the customer
	 * in response to the underwriting (declaration) questions. It expects the
	 * answer to be encapsulated in a Model_Insurance_Answer object.
	 *
	 * @param Model_Insurance_Answer $answer
	 * An Model_Insurance_Answer object encapsulating the answer given
	 * by the customer in response to the corresponding UW question.
	 */
    public function insertUnderwritingAnswer($answer) {
        
        if(empty($this->_underwritingAnswersModel)) {
            
            $this->_underwritingAnswersModel = new Datasource_Insurance_Answers();
        }
        
        $this->_underwritingAnswersModel->insertUnderwritingAnswer($answer);
    }
	
	
	/**
	 * Retrieves underwriting answers for the policy number passed in.
	 *
	 * This method retrieves all underwriting answers associated with the quote or
	 * policynumber passed in. The answers will each be encapsulated in an
	 * Model_Insurance_Answer object, and returned in an array.
	 *
	 * @param string $policyNumber
	 * The full quote or policy number identifying the answers in the data storage.
	 *
	 * @return array
	 * An array of Model_Insurance_Answer objects, or null if no
	 * answers are found.
	 */
    public function getUnderwritingAnswers($policyNumber) {
        
        if(empty($this->_underwritingAnswersModel)) {
            
            $this->_underwritingAnswersModel = new Datasource_Insurance_Answers();
        }
        
        return $this->_underwritingAnswersModel->getUnderwritingAnswers($policyNumber);
    }
	
	
	/**
     * Determines if the underwriting answer is already stored in the data storage.
     *
     * This method attempts to determine if the UW answer passed in is already
     * represented within the data storage. Useful for preventing duplicate insertions
     * into the data storage.
     *
     * @param Model_Insurance_Answer $answer
     * The fully populated underwriting answer to search for in the database.
     *
     * @return boolean
     * Returns true if the underwriting answer has already been stored in the database,
     * false otherwise.
     */
    public function getIsAnswerAlreadyStored($answer) {
        
        if(empty($this->_underwritingAnswersModel)) {
            
            $this->_underwritingAnswersModel = new Datasource_Insurance_Answers();
        }
        
        return $this->_underwritingAnswersModel->getExists($answer);
    }
	
	
	/**
	 * Removes underwriting answers.
	 *
	 * This method removes all underwriting answers associated with the $policyNumber
	 * passed in.
	 *
	 * @param string $policyNumber
	 * The policy number against which all underwriting answers will be removed.
	 *
	 * @return void
	 */
	public function removeAllAnswers($policyNumber) {
		
		if(empty($this->_underwritingAnswersModel)) {
            
            $this->_underwritingAnswersModel = new Datasource_Insurance_Answers();
        }
        
        return $this->_underwritingAnswersModel->removeAllAnswers($policyNumber);
	}
	
	
	/**
	 * Description given in the IChangeable interface.
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		if(empty($this->_underwritingAnswersModel)) {
            
            $this->_underwritingAnswersModel = new Datasource_Insurance_Answers();
        }
        
        return $this->_underwritingAnswersModel->changeQuoteToPolicy($quoteNumber, $policyNumber);
	}
}

?>