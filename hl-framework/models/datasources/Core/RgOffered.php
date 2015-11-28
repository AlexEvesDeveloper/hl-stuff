<?php
class Datasource_Core_RgOffered extends Zend_Db_Table_Multidb
{
    protected   $_name = 'RGoffered';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_referencing';
      
	/**
     * Fetch a type by the passed ID
     *
     * @param id is id of the RGoffered
     * @return array
     */
    public function getLetTypeByID($id){
        $select = $this->select();
        $select->where('id = ?', $id );
        $select->where('isRenewal = 0' );
        $row = $this->fetchRow($select);
        if(count($row)) {        
            return $row['type'];
        } else {
            return false;
        }        
    }
    
}
?>
