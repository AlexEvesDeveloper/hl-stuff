<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Symfony\Component\Form\FormInterface;

interface ValidationGroupSelector
{
    /**
     * Select the validation groups required for the form
     *
     * @param FormInterface $form Constructed form instance
     * @return array Array of validation group names
     */
    public function chooseGroups(FormInterface $form);
}
