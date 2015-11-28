<?php

namespace RRP\Form\DataTransformer;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ProductIds;
use RRP\Common\ReferenceTypes;
use RRP\Utility\SessionReferenceHolder;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ReferenceToProductTypeTransformer
 *
 * @package RRP\Form\DataTransformer
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceToProductTypeTransformer implements DataTransformerInterface
{
    /**
     * @var SessionReferenceHolder
     */
    protected $sessionHolder;

    /**
     * @var string
     */
    protected $currentAsn;

    /**
     * ReferenceToProductTypeTransformer constructor.
     *
     * @param SessionReferenceHolder $sessionHolder
     */
    public function __construct(SessionReferenceHolder $sessionHolder)
    {
        $this->sessionHolder = $sessionHolder;
    }

    /**
     * Transform the value displayed in the view.
     *
     * @param mixed $value
     * @return mixed|void
     */
    public function transform($value)
    {
        // Do nothing on the way out.
    }

    /**
     * Convert the radio option 'HomeLet Reference' into a specific product name.
     *
     * @param mixed $referenceType
     * @return mixed|null
     */
    public function reverseTransform($referenceType)
    {
        // Only transform if the user selected 'HomeLet Reference', otherwise leave the value as it is.
        if ('HomeLetReference' != $referenceType) {
            return $referenceType;
        }

        if (false === ($references = $this->sessionHolder->getReferencesFromSession($this->getCurrentAsn()))) {
            // User hasn't entered their reference numbers yet, so don't attempt to set the product type on this request.
            return null;
        }

        $possibleReferenceTypes = $this->getReferenceTypes($references);
        return $this->determineReferenceType($possibleReferenceTypes);
    }

    /**
     * Establish which reference types have been added to this policy application.
     *
     * @param array $references
     * @return array
     */
    private function getReferenceTypes(array $references)
    {
        // When we find a 'valid' reference of a particular type, set it to true in this array.
        $referenceTypes = array(ProductIds::INSIGHT => null, ProductIds::ENHANCE => null, ProductIds::OPTIMUM => null);

        foreach ($references as $reference) {
            // If we already have a valid reference of this type, no need to evaluate further.
            if (isset($referenceTypes[$reference->getProductId()])) {
                continue;
            }

            // Dismiss this reference if the rent share is not > 0.
            if (0 >= $reference->getRentShare()) {
                continue;
            }

            // Products should only be of the types specified in $referenceTypes.
            $productId = $reference->getProductId();
            if ( ! array_key_exists($productId, $referenceTypes)) {
                continue;
            }

            // We have a valid reference, set its type as we know it hasn't be set yet.
            $referenceTypes[$productId] = $reference->getProduct()->getName();
        }

        return $referenceTypes;
    }

    /**
     * Determine what to set as the reference type for the application.
     * This is required for the case when multiple reference numbers have entered on to one policy.
     *
     * @param array $referenceTypes
     */
    private function determineReferenceType(array $referenceTypes)
    {
        // If we have a valid INSIGHT, use that as the reference type for this application...
        if (isset($referenceTypes[ProductIds::INSIGHT])) {
            return $referenceTypes[ProductIds::INSIGHT];
        }

        // ...Otherwise, if we have a valid ENHANCE, use that...
        if (isset($referenceTypes[ProductIds::ENHANCE])) {
            return $referenceTypes[ProductIds::ENHANCE];
        }

        // ...Otherwise, if we have a valid OPTIMUM, use that...
        if (isset($referenceTypes[ProductIds::OPTIMUM])) {
            return $referenceTypes[ProductIds::OPTIMUM];
        }

        // ...Otherwise, we have a problem.
        throw new \LogicException('No valid reference types have been set, unable to determine which type to use');
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