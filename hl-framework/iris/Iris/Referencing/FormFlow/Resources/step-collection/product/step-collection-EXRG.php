<?php

use Iris\FormFlow\FormFlowStep;
use Iris\Referencing\FormFlow\ConnectAddGuarantorReferenceFormFlow;
use Iris\Referencing\FormFlow\ConnectAddTenantReferenceFormFlow;
use Iris\Referencing\FormFlow\Enumeration\ProductSteps;
use Iris\ProgressiveStore\ProgressiveStoreInterface;
use Symfony\Component\Form\FormInterface;
use Iris\FormFlow\AbstractFormFlow;
use Iris\Referencing\FormFlow\ContinueReferenceFormFlow;
use Barbondev\IRISSDK\Common\Enumeration\EmploymentStatusOptions;
use Barbondev\IRISSDK\Common\Enumeration\ResidentialStatusOptions;

/**
 * Product Step Collection
 *
 * Product Name: Extra Rent Guarantee
 * Rent Guarantee: YES
 * Financial Referees: YES
 * Letting Referees: YES: tenant | NO: guarantor
 * Prospective Landlord: YES: tenant | NO: guarantor
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
return array(

    // Prospective Landlord
    new FormFlowStep(
        ProductSteps::PROSPECTIVE_LANDLORD,
        function (AbstractFormFlow $flow) {
            return ProductSteps::PRODUCT;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::TENANT_DETAILS;
        },
        function (AbstractFormFlow $flow) {

            // If this is the Add Tenant or Add Guarantor process, jump over prospective landlord
            if ($flow instanceof ConnectAddGuarantorReferenceFormFlow) {
                return true;
            }

            // Skip if we can't accept prospective landlord details
            if (false === $flow->getCanAgentCompleteProspectiveLandlord()) {
                return true;
            }

        }
    ),

    // Tenant Details
    new FormFlowStep(
        ProductSteps::TENANT_DETAILS,
        function (AbstractFormFlow $flow) {
            return ProductSteps::PROSPECTIVE_LANDLORD;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::ADDRESS_HISTORY;
        }
    ),

    // Address History
    new FormFlowStep(
        ProductSteps::ADDRESS_HISTORY,
        function (AbstractFormFlow $flow) {
            return ProductSteps::TENANT_DETAILS;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::LETTING_REFEREE;
        }
    ),

    // Letting Referee
    new FormFlowStep(
        ProductSteps::LETTING_REFEREE,
        function (AbstractFormFlow $flow) {
            return ProductSteps::ADDRESS_HISTORY;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::FINANCIAL_REFEREES;
        },
        function (AbstractFormFlow $flow) {

            // If this is the Add Guarantor process, jump over letting referee
            if ($flow instanceof ConnectAddGuarantorReferenceFormFlow) {
                return true;
            }

            /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
            $application = $flow
                ->getProgressiveStore()
                ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication')
            ;

            // If home owner or living with relatives - skip this step
            return in_array($application->getResidentialStatus(), array(
                ResidentialStatusOptions::HOME_OWNER,
                ResidentialStatusOptions::LIVING_WITH_RELATIVES,
            ));
        }
    ),

    // Financial Referees
    new FormFlowStep(
        ProductSteps::FINANCIAL_REFEREES,
        function (AbstractFormFlow $flow) {
            return ProductSteps::LETTING_REFEREE;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::ADDITIONAL_DETAILS;
        },
        function (AbstractFormFlow $flow) {

            /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
            $application = $flow
                ->getProgressiveStore()
                ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication')
            ;

            // If this is a student, unemployed or unknown - skip this step
            return in_array($application->getEmploymentStatus(), array(
                EmploymentStatusOptions::STUDENT,
                EmploymentStatusOptions::UNEMPLOYED,
                EmploymentStatusOptions::UNKNOWN,
            ));
        }
    ),

    // Additional Details
    new FormFlowStep(
        ProductSteps::ADDITIONAL_DETAILS,
        function (AbstractFormFlow $flow) {
            return ProductSteps::FINANCIAL_REFEREES;
        },
        function (AbstractFormFlow $flow) {
            return ProductSteps::SUMMARY;
        },
        function (AbstractFormFlow $flow) {
        }
    ),

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
            return ProductSteps::TERMS_AND_CONDITIONS;
        },
        function (AbstractFormFlow $flow) {
            return null;
        }
    ),

);