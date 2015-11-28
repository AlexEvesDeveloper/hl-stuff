<?php

/**
 * Represents a referencing subject's occupation.
 */
class Model_Referencing_Occupation extends Model_Abstract {
	
	/**
	 * The unique occupation identifier.
	 *
	 * @var integer
	 */
	public $id;
    
    /**
	 * The reference identifier, linking the Reference to this occupation.
	 *
	 * @var integer
	 */
	public $referenceId;
    
    /**
	 * Identifies the occupation in a timeline.
	 *
	 * @var integer
	 * Must corresond to one of the consts exposed by the
	 * Model_Referencing_OccupationChronology class.
	 */
	public $chronology;
	
	/**
	 * Identifies the relative importance of the occupation.
	 * 
	 * Relative importance applies to all occupations within a chronology.
	 * For example, if a reference subject has two 'future' occupations,
	 * the importance assigned to each will apply to the 'future'
	 * chronology only. If the reference subject also has three 'current' occupations,
	 * the importance given to the currents will apply to the 'current' chronology
	 * only, with no relation to the 'future' occupations.
	 * 
	 * Importance is usually defined by the amount received by an occupation.
	 * So, the highest paid occupation in a chronology will typically be
	 * referred to as the first most important, with the second-highest being
	 * the second, etc. 
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_OccupationImportance class.
	 */
	public $importance;
	
	/**
	 * Identifies the type of the occupation.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_OccupationTypes class.
	 */
	public $type;
	
	/**
	 * Indicates if the occupation is permanent or not.
	 * 
	 * @var boolean
	 */
	public $isPermanent;
	
	/**
	 * Holds the income from this occupation.
	 *
	 * @var Zend_Currency
	 */
	public $income;
	
	/**
	 * Holds the start date of the occupation.
	 *
	 * @var Zend_Date
	 */
	public $startDate;
    
    /**
	 * Holds the occupation variables
	 *
	 * @var array
	 * The keys to this associative array MUST be taken from the
	 * Model_Referencing_OccupationVariables class.
	 */
	public $variables;
	
	/**
	 * Holds details of the occupation referee.
	 *
	 * @var mixed
	 * Model_Referencing_OccupationReferee if known, else is null.
	 */
	public $refereeDetails;
	
	/**
	 * Holds details of the occupation reference.
	 *
	 * @var Model_Referencing_OccupationReference
	 */
	public $referencingDetails;
	
	/**
	 * Holds Reference completion status
	 * 
	 * @var boolean
	 */
	
	public $isComplete;
}

?>