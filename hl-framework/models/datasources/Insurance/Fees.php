<?php

/**
 * Datasource for the fees table. 
 */
class Datasource_Insurance_Fees extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_homelet';
	protected $_name = 'fees';
    protected $_primary = 'id';
    
    /**
     * Get all fees related to a particular rate set
 *
     * @param int dealGroupID The ID of the deal group you want
 */
    public function getByAgentRateSetID($dealGroupID) {
    	$select = $this->select()
    				   ->setIntegrityCheck(false)
    				   ->from($this->_name)
    				   ->joinInner('fee_types', 'fee_types.id = fees.fee_type_id', array('fee_name' => 'fee'))
    				   ->where('agent_rate_deal_group_id = ?', $dealGroupID);
		
    	$feeRows = $this->fetchAll($select);
    	$fees = array();
    	foreach($feeRows as $feeRow) {
    		$fees[$feeRow->fee_name] = (double)$feeRow->fee;
    	}
    	
    	return $fees;
    }
}
?>