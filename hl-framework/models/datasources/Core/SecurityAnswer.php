<?php

class Datasource_Core_SecurityAnswer extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_homelet';
    protected $_name = 'security_answers';
    protected $_primary = 'customer_id';
    /**#@-*/

    /**
     * Update customers security question/answer preference
     *
     * @param $identifier Customer identifier
     * @param $question_identifier Security question identifier
     * @param $answer Security answer
     */
    public function updateCustomerSecurityAnswer($identifier, $question_identifier, $answer)
    {
        // Delete previous answer
        $where = $this->getAdapter()->quoteInto('customer_id = ?', $identifier);
        $this->delete($where);

        // Insert security answer
        $data = array(
            'customer_id'           => $identifier,
            'security_question_id'  => $question_identifier,
            'security_answer'       => $answer,
        );

        $this->insert($data);
    }

    /**
     * Get the customer security question preference and answer
     *
     * @param $identifier Customer identifier
     * @return array|null Security answer identifier and answer, or null
     */
    public function getCustomerSecurityAnswer($identifier) {
        $select = $this->select();
        $select->where('customer_id = ?', $identifier);
        $securityAnswerRow = $this->fetchRow($select);

        if ($securityAnswerRow)  {
            return array($securityAnswerRow->security_question_id, $securityAnswerRow->security_answer);
        }
        else {
            return null;
        }
    }
}
