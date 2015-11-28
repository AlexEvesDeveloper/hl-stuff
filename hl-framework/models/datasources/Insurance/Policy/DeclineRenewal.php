<?php

/**
 * Model definition for policy datasource.
 */
class Datasource_Insurance_Policy_DeclineRenewal extends Datasource_Insurance_LegacyQuotes
{
    protected $_name = 'DECLINEDRENEWAL';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Decline a policy renewal invitation, record the data into the declined
     * renewal tables.
     *
     * @param string $policynumber Policy number of policy to decline renewal
     * return bool Status of insert
     */
    public function declinePolicyRenewal($policynumber)
    {
        return $this->insert(array('policynumber' => $policynumber));
    }
    
    /**
     * Detect if the policy has been declined for renewal
     *
     * @param string $policynumber Policy number to check
     * @return bool True if decline, false if not
     */
    public function isPolicyDeclined($policynumber)
    {
        $select = $this->select();
        $select->from($this->_name, array(new Zend_Db_Expr('count(*) as declinecount')));
        $select->where('policynumber = ?', $policynumber);
        $select->limit(1);
        $row = $this->fetchRow($select);
        
        return ($row->declinecount == 0 ? false : true);
    }
}
