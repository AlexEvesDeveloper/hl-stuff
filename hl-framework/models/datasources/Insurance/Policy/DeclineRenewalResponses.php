<?php

/**
 * Model definition for policy datasource.
 */
class Datasource_Insurance_Policy_DeclineRenewalResponses extends Datasource_Insurance_LegacyQuotes
{
    protected $_name = 'DECLINERENEWAL_RESPONSES';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Add a response for declined renewal for a policy number
     *
     * @param string $policynumber Policy number to add declined response for
     * @param int $questionid question id number to link response to
     * @param string $reason Reason label
     * @param string $why Why the renewal was declined, if given
     */
    public function addResponse($policynumber, $questionid, $reason, $why = null)
    {
        $data = array
        (
            'policynumber' => $policynumber,
            'question_id' => $questionid,
            'time_stamp' => new Zend_Db_Expr('NOW()'),
            'response' => ($why != null && $why != '') ? $why : $reason,
        );
        
        return $this->insert($data);
    }
}
