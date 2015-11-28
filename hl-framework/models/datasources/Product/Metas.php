<?php

/**
 * 
 */
class Datasource_Product_Metas extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';
	protected $_name = 'product_metas';
    protected $_primary = 'id';
    
    public function getByName($metaName) {
    	$select = $this->select()->where('name = ?', $metaName);
    	$metaRow = $this->fetchRow($select);
    	if (count($metaRow)>0) {
    		return $metaRow->toArray();
    	} else {
    		throw new Exception('Product meta not found');
    	}
    }
}

?>