<?php

/**
 * Encapsulates a single property aspect answer given by the reference subject.
 */
class Model_Referencing_PropertyAspects_PropertyAspectItem {
	
	/**
	 * The unique Reference identifier.
	 *
	 * @var integer
	 */
	public $referenceId;
	
	/**
	 * The property aspect identifier.
	 *
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_PropertyAspects_PropertyAspectTypes class.
	 *
	 * @var integer
	 */
	public $propertyAspectId;
	
	/**
	 * The property aspect value.
	 *
	 * @var boolean
	 */
	public $value;
}

?>