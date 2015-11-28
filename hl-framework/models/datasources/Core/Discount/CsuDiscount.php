<?php
class Datasource_Core_Discount_CsuDiscount extends Zend_Db_Table_Multidb
{
    protected   $_name = 'csuDiscount';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * This clears CSU Discount entries by csu id and product option id. This is
     * because we may not know the actual ids which we need to get rid of.
     * 
     * @param int $prodOpt
     * @param int $csu
     */
    public function clearCsuDiscountByCsuIdAndProdOptId($prodOpt, $csu)
    {
        $q = 'productOptDiscID IN (SELECT id FROM productOptionDiscount WHERE productOptionID = ?)';
        $where = $this->getAdapter()->quoteInto($q, $prodOpt);
        $where .= $this->getAdapter()->quoteInto(' AND csuid = ?', $csu);
        $this->delete($where);
    }
    
    /**
     * Fetch a csu by the passed ID
     *
     * @param id id of csuDiscount
     * @return array
     */
    public function getCsuDiscountByID($id)
    {
        $select = $this->select();
        $select->where('id = ?', $id );
        $row = $this->fetchRow($select);
        
        if(count($row)){
        	$disc = new Model_Core_Discount_CsuDiscount();
        	$data = $disc->populate($row->toArray());
        } else {
        	$data = false;
        }
        return $data;
    }
    
    /**
     * 
     * @param unknown_type $csuid
     */
    public function getCsuDiscountsByCsuId($csuid)
    {
        $select = $this->select();
        $select->where('csuid = ?', $csuid )
            ->order('productOptDiscID');
        $rows = $this->fetchAll($select);
        if(count($rows)){
            $ret = array();
            foreach ($rows as $row) {
            	$disc = new Model_Core_Discount_CsuDiscount();
                $ret[] = $disc->populate($row->toArray());
            }
            $data = $ret;
        } else {
            $data = false;
        }
        return $data;
    }
    
    /**
     * Fetch a csu Team by the csu 
     * For supervise use
     * @param int $csuid
     * @param int $productOptionID
     * @return csuTeamID
     */
    public function getProductDiscByCsuProductOptionId($csuid, $productOptionID)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('c' => $this->_name)
            )
            ->join(
                array('d' => 'productOptionDiscount')
                ,'d.id=c.productOptDiscID'
            )
            ->where('d.productOptionID = ?', $productOptionID)
            ->where('c.csuid = ?', $csuid);
        $row = $this->fetchAll($select);
        if(count($row) > 0){
       	    $pricelist = array();
       	    foreach ($row as $data) {
       	        $csuDiscount = new Model_Core_Discount_CsuDiscount();
       	        $csuDiscount->populate($data);
                $prodOptDisc = new Model_Core_Product_ProductOptionDiscount();
                $prodOptDisc->populate($data);
                $prodOpts = new Datasource_Core_Product_Options();
       	        $pricelist[] = new Model_Core_Discount_CsuProductOptionDiscount(
                    $csuDiscount,
                    $prodOptDisc,
                    $prodOpts->getProductOptionsByID($data['productOptionID'])
                );
       	    }
        } else {
            $pricelist = false;
        }
        return $pricelist;
    }
    
	/**
     * Save an assignment of discount to a team member
     *      
     * @param csuID, supervisorID, productOptDiscID
     * @return array
     */
    public function setDiscount($csuID, $supervisorID, $productOptDiscIds) 
    {
        foreach ($productOptDiscIds as $productOptDiscId) 
        {
    		$insertArray ['csuid'] = $csuID;
    		$insertArray ['productOptDiscID'] = $productOptDiscId ;
    		$insertArray ['supervisorID'] = $supervisorID;
            $this->insert($insertArray);
        }
        
    }
    
    /**
     * Delete a discount assignment
     *
     * @param int csuid
     * @param array $productOptDiscIDs
     * @return void
     */
    public function removeDiscount($csuID, $productOptDiscIDs) 
    {
        foreach ($productOptDiscIDs as $productOpt) 
        {
            $where = $this->getAdapter()->quoteInto(
                'csuid = ?',
                $csuID
            );
            $where .= $this->getAdapter()->quoteInto(
                ' and productOptDiscID = ?',
                $productOpt
            );
            $this->delete($where);
        }
    }   
}
?>

