<?php
class Datasource_Core_PropertyLetType extends Zend_Db_Table_Multidb
{
    protected   $_name = 'property_let_types';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_referencing';
      
	/**
     * Fetch a type by the passed ID
     *
     * @param id is id of the property_let_types
     * @return array
     */
    public function getLetTypeByID($id){
        $select = $this->select();
        $select->where('id = ?', $id );
        $row = $this->fetchRow($select);
        if(count($row)) {        
            return $row['type'];
        } else {
            return false;
        }        
    }
    
}
?>
