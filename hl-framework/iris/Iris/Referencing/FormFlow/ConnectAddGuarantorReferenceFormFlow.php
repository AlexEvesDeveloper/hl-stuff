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
 * Class ConnectAddGuarantorReferenceFormFlow
 *
 * @package Iris\Referencing\FormFlow
 * @author Paul Swift <paul.swift@barbon.com>
 */
class ConnectAddGuarantorReferenceFormFlow extends AbstractFormFlow
{
    /**
     * Step URLs
     */
    static public $urls = array(
        ProductSteps::PRODUCT => '/iris-add-guarantor', // Note no /product (URL differs from new reference)
        ProductSteps::TENANT_DETAILS => '/iris-add-guarantor/guarantor-details',
        ProductSteps::ADDRESS_HISTORY => '/iris-add-guarantor/address-history',
        ProductSteps::FINANCIAL_REFEREES => '/iris-add-guarantor/financial-referee',
        ProductSteps::PROSPECTIVE_LANDLORD => '/iris-add-guarantor/landlord', // Note no prospective landlord for guarantors
        ProductSteps::LETTING_REFEREE => '/iris-add-guarantor/letting-referee', // Note no letting referee for guarantors
        ProductSteps::ADDITIONAL_DETAILS => '/iris-add-guarantor/additional-details',
        ProductSteps::SUMMARY => '/iris-add-guarantor/summary',
        ProductSteps::TERMS_AND_CONDITIONS => '/iris-add-guarantor/terms-and-conditions',
        ProductSteps::SUBMIT => '/iris-add-guarantor/submit',
    );

    /**
     * {@inheritdoc}
     */
    public function initialise()
    {
        $this
            ->addFormFlowStep(
                new FormFlowStep(
                    ProductSteps::PRODUCT,
                    function (AbstractFormFlow $flow) {
                        return null; // No previous step
                    },
                    function (AbstractFormFlow $flow) {
                        return ProductSteps::TENANT_DETAILS;
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
