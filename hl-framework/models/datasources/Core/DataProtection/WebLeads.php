<?php

/**
 * Encapsulates the data source responsible for storing WebLead data protection items.
 */
class Datasource_Core_DataProtection_WebLeads extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
	protected $_multidb = 'db_legacy_webleads';
	protected $_name = 'DATAPROTECTIONMAP';
	protected $_primary = array('QUOTEID', 'DATAPROTECTIONID');
	/**#@-*/
	
	
	/**
	 * Returns all data protection items identified by the $itemGroupId.
	 *
	 * @param string $itemGroupId
	 * Identifies the data protection item group. This corresponds to a unqiue
	 * WebLead ID.
	 *
	 * @return mixed
	 * Returns an array of Model_Core_DataProtection_Items, if found. Else returns null.
	 */
	public function getItems($itemGroupId) {
		
		$select = $this->select();
        $select->where('QUOTEID = ? ', $itemGroupId);
        $rows = $this->fetchAll($select);
        
		if(count($rows) == 0) {
			
			$returnArray = null;
		}
		else {
			
			$returnArray = array();
			foreach($rows as $row) {
			
				$item = new Model_Core_DataProtection_Item();
				$item->itemGroupId = $itemGroupId;
				$item->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::WEBLEAD;
				$item->constraintTypeId = $row->DATAPROTECTIONID;
				$item->isAllowed = ($row->ISALLOWED == 1) ? true : false;
				
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
		$where = $this->quoteInto('QUOTEID = ? AND DATAPROTECTIONID = ?', $item->itemGroupId, $item->constraintTypeId);
        $this->delete($where);
		
		//Insert the new record.
		$data = array(
			'QUOTEID' => $item->itemGroupId,
			'DATAPROTECTIONID' => $item->constraintTypeId,
			'ISALLOWED' => ($item->isAllowed) ? 1 : 0
		);
		$this->insert($data);
	}
}

?>