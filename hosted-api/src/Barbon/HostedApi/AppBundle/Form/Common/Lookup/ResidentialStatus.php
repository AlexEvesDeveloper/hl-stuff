<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup;

final class ResidentialStatus extends AbstractIrisLookupService
{
    /**
     * {@inheritdoc}
     */
    protected function initialiseChoices()
    {
        $this->buildChoices(
            $this->lookupContainer->getCollection()->getResidentialStatuses()
        );
    }
}
