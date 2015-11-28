<?php
class Datasource_Core_Product_Options extends Zend_Db_Table_Multidb
{
    protected   $_name = 'productOptions';
    protected   $_primary = 'productOptionsID';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * Fetch a productOptions by the passed ID
     *
     * @param id, productOptionsID of the product
     * @return array
     */
    public function getProductOptionsByID($id){
        $select = $this->select();
        $select->where('productOptionsID = ?', $id );
        $row = $this->fetchRow($select);
        if (count($row) > 0) {
            $prodOpt = new Model_Core_Product_ProductOptions();
            $prodOpt->populate($row->toArray());
            $ret = $prodOpt;
        } else {
            $ret = false;
        }
        return $ret; 
    }
    
    /**
     * Fetches a product options object from a given linked policy option id
     * 
     * @param int $policyOption
     * @return Model_Core_Product_ProductOptions object
     */
    public function getProductOptByPolicyOptName($policyOption)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('pro' => $this->_name)
            )
            ->join(
                array('pol' => 'policyOptions'),
                'pro.policyOptionID = pol.policyOptionId',
                array()
            )
            ->where('pol.policyOption = ?', $policyOption);
        $row = $this->fetchRow($select);
        if (count($row) > 0) {
            $prodOpt = new Model_Core_Product_ProductOptions();
            $prodOpt->populate($row->toArray());
            $ret = $prodOpt;
        } else {
            $ret = false;
        }
        return $ret; 
    }
    
    /**
     * Fetch a productOption by the productID, policyOptionID
     *
     * @param productID, policyOptionID, date for the search
     * @return array
     */
    public function getProductByProdAndOpt($productID, $policyOptionID, $date=null) {
        if (is_null($date)) {
            $date = date("Y-m-d");
        }
        $select = $this->select();
        $select->where('productID = ?', $productID)
               ->where('policyOptionID = ?', $policyOptionID)
               ->where("optionStartDate <= ?", $date)
               ->where("optionEndDate >=? or optionEndDate='0000-00-00'", $date);
        $row = $this->fetchRow($select);
        if (count($row) > 0) {
       		$prodOpt = new Model_Core_Product_ProductOptions();
       		$prodOpt->populate($row->toArray());
       		$ret = $prodOpt;
        } else {
            $ret = false;
        }
        return $ret;
    }
    
	/**
     * Fetch a list of product 
     *     
     * @param productID, date for a list of valid product
     * @return array
     */
    public function getProductOptionsList($productID,$date=null) {
        if (is_null($date)) {
            $date = date("Y-m-d");
        }
        $productOptions = array();
        $select = $this->select();
        $select->where("productID = ?", $productID)
               ->where("optionStartDate <= ?", $date)
               ->where("optionEndDate >=? or optionEndDate='0000-00-00'", $date);
        $row = $this->fetchAll($select);
        foreach ($row as $data) {
            $prodOpt = new Model_Core_Product_ProductOptions();
            $prodOpt->populate($data);
            $productOptions[] = $prodOpt;
        }
        return $productOptions;
    }
    
    /**
     * 
     * @param str $productName
     * @param str $policyOption
     */
    public function getProductOptionByProductAndOptionName(
        $productName, $policyOption
    )
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('po' => $this->_name)
            )
            ->join(
                array('p' => 'product'),
                'po.productID = p.productID',
                array()
            )
            ->join(
                array('pol' => 'policyOptions'),
                'po.policyOptionID = pol.policyOptionID',
                array()
            )
            ->where('p.productName = ?', $productName)
            ->where('pol.policyOption = ?', $policyOption);
        $row = $this->fetchRow($select);
        if (count($row)) {
            $prodOptDisc = new Model_Core_Product_ProductOptions();
            $prodOptDisc->populate($row->toArray());
            $discount = $prodOptDisc;
        } else {
            $discount = false;
        }
        return $discount;
    }
}
?>
