<?php
class Datasource_Core_Product extends Zend_Db_Table_Multidb
{
    protected   $_name = 'product';
    protected   $_primary = 'productID';
    protected   $_multidb = 'db_legacy_homelet';
    
/**
     * Fetch a product by the passed ID
     *
     * @param id productID of the product
     * @return array
     */
    public function getProductByID($id){
        $select = $this->select();
        $select->where('productID = ?', $id );
        $row = $this->fetchRow($select);
        if(count($row)) {
            $prod = new Model_Core_Product();
            $prod->populate(
                $row['productID'],
                $row['productName'],
                $row['description'],
                $row['prodStartDate'],
                $row['prodEndDate']
       		 );
       		 $ret = $prod;
        } else {
        	$ret = false;
        }
        return $ret;
        
    }
    
    /**
     * Fetch a product by the product name
     *
     * @param name productName for the search
     * @return array
     */
    public function getProductByName($name) {
        // First we need to check that the postcode is valid
        $select = $this->select();
        $select->where('productName = ?', $name);
        $row = $this->fetchRow($select);
        if(count($row)) {
            $prod = new Model_Core_Product();
            $prod->populate($row->toArray());
             $ret = $prod;
        } else {
            $ret = false;
        }
        return $ret;
    }
	/**
     * Fetch a list of product 
     *     
     * @param date date for a list of valid product
     * @return array
     */
    public function getProductList($date = null) {
        if (is_null($date)) {
            $date = date("Y-m-d");
        }
        $products = array();
        
        $select = $this->select();
        $select->where("prodStartDate <= ?", $date)
               ->where("prodEndDate >=? or prodEndDate='0000-00-00'", $date);
        
        $row = $this->fetchAll($select);

        foreach ($row as $data) {
            $prod = new Model_Core_Product();
            $prod->populate(
                $data['productID'], $data['productName'], $data['description'],
                $data['prodStartDate'], $data['prodEndDate']
            );
            $products[] = $prod;
        }
        return $products;
    }
}
?>
