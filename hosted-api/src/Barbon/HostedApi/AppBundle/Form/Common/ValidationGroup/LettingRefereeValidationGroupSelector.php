<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Symfony\Component\Form\FormInterface;

final class LettingRefereeValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        $dayPhoneData = $form->get('dayPhone')->getData();
        $eveningPhoneData = $form->get('eveningPhone')->getData();
        $emailData = $form->get('email')->getData();

        // All other fields are validated through the Default validation group
        $validation_groups = array('Default');

        // todo: is there an apprpoach that does not require the sdk models?

        /** @var ReferencingApplication $application */
        $application = $form->getParent()->getData();
        if ($application instanceof ReferencingApplication) {

            // If Optimum product, require at least one contact detail must be given
            if (19 == $application->getProductId()) {
                if (empty($dayPhoneData) && empty($eveningPhoneData) && empty($emailData)) {
                    $validation_groups[] = 'dayphone';
                    $validation_groups[] = 'eveningphone';
                    $validation_groups[] = 'email';
                }
            }

            // If no day phone, enforce evening
            if (empty($dayPhoneData)) {
                $validation_groups[] = 'eveningphone';
            }

            // If no evening phone, enforce day
            if (empty($eveningPhoneData)) {
                if ($k = array_search('eveningphone', $validation_groups)) {
                    unset($validation_groups[$k]);
                }
                $validation_groups[] = 'dayphone';
            }

            return $validation_groups;
        }

        return array('Default');
    }
}
