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
 * Class ConnectAddTenantReferenceFormFlow
 *
 * @package Iris\Referencing\FormFlow
 * @author Paul Swift <paul.swift@barbon.com>
 */
class ConnectAddTenantReferenceFormFlow extends AbstractFormFlow
{
    /**
     * Step URLs
     */
    static public $urls = array(
        ProductSteps::PRODUCT => '/iris-add-tenant', // Note no /product (URL differs from new reference)
        ProductSteps::TENANT_DETAILS => '/iris-add-tenant/tenant-details',
        ProductSteps::ADDRESS_HISTORY => '/iris-add-tenant/address-history',
        ProductSteps::FINANCIAL_REFEREES => '/iris-add-tenant/financial-referee',
        ProductSteps::PROSPECTIVE_LANDLORD => '/iris-add-tenant/landlord', // Note no prospective landlord for added tenants
        ProductSteps::LETTING_REFEREE => '/iris-add-tenant/letting-referee',
        ProductSteps::ADDITIONAL_DETAILS => '/iris-add-tenant/additional-details',
        ProductSteps::SUMMARY => '/iris-add-tenant/summary',
        ProductSteps::TERMS_AND_CONDITIONS => '/iris-add-tenant/terms-and-conditions',
        ProductSteps::SUBMIT => '/iris-add-tenant/submit',
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

                        try {
                            $product = $flow->getProductFromApplicationInStore();
                            if (!$product->getHasRentGuarantee()) {
                                return ProductSteps::TENANT_DETAILS;
                            }
                        }
                        catch (ProductNotFoundOnApplicationException $e) {
                            // Do nothing
                        }

                        return ProductSteps::PROSPECTIVE_LANDLORD;
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
