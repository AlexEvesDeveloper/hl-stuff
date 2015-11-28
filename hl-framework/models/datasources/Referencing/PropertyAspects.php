 <?php
 
/**
 * Model definition for the propecty_aspect_map datasource.
 */
class Datasource_Referencing_PropertyAspects extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'property_aspect_map';
    protected $_primary = array('reference_id', 'property_aspect_id');
    
    /**
     * Inserts or updates property aspect items into the datasource.
     *
     * @param array
     * An array of Model_Referencing_PropertyAspects_PropertyAspectItem
     * objects.
     *
     * @return void
     */
	public function upsertAspects($propertyAspectItems) {
		
		if($propertyAspectItems == null) {
			
			return;
		}
		
		foreach($propertyAspectItems as $currentItem) {
			
			$this->upsertAspect($currentItem);
		}
	}
	
	/**
     * Inserts or updates a single property aspect item into the datasource.
     *
     * @param Model_Referencing_PropertyAspects_PropertyAspectItem $aspectItem
     * Encapsulates an aspect identifier and the corresponding value recorded
     * against it for a particular reference.
     *
     * @return void
     */
    public function upsertAspect(Model_Referencing_PropertyAspects_PropertyAspectItem $aspectItem) {
    
		$select = $this->select();
		$select->where('reference_id = ? ', $aspectItem->referenceId);
		$select->where('property_aspect_id = ? ', $aspectItem->propertyAspectId);
		$row = $this->fetchRow($select);
		
		if(empty($row)) {

			$this->insert(array(
				'reference_id' => $aspectItem->referenceId,
				'property_aspect_id' => $aspectItem->propertyAspectId,
				'value' => $aspectItem->value
			));
		}
		else {
			
			$updateArray = array('value' => $aspectItem->value);
			
			$where = $this->quoteInto('reference_id = ? AND property_aspect_id = ?',
				$aspectItem->referenceId, $aspectItem->propertyAspectId);
			
			$this->update($updateArray, $where);
		}
    }
	
	/**
	 * Returns the property aspect items relating to a reference.
	 *
	 * @param integer $referenceId
	 * The unique Reference identifier.
	 *
	 * @return mixed
	 * An array of Model_Referencing_PropertyAspects_PropertyAspectItem objects,
	 * or null if none found.
	 */
	public function getAspects($referenceId) {
		
		$select = $this->select();
		$select->where('reference_id = ? ', $referenceId);
		$rows = $this->fetchAll($select);
		
		$returnVal = null;
		if(count($rows) != 0) {
			
			$returnVal = array();
			foreach($rows as $currentRow) {
				
				$aspectItem = new Model_Referencing_PropertyAspects_PropertyAspectItem();
				$aspectItem->referenceId = $referenceId;
				$aspectItem->propertyAspectId = $currentRow->property_aspect_id;
				$aspectItem->value = $currentRow->value;
				
				$returnVal[] = $aspectItem;
			}
		}
		
		return $returnVal;
	}
}

?>