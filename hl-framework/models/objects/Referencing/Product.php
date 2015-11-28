<?php

/**
 * Represents a referencing product within the system.
 *
 * Encapsulates all details of the referencing product.
 */
class Model_Referencing_Product extends Model_Abstract {

	/**
	 * The product key.
	 *
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_ProductKeys class.
	 *
	 * @var integer
	 */
	public $key;

	/**
	 * The product name.
	 *
	 * @var string
	 */
	public $name;
    
    /**
     * Length of product in months. 0 if not fixed.
     * None applicable products should check the variables
     * for VARIABLE_DURATION.
     *
     * @var int
     */
    public $length;

	/**
	 * Holds variables applicable to the product.
	 *
	 * The keys to this array MUST corresond to consts exposed by the
	 * Model_Referencing_ProductVariables class. The only value given against
	 * a key will be 1, indicating the existence and applicability of that key
	 * against the current product.
	 *
	 * Product variables allow code to differentiate between product types,
	 * and to group similar product types together.
	 *
	 * @var array
	 */
	public $variables = array();

	/**
	 * Indicates whether or not the product is active.
	 *
	 * @var boolean
	 */
	public $isActive;
	
	/**
	 * The order in which this product should be displayed relative to other products.
	 *
	 * @var integer
	 */
	public $displayOrder;
}

?>