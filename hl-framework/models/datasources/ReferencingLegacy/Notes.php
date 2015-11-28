<?php

/**
* Model definition for the Notes datasource.
*/
class Datasource_ReferencingLegacy_Notes extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'notes';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Retrieves the notes for a specific reference.
     * 
     * @param $enquiryId
     * The unique external enquiry identifier.
     * 
     * @return mixed
     * Returns an array of notes, if any found. Else returns null.
     */
    public function getNotes($enquiryId) {
    	
    	$select = $this->select();
        $select->where('refno = ?', $enquiryId);
        $rowSet = $this->fetchAll($select);
        
        $notesArray = array();
        if(count($rowSet) > 0) {
        	
        	foreach($rowSet as $currentRow) {
        		
        		$record = array();
        		$record['text'] = $currentRow->text;
        		$record['textId'] = $currentRow->textId;
        		$notesArray[] = $record;
        	}
        }
        
        if(empty($notesArray)) {
        	
        	$returnVal = null;
        }
        else {
        	
        	$returnVal = $notesArray;
        }
        return $returnVal;
    }
}

?>