<?php

/**
* Model definition for the newtransactions Table
* 
*/
class Datasource_Core_Disbursement_PaymentTransaction extends Zend_Db_Table_Multidb
    {
    
    protected $_name = 'paymentTransaction';
    protected $_primary = 'id';    
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * save details saves a single transaction to the paymentTransaction table
     * @param array Array data to be saverd to the table
     */
    public function saveDetails($data){
	 
        $dataToInsert = array(
           'trans_id'       => $data['trans_id'],    
	       'policynumber'	=> $data['policynumber'],
           'agentschemeno'	=> $data['agentschemeno'],
		   'csuid' 			=> $data['csuid'],
           'whitelabelID' 	=> $data['whitelabelID'],
           'policyTermID' 	=> $data['policyTermID'],
           'paymethod' 		=> $data['paymethod'],
           'paymentdate' 	=> $data['paymentdate'],
		   'months'			=> $data['months'],
		   'amount' 		=> $data['amount'],
		   'handlingcharge' => $data['handlingcharge'],
		   'policyname'		=> $data['policyname']
        );
       return $this->insert($dataToInsert);
    }
    
    
     /**
     * Updates an existing transaction record in the paymentTransaction table.
     *
     * @param an object of Manager_Core_Disbursement to be saverd to the table
     * An up-to-date disbursement record that will be used to update the corresponding
     * record in the data store.
     *
     * @return void
     */
    public function updatePaymentTransaction($disbursement) {
       		
                
        $updatedata = array(
		    'grosspremium'      =>  $disbursement->_grosspremium,
            'premium'           =>  $disbursement->_policypremium,
		    'agentcomm'         =>  $disbursement->_agentcommission,
			'introcomm'         =>  $disbursement->_introComm,
            'netpremium'        =>  $disbursement->_policynetprem,
            'ipt'               =>  $disbursement->_policyIPT,
            'income'            =>  $disbursement->_income,
            'banked'            =>  $disbursement->_banked
         
        );

        $where = $this->quoteInto('id = ?', $disbursement->_ptranID);
        $this->update($updatedata, $where);
    }
    
    
}
?>
