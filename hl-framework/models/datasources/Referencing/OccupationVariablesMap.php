<?php

/**
* Model definition for the OccupationVariablesMap datasource.
*/
class Datasource_Referencing_OccupationVariablesMap extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes.
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'occupation_variables_map';
    protected $_primary = array('occupation_id', 'variable_id');
    /**#@-*/
    
    
    /**
     * Inserts or updates an existing occupation variable in the datasource.
     *
     * @param integer $occupationId
     * Identifies the Occupation who's variable we are to upsert.
     *
     * @param integer $variableId
     * Identifies the occupation variable to upsert.
     *
     * @param string $value
     * The value of the occupation variable.
     *
     * @return void.
     */
    public function upsertVariable($occupationId, $variableId, $value) {
    
        //First identify if a value for this $variableId has already been set for the occupation.
        $select = $this->select();
        $where = $this->quoteInto('occupation_id = ? AND variable_id = ? ', $occupationId, $variableId);
        $select->where($where);
        $variableRow = $this->fetchRow($select);
        
        if(!empty($variableRow)) {
            
            //A value for the $variableId has already been set for the occupation. Delete this,
            //so that the new can be inserted.
            $this->deleteVariable($occupationId, $variableId);
        }
        
        
        //Insert new
        $data = array(
            'occupation_id' => $occupationId,
            'variable_id' => $variableId,
            'value' => $value
        );
        $this->insert($data);
    }
    
    
    /**
     * Retrieves occupational variables linked to an occupation.
     *
     * @param integer $occupationId
     * Identifies the occupation variables to retrieve.
     * 
     * @return array
     * An array of the occupation variables relating to the Occupation identified
     * by the $occupationId passed in.
     */
    public function getVariables($occupationId) {
        
        $select = $this->select();
        $where = $this->quoteInto('occupation_id = ? ', $occupationId);
        $select->where($where);
        $variableRows = $this->fetchAll($select);
        
        if(count($variableRows) == 0) {
            
            //Return the occupation unchanged.
            return null;
        }
        
        
        //Update the $occupation object with the variables, and return.
        $variablesArray = array();
        foreach($variableRows as $row) {
            
            switch($row->variable_id) {
                
                case Model_Referencing_OccupationVariables::ENDDATE:
                    $variablesArray[Model_Referencing_OccupationVariables::ENDDATE] = $row->value;
                    break;
                case Model_Referencing_OccupationVariables::POSITION:
                    $variablesArray[Model_Referencing_OccupationVariables::POSITION] = $row->value;
                    break;
                case Model_Referencing_OccupationVariables::PAYROLL_NUMBER:
                    $variablesArray[Model_Referencing_OccupationVariables::PAYROLL_NUMBER] = $row->value;
                    break;
            }
        }
        return $variablesArray;
    }
    
    
    /**
     * Deletes an existing occupation variable.
     *
     * @param integer $occupationId
     * Identifies the Occupation who's variable we are to delete.
     *
     * @param integer $variableId
     * Identifies the occupation variable to delete.
     *
     * @return void
     */
    public function deleteVariable($occupationId, $variableId) {
        
        $where = $this->quoteInto('occupation_id = ? AND variable_id = ? ', $occupationId, $variableId);
        $this->delete($where);
    }
    
    
    /**
     * Deletes all occupation variables stored against a particular occupation.
     *
     * @param integer $occupationId
     * Identifies the linked occupation variables to delete.
     *
     * @return void
     */
    public function deleteAllVariables($occupationId) {
        
        $where = $this->quoteInto('occupation_id = ? ', $occupationId);
        $this->delete($where);
    }
}

?>