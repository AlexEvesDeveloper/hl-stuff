<?php

/**
 * Model definition for the salesperson datasource.
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Salesperson
 *
 */
class Datasource_Core_Salesperson extends Zend_Db_Table_Multidb {

    protected $_name = 'salesmen';
    protected $_primary = 'salesarea';
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Retrieves a salesperson's details.
     *
     * @param unknown_type $salespersonId
     *
     * @return mixed A Model_Core_Salesperson object encapsulating the agent details or null if not found.
     */
    public function getSalesperson($salespersonId) {
        $select = $this->select();
        $select->where('salesarea = ?', $salespersonId);
        $salespersonRow = $this->fetchRow($select);

        if (!empty($salespersonRow)) {

            $contactDetails = new Model_Core_ContactDetails();
            $contactDetails->telephone1 = $salespersonRow->salesphone;
            $contactDetails->telephone2 = $salespersonRow->salesmobile;
            $contactDetails->email1 = $salespersonRow->salesemail;
            $contactDetails->fax1 = $salespersonRow->salesfax;

            $questionsAndAnswers = $this->getSalespersonQuestionsAndAnswers($salespersonId);

            $salesperson = new Model_Core_Salesperson();

            $salesperson->name =            $salespersonRow->salesman;
            $salesperson->contactDetails =  $contactDetails;
            $salesperson->commissionRate =  $salespersonRow->commissionrate;
            $salesperson->code =            $salespersonRow->code;
            $salesperson->questionAnswers = $questionsAndAnswers;

            $returnVal = $salesperson;
        } else {
            $returnVal = null;
        }

        return $returnVal;
    }

    /**
     * Fetch all available questions that can be posed to salespeople.
     *
     * @return array Array of (int)question ID => (string)question tuples.
     */
    public function getSalespersonAllQuestions() {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('q' => 'salesmenquestion')
            );
        $allQuestions = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allQuestions as $questionRow) {
            $returnVal[$questionRow->smq_id] = $questionRow->smq_question;
        }

        return $returnVal;
    }

    /**
     * Fetch the questions and answers for a salesperson.
     *
     * @param int $salespersonId
     *
     * @return array Array of Model_Core_Salesperon_Answer.
     */
    public function getSalespersonQuestionsAndAnswers($salespersonId) {

        /*
         * SELECT a.sma_answer, q.*
         *   FROM salesmenanswer AS a JOIN salesmenquestion AS q
         *   ON a.sma_smq_id = q.smq_id
         *   WHERE a.sma_sm_id = $salespersonId
         *   ORDER BY q.smq_id ASC;
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('a' => 'salesmenanswer'),
                array('sma_answer')
            )
            ->join(
                array('q' => 'salesmenquestion'),
                'a.sma_smq_id = q.smq_id'
            )
            ->where('a.sma_sm_id = ?', $salespersonId)
            ->order('q.smq_id ASC');

        $salespersonQAs = $this->fetchAll($select);

        $returnVal = array();
        foreach ($salespersonQAs as $questionRow) {
            $question = new Model_Core_Salesperson_Question();
            $question->question = $questionRow->smq_question;

            $answer = new Model_Core_Salesperson_Answer();
            $answer->question = $question;
            $answer->answer = $questionRow->sma_answer;

            $returnVal[] = $answer;
        }

        return $returnVal;
    }

}