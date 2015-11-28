<?php

namespace Iris\Referencing\FormFlow;

use Iris\FormFlow\AbstractFormFlow;
use Iris\FormFlow\Exception\ProductNotFoundOnApplicationException;
use Iris\FormFlow\FormFlowStep;
use Iris\ProgressiveStore\ProgressiveStoreInterface;
use Iris\Referencing\FormFlow\Enumeration\ProductSteps;
use Symfony\Component\Form\FormInterface;

/**
 * Class ContinueReferenceFormFlow
 *
 * @package Iris\Referencing\FormFlow
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class ContinueReferenceFormFlow extends AbstractFormFlow
{
    /**
     * Step URLs
     */
    static public $urls = array(
        ProductSteps::TENANT_DETAILS => '/continue-reference',
        ProductSteps::ADDRESS_HISTORY => '/continue-reference/address-history',
        ProductSteps::FINANCIAL_REFEREES => '/continue-reference/financial-referee',
        ProductSteps::PROSPECTIVE_LANDLORD => '/continue-reference/landlord',
        ProductSteps::LETTING_REFEREE => '/continue-reference/letting-referee',
        ProductSteps::ADDITIONAL_DETAILS => '/continue-reference/additional-details',
        ProductSteps::SUMMARY => '/continue-reference/summary',
        ProductSteps::TERMS_AND_CONDITIONS => '/continue-reference/terms-and-conditions',
        ProductSteps::SUBMIT => '/continue-reference/submit',
    );

    /**
     * {@inheritdoc}
     */
    public function initialise()
    {
        $this
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