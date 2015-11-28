<?php

class Datasource_Referencing_ProductVariablesMap extends Zend_Db_Table_Multidb {    

    protected $_multidb = 'db_referencing';
    protected $_name = 'product_variables_map';
    protected $_primary = array('product_id', 'product_variable_id');

    public function getByProductId($productId) {
            
        $select = $this->select();
        $select->where('product_id = ?', $productId);
        $rows = $this->fetchAll($select);

        $variablesArray = array();
        if(count($rows) > 0) {

        	foreach($rows as $row) {
                
                $variablesArray[$row->product_variable_id] = 1;
            }
        }
        
        return $variablesArray;
    }
}

?>