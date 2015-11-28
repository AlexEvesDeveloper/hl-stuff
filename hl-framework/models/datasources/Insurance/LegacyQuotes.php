<?php

class Datasource_Insurance_LegacyQuotes extends Zend_Db_Table_Multidb {

    protected $_name = 'quote';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Save a quote object in the database
     *
     * @param Model_Insurance_LegacyQuote $quote this is the quote object you want saving
     * @return boolean
     */
    public function save($quote)
    {
        $success=true;

        // Firstly we need to see if this already exists
        $select=$this->select();
        $select->where('policynumber = ?', $quote->policyNumber);
        $row = $this->fetchRow($select);

        $data = array(
                'refno'                     => $quote->refNo,
                'policynumber'              => $quote->policyNumber,
                'companyschemenumber'       => $quote->agentSchemeNumber,
                'policyname'                => $quote->policyName,
                'issuedate'                 => $quote->issueDate,
                'startdate'                 => $quote->startDate,
                'enddate'                   => $quote->endDate,
                'cancdate'                  => $quote->cancelledDate,
                'payby'                     => $quote->payBy,
                'paymethod'                 => $quote->payMethod,
                'paystatus'                 => $quote->payStatus,
                'policyoptions'             => $quote->policyOptions,
                'amountscovered'            => $quote->amountsCovered,
                'optionpremiums'            => $quote->optionPremiums,
                'paidnet'                   => $quote->paidNet,
                'propaddress1'              => is_null($quote->propertyAddress1)?'':$quote->propertyAddress1,
                'propaddress3'              => is_null($quote->propertyAddress2)?'':$quote->propertyAddress2,
                'propaddress5'              => is_null($quote->propertyAddress3)?'':$quote->propertyAddress3,
                'proppostcode'              => is_null($quote->propertyPostcode)?'':$quote->propertyPostcode,
                'datelastpayment'           => $quote->dateOfLastPayment,
                'policytype'                => $quote->policyType,
                'timecompleted'             => $quote->timeCompleted,
                'whitelabelID'              => $quote->whiteLabelID,
                'riskarea'                  => $quote->riskArea,
                'policylength'              => $quote->policyLength,
                'origin'                    => $quote->origin,
                'rateSetId'                 => $quote->rateSetID,
                'excessID'                  => $quote->excessID,
                'status'                    => $quote->status,
                'startTime'                 => $quote->startTime,
                'underwritingQuestionSetID' => $quote->underwritingQuestionSetID,
                'premium'					=> $quote->premium,
         		'IPT'						=> $quote->ipt,
         		'quote'						=> $quote->quote
            );
        if (count($row) > 0) {

            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $quote->policyNumber);
            $this->update($data, $where);
        }
        else {
            // New quote so just insert
            if (!$this->insert($data)) {
                // Failed insertion
                Application_Core_Logger::log("Can't insert quote in table {$this->_name}", 'error');
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Load an existing quote from the database into the object
     *
     * @param string $policyNumber
     * @return Model_Insurance_LegacyQuote
     */
    public function getByPolicyNumber($policyNumber) {

        $quote = new Model_Insurance_LegacyQuote();
        $select = $this->select()
                       ->where('policynumber = ?', $policyNumber);

        $row = $this->fetchRow($select);
        if ($row) {
            $quote->refNo               =  $row->refno;
            $quote->policyNumber        =  $row->policynumber;
            $quote->agentSchemeNumber   =  $row->companyschemenumber;
            $quote->policyName          =  $row->policyname;
            $quote->issueDate           =  $row->issuedate;
            $quote->startDate           =  $row->startdate;
            $quote->endDate             =  $row->enddate;
            $quote->cancelledDate       =  $row->cancdate;
            $quote->payBy               =  $row->payby;
            $quote->payMethod           =  $row->paymethod;
            $quote->payStatus           =  $row->paystatus;
            $quote->policyOptions       =  $row->policyoptions;
            $quote->amountsCovered      =  $row->amountscovered;
            $quote->optionPremiums      =  $row->optionpremiums;
            $quote->paidNet             =  $row->paidnet;
            $quote->propertyAddress1    =  $row->propaddress1;
            $quote->propertyAddress2    =  $row->propaddress3;
            $quote->propertyAddress3    =  $row->propaddress5;
            $quote->propertyPostcode    =  $row->proppostcode;
            $quote->dateOfLastPayment   =  $row->datelastpayment;
            $quote->policyType          =  $row->policytype;
            $quote->timeCompleted       =  $row->timecompleted;
            $quote->riskArea            =  $row->riskarea;
            $quote->whiteLabelID        =  $row->whitelabelID;
            $quote->policyLength        =  $row->policylength;
            $quote->origin              =  $row->origin;
            $quote->rateSetID           =  $row->rateSetID;
            $quote->excessID            =  $row->excessID;
            $quote->status              =  $row->status;
            $quote->startTime           =  $row->startTime;
            $quote->paySchedule         =  $row->payby;
            $quote->underwritingQuestionSetID = $row->underwritingQuestionSetID;
            $quote->dateStarted         =   $row->dateStarted;
            $quote->premium 					=  $row->premium;
	        $quote->ipt     					=  $row->IPT;
	        $quote->quote   					=  $row->quote;
            
	        
            // Calculate the missing data that isn't currently stored in the database
            $agent = new Datasource_Core_Agents();
            $quote->agentRateSetID = $agent->getRatesetID($quote->agentSchemeNumber);
            return $quote;
        }
        else{
            return null;
        }

    }



    /**
     * Getter for the policy option match field
     *
     * Returns the relevant match value (amount/premium/discounts) by which $optionName is covered on the current
     * quote / policy. This can be used to identify specific match value,
     * without the need of extracting the entire contents of the matching fields
     * field and splitting up the pipe-delimited fields.
     *
     * @param $policyOption
     * @param string $optionName, $matchfield
     * The policy option for which the matching field will be returned. It is
     * recommended that calling code use the constants in the Model_PolicyOptionConstants
     * class as values to be passed to this method.
     *
     * @param $matchfield
     * @return mixed
     */
    public function getPolicyOptionMatch($policyOption,$optionName,$matchfield)
    {
        $returnVal = 0;

        //Put the policyoptions and amountscovered into arrays for easier
        //processing.
        $policyOptionsArray = explode("|", $policyOption);
        $matchArray = explode("|", $matchfield);
         
        if(empty($matchArray))
        {
            return $returnVal;
        }
        
        //Now determine if the $optionName exists in the policyoptions, and
        //if yes, return its corresponding amountscovered value.
        if(in_array($optionName, $policyOptionsArray)) {
           
           $key = array_search($optionName, $policyOptionsArray);
           $returnVal = (empty($matchArray[$key])) ? 0 : $matchArray[$key];
        }
        
             
        return $returnVal;
    }


    /**
     * Removes a quote from the datasource.
     *
     * @param array $args
     * An array of identifiers which can be used to identify the quote
     * to be deleted. The identifiers MUST correspond to one of the quote
     * identifier consts exposed by the Manager_Insurance_TenantsContentsPlus_Quote
     * class: CUSTOMER_REFNO or POLICY_NUMBER. Only one is required.
     *
     * @throws Zend_Exception
     * @return void
     */
    public function remove($args)
    {
        $customerRefNo = null;
        $policyNumber = null;

        if(!empty($args)) {
            if(isset($args[Manager_Insurance_TenantsContentsPlus_Quote::CUSTOMER_REFNO])) {
                $customerRefNo = $args[Manager_Insurance_TenantsContentsPlus_Quote::CUSTOMER_REFNO];
            }

            if(isset($args[Manager_Insurance_TenantsContentsPlus_Quote::POLICY_NUMBER])) {
                $policyNumber = $args[Manager_Insurance_TenantsContentsPlus_Quote::POLICY_NUMBER];
            }
        }

        if($customerRefNo != null) {
            $where = $this->quoteInto('refno = ?', $customerRefNo);
        }
        else if($policyNumber != null) {
            $where = $this->quoteInto('policynumber = ?', $policyNumber);
        }
        else {
            throw new Zend_Exception('No arguments provided.');
        }

        $this->delete($where);
    }

    /**
     * Get all quotes linked to the customer reference numbers provided, restricted to the policy number search
     * provided, ordered by the field provided in the direction provided that have not been referred or released
     * from underwriting.
     *
     * @param array $customerRefs Array of customer reference numbers
     * @param string $quoteSearch Quote number search restriction
     * @param array $orderBy Array of order by fields
     * @return array of Model_Insurance_LegacyQuote
     */
    public function getActiveQuotes($customerRefs, $quoteSearch, $orderBy = array())
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
            $transformedOrderBy = array('issuedate DESC');
        }

        // Build query
        $select = $this->select()
                       ->from($this->_name)
                       ->columns(array(new Zend_Db_Expr('DATE_ADD(issuedate, interval 60 DAY) as validuntildate')))
                       ->where('refno IN (?)', $customerRefs)
                       ->where('DATE_ADD(issuedate, interval 60 DAY) >= CURDATE()')
                       ->order($transformedOrderBy);

        // Restrict policy number search field
        if ($quoteSearch != '') {
            $select->having('policynumber LIKE ?', '%' . $quoteSearch . '%');
        }

        if (isset($customerRefs) && count($customerRefs) > 0)
        {
		    $rowSet = $this->fetchAll($select);

            foreach ($rowSet as $row) {
                $quote = new Model_Insurance_LegacyQuote();

                $quote->refNo                       =  $row->refno;
                $quote->policyNumber                =  $row->policynumber;
                $quote->agentSchemeNumber           =  $row->companyschemenumber;
                $quote->policyName                  =  $row->policyname;
                $quote->issueDate                   =  $row->issuedate;
                $quote->startDate                   =  $row->startdate;
                $quote->endDate                     =  $row->enddate;
                $quote->cancelledDate               =  $row->cancdate;
                $quote->payBy                       =  $row->payby;
                $quote->payMethod                   =  $row->paymethod;
                $quote->payStatus                   =  $row->paystatus;
                $quote->policyOptions               =  $row->policyoptions;
                $quote->amountsCovered              =  $row->amountscovered;
                $quote->optionPremiums              =  $row->optionpremiums;
                $quote->paidNet                     =  $row->paidnet;
                $quote->propertyAddress1            =  $row->propaddress1;
                $quote->propertyAddress2            =  $row->propaddress3;
                $quote->propertyAddress3            =  $row->propaddress5;
                $quote->propertyPostcode            =  $row->proppostcode;
                $quote->dateOfLastPayment           =  $row->datelastpayment;
                $quote->policyType                  =  $row->policytype;
                $quote->timeCompleted               =  $row->timecompleted;
                $quote->riskArea                    =  $row->riskarea;
                $quote->whiteLabelID                =  $row->whitelabelID;
                $quote->policyLength                =  $row->policylength;
                $quote->origin                      =  $row->origin;
                $quote->rateSetID                   =  $row->rateSetID;
                $quote->excessID                    =  $row->excessID;
                $quote->status                      =  $row->status;
                $quote->startTime                   =  $row->startTime;
                $quote->paySchedule                 =  $row->payby;
                $quote->underwritingQuestionSetID   =  $row->underwritingQuestionSetID;
                $quote->dateStarted                 =  $row->dateStarted;
                $quote->premium 					=  $row->premium;
                $quote->ipt     					=  $row->IPT;
                $quote->quote   					=  $row->quote;
                $quote->validUntilDate              =  $row->validuntildate;

                $results[] = $quote;
            }
        }

        return $results;
    }
}
