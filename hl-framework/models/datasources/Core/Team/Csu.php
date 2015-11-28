<?php
class Datasource_Core_Team_Csu extends Zend_Db_Table_Multidb
{
    protected   $_name = 'csu';
    protected   $_primary = 'csuid';
    protected   $_multidb = 'db_legacy_homelet';
    
/**
     * Fetch a csu by the passed ID
     *
     * @param csuid csuid of csu
     * @return string 
     */
    public function getCsuNameByID($csuid){
        $select = $this->select();
        $select->where('csuid = ?', $csuid );
        $row = $this->fetchRow($select);
        if(count($row)){
       		 return $row->realname;
        }else{
        	
        	return false;
        }
        
    }
    
    
}
?>
