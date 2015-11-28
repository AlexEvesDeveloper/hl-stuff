<?php

/**
 * Model definition for policy datasource.
 */
class Datasource_Insurance_Policy_DeclineRenewalQuestions extends Datasource_Insurance_LegacyQuotes
{
    protected $_name = 'DECLINERENEWAL_QUESTIONS';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Get the question id number
     *
     * @param string $label Question label
     * @return int Question Id number
     */
    public function getQuestionId($label)
    {
        $select = $this->select();
        $select->from($this->_name, array('id'));
        $select->where('question_name = ?', $label);
        $select->limit(1);
        
        $row = $this->fetchRow($select);
        
        if ($row != null)
            return $row->id;
        
        return null;
    }
}
