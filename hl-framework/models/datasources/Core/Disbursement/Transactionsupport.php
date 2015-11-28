<?php

/**
* Model definition for the Transactions Table
* 
*/
class Datasource_Core_Disbursement_Transactionsupport extends Zend_Db_Table_Multidb
    {
    
    protected $_name = 'transactionsupport';
    protected $_primary = 'trans_id';    
    protected $_multidb = 'db_legacy_homeletDW';
    
    
    /**
     * save details saves a single transaction to the homeletDW.transactions table
     * @param array Array data to be saverd to the table
     */
    public function saveDetails($data){
	 
        $dataToInsert = array(
           'trans_id'            => $data['trans_id'],
	       'customerTitle'       => $data['customerTitle'],
		   'customerFirstName'   => $data['customerFirstName'],
		   'customerLastName'    => $data['customerLastName'],
           'riskAddress1'        => $data['riskAddress1'],
		   'riskAddress2'        => $data['riskAddress2'],
		   'riskAddress3'        => $data['riskAddress3'],
		   'riskPostcode'        => $data['riskPostcode'],
		   'policytype'          => $data['policytype'],
		   'payby'               => $data['payby'],
		   'policyLength'        => $data['policyLength']
		);
       return $this->insert($dataToInsert);
    }
    
    
       
    
}
?>