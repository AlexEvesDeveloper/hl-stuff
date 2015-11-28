<?php

/**
 * Model definition for the sorting code substitution datasource.
 */
class Datasource_Core_Bank_SortCodeSubstitution  extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_homelet_insurance_com';
    protected $_name = 'sortCodeSubstitution';
    protected $_primary = 'sortCodeSubstitutionID';
    
    
    /**
     * Returns a Model_Core_Bank_SortCodeSubstitution object.
     *
     * @param string $sortCode
     * Identifies the corresponding sortcode substitution to retrieve.
     * 
     * @return mixed
     * A Model_Core_Bank_SortCodeSubstitution object, or null if no matches are found.
     */
    public function getSubstitution($sortCode) {
        
        $select = $this->select()
            ->from($this->_name)
            ->where('sortCodeToReplace = ?', $sortCode);
            
        $row = $this->fetchRow($select);
        if(!empty($row)) {
            
            $substitution = new Model_Core_Bank_SortCodeSubstitution();
            $substitution->sortCodeSubstitutionID = $row->sortCodeSubstitutionID;
            $substitution->sortCodeToReplace = $row->sortCodeToReplace;
            $substitution->substituteSortCode = $row->substituteSortCode;
        }
        
        if(empty($substitution)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $substitution;
        }
        
        return $returnVal;
    }
}

?>