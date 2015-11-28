<?php

/**
 * Represents a Reference within the system. The Reference links together all aspects of the referencing
 * process, and comprises the 'root' node of the tree of referencing objects. The Reference identifier 
 * can be used to identify all related data, not just that in the Reference datasource.
 */
class Model_Referencing_Reference extends Model_Abstract {

    /**
	 * Holds the IRN: the internal unique Reference identifier.
	 *
	 * @var integer
	 * E.g. 6123456
	 */
	public $internalId;
	
	/**
	 * Holds the ERN: the external unique 'Enquiry' identifier.
	 *
	 * @var string
	 * E.g. 12345678.1234/01
	 */
	public $externalId;
    
    /**
	 * Holds the Reference status.
	 *
	 * @var Model_Referencing_ReferenceStatus
	 */
	public $status;
    	
	/**
	 * Holds the Reference decision, if applicable.
	 *
	 * @var Model_Referencing_Decision
	 */
	public $decision;
    
    /**
	 * Identifies who completed the reference.
	 * 
     * This is useful to understand whether the Reference was 'emailed-to-tenant' or completed by a single
     * user.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the Model_Referencing_ReferenceCompletionMethods 
	 * class.
	 */
    public $completionMethod;
    
    /**
	 * Holds the details of the property lease.
	 *
	 * @var Model_Referencing_PropertyLease
	 */
	public $propertyLease;
	
	/**
	 * Holds the product selection details.
	 *
	 * @var Model_Referencing_ProductSelection
	 */
	public $productSelection;
	    
    /**
	 * Holds the details of the Reference customer.
	 *
	 * @var Model_Referencing_CustomerMap
	 */
    public $customer;
    
	/**
	 * Holds the details of the entity being referenced (tenant or guarantor).
	 *
	 * @var Model_Referencing_ReferenceSubject
	 */
	public $referenceSubject;

	/**
	 * Holds the progress details of the reference.
	 *
	 * @var Model_Referencing_Progress
	 */
	public $progress;

    /**
     * Referencing declaration version number (from legacy Enquiry table)
     *
     * @var integer
     */
    public $declarationVersion;
}

?>