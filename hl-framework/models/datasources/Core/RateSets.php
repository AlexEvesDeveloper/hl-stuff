<?php
/**
* Model definition for the rates table
*
* @todo This is the new rates system - it's only partially complete. Needs finishing before use!
*/
class Datasource_Core_RateSets extends Zend_Db_Table_Multidb {
    protected $_name = 'rate_sets';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet';
    
    /**
     * This function will find the ID of a rate set based on the agents rate set and property risk area
     *
     * @param integer $agentRateSetID
     * @param integer $riskArea
     * @return integer
     */
    public function getID($agentRateSetID, $riskArea) {
        $select = $this->select()
                       ->where('agent_rate_set_id = ?', $agentRateSetID)
                       ->where('risk_area = ?', $riskArea);
        
        $result = $this->fetchRow($select);
        if ($result) {
            return $result->id;
        }
    }
    
}  
?>