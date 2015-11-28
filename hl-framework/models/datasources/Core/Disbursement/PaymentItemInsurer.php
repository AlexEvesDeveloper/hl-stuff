<?php

/**
* Model definition for the paymentItemInsurer Table
* 
*/
class Datasource_Core_Disbursement_PaymentItemInsurer extends Zend_Db_Table_Multidb
    {
    
    protected $_name = 'paymentItemInsurer';
    protected $_primary = 'id';    
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * save details saves a single transaction to the paymentItemInsurer table
     * @param an object of Manager_Core_Disbursement to be saverd to the table
     */
    public function saveDetails($disbursement){
       
	 
        $dataToInsert =array(
            'paymentTransactionID' => $disbursement->_ptranID,
            'sumInsured'           => $disbursement->_sumInsOption,
		    'grosspremium'         => $disbursement->_grossOptionIns,
            'premium'              => $disbursement->_premOptionIns,
            'netpremium'           => $disbursement->_netOptionIns,
            'ipt'                  => $disbursement->_iptOptionIns,
		    'policyOptionID'       => $disbursement->_optionID,
            'insurerID'            => $disbursement->_insurerID
        );
        $this->insert($dataToInsert);
    }
    
    
      
    
}
?>
