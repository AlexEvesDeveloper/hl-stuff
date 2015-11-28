<?php

class Datasource_Insurance_Quote_Product_Metas extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';
	protected $_name = 'quote_product_metas';
    protected $_primary = 'id';
	
	/**
	 * Cleanup redundant meta data for a removed product in a specific quote
	 *
	 * @param int quoteID ID of the quote
	 * @param int productID ID of the product
	 * @return boolean
	 *
	 */
	public function cleanup($quoteID, $productID) {
		$where = $this->quoteInto('quote_id = ? AND product_id = ?', $quoteID, $productID);
		return $this->delete($where);
	}
	
	/**
	 * Add some meta data for a product in a quote
	 *
	 * @param int quoteID ID of the Quote
	 * @param int productID ID of the product
	 * @param string metaName This is the 'nice name' for the meta item. I was going to use ID's but that was getting insane!!
	 * @param mixed metaValue The value for the meta
	 *
	 * Example: $quoteProductMetas->add($quoteID, Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER, 'build_year', '1980-1990');
	 * Note: The meta name must be valid for the product type or it will return an error
	 */
	public function add($quoteID, $productID, $metaName, $metaValue) {
		// Firstly we need to turn the metaName into a metaID
		$productMetas = new Datasource_Product_Metas();
		try {
			$meta = $productMetas->getByName($metaName);
		} catch (Exception $e) {
			// This meta name doesn't exist - so we can't add it
			throw new Exception('Invalid product meta name');
		}
		
		// Now we have the ID we can add the meta into the database
		$data = array (
			'quote_id'			=> $quoteID,
			'product_id'		=> $productID,
			'product_meta_id'	=> $meta['id']
		);
		
		if ($meta['type']=='STRING') {
			$data['string_value']	= $metaValue;
		} elseif ($meta['type']=='NUMBER') {
			$data['number_value']	= $metaValue;
		}
		
		$this->insert($data);
	}
	
	/**
	 * Gets the meta data for a specific product
	 *
	 * @param int quoteID ID of the quote
	 * @param int productID ID of the product
	 * @return array Associative array of meta data
	 */
	public function getByProductID($quoteID, $productID) {
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('qpm' => $this->_name));
		$select->joinInner('product_metas', 'qpm.product_meta_id = product_metas.id');
		$select->where('qpm.quote_id = ?', $quoteID);
		$select->where('qpm.product_id = ?', $productID);
		
		$metaRows = $this->fetchAll($select);
		
		$returnArray = array();
		if (count($metaRows)>0) {
			foreach ($metaRows as $meta) {
			
				if ($meta->type=='NUMBER') {
					$returnArray[$meta->name] = $meta->number_value;
				} elseif ($meta->type=='STRING') {
					$returnArray[$meta->name] = $meta->string_value;
				}
			}
		}
		return $returnArray;
	}
}
?>