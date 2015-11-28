<?php

/**
 * Encapsulates the referencing progress business logic.
 * 
 * All access to the reference progress datasources should be through this class.
 */
class Manager_Referencing_Progress {

    protected $_progressDatasource;
	
	protected function _loadSources() {
    	
    	if(empty($this->_progressDatasource)) {
    		
			$this->_progressDatasource = new Datasource_Referencing_Progress();
		}
    }
	
	public function createNewProgress($referenceId) {
		
		$progress = new Model_Referencing_Progress();
		$progress->referenceId = $referenceId;
		$progress->items = array();
		return $progress;
	}
	
	public function createNewProgressItem($itemVariable, $itemState) {
		
		$progressItem = new Model_Referencing_ProgressItem();
		$progressItem->itemVariable = $itemVariable;
		$progressItem->itemState = $itemState;
		$progressItem->itemCompletionTimestamp = Zend_Date::now();
		return $progressItem;
	}
    
    /**
     * Save referencing progress details.
     *
     * @param Model_Referencing_Progress $progress
     * Encapsulates the progress details associated with a reference.
     * 
     * @return void
     */
    public function save($progress) {
		
		$this->_loadSources();
    	$this->_progressDatasource->upsertProgress($progress);
    }
    
    /**
     * Retrieves the specified progress details.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * The progress details, encapsulated in a Model_Referencing_Progress
     * object, or null if the progress cannot be found.
     */
    public function retrieve($referenceId) {
		
	    $this->_loadSources();
	    return $this->_progressDatasource->getByReferenceId($referenceId);
    }
    
    /**
     * Retrieves specific progress items from a Model_Referencing_Progress.
     * 
     * Utility method that allows calling code to retrieve a particular progress
     * item from the set of progress items stored against a reference.
     *
     * @param Model_Referencing_Progress $progress 
     * The progress object associated with a reference.
     *
     * @param integer $progressItemVariable
     * The item variable to look for. MUST correspond to one of the constants
     * exposed by Model_Referencing_ProgressItemVariables.
     *
     * @return mixed
     * Returns null if the item cannot be found, else returns the matching
     * Model_Referencing_ProgressItem.
     */
    public static function findSpecificProgressItem($progress, $progressItemVariable) {
	
        $returnVal = null;
		if(!empty($progress)) {
			
			foreach($progress->items as $currentItem) {
	
				if ($currentItem->itemVariable == $progressItemVariable) {
	
					$returnVal = $currentItem;
					break;
				}
			}
		}
        return $returnVal;
    }
}