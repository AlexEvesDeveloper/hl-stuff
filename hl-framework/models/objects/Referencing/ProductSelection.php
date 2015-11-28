<?php

/**
 * Represents a referencing product selection within the system. A product
 * selection is comprised of a product and duration of the product. For
 * fixed-length products, the duration will not apply and should be
 * set to 0.
 */
class Model_Referencing_ProductSelection extends Model_Abstract {
	
	/**
	 * Link the ProductSelection to a Reference.
	 *
	 * @var integer
	 */
    public $referenceId;
    
    /**
	 * Holds the product details.
	 *
	 * @var Model_Referencing_Product
	 */
	public $product;
	
	/**
	 * Holds the duration of the product in months.
	 *
	 * @var integer
	 */
	public $duration;
}

?>