<?php

/**
 * Encapsulates a single data protection answer given by the reference subject.
 */
class Model_Referencing_DataProtectionItem {
	
	/**
	 * The unique Reference identifier.
	 *
	 * @var integer
	 */
	public $referenceId;
	
	/**
	 * The data protection identifier.
	 *
	 * Must correspond to one of the consts exposed by the Model_Referencing_DataProtectionTypes
	 * class.
	 *
	 * @var integer
	 */
	public $dataProtectionId;
	
	/**
	 * The data protection value.
	 *
	 * @var boolean
	 */
	public $dataProtectionValue;
}

?>