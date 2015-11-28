<?php
class Datasource_Core_Team_PositionType extends Zend_Db_Table_Multidb
{
    protected   $_name = 'positionType';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_homelet';
    
   
    /**
     * Fetch a position type by id
     *
     * @param id, id of positionType
     * @return positionType name
     */
    public function getPositionType($id){
    
    	
        $select = $this->select();
        $select->where("id = ?", $id );
                          
        $row = $this->fetchRow($select);

        if(count($row)){
                     
            return $row->type;
        
        }else{
        	
        	return false;
        }
        
    }
    
     /**
     * Fetch a position id by Type
     *
     * @param type, type of positionType
     * @return id
     */
    public function getPositionID($type) {
    
    	
        $select = $this->select();
        $select->where("type = ?", $type );
                          
        $row = $this->fetchRow($select);

        if(count($row)){
                     
            return $row->id;
        
        }else{
        	
        	return false;
        }
        
    }
}
?>
