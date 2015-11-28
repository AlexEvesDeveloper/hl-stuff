<?php

/**
 * This datasource accesses the testPolicy table.
 *
 * All policies in this table should be considered TEST policies.
 */
class Datasource_Insurance_Policy_TestPolicy extends Zend_Db_Table_Multidb {
	
    protected $_name = 'policyTest';
    protected $_id = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * insertTestPolicy inserts a record into the policyTest table
     * @param $data Model_Core_TestPolicy The Data to be inserted
     * @return void
     **/
    public function insertTestPolicy($data){
        $policyNumber = preg_replace('/^Q/', 'P', $data->policynumber);
        $dataToInsert = array(
            'policynumber' => $policyNumber,
            'agentschemeno' => $data->agentschemeno,
            'csuid' => $data->csuid,
            'isTestPolicy' => $data->isTestPolicy
        );
        $this->insert($dataToInsert);
    }
    
    /**
     * Remove a testPolicy from the policyTest table
     * @param $String $policynumber Policy Number of the record to be removed
     * @return void
     *
     **/
    public function remove($policynumber){
        $where = $this->quoteInto('policynumber = ?', $policynumber);
        $this->delete($where);
    }
    
    /**
     * Fetches a test policy record
     *  @param $policyNumber String Policy Number of the record to be retrieved
     *
     **/
    public function fetchByPolicyNumber($policyNumber){
        $testPolicyData = new Model_Core_TestPolicy();
        $testPolicyData = $isTester->fetchByPolicyNumber($policyNumber);
        
    }
   
}
?>