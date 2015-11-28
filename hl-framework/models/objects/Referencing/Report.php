<?php

/**
 * Represents a reference report
 */
class Model_Referencing_Report extends Model_Abstract 
{
	/**
	 * Holds the ERN: the external unique 'Enquiry' identifier.
	 *
	 * @var string
	 * E.g. 12345678.1234/01
	 */
	public $externalId;

    /**
     * Csuid of the internal user who generated the report
     *
     * @var int
     */
    public $csuid;

    /**
     * Date/time of when the report was generated
     *
     * @var string
     */
    public $generationTime;

    /**
     * Report type name - interim or final
     *
     * @var string
     */
    public $reportType;

    /**
     * Report filename
     *
     * @var string
     */
    public $fileName;

    /**
     * Validation key
     *
     * @var string
     */
    public $validationKey;
}

