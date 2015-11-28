<?php

use Iris\FormFlow\FormFlowStep;
use Iris\Referencing\FormFlow\Enumeration\ProductSteps;
use Iris\FormFlow\AbstractFormFlow;

/**
 * Process Step Collection
 *
 * @author Simon Paulger <simon.paulger@barbon.com
 */
return array(
    // Summary
    new FormFlowStep(
        ProductSteps::SUMMARY,
        function (AbstractFormFlow $flow) {
            return ProductSteps::ADDITIONAL_DETAILS;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::TERMS_AND_CONDITIONS;
        }
    ),

    // Terms & Conditions
    new FormFlowStep(
        ProductSteps::TERMS_AND_CONDITIONS,
        function (AbstractFormFlow $flow) {
            return ProductSteps::SUMMARY;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::SUBMIT;
        }
    ),

    // Submit
    new FormFlowStep(
        ProductSteps::SUBMIT,
        function (AbstractFormFlow $flow) {
            return null;
        },
        function (AbstractFormFlow $flow) {
            return null;
        }
    ),
);
