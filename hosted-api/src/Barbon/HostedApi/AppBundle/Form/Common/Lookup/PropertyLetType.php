<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup;

final class PropertyLetType extends AbstractIrisLookupService
{
    /**
     * {@inheritdoc}
     */
    protected function initialiseChoices()
    {
        $this->buildChoices(
            $this->lookupContainer->getCollection()->getPropertyLetTypes()
        );
    }
}
