<?php

/**
 * Model definition for the legacy policy search datasource.  Generally used by
 * the Insurance Munt Manager.
 */
class Datasource_Insurance_Legacy_PolicySearch extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'policyQuoteMerge';
    protected $_primary = 'policynumber';
    /**#@-*/
    
    /**
     * Retrieves matching policy details.
     *
     * @param string $agentschemeno
     * The agent scheme number.
     *
     * @param array criteria
     * An array of search criteria. Must be zero or more of the following only:
     *
     * $criteria['paymentRef']
     * $criteria['campRefNo']
     * $criteria['address1']
     * $criteria['address3']
     * $criteria['postcode']
     * $criteria['policyNo']
     *
     * @return Zend_Db_Table_Rowset_Abstract
     * The results of the search. Could be anything in there, couldn't it?
     */
    public function getPolicies($agentschemeno, $criteria = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name);
        $select->joinInner('customer', 'customer.refno = policyQuoteMerge.refno');
        
        if (!empty($criteria['paymentRef'])) {
            $select->joinInner('schedule', 'schedule.policynumber = policyQuoteMerge.policynumber');
            $select->where("policyQuoteMerge.paystatus != 'DELETED'");
            $select->where($this->quoteInto('companyschemenumber = ? ', $agentschemeno));
            $select->where($this->quoteInto('schedule.paymentrefno = ? ', $criteria['paymentRef']));
        } else {
            if (!empty($criteria['campRefNo'])) {
                $select->joinInner('Campaign', 'Campaign.policynumber = policyQuoteMerge.policynumber');
                $select->where("policyQuoteMerge.paystatus != 'DELETED'");
                $select->where($this->quoteInto('companyschemenumber = ? ', $agentschemeno));
                $select->where($this->quoteInto('Campaign.refno = ? ', $criteria['campRefNo']));
            } else {
                $select->where("policyQuoteMerge.paystatus != 'DELETED'");
                $select->where($this->quoteInto('companyschemenumber = ? ', $agentschemeno));
                
                # Remove policies that have the liabilitytp cover included
//                $select->where("0 = (SELECT COUNT(*) FROM policyCover c LEFT JOIN policyOptions o ON c.policyOptionID = o.policyOptionID WHERE policyOption = 'liabilitytp' AND sumInsured > 0 AND policynumber = ?)", $criteria['policyNo']);
                
                if(!empty($criteria['address1'])) {
                    $like = '%' . $criteria['address1'] . '%';
                    $select->where($this->quoteInto('propAddress1 LIKE ? ', $like));
                }
                
                if(!empty($criteria['address3'])) {
                    $like = '%' . $criteria['address3'] . '%';
                    $select->where($this->quoteInto('propAddress5 LIKE ? ', $like));
                }
                
                if(!empty($criteria['postcode'])) {
                    $like = '%' . $criteria['postcode'] . '%';
                    $select->where($this->quoteInto('propPostcode LIKE ? ', $like));
                }
                
                if(!empty($criteria['policyNo'])) {
                    $select->where($this->quoteInto('policynumber = ? ', $criteria['policyNo']));
                }
            }
        }
        
        $output = array();
        foreach($this->fetchAll($select) as $resultRow) {
            $output[] = $resultRow;
        }
        return $output;
    }
    
    /**
     * Retrieves raw policy details.
     *
     * @param string $polno
     * The full policy/quote number.
     * 
     * @return Zend_Db_Table_Row_Abstract
     * Encapsulates details of the policy.
     */
    public function getPolicy($polno)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        if(preg_match("/PRGI/i",$polno)) {
            $select->from(
                $this->_name,
                array(
                    '*',
                    new Zend_Db_Expr('Round(TransactionSupport.RunningAmount, 2) AS quote'),
                    new Zend_Db_Expr('Round(TransactionSupport.Insurance, 2) AS Insurance'),
                    new Zend_Db_Expr('Round(TransactionSupport.IPT, 2) AS IPT'),
                    new Zend_Db_Expr('Round(TransactionSupport.Income, 2) AS premium')
                )
            );
            
            $select->joinInner('referencing_uk.Enquiry', 'referencing_uk.Enquiry.policynumber = policyQuoteMerge.policynumber');
            $select->joinInner('referencing_uk.TransactionSupport', 'referencing_uk.TransactionSupport.EnquiryID = referencing_uk.Enquiry.ID');
            $select->where($this->quoteInto('policyQuoteMerge.policynumber = ? ', $polno));
            $select->order('policyQuoteMerge.startdate');
        } else {
           $select->from($this->_name);
           $select->where($this->quoteInto('policynumber = ? ', $polno));
        }
        
        $select->limit(1);
        return $this->fetchRow($select);
    }
}
?>
