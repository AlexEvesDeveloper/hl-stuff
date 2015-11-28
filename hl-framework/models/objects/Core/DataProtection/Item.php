<?php

/**
 * Represents a data protection item in the system.
 */
class Model_Core_DataProtection_Item {

	/**
	 * Identifies the item group in the system and in the storage layer (dbase or session).
	 *
	 * Typically the value set here will correspond to the quote/policy number,
	 * the reference number, or a WebLead ID.
	 *
	 * @var mixed
	 * String or integer. If string, then the maximum size allowed is 20 characters.
	 */
	public $itemGroupId;
	
	/**
	 * Identifies if this item is 'insurance', 'referencing' or 'weblead' related.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the Model_Core_DataProtection_ItemEntityTypes class.
	 */
	public $entityTypeId;
	
	/**
	 * Identifies the data protection constraint type.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the Model_Core_DataProtection_ItemConstraintTypes class.
	 */
	public $constraintTypeId;
	
	/**
	 * Indicates if the constraint is allowed.
	 *
	 * @var boolean
	 * True if dpa type is allowed, false otherwise.
	 */
	public $isAllowed;
}

?>