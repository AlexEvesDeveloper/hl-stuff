<?php
class Datasource_Core_Csu extends Zend_Db_Table_Multidb
{
    protected   $_name = 'csu';
    protected   $_primary = 'csuid';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * Fetch a csu by the passed ID
     *
     * @param id of the CSU
     * @return object
     */
    public function getCsuByID($id) {
        $select = $this->select();
        $select->where('csuid = ?', $id );
        $row = $this->fetchRow($select);
        if (count($row) > 0) {
            $csu = new Model_Core_Csu();
            $csu->populate($row);
            $ret = $csu;
        } else {
            $ret = false;
        }
        return $ret; 
    } 
}