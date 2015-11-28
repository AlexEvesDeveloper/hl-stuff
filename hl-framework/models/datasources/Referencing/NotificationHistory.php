<?php

/**
* Model definition for the Notes datasource.
*/
class Datasource_Referencing_NotificationHistory extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'NotificationHistory';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Retrieves the notes for a specific Reference.
     * 
     * @param string $asn
     */
    public function getHistoryByASN($asn) {
    	
    	$select = $this->select();
        $select->where('agentschemeno = ?', $asn);
        $select->order('date DESC')->limit(10);
        $rowSet = $this->fetchAll($select);
        return $rowSet->toArray();
        
    }
}

?>