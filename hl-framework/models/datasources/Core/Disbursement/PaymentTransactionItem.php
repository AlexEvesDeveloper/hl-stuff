<?php

/**
* Model definition for the paymentTransactionItem Table
* 
*/
class Datasource_Core_Disbursement_PaymentTransactionItem extends Zend_Db_Table_Multidb
    {
    
    protected $_name = 'paymentTransactionItem';
    protected $_primary = 'id';    
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * save details saves a single transaction to the paymentTransactionItem table
     * @param an object of Manager_Core_Disbursement to be saverd to the table
     */
    public function saveDetails($disbursement){
        
	  
        $dataToInsert =array(
            'paymentTransactionID' => $disbursement->_ptranID,
            'sumInsured'           => $disbursement->_sumInsOption,
		    'grosspremium'         => $disbursement->_grossOption,
            'premium'              => $disbursement->_premOption,
            'netpremium'           => $disbursement->_netOption,
            'ipt'                  => $disbursement->_iptOption,
		    'agentcomm'            => $disbursement->_aCommOption,
            'introcomm'            => $disbursement->_introCommOption,
			'policyOptionID'       => $disbursement->_optionID
         
        );
        $this->insert($dataToInsert);
    }
    
    
      
    
}
?>
