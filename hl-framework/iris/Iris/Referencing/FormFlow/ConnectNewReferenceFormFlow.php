<?php

namespace Iris\Referencing\FormFlow;

use Iris\FormFlow\AbstractFormFlow;
use Iris\FormFlow\Exception\ProductNotFoundOnApplicationException;
use Iris\FormFlow\FormFlowStep;
use Iris\ProgressiveStore\ProgressiveStoreInterface;
use Symfony\Component\Form\FormInterface;
use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Iris\Referencing\FormFlow\Enumeration\ProductSteps;

/**
 * Class ConnectNewReferenceFormFlow
 *
 * @package Iris\Referencing\FormFlow
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ConnectNewReferenceFormFlow extends AbstractFormFlow
{
    /**
     * Step URLs
     */
    static public $urls = array(
        ProductSteps::PROPERTY => '/iris-new-reference',
        ProductSteps::PRODUCT => '/iris-new-reference/product',
        ProductSteps::TENANT_DETAILS => '/iris-new-reference/tenant-details',
        ProductSteps::ADDRESS_HISTORY => '/iris-new-reference/address-history',
        ProductSteps::FINANCIAL_REFEREES => '/iris-new-reference/financial-referee',
        ProductSteps::PROSPECTIVE_LANDLORD => '/iris-new-reference/landlord',
        ProductSteps::LETTING_REFEREE => '/iris-new-reference/letting-referee',
        ProductSteps::ADDITIONAL_DETAILS => '/iris-new-reference/additional-details',
        ProductSteps::SUMMARY => '/iris-new-reference/summary',
        ProductSteps::TERMS_AND_CONDITIONS => '/iris-new-reference/terms-and-conditions',
        ProductSteps::SUBMIT => '/iris-new-reference/submit',
    );

    /**
     * {@inheritdoc}
     */
    public function initialise()
    {
        $this
            ->addFormFlowStep(
                new FormFlowStep(
                    ProductSteps::PROPERTY,
                    function (AbstractFormFlow $flow) {
                        return null; // No previous step
                    },
                    function (AbstractFormFlow $flow) {
                        return ProductSteps::PRODUCT;
                    }
                )
            )
            ->addFormFlowStep(
                new FormFlowStep(
                    ProductSteps::PRODUCT,
                    function (AbstractFormFlow $flow) {
                        // Pass ?continue=1 to stop first step from flushing session
                        return sprintf('%s?continue=1', ProductSteps::PROPERTY);
                    },
                    function (AbstractFormFlow $flow) {
                        $isRentGuarantee = $flow->getProductFromApplicationInStore()->getHasRentGuarantee();
                        if ($isRentGuarantee) {
                            return ProductSteps::PROSPECTIVE_LANDLORD;
                        }
                        else {
                            return ProductSteps::TENANT_DETAILS;
                        }
                    }
                )
            )
            ->addFormFlowStepCollection(function (AbstractFormFlow $flow) {
                try {
                    $product = $flow->getProductFromApplicationInStore();
                    return require __DIR__ . sprintf('/Resources/step-collection/product/step-collection-%s.php', $product->getProductCode());
                }
                catch (ProductNotFoundOnApplicationException $e) {
                    return array();
                }
            })
        ;
    }
}