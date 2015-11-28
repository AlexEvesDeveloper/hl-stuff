<?php

/**
 * Data protection manager class.
 *
 * Use this class to read/write all DPA items to temporary or permanent storage,
 * for insurance, referencing and webleads.
 */
class Manager_Core_DataProtection {	
	
	/**#@+
	 * Used to indicate the type of storage wanted.
	 */
	const USE_DBASE = 1;
	const USE_SESSION = 2;
	/**#@-**/
	
	/**
	 * Holds the storage type specified.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by this class.
	 */
	protected $_storageType;
	
	/**#@+
     * Internal datasource references.
     */
	protected $_referencingDpaDatasource;
	protected $_insuranceDpaDatasource;
	protected $_webleadsDatasource;
    /**#@-*/
    
	/**
	 * Holds the session used to store DPA data.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by this class.
	 */
	protected $_dataProtectionSession;
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct($storageType = null) {

		if($storageType == null) {
			
			$storageType = self::USE_DBASE;
		}
		
		$this->setStorageType($storageType);
    }
	
	
	/**
	 * Sets the storage type used by the manager.
	 *
	 * The manager will store to the dbase or to the session, depending on the
	 * preference set by calling code.
	 *
	 * @param int $storageType
	 * Must correpond to one of the consts exposed by this class.
	 *
	 * @return void
	 */
	public function setStorageType($storageType) {
		
		$this->_storageType = $storageType;
	}
	
	
	/**
	 * Returns all data protection items identified by the $itemId and the $targetType.
	 *
	 * @param mixed $itemGroupId
	 * Identifies the data protection item group. This value typically corresponds to a quote/policy
	 * number, reference number or WebLead ID.
	 * 
	 * @param int $itemEntityType
	 * Must correspond to one of the consts exposed by the Model_Core_DataProtection_ItemEntityTypes
	 * class.
	 *
	 * @return mixed
	 * Returns an array of Model_Core_DataProtection_Items, if found. Else returns null.
	 */
	public function getItems($itemGroupId, $itemEntityType) {
			
		$itemArray = null;
		
		if($this->_storageType == self::USE_DBASE) {
			
			if($itemEntityType == Model_Core_DataProtection_ItemEntityTypes::REFERENCING) {
				
				if(empty($this->_referencingDpaDatasource)) {
					
					$this->_referencingDpaDatasource = new Datasource_Core_DataProtection_Referencing();
				}
				
				$itemArray = $this->_referencingDpaDatasource->getItems($itemGroupId);
			}
			else if($itemEntityType == Model_Core_DataProtection_ItemEntityTypes::INSURANCE) {
				
				if(empty($this->_insuranceDpaDatasource)) {
					
					$this->_insuranceDpaDatasource = new Datasource_Core_DataProtection_Insurance();
				}
				
				$itemArray = $this->_insuranceDpaDatasource->getItems($itemGroupId);
			}
			else if($itemEntityType == Model_Core_DataProtection_ItemEntityTypes::WEBLEAD) {
				
				if(empty($this->_webleadsDatasource)) {
					
					$this->_webleadsDatasource = new Datasource_Core_DataProtection_WebLeads();
				}
				
				$itemArray = $this->_webleadsDatasource->getItems($itemGroupId);
			}
		}
		else if($this->_storageType == self::USE_SESSION) {
			
			//Retrieve from session.
			$this->_dataProtectionSession = new Zend_Session_Namespace('data_protection');
				$itemArray = $this->_dataProtectionSession->itemArray;
			}
		
		return $itemArray;
	}
	
	
	/**
	 * Inserts or updates data protection items in the storage layer previously specified.
	 *
	 * @param Model_Core_DataProtection_Item $item
	 * Encapsulates the data protection item details.
	 *
	 * @return void
	 */
	public function upsertItem($item) {
		
		if($this->_storageType == self::USE_DBASE) {
			
			if($item->entityTypeId == Model_Core_DataProtection_ItemEntityTypes::REFERENCING) {

				if(empty($this->_referencingDpaDatasource)) {

					$this->_referencingDpaDatasource = new Datasource_Core_DataProtection_Referencing();
				}
				
				$this->_referencingDpaDatasource->upsertItem($item);
			}
			else if($item->entityTypeId == Model_Core_DataProtection_ItemEntityTypes::INSURANCE) {
				
				if(empty($this->_insuranceDpaDatasource)) {
					
					$this->_insuranceDpaDatasource = new Datasource_Core_DataProtection_Insurance();
				}
				
				$this->_insuranceDpaDatasource->upsertItem($item);
			}
			else if($item->entityTypeId == Model_Core_DataProtection_ItemEntityTypes::WEBLEAD) {
			
				if(empty($this->_webleadsDatasource)) {
					
					$this->_webleadsDatasource = new Datasource_Core_DataProtection_WebLeads();
				}
				
				$this->_webleadsDatasource->upsertItem($item);
			}
		}
		else if($this->_storageType == self::USE_SESSION) {
			
			if(empty($this->_dataProtectionSession)) {
				
				$this->_dataProtectionSession = new Zend_Session_Namespace('data_protection');
				$this->_dataProtectionSession->itemArray = array();
			}
			
			$this->_dataProtectionSession->itemArray[] = $item;
		}
	}
	
	
	public function reset() {
		
		if(!empty($this->_dataProtectionSession)) {
			
			unset($this->_dataProtectionSession->itemsArray);
			Zend_Session::namespaceUnset($this->_dataProtectionSession);
		}
	}
}

?>