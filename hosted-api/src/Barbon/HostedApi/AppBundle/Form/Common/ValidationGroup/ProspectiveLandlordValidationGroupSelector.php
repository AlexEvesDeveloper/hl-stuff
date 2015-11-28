<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Symfony\Component\Form\FormInterface;

class ProspectiveLandlordValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        // All other fields are validated through the Default validation group
        $validation_groups = array('Default');
        
        $dayPhone = $form->get('dayPhone')->getData();
        $eveningPhone = $form->get('eveningPhone')->getData();
        
        if ( ! isset($dayPhone) && ! isset($eveningPhone)) {
            $validation_groups[] = 'dayPhone';
            $validation_groups[] = 'eveningPhone';
        }
        elseif ( ! isset($dayPhone)) {
            $validation_groups[] = 'eveningPhone';
        }
        elseif ( ! isset($eveningPhone)) {
            $validation_groups[] = 'dayPhone';
        }

        return $validation_groups;
    }
}
