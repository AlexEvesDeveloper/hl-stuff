<?php
class Datasource_Core_Product_OptionDiscount extends Zend_Db_Table_Multidb
{
    protected   $_name = 'productOptionDiscount';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * Fetch a set of discount for an option 
     *
     * @param productOptionsID 
     * @return array 
     */
    public function getProductOptionDiscountList($productOptionId)
    {
        $select = $this->select();
        $select->where("productOptionID = ?", $productOptionId )
               ->where("valid = 1 ");               
        $row = $this->fetchAll($select);
        if (count($row)) {
            $discount = array();
            foreach ($row as $data) {
                $prodOptDisc = new Model_Core_Product_ProductOptionDiscount();
                $prodOptDisc->populate($data);
                $discount[] = $prodOptDisc;
            }
        } else {
        	$discount = false;
        }
        return $discount;
    }
    
    /**
     * Gets the default product option discount id
     * 
     * @param int $productOptionId
     */
    public function getDefaultProductOptionDiscountId($productOptionId)
    {
        $select = $this->select();
        $select->where("productOptionID = ?", $productOptionId )
               ->where("valid = 1 ")
               ->where("isDefault = 1");               
        $row = $this->fetchRow($select);
        if (count($row)) {
            $default = $row['id'];
        } else {
            $default = false;
        }
        return $default;
    }
    
    /**
     * Fetch a discount by id
     *
     * @param id, id of productOptionDiscount
     * @return array 
     */
    public function getProductOptionDiscountById($id)
    {
        $select = $this->select();
        $select->where("id = ?", $id );
        $row = $this->fetchRow($select);
        if (count($row)) {
            $prodOptDisc = new Model_Core_Product_ProductOptionDiscount();
            $prodOptDisc->populate($row);
            $ret = $prodOptDisc;
        } else {
        	$ret = false;
        }
        return $ret;
    }

    /**
     * 
     * @param str $productName
     * @param str $policyOption
     */
    public function getProductOptionDiscountListByProductAndOptionName(
        $productName, $policyOption
    )
    {
        $optds = new Datasource_Core_Product_Options();
        $opt = $optds->getProductOptionByProductAndOptionName(
            $productName, $policyOption
        );
        $select = $this->select()
            ->where('productOptionID = ?', $opt->getProductOptionsId());
        $row = $this->fetchAll($select);
        if (count($row)) {
            $discount = array();
            foreach ($row as $data) {
                $prodOptDisc = new Model_Core_Product_ProductOptionDiscount();
                $prodOptDisc->populate($data);
                $discount[] = $prodOptDisc;
            }
        } else {
            $discount = false;
        }
        return $discount;
    }
    
}
?>


