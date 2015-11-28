<?php
class Datasource_Insurance_Quote_Products extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';
	protected $_name = 'quote_products';
    protected $_primary = 'id';
	
	/**
	 * 
	 */
	public function getCountByQuoteID ($quoteID) {
		$select = $this->select()->where('quote_id = ?', $quoteID);
		$select->from($this->_name, 'COUNT(*) as product_count');
		
		$row = $this->fetchRow($select);
		
		return $row->product_count;
	}
	
	
	/**
	 * 
	 */
	public function getProductsByQuoteID ($quoteID) {
		$select = $this->select()->where('quote_id = ?', $quoteID);
		$productRows = $this->fetchAll($select);
		
		if (count($productRows)>0) {
			$returnArray = array();
			foreach ($productRows as $productRow) {
				$returnArray[] = $productRow->product_id;
			}
			return $returnArray;
		} else {
			return array();
		}
	}
	
	/**
	 * 
	 */
	public function remove ($quoteID, $productID) {
		// Remove the product from the quote
		$where = $this->quoteInto('quote_id = ? AND product_id = ?', $quoteID, $productID);
 		$this->delete($where);
 		
 		// Clean up meta data
 		$quoteProductMetas = new Datasource_Insurance_Quote_Product_Metas();
 		$quoteProductMetas->cleanup($quoteID, $productID);
	}
	
	public function add ($quoteID, $productID) {
		$data = array (
			'quote_id'		=>	$quoteID,
			'product_id'	=>	$productID
		);
		
		return $this->insert($data);
	}
}
?>