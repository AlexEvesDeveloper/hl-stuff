<?php

namespace RRP\Application;

use RRP\Application\Exception\InvalidTenantReferenceNumberException;
use RRP\Application\Exception\InvalidUnderwritingAnswerNumberException;

/**
 * Interface ApplicationInterface
 *
 * @package RRP\Application
 * @author April Portus <april.portus@barbon.com>
 */
interface ApplicationDecoratorInterface
{
    /**
     * Constructor
     */
    public function __construct();

    /**
     * Gets the application record
     *
     * @return object
     */
    public function getAppData();

    /**
     * Gets the Rent Recovery Plus record
     *
     * @return object
     */
    public function getRrpData();

    /**
     * Gets the Landlord Interest record
     *
     * @return object
     */
    public function getLliData();

    /**
     * Gets the number of Rrp Tenant Reference records
     *
     * @return int
     */
    public function getRrpTenantReferenceCount();

    /**
     * Gets the given Rrp Tenant Reference record number
     *
     * @param int $recordNumber
     * @return object
     * @throws InvalidTenantReferenceNumberException
     */
    public function getRrpTenantReferenceRecord($recordNumber);

    /**
     * Adds a Rrp Tenant Reference record to the array
     *
     * @param object $rrpTenantReference
     * @return $this
     */
    public function addRrpTenantReferenceRecord($rrpTenantReference);

    /**
     * Clears all Rrp Tenant References
     *
     * @return $this
     */
    public function clearAllRrpTenantReferences();

    /**
     * Gets the number of Underwriting Answers
     *
     * @return int
     */
    public function getUnderwritingAnswerCount();

    /**
     * Gets the given Underwriting Answer number
     *
     * @param int $answerNumber
     * @return object
     * @throws InvalidUnderwritingAnswerNumberException
     */
    public function getUnderwritingAnswer($answerNumber);

    /**
     * Gets the Underwriting Answers array
     *
     * @return array
     */
    public function getAllUnderwritingAnswers();

    /**
     * Sets the policy number in the sub records
     *
     * @param $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber);

    /**
     * Saves the application to the database
     *
     * @return bool
     */
    public function save();

    /**
     * Gets the application from the database
     *
     * @param string $policyNumber
     */
    public function populateByPolicyNumber($policyNumber);

    /**
     * Getter for the policy option match field
     *
     * Returns the relevant match value (amount/premium/discounts) by which $optionName is covered on the current
     * quote / policy. This can be used to identify specific match value,
     * without the need of extracting the entire contents of the matching fields
     * field and splitting up the pipe-delimited fields.
     *
     * @param $policyOption
     * @param string $optionName , $matchfield
     * The policy option for which the matching field will be returned. It is
     * recommended that calling code use the constants in the Model_PolicyOptionConstants
     * class as values to be passed to this method.
     *
     * @param string $matchField
     * @return mixed
     */
    public function getPolicyOptionMatch($policyOption, $optionName, $matchField);

}