<?php

/**
 * Encapsulates the data source responsible for storing Referencing data protection items.
 */
class Datasource_Core_DataProtection_Referencing extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
	protected $_multidb = 'db_legacy_referencing';
	protected $_name = 'data_protection_map';
	protected $_primary = array('enquiry_id', 'data_protection_id');
	/**#@-*/
	
	
	/**
	 * Returns all data protection items identified by the $itemGroupId.
	 *
	 * @param integer $itemGroupId
	 * Identifies the data protection item group. This corresponds to a unqiue
	 * internal Reference identifier (IRN).
	 *
	 * @return mixed
	 * Returns an array of Model_Core_DataProtection_Items, if found. Else returns null.
	 */
	public function getItems($itemGroupId) {
		
		$select = $this->select();
        $select->where('enquiry_id = ? ', $itemGroupId);
        $rows = $this->fetchAll($select);
        
		if(count($rows) == 0) {
			
			$returnArray = null;
		}
		else {
			
			$returnArray = array();
			foreach($rows as $row) {
			
				$item = new Model_Core_DataProtection_Item();
				$item->itemGroupId = $itemGroupId;
				$item->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::REFERENCING;
				$item->constraintTypeId = $row->data_protection_id;
				$item->isAllowed = ($row->is_allowed == 1) ? true : false;
				
				$returnArray[] = $item;
			}
		}
		
		return $returnArray;
	}
	
	
	/**
	 * Inserts or updates data protection items in the dbase.
	 *
	 * @param Model_Core_DataProtection_Item $item
	 * Encapsulates the data protection item details.
	 *
	 * @return void
	 */
	public function upsertItem($item) {
		
		//Delete the existing record, if it exists.
		$where = $this->quoteInto('enquiry_id = ? AND data_protection_id = ?', $item->itemGroupId, $item->constraintTypeId);
        $this->delete($where);
		
		//Insert the new record.
		$data = array(
			'enquiry_id' => $item->itemGroupId,
			'data_protection_id' => $item->constraintTypeId,
			'is_allowed' => ($item->isAllowed) ? 1 : 0
		);
		$this->insert($data);
	}
}

?>