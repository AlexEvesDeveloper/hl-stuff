<?php

/**
 * Model definition for the bank sortcode/account multiplier datasource.
 */
class Datasource_Core_Bank_SortCodeAccountMultiplier  extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_homelet_insurance_com';
    protected $_name = 'sortCodeModulus';
    protected $_primary = 'startSortCode';
    
    
    /**
     * Returns a Model_Core_Bank_SortCodeAccountMultipler object corresponding to the sortcode passed in.
     *
     * @param string $sortCode
     * Identifies the sortcode/account multiplier to retrieve.
     * 
     * @return mixed
     * A Model_Core_Bank_SortCodeAccountMultipler object, or null if no matches are found.
     */
    public function getSortCodeAccountMultiplier($sortCode) {

        $select = $this->select()
                  ->from($this->_name)
                  ->where('startSortCode <= ?', $sortCode)
                  ->where('endSortCode >= ?', $sortCode)
                  ->order('exceptionCode ASC');
                  
        $result = $this->fetchAll($select);

        $multiplierGroup = new Model_Core_Bank_SortCodeAccountMultiplerGroup();
        $returnArray = array($multiplierGroup);
        foreach($result as $row) {
            
            $currentStartSortCode = $row->startSortCode;
            $currentEndSortCode = $row->endSortCode;
            
            if(!empty($previousStartSortCode) && !empty($previousEndSortCode)) {
            
                if(($currentStartSortCode != $previousStartSortCode) || ($currentEndSortCode != $previousEndSortCode)) {
                    
                    $multiplierGroup = new Model_Core_Bank_SortCodeAccountMultiplerGroup();
                    $returnArray[] = $multiplierGroup;
                }
            }
            
            $multiplier = new Model_Core_Bank_SortCodeAccountMultipler();
            
            $multiplier->startSortCode = $currentStartSortCode;
            $multiplier->endSortCode = $currentEndSortCode;
            $multiplier->modulusCheck = $row->modulusCheck;
            $multiplier->sortCodeU = $row->sortCodeU;
            $multiplier->sortCodeV = $row->sortCodeV;
            $multiplier->sortCodeW = $row->sortCodeW;
            $multiplier->sortCodeX = $row->sortCodeX;
            $multiplier->sortCodeY = $row->sortCodeY;
            $multiplier->sortCodeZ = $row->sortCodeZ;
            $multiplier->accountNumberA = $row->accountNumberA;
            $multiplier->accountNumberB = $row->accountNumberB;
            $multiplier->accountNumberC = $row->accountNumberC;
            $multiplier->accountNumberD = $row->accountNumberD;
            $multiplier->accountNumberE = $row->accountNumberE;
            $multiplier->accountNumberF = $row->accountNumberF;
            $multiplier->accountNumberG = $row->accountNumberG;
            $multiplier->accountNumberH = $row->accountNumberH;
            $multiplier->exceptionCode = $row->exceptionCode;
            
            $multiplierGroup->multipliers[] = $multiplier;
            
            $previousStartSortCode = $currentStartSortCode;
            $previousEndSortCode = $currentEndSortCode;
        }
        
        
        if(empty($returnArray)) {
            
            $returnVal = null;
        }
        else {
            $returnVal = $returnArray;
        }
        
        return $returnVal;
    }
}

?>