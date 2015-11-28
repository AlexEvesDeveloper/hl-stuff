 <?php
 
/**
 * Model definition for the propecty_aspect_map datasource.
 */
class Datasource_ReferencingLegacy_PropertyAspects extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'property_aspect_map';
    protected $_primary = array('enquiry_id', 'property_aspect_id');
    
    
	/**
     * Inserts or updates a property aspect item into the datasource.
     *
     * @param Model_Referencing_PropertyAspects_PropertyAspectItem $aspectItem
     * Encapsulates an aspect identifier and the corresponding value recorded
     * against it for a particular reference.
     *
     * @return void
     */
    public function upsertAspect(Model_Referencing_PropertyAspects_PropertyAspectItem $aspectItem) {
    
		$select = $this->select();
		$select->where('enquiry_id = ? ', $aspectItem->referenceId);
		$select->where('property_aspect_id = ? ', $aspectItem->propertyAspectId);
		$row = $this->fetchRow($select);
		
		if(empty($row)) {

			$this->insert(array(
				'enquiry_id' => $aspectItem->referenceId,
				'property_aspect_id' => $aspectItem->propertyAspectId,
				'value' => $aspectItem->value
			));
		}
		else {
			
			$updateArray = array('value' => $aspectItem->value);
			
			$where = $this->quoteInto('enquiry_id = ? AND property_aspect_id = ?',
				$aspectItem->referenceId, $aspectItem->propertyAspectId);
			
			$this->update($updateArray, $where);
		}
    }
	
	
	/**
	 * Returns the property aspect items relating to a reference.
	 *
	 * @param string $enquiryId
	 * The unique external Enquiry identifier.
	 *
	 * @return mixed
	 * An array of Model_Referencing_PropertyAspects_PropertyAspectItem objects,
	 * or null if none found.
	 */
	public function getAspects($enquiryId) {
		
		//Convert the external enquiry identifier to internal.
		$enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
		$id = $enquiryDatasource->getInternalIdentifier($enquiryId);
		
		$select = $this->select();
		$select->where('enquiry_id = ? ', $id);
		$rows = $this->fetchAll($select);
		
		$returnVal = null;
		if(count($rows) != 0) {
			
			$returnVal = array();
			foreach($rows as $currentRow) {
				
				$aspectItem = new Model_Referencing_PropertyAspects_PropertyAspectItem();
				$aspectItem->referenceId = $id;
				$aspectItem->propertyAspectId = $currentRow->property_aspect_id;
				$aspectItem->value = $currentRow->value;
				
				$returnVal[] = $aspectItem;
			}
		}
		
		return $returnVal;
	}
}

?>