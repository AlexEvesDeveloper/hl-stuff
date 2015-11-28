<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup;

use Symfony\Component\Form\FormInterface;

final class BankAccountValidationGroupSelector implements ValidationGroupSelector
{
    /**
     * {@inheritdoc}
     */
    public function chooseGroups(FormInterface $form)
    {
        $accountNoData = $form->get('accountNumber')->getData();
        $accountSortCodeData = $form->get('accountSortcode')->getData();

        // Note: Only validate when either field is supplied.
        // If neither field is supplied, no validation should occur.
        if (!empty($accountNoData) || !empty($accountSortCodeData)) {
            return array('bankaccount');
        }

        return array();
    }
}
