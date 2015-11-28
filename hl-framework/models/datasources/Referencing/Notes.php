<?php

/**
* Model definition for the Notes datasource.
*/
class Datasource_Referencing_Notes extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'notes';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Retrieves the notes for a specific Reference.
     * 
     * @param string $externalId
     * The unique external Reference identifier.
     * 
     * @return mixed
     * Returns an array of notes, if any found. Else returns null.
     */
    public function getNotes($externalId) {
    	
    	$select = $this->select();
        $select->where('refno = ?', $externalId);
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