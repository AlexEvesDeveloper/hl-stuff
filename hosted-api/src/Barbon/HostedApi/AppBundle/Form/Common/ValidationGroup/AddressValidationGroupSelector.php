<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Symfony\Component\Form\FormInterface;

final class AddressValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        if (
            ! $form->get('flat')->getData() &&
            ! $form->get('houseName')->getData() &&
            ! $form->get('houseNumber')->getData()
        ) {
            return array(
                'Default',
                'propertyIdentifier',
                'postcode',
            );
        }
        return array(
            'Default',
            'postcode',
        );
    }
}
