<?php

/**
 * Represents an occupation reference.
 */
class Model_Referencing_OccupationReference extends Model_Abstract {
	
	/**
	 * The unique occupation identifier, to which this reference is linked.
	 *
	 * @var integer
	 */
	public $occupationId;
	
	/**
	 * Holds the means by which the reference was provided.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_OccupationReferenceProvisions class.
	 */
	public $provisionType;
	
	/**
	 * Holds the details of how the reference was submitted/returned.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_ReferenceSubmissionTypes class.
	 */
	public $submissionType;
	
	/**
	 * Holds the occupation reference variables, which differ depending on $provisionType.
	 *
	 * @var mixed
	 * The keys of this array MUST correspond to one of the consts exposed 
	 * by the Model_Referencing_OccupationReferenceVariables class. If no variables
	 * applicable, then this attribute will be null.
	 */
	public $variables;
	
	/**
	 * Details whether the reference is acceptable or not.
	 *
	 * @var boolean
	 */
	public $isAcceptable;
}

?>