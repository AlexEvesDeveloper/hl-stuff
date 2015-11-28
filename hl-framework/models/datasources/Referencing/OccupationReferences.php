<?php

/**
* Model definition for the occupational references datasource.
*/
class Datasource_Referencing_OccupationReferences extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'occupation_reference';
    protected $_primary = 'occupation_id';
    /**#@-*/
    
    
    public function __construct() {
    	
    	parent::__construct();
    	$this->_variablesDatasource = new Datasource_Referencing_OccupationReferenceVariablesMap();
    }
    
    
    public function upsertReference($occupationReference) {
        
        if(empty($occupationReference)) {
        	
        	//Nothing to do.
        	return;
        }
        
        //Delete old reference.
        $where = $this->quoteInto('occupation_id = ? ', $occupationReference->occupationId);
        $this->delete($where);
        
        //Insert the new reference.
        $data = array(
            'occupation_id' => $occupationReference->occupationId,
            'provision_type_id' => $occupationReference->provisionType,
            'submission_type_id' => $occupationReference->submissionType,
        	'is_acceptable' => $occupationReference->isAcceptable
        );
        $this->insert($data);
        
        //Delete the old occupation reference variables and insert the new.
        $this->_variablesDatasource->upsertVariables($occupationReference);
    }
    
    
    public function getByOccupationId($occupationId) {
			
		$select = $this->select();
		$select->where('occupation_id = ?', $occupationId);
		$occupationalReferenceRow = $this->fetchRow($select);

		if(empty($occupationalReferenceRow)) {
			
			$returnVal = null;
		}
		else {
			
			$occupationReference = new Model_Referencing_OccupationReference();
			$occupationReference->occupationId = $occupationId;
			$occupationReference->provisionType = $occupationalReferenceRow->provision_type_id;
			$occupationReference->submissionType = $occupationalReferenceRow->submission_type_id;
			$occupationReference->isAcceptable = ($occupationalReferenceRow->is_acceptable == 1) ? true : false;
			
			//Load up the reference variables.
			$occupationReference->variables = $this->_variablesDatasource->getVariables($occupationId);
			$returnVal = $occupationReference;
		}
		
		return $returnVal;
    }
}

?>