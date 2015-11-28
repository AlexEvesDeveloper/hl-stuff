<?php

/**
 * Model definition for the underwritingAnswers table.
 */
class Datasource_Insurance_Answers extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';
	protected $_name = 'underwritingAnswers';
    protected $_primary = 'answerID';
    
    protected $_questionsModel;
    
    
    /**
	 * Writes an array of UW answers to the dbase.
	 *
	 * This method writes to the dbase the answers given to the underwriting
	 * (declaration) questions. It expects an array of all the underwriting answers.
	 *
	 * @param array $answers
	 * An array of Model_Insurance_Answer objects encapsulating the
	 * answers given by the user in response to the UW questions.
	 */
    public function insertUnderwritingAnswers($answers) {
		
		//Insert each of the answers.
		foreach($answers as $currentAnswer) {
			
			$this->insertUnderwritingAnswer($currentAnswer);
		}
    }
	
	/**
	 * Writes a single UW answer to the dbase.
	 *
	 * This method writes to the dbase the UW answer passed in. It expects a single
	 * underwriting answer, encapsulated in an Model_Insurance_Answer
	 * object.
	 *
	 * @param Model_Insurance_Answer $answer
	 * An Model_Insurance_Answer object encapsulating the answer given
	 * by the user in response to the corresponding UW question.
	 */
	public function insertUnderwritingAnswer($answer) {
		
		$dateAnswered = $answer->getDateAnswered()->toString(Zend_Date::ISO_8601);			
		$data = array(
			'policyNumber' => $answer->getPolicyNumber(),
			'questionID' => $answer->getQuestionNumber(),
			'answerGiven' => $answer->getAnswer(),
			'dateAnswered' => $dateAnswered
		);
		
		if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert underwriting answer in table {$this->_name}", 'error');
        }
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
		
		$select = $this->select();
        $select->where('policyNumber = ?', $policyNumber);
        $answersArray = $this->fetchAll($select);
		
		$returnArray = array();
		foreach($answersArray as $currentRow) {
			
			$underwritingAnswer = new Model_Insurance_Answer();
			$underwritingAnswer->setPolicyNumber($currentRow['policyNumber']);
			$underwritingAnswer->setQuestionNumber($currentRow['questionID']);
			$underwritingAnswer->setAnswer($currentRow['answerGiven']);
			
			$dateAnswered = new Zend_Date($currentRow['dateAnswered'], Zend_Date::ISO_8601);
			$underwritingAnswer->setDateAnswered($dateAnswered);


			//Now set the expected answer.
			if(empty($this->_questionsModel)) {
				
				$this->_questionsModel = new Datasource_Insurance_Questions();
			}
			$expectedAnswer = $this->_questionsModel->getExpectedAnswer($currentRow['questionID']);
			$underwritingAnswer->setExpectedAnswer($expectedAnswer);
			$returnArray[] = $underwritingAnswer;
		}
		
		
		//Finalise the return value consistent with this functions contract.
		if(empty($returnArray)) {
			// No warning given as this is a common/normal scenario
			$returnVal = null;
		}
		else {
			
			$returnVal = $returnArray;
		}
		
		return $returnVal;			
	}
	
	/**
	 * Determines if an answer has been stored.
	 *
	 * This method will determine if the UW answer passed in has already been
	 * stored in the database. Will return true or false accordingly. Will search on
	 * all aspects of the answer.
	 *
	 * @param Model_Insurance_Answer $answer The answer to search for in the database.
     * @param bool $isAnswerIncluded (default true) Flag to determine whether the answer is included in the check
     * @return bool True if the answer has been previously stored, false otherwise.
     */
    public function getExists($answer, $isAnswerIncluded=true)
    {
        $select = $this->select();
        $select->where('policyNumber = ?', $answer->getPolicyNumber());
        $select->where('questionID = ?', $answer->getQuestionNumber());
        if ($isAnswerIncluded) {
            $select->where('answerGiven = ?', $answer->getAnswer());
        }
		$select->where('dateAnswered = ?', $answer->getDateAnswered()->toString('YYYY-MM-dd'));
        $rowSet = $this->fetchAll($select);
		
		if (count($rowSet) >= 1) {
			return true;
		}
        // No warning given as this is a common/normal scenario
		return false;
	}

    /**
     * Gets the existing answer as stored in the database
     *
     * @param Model_Insurance_Answer $answer The answer to search for in the database.
     * @return null|string Stored answer or null if it does not exist in the database
     */
    public function getExistingAnswer($answer)
    {
        $select = $this->select();
        $select
            ->where('policyNumber = ?', $answer->getPolicyNumber())
            ->where('questionID = ?', $answer->getQuestionNumber())
            ->order(array('dateAnswered desc', 'answerID desc'))
            ->limit(1)
        ;

        $row = $this->fetchRow($select);
        if ($row) {
            return $row->answerGiven;
        }
        return null;
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
		
        $where = $this->quoteInto('policyNumber = ?', $policyNumber);
        $this->delete($where);
	}

	
	/**
	 * Description given in the IChangeable interface.
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$where = $this->quoteInto('policyNumber = ?', $quoteNumber);
		$updatedData = array('policyNumber' => $policyNumber);
		return $this->update($updatedData, $where);
	}
}

?>