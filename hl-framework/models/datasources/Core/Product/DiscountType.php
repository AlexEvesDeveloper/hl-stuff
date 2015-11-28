<?php
class Datasource_Core_Product_ProductDiscountType extends Zend_Db_Table_Multidb
{
    protected   $_name = 'discountType';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_homelet';
    
   
    /**
     * Fetch a discount type by id
     *
     * @param id, id of discountType
     * @return discount type name
     */
    public function getDiscountType($id){
    
    	
        $select = $this->select();
        $select->where("id = ?", $id );
                          
        $row = $this->fetchRow($select);

        if(count($row)){
                     
            return $row->name;
        
        }else{
        	
        	return false;
        }
        
    }
    
}
?>
