<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\ValidationGroup;

use Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup\ValidationGroupSelector;
use Symfony\Component\Form\FormInterface;

final class ReferencingApplicationValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        // All other fields are validated through the Default validation group
        $validation_groups = array('Default');

        if ($form->has('phone') && $form->has('mobile')) {
            $phoneData = $form->get('phone')->getData();
            $mobileData = $form->get('mobile')->getData();

            // If both or neither phone and mobile fields are given,
            // validate both fields
            if (empty($phoneData) && empty($mobileData)) {
                $validation_groups[] = 'phone';
                $validation_groups[] = 'mobile';
            }
            else {
                // If only phone field alone is given, validate, but
                // not mobile. Only 1 is required.
                if (!empty($phoneData)) {
                    $validation_groups[] = 'phone';
                }

                // If only mobile field alone is given, validate, but
                // not phone. Only 1 is required.
                if (!empty($mobileData)) {
                    $validation_groups[] = 'mobile';
                }
            }
        }

        if ($form->has('bankAccount') && $form->get('bankAccount')) {
            $sortCodeData = $form->get('bankAccount')->get('accountSortcode');
            $accountNumberData = $form->get('bankAccount')->get('accountNumber');

            // If either the sort code or account no are given,
            // validate both fields
            if (!empty($sortCodeData) || !empty($accountNumberData)) {
                $validation_groups[] = 'bankaccount';
            }
        }

        return $validation_groups;
    }
}
