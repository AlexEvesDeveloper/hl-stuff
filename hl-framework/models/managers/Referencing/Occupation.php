<?php

/**
 * Occupation manager class.
 */
class Manager_Referencing_Occupation {
	
	/**#@+
     * Internal datasource references.
     */
	protected $_occupationDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {
        
        $this->_occupationDatasource = new Datasource_Referencing_Occupations();
    }
	
	
	/**
     * Retrieve the occupations applicable to a reference.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return mixed
     * An array of Model_Referencing_Occupation objects, or null if no
     * occupations found.
     */
    public function retrieve($referenceId) {
		
    	return $this->_occupationDatasource->getByReferenceId($referenceId);
    }
	
	
	/**
	 * Utility method that allows calling code to retrieve a particular occupation from a set of occupations stored against a reference subject.
	 *
	 * @param array $occupationsArray
	 * The array of occupations.
	 *
	 * @param integer $chronology
	 * The occupation chronology. Must correspond to one of the consts exposed by the
	 * Model_Referencing_OccupationChronology class.
	 *
	 * @param integer $importance
	 * The occupation importance. Must correspond to one of the consts exposed by the
	 * Model_Referencing_OccupationImportance class.
	 *
	 * @return mixed
	 * Returns null if the occupation cannot be found. Else returns a Model_Referencing_Occupation
	 * from the $occupationsArray matching the $chronology and $classification specified in the
	 * arguments.
	 */
	public function findSpecificOccupation($occupationsArray, $chronology, $importance) {
		
		if(empty($occupationsArray)) {
			
			return null;
		}
		
		
		$returnVal = null;
		foreach($occupationsArray as $occupation) {
			
			if($occupation->chronology == $chronology) {
			
				if($occupation->importance == $importance) {
				
					$returnVal = $occupation;
					break;
				}
			}
		}
		
		return $returnVal;
	}
	
	
	/**
     * Creates a new, empty occupation and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * The unique Reference identifier. This is used to link the new occupation
     * record to the Reference record.
     *
     * @param integer $chronology
     * Indicates the occupational chronology. Must corresond to one of the consts
     * exposed by the Model_Referencing_OccupationalChronology class.
     *
     * @param integer $importance
	 * Indicates the occupation importance. Must corresond to one of the consts
     * exposed by the Model_Referencing_OccupationImportance class.
     *
     * @return Model_Referencing_Occupation
     * Returns the newly created, empty Occupation.
     */
	public function createNewOccupation($referenceId, $chronology, $importance) {

		return $this->_occupationDatasource->createNewOccupation($referenceId, $chronology, $importance);
	}
	
	
	/**
     * Deletes an existing Occupation.
     *
     * @param Model_Referencing_Occupation
     * The Occupation to delete.
     *
     * @return void
     */
	public function deleteOccupation($occupation) {
		
		$this->_occupationDatasource->deleteOccupation($occupation);
	}
}

?>