<?php

/**
* Model definition for the newtransactions Table
* 
*/
class Datasource_Core_Disbursement_Newtransactions extends Zend_Db_Table_Multidb
    {
    
    protected $_name = 'newtransactions';
    protected $_primary = 'trans_id';    
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * save details saves a single transaction to the newtransactions table
     * @param array Array data to be saverd to the table
     */
    public function saveDetails($data){
	 
        $dataToInsert = array(
	       'paymentrefno'	=> $data['paymentrefno'],
		   'policynumber'	=> $data['policynumber'],
		   'paymentdate' 	=> $data['paymentdate'],
		   'amount' 		=> $data['amount'],
		   'handlingcharge' => $data['handlingcharge'],
		   'csuid' 			=> $data['csuid'],
		   'months'			=> $data['months'],
		   'paymethod' 		=> $data['paymethod'],
		   'whitelabelID' 	=> $data['whitelabelID'],
		   'agentschemeno'	=> $data['agentschemeno'],
           'premier' 		=> (isset($data['premier'])) ? $data['premier'] : '',
           'salesman'		=> $data['salesman'],
           'riskarea' 		=> $data['riskarea'],
		   'riskareab'		=> (isset($data['riskareab'])) ? $data['riskareab'] : '',
           'isNewBusiness' 	=> $data['isNewBusiness'],
		   'isPaidnet' 		=> (isset($data['isPaidnet'])) ? $data['isPaidnet'] : 'no',
		   'disbprofileid' 	=>(isset($data['disbprofileid'])) ? $data['disbprofileid'] : 0,
		   'paidfor' 		=> (isset($data['paidfor'])) ? $data['paidfor'] : '',
	       'manualRepair' 	=> (isset($data['manualRepair'])) ? $data['manualRepair'] : ''	
		);
       return $this->insert($dataToInsert);
    }
    
    
     /**
     * Updates an existing transaction record in the newtransactions table.
     *
     * @param an object of Manager_Core_Disbursement data to be saverd to the table
     * An up-to-date disbursement record that will be used to update the corresponding
     * record in the data store.
     *
     * @return void
     */
    public function updateNewTranForTenant($disbursement) {
      	                
        $updatedata = array(
		    'grosspremium'      =>  $disbursement->_grosspremium,
		    'agentcommission'   =>  $disbursement->_agentcommission,
			'dis_t_contents'    =>  $disbursement->_disTcontents,
            'ipt_t_contents'    =>  $disbursement->_iptTcontents,
            'dis_t_bikes'       =>  $disbursement->_disTpedel,
            'ipt_t_bikes'       =>  $disbursement->_iptTpedel,
            'dis_t_possessions' =>  $disbursement->_disTposs,
            'ipt_t_possessions' =>  $disbursement->_iptTposs,
            'hpc'               =>  $disbursement->_hpc
         
        );

        $where = $this->quoteInto('trans_id = ?', $disbursement->_transID);
        $this->update($updatedata, $where);
    }
    
     /**
     * Updates an existing transaction record in the homeletDW.transactions table.
     *
     * @param an object of Manager_Core_Disbursement data to be saverd to the table
     * An up-to-date disbursement record that will be used to update the corresponding
     * record in the data store.
     *
     * @return void
     */
    public function updateTranForLandlord($disbursement) {
    
		
         $updatedata = array(
		    'grosspremium'       				=>  $disbursement->_grosspremium,
		    'agentcommission'    				=>  $disbursement->_agentcommission,
            'dis_l_buildings'	 				=>  $disbursement->_disLbuilding,
            'ipt_l_buildings'	 				=>	$disbursement->_iptLbuilding,
            'dis_l_buildingsaccidentaldamage'	=>  $disbursement->_disLBA,
            'ipt_l_buildingsaccidentaldamage'	=>	$disbursement->_iptLBA,
            'dis_l_contents'    				=>  $disbursement->_disLcontents,
            'ipt_l_contents'     				=>  $disbursement->_iptLcontents,
            'dis_l_contentsaccidentaldamage'	=>	$disbursement->_disLCA,
            'ipt_l_contentsaccidentaldamage'	=>	$disbursement->_iptLCA,
            'dis_l_legal'						=>	$disbursement->_disLG,
         	'ipt_l_legal'						=>	$disbursement->_iptLG,
         	'dis_l_rent'						=>	$disbursement->_disLR,
         	'ipt_l_rent'						=>	$disbursement->_iptLR,
         	'dis_eas'							=>	$disbursement->_disES,
         	'ipt_eas'							=>	$disbursement->_iptES,
         	'dis_pi'							=>	$disbursement->_disEB,
         	'ipt_pi'							=>	$disbursement->_iptEB,
         	'hpc'              					=>  $disbursement->_hpc
         
        );

        $where = $this->quoteInto('trans_id = ?', $disbursement->_transID);
        $this->update($updatedata, $where);
}
    
    
    
}
?>
