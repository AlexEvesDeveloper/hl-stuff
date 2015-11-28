<?php

/**
 * Model definition for policy datasource.
 */
class Datasource_Insurance_LegacyPolicies extends Datasource_Insurance_LegacyQuotes
{
    protected $_name = 'policy';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Load an existing quote from the database into a domain object
     * using the policy refno
     *
     * @param string $refNum
     * @return Model_Insurance_LegacyPolicy
     */
    public function getByRefNo($refNum)
    {
        $select = $this->select()->where('refno = ?', $refNum);
        $row = $this->fetchRow($select);
        return $this->_buildDomainObject($row);
    }
    
    /**
     * Load an existing quote from the database into a domain object
     * using the policy number
     *
     * @param string $policynumber Policy number
     * @return Model_Insurance_LegacyPolicy
     */
    public function getByPolicyNumber($policynumber)
    {
        $select = $this->select()->where('policynumber = ?', $policynumber);
        $row = $this->fetchRow($select);
        return $this->_buildDomainObject($row);
    }

    /**
     * @param $row
     * @return Model_Insurance_LegacyPolicy
     */
    protected function _buildDomainObject($row)
    {
        if ($row)
        {
            $policy = new Model_Insurance_LegacyPolicy();
            
            $policy->refNo               		=  $row->refno;
            $policy->policyNumber        		=  $row->policynumber;
            $policy->agentSchemeNumber   		=  $row->companyschemenumber;
            $policy->policyName          		=  $row->policyname;
            $policy->issueDate           		=  $row->issuedate;
            $policy->startDate           		=  $row->startdate;
            $policy->endDate             		=  $row->enddate;
            $policy->cancelledDate       		=  $row->cancdate;
            $policy->payBy               		=  $row->payby;
            $policy->payMethod           		=  $row->paymethod;
            $policy->payStatus           		=  $row->paystatus;
            $policy->policyOptions       		=  $row->policyoptions;
            $policy->amountsCovered      		=  $row->amountscovered;
            $policy->optionPremiums      		=  $row->optionpremiums;
            $policy->paidNet             		=  $row->paidnet;
            $policy->propertyAddress1    		=  $row->propaddress1;
            $policy->propertyAddress2    		=  $row->propaddress3;
            $policy->propertyAddress3    		=  $row->propaddress5;
            $policy->propertyPostcode    		=  $row->proppostcode;
            $policy->dateOfLastPayment   		=  $row->datelastpayment;                
            $policy->policyType          		=  $row->policytype;
            $policy->timeCompleted       		=  $row->timecompleted;
            $policy->riskArea            		=  $row->riskarea;
            $policy->whiteLabelID        		=  $row->whitelabelID;
            $policy->policyLength        		=  $row->policylength;
            $policy->origin              		=  $row->origin;
            $policy->rateSetID           		=  $row->rateSetID;
            $policy->excessID            		=  $row->excessID;
            $policy->status              		=  $row->status;
            $policy->startTime           		=  $row->startTime;
            $policy->paySchedule         		=  $row->payby;
            $policy->underwritingQuestionSetID 	=  $row->underwritingQuestionSetID;
            $policy->dateStarted         		=  $row->dateStarted;
            $policy->premium 					=  $row->premium;
	        $policy->ipt     					=  $row->IPT;
	        $policy->quote   					=  $row->quote;
	        
            // Calculate the missing data that isn't currently stored in the database
            $agent = new Datasource_Core_Agents();
            $policy->agentRateSetID = $agent->getRatesetID($policy->agentSchemeNumber);
            
            return $policy;
        }
    }

    /**
     * Get all policies linked to the customer reference numbers provided, restricted to the policy number search
     * provided, ordered by the field provided in the direction provided that have not been referred or released
     * from underwriting.
     *
     * @param $customerRefs Array of customer reference numbers
     * @param $quoteSearch Quote number search restriction
     * @param $orderBy Array of order by fields
     * @return array of Model_Insurance_LegacyPolicy
     */
    public function getActivePolicies($customerRefs, $quoteSearch, $orderBy = array())
    {
        $results = array();

        // Transform order by parameter
        if (count($orderBy) > 0)
        {
            $transformedOrderBy = array();
            foreach ($orderBy as $orderByField => $orderByDir) {
                // Validate direction
                if (!in_array(strtoupper($orderByDir), array('ASC', 'DESC'))) {
                    // Invalid direction, ignore
                    continue;
                }

                $transformedOrderBy[] = "$orderByField $orderByDir";
            }
        }
        else
        {
            // Default sort
            $transformedOrderBy = array('startdate DESC');
        }

        // Build query
        $select = $this->select()
            ->from($this->_name)
            ->columns(array(new Zend_Db_Expr('enddate as renewaldate')))
            ->where('refno IN (?)', $customerRefs)
            ->where('paystatus NOT IN ("Referred","ReleasedFromUnderwriting")')
            ->order($transformedOrderBy);

        // Restrict policy number search field
        if ($quoteSearch != '') {
            $select->having('policynumber LIKE ?', '%' . $quoteSearch . '%');
        }

        if (isset($customerRefs) && count($customerRefs) >0)
		$rowSet = $this->fetchAll($select);

        foreach ($rowSet as $row) {
            $policy = new Model_Insurance_LegacyPolicy();

            $policy->refNo                       =  $row->refno;
            $policy->policyNumber                =  $row->policynumber;
            $policy->agentSchemeNumber           =  $row->companyschemenumber;
            $policy->policyName                  =  $row->policyname;
            $policy->issueDate                   =  $row->issuedate;
            $policy->startDate                   =  $row->startdate;
            $policy->endDate                     =  $row->enddate;
            $policy->cancelledDate               =  $row->cancdate;
            $policy->payBy                       =  $row->payby;
            $policy->payMethod                   =  $row->paymethod;
            $policy->payStatus                   =  $row->paystatus;
            $policy->policyOptions               =  $row->policyoptions;
            $policy->amountsCovered              =  $row->amountscovered;
            $policy->optionPremiums              =  $row->optionpremiums;
            $policy->paidNet                     =  $row->paidnet;
            $policy->propertyAddress1            =  $row->propaddress1;
            $policy->propertyAddress2            =  $row->propaddress3;
            $policy->propertyAddress3            =  $row->propaddress5;
            $policy->propertyPostcode            =  $row->proppostcode;
            $policy->dateOfLastPayment           =  $row->datelastpayment;
            $policy->policyType                  =  $row->policytype;
            $policy->timeCompleted               =  $row->timecompleted;
            $policy->riskArea                    =  $row->riskarea;
            $policy->whiteLabelID                =  $row->whitelabelID;
            $policy->policyLength                =  $row->policylength;
            $policy->origin                      =  $row->origin;
            $policy->rateSetID                   =  $row->rateSetID;
            $policy->excessID                    =  $row->excessID;
            $policy->status                      =  $row->status;
            $policy->startTime                   =  $row->startTime;
            $policy->paySchedule                 =  $row->payby;
            $policy->underwritingQuestionSetID   =  $row->underwritingQuestionSetID;
            $policy->dateStarted                 =  $row->dateStarted;
            $policy->premium 					 =  $row->premium;
            $policy->ipt     					 =  $row->IPT;
            $policy->quote   					 =  $row->quote;
            $policy->renewalDate                 =  $row->renewaldate;

            $results[] = $policy;
        }

        return $results;
    }
}
