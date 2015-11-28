<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\FinancialRefereeType;
use Symfony\Component\Form\FormInterface;

final class FinancialRefereeValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        // All other fields are validated through the Default validation group
        $validation_groups = array('Default');
        $financialRefereeType = $form->getData()->getFinancialRefereeType();

        // If there is no financial referee ID type set, don't try to control validation any further
        if ('' == $financialRefereeType) {
            return $validation_groups;
        }

        switch ($financialRefereeType) {
            case FinancialRefereeType::ACCOUNTANT:
                $validation_groups[] = 'contact_details';
                $validation_groups[] = 'referee_details';
                $validation_groups[] = 'income';
                $validation_groups[] = 'applicant_position_commencement';
                break;

            case FinancialRefereeType::EMPLOYER:
                $validation_groups[] = 'contact_details';
                $validation_groups[] = 'referee_details';
                $validation_groups[] = 'applicant_details';
                $validation_groups[] = 'income';
                $validation_groups[] = 'applicant_position_commencement';
                break;

            case FinancialRefereeType::PENSION_ADMINISTRATOR:
                $validation_groups[] = 'contact_details';
                $validation_groups[] = 'referee_details';
                $validation_groups[] = 'income';
                $validation_groups[] = 'pension_details';
                break;

            case FinancialRefereeType::PENSION_STATEMENT:
                $validation_groups[] = 'income';
                break;

            case FinancialRefereeType::SELF_ASSESSMENT:
                $validation_groups[] = 'income';
                $validation_groups[] = 'applicant_position_commencement';
                break;
        }

        return $validation_groups;
    }
}
