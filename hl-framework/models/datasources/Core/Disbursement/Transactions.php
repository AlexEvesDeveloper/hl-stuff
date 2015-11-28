<?php

/**
* Model definition for the Transactions Table
* 
*/
class Datasource_Core_Disbursement_Transactions extends Zend_Db_Table_Multidb
    {
    
    protected $_name = 'transactions';
    protected $_primary = 'trans_id';    
    protected $_multidb = 'db_legacy_homeletDW';
    
    
    /**
     * save details saves a single transaction to the homeletDW.transactions table
     * @param array Array data to be saverd to the table
     */
    public function saveDetails($data){
	 
        $dataToInsert = array(
	       'paymentrefno'   => $data['paymentrefno'],
		   'policynumber'   => $data['policynumber'],
		   'paymentdate'    => $data['paymentdate'],
           'onComputer'     => date("Y-m-d H:m:s"),
		   'amount'         => $data['amount'],
		   'handlingcharge' => $data['handlingcharge'],
		   'csuid'          => $data['csuid'],
		   'months'         => $data['months'],
		   'paymethod'      => $data['paymethod'],
		   'whitelabelID'   => $data['whitelabelID'],
		   'agentschemeno'  => $data['agentschemeno'],
           'premier'        => (isset($data['premier'])) ? $data['premier'] : '',
           'salesman'       => $data['salesman'],
           'riskarea'       => $data['riskarea'],
           'type'           => (isset($data['type'])) ? $data['type'] : 'payment',
		   'isNewBusiness'  => $data['isNewBusiness'],
		   'disbursed'      => (isset($data['disbursed'])) ? $data['disbursed'] : 'yes'
		);
       return $this->insert($dataToInsert);
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
    public function updateTranForTenant($disbursement) {
        
		               
        $updatedata = array(
		    'grosspremium'       =>  $disbursement->_grosspremium,
		    'agentcommission'    =>  $disbursement->_agentcommission,
            'dis_t_contents'     =>  $disbursement->_disTcontents,
            'ipt_t_contents'     =>  $disbursement->_iptTcontents,
            'prem_t_contents'    =>  $disbursement->_premTcontents,
            'sum_t_contents'     =>  $disbursement->_sumTcontents,
            'dis_t_bikes'        =>  $disbursement->_disTpedel,
            'ipt_t_bikes'        =>  $disbursement->_iptTpedel,
            'prem_t_bikes'       =>  $disbursement->_premTpedel,
            'sum_t_bikes'        =>  $disbursement->_sumTpedel,
            'dis_t_possessions'  =>  $disbursement->_disTposs,
            'ipt_t_possessions'  =>  $disbursement->_iptTposs,
            'prem_t_possessions' =>  $disbursement->_premTposs,
            'sum_t_possessions'  =>  $disbursement->_sumTposs,
            'hpc'                =>  $disbursement->_hpc
         
        );

        $where = $this->quoteInto('trans_id = ?', $disbursement->_transIDDW);
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
            'prem_l_buildings'   				=>  $disbursement->_premLbuilding,
            'sum_l_buildings'   				=>  $disbursement->_sumLbuilding,
            'dis_l_buildingsaccidentaldamage'	=>  $disbursement->_disLBA,
            'ipt_l_buildingsaccidentaldamage'	=>	$disbursement->_iptLBA,
            'prem_l_buildingsaccidentaldamage'	=>	$disbursement->_premLBA,
            'sum_l_buildingsaccidentaldamage'	=>	$disbursement->_sumLBA,
            'dis_l_contents'    				=>  $disbursement->_disLcontents,
            'ipt_l_contents'     				=>  $disbursement->_iptLcontents,
            'prem_l_contents'    				=>  $disbursement->_premLcontents,
            'sum_l_contents'     				=>  $disbursement->_sumLcontents,
            'dis_l_contentsaccidentaldamage'	=>	$disbursement->_disLCA,
            'ipt_l_contentsaccidentaldamage'	=>	$disbursement->_iptLCA,
            'prem_l_contentsaccidentaldamage'	=>	$disbursement->_premLCA,
            'sum_l_contentsaccidentaldamage'	=>  $disbursement->_sumLCA,
            'dis_l_legal'						=>	$disbursement->_disLG,
         	'ipt_l_legal'						=>	$disbursement->_iptLG,
         	'prem_l_legal'						=>	$disbursement->_premLG,
         	'sum_l_legal'						=>	$disbursement->_sumLG,
            'dis_l_rent'						=>	$disbursement->_disLR,
         	'ipt_l_rent'						=>	$disbursement->_iptLR,
         	'prem_l_rent'						=>	$disbursement->_premLR,
         	'sum_l_rent'						=>	$disbursement->_sumLR,
            'dis_eas'							=>	$disbursement->_disES,
         	'ipt_eas'							=>	$disbursement->_iptES,
         	'prem_eas'							=>	$disbursement->_premES,
         	'sum_eas'							=>	$disbursement->_sumES,
            'dis_pi'							=>	$disbursement->_disEB,
         	'ipt_pi'							=>	$disbursement->_iptEB,
         	'prem_pi'							=>	$disbursement->_premEB,
         	'sum_pi'							=>	$disbursement->_sumEB,            
            'hpc'               				=>  $disbursement->_hpc
         
        );

        $where = $this->quoteInto('trans_id = ?', $disbursement->_transIDDW);
        $this->update($updatedata, $where);
}
    
    
}
?>