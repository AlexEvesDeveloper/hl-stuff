<?php

/**
* Model definition for the referencing progress datasource.
*/
class Datasource_Referencing_Progress extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'progress_items';
    protected $_primary = array('reference_id', 'progress_variable_id');
    
    /**
     * Persists the progress items associated with a reference.
     * 
     * @param Model_Referencing_Progress $progress
     * Encapsulates the progress items details associated with a reference.
     * 
     * @return void
     */
    public function upsertProgress($progress) {
        
        if(empty($progress)) {

        	return;
        }
    	
    	//Delete existing progress items, as its easier than checking for existing items
        //then updating them.
        $where = $this->quoteInto('reference_id = ? ', $progress->referenceId);
        $this->delete($where);
        
    	//Insert the new.
    	foreach($progress->items as $currentItem) {
    		
			$data = array(
				'reference_id' => $progress->referenceId,
				'progress_variable_id' => $currentItem->itemVariable,
				'progress_state_id' => $currentItem->itemState,
				'timestamp' => $currentItem->itemCompletionTimestamp->toString(Zend_Date::ISO_8601)
	        );
	        
	        $this->insert($data);
    	}
    }    
    
    /**
     * Retrieves the progress items associated with a reference.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return mixed
     * A Model_Referencing_Progress if progress items found, else returns
     * null.
     */
    public function getByReferenceId($referenceId) {
        
        $select = $this->select();
		$select->where('reference_id = ?', $referenceId);
		$progressRows = $this->fetchAll($select);
		
		if(count($progressRows) > 0){
			
			$progress = new Model_Referencing_Progress();
			$progress->referenceId = $referenceId;
			$progress->items = array();
			
			foreach($progressRows as $currentRow) {
				
				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = $currentRow['progress_variable_id'];
				$progressItem->itemState = $currentRow['progress_state_id'];
				$progressItem->itemCompletionTimestamp = new Zend_Date($currentRow['timestamp'], Zend_Date::ISO_8601);
				
				$progress->items[] = $progressItem;
			}
			
			$returnVal = $progress;
		}
		else {
			
			$returnVal = null;
		}
		
		return $returnVal;
    }
}

?>