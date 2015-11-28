<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Symfony\Component\Form\FormInterface;

final class GuarantorDetailsValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        $phoneData = $form->get('phone')->getData();
        $mobileData = $form->get('mobile')->getData();

        // All other fields are validated through the Default validation group
        $validation_groups = array('Default');

        // If both or neither phone and mobile fields are given,
        // validate both fields
        if (empty($phoneData) && empty($mobileData)) {
            $validation_groups[] = 'phone';
            $validation_groups[] = 'mobile';
        }
        else {
            // If only phone field alone is given, validate, but
            // not mobile. Only 1 is required.
            if ( ! empty($phoneData)) {
                $validation_groups[] = 'phone';
            }

            // If only mobile field alone is given, validate, but
            // not phone. Only 1 is required.
            if ( ! empty($mobileData)) {
                $validation_groups[] = 'mobile';
            }
        }

        return $validation_groups;
    }
}
