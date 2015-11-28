<?php

namespace Iris\Referencing\FormFlow;

use Iris\FormFlow\AbstractFormFlow;
use Iris\FormFlow\Exception\ProductNotFoundOnApplicationException;
use Iris\Referencing\FormFlow\Enumeration\ProductSteps;

/**
 * Class CompleteReferenceFormFlow
 *
 * @package Iris\Referencing\FormFlow
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class CompleteReferenceFormFlow extends AbstractFormFlow
{
    /**
     * Step URLs
     */
    static public $urls = array(
        ProductSteps::SUMMARY => '/complete-reference',
        ProductSteps::TERMS_AND_CONDITIONS => '/complete-reference/terms-and-conditions',
        ProductSteps::SUBMIT => '/complete-reference/submit',
    );

    /**
     * {@inheritdoc}
     */
    public function initialise()
    {
        $this
            ->addFormFlowStepCollection(function (AbstractFormFlow $flow) {
                try {
                    return require __DIR__ . '/Resources/step-collection/process/complete-reference.php';
                }
                catch (ProductNotFoundOnApplicationException $e) {
                    return array();
                }
            })
        ;
    }
}
