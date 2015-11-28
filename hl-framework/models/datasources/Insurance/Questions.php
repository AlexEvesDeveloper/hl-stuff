<?php

/**
 * Model definition for the underwritingQuestions table.
 */
class Datasource_Insurance_Questions extends Zend_Db_Table_Multidb
{
    /**
     * @var string database identifier
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * @var string table name
     */
    protected $_name = 'underwritingQuestions';

    /**
     * @var string primary id
     */
    protected $_primary = 'underwritingQuestionID';
    
    /**
	 * Returns the expected (correct) answer for a specified underwriting question.
	 *
	 * @param integer $questionID
	 * Identifies the underwriting question.
	 * 
	 * @return mixed
	 * Returns a string corresponding to one of the consts exposed by the
	 * Model_Insurance_Answer class, or null if the question cannot be found
	 * in the datasource.
	 */
    public function getExpectedAnswer($questionID) {
		
    	$select = $this->select();
        $select->where('underwritingQuestionID = ?', $questionID);
        $row = $this->fetchRow($select);
        
        if(!empty($row)) {
        	
        	$expectedAnswer = $row->correctAnswer;
        }
        else {
        	
        	Application_Core_Logger::log("Can't retrieve underwriting question in table {$this->_name}", 'error');
        	$expectedAnswer = null;
        }
        
        return $expectedAnswer;
    }

    /**
     * Gets a list of questionIds for the given question set
     *
     * @param int $questionSetID
     * @param string $policyName
     * @return array
     */
    public function getQuestionIdList($questionSetID, $policyName)
    {
        $select = $this->select();
        $select->from(
            array('q' => $this->_name),
            array('questionNumber', 'underwritingQuestionID')
        );
        $select
            ->where('questionSetID = ?', $questionSetID)
            ->where('type = ?', $policyName)
            ->order('questionNumber');

        $rowSet = $this->fetchAll($select);

        $results = array();
        foreach ($rowSet as $row) {
            $results[$row->questionNumber] = $row->underwritingQuestionID;
        }
        return $results;
    }
}