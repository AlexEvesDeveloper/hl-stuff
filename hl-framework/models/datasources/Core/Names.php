<?php

/**
* Model definition for the name_details datasource.
*/
class Datasource_Core_Names extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'name_details';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Inserts a new, empty Name into the datasource and returns a corresponding object.
     *
	 * @return Model_Core_Name
	 * Encapsulates the details of the newly inserted Name.
     */
    public function createName() {    
    
        $nameId = $this->insert(array());
        if (empty($nameId)) {
            
            // Failed insertion
            Application_Core_Logger::log("Can't create record in table {$this->_name}", 'error');
            $returnVal = null;
        }
        else {
         
            $name = new Model_Core_Name();
            $name->id = $nameId;
            $returnVal = $name;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Updates an existing Name.
     *
     * @param Model_Core_Name
     * The Name details to update in the datasource.
     *
     * @return void
     */
    public function updateName($name) {
        
        if(empty($name)) {
            
            return;
        }
        
        $data = array(
            'title' => $name->title,
            'first_name' => $name->firstName,
            'middle_name' => $name->middleName,
            'last_name' => $name->lastName,
            'maiden_name' => $name->maidenName
        );

        $where = $this->quoteInto('id = ?', $name->id);
        $this->update($data, $where);
    }
    
    
    /**
     * Returns an existing Name.
     *
     * @param integer $nameId
     * The unique Name identifier.
     *
     * @return mixed
     * A Model_Core_Name encapsulating the Name details, or null if
     * the Name cannot be found.
     */
    public function getById($nameId) {
        
        if(empty($nameId)) {
            
            return null;
        }
        
        
        $select = $this->select();
        $select->where('id = ?', $nameId);
        $nameRow = $this->fetchRow($select);
        
        if(empty($nameRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find Name.');
            $returnVal = null;
        }
        else {
            
            $name = new Model_Core_Name();
            $name->id = $nameRow->id;
            $name->title = $nameRow->title;
            $name->firstName = $nameRow->first_name;
            $name->middleName = $nameRow->middle_name;
            $name->lastName = $nameRow->last_name;
            $name->maidenName = $nameRow->maiden_name;
            $returnVal = $name;
        }
        
        return $returnVal;
    }
}

?>