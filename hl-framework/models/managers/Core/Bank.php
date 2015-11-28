<?php

/**
* Banking rules class providing bank sortcode validation services. The rules are based entirely on those
* provided by VocaLink - maintainers of the ISCD on behalf of the UK Payments Administration Ltd. The latest
* version of the rules can be found at: www.vocalink.com/moduluschecking
*
* The institutions covered by these rules are:
*
* Alliance & Leicester
* Allied Irish
* Bank of England
* Bank of Ireland
* Bank of Scotland
* Barclays
* Bradford and Bingley Building Society
* Citibank
* Clydesdale
* Co-Operative Bank
* Coutts
* First Trust
* Halifax
* Hoares Bank
* HSBC
* Lloyds TSB
* NatWest
* Nationwide Building Society
* Northern
* Royal Bank of Scotland
* Santander
* Ulster Bank
* Woolwich
* Yorkshire Bank
*
* Some sorting codes for the above institutions may not have modulus checking
* routines. In this case, and for institutions not covered, 'The sorting code 
* and account number should be presumed valid unless other evidence implies 
* otherwise' -VocaLink
*/
class Manager_Core_Bank
{
    
    /**
     * Utility method that formats the bank sortcode passed in.
     *
     * Makes formatting corrections to the bank sortcode passed in to to ensure
     * a minimum of 6 digits in length and the removal of whitespace and 
     * hyphens.
     * Will pad the beginning of the sortcode with zeros to achieve the correct
     * length of 6 digits, if the string passed in is less than this.
     * 
     * @param string $sortCode
     * The bank sortcode to format.
     * 
     * @return string
     * The formatted sortcode.
     */
    public function formatSortCode($sortCode) 
    {
        
        $sortCode = preg_replace('/[^0-9]/', '', "$sortCode");
        $len = strlen($sortCode);
        
        for ($i = 1; $i <= 6 - $len; $i++)
            $sortCode = '0' . $sortCode;
        
        return $sortCode;
    }
    
    
    /**
     * Utility method that formats the bank account number passed in.
     *
     * Makes formatting corrections to the bank account number passed in to to 
     * ensure a minimum of 8 digits in length and the removal of whitespace and 
     * hyphens.
     * Will pad the beginning of the sortcode with zeros to achieve the correct
     * length of 8 digits, if the string passed in is less than this.
     * 
     * @param string $accountNumber
     * The bank account number to format.
     * 
     * @return string
     * The formatted account number.
     */
    public function formatAccountNumber($accountNumber)
    {
        
        // Remove white space and convert to string
        $accountNumber = preg_replace('/[^0-9]/', '', "$accountNumber");
        $len = strlen($accountNumber);
        
        for ($i = 1; $i <= 8 - $len; $i++)
             $accountNumber = '0' . $accountNumber;
        
        return $accountNumber;
    }
    
    
    /**
     * Returns the branch details corresponding to the sortcode passed in.
     *
     * The details are encapsulated in a Model_Core_Bank_BranchDetail object,
     * and include the bank name and address.
     *
     * @param string $sortCode
     * The sortcode with which to locate the appropriate branch.
     *
     * @return mixed
     * Returns a Model_Core_Bank_BranchDetail object populated with the branch
     * details, if the branch can be located. Returns null otherwise.
     */
    public function getBranchDetail($sortCode) 
    {
        
        $iscdDataSource = new Datasource_Core_Bank_Iscd();
        $iscd = $iscdDataSource->getIscd($this->formatSortCode($sortCode));
        
        if ( !empty($iscd)) {
            
            //Load up a Model_Core_Bank_BranchDetail object.
            $branchDetail = new Model_Core_Bank_BranchDetail();
            
            $branchDetail->bankNameShort = $iscd->shortNameOfOwningBank;
            $branchDetail->bankNameFull = $iscd->fullOwningBankName1;
            $branchDetail->addressLine1 = $iscd->PRINTAddress1;
            $branchDetail->addressLine2 = $iscd->PRINTAddress2;
            $branchDetail->addressLine3 = $iscd->PRINTAddress3;
            $branchDetail->addressLine4 = $iscd->PRINTAddress4;
            $branchDetail->town = $iscd->PRINTTown;
            $branchDetail->county = $iscd->PRINTCounty;
            $branchDetail->postCode = $iscd->PRINTPostcodeMajorPart . ' ' 
            .$iscd->PRINTPostcodeMinorPart;
            
            $returnVal = $branchDetail;
        } else {
            
            $returnVal = null;
        }
        
        return $returnVal;
    }

    
    /**
     * Validates a bank sort code.
     *
     * For a sortcode to be valid, it must exist in the Industry Sorting Code
     * Directory (ISCD), maintained by VocaLink on behalf of the UK Payments
     * Administration Limited. If not, then it is invalid.
     * 
     * @param mixed $sortCode
     * Sort code to validate. Can be  string or integer.
     *
     * @return boolean
     * Returns true if successfully validated, false otherwise.
     */
    public function isSortCodeValid($sortCode) {
        
        //Attempt to identify a match in the ISCD datasource.
        $iscdDataSource = new Datasource_Core_Bank_Iscd();
        $iscd = $iscdDataSource->getIscd($this->formatSortCode($sortCode));
    
        if ($iscd != null) {
            
            $isValid = true;
        } else {
            
            $isValid = false;
        }
        
        return $isValid;
    }
    
    
    /**
     * Validates a bank account number.
     *
     * This method implements VocaLink-specified modulus checking on the sortcode and
     * account number passed in.
     *
     * Modulus checking is used to check the validity of account numbers for a sorting code.
     * If the sorting code does not exist, this check will not be applicable. Under these
     * circumstances, the account number will be identified as 'valid', as there will be
     * no evidence indicating otherwise.
     *
     * @param mixed $sortCode
     * The bank branch sortcode to validate. Does not have to be be formatted, as this method
     * will format it. Must be a string or an integer.
     * 
     * @param mixed $accountNumber
     * The bank account number to validate. Does not have to be be formatted, as this method
     * will format it. Must be a string or an integer.
     *
     * @return boolean
     * Returns true if the bank account is valid, false otherwise.
     */
    public function isAccountNumberValid($sortCode, $accountNumber) {
        
        //Clean the data passed in.
        $accountNumber = $this->formatAccountNumber($accountNumber);
        $sortCode = $this->formatSortCode($sortCode);
        
        // First test.
        if (!$this->isSortCodeValid($sortCode)) {
            
            //Invalid account details.
            return false;
        }
        
        try {
            //Convert non-standard account numbers into 8 digit account numbers, where necessary.
            $accountIdentifiers = $this->_getConvertedAccountIdentifiers($sortCode, $accountNumber);
            $sortCode = $accountIdentifiers['sortCode'];
            $accountNumber = $accountIdentifiers['accountNumber'];
        }
        catch(Zend_Exception $e) {
            //Invalid account details.
            return false;
        }
        
        // Identify if the sortcode appears in a range specified by the SortCodeAccountMultiplier datasource.
        //If it does not, then the account number cannot be verified, and is therefore considered 'Valid'.
        $multiplierDatasource = new Datasource_Core_Bank_SortCodeAccountMultiplier();
        $multiplierGroups = $multiplierDatasource->getSortCodeAccountMultiplier($sortCode);
        
        // Begin modulus checking.
        $modulusChecksCount = 0;
        $passedModulusChecksCount = 0;
        
        if (!empty($multiplierGroups)) {
            foreach ($multiplierGroups as $currentMultiplierGroup) {
                //Perform a maximum of two modulus checks on the account number and sortcode.
                if ($modulusChecksCount > 2) {
                    
                    break;
                }
                
                foreach ($currentMultiplierGroup->multipliers as $currentMultiplier) {
                	$modulusChecksCount++;

                    //Instantiate a ModulusCalc object for calculating the sortcode/accountnumber modulus.
                    switch($currentMultiplier->modulusCheck) {
                        
                        case 'MOD10':
                            $modulo = Model_Core_Bank_ModulusCalc::MODULUS_10;
                            break;
                        
                        case 'MOD11':
                            $modulo = Model_Core_Bank_ModulusCalc::MODULUS_11;
                            break;
                        
                        case 'DBLAL':
                            $modulo = Model_Core_Bank_ModulusCalc::DOUBLE_ALTERNATE;
                            break;
                        
                        default:
                            //If not provided then we cannot proceed. Assume valid and continue.
                            $passedModulusChecksCount++;
                            continue;
                    }
	
                    $modulusCalc = Model_Core_Bank_ModulusCalc::factory
                    (
                        $sortCode,
                        $accountNumber,
                        $modulo,
                        $currentMultiplier
                    );
                    
                    //If there is an exception code, instantiate an appropriate ModulusException object.
                    $exception = Model_Core_Bank_ModulusExceptionFactory::
                        createModulusException($currentMultiplier->exceptionCode, $modulo, $modulusChecksCount);
                    
                    //Calculate the modulus.
	                if (empty($exception)) {
                        $modulusCalc->calculateTotal();
                        $isValid = $modulusCalc->isValid();
	                
                        if ($isValid) {
                            $passedModulusChecksCount++;
                        }
	                }
                    else {
                        // Sometimes the ModulusException, when set, merely advises that the check should
                        // not proceed and should be assumed as valid.
                        if (!$exception->isCheckRequired($modulusCalc))
                        {
                            $passedModulusChecksCount++;
                            continue;
                        }
        
                        $modulusCalc = $exception->applyPreCheckModifications($modulusCalc);
                        $modulusCalc->calculateTotal();
                        $modulusCalc = $exception->applyPostCheckModifications($modulusCalc);
                        
                        if ($exception->isValidatedByException()) {
                            $isValid = $exception->isValid($modulusCalc);
                        }
                        else {
                            $isValid = $modulusCalc->isValid();
                        }
                        
                        if ($isValid) {
                            $passedModulusChecksCount++;
                        }
                        
                        if (!$exception->isSubsequentChecksRequired($isValid)) {
                            break;
                        }
                    }
                }
            }
        }
        
        //Determine at last if the account number is valid or not.
        $isValid = false;

        if ($modulusChecksCount == 0) {
            //Unable to validate - no checks performed.
            $isValid = false;
        }
        else if ($modulusChecksCount == $passedModulusChecksCount) {
            //Each modulus test has passed.
            $isValid = true;
        }
        else {
            if ($passedModulusChecksCount > 0) {
                if (!empty($exception)) {
                    if ($exception->isOneValidCheckEnough()) {
                        $isValid = true;
                    }
                }
            }
        }
        
        return $isValid;
    }
    
    
    /**
     * Converts non-standard bank account numbers into 8 digit account numbers.
     * 
     * Built according to the rules specified by VocaLink.
     *
     * @param mixed $sortCode
     * The sortcode. Should be formatted. Can be string or integer.
     *
     * @param mixed $accountNumber
     * The account number. Should be formatted. Can be string or integer.
     *
     * @return array
     * Returns an associative array containing the modified sortcode and account number.
     * Retrieve the sortcode using 'sortCode' as the index key, and the account
     * number using 'accountNumber' as the index key.
     */
    protected function _getConvertedAccountIdentifiers($sortCode, $accountNumber) 
    {

        $sortCodeList = preg_split('//', $sortCode, -1, PREG_SPLIT_NO_EMPTY);
        $accountNumberList = preg_split('//', $accountNumber, -1, PREG_SPLIT_NO_EMPTY);

        if (count($accountNumberList) == 8) {
            
            //Account number is normal, simply return it.
            return array('sortCode' => $sortCode, 'accountNumber' => $accountNumber);
        }
        
        
        //Account number is non-standard, which will require modifications. Get the
        //branch details so that the specific account number modifications can be identified.
        $branchDetail = $this->getBranchDetail($sortCode);

        if (count($accountNumberList) == 10) {
            if (preg_match("/national westminster/i", $branchDetail->bankNameFull)) {
        
                //Use the last eight digits only. If there is a hyphen in the account number
                //between the second and third numbers this should be ignored.
                array_shift($accountNumberList);
                array_shift($accountNumberList);
            } else if (preg_match(
            	"/co-operative/i", $branchDetail->bankNameFull
            )) {
            
                //Use the first eight digits only.
                array_pop($accountNumberList);
                array_pop($accountNumberList);
            } else {
                
                throw new Zend_Exception('Unknown 10 digit account number');
            }
        } else if (count($accountNumberList) == 9) {
            
            if (preg_match("/alliance/i", $branchDetail->bankNameFull) ||
            	preg_match("/santander/i", $branchDetail->bankNameFull)) {
                
                //Replace the last digit of the sorting code with the first 
                //digit of the account number, then use the last eight 
                //digits of the account number only.
                $sortCodeList[5] = $accountNumberList[0];
                $accountNumberList = array_shift($accountNumberList);
            } else {
                
                throw new Zend_Exception('Unknown 9 digit account number');
            }
        }
        /*
        
        THIS IS ALREADY HANDLED BY Bank::formatAccountNumber
        
        else if(count($accountNumberList) == 7) {
        
            //Prefix account number with a 0.
            array_unshift($accountNumberList, 0);
        }
        else if(count($accountNumberList) == 6) {
        
            //Prefix account number with 00.
            array_unshift($accountNumberList, 0);
            array_unshift($accountNumberList, 0);
        }
        */
        
        //Merge the lists into sortcode and accountnumber, then return.
        $sortCode = '';
        for ($i = 0; $i < count($sortCodeList); $i++) {
            
            $sortCode .= $sortCodeList[$i];
        }        
        
        $accountNumber = '';
        for ($i = 0; $i < count($accountNumberList); $i++) {
            
            $accountNumber .= $accountNumberList[$i];
        }
        
        return array('sortCode' => $sortCode, 'accountNumber' => $accountNumber);
    }
}
