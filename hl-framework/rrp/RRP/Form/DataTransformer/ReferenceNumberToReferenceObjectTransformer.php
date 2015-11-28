<?php

namespace RRP\Form\DataTransformer;

use Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;
use RRP\Common\ReferenceTypes;
use RRP\Model\RentRecoveryPlusReference;
use RRP\Utility\DecisionDetailsRetriever;
use RRP\Utility\SessionReferenceHolder;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ReferenceNumberToReferenceObjectTransformer
 *
 * @package RRP\Form\DataTransformer
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceNumberToReferenceObjectTransformer implements DataTransformerInterface
{
    /**
     * @var DecisionDetailsRetriever
     */
    protected $decisionDetailsRetriever;

    /**
     * @var SessionReferenceHolder
     */
    protected $sessionHolder;

    /**
     * @var RentRecoveryPlusReference
     */
    protected $convertedReference;

    /**
     * @var string
     */
    protected $currentAsn;

    /**
     * ReferenceNumberToReferenceObjectTransformer constructor.
     *
     * @param DecisionDetailsRetriever $decisionDetailsRetriever
     * @param SessionReferenceHolder $sessionHolder
     * @param RentRecoveryPlusReference $convertedReference
     */
    public function __construct(
        DecisionDetailsRetriever $decisionDetailsRetriever,
        SessionReferenceHolder $sessionHolder,
        RentRecoveryPlusReference $convertedReference
    ) {
        $this->decisionDetailsRetriever = $decisionDetailsRetriever;
        $this->sessionHolder = $sessionHolder;
        $this->convertedReference = $convertedReference;
    }

    /**
     * Return the reference number to be displayed in the view.
     *
     * @param mixed $value
     * @return mixed|void
     */
    public function transform($value)
    {
        if ( ! $value instanceof RentRecoveryPlusReference) {
            // Haven't transformed the object yet.
            return null;
        }

        // Display the original reference number that was entered.
        return $value->getParent()->getReferenceNumber();
    }

    /**
     * Convert the reference number into a RentRecoveryPlusReference object.
     *
     * @param mixed $referenceNumber
     * @return mixed|RentRecoveryPlusReference
     * @throws \LogicException
     */
    public function reverseTransform($referenceNumber)
    {
        // Attempt to retrieve the corresponding applicationUuid from the session.
        if (false === ($referencingApplication = $this->sessionHolder->getReferenceFromSession($referenceNumber, $this->getCurrentAsn()))) {
            return null;
        }

        // Now create (and return) a RentRecoveryPlusReference and set $referencingApplication as its parent
        return $this->createRentRecoveryPlusReference($referencingApplication);
    }

    /**
     * Takes a reference of type ReferencingApplication and sets it as a parent of a RentRecoveryPlusReference.
     *
     * @param ReferencingApplication $referenceToConvert
     * @return RentRecoveryPlusReference
     */
    private function createRentRecoveryPlusReference(ReferencingApplication $referenceToConvert)
    {
        $decisionDetails = $this->decisionDetailsRetriever->getDecisionDetails($referenceToConvert);
        $this->convertedReference->setParent($referenceToConvert)->setDecisionDetails($decisionDetails);

        return $this->convertedReference;
    }

    /**
     * Get current ASN
     *
     * @return string
     */
    public function getCurrentAsn()
    {
        return $this->currentAsn;
    }

    /**
     * Set current ASN
     *
     * @param string $currentAsn
     * @return ReferenceNumberToReferenceObjectTransformer
     */
    public function setCurrentAsn($currentAsn)
    {
        $this->currentAsn = $currentAsn;
        return $this;
    }
}